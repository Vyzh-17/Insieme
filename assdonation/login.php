<?php
session_start();
include 'db.php';

/* ===== VARIABLE INITIALIZATION ===== */
$error = '';
$username = '';
$role = 'user'; // default

/* ===== LOGIN PROCESSING ===== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim(htmlspecialchars($_POST['username']));
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Database query with prepared statement (role included)
    $stmt = $conn->prepare("SELECT id, password, is_admin, role FROM users WHERE username = ? AND role = ?");
    $stmt->bind_param("ss", $username, $role);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hash, $is_admin, $db_role);
        $stmt->fetch();

        if (password_verify($password, $hash)) {
            // Successful login
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['is_admin'] = (bool)$is_admin;
            $_SESSION['role'] = $db_role;

            session_regenerate_id(true);

            // Redirect based on role
            if ($db_role === 'recruiter') {
                header("Location: ../jobs/recruiter_panel.php");
            } else if($is_admin) {
                header("Location: ../admin.html");
            }
            else{
              header("Location: index.php");
            }
            exit;
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "Invalid credentials or role mismatch.";
    }

    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login | Insieme</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@700;800;900&display=swap" rel="stylesheet">
  <style>
    :root {
        --primary: #6C63FF;
        --primary-gradient: linear-gradient(135deg, #6C63FF 0%, #4A40E5 100%);
        --secondary: #FF6B6B;
        --accent: #36D1DC;
        --dark: #2D2B55;
        --light: #F8F9FF;
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

    .login-container {
        width: 100%;
        max-width: 450px;
        margin: 20px auto;
    }

    .header {
        text-align: center;
        margin-bottom: 40px;
    }

    .logo-container {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
    }

    .logo {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: var(--primary-gradient);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        color: white;
        font-size: 28px;
        box-shadow: var(--shadow);
    }

    h1 {
        font-family: 'Montserrat', sans-serif;
        font-size: 36px;
        font-weight: 900;
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .header p {
        color: var(--dark);
        font-size: 18px;
        opacity: 0.8;
    }

    .form-content {
        background: var(--card-bg);
        border-radius: 20px;
        padding: 40px;
        box-shadow: var(--shadow);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid var(--glass-border);
        transition: var(--transition);
    }

    .form-content:hover {
        box-shadow: 0 15px 35px rgba(31, 38, 135, 0.2);
    }

    .error-message {
        background: rgba(255, 107, 107, 0.2);
        color: var(--secondary);
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        border: 1px solid rgba(255, 107, 107, 0.3);
    }

    .error-message i {
        margin-right: 10px;
        font-size: 20px;
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

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 15px 20px;
        border: 2px solid #e6e6e6;
        border-radius: 12px;
        font-size: 16px;
        font-family: 'Poppins', sans-serif;
        transition: var(--transition);
        background: var(--light);
    }

    .form-group input:focus,
    .form-group select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.2);
    }

    button {
        background: var(--primary-gradient);
        color: white;
        border: none;
        border-radius: 50px;
        padding: 16px 35px;
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

    button::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        transition: 0.5s;
    }

    button:hover::before {
        left: 100%;
    }

    button:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(108, 99, 255, 0.4);
    }

    button:active {
        transform: translateY(0);
    }

    button i {
        margin-right: 10px;
    }

    .register-link {
        text-align: center;
        margin-top: 25px;
        color: #666;
    }

    .register-link a {
        color: var(--primary);
        text-decoration: none;
        font-weight: 500;
        transition: var(--transition);
    }

    .register-link a:hover {
        text-decoration: underline;
    }

    @media (max-width: 768px) {
        .form-content {
            padding: 30px 20px;
        }
        
        h1 {
            font-size: 28px;
        }
        
        .logo {
            width: 50px;
            height: 50px;
            font-size: 24px;
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

    <div class="login-container">
        <!-- Header Section -->
        <div class="header">
            <div class="logo-container">
                <div class="logo">
                    <a href="index.php" style="color: inherit; text-decoration: none;">
                        <i class="fas fa-hands-helping"></i>
                    </a>
                </div>
                <h1>Insieme</h1>
            </div>
            <p>- Supporting those who need support -</p>
        </div>

        <!-- Form Section -->
        <div class="form-content">
            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="post">
                <div class="form-group">
                    <label for="username"><i class="fas fa-user"></i> Username</label>
                    <input type="text" id="username" name="username"
                         value="<?php echo htmlspecialchars($username); ?>" required>
                </div>

                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="role"><i class="fas fa-user-tag"></i> Login as</label>
                    <select id="role" name="role" required>
                        <option value="user" <?php echo $role === 'user' ? 'selected' : ''; ?>>User</option>
                        <option value="recruiter" <?php echo $role === 'recruiter' ? 'selected' : ''; ?>>Recruiter</option>
                    </select>
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