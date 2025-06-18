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
    $result = $con->getCustomerPoints($data['customerId']);

    echo json_encode($result);
} catch (Exception $e) {
    error_log('Get Customer Points Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}