<?php
session_start();
require 'config.php';
$input = json_decode(file_get_contents('php://input'), true);
$action = isset($input['action']) ? $input['action'] : '';
header('Content-Type: application/json');
if ($action === 'register') {
    $name = trim($input['name']);
    $email = trim($input['email']);
    $password = $input['password'];
    if (empty($name) || empty($email) || empty($password)) { echo json_encode(['success'=>false,'message'=>'Semua field harus diisi']); exit; }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $mysqli->prepare('INSERT INTO users (name,email,password) VALUES (?,?,?)');
    $stmt->bind_param('sss', $name, $email, $hash);
    if ($stmt->execute()) {
        $_SESSION['user_id'] = $mysqli->insert_id;
        $_SESSION['user_name'] = $name;
        echo json_encode(['success'=>true,'message'=>'Registrasi berhasil']);
    } else {
        echo json_encode(['success'=>false,'message'=>'Email sudah terdaftar atau error']);
    }
    exit;
}
if ($action === 'login') {
    $email = trim($input['email']);
    $password = $input['password'];
    if (empty($email) || empty($password)) { echo json_encode(['success'=>false,'message'=>'Isi email dan password']); exit; }
    $stmt = $mysqli->prepare('SELECT id, name, password FROM users WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($user = $res->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            echo json_encode(['success'=>true,'message'=>'Login berhasil']);
        } else { echo json_encode(['success'=>false,'message'=>'Password salah']); }
    } else { echo json_encode(['success'=>false,'message'=>'User tidak ditemukan']); }
    exit;
}
if (isset($_GET['logout'])) { session_destroy(); header('Location: index.php'); exit; }
echo json_encode(['success'=>false,'message'=>'Invalid request']);
?>
