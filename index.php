<?php
$conn = new mysqli("localhost", "root", "", "ecommerce_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : "";
$search_safe = $conn->real_escape_string($searchTerm);
$scrollToProduct = !empty($searchTerm) ? 'true' : 'false';


// Fetch boys products
$sql_boys = "SELECT * FROM boys_product WHERE 1";
if ($search_safe) {
    $sql_boys .= " AND name LIKE '%$search_safe%'";
}
$sql_boys .= " ORDER BY id DESC";
$result_boys = $conn->query($sql_boys);

// Fetch girls products
$sql_girls = "SELECT * FROM girls_product WHERE 1";
if ($search_safe) {
    $sql_girls .= " AND name LIKE '%$search_safe%'";
}
$sql_girls .= " ORDER BY id DESC";
$result_girls = $conn->query($sql_girls);

// Total products
$totalProducts = ($result_boys->num_rows ?? 0) + ($result_girls->num_rows ?? 0);


session_start();

if (isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['customer_email'])) {
        echo "<script>alert('⚠️ Please login first to add items to cart.'); window.location.href='customer_login.php';</script>";
        exit;
    }

    $email = $_SESSION['customer_email'];

    // Get customer details
    $cust = $conn->query("SELECT * FROM customers WHERE email='$email' LIMIT 1");
    $cust_data = $cust->fetch_assoc();

    $customer_name = $cust_data['name'];
    $phone = $cust_data['phone'];
    $address = $cust_data['address'];
    $location = $cust_data['location'];

    $product_id = $_POST['product_id'];
    $category = $_POST['category'];

    // Fetch price from the right table
    $table = ($category === 'boys') ? 'boys_product' : 'girls_product';
    $prod_query = $conn->query("SELECT price FROM $table WHERE id='$product_id' LIMIT 1");

    if ($prod_query && $prod_query->num_rows > 0) {
        $prod_data = $prod_query->fetch_assoc();
        $base_price = $prod_data['price'];
    } else {
        echo "<script>alert('❌ Product not found!');</script>";
        exit;
    }

    $delivery_charge = 50; // fixed delivery charge

    // Check if this product already exists in the cart
    $check = $conn->query("SELECT * FROM cart WHERE customer_email='$email' AND product_id='$product_id' AND category='$category'");
    if ($check->num_rows > 0) {
        // Already in cart → increase quantity and update price
        $row = $check->fetch_assoc();
        $new_qty = $row['quantity'] + 1;
        $new_price = $base_price * $new_qty + $delivery_charge;

        $conn->query("UPDATE cart 
                      SET quantity='$new_qty', price='$new_price' 
                      WHERE customer_email='$email' AND product_id='$product_id' AND category='$category'");

        echo "<script>window.location='index.php?added=1';</script>";
        exit;
    } else {
        // New product → add delivery charge
        $final_price = $base_price + $delivery_charge;

        $sql = "INSERT INTO cart (customer_email, customer_name, phone, address, location, product_id, category, price, quantity)
                VALUES ('$email', '$customer_name', '$phone', '$address', '$location', '$product_id', '$category', '$final_price', 1)";

        if ($conn->query($sql)) {
            echo "<script>window.location='index.php?added=1';</script>";
            exit;
        } else {
            echo "<script>alert('❌ Failed to add to cart!');</script>";
        }
    }
}

?>




<!DOCTYPE html>
<html>

