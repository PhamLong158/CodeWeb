<?php
// Kết nối cơ sở dữ liệu
include 'config.php';

$message = "";

// Xử lý thêm sản phẩm
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image = $_POST['image'];

    if ($name && $price && $image) {
        $query = "INSERT INTO products (name, price, image) VALUES (:name, :price, :image)";
        $stmt = $conn->prepare($query);
        $stmt->execute([':name' => $name, ':price' => $price, ':image' => $image]);

        $message = "Product added successfully!";
    } else {
        $message = "All fields are required!";
    }
}

// Xử lý cập nhật sản phẩm
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_product'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image = $_POST['image'];

    if ($id && $name && $price && $image) {
        $query = "UPDATE products SET name = :name, price = :price, image = :image WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->execute([':id' => $id, ':name' => $name, ':price' => $price, ':image' => $image]);

        $message = "Product updated successfully!";
    } else {
        $message = "All fields are required!";
    }
}

// Xử lý xóa sản phẩm
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM products WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->execute([':id' => $id]);

    $message = "Product deleted successfully!";
}

// Lấy danh sách sản phẩm
$query = "SELECT * FROM products ORDER BY created_at DESC";
$stmt = $conn->query($query);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy thông tin sản phẩm để sửa (nếu có)
$product_to_edit = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $query = "SELECT * FROM products WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->execute([':id' => $id]);
    $product_to_edit = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Manager</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        h1, h2 {
            color: #333;
        }
        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        .form-container h2 {
            margin-bottom: 20px;
            color: #007BFF;
        }
        .form-container input[type="text"], .form-container input[type="number"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-container button {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .form-container button:hover {
            background-color: #218838;
        }
        .products {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .product {
            background-color: #fff;
            padding: 15px;
            border-radius: 5px;
            width: 200px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .product img {
            max-width: 100%;
            height: auto;
            margin-bottom: 10px;
        }
        .actions {
            margin-top: 10px;
        }
        .actions a {
            color: #007BFF;
            text-decoration: none;
            font-size: 14px;
        }
        .actions a:hover {
            text-decoration: underline;
        }
        .actions .delete {
            color: red;
            text-decoration: none;
            margin-left: 10px;
        }
        .actions .delete:hover {
            text-decoration: underline;
        }
        .message {
            padding: 10px;
            background-color: #28a745;
            color: white;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .message.error {
            background-color: #dc3545;
        }
    </style>
    <script>
        // Hiển thị thông báo xác nhận khi xóa sản phẩm
        function confirmDelete(event) {
            const confirmed = confirm("Bạn chắc chắn muốn xóa sản phẩm này?");
            if (!confirmed) {
                event.preventDefault(); // Ngăn chặn hành động xóa nếu người dùng chọn "Cancel"
            }
        }
    </script>
</head>
<body>
    <h1>Product Manager</h1>

    <!-- Thông báo nếu có -->
    <?php if (!empty($message)): ?>
        <div class="message <?= strpos($message, 'success') !== false ? '' : 'error' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- Form thêm/sửa sản phẩm -->
    <div class="form-container">
        <?php if ($product_to_edit): ?>
            <h2>Edit Product</h2>
        <?php else: ?>
            <h2>Add New Product</h2>
        <?php endif; ?>

        <form method="POST" onsubmit="<?= $product_to_edit ? 'confirmUpdate(event)' : '' ?>">
            <?php if ($product_to_edit): ?>
                <input type="hidden" name="id" value="<?= htmlspecialchars($product_to_edit['id']) ?>">
            <?php endif; ?>
            <label for="name">Product Name:</label>
            <input type="text" id="name" name="name" value="<?= $product_to_edit['name'] ?? '' ?>" required><br><br>

            <label for="price">Price:</label>
            <input type="number" id="price" name="price" step="0.01" value="<?= $product_to_edit['price'] ?? '' ?>" required><br><br>

            <label for="image">Image URL:</label>
            <input type="text" id="image" name="image" value="<?= $product_to_edit['image'] ?? '' ?>" required><br><br>

            <button type="submit" name="<?= $product_to_edit ? 'update_product' : 'add_product' ?>">
                <?= $product_to_edit ? 'Update Product' : 'Add Product' ?>
            </button>
        </form>
    </div>

    <!-- Hiển thị danh sách sản phẩm -->
    <h2>Product List</h2>
    <div class="products">
        <?php if (count($products) > 0): ?>
            <?php foreach ($products as $product): ?>
                <div class="product">
                    <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                    <h3><?= htmlspecialchars($product['name']) ?></h3>
                    <p>Price: $<?= htmlspecialchars($product['price']) ?></p>
                    <div class="actions">
                        <a href="?edit=<?= htmlspecialchars($product['id']) ?>">Edit</a>
                        <a href="?delete=<?= htmlspecialchars($product['id']) ?>" class="delete" onclick="confirmDelete(event)">Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No products found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
