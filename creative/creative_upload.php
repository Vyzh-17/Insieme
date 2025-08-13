<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Please log in to upload your artwork.");
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['upload'])) {
    $title = $_POST['title'];
    $imageName = time() . "_" . basename($_FILES['image']['name']);
    $targetDir = "uploads/creative/";
    $target = $targetDir . $imageName;

    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        $stmt = $conn->prepare("INSERT INTO creative_posts (user_id, title, image_path) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $title, $target);
        $stmt->execute();
        echo "<p style='color:green;'>Uploaded successfully! <a href='creative_index.php'>Go to Gallery</a></p>";
    } else {
        echo "<p style='color:red;'>Upload failed!</p>";
    }
}
?>

<h2>Upload Your Artwork</h2>
<form method="POST" enctype="multipart/form-data">
    <input type="text" name="title" placeholder="Artwork Title" required><br><br>
    <input type="file" name="image" accept="image/*" required><br><br>
    <button type="submit" name="upload">Upload</button>
</form>
