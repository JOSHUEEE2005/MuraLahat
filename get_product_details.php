<?php
require_once 'classes/database.php';

header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $productId = $input['productId'] ?? null;

    if (!$productId) {
        throw new Exception('Product ID is missing');
    }

    $con = new database();
    $product = $con->getProductDetails($productId);

    if (!$product) {
        throw new Exception('Product not found');
    }

    echo json_encode(['success' => true, 'data' => $product]);
} catch (Exception $e) {
    error_log('Get Product Details Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>