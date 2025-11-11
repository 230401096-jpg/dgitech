<?php
require_once __DIR__ . '/../includes/config.php';
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status'=>'error','message'=>'User belum login']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || empty($input['items'])) {
    echo json_encode(['status'=>'error','message'=>'Data item kosong']);
    exit;
}

// logic insert order ...
// (sesuaikan seperti checkout.php)
$order_id = 0; // hasil insert

echo json_encode(['status'=>'success','order_id'=>$order_id]);
