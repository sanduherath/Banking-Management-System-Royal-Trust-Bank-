<?php
require 'db.php';
session_start();

// Handle Login Request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role']; // Get role (user/admin) from form
    $username = htmlentities($_POST['username']);
    $password = htmlentities($_POST['password']);

    if ($role === 'user') {
        $stmt = $conn->prepare("SELECT id, name ,status FROM users WHERE name = ? AND password = ? ");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $name, $status);
            $stmt->fetch();

            if ($status == 'active') {
                $_SESSION['user_id'] = $id;
                $_SESSION['name'] = $name;
                header("Location: dashboard.php");
            } else {
                $error = "Currently Inactive";
            }
        } else {
            $error = "Invalid username or password for user!";
        }
    } elseif ($role === 'admin') {
        $stmt = $conn->prepare("SELECT id FROM admins WHERE username = ? AND password = ?");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id);
            $stmt->fetch();
            $_SESSION['admin'] = true;
            header("Location: admin/dashboard.php");
        } else {
            $error = "Invalid username or password for admin!";
        }
    } else {
        $error = "Invalid role selected!";
    }
}
?>

<?php
if (isset($error)) {
    echo "<script type='text/javascript'>
           window.onload = function() {
               alert('$error');
           }
       </script>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Royal Trust Bank | Digital Banking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            --input-bg: rgba(15, 23, 42, 0.6);
            --button-gradient: linear-gradient(135deg, #00ffff, #3b82f6);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--primary-blue);
            min-height: 100vh;
            overflow: hidden;
            position: relative;
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
            background: linear-gradient(135deg, rgba(10, 22, 40, 0.9), rgba(15, 23, 42, 0.85));
            z-index: -1;
        }

        /* Animated Background Elements */
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }

        /* Floating Orbs */
        .floating-element {
            position: absolute;
            border-radius: 50%;
            background: radial-gradient(circle at 30% 30%, rgba(0, 255, 255, 0.6), rgba(59, 130, 246, 0.3), transparent);
            box-shadow:
                0 0 50px rgba(0, 255, 255, 0.4),
                inset 0 0 50px rgba(59, 130, 246, 0.2);
            animation: float 8s ease-in-out infinite;
            filter: blur(1px);
        }

        .floating-element:nth-child(1) {
            width: 300px;
            height: 300px;
            top: -10%;
            left: -5%;
            animation-delay: 0s;
            animation-duration: 12s;
        }

        .floating-element:nth-child(2) {
            width: 200px;
            height: 200px;
            top: 70%;
            right: -5%;
            animation-delay: 3s;
            animation-duration: 10s;
        }

        .floating-element:nth-child(3) {
            width: 150px;
            height: 150px;
            top: 20%;
            right: 15%;
            animation-delay: 6s;
            animation-duration: 14s;
        }

        .floating-element:nth-child(4) {
            width: 100px;
            height: 100px;
            top: 50%;
            left: 80%;
            animation-delay: 2s;
            animation-duration: 9s;
        }

        .floating-element:nth-child(5) {
            width: 120px;
            height: 120px;
            top: 80%;
            left: 20%;
            animation-delay: 5s;
            animation-duration: 11s;
        }

        .floating-element:nth-child(6) {
            width: 80px;
            height: 80px;
            top: 5%;
            left: 60%;
            animation-delay: 4s;
            animation-duration: 13s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) translateX(0px) rotate(0deg) scale(1);
                opacity: 0.6;
            }

            25% {
                transform: translateY(-30px) translateX(20px) rotate(90deg) scale(1.1);
                opacity: 0.9;
            }

            50% {
                transform: translateY(-20px) translateX(-15px) rotate(180deg) scale(0.9);
                opacity: 0.7;
            }

            75% {
                transform: translateY(-40px) translateX(25px) rotate(270deg) scale(1.05);
                opacity: 0.8;
            }
        }

        /* Particle System */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .particle {
            position: absolute;
            width: 2px;
            height: 2px;
            background: rgba(0, 255, 255, 0.8);
            border-radius: 50%;
            animation: particleFloat 15s linear infinite;
            box-shadow: 0 0 10px rgba(0, 255, 255, 0.5);
        }

        .particle:nth-child(odd) {
            background: rgba(59, 130, 246, 0.8);
            box-shadow: 0 0 10px rgba(59, 130, 246, 0.5);
            animation-duration: 20s;
        }

        .particle:nth-child(1) {
            left: 10%;
            animation-delay: 0s;
        }

        .particle:nth-child(2) {
            left: 20%;
            animation-delay: 2s;
        }

        .particle:nth-child(3) {
            left: 30%;
            animation-delay: 4s;
        }

        .particle:nth-child(4) {
            left: 40%;
            animation-delay: 1s;
        }

        .particle:nth-child(5) {
            left: 50%;
            animation-delay: 3s;
        }

        .particle:nth-child(6) {
            left: 60%;
            animation-delay: 5s;
        }

        .particle:nth-child(7) {
            left: 70%;
            animation-delay: 2.5s;
        }

        .particle:nth-child(8) {
            left: 80%;
            animation-delay: 4.5s;
        }

        .particle:nth-child(9) {
            left: 90%;
            animation-delay: 1.5s;
        }

        .particle:nth-child(10) {
            left: 15%;
            animation-delay: 6s;
        }

        .particle:nth-child(11) {
            left: 25%;
            animation-delay: 3.5s;
        }

        .particle:nth-child(12) {
            left: 35%;
            animation-delay: 5.5s;
        }

        @keyframes particleFloat {
            0% {
                bottom: -10px;
                opacity: 1;
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

        /* Circuit Lines */
        .circuit-lines {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .circuit-line {
            position: absolute;
            background: linear-gradient(90deg, transparent, rgba(0, 255, 255, 0.5), transparent);
            height: 1px;
            animation: circuitMove 8s linear infinite;
        }

        .circuit-line:nth-child(1) {
            top: 20%;
            left: -100%;
            width: 200px;
            animation-delay: 0s;
        }

        .circuit-line:nth-child(2) {
            top: 40%;
            left: -100%;
            width: 300px;
            animation-delay: 2s;
        }

        .circuit-line:nth-child(3) {
            top: 60%;
            left: -100%;
            width: 250px;
            animation-delay: 4s;
        }

        .circuit-line:nth-child(4) {
            top: 80%;
            left: -100%;
            width: 180px;
            animation-delay: 1s;
        }

        @keyframes circuitMove {
            0% {
                left: -100%;
                opacity: 0;
            }

            20% {
                opacity: 1;
            }

            80% {
                opacity: 1;
            }

            100% {
                left: 100%;
                opacity: 0;
            }
        }

        /* Pulsing Rings */
        .pulse-ring {
            position: absolute;
            border: 1px solid rgba(0, 255, 255, 0.3);
            border-radius: 50%;
            animation: pulse-expand 6s ease-out infinite;
        }

        .pulse-ring:nth-child(1) {
            top: 15%;
            right: 10%;
            width: 100px;
            height: 100px;
            animation-delay: 0s;
        }

        .pulse-ring:nth-child(2) {
            bottom: 20%;
            left: 15%;
            width: 150px;
            height: 150px;
            animation-delay: 2s;
        }

        .pulse-ring:nth-child(3) {
            top: 50%;
            left: 5%;
            width: 80px;
            height: 80px;
            animation-delay: 4s;
        }

        @keyframes pulse-expand {
            0% {
                transform: scale(0.8);
                opacity: 1;
                border-width: 2px;
            }

            50% {
                transform: scale(1.2);
                opacity: 0.5;
                border-width: 1px;
            }

            100% {
                transform: scale(2);
                opacity: 0;
                border-width: 0px;
            }
        }

        /* Energy Waves */
        .energy-wave {
            position: absolute;
            width: 200%;
            height: 200%;
            top: -50%;
            left: -50%;
            border-radius: 50%;
            border: 1px solid rgba(59, 130, 246, 0.2);
            animation: wave-expand 10s linear infinite;
        }

        .energy-wave:nth-child(1) {
            animation-delay: 0s;
        }

        .energy-wave:nth-child(2) {
            animation-delay: 3s;
            border-color: rgba(0, 255, 255, 0.2);
        }

        .energy-wave:nth-child(3) {
            animation-delay: 6s;
            border-color: rgba(147, 51, 234, 0.2);
        }

        @keyframes wave-expand {
            0% {
                transform: scale(0);
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }

            100% {
                transform: scale(1);
                opacity: 0;
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
                linear-gradient(rgba(59, 130, 246, 0.1) 1px, transparent 1px),
                linear-gradient(90deg, rgba(59, 130, 246, 0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: grid-move 20s linear infinite;
            z-index: 1;
        }

        @keyframes grid-move {
            0% {
                transform: translate(0, 0);
            }

            100% {
                transform: translate(50px, 50px);
            }
        }

        /* Main Container */
        .main-container {
            position: relative;
            z-index: 10;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        /* Login Card */
        .login-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            box-shadow:
                0 25px 50px rgba(0, 0, 0, 0.5),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            width: 100%;
            max-width: 400px;
            padding: 2.5rem;
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(0, 255, 255, 0.1), transparent);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% {
                left: -100%;
            }

            100% {
                left: 100%;
            }
        }

        /* Header */
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
        }

        .logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 1rem;
            background: var(--button-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-blue);
            box-shadow: 0 10px 30px rgba(0, 255, 255, 0.3);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                box-shadow: 0 10px 30px rgba(0, 255, 255, 0.3);
            }

            50% {
                transform: scale(1.05);
                box-shadow: 0 15px 40px rgba(0, 255, 255, 0.5);
            }
        }

        .login-title {
            color: var(--text-primary);
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .login-subtitle {
            color: var(--text-secondary);
            font-size: 0.9rem;
            font-weight: 400;
        }

        /* Form Elements */
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-label {
            display: block;
            color: var(--text-secondary);
            font-size: 0.8rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            width: 100%;
            background: var(--input-bg);
            border: 1px solid rgba(59, 130, 246, 0.2);
            border-radius: 10px;
            padding: 1rem;
            color: var(--text-primary);
            font-size: 0.95rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent-cyan);
            box-shadow: 0 0 20px rgba(0, 255, 255, 0.2);
            background: rgba(15, 23, 42, 0.8);
        }

        .form-control::placeholder {
            color: var(--text-secondary);
            opacity: 0.7;
        }

        .form-select {
            background: var(--input-bg);
            border: 1px solid rgba(59, 130, 246, 0.2);
            border-radius: 10px;
            padding: 1rem;
            color: var(--text-primary);
            font-size: 0.95rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            cursor: pointer;
        }

        .form-select:focus {
            outline: none;
            border-color: var(--accent-cyan);
            box-shadow: 0 0 20px rgba(0, 255, 255, 0.2);
            background: rgba(15, 23, 42, 0.8);
        }

        .form-select option {
            background: var(--primary-blue);
            color: var(--text-primary);
        }

        /* Password Toggle */
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            cursor: pointer;
            font-size: 1.2rem;
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: var(--accent-cyan);
        }

        /* Buttons */
        .btn-login {
            width: 100%;
            background: var(--button-gradient);
            border: none;
            border-radius: 12px;
            padding: 1.2rem;
            color: var(--primary-blue);
            font-size: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(0, 255, 255, 0.3);
            position: relative;
            overflow: hidden;
            margin-bottom: 1rem;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(0, 255, 255, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        /* Forgot Password Link */
        .forgot-link {
            display: block;
            text-align: center;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }

        .forgot-link:hover {
            color: var(--accent-cyan);
            text-decoration: underline;
        }

        /* Error Alert */
        .alert-danger {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            backdrop-filter: blur(10px);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .main-container {
                padding: 1rem;
            }

            .login-card {
                padding: 2rem;
            }

            .login-title {
                font-size: 1.3rem;
            }

            .logo {
                width: 70px;
                height: 70px;
                font-size: 1.8rem;
            }
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 1.5rem;
            }

            .form-control,
            .form-select {
                padding: 0.8rem;
            }

            .btn-login {
                padding: 1rem;
            }
        }

        /* Loading Animation */
        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 0.5rem;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <!-- Video Background -->
    <div class="video-background">
        <video autoplay muted loop>
            <!-- Replace with your video file path -->
            <source src="your-background-video.mp4" type="video/mp4">
            <source src="your-background-video.webm" type="video/webm">
        </video>
    </div>

    <!-- Background Overlay -->
    <div class="bg-overlay"></div>

    <!-- Grid Lines -->
    <div class="grid-lines"></div>

    <!-- Floating Background Elements -->
    <div class="bg-animation">
        <div class="floating-element"></div>
        <div class="floating-element"></div>
        <div class="floating-element"></div>
    </div>

    <!-- Main Container -->
    <div class="main-container">
        <div class="login-card animate__animated animate__fadeInUp">
            <!-- Header -->
            <div class="login-header">
                <div class="logo">RTB</div>
                <p class="login-subtitle">Welcome to Royal Trust Bank</p>
            </div>

            <!-- Error Display -->
            <?php if (isset($error)): ?>
                <div class="alert alert-danger animate__animated animate__shakeX">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="POST" action="" id="loginForm">
                <!-- Username Field -->
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text"
                        class="form-control"
                        id="username"
                        name="username"
                        placeholder="Enter your username"
                        required>
                </div>

                <!-- Password Field -->
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div style="position: relative;">
                        <input type="password"
                            class="form-control"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            required>
                        <span class="password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye" id="passwordIcon"></i>
                        </span>
                    </div>
                </div>

                <!-- Role Selection -->
                <div class="form-group">
                    <label for="role" class="form-label">User Type</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="user">User</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>

                <!-- Login Button -->
                <button type="submit" class="btn-login" id="loginBtn">
                    <span class="loading-spinner"></span>
                    <span class="btn-text">Login</span>
                </button>

                <!-- Forgot Password Link -->
                <a href="test.html" class="forgot-link">Forgot your password?</a>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>

    <script>
        // Password Toggle Function
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        }

        // Form Submission with Loading Animation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const loginBtn = document.getElementById('loginBtn');
            const spinner = loginBtn.querySelector('.loading-spinner');
            const btnText = loginBtn.querySelector('.btn-text');

            // Show loading state
            spinner.style.display = 'inline-block';
            btnText.textContent = 'Logging in...';
            loginBtn.disabled = true;
        });

        // Input Focus Effects
        document.querySelectorAll('.form-control, .form-select').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });

            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
            });
        });

        // Typing Animation for Title (Optional)
        function typeWriter(text, element, speed = 100) {
            let i = 0;
            element.innerHTML = '';

            function type() {
                if (i < text.length) {
                    element.innerHTML += text.charAt(i);
                    i++;
                    setTimeout(type, speed);
                }
            }
            type();
        }

        // Initialize typing animation on page load
        window.addEventListener('load', function() {
            const title = document.querySelector('.login-title');
            if (title) {
                typeWriter('用户登录', title, 150);
            }
        });
    </script>
</body>

</html>