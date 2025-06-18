<?php
session_start();
require_once 'classes/database.php';

header('Content-Type: application/json');

set_time_limit(30);

error_log('Process Transaction Request: ' . print_r(file_get_contents('php://input'), true));
error_log('Initial Session State: customer_id=' . ($_SESSION['customer_id'] ?? 'unset') . ', guest_cart=' . print_r($_SESSION['guest_cart'] ?? [], true));
error_log('Session ID: ' . session_id());

try {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || !isset($data['cashPaid']) || !isset($data['total']) || !isset($data['customerType'])) {
        throw new Exception('Missing or invalid transaction data: ' . json_encode($data));
    }

    $customerId = $data['customerId'] ?? $_SESSION['customer_id'] ?? null;
    $cashPaid = floatval($data['cashPaid']);
    $total = floatval($data['total']);
    $usePoints = $data['usePoints'] ?? false;
    $customerType = $data['customerType'];

    error_log("Validated Data: customerId=$customerId, cashPaid=$cashPaid, total=$total, usePoints=" . ($usePoints ? 'true' : 'false') . ", customerType=$customerType");

    if ($total <= 0 || !is_numeric($total)) {
        throw new Exception('Invalid cart total: ' . $total);
    }
    if ($cashPaid > 1000000 || !is_numeric($cashPaid)) {
        throw new Exception('Invalid payment amount: cashPaid=' . $cashPaid . '. Must be less than 1,000,000.');
    }

    $con = new database();
    $pdo = $con->opencon();

    if ($customerType === 'member') {
        $_SESSION['guest_cart'] = [];
        error_log("Reset guest_cart for member checkout");
        if (empty($customerId) || strpos($customerId, 'guest_') === 0 || !is_numeric($customerId)) {
            throw new Exception('Invalid customer ID for member checkout');
        }
        $stmt = $pdo->prepare("SELECT Customer_ID, Membership_Status FROM customer WHERE Customer_ID = ?");
        $stmt->execute([$customerId]);
        $customer = $stmt->fetch();
        if (!$customer) {
            throw new Exception('Customer ID ' . $customerId . ' not found');
        }
        if ($customer['Membership_Status'] != 1) {
            throw new Exception('Customer ID ' . $customerId . ' is not an active member');
        }
        $oldCustomerId = $_SESSION['customer_id'] ?? null;
        $_SESSION['customer_id'] = $customerId;
        error_log("Member checkout with Customer_ID: $customerId, Previous ID: " . ($oldCustomerId ?? 'none'));

        if (!empty($oldCustomerId) && strpos($oldCustomerId, 'guest_') === 0 && $oldCustomerId !== $customerId) {
            error_log("Transferring cart from guest ID: $oldCustomerId to member ID: $customerId");
            $stmt = $pdo->prepare("UPDATE cart SET Customer_ID = ? WHERE Customer_ID = ?");
            $stmt->execute([$customerId, $oldCustomerId]);
            error_log("Transferred {$stmt->rowCount()} cart items from guest to member in database");
        }
    } else if ($customerType === 'guest') {
        if (empty($customerId)) {
            $customerId = 'guest_' . uniqid();
            $_SESSION['customer_id'] = $customerId;
            error_log("Assigned Guest ID: $customerId");
        }
    } else {
        throw new Exception('Invalid customer type: ' . $customerType);
    }

    $stmt = $pdo->prepare("SELECT Cart_ID, Customer_ID, Product_ID, Quantity, Price FROM cart WHERE Customer_ID = ?");
    $stmt->execute([$customerId]);
    $dbCart = $stmt->fetchAll();
    error_log("Cart Table State for Customer_ID=$customerId: " . print_r($dbCart, true));

    error_log("Fetching cart for Customer_ID: $customerId");
    $cartItems = $con->getCart($customerId);
    error_log('Cart Items Retrieved: ' . print_r($cartItems, true));
    if (empty($cartItems)) {
        throw new Exception('Cart is empty for customer ID: ' . $customerId . '. Please add items to your cart before checking out.');
    }

    $calculatedTotal = array_sum(array_map(function($item) {
        return floatval($item['Quantity']) * floatval($item['Price']);
    }, $cartItems));
    if (abs($calculatedTotal - $total) > 0.01) {
        throw new Exception("Cart total mismatch: calculated=$calculatedTotal, provided=$total");
    }

    $pointsUsed = 0;
    $discountedTotal = $total;
    if ($usePoints && $customerType === 'member') {
        error_log("Attempting points redemption for Customer_ID: $customerId");
        $pointsResult = $con->getCustomerPoints($customerId);
        if (!$pointsResult['success']) {
            throw new Exception('Failed to retrieve points: ' . ($pointsResult['error'] ?? 'Unknown error'));
        }
        $availablePoints = (int)$pointsResult['points'];
        error_log("Points available for Customer_ID=$customerId: $availablePoints");
        if ($availablePoints > 0) {
            $pointsUsed = min($availablePoints, floor($total)); // Use all points up to the total amount
            $discountedTotal = max(0, $total - $pointsUsed);
            error_log("Applied $pointsUsed points for a ₱$pointsUsed discount for Customer_ID: $customerId, Discounted Total: $discountedTotal");
        } else {
            error_log("No points available for redemption for Customer_ID: $customerId");
        }
    }

    if ($cashPaid < $discountedTotal) {
        throw new Exception('Invalid payment amount: cashPaid=' . $cashPaid . ', discountedTotal=' . $discountedTotal);
    }

    foreach ($cartItems as $item) {
        if (!isset($item['Price']) || $item['Price'] <= 0) {
            throw new Exception("Invalid or missing price for product ID {$item['Product_ID']}");
        }
        $stmt = $pdo->prepare("SELECT Product_Stock FROM product WHERE Product_ID = ?");
        $stmt->execute([$item['Product_ID']]);
        $stock = $stmt->fetchColumn();
        if ($stock === false || $stock < $item['Quantity']) {
            throw new Exception("Insufficient stock for product ID {$item['Product_ID']}: stock=$stock, requested={$item['Quantity']}");
        }
    }

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO transaction_ml (Customer_ID, Trans_Total, Points_Redeemed, Transaction_Date) 
                           VALUES (?, ?, ?, CURDATE())");
    $execResult = $stmt->execute([$customerType === 'member' ? $customerId : null, $discountedTotal, $pointsUsed]);
    if (!$execResult) {
        throw new Exception('Failed to insert into transaction_ml: ' . print_r($stmt->errorInfo(), true));
    }
    $transactionId = $pdo->lastInsertId();
    if (!is_numeric($transactionId) || $transactionId <= 0) {
        throw new Exception('Invalid transaction ID generated: ' . $transactionId);
    }
    error_log("Inserted Transaction ID: $transactionId");

    foreach ($cartItems as $item) {
        $stmt = $pdo->prepare("INSERT INTO transaction_items (Transaction_ID, Product_ID, Quantity, Original_Price) 
                               VALUES (?, ?, ?, ?)");
        $execResult = $stmt->execute([$transactionId, $item['Product_ID'], $item['Quantity'], $item['Price']]);
        if (!$execResult) {
            throw new Exception('Failed to insert into transaction_items: ' . print_r($stmt->errorInfo(), true));
        }
        error_log("Inserted Transaction Item: Product_ID={$item['Product_ID']}, Quantity={$item['Quantity']}, Original_Price={$item['Price']}");

        $stmt = $pdo->prepare("UPDATE product SET Product_Stock = Product_Stock - ? WHERE Product_ID = ?");
        $execResult = $stmt->execute([$item['Quantity'], $item['Product_ID']]);
        if (!$execResult) {
            throw new Exception('Failed to update product stock: ' . print_r($stmt->errorInfo(), true));
        }
    }

    if (strpos($customerId, 'guest_') === 0) {
        $_SESSION['guest_cart'] = [];
        error_log('Cleared guest cart');
    } else {
        $stmt = $pdo->prepare("DELETE FROM cart WHERE Customer_ID = ?");
        $stmt->execute([$customerId]);
        error_log('Cleared member cart for Customer_ID: ' . $customerId);
    }

    if (!$pdo->commit()) {
        throw new Exception('Failed to commit database transaction');
    }
    error_log('Core transaction committed successfully');

    if ($customerType === 'member') {
        $pdo->beginTransaction();
        $pointsEarned = floor($discountedTotal / 100) * 5;
        error_log("Calculated points earned: $pointsEarned for discountedTotal=$discountedTotal, Customer_ID=$customerId");
        if ($pointsEarned > 0) {
            $pointsResult = $con->updateCustomerPoints($customerId, $pointsEarned, $transactionId, false);
            if (!$pointsResult['success']) {
                throw new Exception('Failed to update points earned: ' . ($pointsResult['error'] ?? 'Unknown error'));
            }
            error_log("Points Earned: $pointsEarned for Customer_ID: $customerId");
        }
        if ($pointsUsed > 0) {
            $pointsResult = $con->updateCustomerPoints($customerId, $pointsUsed, $transactionId, true);
            if (!$pointsResult['success']) {
                throw new Exception('Failed to update points redeemed: ' . ($pointsResult['error'] ?? 'Unknown error'));
            }
            error_log("Points Used: $pointsUsed for Customer_ID: $customerId");
        }
        $pdo->commit();
        error_log('Points transaction committed successfully');
    }

    echo json_encode([
        'success' => true,
        'total' => number_format($discountedTotal, 2),
        'change' => number_format($cashPaid - $discountedTotal, 2),
        'pointsEarned' => $pointsEarned ?? 0,
        'pointsUsed' => $pointsUsed,
        'items' => $cartItems
    ]);
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
        error_log('Transaction rolled back due to: ' . $e->getMessage());
    }
    error_log('Process Transaction Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>