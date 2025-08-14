<?php
session_start();
include 'db.php'; // path to your DB connection

if(!isset($_SESSION['user_id'])) {
    echo "You must be logged in!";
    exit;
}

// Check if 'message' is set
if(isset($_POST['message']) && !empty(trim($_POST['message']))) {
    $user_id = $_SESSION['user_id'];
    $message = trim($_POST['message']);

    $stmt = $conn->prepare("INSERT INTO messages (user_id, message) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $message);
    if($stmt->execute()) {
        echo "Message sent!";
    } else {
        echo "Failed to send message.";
    }
} else {
    echo "No message sent!";
}
?>
