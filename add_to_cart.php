<?php
session_start();
require_once 'classes/database.php';

$response = ['success' => false, 'error' => ''];

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($_SESSION['customer_id'])) {
        // Create a guest customer if none exists
        $_SESSION['customer_type'] = 'guest';
        $_SESSION['customer_id'] = 'guest_' . session_id();
    }
    
    if (!isset($data['productId']) || !isset($data['quantity']) || !isset($data['price'])) {
        throw new Exception('Invalid cart data');
    }
    
    $con = new database();
    $result = $con->addToCart($_SESSION['customer_id'], $data['productId'], $data['quantity'], $data['price']);
    
    if ($result['success']) {
        $cartItems = $con->getCart($_SESSION['customer_id']);
        $cartTotal = array_sum(array_map(function($item) {
            return floatval($item['Quantity']) * floatval($item['Price']);
        }, $cartItems));
        $cartCount = count($cartItems);
        
        $response['success'] = true;
        $response['cartTotal'] = $cartTotal;
        $response['cartCount'] = $cartCount;
    } else {
        $response['error'] = $result['error'];
    }
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);