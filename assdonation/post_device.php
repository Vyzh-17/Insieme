<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    
    if (!$title || !$description) {
        $error = "Title and description are required.";
    } elseif (!isset($_FILES['image']) || $_FILES['image']['error'] !== 0) {
        $error = "Image upload is required.";
    } else {
        $img = $_FILES['image'];
        $ext = pathinfo($img['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg','jpeg','png','gif'];
        if (!in_array(strtolower($ext), $allowed)) {
            $error = "Allowed image types: jpg, jpeg, png, gif.";
        } else {
            $newName = uniqid() . '.' . $ext;
            if (move_uploaded_file($img['tmp_name'], 'uploads/' . $newName)) {
                $stmt = $conn->prepare("INSERT INTO devices (user_id, title, description, image, posted_at) VALUES (?, ?, ?, ?, NOW())");
                $stmt->bind_param("isss", $_SESSION['user_id'], $title, $description, $newName);
                if ($stmt->execute()) {
                    header("Location: index.php");
                    exit;
                } else {
                    $error = "Database error: " . $conn->error;
                }
            } else {
                $error = "Failed to move uploaded file.";
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
    <title>Post a Device | TechShare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', 'Segoe UI', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f0f4f8 0%, #e2e8f0 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            color: #2d3748;
            position: relative;
            overflow-x: hidden;
        }
        
        /* Decorative background elements */
        .bg-circle {
            position: fixed;
            border-radius: 50%;
            filter: blur(60px);
            z-index: -1;
            opacity: 0.15;
        }
        
        .circle-1 {
            width: 400px;
            height: 400px;
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            top: -100px;
            left: -100px;
        }
        
        .circle-2 {
            width: 500px;
            height: 500px;
            background: linear-gradient(135deg, #ff9a9e 0%, #fad0c4 100%);
            bottom: -150px;
            right: -150px;
        }
        
        .circle-3 {
            width: 300px;
            height: 300px;
            background: linear-gradient(135deg, #42e695 0%, #3bb2b8 100%);
            top: 50%;
            right: 10%;
        }
        
        .floating {
            position: absolute;
            z-index: -1;
            opacity: 0.1;
        }
        
        .floating-1 {
            top: 10%;
            left: 15%;
            font-size: 4rem;
            animation: float 15s infinite ease-in-out;
            color: #6a11cb;
        }
        
        .floating-2 {
            bottom: 20%;
            right: 15%;
            font-size: 5rem;
            animation: float 18s infinite ease-in-out reverse;
            color: #2575fc;
        }
        
        .floating-3 {
            top: 30%;
            right: 20%;
            font-size: 3.5rem;
            animation: float 12s infinite ease-in-out;
            color: #42e695;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(10deg); }
        }
        
        .container {
            width: 100%;
            max-width: 650px;
            background: rgba(255, 255, 255, 0.92);
            border-radius: 24px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.12);
            overflow: hidden;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            position: relative;
            z-index: 10;
        }
        
        .header {
            background: linear-gradient(to right, #6a11cb 0%, #2575fc 100%);
            color: white;
            padding: 45px 30px 35px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: "";
            position: absolute;
            top: -50px;
            left: -50px;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }
        
        .header::after {
            content: "";
            position: absolute;
            bottom: -60px;
            right: -60px;
            width: 180px;
            height: 180px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
        }
        
        .device-icon {
            width: 90px;
            height: 90px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 42px;
            backdrop-filter: blur(5px);
            border: 2px solid rgba(255, 255, 255, 0.3);
            transform: rotate(45deg);
            position: relative;
            z-index: 2;
        }
        
        .device-icon i {
            transform: rotate(-45deg);
        }
        
        .header h1 {
            font-size: 2.6rem;
            margin-bottom: 10px;
            font-weight: 800;
            position: relative;
            z-index: 2;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        
        .header p {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 500px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
            font-weight: 300;
        }
        
        .form-container {
            padding: 45px 40px 40px;
        }
        
        .error-message {
            background: linear-gradient(to right, #ff416c, #ff4b2b);
            color: white;
            padding: 18px;
            border-radius: 14px;
            margin-bottom: 30px;
            display: <?php echo $error ? 'flex' : 'none'; ?>;
            align-items: center;
            gap: 12px;
            animation: fadeIn 0.4s ease;
            box-shadow: 0 5px 15px rgba(255, 75, 43, 0.25);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .form-group {
            margin-bottom: 30px;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 14px;
            font-weight: 600;
            color: #2c3e50;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .form-group label i {
            color: #6a11cb;
            background: rgba(106, 17, 203, 0.1);
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 18px;
        }
        
        .form-control {
            width: 100%;
            padding: 18px 22px;
            border: 2px solid #e1e8ed;
            border-radius: 16px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            background: #f8fafc;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        
        .form-control:focus {
            border-color: #6a11cb;
            background: white;
            box-shadow: 0 0 0 4px rgba(106, 17, 203, 0.15);
            outline: none;
        }
        
        textarea.form-control {
            min-height: 160px;
            resize: vertical;
        }
        
        .image-upload {
            position: relative;
            margin-top: 10px;
        }
        
        .image-preview {
            width: 100%;
            height: 220px;
            background: #f8fafc;
            border: 3px dashed #cbd5e0;
            border-radius: 16px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s ease;
            overflow: hidden;
            position: relative;
            background-image: 
                linear-gradient(45deg, #edf2f7 25%, transparent 25%),
                linear-gradient(-45deg, #edf2f7 25%, transparent 25%),
                linear-gradient(45deg, transparent 75%, #edf2f7 75%),
                linear-gradient(-45deg, transparent 75%, #edf2f7 75%);
            background-size: 20px 20px;
            background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
        }
        
        .image-preview:hover {
            border-color: #6a11cb;
            background-color: #f0f4f8;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
        }
        
        .image-preview i {
            font-size: 60px;
            color: #a0aec0;
            margin-bottom: 20px;
            transition: all 0.3s;
        }
        
        .image-preview span {
            color: #718096;
            font-size: 1.15rem;
            text-align: center;
            max-width: 80%;
            line-height: 1.5;
        }
        
        #previewImage {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
        }
        
        #fileInput {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            opacity: 0;
            cursor: pointer;
        }
        
        .file-info {
            margin-top: 15px;
            font-size: 1rem;
            color: #4a5568;
            text-align: center;
            font-weight: 500;
        }
        
        .btn-submit {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: white;
            border: none;
            padding: 20px;
            font-size: 1.2rem;
            border-radius: 16px;
            cursor: pointer;
            font-weight: 700;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(106, 17, 203, 0.35);
            margin-top: 15px;
            position: relative;
            overflow: hidden;
            letter-spacing: 0.5px;
        }
        
        .btn-submit::before {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: 0.5s;
        }
        
        .btn-submit:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(106, 17, 203, 0.45);
        }
        
        .btn-submit:hover::before {
            left: 100%;
        }
        
        .btn-submit:active {
            transform: translateY(0);
        }
        
        .btn-submit i {
            margin-right: 10px;
        }
        
        .back-link {
            display: inline-block;
            margin-top: 30px;
            text-align: center;
            width: 100%;
            color: #6a11cb;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .back-link:hover {
            color: #2575fc;
            text-decoration: underline;
        }
        
        .form-footer {
            text-align: center;
            margin-top: 25px;
            font-size: 1rem;
            color: #718096;
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .requirements {
            background: linear-gradient(to right, #f8fafc, #edf2f7);
            border-radius: 16px;
            padding: 25px;
            margin-top: 30px;
            border-left: 4px solid #6a11cb;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .requirements h3 {
            margin-bottom: 15px;
            color: #2c3e50;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.3rem;
        }
        
        .requirements ul {
            padding-left: 25px;
            color: #4a5568;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        
        .requirements li {
            margin-bottom: 12px;
            position: relative;
            padding-left: 25px;
        }
        
        .requirements li::before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #6a11cb;
            font-weight: bold;
        }
        
        @media (max-width: 768px) {
            .requirements ul {
                grid-template-columns: 1fr;
            }
            
            .container {
                max-width: 95%;
            }
            
            .form-container {
                padding: 35px 25px 30px;
            }
            
            .header {
                padding: 35px 20px 25px;
            }
            
            .header h1 {
                font-size: 2.2rem;
            }
        }
        
        @media (max-width: 480px) {
            .device-icon {
                width: 70px;
                height: 70px;
                font-size: 30px;
            }
            
            .form-group label {
                font-size: 1rem;
            }
            
            .form-control {
                padding: 15px 18px;
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Decorative background elements -->
    <div class="bg-circle circle-1"></div>
    <div class="bg-circle circle-2"></div>
    <div class="bg-circle circle-3"></div>
    
    <div class="floating floating-1"> <i class="fas fa-hands-helping"></i></div>
    <div class="floating floating-2"> <i class="fas fa-hands-helping"></i></div>
    <div class="floating floating-3"> <i class="fas fa-hands-helping"></i></div>
    
    <div class="container">
        <div class="header">
            <div class="device-icon">
                 <i class="fas fa-hands-helping"></i>
            </div>
            <h1>Donate your Device</h1>
            <p>Share your device with our community</p>
        </div>
        
        <div class="form-container">
            <div class="error-message" id="errorMessage">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo $error; ?></span>
            </div>
            
            <form method="post" enctype="multipart/form-data" id="deviceForm">
                <div class="form-group">
                    <label for="title"><i class="fas fa-heading"></i> Device Name</label>
                    <input type="text" id="title" name="title" class="form-control" placeholder="Enter device name " required>
                </div>
                
                <div class="form-group">
                    <label for="description"><i class="fas fa-align-left"></i> Description</label>
                    <textarea id="description" name="description" class="form-control" placeholder="Describe your device features, condition, specifications, and any special details..." required></textarea>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-camera"></i> Device Image</label>
                    <div class="image-upload">
                        <div class="image-preview" id="imagePreview">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>Click or drag to upload a high-quality image of your device</span>
                            <img id="previewImage" src="" alt="Preview">
                            <input type="file" id="fileInput" name="image" accept="image/*" required>
                        </div>
                        <div class="file-info" id="fileInfo">No file selected</div>
                    </div>
                </div>
                
                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i> Post Device
                </button>
            </form>
            
            <a href="index.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to Home
            </a>
            
            <div class="form-footer">
                <p><i class="fas fa-file-image"></i> Supported: JPG, PNG, GIF</p>
                <p><i class="fas fa-weight-hanging"></i> Max size: 5MB</p>
                <p><i class="fas fa-shield-alt"></i> Secure upload</p>
            </div>
            
          
        </div>
    </div>
    
    <script>
        // Image preview functionality
        const fileInput = document.getElementById('fileInput');
        const imagePreview = document.getElementById('imagePreview');
        const previewImage = document.getElementById('previewImage');
        const fileInfo = document.getElementById('fileInfo');
        const uploadIcon = imagePreview.querySelector('i');
        const uploadText = imagePreview.querySelector('span');
        const errorMessage = document.getElementById('errorMessage');
        
        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            
            if (file) {
                // Check file size (max 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    showError("File size exceeds 5MB limit");
                    this.value = '';
                    return;
                }
                
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewImage.style.display = 'block';
                    uploadIcon.style.display = 'none';
                    uploadText.style.display = 'none';
                    fileInfo.textContent = `Selected: ${file.name} (${formatFileSize(file.size)})`;
                    fileInfo.style.color = '#2d3748';
                }
                
                reader.readAsDataURL(file);
            } else {
                previewImage.style.display = 'none';
                uploadIcon.style.display = 'block';
                uploadText.style.display = 'block';
                fileInfo.textContent = 'No file selected';
                fileInfo.style.color = '#718096';
            }
        });
        
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
        
        // Form validation
        const form = document.getElementById('deviceForm');
        
        form.addEventListener('submit', function(e) {
            let valid = true;
            const title = document.getElementById('title').value.trim();
            const description = document.getElementById('description').value.trim();
            const image = document.getElementById('fileInput').files.length;
            
            if (!title) {
                showError("Please enter a device title");
                valid = false;
            } else if (!description) {
                showError("Please provide a device description");
                valid = false;
            } else if (!image) {
                showError("Please upload an image of your device");
                valid = false;
            }
            
            if (!valid) {
                e.preventDefault();
            }
        });
        
        function showError(message) {
            errorMessage.querySelector('span').textContent = message;
            errorMessage.style.display = 'flex';
            
            // Auto-hide error after 5 seconds
            setTimeout(() => {
                errorMessage.style.display = 'none';
            }, 5000);
        }
        
        // Drag and drop functionality
        imagePreview.addEventListener('dragover', (e) => {
            e.preventDefault();
            imagePreview.style.borderColor = '#6a11cb';
            imagePreview.style.backgroundColor = '#e2e8f0';
            imagePreview.style.transform = 'scale(1.02)';
        });
        
        imagePreview.addEventListener('dragleave', () => {
            imagePreview.style.borderColor = '#cbd5e0';
            imagePreview.style.backgroundColor = '#f8fafc';
            imagePreview.style.transform = 'scale(1)';
        });
        
        imagePreview.addEventListener('drop', (e) => {
            e.preventDefault();
            imagePreview.style.borderColor = '#cbd5e0';
            imagePreview.style.backgroundColor = '#f8fafc';
            imagePreview.style.transform = 'scale(1)';
            
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                const event = new Event('change');
                fileInput.dispatchEvent(event);
            }
        });
    </script>
</body>
</html>