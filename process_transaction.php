<?php
session_start();
require_once 'classes/database.php';
header('Content-Type: application/json');

try {
    // Parse JSON input
    $data = json_decode(file_get_contents('php://input'), true);
    $customerId = isset($data['customerId']) ? (int)$data['customerId'] : null;
    $cashPaid = isset($data['cashPaid']) ? floatval($data['cashPaid']) : null;

    // Log received values for debugging
    error_log("Received: customerId=$customerId, cashPaid=$cashPaid", 3, 'php_errors.log');

    // Validate inputs
    if (!$customerId || $customerId <= 0 || !$cashPaid || $cashPaid <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid customer ID or cash paid']);
        exit;
    }

    $con = new database();
    $pdo = $con->opencon();

    // Verify customer exists
    $stmt = $pdo->prepare("SELECT Customer_ID FROM customer WHERE Customer_ID = ?");
    $stmt->execute([$customerId]);
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Customer ID does not exist']);
        exit;
    }

    $pdo->beginTransaction();

    // Fetch cart items
    $stmt = $pdo->prepare("SELECT c.Product_ID, c.Quantity, c.Price, p.Product_Stock
                           FROM cart c
                           JOIN product p ON c.Product_ID = p.Product_ID
                           WHERE c.Customer_ID = ?");
    $stmt->execute([$customerId]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($cartItems)) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => 'Cart is empty']);
        exit;
    }

    // Calculate total
    $total = 0;
    foreach ($cartItems as $item) {
        $total += floatval($item['Price']) * intval($item['Quantity']);
    }

    if ($cashPaid < $total) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => 'Insufficient cash paid']);
        exit;
    }

    // Validate stock
    foreach ($cartItems as $item) {
        if ($item['Quantity'] > $item['Product_Stock']) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'error' => "Insufficient stock for product ID {$item['Product_ID']}"]);
            exit;
        }
    }

    // Insert transaction into transaction_ml
    $stmt = $pdo->prepare("INSERT INTO transaction_ml (Customer_ID, Trans_Total, Transaction_Date) VALUES (?, ?, CURDATE())");
    $stmt->execute([$customerId, $total]);
    $transactionId = $pdo->lastInsertId();

    // Insert transaction items
    $stmt = $pdo->prepare("INSERT INTO transaction_items (Transaction_ID, Product_ID, Quantity, Original_Price) VALUES (?, ?, ?, ?)");
    foreach ($cartItems as $item) {
        $stmt->execute([$transactionId, $item['Product_ID'], $item['Quantity'], $item['Price']]);
    }

    // Insert payment
    $stmt = $pdo->prepare("INSERT INTO payment (Transaction_ID, Payment_Type, Payment_Date, Payment_Amount) VALUES (?, 'Cash', NOW(), ?)");
    $stmt->execute([$transactionId, $total]);

    // Insert income
    $paymentId = $pdo->lastInsertId();
    $stmt = $pdo->prepare("INSERT INTO income (Payment_ID, Income_Amount, Income_Date) VALUES (?, ?, NOW())");
    $stmt->execute([$paymentId, $total]);

    // Update stock
    $stmt = $pdo->prepare("UPDATE product SET Product_Stock = Product_Stock - ? WHERE Product_ID = ?");
    foreach ($cartItems as $item) {
        $stmt->execute([$item['Quantity'], $item['Product_ID']]);
    }

    // Clear cart
    $stmt = $pdo->prepare("DELETE FROM cart WHERE Customer_ID = ?");
    $stmt->execute([$customerId]);

    $pdo->commit();

    // Calculate change
    $change = $cashPaid - $total;

    echo json_encode([
        'success' => true,
        'total' => number_format($total, 2, '.', ''),
        'change' => number_format($change, 2, '.', ''),
        'items' => $cartItems // For stock updates on client-side
    ]);
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Transaction Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine(), 3, 'php_errors.log');
    echo json_encode(['success' => false, 'error' => 'Transaction failed: ' . $e->getMessage()]);
}
?>