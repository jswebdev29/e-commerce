<?php
session_start();
$conn = new mysqli("localhost", "root", "", "ecommerce_db");
if ($conn->connect_error)
    die("Connection failed: " . $conn->connect_error);

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = trim($_POST["username"]); // can be username or email

    // Check if user exists by username OR email
    $sql = "SELECT * FROM login_owner WHERE username='$input' OR email='$input' LIMIT 1";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $token = bin2hex(random_bytes(16));
        $expiry = time() + 45; // valid for 45 seconds

        $row = $result->fetch_assoc();
        $username = $row['username']; // store username for session

        $conn->query("UPDATE login_owner SET reset_token='$token', token_expiry='$expiry' WHERE username='$username'");

        $_SESSION['reset_username'] = $username;

        $msg = "Password reset link (valid 45 sec): 
        <a href='forgot_password_verify.php?token=$token'>$token</a>";
    } else {
        $msg = "User not found!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url(/e-commerce/admin/assets/img/forgot\ pass\ bg.jpg);
            background-size: cover;
            background-position: center;
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: right;  
            align-items: center;        
        }

        .container {
            background: #fff;
            padding: 80px 40px;
            margin-right: 60px;
            margin-top: 50px;
            text-align: center;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            border-radius: 12px;
            /* box-shadow: 0 8px 20px rgba(0,0,0,0.15); */
            /* border-radius: 12px; */
        }

        .logo {
            font-size: 50px;
            color: #2575fc;
            margin-bottom: 15px;
            animation: zoomIn 0.8s ease-in-out;
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
            animation: fadeIn 0.8s ease-in-out;
        }

        input[type="text"] {
            width: 90%;
            padding: 12px;
            margin: 10px 0 20px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus {
            border-color: #2575fc;
            box-shadow: 0 0 8px rgba(37, 117, 252, 0.5);
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #2575fc;
            border: none;
            color: #fff;
            font-size: 16px;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.2s, background 0.3s;
        }

        button:hover {
            background: #6a11cb;
            transform: scale(1.05);
        }

        p {
            margin-top: 15px;
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
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes zoomIn {
            from {
                transform: scale(0.5);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Font Awesome Logo -->
        <i class="fas fa-lock logo"></i>

        <h2>Forgot Password</h2>
        <form method="post">
            <input type="text" name="username" placeholder="Enter Username or Email" required>
            <button type="submit">Send Reset Link</button>
        </form>
        <p style="color:red"><?= $msg ?></p>
    </div>
</body>

</html>