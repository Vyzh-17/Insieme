<?php
session_start();
include '../assets/db.php';

if (isset($_GET['job_id'])) {
    $job_id = $_GET['job_id'];
    $user_id = $_SESSION['user_id'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['resume'])) {
        $resume = $_FILES['resume']['name'];
        $temp = $_FILES['resume']['tmp_name'];
        $path = "uploads/resumes/" . time() . "_" . basename($resume);

        if (move_uploaded_file($temp, $path)) {
            $sql = "INSERT INTO job_applications (job_id, user_id, resume_path)
                    VALUES ($job_id, $user_id, '$path')";
            mysqli_query($conn, $sql);
            echo "Resume submitted successfully!";
        } else {
            echo "Upload failed!";
        }
    }
    ?>
    <form method="post" enctype="multipart/form-data">
        <label>Upload Your Resume (PDF):</label>
        <input type="file" name="resume" accept=".pdf" required>
        <button type="submit">Apply</button>
    </form>
    <?php
} else {
    echo "Invalid job.";
}
?>
