

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard</title>
    <style>
      body {
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f4f6f8;
        color: #333;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
        
      }
      .container1 {
        background-color: #000000;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 30px;
        width: 20%;
        height: 700px;
        max-width: 900px;
        text-align: center;
        margin: 20px;
      }
      .container2 {
        background-color: #000000;
        border-radius: 10px;
        box-shadow: 2px 4px 5px rgba(0, 0, 0, 0.1);
        padding: 30px;
        padding-top: 0px;
        width: 100%;
        max-width: 900px;
        text-align: center;
        margin: 20px;
        color: #ebf5ff;
        font-family: "Times New Roman", Times, serif;
      }
      .container2 h1 {
        padding-top: 40px;
      }
      .container2 img {
        padding-top: 10px;
        width: 100%;
      }

      .header {
        margin-bottom: 20px;
        padding-bottom: 30px;
      }
      .header h1 {
        color: #6fb2fa;
        font-family: Cambria, Cochin, Georgia, Times, "Times New Roman", serif;
        text-shadow: 0px 1px 2px #1f1e1e;
      }
     
      .header h1 img {
        width: 20%;

        border-radius: 50px;
      }
      .card-container {
        display: block;
        flex-wrap: wrap;
        justify-content: space-around;
      }
      .card {
        background-color: #007bff;
        color: #fff;
        padding: 20px;
        border-radius: 10px;
        margin: 10px;
        width: 200px;
        text-align: center;
        transition: transform 0.3s, background-color 0.3s;
      }
      .card a {
        text-decoration: none;
        color: #fff;
        font-weight: bold;
      }
      .card:hover {
        transform: scale(1.05);
        background-color: #0056b3;
      }
      .footer {
        margin-top: 20px;
        font-size: 0.9em;
        color: #777;
      }
      .main_container {
        display: flex;
        width: 100%;
        margin: 100px;
        margin-top: 0px;
      }
   
    </style>
  </head>
  <body>
    <div class="main_container">
      <div class="container1">
        <div class="header">
          <?php include '../includes/admin_header.php'; ?>

          <h1>Welcome Admin</h1>
        </div>
        <div class="card-container">
          <div class="card">
            <a href="register_user.php">Register New User</a>
          </div>
          <div class="card">
            <a href="manage_users.php">Manage Users</a>
          </div>
          <div class="card">
            <a href="deposit_fund.php">Deposit Fund</a>
          </div>
          <div class="card">
            <a href="transactions.php">View Transactions</a>
          </div>
          <div class="card">
            <a href="logout.php">Logout</a>
          </div>
          <div class="footer">
      <?php include '../includes/admin_footer.php'; ?>
    </div>
        </div>
      </div>
      <div class="container2">
         <?php
        require '../db.php';
        session_start();
        
        if (!isset($_SESSION['admin'])) {
            header("Location: index.php");
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = htmlentities($_POST['fname']);
            $accno = htmlentities($_POST['accno']);
            $address = htmlentities($_POST['address']);
            $email = htmlentities($_POST['email']);
            $nic = htmlentities($_POST['nic']);
            $phone = htmlentities($_POST['phone']);
            $date_of_birth = htmlentities($_POST['date_of_birth']);
            $password = 123; // Default or custom password
            $balance = 0;
            $status = 'active'; // Default status
        
            // Corrected query to include all fields and bind all parameters properly
            $stmt = $conn->prepare("INSERT INTO users (name, accno, address, email, nic, phone, date_of_birth, password, balance, status) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            // Correct the bind_param with the correct types for each parameter
            $stmt->bind_param("ssssssssds", $name, $accno, $address, $email, $nic, $phone, $date_of_birth, $password, $balance, $status);
            if ($stmt->execute()) {
              echo "<script>document.addEventListener('DOMContentLoaded', function() { Swal.fire({title: 'Success', text: 'User registered successfully!', icon: 'success'}).then(() => { window.location.href = 'dashboard.php'; }); });</script>";
          } else {
              echo "<script>document.addEventListener('DOMContentLoaded', function() { Swal.fire('Error', 'Error: " . $conn->error . "', 'error'); });</script>";
          }
        }
        ?>
        
        
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Register New User</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f9;
                    margin: 0;
                    padding: 0;
                }
                .container {
                    width: 60%;
                    margin: 0 auto;
                    padding: 20px;
                    background-color: #ffffff;
                    border-radius: 8px;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                }
                h1 {
                    text-align: center;
                    color: #333;
                }
                form {
                    display: flex;
                    flex-direction: column;
                }
                label {
                    font-size: 16px;
                    margin-bottom: 5px;
                    color: #0056b3;
                    text-align: left;
                    padding-left: 15px;
                    padding-bottom: 10px;
                    
                }
                input {
                    padding: 10px;
                    margin-bottom: 15px;
                    border: 1px solid #ccc;
                    border-radius: 4px;
                    font-size: 16px;
                    width: 90%;
                   margin-left: 5px;
                   color: #0056b3;
                }
                input[type="date"] {
                    padding: 10px;
                }
                button {
                    padding: 12px;
                    background-color: blue;
                    color: white;
                    border: none;
                    border-radius: 4px;
                    font-size: 16px;
                    cursor: pointer;
                }
                button:hover {
                    background-color: #45a049;
                }
                .back-link {
                    display: block;
                    text-align: center;
                    margin-top: 20px;
                    text-decoration: none;
                    color: #007bff;
                }
                .back-link:hover {
                    text-decoration: underline;
                }
                h1{
                    color: #6fb2fa;
                    font-weight: bolder;
                    padding-bottom: 10px;
                    text-shadow: 0px 1px 2px #ffffff;
                }
            </style>
        </head>
        <body>
            <h1>Register New User</h1>
        <div class="container">
           
            <form method="POST" action="">
                <label for="fname">Full Name:</label>
                <input type="text" name="fname" placeholder="Enter Full Name" required><br>
        
                <label for="accno">Account Number:</label>
                <input type="text" name="accno" placeholder="Enter Account Number" required><br>
        
                <label for="address">Address:</label>
                <input type="text" name="address" placeholder="Enter Address" required><br>
        
                <label for="email">Email:</label>
                <input type="email" name="email" placeholder="Enter Email" required><br>
        
                <label for="nic">NIC:</label>
                <input type="text" name="nic" placeholder="Enter NIC" required><br>
        
                <label for="phone">Phone Number:</label>
                <input type="text" name="phone" placeholder="Enter Phone Number" required><br>
        
                <label for="date_of_birth">Date of Birth:</label>
                <input type="date" name="date_of_birth" required><br>
        
                <button type="submit">Register User</button>
            </form>
            <a class="back-link" href="dashboard.php">Back to Dashboard</a>
        </div>
        
        
        </body>
        </html>
        
    </div>
    
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    
  </body>
</html>
