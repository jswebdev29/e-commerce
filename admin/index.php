<?php
session_start();

// DB connection
$conn = new mysqli('localhost', 'root', '', 'ecommerce_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize tracking
if (!isset($_SESSION['failed_attempts'])) $_SESSION['failed_attempts'] = 0;
if (!isset($_SESSION['first_attempt_time'])) $_SESSION['first_attempt_time'] = time();
if (!isset($_SESSION['block_time'])) $_SESSION['block_time'] = 0;

$error = "";
$blocked = false;
$remainingTime = 0;

// Check if still blocked
if (time() - $_SESSION['block_time'] < 30) {
    $blocked = true;
    $remainingTime = 30 - (time() - $_SESSION['block_time']);
    $error = "Too many failed attempts. Try again in $remainingTime seconds.";
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]) ?? '';
    $password = trim($_POST["password"]) ?? '';
    $userCaptcha = trim($_POST["captchaCode"]) ?? '';
    $realCaptcha = $_POST["captcha"] ?? '';

    if ($userCaptcha !== $realCaptcha) {
        $error = "Captcha does not match!";
    } else {
        $sql = "SELECT * FROM login_owner WHERE (username = '$username' OR email = '$username') AND password = '$password' LIMIT 1";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            // Reset counters on success
            $_SESSION['failed_attempts'] = 0;
            $_SESSION['first_attempt_time'] = time();
            $_SESSION['block_time'] = 0;

            $_SESSION["username"] = $username;
            header("Location: dashboard.php");
            exit();
        } else {
            // Wrong password handling
            if (time() - $_SESSION['first_attempt_time'] > 30) {
                $_SESSION['failed_attempts'] = 1;
                $_SESSION['first_attempt_time'] = time();
            } else {
                $_SESSION['failed_attempts']++;
                if ($_SESSION['failed_attempts'] >= 2) {
                    $_SESSION['block_time'] = time();
                    $blocked = true;
                    $remainingTime = 30;
                }
            }

            if (!$blocked && !$error) {
                $error = "Invalid username or password!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" />

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: linear-gradient(319deg, #446f91, #008eff);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            position: relative;
            padding: 30px 40px;
            width: 300px;
            text-align: center;
            border-radius: 12px;
            background: conic-gradient(#ff0000, #00ff00, #0000ff, #ffff00, #ff0000);
            overflow: hidden;
            z-index: 1;
        }

        .login-card::before {
            content: "";
            position: absolute;
            top: -4px;
            left: -4px;
            right: -4px;
            bottom: -4px;
            border-radius: 12px;
            background: conic-gradient(#ff0000, #00ff00, #0000ff, #ffff00, #ff0000);
            animation: rotateBorder 3s linear infinite;
            z-index: -1;
        }

        .login-card::after {
            content: "";
            position: absolute;
            top: 4px;
            left: 4px;
            right: 4px;
            bottom: 4px;
            border-radius: 10px;
            background: #007de1;
            z-index: -1;
        }

        @keyframes rotateBorder {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .login-card h2 {
            margin-top: 10px;
            color: rgb(134, 4, 4);
            font-size: 30px;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-bottom: 10px;
        }

        input {
            border-top: none;
            border-left: none;
            border-right: none;
            border-bottom: 2px solid black;
            color: white;
            background-color: transparent;
            width: 90%;
            padding: 10px 10px 10px 35px;
            margin: 8px 0;
            font-size: 18px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        input:focus-visible {
            outline: none;
        }

        input::placeholder {
            color: #ffffff80;
        }

        input[type="submit"],
        input[type="reset"] {
            width: 42%;
            padding: 10px;
            border: none;
            border-radius: 8px;
            margin: 10px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        input[type="submit"] {
            background: #370b66;
            color: white;
        }

        input[type="reset"] {
            background: #370b66cc;
            color: white;
        }

        input[type="submit"]:hover,
        input[type="reset"]:hover {
            border: 1px solid black;
            background: green;
            transform: scale(1.05);
        }

        .captcha-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 15px 0;
        }

        #captcha {
            user-select: none;
            font-weight: bold;
            font-style: italic;
            text-decoration: line-through;
            font-size: 20px;
            letter-spacing: 12px;
            background: #fff;
            border: 2px solid black;
            color: #000;
            padding: 10px 20px;
            border-radius: 6px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);
        }

        #refreshBtn {
            background: #370b66;
            color: white;
            border: none;
            padding: 12px 14px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
            margin-left: 10px;
        }

        #refreshBtn:hover {
            border: 1px solid black;
            background: green;
            transform: scale(1.1);
        }

        .input-container {
            position: relative;
            width: 100%;
            margin: 10px 0;
        }

        .input-container i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: white;
            font-size: 18px;
            pointer-events: none;
        }

        .forgot-link {
            margin-top: 10px;
        }

        .forgot-link a {
            color: yellow;
            text-decoration: none;
            font-size: 14px;
        }

        .forgot-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="login-card">
        <h2><i class="fa-solid fa-circle-user"></i> Login</h2>
        <?php if ($error) echo "<p class='error'>$error</p>"; ?>
        <?php if ($blocked) echo "<p id='timer'>Try again in $remainingTime seconds</p>"; ?>

        <form method="post" id="loginForm">
            <div class="input-container">
                <i class="fa-solid fa-user"></i>
                <input type="text" name="username" placeholder="Enter Username or Email" required <?php if($blocked) echo "disabled"; ?>>
            </div>

            <div class="input-container">
                <i class="fa-solid fa-lock"></i>
                <input type="password" name="password" placeholder="Enter Password" required <?php if($blocked) echo "disabled"; ?>>
            </div>

            <div class="captcha-container">
                <p id="captcha"></p>
                <button id="refreshBtn" type="button"><i class="fa-solid fa-rotate"></i></button>
            </div>

            <div class="input-container">
                <i class="fa-solid fa-shield-halved"></i>
                <input type="hidden" name="captcha" id="captchaHidden">
                <input type="text" name="captchaCode" placeholder="Enter Captcha" required <?php if($blocked) echo "disabled"; ?>>
            </div>

            <div>
                <input type="submit" value="Login" <?php if($blocked) echo "disabled"; ?>>
                <input type="reset" value="Reset" <?php if($blocked) echo "disabled"; ?>>
            </div>
        </form>

        <!-- ✅ Forgot Password Link -->
        <div class="forgot-link">
            <a href="forgot_password.php">Forgot Password?</a>
        </div>
    </div>

    <script>
        // ✅ Generate Captcha
        const captcha = document.getElementById('captcha');
        const captchaHidden = document.getElementById('captchaHidden');
        const refreshBtn = document.getElementById('refreshBtn');

        function generateCaptcha() {
            const text = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789";
            let code = "";
            for (let i = 0; i < 5; i++) {
                code += text[Math.floor(Math.random() * text.length)];
            }
            captcha.innerText = code;
            captchaHidden.value = code;
        }

        refreshBtn.addEventListener('click', (e) => {
            e.preventDefault();
            generateCaptcha();
        });

        generateCaptcha();

        // ✅ Countdown for blocked state
        <?php if ($blocked): ?>
        let remaining = <?php echo $remainingTime; ?>;
        const timerElem = document.getElementById('timer');
        const inputs = document.querySelectorAll('#loginForm input, #loginForm button');

        const countdown = setInterval(() => {
            remaining--;
            if (remaining <= 0) {
                clearInterval(countdown);
                timerElem.style.display = 'none';
                inputs.forEach(inp => inp.disabled = false);
            } else {
                timerElem.innerText = `Try again in ${remaining} seconds`;
            }
        }, 1000);
        <?php endif; ?>
    </script>
</body>
</html>
