<?php
session_start();
$conn = new mysqli("localhost", "root", "", "ecommerce_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$msg = "";
$showForm = false;

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $sql = "SELECT * FROM login_owner WHERE reset_token='$token' LIMIT 1";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (time() <= $row['token_expiry']) {
            $showForm = true;
            $_SESSION['reset_username'] = $row['username'];
        } else {
            $msg = "Token expired!";
        }
    } else {
        $msg = "Invalid token!";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dob = $_POST['dob'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $birthplace = strtolower(trim($_POST['birthplace']));
    $newpass = $_POST['newpass'];
    $username = $_SESSION['reset_username'] ?? '';

    $sql = "SELECT * FROM login_owner 
            WHERE username='$username' 
              AND dob='$dob' 
              AND phone='$phone' 
              AND email='$email' 
              AND LOWER(security_answer)='$birthplace'
            LIMIT 1";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $conn->query("UPDATE login_owner 
                      SET password='$newpass', reset_token=NULL, token_expiry=NULL 
                      WHERE username='$username'");
        $msg = "✅ Password reset successful! <a href='index.php'>Login</a>";
    } else {
        $msg = "❌ Verification failed!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Identity</title>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            background: #fff;
            padding: 10px 30px;
            width: 400px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            animation: fadeIn 0.8s ease-in-out;
        }
        .logo {
            font-size: 50px;
            color: #2575fc;
            margin-bottom: 10px;
            animation: pulse 2s infinite;
        }
        h2 {
            margin-bottom: 10px;
            color: #333;
        }
        #timer {
            font-weight: bold;
            margin-bottom: 10px;
            color: #e63946;
        }
        input, button {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border-radius: 8px;
            font-size: 14px;
        }
        input {
            border: 2px solid #ddd;
            transition: 0.3s;
        }
        input:focus {
            border-color: #2575fc;
            box-shadow: 0 0 6px rgba(37,117,252,0.5);
            outline: none;
        }
        button {
            background: #2575fc;
            border: none;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s, background 0.3s;
        }
        button:hover {
            background: #6a11cb;
            transform: scale(1.05);
        }
        p {
            margin-top: 10px;
            font-size: 14px;
        }
        p a {
            color: #2575fc;
            text-decoration: none;
            font-weight: bold;
        }
        p a:hover {
            text-decoration: underline;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
    </style>
    <script>
        let remaining = 60;
        setInterval(() => {
            if (remaining > 0) {
                document.getElementById("timer").innerText = "⏳ Token expires in " + remaining + " sec";
                remaining--;
            } else {
                let form = document.getElementById("verifyForm");
                if (form) form.style.display = "none";
                document.getElementById("timer").innerText = "❌ Token expired!";
            }
        }, 1000);
    </script>
</head>
<body>
    <div class="container">
        <!-- Font Awesome Lock Logo -->
        <i class="fas fa-lock logo"></i>

        <h2>Verify Identity</h2>
        <p id="timer"></p>
        <?php if ($msg) echo "<p style='color:red'>$msg</p>"; ?>
        
        <?php if ($showForm): ?>
        <form method="post" id="verifyForm">
            <input type="date" name="dob" required>
            <input type="text" name="phone" placeholder="Phone Number" required>
            <input type="email" name="email" placeholder="Email" required>

            <label style="float:left; font-size:13px; margin-top:5px;">Q:-What is your Birthplace City?</label>
            <input type="text" name="birthplace" placeholder="Enter City" required>

            <input type="password" name="newpass" placeholder="New Password" required>
            <button type="submit">Reset Password</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
