<?php
$mysqli = new mysqli("localhost", "root", "", "insieme");

if ($mysqli->connect_error) {
  die("Connection failed: " . $mysqli->connect_error);
}

$result = $mysqli->query("SELECT SUM(amount) AS total FROM donation");
$row = $result->fetch_assoc();
$total = $row['total'] ?? 0;

echo $total;

$mysqli->close();
?>
