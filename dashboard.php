<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT balance FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($balance);
$stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Royal Trust Bank</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-blue: #0a1628;
            --secondary-blue: #1e3a8a;
            --accent-cyan: #00ffff;
            --accent-blue: #3b82f6;
            --glass-bg: rgba(15, 23, 42, 0.8);
            --glass-border: rgba(59, 130, 246, 0.3);
            --text-primary: #ffffff;
            --text-secondary: #94a3b8;
            --card-bg: rgba(15, 23, 42, 0.6);
            --success-green: #10b981;
            --warning-orange: #f59e0b;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--primary-blue);
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
            color: var(--text-primary);
        }

        /* Video Background */
        .video-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2;
        }

        .video-background video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Background Overlay */
        .bg-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 80%, rgba(0, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(59, 130, 246, 0.1) 0%, transparent 50%),
                linear-gradient(135deg, rgba(10, 22, 40, 0.95), rgba(15, 23, 42, 0.9));
            z-index: -1;
            animation: bg-shift 15s ease-in-out infinite;
        }

        @keyframes bg-shift {
            0%, 100% {
                background: 
                    radial-gradient(circle at 20% 80%, rgba(0, 255, 255, 0.1) 0%, transparent 50%),
                    radial-gradient(circle at 80% 20%, rgba(59, 130, 246, 0.1) 0%, transparent 50%),
                    linear-gradient(135deg, rgba(10, 22, 40, 0.95), rgba(15, 23, 42, 0.9));
            }
            33% {
                background: 
                    radial-gradient(circle at 70% 30%, rgba(0, 255, 255, 0.15) 0%, transparent 50%),
                    radial-gradient(circle at 30% 70%, rgba(147, 51, 234, 0.1) 0%, transparent 50%),
                    linear-gradient(135deg, rgba(10, 22, 40, 0.95), rgba(15, 23, 42, 0.9));
            }
            66% {
                background: 
                    radial-gradient(circle at 50% 50%, rgba(59, 130, 246, 0.12) 0%, transparent 50%),
                    radial-gradient(circle at 90% 10%, rgba(0, 255, 255, 0.08) 0%, transparent 50%),
                    linear-gradient(135deg, rgba(10, 22, 40, 0.95), rgba(15, 23, 42, 0.9));
            }
        }

        /* Grid Lines */
        .grid-lines {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(rgba(59, 130, 246, 0.08) 1px, transparent 1px),
                linear-gradient(90deg, rgba(59, 130, 246, 0.08) 1px, transparent 1px);
            background-size: 60px 60px;
            animation: grid-move 25s linear infinite;
            z-index: 1;
            pointer-events: none;
        }

        @keyframes grid-move {
            0% { transform: translate(0, 0); }
            100% { transform: translate(60px, 60px); }
        }

        /* Floating Particles */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 2;
        }

        .particle {
            position: absolute;
            width: 3px;
            height: 3px;
            background: rgba(0, 255, 255, 0.6);
            border-radius: 50%;
            animation: particleFloat 20s linear infinite;
            box-shadow: 0 0 15px rgba(0, 255, 255, 0.4);
        }

        .particle:nth-child(odd) {
            background: rgba(59, 130, 246, 0.6);
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.4);
        }

        .particle:nth-child(1) { left: 5%; animation-delay: 0s; }
        .particle:nth-child(2) { left: 15%; animation-delay: 3s; }
        .particle:nth-child(3) { left: 25%; animation-delay: 6s; }
        .particle:nth-child(4) { left: 35%; animation-delay: 2s; }
        .particle:nth-child(5) { left: 45%; animation-delay: 8s; }
        .particle:nth-child(6) { left: 55%; animation-delay: 1s; }
        .particle:nth-child(7) { left: 65%; animation-delay: 5s; }
        .particle:nth-child(8) { left: 75%; animation-delay: 7s; }
        .particle:nth-child(9) { left: 85%; animation-delay: 4s; }
        .particle:nth-child(10) { left: 95%; animation-delay: 9s; }

        @keyframes particleFloat {
            0% {
                bottom: -10px;
                opacity: 0;
                transform: translateX(0px) scale(0);
            }
            10% {
                opacity: 1;
                transform: translateX(10px) scale(1);
            }
            90% {
                opacity: 1;
                transform: translateX(-10px) scale(1);
            }
            100% {
                bottom: 100vh;
                opacity: 0;
                transform: translateX(5px) scale(0);
            }
        }

        /* Main Container */
        .container {
            position: relative;
            z-index: 10;
            min-height: 100vh;
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Header */
        header {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
        }

        header::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(0, 255, 255, 0.1), transparent);
            animation: shimmer 4s infinite;
        }

        @keyframes shimmer {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--accent-cyan), var(--accent-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-align: center;
            position: relative;
            z-index: 2;
            animation: glow-text 3s ease-in-out infinite alternate;
        }

        @keyframes glow-text {
            0% {
                text-shadow: 0 0 20px rgba(0, 255, 255, 0.5);
            }
            100% {
                text-shadow: 0 0 40px rgba(0, 255, 255, 0.8);
            }
        }

        /* Navigation */
        nav {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
        }

        nav ul {
            list-style: none;
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
        }

        nav li a {
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
            font-size: 1.1rem;
            padding: 1rem 2rem;
            border-radius: 15px;
            transition: all 0.3s ease;
            position: relative;
            background: var(--card-bg);
            border: 1px solid transparent;
            backdrop-filter: blur(10px);
        }

        nav li a:hover,
        nav li a.active {
            color: var(--accent-cyan);
            border-color: var(--accent-cyan);
            box-shadow: 0 10px 30px rgba(0, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        nav li a::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0, 255, 255, 0.1), rgba(59, 130, 246, 0.1));
            border-radius: 15px;
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: -1;
        }

        nav li a:hover::before {
            opacity: 1;
        }

        /* Main Content */
        main {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
        }

        /* Balance Card */
        .balance {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
            text-align: center;
            grid-column: span 2;
        }

        .balance::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(0, 255, 255, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
            z-index: 1;
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .balance-content {
            position: relative;
            z-index: 2;
        }

        .balance h2 {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 2rem;
            color: var(--text-primary);
        }

        .balance-amount {
            font-size: 4rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--success-green), var(--accent-cyan));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 2rem;
            animation: pulse-amount 2s ease-in-out infinite;
        }

        @keyframes pulse-amount {
            0%, 100% {
                transform: scale(1);
                filter: drop-shadow(0 0 20px rgba(16, 185, 129, 0.5));
            }
            50% {
                transform: scale(1.02);
                filter: drop-shadow(0 0 30px rgba(16, 185, 129, 0.8));
            }
        }

        /* Action Cards */
        .action-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 35px 70px rgba(0, 0, 0, 0.4);
            border-color: var(--accent-cyan);
        }

        .action-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(0, 255, 255, 0.1), transparent);
            transition: left 0.5s ease;
        }

        .action-card:hover::before {
            left: 100%;
        }

        .action-card h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-primary);
            position: relative;
            z-index: 2;
        }

        .action-card p {
            color: var(--text-secondary);
            margin-bottom: 2rem;
            line-height: 1.6;
            position: relative;
            z-index: 2;
        }

        /* Buttons */
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, var(--accent-cyan), var(--accent-blue));
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            padding: 1.2rem 2.5rem;
            border-radius: 15px;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(0, 255, 255, 0.3);
            position: relative;
            overflow: hidden;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0, 255, 255, 0.4);
            color: var(--primary-blue);
            text-decoration: none;
        }

        /* Footer */
        footer {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 2rem;
            margin-top: 3rem;
            text-align: center;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
        }

        footer p {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        /* Quick Stats */
        .quick-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(59, 130, 246, 0.2);
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            border-color: var(--accent-cyan);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--accent-cyan);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.9rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Security Indicators */
        .security-indicators {
            display: flex;
            justify-content: space-around;
            margin-top: 1.5rem;
            gap: 1rem;
            position: relative;
            z-index: 2;
        }

        .security-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }

        .security-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            filter: drop-shadow(0 0 10px rgba(0, 255, 255, 0.5));
        }

        .security-item span {
            font-size: 0.9rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        /* Feature List */
        .feature-list {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-top: 1.5rem;
            position: relative;
            z-index: 2;
        }

        .feature-item {
            background: rgba(0, 255, 255, 0.1);
            border: 1px solid rgba(0, 255, 255, 0.2);
            border-radius: 10px;
            padding: 0.8rem;
            text-align: center;
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--text-primary);
            transition: all 0.3s ease;
        }

        .feature-item:hover {
            background: rgba(0, 255, 255, 0.2);
            border-color: var(--accent-cyan);
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0, 255, 255, 0.3);
        }
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            header h1 {
                font-size: 2rem;
            }

            nav ul {
                gap: 1rem;
            }

            nav li a {
                padding: 0.8rem 1.5rem;
                font-size: 1rem;
            }

            .balance {
                grid-column: span 1;
                padding: 2rem;
            }

            .balance-amount {
                font-size: 3rem;
            }

            main {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            header {
                padding: 1.5rem;
            }

            .balance-amount {
                font-size: 2.5rem;
            }

            .action-card {
                padding: 2rem;
            }

            .btn {
                padding: 1rem 2rem;
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Video Background -->
    <div class="video-background">
        <video autoplay muted loop>
            <source src="your-background-video.mp4" type="video/mp4">
            <source src="your-background-video.webm" type="video/webm">
        </video>
    </div>
    
    <!-- Background Overlay -->
    <div class="bg-overlay"></div>
    
    <!-- Grid Lines -->
    <div class="grid-lines"></div>
    
    <!-- Particles -->
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div class="container">
        <header class="animate__animated animate__fadeInDown">
            <h1>Welcome, <?php echo $_SESSION['name']; ?>!</h1>
        </header>

        <nav class="animate__animated animate__fadeInUp animate__delay-1s">
            <ul>
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
                <li><a href="transactions.php">Transactions</a></li>
                <li><a href="transfer.php">Transfer Funds</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>

        <main>
            <section class="balance animate__animated animate__fadeInUp animate__delay-1s">
                <div class="balance-content">
                    <h2>Account Overview</h2>
                    <div class="balance-amount">LKR <?php echo number_format($balance, 2); ?></div>
                    <div class="quick-stats">
                        <div class="stat-card">
                            <div class="stat-value">Active</div>
                            <div class="stat-label">Account Status</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value">LKR</div>
                            <div class="stat-label">Currency</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value">24/7</div>
                            <div class="stat-label">Support</div>
                        </div>
                    </div>
                    <a class="btn" href="transfer.php">Quick Transfer</a>
                </div>
            </section>

            <div class="action-card animate__animated animate__fadeInUp animate__delay-2s">
                <h3>Account Security</h3>
                <p>Your account is protected with advanced encryption and multi-layer security protocols.</p>
                <div class="security-indicators">
                    <div class="security-item">
                        <div class="security-icon">🔒</div>
                        <span>Encrypted</span>
                    </div>
                    <div class="security-item">
                        <div class="security-icon">✓</div>
                        <span>Verified</span>
                    </div>
                    <div class="security-item">
                        <div class="security-icon">🛡️</div>
                        <span>Protected</span>
                    </div>
                </div>
            </div>

            <div class="action-card animate__animated animate__fadeInUp animate__delay-2s">
                <h3>Digital Banking</h3>
                <p>Experience the future of banking with our cutting-edge digital platform.</p>
                <div class="feature-list">
                    <div class="feature-item">⚡ Instant Transfers</div>
                    <div class="feature-item">🌐 24/7 Access</div>
                    <div class="feature-item">📱 Mobile Ready</div>
                    <div class="feature-item">🔐 Bank-Grade Security</div>
                </div>
            </div>
        </main>

        <footer class="animate__animated animate__fadeInUp animate__delay-3s">
            <p>&copy; 2025 Royal Trust Bank - Digital Banking System. All rights reserved.</p>
        </footer>
    </div>

    <script>
        // Add active state to current page
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('nav a');
            
            navLinks.forEach(link => {
                if (link.getAttribute('href') === currentPath.split('/').pop()) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });
        });

        // Add hover effects to cards
        document.querySelectorAll('.action-card, .stat-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Balance counter animation
        function animateBalance() {
            const balanceElement = document.querySelector('.balance-amount');
            const finalValue = <?php echo $balance; ?>;
            let currentValue = 0;
            const increment = finalValue / 50;
            
            const timer = setInterval(() => {
                currentValue += increment;
                if (currentValue >= finalValue) {
                    currentValue = finalValue;
                    clearInterval(timer);
                }
                balanceElement.textContent = `LKR ${currentValue.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
            }, 50);
        }

        // Start balance animation after page load
        window.addEventListener('load', function() {
            setTimeout(animateBalance, 1500);
        });
    </script>

    <?php include 'footer.php'; ?>
</body>
</html>