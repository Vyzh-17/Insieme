<?php
// service_request.php - Original structure with minor security enhancements
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "insieme";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Basic sanitization (preserving original structure)
    $service_name = htmlspecialchars(trim($_POST['service_name']));
    $city = htmlspecialchars(trim($_POST['city']));
    $phone = trim($_POST['phone']);
    
    // Simple phone validation (preserves original flow)
    if (!preg_match('/^[0-9+]{8,15}$/', $phone)) {
        $message = "❌ Please enter a valid phone number (8-15 digits)";
    } else {
        $sql = "INSERT INTO service_requests (service_name, city, phone) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $service_name, $city, $phone);

        if ($stmt->execute()) {
            $message = "✅ Your request has been submitted!";
        } else {
            $message = "❌ Error: Please try again later";
            // Log error instead of showing details: error_log($conn->error);
        }
        $stmt->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insieme - Service Request</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', 'Segoe UI', sans-serif;
        }

        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        :root {
            --primary: #3a86ff; /* Vibrant blue */
            --secondary: #83c5ff; /* Soft blue */
            --accent: #e6f2ff; /* Lightest blue */
            --light: #ffffff;
            --dark: #1a3e72; /* Deep blue */
            --success: #4caf50;
            --error: #f44336;
            --gradient: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }

        body {
            background-color: #f9fbfe;
            color: #333;
            line-height: 1.7;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            text-align: center;
            padding: 40px 0;
            margin-bottom: 10px;
            position: relative;
        }

        header::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 150px;
            height: 4px;
            background: var(--gradient);
            border-radius: 2px;
        }

        .logo {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .logo-icon {
            width: 80px;
            height: 80px;
            background: var(--gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 20px rgba(58, 134, 255, 0.3);
        }

        .logo i {
            font-size: 2.5rem;
            color: white;
        }

        h1 {
            font-size: 2.8rem;
            background: var(--gradient);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 10px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .tagline {
            font-size: 1.2rem;
            color: var(--dark);
            max-width: 700px;
            margin: 0 auto 30px;
            font-weight: 300;
        }

        .services-section {
            margin-bottom: 60px;
        }

        .section-title {
            text-align: center;
            font-size: 2.2rem;
            color: var(--dark);
            margin-bottom: 40px;
            position: relative;
            padding-bottom: 15px;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--gradient);
            border-radius: 2px;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .service-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(58, 134, 255, 0.08);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.1);
            position: relative;
            z-index: 1;
        }

        .service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--gradient);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: -1;
        }

        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(58, 134, 255, 0.2);
            color: white;
        }

        .service-card:hover::before {
            opacity: 1;
        }

        .service-card:hover .card-header {
            background: transparent;
            color: white;
        }

        .service-card:hover .card-body h3,
        .service-card:hover .card-body p,
        .service-card:hover .features h4,
        .service-card:hover .features li {
            color: white;
        }

        .service-card:hover .features li i {
            color: white;
        }

        .card-header {
            background: var(--primary);
            color: white;
            padding: 25px;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: all 0.3s ease;
        }

        .card-header i {
            font-size: 2rem;
        }

        .card-body {
            padding: 25px;
            position: relative;
        }

        .card-body h3 {
            color: var(--dark);
            margin-bottom: 15px;
            font-size: 1.4rem;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .card-body p {
            color: #555;
            margin-bottom: 20px;
            font-size: 1rem;
            line-height: 1.6;
            transition: color 0.3s ease;
        }

        .features {
            margin-top: 20px;
        }

        .features h4 {
            color: var(--primary);
            margin-bottom: 12px;
            font-size: 1.1rem;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .features ul {
            list-style-type: none;
            padding-left: 0;
        }

        .features li {
            padding: 8px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 0.95rem;
            transition: color 0.3s ease;
        }

        .features li:last-child {
            border-bottom: none;
        }

        .features li i {
            color: var(--primary);
            margin-top: 4px;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }

        .form-section {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(58, 134, 255, 0.1);
            padding: 40px;
            max-width: 700px;
            margin: 0 auto;
            position: relative;
            overflow: hidden;
        }

        .form-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 8px;
            height: 100%;
            background: var(--gradient);
        }

        .form-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: 500;
            color: var(--dark);
            font-size: 1.05rem;
        }

        input, select {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e0e9ff;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: #f9fbfe;
        }

        input:focus, select:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(58, 134, 255, 0.2);
            background-color: white;
        }

        .btn {
            background: var(--gradient);
            color: white;
            border: none;
            padding: 16px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: block;
            width: 100%;
            margin-top: 15px;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--secondary) 0%, var(--primary) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: -1;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(58, 134, 255, 0.3);
        }

        .btn:hover::before {
            opacity: 1;
        }

        .msg {
            padding: 15px;
            margin: 20px 0;
            border-radius: 10px;
            text-align: center;
            font-weight: 500;
            font-size: 1.05rem;
            position: relative;
            overflow: hidden;
        }

        .msg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
        }

        .success {
            background: #edf7ed;
            color: #1e4620;
            border-left: 5px solid #4caf50;
        }

        .error {
            background: #fde8e8;
            color: #611a15;
            border-left: 5px solid #f44336;
        }

        .service-info {
            text-align: center;
            margin-bottom: 40px;
            padding: 0 20px;
        }

        .service-info p {
            font-size: 1.15rem;
            color: #444;
            max-width: 800px;
            margin: 0 auto;
            line-height: 1.8;
        }

        .note {
            font-size: 0.9rem;
            color: #666;
            margin-top: 6px;
            font-style: italic;
        }
    

        footer {
            text-align: center;
            padding: 30px 0;
            margin-top: 60px;
            color: var(--dark);
            border-top: 1px solid rgba(26, 62, 114, 0.1);
            font-size: 0.95rem;
        }

        footer p:first-child {
            margin-bottom: 10px;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .services-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .form-section {
                padding: 30px 20px;
            }
            
            h1 {
                font-size: 2.2rem;
            }
            
            .section-title {
                font-size: 1.8rem;
            }
        }

        /* Floating bubbles decoration */
        .bubble {
            position: absolute;
            border-radius: 50%;
            background: rgba(58, 134, 255, 0.1);
            z-index: -1;
        }

        .bubble-1 {
            width: 150px;
            height: 150px;
            top: 10%;
            left: 5%;
            animation: float 8s ease-in-out infinite;
        }

        .bubble-2 {
            width: 200px;
            height: 200px;
            bottom: 15%;
            right: 5%;
            animation: float 10s ease-in-out infinite;
            animation-delay: 1s;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
            }
            50% {
                transform: translateY(-20px) rotate(5deg);
            }
        }
    </style>
