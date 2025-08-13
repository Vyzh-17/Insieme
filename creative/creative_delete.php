<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Please log in to delete posts.");
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['post_id'])) {
    $post_id = $_POST['post_id'];

    // Ensure ownership
    $stmt = $conn->prepare("SELECT image_path FROM creative_posts WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $post_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Remove image file
        if (file_exists($row['image_path'])) {
            unlink($row['image_path']);
        }

        // Remove DB entry
        $conn->query("DELETE FROM creative_posts WHERE id=$post_id");
        $conn->query("DELETE FROM creative_likes WHERE post_id=$post_id");
    }
}
header("Location: creative_index.php");
