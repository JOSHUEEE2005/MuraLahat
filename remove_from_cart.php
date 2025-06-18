<?php
session_start();
require_once 'classes/database.php';

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['cartId'])) {
        throw new Exception('Invalid cart ID');
    }

    $cartId = $data['cartId'];
    $con = new database();

    // Handle guest cart
    if (strpos($_SESSION['customer_id'] ?? '', 'guest_') === 0 && isset($_SESSION['guest_cart'])) {
        $initialCount = count($_SESSION['guest_cart']);
        $_SESSION['guest_cart'] = array_filter($_SESSION['guest_cart'], function($item) use ($cartId) {
            return 'guest_' . $item['Product_ID'] !== $cartId;
        });
        $_SESSION['guest_cart'] = array_values($_SESSION['guest_cart']); // Reindex array
        if (count($_SESSION['guest_cart']) < $initialCount) {
            $cartItems = $con->getCart($_SESSION['customer_id']);
            $cartTotal = array_sum(array_map(function($item) {
                return floatval($item['Quantity']) * floatval($item['Price']);
            }, $cartItems));
            $cartCount = count($cartItems);
            echo json_encode([
                'success' => true,
                'cartTotal' => number_format($cartTotal, 2),
                'cartCount' => $cartCount
            ]);
        } else {
            throw new Exception('Item not found in guest cart');
        }
        exit;
    }

    // Handle member cart
    $result = $con->removeFromCart($cartId);
    if (!$result['success']) {
        throw new Exception($result['error'] ?? 'Failed to remove item from cart');
    }
    echo json_encode($result);
} catch (Exception $e) {
    error_log('Remove from Cart Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}