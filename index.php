<?php
require 'db.php';
session_start();

// Handle Login Request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $role = $_POST['role']; // Get role (user/admin) from form
  $username = htmlentities($_POST['username']);
  $password = htmlentities($_POST['password']);

  if ($role === 'user') {
    // User Login
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

    // if($status='active'){
    //         if ($stmt->num_rows > 0) {
    //             $stmt->bind_result($id, $name);
    //             $stmt->fetch();
    //             $_SESSION['user_id'] = $id;
    //             $_SESSION['name'] = $name;
    //             header("Location: dashboard.php");
    //         } else {
    //             $error = "Invalid username or password for user!";
    //         }}
    //         else{
    //             $error = "Currently Inactive";
    //         }
  } elseif ($role === 'admin') {
    // Admin Login
    $stmt = $conn->prepare("SELECT id FROM admins WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
      $stmt->bind_result($id);
      $stmt->fetch();
      $_SESSION['admin'] = true;
      header("Location: admin\dashboard.php");
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
  // echo "<p style='color: red;'>$error</p>";
  // echo "<script type='text/javascript'>alert('$error');</script>";
  echo "<script type='text/javascript'>
           window.onload = function() {
               alert('$error');
           }
       </script>";
}
?>

<!-- <form method="POST" action="">
    
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <select name="role" required>
        <option value="user">User</option>
        <option value="admin">Admin</option>
    </select><br>
    
    <button type="submit">Login</button>
</form> -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Royal Trust Bank | Digital Banking</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        :root {
            --primary: #0a2540;
            --secondary: #00d4ff;
            --accent: #7b61ff;
            --light: #f6f9fc;
            --dark: #0a2540;
        }
        
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--light);
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
        }
        
        .login-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .brand-section {
            background: linear-gradient(135deg, var(--primary), var(--dark));
            border-radius: 20px 0 0 20px;
            position: relative;
            overflow: hidden;
        }
        
        .brand-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(123,97,255,0.1) 0%, rgba(123,97,255,0) 70%);
            animation: rotate 20s linear infinite;
        }
        
        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .brand-content {
            position: relative;
            z-index: 2;
        }
        
        .logo {
            width: 80px;
            margin-bottom: 1.5rem;
        }
        
        .form-section {
            padding: 3rem;
        }
        
        .form-title {
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 2rem;
            position: relative;
        }
        
        .form-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 50px;
            height: 4px;
            background: linear-gradient(90deg, var(--secondary), var(--accent));
            border-radius: 2px;
        }
        
        .form-control {
            border: none;
            border-bottom: 2px solid #e0e0e0;
            border-radius: 0;
            padding-left: 0;
            padding-right: 0;
            background: transparent;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            box-shadow: none;
            border-bottom-color: var(--accent);
        }
        
        .input-icon {
            position: absolute;
            right: 0;
            bottom: 10px;
            color: #aaa;
        }
        
        .btn-login {
            background: linear-gradient(90deg, var(--secondary), var(--accent));
            border: none;
            border-radius: 50px;
            padding: 12px 24px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            width: 100%;
            margin-top: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(123, 97, 255, 0.3);
        }
        
        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(123, 97, 255, 0.4);
        }
        
        .forgot-link {
            color: var(--accent);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }
        
        .forgot-link:hover {
            color: var(--secondary);
            text-decoration: underline;
        }
        
        .role-select {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1rem;
        }
        
        .floating-label {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        .floating-label label {
            position: absolute;
            top: 0;
            left: 0;
            color: #999;
            pointer-events: none;
            transition: all 0.3s ease;
        }
        
        .floating-label input:focus + label,
        .floating-label input:not(:placeholder-shown) + label {
            transform: translateY(-20px);
            font-size: 0.8rem;
            color: var(--accent);
        }
        
        .particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }
        
        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            animation: float 15s infinite linear;
        }
        
        @keyframes float {
            0% { transform: translateY(0) rotate(0deg); opacity: 1; }
            100% { transform: translateY(-1000px) rotate(720deg); opacity: 0; }
        }
        
        @media (max-width: 992px) {
            .brand-section {
                border-radius: 20px 20px 0 0;
                padding: 2rem !important;
            }
            
            .form-section {
                border-radius: 0 0 20px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center py-5">
        <div class="login-container glass-card overflow-hidden">
            <div class="row g-0">
                <!-- Brand Section -->
                <div class="col-lg-5 brand-section d-flex align-items-center justify-content-center p-5 text-white">
                    <div class="brand-content text-center">
                        <img src="img/Royal Trust Bank (1).png" alt="Royal Trust Bank" class="logo animate__animated animate__fadeIn">
                        <h1 class="animate__animated animate__fadeIn animate__delay-1s">Royal Trust Bank</h1>
                        <p class="animate__animated animate__fadeIn animate__delay-2s">Seamless Banking for a Digital World</p>
                        
                        <!-- Animated particles -->
                        <div class="particles">
                            <div class="particle" style="width: 5px; height: 5px; left: 10%; animation-delay: 0s;"></div>
                            <div class="particle" style="width: 7px; height: 7px; left: 20%; animation-delay: 2s;"></div>
                            <div class="particle" style="width: 4px; height: 4px; left: 35%; animation-delay: 4s;"></div>
                            <div class="particle" style="width: 6px; height: 6px; left: 50%; animation-delay: 6s;"></div>
                            <div class="particle" style="width: 5px; height: 5px; left: 65%; animation-delay: 8s;"></div>
                            <div class="particle" style="width: 8px; height: 8px; left: 80%; animation-delay: 10s;"></div>
                            <div class="particle" style="width: 6px; height: 6px; left: 90%; animation-delay: 12s;"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Form Section -->
                <div class="col-lg-7 form-section">
                    <h2 class="form-title animate__animated animate__fadeIn">Login to Your Account</h2>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger animate__animated animate__shakeX">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" class="animate__animated animate__fadeIn animate__delay-1s">
                        <div class="floating-label">
                            <input type="text" class="form-control" id="username" name="username" placeholder=" " required>
                            <label for="username"><i class="fas fa-user me-2"></i>Username</label>
                            <i class="fas fa-user-check input-icon"></i>
                        </div>
                        
                        <div class="floating-label">
                            <input type="password" class="form-control" id="password" name="password" placeholder=" " required>
                            <label for="password"><i class="fas fa-lock me-2"></i>Password</label>
                            <i class="fas fa-eye input-icon" style="cursor: pointer;" onclick="togglePassword()"></i>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <a href="test.html" class="forgot-link">
                                <i class="fas fa-key me-1"></i>Forgot Password?
                            </a>
                        </div>
                        
                        <div class="mb-4">
                            <label for="role" class="form-label">Select Your Role</label>
                            <select class="form-select role-select" id="role" name="role" required>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-login">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    
    <script>
        // Toggle password visibility
        function togglePassword() {
            const password = document.getElementById('password');
            const icon = document.querySelector('.fa-eye');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        
        // Create additional particles
        document.addEventListener('DOMContentLoaded', function() {
            const particlesContainer = document.querySelector('.particles');
            for (let i = 0; i < 15; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.width = `${Math.random() * 5 + 3}px`;
                particle.style.height = particle.style.width;
                particle.style.left = `${Math.random() * 100}%`;
                particle.style.animationDelay = `${Math.random() * 15}s`;
                particle.style.animationDuration = `${Math.random() * 15 + 10}s`;
                particlesContainer.appendChild(particle);
            }
        });
    </script>
</body>
</html>