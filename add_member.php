<?php
ob_start();
session_start();
require_once 'classes/database.php';

header('Content-Type: application/json');

$con = new database();
$response = ['success' => false, 'error' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log('POST Data: ' . print_r($_POST, true));
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName = trim($_POST['lastName'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $street = trim($_POST['street'] ?? '');
    $barangay = trim($_POST['barangay'] ?? '');
    $city = trim($_POST['city'] ?? '');

    try {
        if (empty($firstName) || empty($lastName) || empty($phone) || empty($street) || empty($barangay) || empty($city)) {
            throw new Exception('All fields are required.');
        }
        if (!preg_match('/^[0-9]{10}$/', $phone)) {
            throw new Exception('Phone number must be 10 digits.');
        }

        $result = $con->addCustomerMembership($firstName, $lastName, $phone, $street, $barangay, $city);
        if ($result['success']) {
            $response = [
                'success' => true,
                'customerId' => $result['customerId'],
                'message' => "Member added successfully! Customer ID: {$result['customerId']}"
            ];
        } else {
            throw new Exception($result['error'] ?? 'Failed to add member.');
        }
    } catch (Exception $e) {
        $response['error'] = $e->getMessage();
        error_log('Add Member Error: ' . $e->getMessage());
    }
} else {
    $response['error'] = 'Invalid request method.';
}

echo json_encode($response);
ob_end_flush();
?>