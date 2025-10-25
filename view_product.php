<?php
$conn = new mysqli("localhost", "root", "", "ecommerce_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$id = intval($_GET['id']);
$category = $_GET['category'] ?? 'boys';
$table = ($category === 'girls') ? 'girls_product' : 'boys_product';

$product = $conn->query("SELECT * FROM $table WHERE id = $id")->fetch_assoc();

if (!$product) {
    echo "<h3>Product not found!</h3>";
    exit;
}

$delivery_charge = 50; // ₹50 delivery charge
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($product['name']); ?> | SmartCart</title>
    <link rel="stylesheet" href="/e-commerce/admin/assets/bootstrap.min.css">
</head>
<body class="p-4">
    <div class="container text-center">
        <img src="admin/<?php echo htmlspecialchars($product['image_path']); ?>" width="300" height="300" class="rounded mb-3">
        <h2><?php echo htmlspecialchars($product['name']); ?></h2>
        <h4 class="text-success">Price: ₹<span id="price"><?php echo number_format($product['price'], 2); ?></span></h4>

        <div class="mb-3">
            <label for="quantity" class="form-label">Quantity:</label>
            <input type="number" id="quantity" value="1" min="1" class="form-control w-25 mx-auto">
        </div>

        <h5>Delivery Charge: ₹<span id="delivery"><?php echo number_format($delivery_charge, 2); ?></span></h5>
        <h4>Total Price: ₹<span id="total"><?php echo number_format($product['price'] + $delivery_charge, 2); ?></span></h4>

        <p><a href="index.php" class="btn btn-secondary">Back</a></p>
    </div>

    <script>
        const price = <?php echo $product['price']; ?>;
        const delivery = <?php echo $delivery_charge; ?>;
        const quantityInput = document.getElementById('quantity');
        const totalSpan = document.getElementById('total');

        quantityInput.addEventListener('input', () => {
            let qty = parseInt(quantityInput.value) || 1;
            let total = (price * qty) + delivery;
            totalSpan.textContent = total.toFixed(2);
        });
    </script>
</body>
</html>
