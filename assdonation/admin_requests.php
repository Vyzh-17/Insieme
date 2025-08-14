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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Pending Requests</title>
    <style>
        :root {
            --primary-color: #4a6fa5;
            --secondary-color: #166088;
            --accent-color: #4fc3f7;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --border-radius: 4px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        
        h1 {
            color: var(--secondary-color);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--accent-color);
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        .no-requests {
            padding: 20px;
            background-color: var(--light-color);
            border-radius: var(--border-radius);
            text-align: center;
            color: var(--dark-color);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        tr:hover {
            background-color: #f1f1f1;
        }
        
        .action-link {
            color: white;
            text-decoration: none;
            padding: 6px 12px;
            border-radius: var(--border-radius);
            font-size: 14px;
            font-weight: 500;
            margin: 0 5px;
            display: inline-block;
        }
        
        .approve-link {
            background-color: var(--success-color);
        }
        
        .approve-link:hover {
            background-color: #218838;
        }
        
        .reject-link {
            background-color: var(--danger-color);
        }
        
        .reject-link:hover {
            background-color: #c82333;
        }
        
        .id-proof-link {
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .id-proof-link:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            table {
                display: block;
                overflow-x: auto;
            }
            
            th, td {
                padding: 8px 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Pending Device Requests</h1>
        <a href="index.php" class="back-link">← Back to Home</a>

        <?php if ($result->num_rows === 0): ?>
            <div class="no-requests">
                <p>No pending requests at this time.</p>
            </div>
        <?php else: ?>
            <table>
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
                        <td><?= date('M j, Y g:i A', strtotime($row['requested_at'])) ?></td>
                        <td>
                            <?php if ($row['id_proof']): ?>
                                <a href="uploads/<?= htmlspecialchars($row['id_proof']) ?>" target="_blank" class="id-proof-link">View ID</a>
                            <?php else: ?>
                                <span>No ID uploaded</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="?action=approve&request_id=<?= $row['request_id'] ?>" onclick="return confirm('Are you sure you want to approve this request?')" class="action-link approve-link">Approve</a>
                            <a href="?action=reject&request_id=<?= $row['request_id'] ?>" onclick="return confirm('Are you sure you want to reject this request?')" class="action-link reject-link">Reject</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>