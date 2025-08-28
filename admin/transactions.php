<?php
require '../db.php';
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}

// Handle form submission for date range filter
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-d');
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : date('Y-m-d');

// Fetch transactions based on the selected date range
$stmt = $conn->prepare("SELECT t.id, u.name, t.type, t.amount, t.timestamp 
                        FROM transactions t
                        JOIN users u ON t.user_id = u.id
                        WHERE t.timestamp BETWEEN ? AND ?
                        ORDER BY t.timestamp DESC");
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions by Time Period - Royal Trust Bank</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            --danger-red: #ef4444;
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
        .admin-container {
            position: relative;
            z-index: 10;
            min-height: 100vh;
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
            display: flex;
            gap: 2rem;
        }

        /* Sidebar */
        .admin-sidebar {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 2rem;
            width: 350px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
        }

        .admin-sidebar::before {
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

        .admin-sidebar-content {
            position: relative;
            z-index: 2;
        }

        .admin-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--glass-border);
        }

        .admin-header h1 {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--accent-cyan), var(--accent-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .admin-header p {
            color: var(--text-secondary);
            font-size: 1rem;
        }

        /* Admin Cards */
        .admin-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            padding: 4px;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
            text-align: center;
            width: 290px;
        }

        .admin-card:hover {
            transform: translateY(-5px);
            border-color: var(--accent-cyan);
            box-shadow: 0 10px 25px rgba(0, 255, 255, 0.3);
        }

        .admin-card.active {
            border-color: var(--accent-cyan);
            box-shadow: 0 10px 25px rgba(0, 255, 255, 0.3);
        }

        .admin-card a {
            color: var(--text-primary);
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            display: block;
            padding: 1rem;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .admin-card a:hover {
            background: rgba(0, 255, 255, 0.1);
            color: var(--accent-cyan);
        }

        .admin-card.active a {
            background: rgba(0, 255, 255, 0.2);
            color: var(--accent-cyan);
        }

        .admin-card i {
            margin-right: 0.5rem;
            font-size: 1.2rem;
        }

        .admin-footer {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--glass-border);
            text-align: center;
        }

        .admin-footer p {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        /* Main Content */
        .admin-main {
            flex: 1;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
        }

        .admin-main::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
            z-index: 1;
        }

        .admin-main-content {
            position: relative;
            z-index: 2;
        }

        /* Header */
        .page-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--glass-border);
        }

        .page-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--accent-cyan), var(--accent-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .page-header .subtitle {
            color: var(--text-secondary);
            font-size: 1.1rem;
        }

        /* Form Styling */
        .filter-form {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        .filter-form .form-row {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 2rem;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }

        .date-input-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }

        .date-input-group label {
            color: var(--accent-cyan);
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filter-form input[type="date"] {
            padding: 12px 15px;
            font-size: 16px;
            border: 1px solid var(--glass-border);
            border-radius: 10px;
            background: rgba(15, 23, 42, 0.7);
            color: var(--text-primary);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .filter-form input[type="date"]:focus {
            outline: none;
            border-color: var(--accent-cyan);
            box-shadow: 0 0 15px rgba(0, 255, 255, 0.3);
        }

        .filter-form input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
        }

        .search-btn {
            background: linear-gradient(135deg, var(--accent-cyan), var(--accent-blue));
            color: var(--primary-blue);
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 5px 15px rgba(0, 255, 255, 0.3);
        }

        .search-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 255, 255, 0.4);
        }

        /* Table Styling */
        .table-container {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .transactions-table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 10px;
            overflow: hidden;
        }

        .transactions-table th {
            background: linear-gradient(135deg, var(--accent-cyan), var(--accent-blue));
            color: var(--primary-blue);
            padding: 15px;
            text-align: center;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.9rem;
        }

        .transactions-table td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid rgba(59, 130, 246, 0.2);
            color: var(--text-primary);
            font-weight: 500;
        }

        .transactions-table tbody tr {
            background: rgba(15, 23, 42, 0.4);
            transition: all 0.3s ease;
        }

        .transactions-table tbody tr:nth-child(even) {
            background: rgba(15, 23, 42, 0.6);
        }

        .transactions-table tbody tr:hover {
            background: rgba(0, 255, 255, 0.1);
            transform: scale(1.01);
            box-shadow: 0 5px 15px rgba(0, 255, 255, 0.2);
        }

        .transaction-id {
            font-family: 'Monaco', 'Courier New', monospace;
            color: var(--accent-cyan);
            font-weight: 600;
        }

        .transaction-type {
            text-transform: capitalize;
            font-weight: 600;
        }

        .transaction-type.deposit {
            color: var(--success-green);
        }

        .transaction-type.withdrawal {
            color: var(--warning-orange);
        }

        .transaction-amount {
            font-weight: 700;
            color: var(--accent-cyan);
        }

        /* Back Button */
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            background: linear-gradient(135deg, var(--accent-blue), var(--secondary-blue));
            color: var(--text-primary);
            padding: 12px 25px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
        }

        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
        }

        .back-btn i {
            font-size: 1rem;
        }

        /* No Data Message */
        .no-data {
            text-align: center;
            padding: 3rem;
            color: var(--text-secondary);
            font-size: 1.2rem;
        }

        .no-data i {
            font-size: 3rem;
            color: var(--accent-cyan);
            margin-bottom: 1rem;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .admin-container {
                flex-direction: column;
            }
            
            .admin-sidebar {
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            .admin-container {
                padding: 1rem;
            }
            
            .admin-sidebar, .admin-main {
                padding: 1.5rem;
            }
            
            .page-header h1 {
                font-size: 2rem;
            }
            
            .filter-form .form-row {
                flex-direction: column;
                gap: 1rem;
            }
            
            .transactions-table {
                font-size: 0.85rem;
            }
            
            .transactions-table th,
            .transactions-table td {
                padding: 10px 8px;
            }
        }

        @media (max-width: 480px) {
            .page-header h1 {
                font-size: 1.8rem;
            }
            
            .transactions-table th,
            .transactions-table td {
                padding: 8px 5px;
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <!-- Video Background -->
    <div class="video-background">
        <video autoplay muted loop>
            <source src="https://assets.mixkit.co/videos/preview/mixkit-abstract-digital-waves-9885-large.mp4" type="video/mp4">
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

    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar animate__animated animate__fadeInLeft">
            <div class="admin-sidebar-content">
                <div class="admin-header">
                    <h1>Admin Dashboard</h1>
                    <p>Royal Trust Bank</p>
                </div>
                <div class="admin-card animate__animated animate__fadeInLeft animate__delay-1s">
                    <a href="dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a>
                </div>
                <div class="admin-card animate__animated animate__fadeInLeft animate__delay-1s">
                    <a href="register_user.php"><i class="fas fa-user-plus"></i> Register New User</a>
                </div>
                
                <div class="admin-card animate__animated animate__fadeInLeft animate__delay-1s">
                    <a href="manage_users.php"><i class="fas fa-users-cog"></i> Manage Users</a>
                </div>
                
                <div class="admin-card animate__animated animate__fadeInLeft animate__delay-1s">
                    <a href="deposit_fund.php"><i class="fas fa-money-bill-wave"></i> Deposit Fund</a>
                </div>
                
                <div class="admin-card active animate__animated animate__fadeInLeft animate__delay-1s">
                    <a href="transactions.php"><i class="fas fa-exchange-alt"></i> View Transactions</a>
                </div>
                
                <div class="admin-card animate__animated animate__fadeInLeft animate__delay-1s">
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
                
                <div class="admin-footer">
                    <p>&copy; 2025 Royal Trust Bank. All rights reserved.</p>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="admin-main animate__animated animate__fadeInRight">
            <div class="admin-main-content">
                <div class="page-header">
                    <h1><i class="fas fa-chart-line"></i> Transactions Report</h1>
                    <p class="subtitle">View and filter transactions by date range</p>
                </div>

                <!-- Date Range Filter Form -->
                <div class="filter-form animate__animated animate__fadeInUp animate__delay-1s">
                    <form method="POST" action="">
                        <div class="form-row">
                            <div class="date-input-group">
                                <label for="start_date">Start Date</label>
                                <input type="date" id="start_date" name="start_date" value="<?= $start_date ?>" required>
                            </div>
                            <div class="date-input-group">
                                <label for="end_date">End Date</label>
                                <input type="date" id="end_date" name="end_date" value="<?= $end_date ?>" required>
                            </div>
                        </div>
                        <button type="submit" class="search-btn">
                            <i class="fas fa-search"></i> Search Transactions
                        </button>
                    </form>
                </div>

                <!-- Transactions Table -->
                <div class="table-container animate__animated animate__fadeInUp animate__delay-1.5s">
                    <?php if ($result->num_rows > 0): ?>
                        <table class="transactions-table">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-hashtag"></i> Transaction ID</th>
                                    <th><i class="fas fa-user"></i> User Name</th>
                                    <th><i class="fas fa-tag"></i> Type</th>
                                    <th><i class="fas fa-dollar-sign"></i> Amount</th>
                                    <th><i class="fas fa-calendar"></i> Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()) : ?>
                                    <tr>
                                        <td class="transaction-id">#<?= $row['id'] ?></td>
                                        <td><?= htmlspecialchars($row['name']) ?></td>
                                        <td class="transaction-type <?= strtolower($row['type']) ?>"><?= ucfirst($row['type']) ?></td>
                                        <td class="transaction-amount">LKR <?= number_format($row['amount'], 2) ?></td>
                                        <td><?= date('M d, Y - H:i', strtotime($row['timestamp'])) ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="no-data">
                            <i class="fas fa-search"></i>
                            <p>No transactions found for the selected date range.</p>
                            <p style="margin-top: 0.5rem; font-size: 1rem;">Try adjusting your search criteria.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="animate__animated animate__fadeInUp animate__delay-2s" style="text-align: center;">
                    <a href="dashboard.php" class="back-btn">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Add smooth scrolling and enhanced interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Add loading animation to form submission
            const form = document.querySelector('form');
            const searchBtn = document.querySelector('.search-btn');
            
            form.addEventListener('submit', function() {
                searchBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...';
                searchBtn.disabled = true;
            });

            // Add hover effects to cards
            document.querySelectorAll('.admin-card, .stat-card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });

            // Set active state for transactions page
            const transactionsCard = document.querySelector('.admin-card.active');
            if (transactionsCard) {
                transactionsCard.style.borderColor = 'var(--accent-cyan)';
                transactionsCard.style.boxShadow = '0 10px 25px rgba(0, 255, 255, 0.3)';
            }

            // Auto-focus on first date input
            document.getElementById('start_date').focus();
        });
    </script>
</body>
</html>