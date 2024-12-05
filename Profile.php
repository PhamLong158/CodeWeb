<?php
// Kiểm tra và gán các giá trị từ $_SESSION hoặc đặt giá trị mặc định
$name = isset($_SESSION['name']) ? $_SESSION['name'] : '';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
$phone = isset($_SESSION['phone']) ? $_SESSION['phone'] : '';
$date_of_birth = isset($_SESSION['date_of_birth']) ? $_SESSION['date_of_birth'] : '';
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$password = ''; // Không hiển thị mật khẩu vì lý do bảo mật

// Thông báo lỗi nếu cần
$error_message = '';
if (empty($name)) {
    $error_message = "Name is undefined. Please update your profile.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Phone Store</title>
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
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], input[type="email"], input[type="tel"], input[type="date"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .error-message {
            color: red;
            margin-bottom: 10px;
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
        <h1>Your Profile</h1>
        <nav>
            <a href="Home.php">Home</a>
            <a href="Contact.php">Contact</a>
            <a href="Profile.php">Profile</a>
            <a href="Cart.php">My Cart</a>
        </nav>
    </header>
    <main>
        <h2>Update Your Profile</h2>

        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?= htmlspecialchars($error_message) ?></p>
        <?php endif; ?>

        <form action="ProfileUpdate.php" method="POST">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($phone) ?>" required>
            </div>

            <div class="form-group">
                <label for="date_of_birth">Date of Birth:</label>
                <input type="date" id="date_of_birth" name="date_of_birth" value="<?= htmlspecialchars($date_of_birth) ?>" required>
            </div>

            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($username) ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter new password (optional)">
            </div>

            <button type="submit" class="btn">Update</button>
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
