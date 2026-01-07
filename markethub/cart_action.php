<?php
session_start();
require 'config.php';
$input = json_decode(file_get_contents('php://input'), true);
$action = isset($input['action']) ? $input['action'] : '';
header('Content-Type: application/json');
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = array();
if ($action === 'add') {
    $pid = intval($input['product_id']);
    if ($pid <= 0) { echo json_encode(array('success'=>false,'message'=>'Invalid product')); exit; }
    if (!isset($_SESSION['cart'][$pid])) $_SESSION['cart'][$pid] = 0;
    $_SESSION['cart'][$pid] += 1;
    echo json_encode(array('success'=>true,'message'=>'Added')); exit;
}
if ($action === 'update') {
    $pid = intval($input['product_id']);
    $qty = intval($input['qty']);
    if ($qty <= 0) { unset($_SESSION['cart'][$pid]); } else { $_SESSION['cart'][$pid] = $qty; }
    echo json_encode(array('success'=>true)); exit;
}
if ($action === 'remove') { $pid = intval($input['product_id']); unset($_SESSION['cart'][$pid]); echo json_encode(array('success'=>true)); exit; }
if ($action === 'clear') { $_SESSION['cart'] = array(); echo json_encode(array('success'=>true)); exit; }
echo json_encode(array('success'=>false,'message'=>'Invalid action'));
?>
