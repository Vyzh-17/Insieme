<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$userStmt = $conn->prepare("SELECT username, email, created_at FROM users WHERE id = ?");
$userStmt->bind_param("i", $user_id);
$userStmt->execute();
$userResult = $userStmt->get_result();
$user = $userResult->fetch_assoc();

// Fetch requested devices with request status
$requestSql = "
    SELECT d.title, r.status, r.requested_at, r.approved_at
    FROM requests r
    JOIN devices d ON r.device_id = d.id
    WHERE r.user_id = ?
    ORDER BY r.requested_at DESC
";
$requestStmt = $conn->prepare($requestSql);
$requestStmt->bind_param("i", $user_id);
$requestStmt->execute();
$requestedDevices = $requestStmt->get_result();

// Fetch donated devices
$donatedSql = "SELECT title, posted_at FROM devices WHERE user_id = ? ORDER BY posted_at DESC";
$donatedStmt = $conn->prepare($donatedSql);
$donatedStmt->bind_param("i", $user_id);
$donatedStmt->execute();
$donatedDevices = $donatedStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile | DeviceShare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background-color: #ffffff;
            color: #333;
            min-height: 100vh;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            overflow-x: hidden;
        }
        
        /* Decorative elements */
        .deco-circle {
            position: absolute;
            border-radius: 50%;
            z-index: -1;
            opacity: 0.05;
        }
        
        .circle-1 {
            width: 400px;
            height: 400px;
            background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
            top: -200px;
            right: -200px;
        }
        
        .circle-2 {
            width: 300px;
            height: 300px;
            background: linear-gradient(135deg, #f72585 0%, #b5179e 100%);
            bottom: -150px;
            left: -150px;
        }
        
        .circle-3 {
            width: 200px;
            height: 200px;
            background: linear-gradient(135deg, #4cc9f0 0%, #4895ef 100%);
            top: 40%;
            left: 10%;
        }
        
        .deco-square {
            position: absolute;
            width: 120px;
            height: 120px;
            transform: rotate(45deg);
            z-index: -1;
            opacity: 0.05;
        }
        
        .square-1 {
            background: linear-gradient(135deg, #7209b7 0%, #560bad 100%);
            top: 20%;
            right: 10%;
        }
        
        .square-2 {
            background: linear-gradient(135deg, #f15bb5 0%, #fee440 100%);
            bottom: 15%;
            right: 15%;
        }
        
        .container {
            max-width: 1200px;
            width: 100%;
            margin: 20px auto;
            z-index: 10;
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
            padding: 20px;
            position: relative;
        }
        
        .header h1 {
            font-size: 2.8rem;
            margin-bottom: 10px;
            color: #2b2d42;
            position: relative;
            display: inline-block;
        }
        
        .header h1:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, #4361ee, #4cc9f0);
            border-radius: 2px;
        }
        
        .header p {
            font-size: 1.2rem;
            color: #6c757d;
            max-width: 600px;
            margin: 20px auto 0;
            line-height: 1.6;
        }
        
        .profile-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            padding: 40px;
            margin-bottom: 40px;
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            position: relative;
            overflow: hidden;
            border: 1px solid #f0f0f0;
        }
        
        .profile-card:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 8px;
            height: 100%;
            background: linear-gradient(to bottom, #4361ee, #4cc9f0);
        }
        
        .user-info {
            flex: 1;
            min-width: 300px;
        }
        
        .avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4361ee 0%, #4cc9f0 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
            margin: 0 auto 25px;
            box-shadow: 0 8px 20px rgba(67, 97, 238, 0.3);
        }
        
        .user-info h2 {
            font-size: 1.8rem;
            margin-bottom: 15px;
            color: #2b2d42;
            text-align: center;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .info-label {
            font-weight: 600;
            width: 150px;
            color: #4361ee;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .info-value {
            flex: 1;
            color: #495057;
        }
        
        .stats {
            display: flex;
            justify-content: space-around;
            margin-top: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .stat-box {
            background: #f8f9fa;
            color: #2b2d42;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            min-width: 150px;
            flex: 1;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #e9ecef;
        }
        
        .stat-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }
        
        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 5px;
            background: linear-gradient(90deg, #4361ee, #4cc9f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .stat-label {
            font-size: 1rem;
            color: #6c757d;
        }
        
        .section {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            padding: 40px 30px;
            margin-bottom: 40px;
            border: 1px solid #f0f0f0;
            position: relative;
            overflow: hidden;
        }
        
        .section:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(to bottom, #7209b7, #f15bb5);
        }
        
        .section-header {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .section-header i {
            background: linear-gradient(135deg, #7209b7 0%, #f15bb5 100%);
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-right: 15px;
            box-shadow: 0 4px 10px rgba(114, 9, 183, 0.3);
        }
        
        .section-header h2 {
            font-size: 1.8rem;
            color: #2b2d42;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
        }
        
        th {
            background-color: #f8f9fa;
            color: #495057;
            font-weight: 600;
            border-bottom: 2px solid #e9ecef;
        }
        
        tr:hover {
            background-color: #fbfdff;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            display: inline-block;
            font-weight: 500;
        }
        
        .status-approved {
            background-color: #d4edda;
            color: #155724;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            display: inline-block;
            font-weight: 500;
        }
        
        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            display: inline-block;
            font-weight: 500;
        }
        
        .empty-message {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
            font-size: 1.2rem;
            background: #f8f9fa;
            border-radius: 15px;
            margin-top: 20px;
        }
        
        .empty-message i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #dee2e6;
            display: block;
        }
        
        .navigation {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        
        .nav-btn {
            background: #ffffff;
            color: #4361ee;
            border: 2px solid #4361ee;
            padding: 12px 30px;
            border-radius: 30px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.15);
        }
        
        .nav-btn:hover {
            background: linear-gradient(135deg, #4361ee 0%, #4cc9f0 100%);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(67, 97, 238, 0.25);
            border-color: transparent;
        }
        
        .footer {
            color: #6c757d;
            text-align: center;
            padding: 20px;
            margin-top: 40px;
            font-size: 0.9rem;
            border-top: 1px solid #f0f0f0;
            width: 100%;
            max-width: 1200px;
        }
        
        @media (max-width: 768px) {
            .profile-card {
                flex-direction: column;
                padding: 30px 20px;
            }
            
            .section {
                padding: 30px 20px;
            }
            
            .section-header h2 {
                font-size: 1.5rem;
            }
            
            th, td {
                padding: 10px;
                font-size: 0.9rem;
            }
            
            .header h1 {
                font-size: 2.2rem;
            }
            
            .stat-box {
                min-width: 120px;
            }
        }
        
        @media (max-width: 480px) {
            .header h1 {
                font-size: 1.8rem;
            }
            
            .avatar {
                width: 90px;
                height: 90px;
                font-size: 2rem;
            }
            
            .stats {
                flex-direction: column;
            }
            
            .info-row {
                flex-direction: column;
                gap: 5px;
            }
            
            .info-label {
                width: 100%;
            }
            
            .navigation {
                flex-direction: column;
                gap: 12px;
            }
            
            .nav-btn {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Decorative background elements -->
    <div class="deco-circle circle-1"></div>
    <div class="deco-circle circle-2"></div>
    <div class="deco-circle circle-3"></div>
    <div class="deco-square square-1"></div>
    <div class="deco-square square-2"></div>
   
    <div class="container">
        <div class="header">
            <h1>Your DeviceShare Profile</h1>
            <p>Welcome to your personal dashboard. Here you can manage your device requests, donations, and account settings.</p>
        </div>
        <div class="navigation">
            <button class="nav-btn" onclick="window.location.href='index.php'">
                <i class="fas fa-home"></i> Back to Home
            </button>
            
            <button class="nav-btn" onclick="window.location.href='logout.php'">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </div><br>
        <div class="profile-card">
            <div class="user-info">
                <div class="avatar">
                    <i class="fas fa-user"></i>
                </div>
                <h2><?= htmlspecialchars($user['username']) ?></h2>
                
                <div class="info-row">
                    <div class="info-label">
                        <i class="fas fa-envelope"></i> Email:
                    </div>
                    <div class="info-value"><?= htmlspecialchars($user['email']) ?></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">
                        <i class="fas fa-calendar-alt"></i> Member Since:
                    </div>
                    <div class="info-value"><?= date('F Y', strtotime($user['created_at'])) ?></div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">
                        <i class="fas fa-map-marker-alt"></i> Location:
                    </div>
                    <div class="info-value">San Francisco, CA</div>
                </div>
            </div>
            
            <div class="stats">
                <div class="stat-box">
                    <div class="stat-value"><?= $requestedDevices->num_rows ?></div>
                    <div class="stat-label">Device Requests</div>
                </div>
                
                <div class="stat-box">
                    <div class="stat-value"><?= $donatedDevices->num_rows ?></div>
                    <div class="stat-label">Devices Donated</div>
                </div>
                
                <div class="stat-box">
                    <div class="stat-value">87</div>
                    <div class="stat-label">Community Points</div>
                </div>
            </div>
        </div>
        
        <div class="section">
            <div class="section-header">
                <i class="fas fa-laptop"></i>
                <h2>Your Requested Devices</h2>
            </div>
            
            <?php if ($requestedDevices->num_rows === 0): ?>
                <div class="empty-message">
                    <i class="fas fa-inbox"></i>
                    <p>You haven't requested any devices yet</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Device</th>
                            <th>Status</th>
                            <th>Requested</th>
                            <th>Approved</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($row = $requestedDevices->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['title']) ?></td>
                            <td>
                                <?php 
                                    $statusClass = 'status-' . $row['status'];
                                    echo '<span class="' . $statusClass . '">' . ucfirst($row['status']) . '</span>';
                                ?>
                            </td>
                            <td><?= date('M d, Y', strtotime($row['requested_at'])) ?></td>
                            <td><?= $row['approved_at'] ? date('M d, Y', strtotime($row['approved_at'])) : '-' ?></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <div class="section">
            <div class="section-header">
                <i class="fas fa-gift"></i>
                <h2>Your Donated Devices</h2>
            </div>
            
            <?php if ($donatedDevices->num_rows === 0): ?>
                <div class="empty-message">
                    <i class="fas fa-box-open"></i>
                    <p>You haven't donated any devices yet</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Device</th>
                            <th>Posted Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($row = $donatedDevices->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['title']) ?></td>
                            <td><?= date('M d, Y', strtotime($row['posted_at'])) ?></td>
                            <td><span class="status-approved">Available</span></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        
        
        <div class="footer">
            <p>DeviceShare Community &copy; 2023 | Sharing technology, building connections</p>
        </div>
    </div>
</body>
</html>