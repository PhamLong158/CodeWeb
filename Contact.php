<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];   
    $email = $_POST['email'];   
    $message = $_POST['message'];

    $query = "INSERT INTO contacts (name, email, message) VALUES (:name, :email, :message)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':message', $message);
    $stmt->execute();

    echo "<script>alert('Message sent successfully!');</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Phone Store</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa; /* Nền màu trắng nhạt */
        }
        header {
            background-color: #333;
            color: white;
            padding: 15px;
            text-align: center;
        }
        header nav a {
            color: white;
            margin-right: 15px;
            text-decoration: none;
        }
        main {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            width: 100%;
            max-width: 400px;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        label {
            font-weight: bold;
        }
        input, textarea, button {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #333;
            color: white;
            font-size: 16px;
            cursor: pointer;
            border: none;
        }
        button:hover {
            background-color: #555;
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
        <h1>Contact Us</h1>
        <nav>
            <a href="home.php">Home</a>
            <a href="contact.php">Contact</a>
            <a href="profile.php">Profile</a>
            <a href="cart.php">My Cart</a>
        </nav>
    </header>
    <main>
        <form method="POST">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="message">Message:</label>
            <textarea id="message" name="message" rows="5" required></textarea>
            
            <button type="submit">Submit</button>
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
