<?php
require_once '../classes/database.php';

header('Content-Type: application/json');

if (isset($_POST['username'])) {
    $username = $_POST['username'];
    $con = new database();

    $db = $con->opencon();
    if (!$db) {
        echo json_encode(['error' => 'Database connection failed']);
        exit;
    }

    $query = $db->prepare("SELECT Username FROM user_account WHERE Username = ?");
    $query->execute([$username]);
    $existingUser = $query->fetch();

    if ($existingUser) {
        echo json_encode(['exists' => true]);
    } else {
        echo json_encode(['exists' => false]);
    }
}
?>