<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "ecommerce_db");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$msg = "";

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if ($email == "" || $password == "") {
        $msg = "⚠️ Please fill all fields.";
    } else {
        $query = "SELECT * FROM customers WHERE email='$email' LIMIT 1";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            if (password_verify($password, $row['password'])) {
                $_SESSION['customer_email'] = $row['email'];
                $_SESSION['customer_name'] = $row['name'];
                header("Location: index.php");
                exit;
            } else {
                $msg = "❌ Invalid password.";
            }
        } else {
            $msg = "❌ No account found with this email.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="col-md-4 mx-auto card shadow p-4">
            <h3 class="text-center mb-3">Customer Login</h3>
            <?php if ($msg != ""): ?>
                <div class="alert alert-danger text-center py-2"><?php echo $msg; ?></div>
            <?php endif; ?>
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Email:</label>
                    <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password:</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
                <p class="text-center mt-3 mb-0">Don't have an account? <a href="customer_register.php">Register</a></p>
            </form>
        </div>
    </div>
</body>
</html>
