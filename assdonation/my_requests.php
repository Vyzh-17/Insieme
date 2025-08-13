<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT r.id, r.status, r.requested_at, r.approved_at, d.title, u.email AS donor_email, u.username AS donor_name
        FROM requests r 
        JOIN devices d ON r.device_id = d.id 
        JOIN users u ON d.user_id = u.id
        WHERE r.user_id = ?
        ORDER BY r.requested_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head><title>My Requests</title></head>
<body>

<h1>My Device Requests</h1>
<p><a href="index.php">Back to Home</a> | <a href="logout.php">Logout</a></p>

<?php if ($result->num_rows === 0): ?>
    <p>You have not made any requests yet.</p>
<?php else: ?>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>Device</th>
                <th>Status</th>
                <th>Requested At</th>
                <th>Approved At</th>
                <th>Next Steps</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= ucfirst($row['status']) ?></td>
                <td><?= $row['requested_at'] ?></td>
                <td><?= $row['approved_at'] ?? '-' ?></td>
                <td>
                    <?php 
                    if ($row['status'] === 'approved'): 
                        echo "Your request is approved.<br>";
                        echo "Please contact donor <strong>" . htmlspecialchars($row['donor_name']) . "</strong>";
                        echo " at <a href='mailto:" . htmlspecialchars($row['donor_email']) . "'>" . htmlspecialchars($row['donor_email']) . "</a> to arrange pickup.";
                    elseif ($row['status'] === 'pending'):
                        echo "Waiting for admin approval.";
                    else:
                        echo "Request rejected.";
                    endif; 
                    ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>
