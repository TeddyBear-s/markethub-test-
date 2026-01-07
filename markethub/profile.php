<?php
session_start();
require 'config.php';
if (!isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }
$user_id = $_SESSION['user_id'];

if (isset($_POST['update_profile'])) {
    $name = $mysqli->real_escape_string($_POST['name']);
    $dob = $mysqli->real_escape_string($_POST['birth_date']);
    $phone = $mysqli->real_escape_string($_POST['phone']);
    $addr = $mysqli->real_escape_string($_POST['address']);
    
    $stmt = $mysqli->prepare("UPDATE users SET name=?, birth_date=?, phone=?, address=? WHERE id=?");
    $stmt->bind_param('ssssi', $name, $dob, $phone, $addr, $user_id);
    
    if ($stmt->execute()) {
        $_SESSION['user_name'] = $name; 
        
        header("Location: profile.php?msg=updated"); 
        exit;
    }
}

if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
    $target_dir = "uploads/profile/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
    
    $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
    $filename = "user_" . $user_id . "_" . time() . "." . $ext;
    $target_file = $target_dir . $filename;

    if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_file)) {
        $mysqli->query("UPDATE users SET profile_image='$target_file' WHERE id=$user_id");
        header("Location: profile.php?msg=photo_uploaded"); exit;
    }
}

if (isset($_GET['delete_photo'])) {
    $res = $mysqli->query("SELECT profile_image FROM users WHERE id=$user_id");
    $u = $res->fetch_assoc();
    if ($u['profile_image'] && file_exists($u['profile_image'])) unlink($u['profile_image']);
    
    $mysqli->query("UPDATE users SET profile_image=NULL WHERE id=$user_id");
    header("Location: profile.php"); exit;
}

$res = $mysqli->query("SELECT * FROM users WHERE id=$user_id");
$user = $res->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Profil - MarketHUB</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
    :root {
        --primary: #2563eb;
        --bg: #f3f4f6;
    }

    body {
        font-family: 'Inter', sans-serif;
        background: var(--bg);
        margin: 0;
        padding: 20px;
        color: #1f2937;
    }

    .container {
        max-width: 900px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 300px 1fr;
        gap: 20px;
    }

    .card {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .profile-pic-container {
        text-align: center;
        margin-bottom: 20px;
    }

    .profile-pic {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid white;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        background: #e5e7eb;
    }

    .btn-upload {
        display: inline-block;
        margin-top: 10px;
        font-size: 13px;
        color: var(--primary);
        cursor: pointer;
        font-weight: 600;
    }

    .btn-delete {
        color: #ef4444;
        font-size: 12px;
        text-decoration: none;
        display: block;
        margin-top: 5px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 5px;
        color: #4b5563;
    }

    input,
    textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-family: inherit;
        box-sizing: border-box;
    }

    .btn-save {
        background: var(--primary);
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        width: 100%;
    }

    .header-nav {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
        grid-column: span 2;
    }

    @media (max-width: 768px) {
        .container {
            grid-template-columns: 1fr;
        }

        .header-nav {
            grid-column: span 1;
        }
    }
</style>
</head>
<body>

<div class="container">
    <div class="header-nav">
        <a href="index.php" style="text-decoration:none; color: var(--primary); font-weight:600;">← Kembali Belanja</a>
        <a href="?logout=1" style="color: #ef4444; text-decoration:none;">Logout</a>
    </div>

    <div class="card">
        <div class="profile-pic-container">
            <?php 
                $img = (!empty($user['profile_image']) && file_exists($user['profile_image'])) ? $user['profile_image'] : 'https://ui-avatars.com/api/?name='.urlencode($user['name']).'&size=150';
            ?>
            <img src="<?php echo $img; ?>" class="profile-pic" alt="Profile">
            
            <form action="" method="POST" enctype="multipart/form-data" id="photoForm">
                <label class="btn-upload">
                    Ganti Foto
                    <input type="file" name="profile_pic" style="display:none" onchange="document.getElementById('photoForm').submit()">
                </label>
            </form>
            
            <?php if(!empty($user['profile_image'])): ?>
                <a href="?delete_photo=1" class="btn-delete" onclick="return confirm('Hapus foto profil?')">Hapus Foto</a>
            <?php endif; ?>
        </div>
        
        <div style="border-top: 1px solid #eee; padding-top: 15px;">
            <p style="margin:0; font-size: 14px; color: #6b7280;">Email:</p>
            <p style="margin:5px 0 15px; font-weight: 600;"><?php echo $user['email']; ?></p>
            <p style="margin:0; font-size: 14px; color: #6b7280;">Member Sejak:</p>
            <p style="margin:5px 0; font-weight: 600;"><?php echo date('d M Y', strtotime($user['created_at'])); ?></p>
        </div>
    </div>

    <div class="card">
        <h2 style="margin-top:0">Edit Profil</h2>
        <form method="POST">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group">
                    <label>Tanggal Lahir</label>
                    <input type="date" name="birth_date" value="<?php echo $user['birth_date']; ?>">
                </div>
                <div class="form-group">
                    <label>Nomor HP</label>
                    <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" placeholder="0812...">
                </div>
            </div>

            <div class="form-group">
                <label>Alamat Lengkap</label>
                <textarea name="address" rows="4"><?php echo htmlspecialchars($user['address']); ?></textarea>
            </div>

            <button type="submit" name="update_profile" class="btn-save">Simpan Perubahan</button>
        </form>
    </div>
</div>

</body>
</html>