<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    die("Access denied. Admins only.");
}

// Handle approve/reject actions
if (isset($_GET['action'], $_GET['request_id'])) {
    $action = $_GET['action'];
    $request_id = intval($_GET['request_id']);

    if ($action === 'approve') {
        $stmt = $conn->prepare("UPDATE requests SET status='approved', approved_at=NOW() WHERE id=?");
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
    } elseif ($action === 'reject') {
        $stmt = $conn->prepare("UPDATE requests SET status='rejected' WHERE id=?");
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
    }
    header("Location: admin_requests.php");
    exit;
}

// Fetch pending requests with user and device info, including id_proof
$sql = "SELECT r.id AS request_id, r.requested_at, r.id_proof, u.username AS recipient, d.title AS device_title 
        FROM requests r
        JOIN users u ON r.user_id = u.id
        JOIN devices d ON r.device_id = d.id
        WHERE r.status='pending'
        ORDER BY r.requested_at ASC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Pending Requests</title>
</head>
<body>
<h1>Pending Device Requests</h1>
<p><a href="index.php">Back to Home</a></p>

<?php if ($result->num_rows === 0): ?>
    <p>No pending requests.</p>
<?php else: ?>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>Device</th>
                <th>Requested By</th>
                <th>Requested At</th>
                <th>ID Proof</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['device_title']) ?></td>
                <td><?= htmlspecialchars($row['recipient']) ?></td>
                <td><?= $row['requested_at'] ?></td>
                <td>
                    <?php if ($row['id_proof']): ?>
                        <a href="uploads/<?= htmlspecialchars($row['id_proof']) ?>" target="_blank">View ID</a>
                    <?php else: ?>
                        No ID uploaded
                    <?php endif; ?>
                </td>
                <td>
                    <a href="?action=approve&request_id=<?= $row['request_id'] ?>" onclick="return confirm('Approve this request?')">Approve</a> |
                    <a href="?action=reject&request_id=<?= $row['request_id'] ?>" onclick="return confirm('Reject this request?')">Reject</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>
