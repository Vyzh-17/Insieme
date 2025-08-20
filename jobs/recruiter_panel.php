<?php
session_start();
include '../assets/db.php'; 

// --- ✅ SECURITY: Make sure only recruiters can access this ---

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'recruiter') {
    die("Access denied. Only recruiters can view this page.");
}
$recruiter_id = $_SESSION['user_id'];

// --- ✅ Close Job Handler ---
if (isset($_GET['close_job'])) {
    $close_id = intval($_GET['close_job']);
    mysqli_query($conn, "UPDATE job_posts SET status='closed' WHERE id=$close_id AND posted_by=$recruiter_id");
    echo "<script>alert('Job has been closed successfully.'); window.location='recruiter_panel.php';</script>";
    exit;
}

// --- ✅ Fetch Recruiter's Job Posts ---
$sql = "SELECT * FROM job_posts WHERE posted_by = $recruiter_id AND status != 'closed' ORDER BY posted_at DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Recruiter Job Panel - Insieme</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #f9f9f9; }
        .job-card { background: #fff; padding: 15px; margin-bottom: 20px; border-left: 4px solid #007BFF; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .job-card h3 { margin-top: 0; }
        .job-card form { margin-top: 10px; }
        button { background-color: #dc3545; color: #fff; border: none; padding: 8px 12px; cursor: pointer; border-radius: 4px; }
        button:hover { background-color: #c82333; }
        summary { cursor: pointer; font-weight: bold; margin-top: 10px; }
        a { text-decoration: none; color: #007BFF; }
    </style>
</head>
<body>
<nav style="background-color: #007BFF; padding: 10px; margin-bottom: 20px; border-radius: 5px;">

    <a href="post_job.php" style="color: white; margin-right: 20px; font-weight: bold;">➕ Post Job</a>
    <a href="recruiter_panel.php" style="color: white; margin-right: 20px; font-weight: bold;">📋 My Jobs</a>
    <a href="../logout.php" style="color: white; font-weight: bold;">🚪 Logout</a>
</nav>
<h2>👨‍💼 Recruiter Panel - Manage Your Job Posts</h2>

<?php
if (mysqli_num_rows($result) === 0) {
    echo "<p>You have not posted any jobs yet.</p>";
}

while ($row = mysqli_fetch_assoc($result)):
    $job_id = $row['id'];
    $status = ucfirst($row['status']);
?>

<div class="job-card">
    <h3><?= htmlspecialchars($row['title']) ?> - <?= htmlspecialchars($row['company_name']) ?></h3>
    <p><strong>Status:</strong> <?= $status ?></p>
    <p><strong>Location:</strong> <?= htmlspecialchars($row['location']) ?></p>
    <p><strong>Salary:</strong> <?= htmlspecialchars($row['salary']) ?></p>
    <p><?= nl2br(htmlspecialchars($row['description'])) ?></p>

    <!-- ✅ Close Button -->
    <?php if ($row['status'] === 'approved'): ?>
        <form method="get" onsubmit="return confirm('Are you sure you want to close this job post?');">
            <input type="hidden" name="close_job" value="<?= $job_id ?>">
            <button type="submit">❌ Close Job</button>
        </form>
    <?php endif; ?>

    <!-- ✅ View Applications -->
    <?php if ($row['status'] === 'approved' || $row['status'] === 'closed'): ?>
        <details>
            <summary>📄 View Applications</summary>
            <div style="margin-left: 20px;">
                <?php
                $apps = mysqli_query($conn, "SELECT * FROM job_applications WHERE job_id = $job_id");
                if (mysqli_num_rows($apps) > 0) {
                    while ($app = mysqli_fetch_assoc($apps)) {
                       $resume = htmlspecialchars($app['resume_path']);
$resume_path = "jobs/" . $resume; // Add 'jobs/' prefix
echo "<p>User ID: {$app['user_id']} - <a href='../$resume_path' target='_blank'>View Resume</a></p>";

                    }
                } else {
                    echo "<p>No applications yet.</p>";
                }
                ?>
            </div>
        </details>
    <?php endif; ?>
</div>

<?php endwhile; ?>

</body>
</html>
