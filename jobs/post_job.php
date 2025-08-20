<?php
session_start();
include '../assets/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $company = mysqli_real_escape_string($conn, $_POST['company_name']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $salary = mysqli_real_escape_string($conn, $_POST['salary']);
    $posted_by = $_SESSION['user_id'];

    // --- ✅ Handle Verification File Upload ---
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
            echo "❌ Failed to upload verification file.";
            exit;
        }
    }

    // --- ✅ Insert Job Post with File Path ---
    $sql = "INSERT INTO job_posts (title, description, company_name, location, salary, posted_by, verification_file)
            VALUES ('$title', '$desc', '$company', '$location', '$salary', $posted_by, '$file_path')";
    mysqli_query($conn, $sql);

    echo "✅ Job post submitted for approval!";
}
?>

<!-- ✅ HTML FORM -->
<form method="post" enctype="multipart/form-data">
    <input name="title" placeholder="Job Title" required><br>
    <textarea name="description" placeholder="Job Description" required></textarea><br>
    <input name="company_name" placeholder="Company Name" required><br>
    <input name="location" placeholder="Location" required><br>
    <input name="salary" placeholder="Salary" required><br>
    <label>📄 Upload Verification File (PDF, DOC, etc.):</label><br>
    <input type="file" name="verification_file" accept=".pdf,.doc,.docx,.jpg,.png" required><br><br>
    <button type="submit">Submit Job</button>
</form>
