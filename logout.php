<?php
session_start();
require_once 'classes/database.php';

$con = new database();

if (isset($_SESSION['user_id']) && isset($_SESSION['session_id'])) {
    $result = $con->logoutUser($_SESSION['user_id'], $_SESSION['session_id']);
    if ($result['success']) {
        session_unset();
        session_destroy();
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => $result['error']]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'No active session found']);
}
exit;
?>