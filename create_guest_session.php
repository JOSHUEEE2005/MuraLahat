<?php
session_start();
require_once 'classes/database.php';

// Generate unique guest ID if not exists
if (!isset($_SESSION['customer_id'])) {
    $_SESSION['customer_id'] = 'guest_' . uniqid();
    $_SESSION['customer_type'] = 'guest';
}

echo json_encode(['success' => true]);