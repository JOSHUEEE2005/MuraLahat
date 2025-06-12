<?php
ob_start();
session_start();
require_once('classes/database.php');

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON input: ' . json_last_error_msg());
    }

    $productId = $input['productId'] ?? null;

    if (!$productId) {
        throw new Exception('Missing product ID');
    }

    $con = new database();
    $product = $con->getProductDetails($productId);

    if (!$product) {
        throw new Exception('Product not found');
    }

    echo json_encode(['success' => true, 'product' => $product]);

} catch (Exception $e) {
    error_log('Get Product Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

ob_end_flush();
?>