<head>
    <title>Clothes Store</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="/e-commerce/admin/assets/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        * {
            margin: 0px;
            padding: 0px;
            box-sizing: border-box;
        }

        h3 {
            margin: auto;
            margin-top: 10px;
            padding: 5px;
            font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif;
            font-size: 25px;
            text-align: center;
            animation: anmi1 3s ease-in-out 1s infinite;
        }

        @keyframes anmi1 {
            from {
                background-color: #74a8cc;
                border: 2px solid rgb(199, 97, 71);
                width: 40%;
                letter-spacing: 10px;
            }

            to {
                color: white;
                background-color: #2941c7;
                border: 2px solid rgb(15, 131, 96);
                border-radius: 20px;
                width: 90%;
                letter-spacing: 3px;
            }
        }

        .topnav {
            background-color: #84c5f3;
            position: fixed;
            top: 0px;
            z-index: 999;
            width: 100%;
        }

        /* Default white icons */
        .topnav .nav-link {
            color: #fff;
            transition: transform 0.3s ease, color 0.3s ease;
        }

        /* Hover colors per platform */
        .topnav .nav-link:hover i.fa-phone {
            color: #006300;
            transform: scale(1.8);
        }

        /* Green for phone */
        .topnav .nav-link:hover i.fa-envelope {
            color: #ffe23d;
            transform: scale(1.8);
        }

        /* Yellow for email */
        .topnav .nav-link:hover i.fa-instagram {
            color: #E1306C;
            transform: scale(1.8);
        }

        /* Instagram pink */
        .topnav .nav-link:hover i.fa-facebook-f {
            color: #1877F2;
            transform: scale(1.8);
        }

        /* Facebook blue */
        .topnav .nav-link:hover i.fa-twitter {
            color: #1DA1F2;
            transform: scale(1.8);
        }

        /* Twitter blue */
        .topnav .nav-link:hover i.fa-pinterest-p {
            color: #E60023;
            transform: scale(1.8);
        }

        /* Pinterest red */

        .navbar {
            background-color: #84c5f3;
            position: fixed;
            top: 30px;
            z-index: 998;
            width: 100%;
        }

        .navbar .nav-link {
            color: #000;
            transition: all 0.3s ease-in-out;
            border-radius: 5px;
            padding: 6px 12px;
        }

        .navbar .nav-link:hover {
            color: #fff !important;
            background-color: #0d6efd !important;
            transform: scale(1.05);
        }


        .card {
            transition: all 0.3s ease-in-out;
            /* smooth animation */
        }

        .card:hover {
            transform: translateY(-5px);
            /* slight lift */
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            /* soft shadow */
        }

        /* Call to Action Section */
        .cta-section {
            background-color: #dad6ff;
            padding: 50px 20px;
            text-align: center;
            border: 3px solid #b4b4b5;
        }

        .cta-section h4 {
            font-size: 26px;
            color: #0056b3;
            margin-bottom: 15px;
        }

        .cta-section p {
            font-size: 18px;
            margin-bottom: 25px;
            color: #333;
        }

        .cta-section .cta-btn {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .cta-section .cta-btn:hover {
            background-color: #0056b3;
        }

        .services-section {
            background-color: #f0f8ff;
            padding: 50px 20px;
            text-align: center;
        }

        .services-section h2 {
            font-size: 28px;
            margin-bottom: 30px;
        }

        .services-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 50px;
        }

        .service-box {
            background: #fff;
            border: 2px solid #d3d1d1;
            padding: 20px;
            width: 220px;
            border-radius: 10px;
            transition: transform 0.3s ease;
        }

        .service-box i {
            font-size: 36px;
            color: #007bff;
            margin-bottom: 10px;
        }

        .service-box:hover {
            transform: scale(1.05);
        }

        .video-container {
            display: flex;
            justify-content: center;
            margin: 80px 0;
        }

        video {
            width: 90%;
            max-width: 1200px;
            border-radius: 10px;
        }

        .offers-banner {
            background: linear-gradient(90deg, #ff6a00, #ee0979);
            color: white;
            text-align: center;
            width: 240px;
            margin: 15px;
            padding: 18px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 20px 0px;
            position: sticky;
            top: 120px;
            left: 1300px;
            z-index: 997;
        }

        /* ---------------------------------------- */
        footer {
            margin-top: 100px;
            background-color: #343a40;
            border-radius: 0px 0px 5px 5px;
            color: #cbcbcb;
            padding: 20px;
            font-size: 16px;
        }

        .footer-container {
            display: flex;
            text-align: center;
            margin: 0 auto;
            margin-left: 60px;
        }

        .footer-section {
            width: 200px;
            margin: 10px;
        }

        .footer-section h4 {
            color: white;
            font-size: 20px;
            margin-bottom: 10px;
        }

        .footer-section ul {
            list-style: none;
            padding: 0;
        }

        .footer-section ul li {
            margin-bottom: 5px;
        }

        .footer-section ul li a {
            color: #bbb;
            text-decoration: none;
        }

        .footer-section ul li a:hover {
            color: #fff;
        }

        .footer-bottom {
            text-align: center;
            border-top: 1px solid #b1b1b1;
            padding-top: 10px;
            margin-top: 20px;
            color: #aaa;
        }

        .footer-section a {
            color: #ecf0f1;
            text-decoration: none;
            transition: color 0.3s ease, transform 0.3s ease;
            display: inline-block;
            align-items: center;
            padding: 6px 9px;
        }

        .footer-section a:hover {
            color: #f39c12;
            transform: scale(1.2);
        }

        .footer-section i {
            font-size: 30px;
        }
    </style>

</head>

<body>

    <nav class="topnav navbar-expand-lg" style="background-color: #28d645;" data-bs-theme="light">
        <div class="container-fluid d-flex align-items-center justify-content-end">
            <!-- Contact info -->
            <ul class="nav">
                <li class="nav-item">
                    <a class="nav-link text-white" href="tel:+917340706375">
                        <i class="fa-solid fa-phone me-1"></i> +91 7340706375
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="#" id="showEmail">
                        <i class="fa-solid fa-envelope me-1"></i> Click to show email
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-white" href="https://instagram.com" target="_blank" title="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="https://facebook.com" target="_blank" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="https://twitter.com" target="_blank" title="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="https://pinterest.com" target="_blank" title="Pinterest">
                        <i class="fab fa-pinterest-p"></i>
                    </a>
                </li>

            </ul>

        </div>
    </nav>


    <nav class="navbar navbar-expand-lg" style="background-color: #84c5f3;">
        <div class="container-fluid">
            <!-- Logo + Brand -->
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="/e-commerce/img/logo1.jpg" alt="Logo" width="50" height="42" class="me-2 rounded-circle">
                <span>eCommerce Website</span>
            </a>

            <!-- Navbar toggler -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Links -->
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <a class="nav-link active" aria-current="page" href="/e-commerce/index.php">Home</a>
                    <a class="nav-link" href="#BoysProduct">Boys Products</a>
                    <a class="nav-link" href="#GirlsProduct">Girls Products</a>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            Dropdown
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/e-commerce/admin/index.php">Login owner</a></li>
                            <li><a class="dropdown-item disabled" href="/e-commerce/admin/logout.php">Logout</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <?php if (isset($_SESSION['customer_email'])): ?>
                                <li><a class="dropdown-item" href="customer_logout.php">Logout
                                        (<?php echo $_SESSION['customer_email']; ?>)</a></li>
                            <?php else: ?>
                                <li><a class="dropdown-item" href="customer_login.php">Login customer</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a type="button" class="nav-link position-relative">
                            Orders
                            <span id="ord"
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary">
                                <?php echo $totalProducts; ?>
                            </span>
                        </a>
                    </li>


                </ul>

                <!-- Search box -->
                <form class="d-flex" role="search" method="get" action="">
                    <input class="form-control me-2" type="search" name="search"
                        value="<?php echo htmlspecialchars($searchTerm); ?>" placeholder="Search product name..."
                        aria-label="Search" />
                    <button class="btn btn-outline-success" type="submit">
                        <i class="fa-solid fa-magnifying-glass"></i> Search
                    </button>
                </form>

            </div>
        </div>
    </nav>


    <!-- ------------------------------------------------------------------- -->

    <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">

        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active"
                aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1"
                aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2"
                aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="/e-commerce/img/ecommerce-1.webp" class="d-block w-100 " height="520px" alt="...">
                <div class="carousel-caption d-none d-md-block">
                    <h5>First slide label</h5>
                    <p>Some representative placeholder content for the first slide.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="/e-commerce/img/ecommerce-2.webp" class="d-block w-100" height="520px" alt="...">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Second slide label</h5>
                    <p>Some representative placeholder content for the second slide.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="/e-commerce/img/eCommerce-3.jpg" class="d-block w-100" height="520px" alt="...">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Third slide label</h5>
                    <p>Some representative placeholder content for the third slide.</p>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying"
            data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying"
            data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <!-- --------------------------------------------- -->
    <!-- CTA Section -->
    <section class="cta-section text-center my-5">
        <h4>Ready to shop?</h4>
        <p>Explore the latest trends at unbeatable prices.</p>
        <a href="#Product" id="startShoppingBtn" class="cta-btn btn btn-warning btn-lg">Start Shopping</a>
    </section>

    <!-- ------------------------------------------------------- -->
    <!-- 💼 Services Section -->
    <section class="services-section">
        <h2>Our Services</h2>
        <div class="services-grid">
            <div class="service-box">
                <i class="fas fa-shipping-fast"></i>
                <h4>Fast Delivery</h4>
                <p>Get your products delivered within 2-3 days nationwide.</p>
            </div>
            <div class="service-box">
                <i class="fas fa-tags"></i>
                <h4>Best Deals</h4>
                <p>Factory prices directly to your doorstep. No middlemen.</p>
            </div>
            <div class="service-box">
                <i class="fas fa-shield-alt"></i>
                <h4>Secure Payment</h4>
                <p>Multiple secure payment options with SSL encryption.</p>
            </div>
            <div class="service-box">
                <i class="fas fa-headset"></i>
                <h4>24/7 Support</h4>
                <p>Chat, call, or email us anytime. We’re always here to help.</p>
            </div>
        </div>
    </section>

    <!-- ---------------------------------------------------- -->
    <!-- Promo Video -->
    <div class="video-container">
        <video src="/e-commerce/img/44942217.mp4" autoplay muted loop></video>
        <!-- <video src="/ecommerce/image/44942217.mp4" controls autoplay muted loop></video> -->
    </div>

    <!-- -------------------------------------------- -->

    <!-- Product Section -->
    <h2 id="availProduct" class="text-center my-4">Available Products</h2>

    <!-- Offers -->
    <div class="offers-banner">
        🎉 Mega Sale! Flat 30% OFF on First Order | Free Shipping over ₹999 🎉
    </div>

    <!-- Boys Products -->
    <h3 class="text-center my-4">Boys Products</h3>
    <div id="BoysProduct" class="d-flex justify-content-evenly flex-wrap text-center my-3">
        <?php if ($result_boys->num_rows > 0) { ?>
            <?php while ($row = $result_boys->fetch_assoc()) { ?>
                <div class="card mx-2 my-3" style="width: 18rem;">
                    <?php $fullImagePath = "admin/" . $row['image_path']; ?>
                    <img src="<?php echo htmlspecialchars($fullImagePath); ?>" class="card-img-top" height="250px">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                        <p class="card-text text-success fw-bold">₹<?php echo number_format($row['price'], 2); ?></p>
                        <form method="post" class="d-inline">
                            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="category" value="boys">
                            <button type="submit" name="add_to_cart" class="btn btn-primary">
                                <i class="fa-solid fa-cart-plus"></i> Add to Cart
                            </button>
                        </form>
                        <a href="view_product.php?id=<?php echo $row['id']; ?>&category=boys" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-eye"></i> View
                        </a>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <p class="text-danger fw-bold">No boys products found for "<?php echo htmlspecialchars($searchTerm); ?>"</p>
        <?php } ?>
    </div>

    <!-- Girls Products -->
    <h3 class="text-center my-4">Girls Products</h3>
    <div id="GirlsProduct" class="d-flex justify-content-evenly flex-wrap text-center my-3">
        <?php if ($result_girls->num_rows > 0) { ?>
            <?php while ($row = $result_girls->fetch_assoc()) { ?>
                <div class="card mx-2 my-3" style="width: 18rem;">
                    <?php $fullImagePath = "admin/" . $row['image_path']; ?>
                    <img src="<?php echo htmlspecialchars($fullImagePath); ?>" class="card-img-top" height="250px">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                        <p class="card-text text-success fw-bold">₹<?php echo number_format($row['price'], 2); ?></p>
                        <form method="post" class="d-inline">
                            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="category" value="girls">
                            <button type="submit" name="add_to_cart" class="btn btn-primary">
                                <i class="fa-solid fa-cart-plus"></i> Add to Cart
                            </button>
                        </form>
                        <a href="view_product.php?id=<?php echo $row['id']; ?>&category=girls"
                            class="btn btn-outline-secondary">
                            <i class="fa-solid fa-eye"></i> View
                        </a>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <p class="text-danger fw-bold">No girls products found for "<?php echo htmlspecialchars($searchTerm); ?>"</p>
        <?php } ?>
    </div>




    <!-- ------------------------------------------------------------------------- -->
    <!-- Footer -->
    <footer>
        <div class="footer-container">
            <div class="footer-section">
                <h4>MY ACCOUNT</h4>
                <p><br>My account <br><br>Order list <br><br>Returns <br><br>Specials <br><br>Site map</p>
            </div>

            <div class="footer-section">
                <h4>OUR SUPPORT</h4>
                <p><br>About Us <br><br>Privacy policy <br><br>Your account <br><br>Advance search <br><br>Contact us
                </p>
            </div>

            <div class="footer-section">
                <!-- <h4>OPENING TIME</h4>
                <p><br>Mon-sat --- 08:00 AM - 05.00 PM <br><br>Sun ---------Closed</p> -->

                <h4>SMARTCART</h4>
                <p><br>Your one-stop shop for <br> fashion and gadgets.</p>
            </div>

            <div class="footer-section">
                <h4>CATEGORIES</h4>
                <ul>
                    <li><a href="#BoysProduct"><br>Boys Clothes</a></li>
                    <li><a href="#GirlsProduct">Girls Clothes</a></li>
                    <!-- <li><a href="#">Phones</a></li> -->
                </ul>
            </div>

            <div class="footer-section">
                <h4>CONTACT US</h4>
                <p><br>Address: 123 main street,makhu</p>
                <div>
                    <a href="https://phone.com">
                        <i class="fas fa-mobile-alt"></i>+91-9876543210</a>
                    <a href="mailto:jaswindersingh@gmail.com" title="Email">
                        <i class="fas fa-envelope"></i> support@smartcart.com
                    </a>
                </div>

                <a href="https://instagram.com" target="_blank" title="Instagram">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="https://facebook.com" target="_blank" title="Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="https://twitter.com" target="_blank" title="Twitter">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="https://pinterest.com" target="_blank" title="Pinterest">
                    <i class="fab fa-pinterest-p"></i>
                </a>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copyright; 2025 SmartCart. All rights reserved.</p>
        </div>
    </footer>


    <script>

        // Animate Orders badge
        const totalProducts = <?php echo $totalProducts; ?>;
        if (totalProducts > 0) {
            let a = 1;
            const myInterval = setInterval(() => {
                const badge = document.getElementById("ord");
                badge.innerHTML = a++;
                if (a > totalProducts) {
                    clearInterval(myInterval);
                    badge.innerHTML = totalProducts;
                }
            }, 150);
        } else {
            document.getElementById("ord").innerHTML = 0;
        }


        // Show email obfuscation
        document.getElementById("showEmail").addEventListener("click", function (e) {
            e.preventDefault();
            const user = "example";
            const domain = "gmail";
            const domain2 = "com";
            const email = user + "[at]" + domain + "[dot]" + domain2;
            this.href = `mailto:${email}`;
            this.innerHTML = `<i class="fa-solid fa-envelope me-1"></i> ${email}`;
        });

        // Scroll to product section
        document.getElementById("startShoppingBtn").addEventListener("click", function (e) {
            e.preventDefault();
            document.getElementById("availProduct").scrollIntoView({ behavior: "smooth" });
        });

        // Scroll to product section if search was used
        const scrollToProduct = <?php echo $scrollToProduct; ?>;
        if (scrollToProduct) {
            document.getElementById("availProduct").scrollIntoView({ behavior: "smooth" });
        }


        if (window.location.search.includes('added')) {
            alert("✅ Product added to cart!");
        }

    </script>
</body>

</html>