</head>
<body>
    <div class="bubble bubble-1"></div>
    <div class="bubble bubble-2"></div>
    
    <div class="container">
        <header>
            <div class="logo">
                <div class="logo-icon"><a href="../index.html ">
                    <i class="fas fa-hands-helping"></i></a>
                </div>
                <h1>Insieme Support</h1>
            </div>
            <p class="tagline">Empowering individuals with disabilities through compassionate, specialized support</p>
        </header>
        
        
        <section class="services-section">
            <h2 class="section-title">Our Services</h2>
            
            <div class="services-grid">
                <!-- HelpHand Service Card -->
                <div class="service-card">
                    <div class="card-header">
                        <i class="fas fa-book-reader"></i>
                        <h2>HelpHand</h2>
                    </div>
                    <div class="card-body">
                        <h3>Exam Assistance for Visually Impaired</h3>
                        <p>Comprehensive support system ensuring blind and visually impaired individuals receive equal opportunities during examinations through specialized assistance.</p>
                        
                        <div class="features">
                            <h4>Key Features:</h4>
                            <ul>
                                <li><i class="fas fa-check"></i> Certified reader assistance</li>
                                <li><i class="fas fa-check"></i> Professional scribe services</li>
                                <li><i class="fas fa-check"></i> Braille and large print options</li>
                                <li><i class="fas fa-check"></i> Custom time accommodations</li>
                                <li><i class="fas fa-check"></i> Pre-exam orientation</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Job Request Service Card -->
                <div class="service-card">
                    <div class="card-header">
                        <i class="fas fa-briefcase"></i>
                        <h2>CareerBridge</h2>
                    </div>
                    <div class="card-body">
                        <h3>Employment Support Network</h3>
                        <p>End-to-end employment solutions from job search to workplace integration for people with disabilities.</p>
                        
                        <div class="features">
                            <h4>Key Features:</h4>
                            <ul>
                                <li><i class="fas fa-check"></i> Personalized career coaching</li>
                                <li><i class="fas fa-check"></i> Employer partnership program</li>
                                <li><i class="fas fa-check"></i> Workplace adaptation consulting</li>
                                <li><i class="fas fa-check"></i> Ongoing employment support</li>
                                <li><i class="fas fa-check"></i> Skill development workshops</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Paraolympic Training Service Card -->
                <div class="service-card">
                    <div class="card-header">
                        <i class="fas fa-running"></i>
                        <h2>ParaFit</h2>
                    </div>
                    <div class="card-body">
                        <h3>Adaptive Sports Training</h3>
                        <p>Comprehensive athletic development programs for para-athletes from beginner to elite levels.</p>
                        
                        <div class="features">
                            <h4>Key Features:</h4>
                            <ul>
                                <li><i class="fas fa-check"></i> Individualized training plans</li>
                                <li><i class="fas fa-check"></i> State-of-the-art adaptive equipment</li>
                                <li><i class="fas fa-check"></i> Certified para-sport coaches</li>
                                <li><i class="fas fa-check"></i> Competition pathway development</li>
                                <li><i class="fas fa-check"></i> Mental performance training</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Legal Advice Service Card -->
                <div class="service-card">
                    <div class="card-header">
                        <i class="fas fa-balance-scale"></i>
                        <h2>RightsGuard</h2>
                    </div>
                    <div class="card-body">
                        <h3>Disability Legal Advocacy</h3>
                        <p>Expert legal services protecting the rights and accessibility of individuals with disabilities.</p>
                        
                        <div class="features">
                            <h4>Key Features:</h4>
                            <ul>
                                <li><i class="fas fa-check"></i> Discrimination case support</li>
                                <li><i class="fas fa-check"></i> ADA compliance guidance</li>
                                <li><i class="fas fa-check"></i> Benefits appeals assistance</li>
                                <li><i class="fas fa-check"></i> Accessibility audits</li>
                                <li><i class="fas fa-check"></i> Educational rights advocacy</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="service-card">
                    <div class="card-header">
                        <i class="fas fa-heart"></i>
                        <h2>MindCare</h2>
                    </div>
                    <div class="card-body">
                        <h3>Specialized Counseling</h3>
                        <p>Mental health services addressing the unique psychological needs of individuals with disabilities.</p>
                        
                        <div class="features">
                            <h4>Service Features:</h4>
                            <ul>
                                <li><i class="fas fa-check-circle"></i> Disability-affirming therapy</li>
                                <li><i class="fas fa-check-circle"></i> Peer support networks</li>
                                <li><i class="fas fa-check-circle"></i> Family counseling programs</li>
                                <li><i class="fas fa-check-circle"></i> Trauma-informed care</li>
                                <li><i class="fas fa-check-circle"></i> Flexible session formats</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="service-card">
                    <div class="card-header">
                        <i class="fas fa-universal-access"></i>
                        <h2>AccessPlus</h2>
                    </div>
                    <div class="card-body">
                        <h3>Accessibility Consulting</h3>
                        <p>Comprehensive solutions to remove physical and digital barriers for full participation.</p>
                        
                        <div class="features">
                            <h4>Service Features:</h4>
                            <ul>
                                <li><i class="fas fa-check-circle"></i> Facility accessibility audits</li>
                                <li><i class="fas fa-check-circle"></i> Digital accessibility testing</li>
                                <li><i class="fas fa-check-circle"></i> Assistive technology training</li>
                                <li><i class="fas fa-check-circle"></i> Universal design consulting</li>
                                <li><i class="fas fa-check-circle"></i> Awareness training programs</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <section class="form-section">
            <h2 class="section-title">Request Service</h2>
            
            <?php if ($message): ?>
                <div class="msg <?= strpos($message, '✅') !== false ? 'success' : 'error' ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>
            
            <form method="post" class="form-container"id="serviceForm" >
                <div class="form-group">
                    <label for="service_name">Select Service</label>
                    <select id="service_name" name="service_name" required>
                        <option value="">-- Choose Service --</option>
                        <option value="HelpHand">HelpHand</option>
                        <option value="CareerBridge">CareerBridge</option>
                        <option value="ParaFit">ParaFit</option>
                        <option value="RightsGuard">RightsGuard</option>
                        <option value="MindCare">MindCare</option>
                        <option value="AccessPlus">AccessPlus</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="city">Location</label>
                    <input 
                        type="text" 
                        id="city" 
                        name="city" 
                        placeholder="Enter your city or region" 
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="phone">Contact Number</label>
                    <input 
                        type="tel" 
                        id="phone" 
                        name="phone" 
                        placeholder="Enter phone number" 
                        required
                        pattern="[0-9+]{8,15}"
                        title="8-15 digits with optional '+'"
                    >
                    <p class="note">We'll contact you to confirm details</p>
                </div>
                
                <button type="submit" class="btn">
                    <i class="fas fa-paper-plane"></i> Submit Request
                </button>
            </form>
        </section>
        
        <footer>
            <p>© 2023 Insieme Support Services. All rights reserved.</p>
            <p>Building an inclusive world, one service at a time.</p>
        </footer>
    </div>
</body>
</html>