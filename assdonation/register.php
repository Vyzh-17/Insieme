<?php
include 'db.php';
session_start();

$error = '';
$username = '';
$email = '';
$selectedRole = 'user'; // default

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];
    $selectedRole = $role;

    if (!$username || !$email || !$password || !$confirm_password || !$role) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if username or email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username=? OR email=?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username or email already taken.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $hash, $role);
            if ($stmt->execute()) {
                header("Location: login.php");
                exit;
            } else {
                $error = "Database error: " . $conn->error;
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
    <title>Register | Insieme</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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

        .header {
            background: linear-gradient(to right, #0069d9, #0052cc);
            padding: 20px 40px;
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

        .register-container {
            background: white;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 15px 40px rgba(0, 105, 180, 0.15);
            width: 100%;
            max-width: 550px;
            max-height:750px;
            overflow: hidden;
        }

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

        input, select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #b3d7ff;
            border-radius: 8px;
            font-size: 1rem;
            background: #f8fbff;
        }

        input:focus, select:focus {
            border-color: #0069d9;
            background: white;
            box-shadow: 0 0 0 3px rgba(0, 105, 180, 0.2);
            outline: none;
        }

        .error-message {
            color: #dc3545;
            margin: 1rem 0;
            font-weight: 500;
            text-align: center;
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

        .login-link {
            margin-top: 1.5rem;
            color: #666;
            text-align: center;
        }

        .login-link a {
            color: #0069d9;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="header">
            <div class="logo-container">
                <div class="logo">
                    <a href="index.php" style="color: inherit; text-decoration: none;">
                        <i class="fas fa-hands-helping"></i>
                    </a>
                </div>
                <h1>Insieme</h1>
            </div>
            <p style="opacity: 0.9;">- Join our community -</p>
        </div>

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
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email"
                        value="<?php echo htmlspecialchars($email); ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <div class="form-group">
                    <label for="role">Register as</label>
                    <select id="role" name="role" required>
                        <option value="user" <?php echo ($selectedRole === 'user') ? 'selected' : ''; ?>>User</option>
                        <option value="recruiter" <?php echo ($selectedRole === 'recruiter') ? 'selected' : ''; ?>>Recruiter</option>
                    </select>
                </div>

                <button type="submit">
                    <i class="fas fa-user-plus"></i> Register
                </button>
            </form>

            <p class="login-link">
                Already registered? <a href="login.php">Login here</a>
            </p>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>
