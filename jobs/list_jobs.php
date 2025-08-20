<?php
include '../assets/db.php';

$result = mysqli_query($conn, "SELECT * FROM job_posts WHERE status='approved' ORDER BY posted_at DESC");
while ($job = mysqli_fetch_assoc($result)) {
    echo "<h3>{$job['title']} - {$job['company_name']}</h3>";
    echo "<p>{$job['description']}</p>";
    echo "<p>Location: {$job['location']}</p>";
    echo "<p>Salary: {$job['salary']}</p>";
    echo "<a href='apply.php?job_id={$job['id']}'>Apply Now</a><hr>";
}
?>
