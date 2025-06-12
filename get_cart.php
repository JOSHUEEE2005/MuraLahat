<?php
session_start();
require_once('classes/database.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $customerId = $data['customerId'] ?? $_SESSION['customer_id'] ?? 1;

    $con = new database();
    $cartItems = $con->getCart($customerId);

    $items = array_map(function($item) {
        return [
            'Product_ID' => $item['Product_ID'],
            'Product_Name' => $item['Product_Name'],
            'Quantity' => $item['Quantity'],
            'Price' => $item['Price'],
            'Product_Stock' => $item['Product_Stock']
        ];
    }, $cartItems);

    echo json_encode(['success' => true, 'items' => $items]);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>