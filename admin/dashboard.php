<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
session_regenerate_id(true);

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "ecommerce_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle filter
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Handle search
$search = isset($_GET['q']) ? trim($_GET['q']) : "";

// Escape search input
$search_safe = $conn->real_escape_string($search);

// Boys query
$sql_boys = "SELECT * FROM boys_product WHERE 1";
if ($filter === 'active') {
    $sql_boys .= " AND status='active'";
} elseif ($filter === 'inactive') {
    $sql_boys .= " AND status='inactive'";
}
if (!empty($search_safe)) {
    $sql_boys .= " AND name LIKE '%$search_safe%'";
}
$sql_boys .= " ORDER BY id DESC";
$result_boys = $conn->query($sql_boys);

// Girls query
$sql_girls = "SELECT * FROM girls_product WHERE 1";
if ($filter === 'active') {
    $sql_girls .= " AND status='active'";
} elseif ($filter === 'inactive') {
    $sql_girls .= " AND status='inactive'";
}
if (!empty($search_safe)) {
    $sql_girls .= " AND name LIKE '%$search_safe%'";
}
$sql_girls .= " ORDER BY id DESC";
$result_girls = $conn->query($sql_girls);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Clothes Store</title>
    <link rel="stylesheet" href="/e-commerce/admin/assets/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .navbar {
            position: fixed;
            top: 0;
            z-index: 998;
            width: 100%;
        }

        .navbar .nav-link {
            transition: all 0.3s ease-in-out;
            border-radius: 5px;
            padding: 6px 12px;
        }

        .navbar .nav-link:hover {
            color: #000 !important;
            background-color: #fff !important;
            transform: scale(1.05);
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="fas fa-store"></i> MyShop</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation"><span
                    class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" href="dashboard.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="#boys">Boys Product</a></li>
                    <li class="nav-item"><a class="nav-link active" href="#girls">Girls Product</a></li>
                    <li class="nav-item"><a class="nav-link" href="add_product.php">Add Product</a></li>
                </ul>

                <form class="d-flex" role="search" action="dashboard.php" method="get">
                    <input class="form-control me-2" type="search" placeholder="Search products..." name="q"
                        value="<?php echo htmlspecialchars($search); ?>">
                    <button class="btn btn-outline-success" type="submit"><i class="fa-solid fa-magnifying-glass"></i>
                        Search</button>
                </form>

                <ul class="navbar-nav mx-2 mb-2 mb-lg-0">
                    <li class="nav-item"><a class="btn btn-danger" href="logout.php"><i
                                class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5 pt-5">
        <p class="fs-4">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>! 🎉</p>
        <p>Glad to see you logged in successfully.</p>

        <!-- Filter Buttons -->
        <div class="mb-3">
            <a href="?filter=all<?php echo $search ? '&q=' . urlencode($search) : ''; ?>"
                class="btn btn-secondary">All</a>
            <a href="?filter=active<?php echo $search ? '&q=' . urlencode($search) : ''; ?>"
                class="btn btn-success">Active</a>
            <a href="?filter=inactive<?php echo $search ? '&q=' . urlencode($search) : ''; ?>"
                class="btn btn-danger">Inactive</a>
        </div>

        <!-- Boys Products -->
        <h2 id="boys">Boys Products</h2>
        <table class="table table-bordered table-hover align-middle text-center mb-5">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Price (₹)</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $serial = 1;
                while ($row = $result_boys->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $serial++; ?></td>
                        <td><img src="<?php echo htmlspecialchars($row['image_path']); ?>"
                                alt="<?php echo htmlspecialchars($row['name']); ?>" style="width:80px; height:auto;"></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo number_format($row['price'], 2); ?></td>
                        <td><?php echo $row['status'] == 'active' ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>'; ?>
                        </td>
                        <td>
                            <a href="edit_product.php?section=boys&id=<?php echo $row['id']; ?>"
                                class="btn btn-warning btn-sm"><i class="fa fa-edit"></i> Edit</a>
                            <a href="delete_product.php?section=boys&id=<?php echo $row['id']; ?>"
                                class="btn btn-danger btn-sm" onclick="return confirm('Delete this product?')"><i
                                    class="fa fa-trash"></i> Delete</a>
                            <?php if ($row['status'] == 'active') { ?>
                                <a href="active_inactive.php?section=boys&id=<?php echo $row['id']; ?>&status=inactive"
                                    class="btn btn-secondary btn-sm"><i class="fa fa-toggle-off"></i> Make Inactive</a>
                            <?php } else { ?>
                                <a href="active_inactive.php?section=boys&id=<?php echo $row['id']; ?>&status=active"
                                    class="btn btn-success btn-sm"><i class="fa fa-toggle-on"></i> Make Active</a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Girls Products -->
        <h2 id="girls">Girls Products</h2>
        <table class="table table-bordered table-hover align-middle text-center">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Price (₹)</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $serial = 1;
                while ($row = $result_girls->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $serial++; ?></td>
                        <td><img src="<?php echo htmlspecialchars($row['image_path']); ?>"
                                alt="<?php echo htmlspecialchars($row['name']); ?>" style="width:80px; height:auto;"></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo number_format($row['price'], 2); ?></td>
                        <td><?php echo $row['status'] == 'active' ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>'; ?>
                        </td>
                        <td>
                            <a href="edit_product.php?section=girls&id=<?php echo $row['id']; ?>"
                                class="btn btn-warning btn-sm"><i class="fa fa-edit"></i> Edit</a>
                            <a href="delete_product.php?section=girls&id=<?php echo $row['id']; ?>"
                                class="btn btn-danger btn-sm" onclick="return confirm('Delete this product?')"><i
                                    class="fa fa-trash"></i> Delete</a>
                            <?php if ($row['status'] == 'active') { ?>
                                <a href="active_inactive.php?section=girls&id=<?php echo $row['id']; ?>&status=inactive"
                                    class="btn btn-secondary btn-sm"><i class="fa fa-toggle-off"></i> Make Inactive</a>
                            <?php } else { ?>
                                <a href="active_inactive.php?section=girls&id=<?php echo $row['id']; ?>&status=active"
                                    class="btn btn-success btn-sm"><i class="fa fa-toggle-on"></i> Make Active</a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>