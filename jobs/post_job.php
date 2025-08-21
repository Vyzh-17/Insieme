<?php
session_start();
include '../assets/db.php';

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $company = mysqli_real_escape_string($conn, $_POST['company_name']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $salary = mysqli_real_escape_string($conn, $_POST['salary']);
    $posted_by = $_SESSION['user_id'];

    // Handle Verification File Upload
    $file_path = '';
    if (isset($_FILES['verification_file']) && $_FILES['verification_file']['error'] === 0) {
        $upload_dir = '../uploads/verifications/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $filename = time() . '_' . basename($_FILES['verification_file']['name']);
        $target_path = $upload_dir . $filename;

        if (move_uploaded_file($_FILES['verification_file']['tmp_name'], $target_path)) {
            $file_path = 'uploads/verifications/' . $filename;
        } else {
            $message = 'Failed to upload verification file.';
            $message_type = 'error';
        }
    }

    // Insert Job Post with File Path
    if (empty($message)) {
        $sql = "INSERT INTO job_posts (title, description, company_name, location, salary, posted_by, verification_file)
                VALUES ('$title', '$desc', '$company', '$location', '$salary', $posted_by, '$file_path')";
        
        if (mysqli_query($conn, $sql)) {
            $message = 'Job post submitted for approval!';
            $message_type = 'success';
        } else {
            $message = 'Error: ' . mysqli_error($conn);
            $message_type = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post a Job - Insieme</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #af0000;
            --primary-gradient: linear-gradient(135deg, #840000 0%, #d90000 100%);
            --secondary: #FF6B6B;
            --accent: #36D1DC;
            --accent-gradient: linear-gradient(135deg, #36D1DC 0%, #5B86E5 100%);
            --dark: #2D2B55;
            --light: #F8F9FF;
            --success: #4CD964;
            --error: #FF6B6B;
            --card-bg: rgba(255, 255, 255, 0.92);
            --glass-bg: rgba(255, 255, 255, 0.25);
            --glass-border: rgba(255, 255, 255, 0.4);
            --shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #F5F7FA 0%, #E4E8F5 100%);
            color: var(--dark);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 15% 50%, rgba(108, 99, 255, 0.15) 0%, transparent 30%),
                radial-gradient(circle at 85% 30%, rgba(255, 107, 107, 0.15) 0%, transparent 30%);
            z-index: -1;
        }

        .floating-shapes {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: -1;
            overflow: hidden;
        }

        .shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.1;
        }

        .shape-1 {
            width: 300px;
            height: 300px;
            background: var(--primary);
            top: 10%;
            left: 5%;
            animation: float 15s infinite ease-in-out;
        }

        .shape-2 {
            width: 200px;
            height: 200px;
            background: var(--secondary);
            bottom: 15%;
            right: 5%;
            animation: float 12s infinite ease-in-out 2s;
        }

        .shape-3 {
            width: 150px;
            height: 150px;
            background: var(--accent);
            top: 60%;
            left: 10%;
            animation: float 10s infinite ease-in-out 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 20px auto;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo {
            font-family: 'Montserrat', sans-serif;
            font-size: 36px;
            font-weight: 900;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-flex;
            align-items: center;
            margin-bottom: 10px;
            text-decoration: none;
        }

        .logo i {
            margin-right: 15px;
            font-size: 40px;
        }

        .subtitle {
            color: var(--dark);
            font-size: 18px;
            opacity: 0.8;
        }

        .form-container {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 40px;
            box-shadow: var(--shadow);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            transition: var(--transition);
        }

        .form-container:hover {
            box-shadow: 0 15px 35px rgba(31, 38, 135, 0.2);
        }

        .form-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 30px;
            color: var(--dark);
            display: flex;
            align-items: center;
            position: relative;
        }

        .form-title i {
            margin-right: 15px;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .form-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 80px;
            height: 5px;
            background: var(--primary-gradient);
            border-radius: 5px;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
            display: flex;
            align-items: center;
        }

        .form-group label i {
            margin-right: 10px;
            color: var(--primary);
            width: 20px;
        }

        .form-control {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e6e6e6;
            border-radius: 12px;
            font-size: 16px;
            font-family: 'Poppins', sans-serif;
            transition: var(--transition);
            background: var(--light);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.2);
        }

        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }

        .file-upload-container {
            border: 2px dashed #e6e6e6;
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            transition: var(--transition);
            background: var(--light);
            cursor: pointer;
            position: relative;
        }

        .file-upload-container:hover {
            border-color: var(--primary);
            background: rgba(108, 99, 255, 0.05);
        }

        .file-upload-container i {
            font-size: 42px;
            color: var(--primary);
            margin-bottom: 15px;
        }

        .file-upload-text {
            color: var(--dark);
            margin-bottom: 15px;
        }

        .file-upload-text h4 {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .file-upload-text p {
            font-size: 14px;
            opacity: 0.7;
        }

        .file-input {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            opacity: 0;
            cursor: pointer;
        }

        .file-name {
            margin-top: 15px;
            font-size: 14px;
            color: var(--primary);
            font-weight: 500;
            display: none;
        }

        .btn-submit {
            background: var(--primary-gradient);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 18px 35px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: block;
            width: 100%;
            box-shadow: 0 5px 15px rgba(108, 99, 255, 0.3);
            position: relative;
            overflow: hidden;
        }

        .btn-submit::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: 0.5s;
        }

        .btn-submit:hover::before {
            left: 100%;
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(108, 99, 255, 0.4);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-submit i {
            margin-right: 10px;
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 10px;
            color: white;
            font-weight: 500;
            box-shadow: var(--shadow);
            transform: translateX(100%);
            opacity: 0;
            transition: all 0.5s cubic-bezier(0.68, -0.55, 0.27, 1.55);
            z-index: 1000;
            display: flex;
            align-items: center;
        }

        .notification.success {
            background: var(--success);
            transform: translateX(0);
            opacity: 1;
        }

        .notification.error {
            background: var(--error);
            transform: translateX(0);
            opacity: 1;
        }

        .notification i {
            margin-right: 10px;
            font-size: 20px;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            color: var(--dark);
            opacity: 0.7;
            font-size: 14px;
        }

        .message {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .message.success {
            background: rgba(76, 217, 100, 0.2);
            color: var(--success);
            border: 1px solid rgba(76, 217, 100, 0.3);
        }

        .message.error {
            background: rgba(255, 107, 107, 0.2);
            color: var(--error);
            border: 1px solid rgba(255, 107, 107, 0.3);
        }

        .message i {
            margin-right: 10px;
            font-size: 20px;
        }

        .nav-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        .nav-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            transition: var(--transition);
        }

        .nav-link:hover {
            color: var(--dark);
        }

        .nav-link i {
            margin-right: 8px;
        }

        @media (max-width: 768px) {
            .form-container {
                padding: 30px 20px;
            }
            
            .logo {
                font-size: 28px;
            }
            
            .form-title {
                font-size: 24px;
            }
            
            .btn-submit {
                padding: 15px 25px;
            }
            
            .nav-links {
                flex-direction: column;
                align-items: center;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="floating-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>

    <div class="container">
        <div class="header">
            <a href="recruiter_panel.php" class="logo">
                <i class="fas fa-hands-helping"></i>
                <span>Insieme</span>
            </a>
            <p class="subtitle">Post a new job opportunity</p>
            
            <div class="nav-links">
                <a href="recruiter_panel.php" class="nav-link">
                    <i class="fas fa-briefcase"></i> My Jobs
                </a>
                <a href="../logout.php" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <form method="post" enctype="multipart/form-data" class="form-container" id="jobForm">
            <h2 class="form-title">
                <i class="fas fa-briefcase"></i> Job Details
            </h2>

            <!-- PHP message display -->
            <?php if (!empty($message)): ?>
                <div class="message <?= $message_type ?>">
                    <i class="fas fa-<?= $message_type == 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i> 
                    <?= $message ?>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="title"><i class="fas fa-pen"></i> Job Title</label>
                <input type="text" id="title" name="title" class="form-control" placeholder="e.g. Senior Web Developer" required>
            </div>

            <div class="form-group">
                <label for="company_name"><i class="fas fa-building"></i> Company Name</label>
                <input type="text" id="company_name" name="company_name" class="form-control" placeholder="e.g. Insieme Technologies" required>
            </div>

            <div class="form-group">
                <label for="location"><i class="fas fa-map-marker-alt"></i> Location</label>
                <input type="text" id="location" name="location" class="form-control" placeholder="e.g. New York, NY or Remote" required>
            </div>

            <div class="form-group">
                <label for="salary"><i class="fas fa-money-bill-wave"></i> Salary</label>
                <input type="text" id="salary" name="salary" class="form-control" placeholder="e.g. $80,000 - $100,000 per year" required>
            </div>

            <div class="form-group">
                <label for="description"><i class="fas fa-file-alt"></i> Job Description</label>
                <textarea id="description" name="description" class="form-control" placeholder="Describe the job responsibilities, requirements, and benefits..." required></textarea>
            </div>

            <div class="form-group">
                <label><i class="fas fa-file-upload"></i> Verification Document</label>
                <div class="file-upload-container" id="fileUploadContainer">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <div class="file-upload-text">
                        <h4>Upload verification file</h4>
                        <p>PDF, DOC, DOCX, JPG, or PNG (Max: 5MB)</p>
                    </div>
                    <input type="file" id="verification_file" name="verification_file" class="file-input" accept=".pdf,.doc,.docx,.jpg,.png" required>
                    <div class="file-name" id="fileName"></div>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-paper-plane"></i> Submit Job for Approval
            </button>
        </form>

        <div class="footer">
            <p>© <?php echo date('Y'); ?> Insieme Recruiter Portal. All rights reserved.</p>
        </div>
    </div>

    <script>
        // File upload name display
        document.getElementById('verification_file').addEventListener('change', function(e) {
            const fileName = document.getElementById('fileName');
            if (this.files.length > 0) {
                fileName.textContent = this.files[0].name;
                fileName.style.display = 'block';
            } else {
                fileName.style.display = 'none';
            }
        });

        // Form validation
        document.getElementById('jobForm').addEventListener('submit', function(e) {
            let isValid = true;
            const inputs = this.querySelectorAll('input[required], textarea[required]');
            
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.style.borderColor = 'var(--error)';
                } else {
                    input.style.borderColor = '';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                showNotification('Please fill in all required fields', 'error');
            }
        });

        // Show notification function
        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${message}`;
            document.body.appendChild(notification);
            
            // Remove notification after 5 seconds
            setTimeout(() => {
                notification.remove();
            }, 5000);
        }
    </script>
</body>
</html>