<?php
session_start();
require_once 'classes/database.php';

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['customerId'])) {
        throw new Exception('Customer ID is required');
    }

    $con = new database();
    $cartItems = $con->getCart($data['customerId']);

    echo json_encode([
        'success' => true,
        'items' => $cartItems
    ]);
} catch (Exception $e) {
    error_log('Get Cart Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}