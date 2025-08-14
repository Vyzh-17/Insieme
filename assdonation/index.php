<?php
/* 🌈✨ Insieme Assistive Devices Platform ✨🌈
   📂 File: devices.php
   🚀 Mission: Connect donors with those in need
   💻 Author: [Your Name] */
include 'db.php';
session_start();

$sql = "SELECT d.*, u.username AS donor FROM devices d 
        JOIN users u ON d.user_id = u.id 
        WHERE d.id NOT IN (SELECT device_id FROM requests WHERE status='approved')
        ORDER BY d.posted_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en" class="theme-light">
<head>
    <!-- 🎨 Meta & Links -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Devices | Insieme - Bridging Needs with Generosity</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    
    <style>
        /* 🌟 CSS Variables - Cosmic Theme */
        :root {
            --primary: #5e72e4;
            --primary-light: #ebf4ff;
            --primary-dark: #4a5acf;
            --secondary: #11cdef;
            --accent: #f5365c;
            --text: #2d3748;
            --text-light: #718096;
            --light-bg: #f8fafc;
            --card-bg: #ffffff;
            --border: #e2e8f0;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px rgba(67, 97, 238, 0.1);
            --shadow-lg: 0 10px 25px rgba(67, 97, 238, 0.15);
            --radius: 12px;
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            --gradient: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }

        /* 🌌 Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: var(--light-bg);
            color: var(--text);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-image: radial-gradient(circle at 10% 20%, rgba(235, 244, 255, 0.5) 0%, transparent 20%);
        }

        /* 🚀 Header - Floating Navigation */
        header {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.8);
        }

        .header-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            
            ont-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: 1.8rem;
            color: var(--primary);
            text-decoration: none;
           font-family: 'Poppins', sans-serif;
            
        }

        .logo-icon {
            background: var(--gradient);
            color: white;
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 6px rgba(94, 114, 228, 0.3);
        }

        /* 🎯 Navigation */
        .nav-links {
            display: flex;
            gap: 1.5rem;
            list-style: none;
        }

        .nav-links a {
            color: var(--text);
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem;
            transition: var(--transition);
            position: relative;
            font-size: 1.05rem;
        }

        .nav-links a:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: var(--transition);
        }

        .nav-links a:hover:after,
        .nav-links a.active:after {
            width: 100%;
        }

        .nav-links a:hover {
            color: var(--primary);
        }

        /* 🎛️ Buttons */
        .btn {
            padding: 0.6rem 1.25rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--gradient);
            color: white !important;
            box-shadow: 0 4px 6px rgba(94, 114, 228, 0.3);
        }

        .btn-outline {
            border: 1px solid var(--border);
            color: var(--text);
            background: transparent;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 14px rgba(94, 114, 228, 0.3);
        }

        .btn-outline:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        /* 🌠 Main Content */
        main {
            flex: 1;
            max-width: 1400px;
            width: 100%;
            margin: 3rem auto;
            padding: 0 2rem;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .page-header {
            text-align: center;
            margin-bottom: 4rem;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        .page-header h1 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            background: linear-gradient(to right, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.2;
            font-family: 'Playfair Display', serif;
        }

        .page-header p {
            color: var(--text-light);
            font-size: 1.15rem;
            line-height: 1.8;
        }

        /* 💎 Device Cards Grid */
        .devices-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 2.5rem;
            padding: 1rem 0;
        }

        /* ✨ Device Card - 3D Effect */
        .device-card {
            background: var(--card-bg);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            height: 100%;
            position: relative;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .device-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient);
        }

        .device-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
        }

        .card-image {
            height: 220px;
            overflow: hidden;
            background: linear-gradient(135deg, #f0f4ff 0%, #e6f7ff 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .device-card:hover .card-image img {
            transform: scale(1.1);
        }

        .card-content {
            padding: 1.75rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .card-badge {
            background: var(--primary-light);
            color: var(--primary);
            padding: 0.35rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 700;
            margin-bottom: 1.25rem;
            display: inline-block;
            align-self: flex-start;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card-content h3 {
            font-size: 1.5rem;
            margin-bottom: 0.75rem;
            color: var(--text);
            font-weight: 700;
        }

        .device-donor {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-light);
            margin-bottom: 1.25rem;
            font-size: 0.95rem;
        }

        .device-donor i {
            color: var(--primary);
        }

        .device-description {
            color: var(--text-light);
            margin-bottom: 1.75rem;
            flex-grow: 1;
            line-height: 1.7;
        }

        /* 🛎️ Call-to-Action */
        .request-btn {
            display: block;
            width: 100%;
            padding: 0.85rem;
            background: var(--gradient);
            color: white;
            text-align: center;
            text-decoration: none;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            font-size: 1rem;
            box-shadow: 0 4px 6px rgba(94, 114, 228, 0.3);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .request-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 14px rgba(94, 114, 228, 0.3);
        }

        /* 🌈 Empty State */
        .no-devices {
            grid-column: 1 / -1;
            text-align: center;
            padding: 5rem 2rem;
            background: var(--card-bg);
            border-radius: var(--radius);
            box-shadow: var(--shadow-md);
            border: 1px dashed var(--border);
            margin: 2rem 0;
        }

        .no-devices i {
            font-size: 5rem;
            color: var(--text-light);
            margin-bottom: 2rem;
            opacity: 0.5;
        }

        .no-devices h3 {
            font-size: 2rem;
            margin-bottom: 1.5rem;
            color: var(--text);
            font-weight: 700;
        }

        .no-devices p {
            color: var(--text-light);
            margin-bottom: 2.5rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            font-size: 1.1rem;
            line-height: 1.8;
        }

        /* 🏰 Footer */
        footer {
            background: var(--card-bg);
            border-top: 1px solid var(--border);
            padding: 4rem 0 2rem;
            margin-top: 5rem;
        }

        .footer-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 3rem;
        }

        .footer-col h3 {
            font-size: 1.5rem;
            margin-bottom: 1.75rem;
            color: var(--text);
            position: relative;
            padding-bottom: 1rem;
            font-weight: 700;
        }

        .footer-col h3:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 4px;
            background: var(--gradient);
            border-radius: 2px;
        }

        .copyright {
            text-align: center;
            padding-top: 3rem;
            margin-top: 3rem;
            border-top: 1px solid var(--border);
            color: var(--text-light);
            font-size: 0.95rem;
        }

        /* 🎆 Responsive Design */
        @media (max-width: 1024px) {
            .page-header h1 {
                font-size: 2.5rem;
            }
        }

        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                gap: 1.5rem;
                padding: 1.5rem;
            }
            
            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .page-header h1 {
                font-size: 2.25rem;
            }
            
            .devices-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- 🚀 Header Section -->
    <header>
        <div class="header-container">
            <a href="../index.html" class="logo">
                <div class="logo-icon">
                    <i class="fas fa-hands-helping"></i>
                </div>
                <span>Insieme</span>
                
            </a>
            
          
            
            <div class="user-actions">
                <button onclick="startReading()"> Read Page</button>
                <button onclick="stopReading()"> Stop</button>
                <?php if (isset($_SESSION['username']) && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
                    <span class="user-greeting">
                        <i class="fas fa-user-shield"></i> <?= htmlspecialchars($_SESSION['username']) ?>
                    </span>
                    <a href="admin_requests.php" class="btn btn-primary"><i class="fas fa-cog"></i> Admin Panel</a>
                    <a href="logout.php" class="btn btn-outline"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    
                <?php elseif (isset($_SESSION['username'])): ?>
                     <a href="post_device.php" class="btn btn-primary"><i class="fas fa-plus"></i> Post Device</a>
                    <span class="user-greeting"><a href="profile.php" class="btn btn-outline">
                        <i class="fas fa-user-circle"></i> <?= htmlspecialchars($_SESSION['username']) ?></a>
                    </span>
                   
                   
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline"><i class="fas fa-sign-in-alt"></i> Login</a>
                    <a href="register.php" class="btn btn-primary"><i class="fas fa-user-plus"></i> Register</a>
                <?php endif; ?>
                
            </div>
        </div>
    </header>

    <!-- 🌟 Main Content -->
    <main>
        <div class="page-header">
            <h1>Available Assistive Devices</h1>
            <p>Discover carefully donated assistive devices that can make a real difference. Each device is waiting for a new home where it can help someone in need.</p>
        </div>

        <div class="devices-container">
            <?php if ($result->num_rows == 0): ?>
                <div class="no-devices">
                    <i class="fas fa-box-open"></i>
                    <h3>No Devices Available</h3>
                    <p>Currently there are no assistive devices listed. Be the first to donate a device and help someone in need.</p>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="post_device.php" class="request-btn" style="max-width: 300px; margin: 0 auto;">Donate a Device</a>
                    <?php else: ?>
                        <a href="login.php" class="request-btn" style="max-width: 200px; margin: 0 auto;">Login to Donate</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="device-card">
                        <div class="card-image">
                            <?php if(!empty($row['image'])): ?>
                                <img src="uploads/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['title']) ?>">
                            <?php else: ?>
                                <div class="placeholder-icon">
                                    <i class="fas fa-medkit"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-content">
                            <span class="card-badge">Available</span>
                            <h3><?= htmlspecialchars($row['title']) ?></h3>
                            <div class="device-donor">
                                <i class="fas fa-heart"></i>
                                <span>Donated by <?= htmlspecialchars($row['donor']) ?></span>
                            </div>
                            <p class="device-description"><?= nl2br(htmlspecialchars($row['description'])) ?></p>
                            <div class="card-actions">
                                <?php if(isset($_SESSION['user_id'])): ?>
                                    <form method="get" action="request_device.php">
                                        <input type="hidden" name="device_id" value="<?= $row['id'] ?>">
                                        <button type="submit" class="request-btn">
                                            <i class="fas fa-hand-holding-heart"></i> Request Device
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <div class="login-prompt">
                                        <a href="login.php">Login</a> to request this device
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </main>

    <!-- 🏡 Footer -->
    <footer>
        <div class="footer-container">
            <div class="footer-col">
                <h3>About Insieme</h3>
                <p>We connect people in need of assistive devices with generous donors. Our mission is to make assistive technology accessible to everyone.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            
            
            
            
        </div>
        
        <div class="copyright">
            <p>&copy; <?= date('Y') ?> Insieme Assistive Devices Network. All rights reserved.</p>
        </div>
    </footer>
</body>
<script>
let isReading = false;  
let utterance;

document.addEventListener("keydown", function(event) {
    if (event.key.toLowerCase() === "w") { // toggle on W
        if (!isReading) {
            startReading();
        } else {
            stopReading();
        }
    }
});

function startReading() {
    if (isReading) return; // avoid double start
    let text = document.body.innerText; 
    utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = "en-US"; 
    speechSynthesis.speak(utterance);
    isReading = true;
}

function stopReading() {
    speechSynthesis.cancel(); 
    isReading = false;
}
</script>
</html>