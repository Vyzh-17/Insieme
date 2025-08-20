<?php
include '../assets/db.php';

if (isset($_GET['approve'])) {
    $id = $_GET['approve'];
    mysqli_query($conn, "UPDATE job_posts SET status='approved' WHERE id=$id");
}
if (isset($_GET['reject'])) {
    $id = $_GET['reject'];
    mysqli_query($conn, "UPDATE job_posts SET status='rejected' WHERE id=$id");
}

$result = mysqli_query($conn, "SELECT * FROM job_posts WHERE status='pending'");
while ($row = mysqli_fetch_assoc($result)) {
    echo "<h3>{$row['title']} at {$row['company_name']}</h3>";
    echo "<p>{$row['description']}</p>";
    echo "<a href='?approve={$row['id']}'>Approve</a> | ";
    echo "<a href='?reject={$row['id']}'>Reject</a><hr>";
}
?>
