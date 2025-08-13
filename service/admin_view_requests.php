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

$result = $conn->query("SELECT * FROM service_requests ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - View Service Requests</title>
    <style>
        table { border-collapse: collapse; width: 80%; margin: auto; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background: #007bff; color: white; }
        a.button { padding: 6px 12px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; }
        a.button:hover { background: #218838; }
    </style>
</head>
<body>
    <h2 style="text-align:center;">Service Requests</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Service</th>
            <th>City</th>
            <th>Phone</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php while($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['service_name'] ?></td>
            <td><?= $row['city'] ?></td>
            <td><?= $row['phone'] ?></td>
            <td><?= $row['status'] ?></td>
            <td>
                <?php if ($row['status'] == 'Pending') { ?>
                    <a class="button" href="?accept=<?= $row['id'] ?>">Accept</a>
                <?php } else { echo "✅ Accepted"; } ?>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
<?php $conn->close(); ?>
