<?php
$conn = new mysqli("localhost", "root", "", "ecommerce_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = intval($_GET['id']);
$section = $_GET['section'] ?? 'boys'; // default boys

// Map section to table name
$table = ($section === 'girls') ? "girls_product" : "boys_product";

// Delete the product
$sql = "DELETE FROM $table WHERE id = $id";
if ($conn->query($sql) === TRUE) {
    header("Location: dashboard.php");
    exit();
} else {
    echo "Error deleting record: " . $conn->error;
}

$conn->close();
?>
