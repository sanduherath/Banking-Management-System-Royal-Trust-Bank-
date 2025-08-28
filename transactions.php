<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Check if we should show all transactions or just the latest 6
$show_all = isset($_GET['show']) && $_GET['show'] == 'all';

// Fetch transaction history with limit if not showing all
if ($show_all) {
    $stmt = $conn->prepare("SELECT type, amount, timestamp FROM transactions WHERE user_id = ? ORDER BY timestamp DESC");
} else {
    $stmt = $conn->prepare("SELECT type, amount, timestamp FROM transactions WHERE user_id = ? ORDER BY timestamp DESC LIMIT 6");
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Get total count of transactions
$count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM transactions WHERE user_id = ?");
$count_stmt->bind_param("i", $user_id);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_count = $count_result->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History - Royal Trust Bank</title>
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
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        /* Transaction Table */
        .transaction-table {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
        }

        .transaction-table::before {
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

        .transaction-table-content {
            position: relative;
            z-index: 2;
        }

        .transaction-table h2 {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 2rem;
            color: var(--text-primary);
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
        }

        th, td {
            padding: 1.2rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        th {
            background: rgba(0, 255, 255, 0.1);
            color: var(--accent-cyan);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tr:hover {
            background: rgba(0, 255, 255, 0.05);
        }

        .transaction-type {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .transaction-type.deposit {
            background: rgba(16, 185, 129, 0.2);
            color: var(--success-green);
        }

        .transaction-type.withdrawal {
            background: rgba(245, 158, 11, 0.2);
            color: var(--warning-orange);
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
            border: none;
            cursor: pointer;
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

        .btn-outline {
            background: transparent;
            border: 2px solid var(--accent-cyan);
            color: var(--accent-cyan);
            box-shadow: 0 5px 15px rgba(0, 255, 255, 0.2);
        }

        .btn-outline:hover {
            background: rgba(0, 255, 255, 0.1);
            color: var(--accent-cyan);
            box-shadow: 0 10px 25px rgba(0, 255, 255, 0.3);
        }

        .btn-container {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
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

        .transaction-count {
            text-align: center;
            margin-bottom: 1.5rem;
            color: var(--text-secondary);
            font-size: 1.1rem;
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

            .transaction-table {
                padding: 1.5rem;
            }

            th, td {
                padding: 0.8rem;
            }

            .btn-container {
                flex-direction: column;
                align-items: center;
            }
        }

        @media (max-width: 480px) {
            header {
                padding: 1.5rem;
            }

            .transaction-table {
                padding: 1rem;
                overflow-x: auto;
            }

            table {
                min-width: 500px;
            }

            th, td {
                padding: 0.6rem;
                font-size: 0.9rem;
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
            <h1>Transaction History</h1>
        </header>

        <nav class="animate__animated animate__fadeInUp animate__delay-1s">
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="transactions.php" class="active">Transactions</a></li>
                <li><a href="transfer.php">Transfer Funds</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>

        <main>
            <section class="transaction-table animate__animated animate__fadeInUp animate__delay-1s">
                <div class="transaction-table-content">
                    <h2>Your Recent Transactions</h2>
                    
                    <div class="transaction-count">
                        <?php if ($show_all): ?>
                            Showing all <?php echo $total_count; ?> transactions
                        <?php else: ?>
                            Showing latest 6 of <?php echo $total_count; ?> transactions
                        <?php endif; ?>
                    </div>
                    
                    <table>
                        <tr>
                            <th>Type</th>
                            <th>Amount (LKR)</th>
                            <th>Date</th>
                        </tr>
                        <?php 
                        if ($result->num_rows > 0):
                            while ($row = $result->fetch_assoc()): 
                        ?>
                            <tr>
                                <td>
                                    <span class="transaction-type <?php echo $row['type']; ?>">
                                        <?php echo ucfirst($row['type']); ?>
                                    </span>
                                </td>
                                <td><?php echo number_format($row['amount'], 2); ?></td>
                                <td><?php echo $row['timestamp']; ?></td>
                            </tr>
                        <?php 
                            endwhile;
                        else: 
                        ?>
                            <tr>
                                <td colspan="3" style="text-align: center;">No transactions found</td>
                            </tr>
                        <?php endif; ?>
                    </table>
                    
                    <div class="btn-container">
                        <a class="btn" href="dashboard.php">Back to Dashboard</a>
                        
                        <?php if ($show_all): ?>
                            <a class="btn btn-outline" href="transactions.php">Show Less</a>
                        <?php else: ?>
                            <a class="btn btn-outline" href="transactions.php?show=all">View All Transactions</a>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
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

        // Add hover effects to table rows
        document.querySelectorAll('tr').forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.transform = 'translateX(5px)';
                this.style.transition = 'transform 0.3s ease';
            });
            
            row.addEventListener('mouseleave', function() {
                this.style.transform = 'translateX(0)';
            });
        });
    </script>
</body>
</html>