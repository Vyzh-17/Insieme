<?php
// admin_view_requests.php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "insieme";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// Accept request
if (isset($_GET['accept'])) {
    $id = intval($_GET['accept']);
    $conn->query("UPDATE service_requests SET status='Accepted' WHERE id=$id");
    header("Location: admin_view_requests.php");
    exit();
}

// Reject request (and delete from database)
if (isset($_GET['reject'])) {
    $id = intval($_GET['reject']);
    $conn->query("DELETE FROM service_requests WHERE id=$id");
    header("Location: admin_view_requests.php");
    exit();
}

$result = $conn->query("SELECT * FROM service_requests ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - View Service Requests</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin: auto;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px 15px;
            text-align: center;
        }
        th {
            background: #3498db;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .button {
            padding: 6px 12px;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            margin: 0 3px;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .accept-btn {
            background: #28a745;
        }
        .accept-btn:hover {
            background: #218838;
            transform: translateY(-1px);
        }
        .reject-btn {
            background: #dc3545;
        }
        .reject-btn:hover {
            background: #c82333;
            transform: translateY(-1px);
        }
        .status {
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 3px;
        }
        .status-pending {
            color: #ffc107;
            background-color: #fff3cd;
        }
        .status-accepted {
            color: #28a745;
            background-color: #d4edda;
        }
        .no-requests {
            text-align: center;
            padding: 20px;
            color: #6c757d;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Admin - Service Requests</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Service</th>
                <th>City</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php 
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) { 
            ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['service_name'] ?></td>
                <td><?= $row['city'] ?></td>
                <td><?= $row['phone'] ?></td>
                <td>
                    <span class="status status-<?= strtolower($row['status']) ?>">
                        <?= $row['status'] ?>
                    </span>
                </td>
                <td>
                    <?php if ($row['status'] == 'Pending') { ?>
                        <a class="button accept-btn" href="?accept=<?= $row['id'] ?>">Accept</a>
                        <a class="button reject-btn" href="?reject=<?= $row['id'] ?>">Reject</a>
                    <?php } else { 
                        echo "<span class='status status-".strtolower($row['status'])."'>".$row['status']."</span>"; 
                    } ?>
                </td>
            </tr>
            <?php 
                }
            } else { 
            ?>
            <tr>
                <td colspan="6" class="no-requests">No service requests found</td>
            </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
<?php $conn->close(); ?>