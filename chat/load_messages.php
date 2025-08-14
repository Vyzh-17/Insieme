<?php
session_start();
include 'db.php'; // path to your DB connection

$query = "SELECT messages.message, messages.timestamp, users.username 
          FROM messages 
          JOIN users ON messages.user_id = users.id
          ORDER BY messages.timestamp ASC";

$result = $conn->query($query);

while($row = $result->fetch_assoc()) {
    echo "<p><strong>".htmlspecialchars($row['username']).":</strong> ".htmlspecialchars($row['message'])." <small>".$row['timestamp']."</small></p>";
}
?>
