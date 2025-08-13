<?php
include 'db.php';
session_start();

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

$result = $conn->query("SELECT * FROM creative_posts ORDER BY created_at DESC");
?>

<h1>Creative Corner</h1>
<a href="creative_upload.php">+ Upload Your Artwork</a>
<hr>

<style>
.gallery {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}
.card {
    width: 250px;
    border: 1px solid #ccc;
    padding: 10px;
    border-radius: 8px;
    text-align: center;
    background: #f9f9f9;
}
.card img {
    width: 100%;
    height: auto;
    border-radius: 5px;
}
.like-btn {
    background: #ff4b5c;
    color: white;
    padding: 5px 10px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}
.delete-btn {
    background: #555;
    color: white;
    padding: 5px 10px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    margin-top: 5px;
}
</style>

<div class="gallery">
<?php while ($row = $result->fetch_assoc()) { ?>
    <div class="card">
        <h3><?= htmlspecialchars($row['title']) ?></h3>
        <img src="<?= $row['image_path'] ?>" alt="Artwork">
        <p>Likes: <?= $row['likes'] ?></p>

        <?php if ($user_id) { ?>
            <form action="creative_like.php" method="POST" style="display:inline;">
                <input type="hidden" name="post_id" value="<?= $row['id'] ?>">
                <button type="submit" class="like-btn">❤️ Like</button>
            </form>
        <?php } ?>

        <?php if ($row['user_id'] == $user_id) { ?>
            <form action="creative_delete.php" method="POST" style="display:inline;">
                <input type="hidden" name="post_id" value="<?= $row['id'] ?>">
                <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this post?');">🗑 Delete</button>
            </form>
        <?php } ?>
    </div>
<?php } ?>
</div>
