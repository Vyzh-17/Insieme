<?php
$amount = isset($_GET['amount']) ? intval($_GET['amount']) : 0;

$mysqli = new mysqli("localhost", "root", "", "insieme");

if ($mysqli->connect_error) {
  die("Connection failed: " . $mysqli->connect_error);
}

$stmt = $mysqli->prepare("INSERT INTO donation (amount) VALUES (?)");
$stmt->bind_param("i", $amount);
$stmt->execute();

$stmt->close();
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Thank You for Your Donation</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    body {
      background: linear-gradient(135deg, #f0f7ff 0%, #e6f2ff 100%);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
    }
    
    .container {
      max-width: 800px;
      width: 100%;
      background: white;
      border-radius: 20px;
      box-shadow: 0 15px 30px rgba(0, 82, 204, 0.15);
      overflow: hidden;
      display: flex;
      flex-direction: column;
    }
    
    .header {
      background: linear-gradient(135deg, #0066cc 0%, #003d99 100%);
      color: white;
      padding: 40px 30px;
      text-align: center;
    }
    
    .header h1 {
      font-size: 2.5rem;
      margin-bottom: 10px;
      font-weight: 600;
    }
    
    .header p {
      font-size: 1.2rem;
      opacity: 0.9;
    }
    
    .content {
      padding: 50px 40px;
      text-align: center;
    }
    
    .thank-you-message {
      margin-bottom: 40px;
    }
    
    .thank-you-message h2 {
      color: #0066cc;
      font-size: 2.2rem;
      margin-bottom: 20px;
    }
    
    .amount-display {
      background: #e6f2ff;
      border-radius: 15px;
      padding: 25px;
      display: inline-flex;
      align-items: center;
      margin: 25px 0;
      box-shadow: 0 5px 15px rgba(0, 102, 204, 0.1);
    }
    
    .amount-display .icon {
      background: #0066cc;
      color: white;
      width: 60px;
      height: 60px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 20px;
      font-size: 1.8rem;
    }
    
    .amount-display .text {
      text-align: left;
    }
    
    .amount-display .amount {
      color: #003d99;
      font-size: 2.8rem;
      font-weight: 700;
      line-height: 1;
    }
    
    .amount-display .label {
      color: #0066cc;
      font-size: 1.1rem;
      font-weight: 500;
      margin-top: 5px;
    }
    
    .impact-section {
      background: #f5faff;
      border-radius: 15px;
      padding: 30px;
      margin: 30px 0;
    }
    
    .impact-section h3 {
      color: #0066cc;
      margin-bottom: 20px;
      font-size: 1.5rem;
    }
    
    .impact-icons {
      display: flex;
      justify-content: space-around;
      margin-top: 25px;
      flex-wrap: wrap;
    }
    
    .impact-item {
      display: flex;
      flex-direction: column;
      align-items: center;
      margin: 10px;
      width: 120px;
    }
    
    .impact-icon {
      background: #0066cc;
      color: white;
      width: 70px;
      height: 70px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.8rem;
      margin-bottom: 12px;
    }
    
    .impact-item span {
      color: #003d99;
      font-weight: 500;
      font-size: 0.95rem;
      text-align: center;
    }
    
    .actions {
      margin-top: 40px;
    }
    
    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 15px 35px;
      font-size: 1.1rem;
      font-weight: 600;
      text-decoration: none;
      border-radius: 50px;
      transition: all 0.3s ease;
      margin: 0 10px;
    }
    
    .btn-primary {
      background: #0066cc;
      color: white;
      box-shadow: 0 5px 15px rgba(0, 102, 204, 0.3);
    }
    
    .btn-primary:hover {
      background: #0052a3;
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(0, 102, 204, 0.4);
    }
    
    .btn-outline {
      border: 2px solid #0066cc;
      color: #0066cc;
      background: transparent;
    }
    
    .btn-outline:hover {
      background: #e6f2ff;
      transform: translateY(-3px);
    }
    
    .footer {
      background: #003d99;
      color: rgba(255, 255, 255, 0.8);
      text-align: center;
      padding: 25px;
      font-size: 0.95rem;
    }
    
    .footer a {
      color: white;
      text-decoration: none;
    }
    
    .footer a:hover {
      text-decoration: underline;
    }
    
    @media (max-width: 768px) {
      .header {
        padding: 30px 20px;
      }
      
      .header h1 {
        font-size: 2rem;
      }
      
      .content {
        padding: 40px 20px;
      }
      
      .amount-display {
        flex-direction: column;
        text-align: center;
        padding: 20px;
      }
      
      .amount-display .icon {
        margin-right: 0;
        margin-bottom: 15px;
      }
      
      .amount-display .text {
        text-align: center;
      }
      
      .impact-icons {
        justify-content: center;
      }
      
      .btn {
        width: 100%;
        margin: 10px 0;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>Thank You for Your Generosity!</h1>
      <p>Your contribution makes a meaningful difference</p>
    </div>
    
    <div class="content">
      <div class="thank-you-message">
        <h2>We Appreciate Your Support</h2>
        <p>Your donation helps us continue our mission and create positive change in our community.</p>
        
        <div class="amount-display">
          <div class="icon">
            <i class="fas fa-hand-holding-heart"></i>
          </div>
          <div class="text">
            <div class="amount">₹<?php echo htmlspecialchars($amount); ?></div>
            <div class="label">Donation Amount</div>
          </div>
        </div>
      </div>
      
      <div class="impact-section">
        <h3>Your donation will help provide:</h3>
        <div class="impact-icons">
          <div class="impact-item">
            <div class="impact-icon">
              <i class="fas fa-book"></i>
            </div>
            <span>Educational Materials</span>
          </div>
          <div class="impact-item">
            <div class="impact-icon">
              <i class="fas fa-utensils"></i>
            </div>
            <span>Food Supplies</span>
          </div>
          <div class="impact-item">
            <div class="impact-icon">
              <i class="fas fa-heartbeat"></i>
            </div>
            <span>Medical Support</span>
          </div>
          <div class="impact-item">
            <div class="impact-icon">
              <i class="fas fa-home"></i>
            </div>
            <span>Shelter Assistance</span>
          </div>
        </div>
      </div>
      
      <div class="actions">
        <a href="../index.html" class="btn btn-primary">
          Go Back Home
        </a>
        <a href="#" class="btn btn-outline">
           Share Your Support
        </a>
      </div>
    </div>
    
    <div class="footer">
      <p>© 2023 Insieme Foundation. All donations are tax deductible in accordance with IRS regulations.</p>
      <p>Questions? <a href="mailto:contact@insieme.org">contact@insieme.org</a> | <a href="tel:+11234567890">+1 (123) 456-7890</a></p>
    </div>
  </div>

  <script>
    // Add subtle animation to the amount display
    document.addEventListener('DOMContentLoaded', function() {
      const amountDisplay = document.querySelector('.amount-display');
      
      setTimeout(() => {
        amountDisplay.style.transform = 'scale(1.05)';
        amountDisplay.style.transition = 'transform 0.5s ease';
        
        setTimeout(() => {
          amountDisplay.style.transform = 'scale(1)';
        }, 300);
      }, 500);
      
      // Button hover effects
      const buttons = document.querySelectorAll('.btn');
      buttons.forEach(btn => {
        btn.addEventListener('mouseenter', function() {
          this.style.transform = 'translateY(-3px)';
        });
        
        btn.addEventListener('mouseleave', function() {
          this.style.transform = 'translateY(0)';
        });
      });
    });
  </script>
</body>
</html>