<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Please log in to like posts.");
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['post_id'])) {
    $post_id = $_POST['post_id'];

    // Prevent duplicate likes
    $check = $conn->prepare("SELECT * FROM creative_likes WHERE post_id=? AND user_id=?");
    $check->bind_param("ii", $post_id, $user_id);
    $check->execute();

    if ($check->get_result()->num_rows == 0) {
        $conn->query("INSERT INTO creative_likes (post_id, user_id) VALUES ($post_id, $user_id)");
        $conn->query("UPDATE creative_posts SET likes = likes + 1 WHERE id=$post_id");
    }
}
header("Location: creative_index.php");
