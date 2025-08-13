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
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
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
    background: linear-gradient(135deg, #e6f0ff, #f6f9fc);
    min-height: 100vh;
    overflow-x: hidden;
}

.glass-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 25px;
    border: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    max-width: 1100px;
    width: 90%;
}

.brand-section {
    background: linear-gradient(135deg, var(--primary), var(--dark));
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    padding: 3rem;
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
    background: radial-gradient(circle, rgba(123,97,255,0.15) 0%, rgba(123,97,255,0) 70%);
    animation: rotate 30s linear infinite;
}

@keyframes rotate {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Enhanced Bubble Animation Styles */
.bubble-container {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    overflow: hidden;
    z-index: 1;
}

.bubble {
    position: absolute;
    bottom: -100px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    animation: float 15s infinite linear;
    box-shadow: 
        0 0 10px rgba(255, 255, 255, 0.3),
        inset 0 0 5px rgba(255, 255, 255, 0.5);
    opacity: 0.7;
    filter: blur(1px);
}

.bubble:nth-child(1) {
    width: 20px;
    height: 20px;
    left: 10%;
    animation-delay: 0s;
    animation-duration: 18s;
}

.bubble:nth-child(2) {
    width: 35px;
    height: 35px;
    left: 25%;
    animation-delay: 2s;
    animation-duration: 22s;
}

.bubble:nth-child(3) {
    width: 15px;
    height: 15px;
    left: 45%;
    animation-delay: 4s;
    animation-duration: 16s;
}

.bubble:nth-child(4) {
    width: 25px;
    height: 25px;
    left: 65%;
    animation-delay: 1s;
    animation-duration: 20s;
}

.bubble:nth-child(5) {
    width: 30px;
    height: 30px;
    left: 85%;
    animation-delay: 3s;
    animation-duration: 24s;
}

.bubble:nth-child(6) {
    width: 18px;
    height: 18px;
    left: 30%;
    animation-delay: 5s;
    animation-duration: 17s;
}

.bubble:nth-child(7) {
    width: 22px;
    height: 22px;
    left: 50%;
    animation-delay: 1.5s;
    animation-duration: 19s;
}

.bubble:nth-child(8) {
    width: 28px;
    height: 28px;
    left: 70%;
    animation-delay: 3.5s;
    animation-duration: 21s;
}

@keyframes float {
    0% {
        bottom: -100px;
        transform: translateX(0) rotate(0deg);
        opacity: 0.7;
    }
    50% {
        transform: translateX(50px) rotate(180deg);
        opacity: 1;
    }
    100% {
        bottom: 100%;
        transform: translateX(-50px) rotate(360deg);
        opacity: 0;
    }
}

.brand-content {
    position: relative;
    z-index: 2;
    text-align: center;
}

.logo {
    width: 100px;
    margin-bottom: 1.5rem;
}

.form-section {
    padding: 4rem 3rem;
    position: relative;
}

.form-title {
    font-size: 2rem;
    font-weight: 700;
    color: var(--primary);
    margin-bottom: 2rem;
    position: relative;
}

.form-title::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 0;
    width: 60px;
    height: 4px;
    background: linear-gradient(90deg, var(--secondary), var(--accent));
    border-radius: 2px;
}

.form-control {
    border: none;
    border-bottom: 2px solid #ccc;
    border-radius: 0;
    padding: 10px 0;
    margin-bottom: 1.8rem;
    background: transparent;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-bottom-color: var(--accent);
    box-shadow: none;
}

.floating-label {
    position: relative;
}

.floating-label label {
    position: absolute;
    top: 50%;
    left: 0;
    transform: translateY(-50%);
    color: #999;
    pointer-events: none;
    transition: 0.3s ease all;
    font-weight: 500;
}

.floating-label input:focus + label,
.floating-label input:not(:placeholder-shown) + label {
    top: -10px;
    font-size: 0.8rem;
    color: var(--accent);
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
    padding: 14px 0;
    font-weight: 600;
    letter-spacing: 0.5px;
    width: 100%;
    color: #fff;
    transition: all 0.3s ease;
    box-shadow: 0 6px 20px rgba(123, 97, 255, 0.3);
}

.btn-login:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 30px rgba(123, 97, 255, 0.4);
}

.forgot-link {
    color: var(--accent);
    font-size: 0.9rem;
    text-decoration: none;
    transition: 0.3s;
}

.forgot-link:hover {
    color: var(--secondary);
    text-decoration: underline;
}

.role-select {
    appearance: none;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    background-size: 1rem;
}

@media (max-width: 992px) {
    .brand-section {
        border-radius: 25px 25px 0 0;
        padding: 2rem;
    }
    .form-section {
        border-radius: 0 0 25px 25px;
        padding: 3rem 2rem;
    }
}
</style>
</head>
<body>
<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center py-5">
    <div class="glass-card">
        <div class="row g-0">
            <!-- Brand Section with Enhanced Bubbles -->
            <div class="col-lg-5 brand-section">
                <div class="bubble-container">
                    <div class="bubble"></div>
                    <div class="bubble"></div>
                    <div class="bubble"></div>
                    <div class="bubble"></div>
                    <div class="bubble"></div>
                    <div class="bubble"></div>
                    <div class="bubble"></div>
                    <div class="bubble"></div>
                </div>
                <div class="brand-content">
                    <img src="img/Royal Trust Bank (1).png" alt="Royal Trust Bank" class="logo">
                    <h1>Royal Trust Bank</h1>
                    <p>Seamless Banking for a Digital World</p>
                </div>
            </div>

            <!-- Form Section -->
            <div class="col-lg-7 form-section">
                <h2 class="form-title">Login to Your Account</h2>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
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
                        <a href="test.html" class="forgot-link"><i class="fas fa-key me-1"></i>Forgot Password?</a>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
<script>
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
</script>
</body>
</html>