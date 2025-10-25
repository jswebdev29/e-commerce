<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "ecommerce_db");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$msg = "";

if (isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $location = trim($_POST['location']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm = trim($_POST['confirm_password']);

    // Basic validation
    if ($name == "" || $phone == "" || $address == "" || $location == "" || $email == "" || $password == "" || $confirm == "") {
        $msg = "⚠️ All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = "⚠️ Invalid email format.";
    } elseif (!preg_match("/^[0-9]{10}$/", $phone)) {
        $msg = "⚠️ Invalid phone number (must be 10 digits).";
    } elseif ($password !== $confirm) {
        $msg = "⚠️ Passwords do not match.";
    } else {
        // Check if email already exists
        $check = mysqli_query($conn, "SELECT * FROM customers WHERE email='$email'");
        if (mysqli_num_rows($check) > 0) {
            $msg = "⚠️ Email already registered. Try logging in.";
        } else {
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $insert = "INSERT INTO customers (name, phone, address, location, email, password) 
                       VALUES ('$name', '$phone', '$address', '$location', '$email', '$hashed')";
            if (mysqli_query($conn, $insert)) {
                $_SESSION['customer_email'] = $email;
                $_SESSION['customer_name'] = $name;
                header("Location: index.php");
                exit;
            } else {
                $msg = "❌ Registration failed. Try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Register</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="col-md-6 mx-auto card shadow p-4">
            <h3 class="text-center mb-3">Create Account</h3>
            <?php if ($msg != ""): ?>
                <div class="alert alert-danger text-center py-2"><?php echo $msg; ?></div>
            <?php endif; ?>
            <form method="post">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Full Name:</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter your full name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone No:</label>
                        <input type="text" name="phone" class="form-control" placeholder="Enter 10-digit phone no." required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Address:</label>
                    <input type="text" name="address" class="form-control" placeholder="Enter your address" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Location (City/State):</label>
                    <input type="text" name="location" class="form-control" placeholder="Enter your city or state" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email:</label>
                    <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password:</label>
                        <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Confirm Password:</label>
                        <input type="password" name="confirm_password" class="form-control" placeholder="Confirm password" required>
                    </div>
                </div>
                <button type="submit" name="register" class="btn btn-success w-100">Register</button>
                <p class="text-center mt-3 mb-0">
                    Already have an account? <a href="customer_login.php">Login</a>
                </p>
            </form>
        </div>
    </div>
</body>
</html>
