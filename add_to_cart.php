<?php
session_start();
require_once('classes/database.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $customerId = $data['customerId'] ?? $_SESSION['customer_id'] ?? 1; // Fallback to 1 if not set
    $productId = $data['productId'] ?? 0;
    $quantity = $data['quantity'] ?? 0;
    $price = $data['price'] ?? 0;

    if ($productId <= 0 || $quantity <= 0 || $price <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid input data']);
        exit;
    }

    $con = new database();
    $result = $con->addToCart($customerId, $productId, $quantity, $price);

    if ($result['success']) {
        // Fetch updated cart to calculate total
        $cartItems = $con->getCart($customerId);
        $total = array_sum(array_map(function($item) {
            return $item['Quantity'] * $item['Price'];
        }, $cartItems));
        $result['cartTotal'] = number_format($total, 2);
        $result['cartCount'] = count($cartItems);
    }

    echo json_encode($result);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>