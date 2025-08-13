<?php
/* ===== SESSION INITIALIZATION & DATABASE CONNECTION ===== */
session_start();
include 'db.php';

/* ===== VARIABLE INITIALIZATION ===== */
$error = '';
$username = '';

/* ===== LOGIN PROCESSING ===== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $username = trim(htmlspecialchars($_POST['username']));
    $password = $_POST['password'];

    // Database query with prepared statement
    $stmt = $conn->prepare("SELECT id, password, is_admin FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hash, $is_admin);
        $stmt->fetch();
        
        if (password_verify($password, $hash)) {
            // Successful login
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['is_admin'] = (bool)$is_admin;  // Explicit boolean conversion
            
            // Regenerate session ID for security
            session_regenerate_id(true);
            
            header("Location: index.php");
            exit;
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "User not found.";
    }
    
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Insieme</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ===== GLOBAL STYLES ===== */
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
            color: #333;
        }
        
        /* ===== HEADER STYLE (MATCHING REFERENCE) ===== */
        .header {
            background: linear-gradient(to right, #0069d9, #0052cc);
            padding: 30px 40px;
            text-align: center;
            color: white;
            border-radius: 20px 20px 0 0;
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
        
        /* ===== LOGIN CONTAINER ===== */
        .login-container {
            background: white;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 15px 40px rgba(0, 105, 180, 0.15);
            width: 100%;
            max-width: 400px;
            overflow: hidden;
        }
        
        /* ===== FORM STYLES ===== */
        .form-content {
            padding: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #0069d9;
            font-weight: 600;
        }
        
        input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #b3d7ff;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
            background: #f8fbff;
        }
          button {
            background: linear-gradient(to right, #0069d9, #0052cc);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s;
            margin-top: 0.5rem;
        }
        
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 105, 180, 0.3);
        }
        
        /* ===== LINK STYLES ===== */
        .register-link {
            margin-top: 1.5rem;
            color: #666;
        }
        
        .register-link a {
            color: #0069d9;
            text-decoration: none;
            font-weight: 600;
        }
        
        .register-link a:hover {
            text-decoration: underline;
        }
        /* [Keep all other CSS styles from previous version] */
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Blue Header with Logo -->
        <div class="header">
            <div class="logo-container">
                <div class="logo">
                    <a href="index.php" style="color: inherit; text-decoration: none;">
                        <i class="fas fa-hands-helping"></i>
                    </a>
                </div>
                <h1>Insieme</h1>
            </div>
            <p style="opacity: 0.9;">- Supporting those who need support -</p>
        </div>
        
        <!-- Login Form -->
        <div class="form-content">
            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="post">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" 
                           value="<?php echo htmlspecialchars($username); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>
            
            <p class="register-link">
                Don't have an account? <a href="register.php">Register here</a>
            </p>
        </div>
    </div>
</body>
</html>