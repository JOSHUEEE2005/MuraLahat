<?php
session_start();
require_once 'classes/database.php';

$response = ['success' => false, 'error' => ''];

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['customerType'])) {
        throw new Exception('Customer type not specified');
    }
    
    $con = new database();
    
    if ($data['customerType'] == 'non-member') {
        $_SESSION['customer_type'] = 'non-member';
        $_SESSION['customer_id'] = $con->getOrCreateDefaultCustomer();
        $response['success'] = true;
    } else {
        throw new Exception('Invalid customer type');
    }
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?>