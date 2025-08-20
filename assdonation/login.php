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
            } else {
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
  <style>
    /* Styles omitted for brevity; reuse your styles from your original file */
    /* Include your global styles, form styles, etc. here */
    /* You can paste your previous CSS here */
  </style>
</head>
<body>
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
      <p style="opacity: 0.9;">- Supporting those who need support -</p>
    </div>

    <!-- Form Section -->
    <div class="form-content">
      <?php if ($error): ?>
        <div class="error-message" style="color: red; margin-bottom: 1rem;">
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

        <div class="form-group">
          <label for="role">Login as</label>
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
