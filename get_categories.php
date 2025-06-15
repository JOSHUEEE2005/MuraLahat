<?php
require_once 'classes/database.php';

header('Content-Type: application/json');

try {
    $con = new database();
    $categories = $con->viewCategory();
    echo json_encode(['success' => true, 'categories' => $categories]);
} catch (Exception $e) {
    error_log('Get Categories Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>