<?php
session_start();
include 'config.php';

// Kiểm tra giỏ hàng
$cart_products = [];
if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
    $cart_product_ids = $_SESSION['cart'];
    $placeholders = str_repeat('?,', count($cart_product_ids) - 1) . '?';
    $stmt = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($cart_product_ids);
    $cart_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $message = "Your cart is empty!";
}

// Xử lý xóa sản phẩm khỏi giỏ hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_product_id'])) {
    $remove_product_id = $_POST['remove_product_id'];

    if (isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array_filter($_SESSION['cart'], function($product_id) use ($remove_product_id) {
            return $product_id != $remove_product_id;
        });
        $_SESSION['cart'] = array_values($_SESSION['cart']); // Đảm bảo chỉ số mảng là liên tục
        $message = "Product removed from the cart.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart - Phone Store</title>
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
    </style>
</head>
<body>
    <header>
        <h1>My Cart</h1>
        <nav>
            <a href="Home.php">Home</a>
            <a href="Contact.php">Contact</a>
            <a href="rofile.php">Profile</a>
        </nav>
    </header>
    <main>
        <h2>Shopping Cart</h2>
        <?php if (isset($message)): ?>
            <p style="color: green;"><?= htmlspecialchars($message) ?></p>
        <?php else: ?>
            <div class="cart-items">
                <?php foreach ($cart_products as $product): ?>
                    <div class="cart-item">
                        <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                        <div>
                            <p><?= htmlspecialchars($product['name']) ?></p>
                            <p><?= htmlspecialchars($product['price']) ?> USD</p>
                        </div>
                        <form method="POST" action="cart.php">
                            <input type="hidden" name="remove_product_id" value="<?= htmlspecialchars($product['id']) ?>">
                            <button type="submit" class="btn btn-remove">Remove</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
