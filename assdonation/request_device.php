<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=request_device");
    exit;
}

$device_id = filter_input(INPUT_GET, 'device_id', FILTER_VALIDATE_INT);
if (!$device_id) {
    header("Location: devices.php?error=invalid_device");
    exit;
}

$stmt = $conn->prepare("SELECT d.*, u.username AS donor 
                       FROM devices d JOIN users u ON d.user_id = u.id 
                       WHERE d.id = ?");
$stmt->bind_param("i", $device_id);
$stmt->execute();
$device = $stmt->get_result()->fetch_assoc();

if (!$device) {
    header("Location: devices.php?error=device_not_found");
    exit;
}
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check existing approved requests
    $checkApproved = $conn->prepare("SELECT id FROM requests 
                                    WHERE user_id=? AND status='approved' 
                                    LIMIT 1");
    $checkApproved->bind_param("i", $_SESSION['user_id']);
    $checkApproved->execute();
    
    if ($checkApproved->get_result()->num_rows > 0) {
        $error = "⛔ You already have an approved device. Return your current device before requesting another.";
    } else {
        // Check duplicate requests
        $checkDuplicate = $conn->prepare("SELECT id FROM requests 
                                         WHERE device_id=? AND user_id=? 
                                         AND status IN ('pending', 'approved')");
        $checkDuplicate->bind_param("ii", $device_id, $_SESSION['user_id']);
        $checkDuplicate->execute();
        
        if ($checkDuplicate->get_result()->num_rows > 0) {
            $error = "⚠️ You've already requested this device.";
        } elseif (empty($_FILES['id_file']['tmp_name'])) {
            $error = "📄 Please upload your ID document for verification.";
        } else {
            // Secure file upload handling
            $id_file = $_FILES['id_file'];
            $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
            $max_size = 2 * 1024 * 1024; // 2MB
            
            if (!in_array($id_file['type'], $allowed_types)) {
                $error = "❌ Invalid file type. Only JPG, PNG, or PDF allowed.";
            } elseif ($id_file['size'] > $max_size) {
                $error = "📁 File too large. Maximum 2MB allowed.";
            } else {
                $file_ext = pathinfo($id_file['name'], PATHINFO_EXTENSION);
                $new_filename = 'id_'.bin2hex(random_bytes(8)).'.'.strtolower($file_ext);
                $upload_path = 'uploads/'.$new_filename;
                
                if (move_uploaded_file($id_file['tmp_name'], $upload_path)) {
                    // Insert request with transaction
                    $conn->begin_transaction();
                    try {
                        $insert = $conn->prepare("INSERT INTO requests 
                                                (device_id, user_id, status, requested_at, id_proof) 
                                                VALUES (?, ?, 'pending', NOW(), ?)");
                        $insert->bind_param("iis", $device_id, $_SESSION['user_id'], $new_filename);
                        
                        if ($insert->execute()) {
                            $conn->commit();
                            $message = " Request submitted successfully! Admin will review your application.";
                        } else {
                            throw new Exception("Database error: ".$conn->error);
                        }
                    } catch (Exception $e) {
                        $conn->rollback();
                        @unlink($upload_path);
                        $error = "⚠️ System error. Please try again later.";
                    }
                } else {
                    $error = "⚠️ File upload failed. Please try again.";
                }
            }
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request <?= htmlspecialchars($device['title']) ?> | Insieme</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #0069d9;
            --primary-light: #e6f2ff;
            --secondary: #10B981;
            --error: #EF4444;
            --success: #10B981;
            --text: #111827;
            --text-light: #6B7280;
            --radius: 12px;
            --shadow: 0 4px 6px rgba(0,0,0,0.05);
            --transition: all 0.2s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #e6f7ff, #f0f9ff);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            color: var(--text);
        }
        
        .container {
            max-width: 800px;
            width: 100%;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0, 105, 180, 0.15);
        }
        
        .header {
            background: linear-gradient(to right, #0069d9, #0052cc);
            padding: 30px 40px;
            text-align: center;
            color: white;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .logo {
            width: 60px;
            height: 60px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: #0069d9;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        h1 {
            font-size: 2.2rem;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .content {
            padding: 40px;
            position: relative;
        }
        
        .device-preview {
            display: flex;
            gap: 30px;
            margin-bottom: 30px;
            align-items: center;
            background: var(--primary-light);
            padding: 25px;
            border-radius: var(--radius);
            border-left: 4px solid var(--primary);
        }
        
        .device-image {
            width: 120px;
            height: 120px;
            border-radius: 12px;
            object-fit: cover;
            box-shadow: 0 8px 20px rgba(0, 105, 180, 0.1);
        }
        
        .device-info h2 {
            color: var(--primary);
            margin-bottom: 10px;
        }
        
        .device-donor {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-light);
        }
        
        .alert {
            padding: 18px;
            border-radius: var(--radius);
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.05rem;
        }
        
        .alert-success {
            background-color: rgba(16, 185, 129, 0.1);
            border-left: 4px solid var(--success);
            color: var(--success);
        }
        
        .alert-error {
            background-color: rgba(239, 68, 68, 0.1);
            border-left: 4px solid var(--error);
            color: var(--error);
        }
        
        .request-form {
            background: white;
            border-radius: 15px;
            padding: 35px;
            box-shadow: 0 10px 30px rgba(0, 105, 180, 0.1);
            border: 1px solid rgba(0, 105, 180, 0.1);
        }
        
        .form-title {
            color: var(--primary);
            font-size: 1.8rem;
            margin-bottom: 25px;
            text-align: center;
            font-weight: 700;
        }
        
        .file-upload-container {
            margin-bottom: 30px;
        }
        
        .file-upload-label {
            display: block;
            margin-bottom: 12px;
            font-weight: 600;
            color: var(--text);
        }
        
        .file-upload-description {
            color: var(--text-light);
            margin-bottom: 20px;
            font-size: 0.95rem;
        }
        
        .file-upload-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }
        
        .file-upload-button {
            border: 2px dashed #b3d7ff;
            border-radius: 50px;
            padding: 18px;
            font-size: 1.1rem;
            color: var(--primary);
            background: #f8fbff;
            width: 100%;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .file-upload-button:hover {
            background: #e6f2ff;
            border-color: var(--primary);
        }
        
        .file-upload-input {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        .file-name {
            margin-top: 12px;
            font-size: 0.95rem;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .btn {
            padding: 16px 30px;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-primary {
            background: linear-gradient(to right, #0069d9, #0052cc);
            color: white;
            box-shadow: 0 8px 20px rgba(0, 105, 180, 0.3);
            flex: 1;
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(0, 105, 180, 0.4);
        }
        
        .btn-outline {
            background: white;
            border: 2px solid #b3d7ff;
            color: var(--primary);
        }
        
        .btn-outline:hover {
            background: #e6f2ff;
        }
        
        .security-note {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 25px;
            color: var(--text-light);
            font-size: 0.95rem;
        }
        
        @media (max-width: 768px) {
            .device-preview {
                flex-direction: column;
                text-align: center;
            }
            
            .device-image {
                width: 100%;
                height: auto;
                max-height: 200px;
            }
            
            .button-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo-container">
                <div class="logo">
                    <i class="fas fa-hands-helping"></i>
                </div>
                <h1>Request Device</h1>
            </div>
        </div>
        
        <div class="content">
            <div class="device-preview">
                <?php if(!empty($device['image'])): ?>
                    <img src="uploads/<?= htmlspecialchars($device['image']) ?>" alt="<?= htmlspecialchars($device['title']) ?>" class="device-image">
                <?php else: ?>
                    <div class="device-image" style="background: #e6f2ff; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-medkit" style="font-size: 2.5rem; color: var(--primary);"></i>
                    </div>
                <?php endif; ?>
                <div class="device-info">
                    <h2><?= htmlspecialchars($device['title']) ?></h2>
                    <p class="device-donor">
                        <i class="fas fa-user"></i>
                        Donated by <?= htmlspecialchars($device['donor']) ?>
                    </p>
                </div>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <p><?= $message ?></p>
                        <p style="margin-top: 8px;">
                            <a href="index.php" style="color: inherit; text-decoration: underline;">Browse other devices</a>
                        </p>
                    </div>
                </div>
            <?php else: ?>
                <?php if($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <p><?= $error ?></p>
                    </div>
                <?php endif; ?>
                
                <form method="post" enctype="multipart/form-data" class="request-form">
                    <h3 class="form-title">Verification Required</h3>
                    
                    <div class="file-upload-container">
                        <label class="file-upload-label">Upload ID Document</label>
                        <p class="file-upload-description">
                            Please upload a clear photo or scan of your government-issued ID for verification.
                            Accepted formats: JPG, PNG, PDF (max 2MB)
                        </p>
                        
                        <div class="file-upload-wrapper">
                            <div class="file-upload-button">
                                <i class="fas fa-cloud-upload-alt"></i> Choose File
                            </div>
                            <input type="file" name="id_file" class="file-upload-input" accept=".jpg,.jpeg,.png,.pdf" required>
                        </div>
                        <div class="file-name" id="fileName">
                            <i class="fas fa-file-alt"></i>
                            <span>No file selected</span>
                        </div>
                    </div>
                    
                    <div class="button-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Submit Request
                        </button>
                        <a href="index.php" class="btn btn-outline">
                             Cancel
                        </a>
                    </div>
                    
                    <div class="security-note">
                        <i class="fas fa-lock"></i>
                        <span>Your information is securely stored and encrypted</span>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Show selected filename
        document.querySelector('.file-upload-input').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || 'No file selected';
            document.getElementById('fileName').innerHTML = `
                <i class="fas fa-file-alt"></i>
                <span>${fileName}</span>
            `;
        });
    </script>
</body>
</html>