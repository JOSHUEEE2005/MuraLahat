<?php
ob_start(); // Start output buffering
ini_set('display_errors', 0); // Disable error display
ini_set('log_errors', 1); // Enable error logging
ini_set('error_log', 'php_errors.log'); // Set error log file

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'classes/database.php';

header('Content-Type: application/json');
$db = new database();
$response = ['success' => false, 'error' => ''];

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    error_log('Generated CSRF token: ' . $_SESSION['csrf_token'] . ' | Session ID: ' . session_id());
}

error_log('Request Method: ' . $_SERVER['REQUEST_METHOD']);
error_log('Session ID: ' . session_id());
error_log('Session Data: ' . print_r($_SESSION, true));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log('POST Data: ' . print_r($_POST, true));
    error_log('POST CSRF token: ' . ($_POST['csrf_token'] ?? 'none'));
    error_log('Session CSRF token: ' . ($_SESSION['csrf_token'] ?? 'none'));

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $response['error'] = 'Invalid CSRF token';
        error_log('CSRF validation failed');
        echo json_encode($response);
        ob_end_clean(); // Clean buffer
        exit;
    }

    $firstName = trim(preg_replace('/\s+/', ' ', $_POST['firstName'] ?? ''));
    $lastName = trim(preg_replace('/\s+/', ' ', $_POST['lastName'] ?? ''));
    $phoneNumber = trim($_POST['phoneNumber'] ?? '');
    $street = trim($_POST['street'] ?? '');
    $barangay = trim($_POST['barangay'] ?? '');
    $city = trim($_POST['city'] ?? '');

    if (empty($firstName) || empty($lastName) || empty($phoneNumber) || empty($street) || empty($barangay) || empty($city)) {
        $response['error'] = 'All fields are required';
    } elseif (!preg_match('/^[0-9]{10}$/', $phoneNumber)) {
        $response['error'] = 'Phone number must be 10 digits';
    } else {
        try {
            $result = $db->addCustomerMembership($firstName, $lastName, $phoneNumber, $street, $barangay, $city);
            if ($result['success']) {
                $response['success'] = true;
                $response['customerId'] = $result['customerId'];
                $response['message'] = "New member added! Customer ID: {$result['customerId']}";
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                error_log('New CSRF token: ' . $_SESSION['csrf_token']);
            } else {
                $response['error'] = $result['error'] ?? 'Failed to add member';
            }
        } catch (Exception $e) {
            $response['error'] = 'Error: ' . $e->getMessage();
            error_log('Exception: ' . $e->getMessage());
        }
    }
} else {
    $response['error'] = 'Invalid request method';
}

$response['csrf_token'] = $_SESSION['csrf_token'];
error_log('Response: ' . json_encode($response));
echo json_encode($response);
ob_end_clean();
exit;
?>