<?php
session_start();
include 'config.php';

// Kiểm tra trạng thái thanh toán
if (isset($_SESSION['payment_status']) && $_SESSION['payment_status'] === 'success') {
    $message = "Payment successful. Thank you for your purchase!";
    unset($_SESSION['payment_status']); // Xóa thông báo sau khi hiển thị
}

// Kiểm tra giỏ hàng
$cart_products = [];
$total_price = 0; // Khởi tạo tổng thanh toán
if (isset($_SESSION['Cart']) && count($_SESSION['Cart']) > 0) {
    $cart_product_ids = $_SESSION['Cart'];
    $placeholders = str_repeat('?,', count($cart_product_ids) - 1) . '?';
    
    // Sửa lỗi: Bind array đúng cách trong PDO
    $stmt = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($cart_product_ids); // Truyền đúng mảng các ID sản phẩm vào execute
    $cart_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Tính tổng thanh toán
    foreach ($cart_products as $product) {
        $product_id = $product['id'];
        $product_quantity = isset($_SESSION['Cart'][$product_id]) ? $_SESSION['Cart'][$product_id] : 1;
        $total_price += $product['price'] * $product_quantity;
    }
} else {
    $message = "Your cart is empty!";
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
        header("Location: Cart.php"); // Chuyển hướng về trang giỏ hàng sau khi xóa
        exit();
    }
}

// Xử lý thay đổi số lượng sản phẩm trong giỏ hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quantity'])) {
    $update_quantity_id = $_POST['update_product_id'];
    $new_quantity = $_POST['quantity'];

    if (isset($_SESSION['Cart'][$update_quantity_id])) {
        $_SESSION['Cart'][$update_quantity_id] = $new_quantity;
        header("Location: Cart.php"); // Chuyển hướng về trang giỏ hàng sau khi cập nhật số lượng
        exit();
    }
}

// Xử lý thanh toán
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['proceed_to_checkout'])) {
    // Giả lập thanh toán thành công
    $payment_success = true;  // Đây là nơi bạn sẽ tích hợp với hệ thống thanh toán của bạn

    if ($payment_success) {
        // Xóa giỏ hàng sau khi thanh toán thành công
        unset($_SESSION['Cart']);
        $_SESSION['payment_status'] = 'success'; // Lưu trạng thái thanh toán thành công
        header("Location: Cart.php"); // Chuyển hướng về trang giỏ hàng sau khi thanh toán
        exit();
    } else {
        $message = "Payment failed. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My CART - Phone Store</title>
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
        }
        header nav a {
            color: white;
            margin-right: 15px;
            text-decoration: none;
        }
        main {
            padding: 20px;
        }
        .cart-item {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }
        .cart-item img {
            max-width: 100px;
            height: auto;
        }
        .btn {
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
        }
        .btn-remove {
            background-color: #dc3545;
        }
        .success-message {
            color: green;
            font-weight: bold;
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
        <h1>MY CART</h1>
        <nav>
            <a href="Home.php">Home</a>
            <a href="Contact.php">Contact</a>
            <a href="Profile.php">Profile</a>
            <a href="Cart.php">My Cart</a>
        </nav>
    </header>
    <main>
        <h2>Shopping CART</h2>
        <?php if (isset($message)): ?>
            <p class="success-message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        <?php if (count($cart_products) > 0): ?>
            <div class="cart-items">
                <?php foreach ($cart_products as $product): ?>
                    <div class="cart-item">
                        <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                        <div>
                            <p><?= htmlspecialchars($product['name']) ?></p>
                            <p><?= htmlspecialchars($product['price']) ?> USD</p>
                        </div>
                        <form method="POST" action="Cart.php">
                            <input type="hidden" name="remove_product_id" value="<?= htmlspecialchars($product['id']) ?>">
                            <button type="submit" class="btn btn-remove">Remove</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
        <?php endif; ?>

        <!-- Thanh toán -->
        <h3>Total: <?= number_format($total_price, 2) ?> USD</h3>
        <form method="POST" action="Cart.php">
            <button type="submit" name="proceed_to_checkout" class="btn">Proceed to Checkout</button>
        </form>
    </main>
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
