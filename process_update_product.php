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
    // Create uploads directory if it doesn't exist
    $uploadDir = 'uploads/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $productId = $_POST['productId'] ?? null;
    $prodName = $_POST['prodName'] ?? null;
    $stockMode = $_POST['stockMode'] ?? null;
    $prodStock = isset($_POST['prodStock']) ? (int)$_POST['prodStock'] : null;
    $prodPrice = isset($_POST['prodPrice']) ? (float)$_POST['prodPrice'] : null;
    $effectiveFrom = $_POST['effectiveFrom'] ?? null;
    $effectiveTo = $_POST['effectiveTo'] ?? null;
    $categoryIds = isset($_POST['categoryIds']) ? json_decode($_POST['categoryIds'], true) : [];

    // Log incoming data for debugging
    error_log("Update Product Request: " . json_encode([
        'productId' => $productId,
        'prodName' => $prodName,
        'stockMode' => $stockMode,
        'prodStock' => $prodStock,
        'prodPrice' => $prodPrice,
        'effectiveFrom' => $effectiveFrom,
        'effectiveTo' => $effectiveTo,
        'categoryIds' => $categoryIds,
        'hasFile' => isset($_FILES['productImage'])
    ]));

    // Validate inputs with specific error messages
    $errors = [];
    if (!$productId) $errors[] = 'Product ID is missing';
    if (!$prodName) $errors[] = 'Product name is missing';
    if (!$stockMode || !in_array($stockMode, ['set', 'add'])) $errors[] = 'Invalid stock mode';
    if (!isset($prodStock) || $prodStock < 0) $errors[] = 'Stock must be a non-negative number';
    if (!isset($prodPrice) || $prodPrice < 0) $errors[] = 'Price must be a non-negative number';
    if (!$effectiveFrom) $errors[] = 'Effective from date is missing';
    if (empty($categoryIds) || !is_array($categoryIds)) $errors[] = 'At least one category is required';

    if (!empty($errors)) {
        throw new Exception(implode('; ', $errors));
    }

    // Handle file upload
    $imagePath = null;
    if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['productImage'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 2 * 1024 * 1024; // 2MB
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception('Invalid file type. Only JPEG, PNG, and GIF are allowed.');
        }
        if ($file['size'] > $maxSize) {
            throw new Exception('File size exceeds 2MB limit.');
        }
        $fileName = uniqid('prod_') . '_' . basename($file['name']);
        $imagePath = $uploadDir . $fileName;
        if (!move_uploaded_file($file['tmp_name'], $imagePath)) {
            throw new Exception('Failed to upload image.');
        }
    }

    // Validate category IDs
    $con = new database();
    $stmt = $con->opencon()->prepare("SELECT Category_ID FROM category WHERE Category_ID IN (" . implode(',', array_fill(0, count($categoryIds), '?')) . ")");
    $stmt->execute($categoryIds);
    $validCategoryIds = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'Category_ID');
    if (count($validCategoryIds) !== count($categoryIds)) {
        throw new Exception('One or more category IDs are invalid');
    }

    $result = $con->updateProduct($productId, $prodName, $prodStock, $prodPrice, $effectiveFrom, $effectiveTo, $categoryIds, $stockMode, $imagePath);

    error_log("Update Product Result: " . json_encode($result));
    echo json_encode($result);

} catch (Exception $e) {
    error_log('Update Product Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

ob_end_flush();
?>