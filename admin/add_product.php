<?php
$conn = new mysqli("localhost", "root", "", "ecommerce_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST["name"]), ENT_QUOTES, 'UTF-8');
    $price = (float) $_POST["price"];
    $section = $_POST["section"]; // Boy or Girl
    $image = $_FILES["image"]["name"];

    // Folder for uploads
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Unique name to avoid overwriting
    $target_file = $target_dir . time() . "_" . basename($image);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // ✅ Allowed image types
    $allowed = ["jpg", "jpeg", "png", "gif", "webp"];
    if (!in_array($imageFileType, $allowed)) {
        $message = "❌ Only JPG, JPEG, PNG, GIF & WEBP files are allowed.";
    } else {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            
            // ✅ Decide table based on section
            if ($section === "Boy") {
                $table = "boys_product";
            } else {
                $table = "girls_product";
            }

            // Insert into correct table
            $sql = "INSERT INTO $table (name, price, image_path, status) 
                    VALUES ('$name', '$price', '$target_file', 'active')";

            if ($conn->query($sql) === TRUE) {
                $message = "✅ Product added successfully to $table!";
            } else {
                $message = "❌ Database Error: " . $conn->error;
            }
        } else {
            $message = "❌ Image upload failed.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Clothes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">

                <!-- Message -->
                <?php if ($message): ?>
                    <div class="alert alert-info text-center">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <div class="card shadow-lg border-0">
                    <div class="card-header bg-primary text-white text-center">
                        <h4>Add New Clothes</h4>
                    </div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">

                            <div class="mb-3">
                                <label class="form-label">Clothes Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Enter product name"
                                    required oninput="this.value = this.value.replace(/[^a-zA-Z0-9 ]/g, '');">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Price (₹)</label>
                                <input type="number" name="price" step="0.01" class="form-control"
                                    placeholder="Enter price" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Section</label>
                                <select name="section" class="form-control" required>
                                    <option value="" disabled selected>Select a Section</option>
                                    <option value="Boy">Boys</option>
                                    <option value="Girl">Girls</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Upload Image</label>
                                <input type="file" name="image" class="form-control" accept="image/*" required>
                            </div>

                            <button type="submit" class="btn btn-success w-100">Add Clothes</button>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <a href="dashboard.php" class="btn btn-outline-dark">View Clothes List</a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
