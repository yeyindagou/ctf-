<?php
session_start();
require 'db.php'; // 连接数据库

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 密码加密
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 检查用户名是否已存在
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user) {
        $error = "用户名已存在！";
    } else {
        // 用户名不存在，插入新用户
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $hashed_password]);

        // 注册成功后跳转到登录页面
        header('Location: login.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>注册页面</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: white;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        .container h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .form-group input:focus {
            border-color: #007BFF;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.2);
        }
        .btn {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .error {
            color: #721c24;
            background-color: #f8d7da;
            padding: 10px;
            margin-top: 15px;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
        }
        .link {
            margin-top: 15px;
            text-align: center;
            font-size: 14px;
        }
        .link a {
            color: #007BFF;
            text-decoration: none;
        }
        .link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>注册</h1>
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">用户名</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div class="form-group">
                <label for="password">密码</label>
                <input type="password" name="password" id="password" required>
            </div>
            <button type="submit" class="btn">注册</button>
        </form>
        <?php if (isset($error)): ?>
            <div class="error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        <div class="link">
            已经有账号？ <a href="login.php">点击登录</a>
        </div>
    </div>
</body>
</html>
