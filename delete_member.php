<?php
session_start();
require_once 'classes/database.php';

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['customerId']) || !is_numeric($data['customerId'])) {
        throw new Exception('Valid Customer ID is required');
    }

    $customerId = (int)$data['customerId'];
    $con = new database();
    $result = $con->deleteMember($customerId);

    echo json_encode($result);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>