<?php
require '../db.php';
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: ../index.php");
    exit;
}

// Handle user deletion
if (isset($_GET['delete'])) {
  $user_id = intval($_GET['delete']);
  $stmt = $conn->prepare("UPDATE users SET status = 'inactive' WHERE accno = ?");
  $stmt->bind_param("i", $user_id);
  if ($stmt->execute()) {
      $success_message = "User deactivated successfully!";
  } else {
      $error_message = "Error: " . $conn->error;
  }
}

// Fetch all active users
$result = $conn->query("SELECT accno, name, email, balance, status FROM users WHERE status = 'active'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Royal Trust Bank Admin</title>
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
            height: fit-content;
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

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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
            box-shadow: 0 5px 15px rgba(0, 255, 255, 0.2);
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

        .admin-card.active a {
            background: rgba(0, 255, 255, 0.1);
            color: var(--accent-cyan);
        }

        .admin-card a:hover {
            background: rgba(0, 255, 255, 0.1);
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
        .manage-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .manage-header h2 {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--accent-cyan), var(--accent-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
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

        .manage-header p {
            color: var(--text-secondary);
            font-size: 1.1rem;
        }

        /* Alerts */
        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            backdrop-filter: blur(10px);
            font-weight: 500;
            text-align: center;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #6ee7b7;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }

        /* Table Container */
        .table-container {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            padding: 1.5rem;
            overflow-x: auto;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        /* Users Table */
        .users-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        .users-table th {
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-cyan));
            color: var(--primary-blue);
            padding: 1rem;
            text-align: center;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
        }

        .users-table th:first-child {
            border-top-left-radius: 10px;
        }

        .users-table th:last-child {
            border-top-right-radius: 10px;
        }

        .users-table td {
            padding: 1rem;
            text-align: center;
            color: var(--text-primary);
            border-bottom: 1px solid rgba(59, 130, 246, 0.2);
            vertical-align: middle;
        }

        .users-table tbody tr {
            transition: all 0.3s ease;
        }

        .users-table tbody tr:hover {
            background: rgba(0, 255, 255, 0.05);
            transform: scale(1.01);
        }

        .users-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Action Buttons */
        .action-btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            margin: 0 0.25rem;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            border: 1px solid;
        }

        .btn-edit {
            background: linear-gradient(135deg, var(--success-green), #16d97f);
            color: white;
            border-color: var(--success-green);
        }

        .btn-edit:hover {
            background: linear-gradient(135deg, #16d97f, var(--success-green));
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3);
        }

        .btn-deactivate {
            background: linear-gradient(135deg, var(--danger-red), #f87171);
            color: white;
            border-color: var(--danger-red);
        }

        .btn-deactivate:hover {
            background: linear-gradient(135deg, #f87171, var(--danger-red));
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(239, 68, 68, 0.3);
        }

        /* Balance Display */
        .balance-display {
            font-weight: 600;
            color: var(--accent-cyan);
            font-size: 1.1rem;
        }

        /* Account Number */
        .account-number {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: var(--accent-blue);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--text-secondary);
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: var(--accent-cyan);
        }

        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
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
            
            .manage-header h2 {
                font-size: 2rem;
            }
            
            .users-table th,
            .users-table td {
                padding: 0.75rem 0.5rem;
                font-size: 0.9rem;
            }
            
            .action-btn {
                padding: 0.4rem 0.8rem;
                font-size: 0.8rem;
                margin: 0.1rem;
            }
        }

        @media (max-width: 480px) {
            .admin-sidebar, .admin-main {
                padding: 1rem;
            }
            
            .manage-header h2 {
                font-size: 1.8rem;
            }
            
            .users-table {
                font-size: 0.8rem;
            }
            
            .action-btn {
                display: block;
                margin: 0.25rem 0;
                width: 100%;
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
                
                <div class="admin-card active animate__animated animate__fadeInLeft animate__delay-1s">
                    <a href="manage_users.php"><i class="fas fa-users-cog"></i> Manage Users</a>
                </div>
                
                <div class="admin-card animate__animated animate__fadeInLeft animate__delay-1s">
                    <a href="deposit_fund.php"><i class="fas fa-money-bill-wave"></i> Deposit Fund</a>
                </div>
                
                <div class="admin-card animate__animated animate__fadeInLeft animate__delay-1s">
                    <a href="transactions.php"><i class="fas fa-exchange-alt"></i> View Transactions</a>
                </div>
                
                <div class="admin-card animate__animated animate__fadeInLeft animate__delay-1s">
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
                
                <div class="admin-footer">
                    <p>&copy; 2024 Royal Trust Bank. All rights reserved.</p>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="admin-main animate__animated animate__fadeInRight">
            <div class="admin-main-content">
                <!-- Header -->
                <div class="manage-header">
                    <h2>Manage Users</h2>
                    <p>View and manage all active users in the system</p>
                </div>

                <!-- Success/Error Messages -->
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success animate__animated animate__bounceIn">
                        <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger animate__animated animate__shakeX">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <!-- Users Table -->
                <div class="table-container animate__animated animate__fadeInUp animate__delay-1s">
                    <?php if ($result && $result->num_rows > 0): ?>
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-hashtag"></i> Account No.</th>
                                    <th><i class="fas fa-user"></i> Name</th>
                                    <th><i class="fas fa-envelope"></i> Email</th>
                                    <th><i class="fas fa-wallet"></i> Balance</th>
                                    <th><i class="fas fa-cogs"></i> Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><span class="account-number"><?php echo htmlspecialchars($row['accno']); ?></span></td>
                                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td><span class="balance-display">LKR <?php echo number_format($row['balance'], 2); ?></span></td>
                                        <td>
                                            <a href="edit_user.php?accno=<?php echo $row['accno']; ?>" class="action-btn btn-edit">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="?delete=<?php echo $row['accno']; ?>" class="action-btn btn-deactivate" onclick="return confirm('Are you sure you want to deactivate this user?')">
                                                <i class="fas fa-user-times"></i> Deactivate
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-users-slash"></i>
                            <h3>No Active Users Found</h3>
                            <p>There are currently no active users in the system.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Add hover effects to cards
        document.querySelectorAll('.admin-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                if (!this.classList.contains('active')) {
                    this.style.transform = 'translateY(-5px)';
                }
            });
            
            card.addEventListener('mouseleave', function() {
                if (!this.classList.contains('active')) {
                    this.style.transform = 'translateY(0)';
                }
            });
        });

        // Auto-hide success/error messages after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-20px)';
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.parentNode.removeChild(alert);
                    }
                }, 300);
            });
        }, 5000);

        // Add click animation to action buttons
        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                if (this.classList.contains('btn-deactivate')) {
                    if (!confirm('Are you sure you want to deactivate this user? This action will set their status to inactive.')) {
                        e.preventDefault();
                    }
                }
            });
        });

        // Table row hover effect
        document.querySelectorAll('.users-table tbody tr').forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.background = 'rgba(0, 255, 255, 0.05)';
            });
            
            row.addEventListener('mouseleave', function() {
                this.style.background = 'transparent';
            });
        });
    </script>
</body>
</html>