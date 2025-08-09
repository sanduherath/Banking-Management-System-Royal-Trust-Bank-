

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
       
      }
      .container1 {
        background-color: #000000;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 30px;
        width: 20%;
        max-width: 900px;
        text-align: center;
        margin: 20px;
        height:600px;
      }
      .container2 {
        background-color: #000000;
        border-radius: 10px;
        box-shadow: 2px 4px 5px rgba(0, 0, 0, 0.1);
        padding: 30px;
        padding-top: 10px;
        width: 100%;
        max-width: 1000px;
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
      
    
  </body>
</html>
<?php
require '../db.php';
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}

// Handle user deletion
if (isset($_GET['delete'])) {
  $user_id = intval($_GET['delete']);
  $stmt = $conn->prepare("UPDATE users SET status = 'inactive' WHERE accno = ?");
  $stmt->bind_param("i", $user_id);
  if ($stmt->execute()) {
      echo "<script>document.addEventListener('DOMContentLoaded', function() { Swal.fire({title: 'Success', text: 'User deleted successfully!', icon: 'success'}).then(() => { window.location.href = 'manage_users.php'; }); });</script>";
  } else {
      echo "<script>document.addEventListener('DOMContentLoaded', function() { Swal.fire('Error', 'Error: " . $conn->error . "', 'error'); });</script>";
  }
}

// Fetch all active users
$result = $conn->query("SELECT accno, name, email, balance, status FROM users WHERE status = 'active'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manage Users</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: black;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            padding: 12px 15px;
            text-align: center;
            border: 1px solid #ddd;
        }
        table th {
            background-color: #071330;
            color: #ffffff;
        }
        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        table tr:hover {
            background-color: #e9e9e9;
        }
        table td a {
            color: #ffffff;
            background-color: #ff6f61;
            padding: 8px 16px;
            border-radius: 5px;
            text-decoration: none;
            margin: 0 5px;
        }
        table td a:hover {
            background-color: #ff3b2f;
        }
        .back-btn {
            display: block;
            margin-top: 20px;
            text-align: center;
            text-decoration: none;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            width:25%;
        }
        .back-btn:hover {
            background-color: #0056b3;
        }
        #deactivate{
            background-color:red;
        }
        #edit{
            background-color:#008000;
        }

        


    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Active Users</h1>

        <!-- Users Table -->
        <table>
            <thead>
                <tr>
                    <th>Acc.num</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Balance</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['accno']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td>LKR <?php echo number_format($row['balance'], 2); ?></td>
                        <td>
                            <a href="?delete=<?php echo $row['accno']; ?>" id="deactivate" >Deactivate</a>
                            <a href="edit_user.php?accno=<?php echo $row['accno']; ?>" id="edit" >Edit</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Back to Dashboard -->
        <!-- <a href="dashboard.php" class="back-btn">Back to Dashboard</a> -->
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>
</html>
