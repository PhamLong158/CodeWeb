<?php
session_start();
include 'config.php';

// Xử lý tìm kiếm
$search = isset($_GET['search']) ? $_GET['search'] : '';
$query = "SELECT * FROM products WHERE name LIKE :search";
$stmt = $conn->prepare($query);
$stmt->execute([':search' => "%$search%"]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Xử lý thêm sản phẩm vào giỏ hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    if (!isset($_SESSION['Cart'])) {
        $_SESSION['Cart'] = [];
    }

    if (!in_array($product_id, $_SESSION['Cart'])) {
        $_SESSION['Cart'][] = $product_id;
        header("Location: cart.php"); // Redirect to cart page
        exit();
    } else {
        $message = "Product is already in the cart.";
    }
}

// Xử lý xóa sản phẩm khỏi giỏ hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_product_id'])) {
    $remove_product_id = $_POST['remove_product_id'];

    if (isset($_SESSION['Cart'])) {
        // Lọc bỏ sản phẩm cần xóa
        $_SESSION['Cart'] = array_filter($_SESSION['Cart'], function($product_id) use ($remove_product_id) {
            return $product_id != $remove_product_id;
        });
        $_SESSION['Cart'] = array_values($_SESSION['Cart']); // Đảm bảo chỉ số mảng là liên tục
        $message = "Product removed from the CART.";

        // Chuyển hướng về trang Home sau khi xóa
        header("Location: Home.php");
        exit; // Dừng thực thi mã phía dưới để đảm bảo chuyển hướng xảy ra
    }
}

// Xử lý Logout
if (isset($_GET['logout'])) {
    session_unset(); // Hủy tất cả các biến session
    session_destroy(); // Hủy phiên làm việc
    header("Location: login.php"); // Chuyển hướng về trang login
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Phone Store</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #333;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header nav a {
            color: white;
            margin-right: 15px;
            text-decoration: none;
        }

        .search-form {
            display: flex;
            align-items: center;
            margin-right: 200px; /* Dịch thanh tìm kiếm sang bên trái thêm khoảng 5cm */
        }

        .search-form input {
            padding: 8px;
            font-size: 16px;
            width: 200px;
        }

        .search-form button {
            padding: 8px 16px;
            font-size: 16px;
            cursor: pointer;
        }

        .logout-btn {
            background-color: #dc3545; /* Màu đỏ cho nút Logout */
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
            text-decoration: none; /* Xóa gạch dưới */
        }

        .products {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .product {
            border: 1px solid #ccc;
            padding: 15px;
            text-align: center;
        }

        .product img {
            max-width: 100%;
            height: auto;
        }

        .btn {
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
            margin-top: 10px;
        }

        .slider {
            position: relative;
            width: 100%;
            height: 50vh;
            overflow: hidden;
        }

        .slides {
            display: flex;
            transition: transform 0.5s ease-in-out;
            height: 100%;
        }

        .slide {
            min-width: 100%;
            height: 100%;
            box-sizing: border-box;
        }

        .slide img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: center;
        }

        button.prev, button.next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            z-index: 10;
        }

        button.prev {
            left: 10px;
        }

        button.next {
            right: 10px;
        }

        button.prev:hover, button.next:hover {
            background-color: rgba(0, 0, 0, 0.7);
        }
        footer {
    background-color: #333; /* Màu nền tối */
    color: white; /* Màu chữ trắng */
    text-align: center; /* Canh giữa nội dung */
    padding: 20px 10px; /* Khoảng cách trong */
    margin-top: 30px; /* Tạo khoảng cách với nội dung phía trên */
    font-size: 14px; /* Cỡ chữ */
}

footer a {
    color: #00bcd4; /* Màu liên kết */
    text-decoration: none; /* Xóa gạch dưới liên kết */
    margin: 0 5px; /* Khoảng cách giữa các liên kết */
}

footer a:hover {
    color: #0097a7; /* Đổi màu khi hover */
    text-decoration: underline; /* Thêm gạch dưới khi hover */
}

    </style>
</head>
<body>
    <header>
        <h1>Long Vinh Phone Store</h1>
        <nav>
            <a href="Home.php">Home</a>
            <a href="Contact.php">Contact</a>
            <a href="Profile.php">Profile</a>
            <a href="Cart.php">My Cart</a>
        </nav>
        <form class="search-form" method="GET">
            <input type="text" name="search" placeholder="Search for products..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Search</button>
        </form>
        <a href="?logout=true" class="logout-btn">Logout</a>
    </header>

    <!-- Slider -->
    <div class="slider">
        <div class="slides">
            <div class="slide">
                <img src="https://www.cnet.com/a/img/55Y_q1Pg0cN5Uae_B4GzIc5Ai-8=/940x528/2021/09/17/96d9c3ae-6ff1-44ed-b2c5-6d7321857df9/screen-shot-2021-09-17-at-10-55-59-am.png" alt="Slide 1">
            </div>
            <div class="slide">
                <img src="https://mir-s3-cdn-cf.behance.net/project_modules/max_1200/668ca5128790251.615d8356916ac.gif" alt="Slide 2">
            </div>
            <div class="slide">
                <img src="https://shopdunk.com/images/uploaded/banner/banner%202024/Thang11-2024/Thang11_V2/banner%20tr%E1%BA%A3%20g%C3%B3p%201_PC.png" alt="Slide 3">
            </div>
        </div>
        <button class="prev" onclick="prevSlide()">&#10094;</button>
        <button class="next" onclick="nextSlide()">&#10095;</button>
    </div>

    <!-- Notification -->
    <?php if (isset($message)): ?>
        <p style="color: green;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <!-- Products -->
    <h2>Featured Products</h2>
    <div class="products">
        <?php if (count($products) > 0): ?>
            <?php foreach ($products as $product): ?>
                <div class="product">
                    <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                    <h3><?= htmlspecialchars($product['name']) ?></h3>
                    <p><?= htmlspecialchars($product['price']) ?> USD</p>
                    <form method="POST" action="">
                        <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>">
                        <button type="submit" class="btn">Add to Cart</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No products found.</p>
        <?php endif; ?>
    </div>

    <script>
        let currentIndex = 0;
        const slides = document.querySelectorAll('.slide');
        const totalSlides = slides.length;

        function nextSlide() {
            currentIndex = (currentIndex + 1) % totalSlides;
            updateSlider();
        }

        function prevSlide() {
            currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
            updateSlider();
        }

        function updateSlider() {
            const offset = -currentIndex * 100;
            document.querySelector('.slides').style.transform = `translateX(${offset}%)`;
        }

        setInterval(nextSlide, 3000);
        updateSlider();
    </script>
    <footer>
    <p>© 2024 Long Vinh Phone Store. All rights reserved.</p>
    <p>Contact us: <a href="mailto:support@longvinhphonestore.com">support@longvinhphonestore.com</a> | Phone: +123 456 789</p>
    <p>Follow us: 
        <a href="https://www.facebook.com/LongVinhPhoneStore" target="_blank">Facebook</a> | 
        <a href="https://www.twitter.com/LongVinhPhoneStore" target="_blank">Twitter</a> | 
        <a href="https://www.instagram.com/LongVinhPhoneStore" target="_blank">Instagram</a>
    </p>
</footer>

</body>
</html>
