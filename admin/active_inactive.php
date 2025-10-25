<?php
$conn = new mysqli("localhost", "root", "", "ecommerce_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = intval($_GET['id']);
$section = $_GET['section'] ?? 'boys'; // default boys
$status = ($_GET['status'] === 'active') ? 'active' : 'inactive';

// Map section to table name
$table = ($section === 'girls') ? "girls_product" : "boys_product";

// Update status
$conn->query("UPDATE $table SET status='$status' WHERE id=$id");

header("Location: dashboard.php");
exit();

$conn->close();
?>
