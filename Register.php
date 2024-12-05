<?php
// Bật hiển thị lỗi (debug)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Kết nối CSDL
$conn = new mysqli('localhost', 'root', '', 'dienthoai');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Biến thông báo
$success = "";
$error = [];

// Xử lý khi gửi form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Kiểm tra nhập liệu
    if (empty($full_name) || empty($phone) || empty($email) || empty($dob) || empty($username) || empty($password) || empty($confirm_password)) {
        $error[] = "Please fill in all required fields.";
    }
    if ($password !== $confirm_password) {
        $error[] = "Password confirmation does not match.";
    }
    if (!preg_match('/^\d{10,15}$/', $phone)) {
        $error[] = "Invalid phone number.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error[] = "Invalid email format.";
    }

    // Kiểm tra trùng lặp email/username
    if (empty($error)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error[] = "Email or username already exists.";
        }
        $stmt->close();
    }

    // Lưu vào CSDL nếu không có lỗi
    if (empty($error)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT); // Mã hóa mật khẩu
        $stmt = $conn->prepare("INSERT INTO users (full_name, phone, email, dob, username, password) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $full_name, $phone, $email, $dob, $username, $hashed_password);

        if ($stmt->execute()) {
            $success = "Registration successful! You can log in now.";
        } else {
            $error[] = "Error saving data: " . $stmt->error;
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Buy & Sell Electronics</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #e3e6e8, #cfd3d6);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .error {
            color: #ff4d4f;
            font-size: 13px;
            margin-bottom: 10px;
        }
        .success {
            color: #28a745;
            text-align: center;
            margin-bottom: 10px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .info-link {
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Register</h2>

        <!-- Hiển thị lỗi -->
        <?php if (!empty($error)): ?>
            <div class="error">
                <?php foreach ($error as $message): ?>
                    <p><?php echo htmlspecialchars($message); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Hiển thị thành công -->
        <?php if (!empty($success)): ?>
            <div class="success">
                <?php echo htmlspecialchars($success); ?>
                <p><a href="login.php" style="color: #007bff;">Log in now</a></p>
            </div>
        <?php else: ?>
            <form method="post">
                <div class="form-group">
                    <label for="full_name">Full Name:</label>
                    <input type="text" id="full_name" name="full_name" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number:</label>
                    <input type="text" id="phone" name="phone" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="dob">Date of Birth:</label>
                    <input type="date" id="dob" name="dob" required>
                </div>
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit">Register</button>
            </form>
        <?php endif; ?>

        <div class="info-link">
            <p>Already have an account? <a href="login.php">Log in now</a></p>
        </div>
    </div>
</body>
</html>
