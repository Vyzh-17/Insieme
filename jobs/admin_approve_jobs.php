<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Post Approval Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        header {
            background: linear-gradient(135deg, #4b6cb7 0%, #182848 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }
        
        .subtitle {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .dashboard-stats {
            display: flex;
            justify-content: space-around;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            text-align: center;
            flex: 1;
            min-width: 200px;
        }
        
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #4b6cb7;
            margin: 10px 0;
        }
        
        .job-list {
            background: white;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        .job-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
            align-items: center;
        }
        
        .section-title {
            font-size: 24px;
            color: #2d3a80;
        }
        
        .job-card {
            border: 1px solid #e1e5eb;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .job-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }
        
        .job-title {
            font-size: 20px;
            font-weight: 600;
            color: #2d3a80;
        }
        
        .company {
            font-size: 16px;
            color: #555;
            margin: 5px 0 15px;
        }
        
        .job-description {
            color: #555;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        
        .job-actions {
            display: flex;
            gap: 15px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }
        
        .btn-approve {
            background-color: #2ed573;
            color: white;
        }
        
        .btn-approve:hover {
            background-color: #23c263;
        }
        
        .btn-reject {
            background-color: #ff6b81;
            color: white;
        }
        
        .btn-reject:hover {
            background-color: #ff4757;
        }
        
        .btn i {
            margin-right: 8px;
        }
        
        .no-pending {
            text-align: center;
            padding: 40px;
            color: #888;
        }
        
        .no-pending i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #a4b0be;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            margin-left: 10px;
        }
        
        .status-pending {
            background-color: #ffeaa7;
            color: #d35400;
        }
        
        @media (max-width: 768px) {
            .dashboard-stats {
                flex-direction: column;
            }
            
            .job-actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Job Post Approval Dashboard</h1>
            <p class="subtitle">Review and manage pending job postings</p>
        </header>
        
        <div class="dashboard-stats">
            <div class="stat-card">
                <i class="fas fa-clipboard-list fa-2x"></i>
                <div class="stat-number">12</div>
                <div class="stat-label">Pending Reviews</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-check-circle fa-2x"></i>
                <div class="stat-number">42</div>
                <div class="stat-label">Approved Posts</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-times-circle fa-2x"></i>
                <div class="stat-number">8</div>
                <div class="stat-label">Rejected Posts</div>
            </div>
        </div>
        
        <div class="job-list">
            <div class="job-header">
                <h2 class="section-title">Pending Job Posts</h2>
            </div>
            
            <?php
            include '../assets/db.php';

            if (isset($_GET['approve'])) {
                $id = $_GET['approve'];
                mysqli_query($conn, "UPDATE job_posts SET status='approved' WHERE id=$id");
                echo "<script>alert('Job post approved successfully!');</script>";
            }
            if (isset($_GET['reject'])) {
                $id = $_GET['reject'];
                mysqli_query($conn, "UPDATE job_posts SET status='rejected' WHERE id=$id");
                echo "<script>alert('Job post rejected.');</script>";
            }

            $result = mysqli_query($conn, "SELECT * FROM job_posts WHERE status='pending'");
            $count = mysqli_num_rows($result);
            
            if ($count > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<div class='job-card'>";
                    echo "<h3 class='job-title'>{$row['title']} <span class='status-badge status-pending'>Pending</span></h3>";
                    echo "<p class='company'><i class='fas fa-building'></i> {$row['company_name']}</p>";
                    echo "<p class='job-description'>{$row['description']}</p>";
                    echo "<div class='job-actions'>";
                    $filePath = '../uploads/verifications/' . basename($row['verification_file']);
echo "<p> 
      <a href='{$filePath}' target='_blank'><i class='fas fa-file'></i> View File</a></p>";

                    echo "<a href='?approve={$row['id']}' class='btn btn-approve'><i class='fas fa-check'></i> Approve</a>";
                    echo "<a href='?reject={$row['id']}' class='btn btn-reject'><i class='fas fa-times'></i> Reject</a>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<div class='no-pending'>";
                echo "<i class='fas fa-check-circle'></i>";
                echo "<h3>No Pending Job Posts</h3>";
                echo "<p>All job postings have been reviewed. Check back later for new submissions.</p>";
                echo "</div>";
            }
            ?>
        </div>
    </div>

    <script>
        // Simple confirmation for actions
        document.addEventListener('DOMContentLoaded', function() {
            const approveButtons = document.querySelectorAll('.btn-approve');
            const rejectButtons = document.querySelectorAll('.btn-reject');
            
            approveButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (!confirm('Are you sure you want to approve this job post?')) {
                        e.preventDefault();
                    }
                });
            });
            
            rejectButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (!confirm('Are you sure you want to reject this job post?')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
</body>
</html>