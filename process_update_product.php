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
    $prodName = $input['prodName'] ?? null;
    $stockMode = $input['stockMode'] ?? null;
    $prodStock = isset($input['prodStock']) ? (int)$input['prodStock'] : null;
    $prodPrice = isset($input['prodPrice']) ? (float)$input['prodPrice'] : null;
    $effectiveFrom = $input['effectiveFrom'] ?? null;
    $effectiveTo = $input['effectiveTo'] ?? null;

    if (!$productId || !$prodName || !$stockMode || $prodStock < 0 || $prodPrice < 0 || !$effectiveFrom || !in_array($stockMode, ['set', 'add'])) {
        throw new Exception('Missing or invalid input fields');
    }

    $con = new database();
    $result = $con->updateProduct($productId, $prodName, $prodStock, $prodPrice, $effectiveFrom, $effectiveTo, $stockMode);

    echo json_encode($result);

} catch (Exception $e) {
    error_log('Update Product Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

ob_end_flush();
?>