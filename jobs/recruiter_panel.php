<?php
session_start();
include '../assets/db.php'; 

// --- ✅ SECURITY: Make sure only recruiters can access this ---

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'recruiter') {
    die("Access denied. Only recruiters can view this page.");
}
$recruiter_id = $_SESSION['user_id'];

// --- ✅ Close Job Handler ---
if (isset($_GET['close_job'])) {
    $close_id = intval($_GET['close_job']);
    mysqli_query($conn, "UPDATE job_posts SET status='closed' WHERE id=$close_id AND posted_by=$recruiter_id");
    echo "<script>alert('Job has been closed successfully.'); window.location='recruiter_panel.php';</script>";
    exit;
}

// --- ✅ Fetch Recruiter's Job Posts ---
$sql = "SELECT * FROM job_posts WHERE posted_by = $recruiter_id AND status != 'closed' ORDER BY posted_at DESC";

$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recruiter Panel - Insieme</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6C63FF;
            --primary-gradient: linear-gradient(135deg, #6C63FF 0%, #4A40E5 100%);
            --secondary: #FF6B6B;
            --accent: #36D1DC;
            --accent-gradient: linear-gradient(135deg, #36D1DC 0%, #5B86E5 100%);
            --dark: #2D2B55;
            --light: #F8F9FF;
            --success: #4CD964;
            --warning: #FFCC00;
            --card-bg: rgba(255, 255, 255, 0.85);
            --glass-bg: rgba(255, 255, 255, 0.25);
            --glass-border: rgba(255, 255, 255, 0.4);
            --shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #F5F7FA 0%, #E4E8F5 100%);
            color: var(--dark);
            line-height: 1.6;
            min-height: 100vh;
            padding: 0;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 10% 20%, rgba(108, 99, 255, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(255, 107, 107, 0.15) 0%, transparent 40%);
            z-index: -1;
        }

        .floating-shapes {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: -1;
            overflow: hidden;
        }

        .shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.1;
        }

        .shape-1 {
            width: 300px;
            height: 300px;
            background: var(--primary);
            top: -100px;
            left: -100px;
            animation: float 15s infinite ease-in-out;
        }

        .shape-2 {
            width: 200px;
            height: 200px;
            background: var(--secondary);
            bottom: -50px;
            right: -50px;
            animation: float 12s infinite ease-in-out 2s;
        }

        .shape-3 {
            width: 150px;
            height: 150px;
            background: var(--accent);
            top: 30%;
            right: 20%;
            animation: float 10s infinite ease-in-out 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--glass-border);
            padding: 15px 0;
            margin-bottom: 30px;
            box-shadow: var(--shadow);
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-family: 'Montserrat', sans-serif;
            font-size: 28px;
            font-weight: 900;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: flex;
            align-items: center;
        }

        .logo i {
            margin-right: 12px;
            font-size: 32px;
        }

        nav {
            display: flex;
            gap: 15px;
        }

        nav a {
            color: var(--dark);
            text-decoration: none;
            font-weight: 500;
            padding: 10px 20px;
            border-radius: 30px;
            transition: var(--transition);
            display: flex;
            align-items: center;
            background: var(--glass-bg);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            border: 1px solid var(--glass-border);
        }

        nav a i {
            margin-right: 8px;
        }

        nav a:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(108, 99, 255, 0.3);
        }

        .page-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 36px;
            font-weight: 800;
            margin-bottom: 30px;
            color: var(--dark);
            display: flex;
            align-items: center;
            position: relative;
        }

        .page-title i {
            margin-right: 15px;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .page-title::after {
            content: '';
            position: absolute;
           
            width: 80px;
            height: 5px;
            
            border-radius: 5px;
        }

        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 25px;
            box-shadow: var(--shadow);
            transition: var(--transition);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            border: 1px solid var(--glass-border);
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: var(--primary-gradient);
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(31, 38, 135, 0.2);
        }

        .stat-card {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .stat-card i {
            font-size: 40px;
            margin-bottom: 15px;
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: var(--primary-gradient);
            color: white;
        }

        .stat-card h3 {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 5px;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stat-card p {
            color: var(--dark);
            font-weight: 500;
            opacity: 0.8;
        }

        .job-list {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .job-card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 30px;
            box-shadow: var(--shadow);
            transition: var(--transition);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            border: 1px solid var(--glass-border);
            position: relative;
            overflow: hidden;
        }

        .job-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(31, 38, 135, 0.15);
        }

        .job-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 8px;
            height: 100%;
            background: var(--primary-gradient);
        }

        .job-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .job-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 5px;
        }

        .company {
            color: var(--primary);
            font-weight: 500;
            margin-bottom: 5px;
            font-size: 18px;
        }

        .job-status {
            padding: 8px 16px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-approved {
            background: rgba(76, 217, 100, 0.2);
            color: var(--success);
            border: 1px solid rgba(76, 217, 100, 0.3);
        }

        .status-pending {
            background: rgba(255, 204, 0, 0.2);
            color: var(--warning);
            border: 1px solid rgba(255, 204, 0, 0.3);
        }

        .job-details {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            background: var(--light);
            padding: 12px 15px;
            border-radius: 10px;
        }

        .detail-item i {
            color: var(--primary);
            margin-right: 12px;
            font-size: 18px;
        }

        .job-description {
            margin-bottom: 25px;
            color: #555;
            line-height: 1.7;
            background: var(--light);
            padding: 20px;
            border-radius: 10px;
        }

        .job-actions {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 50px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .btn i {
            margin-right: 8px;
        }

        .btn-close {
            background: var(--secondary);
            color: white;
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
        }

        .btn-close:hover {
            background: #FF5252;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255, 107, 107, 0.4);
        }

        .btn-view {
            background: var(--light);
            color: var(--dark);
            border: 1px solid var(--glass-border);
        }

        .btn-view:hover {
            background: white;
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        }

        details {
            margin-top: 25px;
        }

        summary {
            cursor: pointer;
            font-weight: 600;
            color: var(--primary);
            padding: 15px;
            border-radius: 10px;
            background: var(--light);
            display: flex;
            align-items: center;
            transition: var(--transition);
            list-style: none;
        }

        summary::-webkit-details-marker {
            display: none;
        }

        summary i {
            margin-right: 12px;
            transition: var(--transition);
        }

        details[open] summary i {
            transform: rotate(90deg);
        }

        summary:hover {
            background: rgba(108, 99, 255, 0.1);
        }

        .applications-list {
            margin-top: 20px;
            padding: 20px;
            background: var(--light);
            border-radius: 10px;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .application-item {
            padding: 15px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: var(--transition);
        }

        .application-item:hover {
            background: white;
            border-radius: 8px;
        }

        .application-item:last-child {
            border-bottom: none;
        }

        .resume-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            padding: 8px 15px;
            border-radius: 30px;
            background: rgba(108, 99, 255, 0.1);
            transition: var(--transition);
        }

        .resume-link i {
            margin-right: 8px;
        }

        .resume-link:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(108, 99, 255, 0.3);
        }

        .no-jobs {
            text-align: center;
            padding: 60px 40px;
            background: var(--card-bg);
            border-radius: 20px;
            box-shadow: var(--shadow);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            border: 1px solid var(--glass-border);
        }

        .no-jobs i {
            font-size: 80px;
            margin-bottom: 25px;
            background: var(--accent-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .no-jobs h3 {
            font-size: 28px;
            margin-bottom: 15px;
            color: var(--dark);
        }

        .no-jobs p {
            color: #666;
            margin-bottom: 30px;
            font-size: 18px;
        }

        .btn-primary {
            background: var(--primary-gradient);
            color: white;
            padding: 15px 30px;
            font-size: 16px;
            box-shadow: 0 5px 15px rgba(108, 99, 255, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(108, 99, 255, 0.4);
        }

        footer {
            text-align: center;
            padding: 30px;
            margin-top: 60px;
            color: var(--dark);
            font-size: 14px;
            opacity: 0.8;
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 20px;
            }
            
            nav {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .dashboard-cards {
                grid-template-columns: 1fr;
            }
            
            .job-header {
                flex-direction: column;
                gap: 15px;
            }
            
            .job-details {
                grid-template-columns: 1fr;
            }
            
            .job-actions {
                flex-direction: column;
            }
            
            .page-title {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <div class="floating-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>

    <header>
        <div class="header-content">
            <div class="logo">
                <i class="fas fa-hands-helping"></i>
                <span>Insieme</span>
            </div>
            <nav>
                <a href="post_job.php"><i class="fas fa-plus"></i> Post Job</a>
                <a href="recruiter_panel.php"><i class="fas fa-briefcase"></i> My Jobs</a>
                <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <h1 class="page-title">
            <i class="fas fa-user-tie"></i> Recruiter Dashboard
        </h1>

        <div class="dashboard-cards">
            <div class="card stat-card">
                <i class="fas fa-briefcase"></i>
                <h3><?php echo mysqli_num_rows($result); ?></h3>
                <p>Active Job Posts</p>
            </div>
            <div class="card stat-card">
                <i class="fas fa-file-alt"></i>
                <h3>
                    <?php
                    $total_apps = 0;
                    if (mysqli_num_rows($result) > 0) {
                        mysqli_data_seek($result, 0);
                        while ($row = mysqli_fetch_assoc($result)) {
                            $job_id = $row['id'];
                            $apps = mysqli_query($conn, "SELECT * FROM job_applications WHERE job_id = $job_id");
                            $total_apps += mysqli_num_rows($apps);
                        }
                        mysqli_data_seek($result, 0);
                    }
                    echo $total_apps;
                    ?>
                </h3>
                <p>Total Applications</p>
            </div>
            <div class="card stat-card">
                <i class="fas fa-check-circle"></i>
                <h3>
                    <?php
                    $approved = 0;
                    if (mysqli_num_rows($result) > 0) {
                        mysqli_data_seek($result, 0);
                        while ($row = mysqli_fetch_assoc($result)) {
                            if ($row['status'] === 'approved') $approved++;
                        }
                        mysqli_data_seek($result, 0);
                    }
                    echo $approved;
                    ?>
                </h3>
                <p>Approved Jobs</p>
            </div>
        </div>

        <div class="job-list">
            <?php if (mysqli_num_rows($result) === 0): ?>
                <div class="no-jobs">
                    <i class="fas fa-folder-open"></i>
                    <h3>No Job Posts Yet</h3>
                    <p>You haven't posted any jobs yet. Get started by posting your first job opportunity!</p>
                    <a href="post_job.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Post Your First Job
                    </a>
                </div>
            <?php else: ?>
                <?php while ($row = mysqli_fetch_assoc($result)):
                    $job_id = $row['id'];
                    $status = ucfirst($row['status']);
                ?>
                    <div class="job-card">
                        <div class="job-header">
                            <div>
                                <h2 class="job-title"><?= htmlspecialchars($row['title']) ?></h2>
                                <p class="company"><?= htmlspecialchars($row['company_name']) ?></p>
                            </div>
                            <span class="job-status status-<?= $row['status'] ?>"><?= $status ?></span>
                        </div>

                        <div class="job-details">
                            <div class="detail-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?= htmlspecialchars($row['location']) ?></span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-money-bill-wave"></i>
                                <span><?= htmlspecialchars($row['salary']) ?></span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-clock"></i>
                                <span>Posted: <?= date('M j, Y', strtotime($row['posted_at'])) ?></span>
                            </div>
                        </div>

                        <div class="job-description">
                            <p><?= nl2br(htmlspecialchars($row['description'])) ?></p>
                        </div>

                        <?php if ($row['status'] === 'approved'): ?>
                            <div class="job-actions">
                                <form method="get" onsubmit="return confirm('Are you sure you want to close this job post?');">
                                    <input type="hidden" name="close_job" value="<?= $job_id ?>">
                                    <button type="submit" class="btn btn-close">
                                        <i class="fas fa-times"></i> Close Job
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>

                        <?php if ($row['status'] === 'approved' || $row['status'] === 'closed'): ?>
                            <details>
                                <summary>
                                    <i class="fas fa-chevron-right"></i> View Applications (<?php 
                                        $apps_count = mysqli_query($conn, "SELECT COUNT(*) as count FROM job_applications WHERE job_id = $job_id");
                                        $count = mysqli_fetch_assoc($apps_count)['count'];
                                        echo $count;
                                    ?>)
                                </summary>
                                <div class="applications-list">
                                    <?php
                                    $apps = mysqli_query($conn, "SELECT * FROM job_applications WHERE job_id = $job_id");
                                    if (mysqli_num_rows($apps) > 0) {
                                        while ($app = mysqli_fetch_assoc($apps)) {
                                            $resume = htmlspecialchars($app['resume_path']);
                                            $resume_path = "jobs/" . $resume;
                                            echo '
                                            <div class="application-item">
                                                <div>
                                                    <strong>User ID:</strong> ' . $app['user_id'] . '
                                                </div>
                                                <a href="../' . $resume_path . '" target="_blank" class="resume-link">
                                                    <i class="fas fa-download"></i> View Resume
                                                </a>
                                            </div>';
                                        }
                                    } else {
                                        echo "<p>No applications yet.</p>";
                                    }
                                    ?>
                                </div>
                            </details>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>© <?php echo date('Y'); ?> Insieme Recruiter Portal. All rights reserved.</p>
    </footer>

    <script>
        // Add some subtle animations
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.card, .job-card');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
                card.classList.add('fade-in');
            });
        });
    </script>
</body>
</html>