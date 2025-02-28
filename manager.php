<?php
session_start();
require 'db.php';

// 检查是否是管理员
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

// 获取所有题目
$stmt = $pdo->query("SELECT * FROM challenges ORDER BY module ASC, id ASC");
$challenges = $stmt->fetchAll();

// 添加/修改/删除题目
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['action'] == 'add') {
        // 添加新题目
        $title = $_POST['title'] ?? null;
        $module = $_POST['module'] ?? null;
        $flag = $_POST['flag'] ?? null;
        $url = $_POST['url'] ?? null;
        $file_url = $_POST['file_url'] ?? null; // 接收文件下载地址
        $score = $_POST['score'] ?? 0; // 接收分数值

        $stmt = $pdo->prepare("INSERT INTO challenges (title, module, flag, url, file_path, score) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $module, $flag, $url, $file_url, $score]);
    } elseif ($_POST['action'] == 'edit') {
        // 修改题目
        $id = $_POST['id'];
        $title = $_POST['title'] ?? null;
        $module = $_POST['module'] ?? null;
        $flag = $_POST['flag'] ?? null;
        $url = $_POST['url'] ?? null;
        $file_url = $_POST['file_url'] ?? null; // 接收文件下载地址
        $score = $_POST['score'] ?? 0; // 接收分数值

        $stmt = $pdo->prepare("UPDATE challenges SET title = ?, module = ?, flag = ?, url = ?, file_path = ?, score = ? WHERE id = ?");
        $stmt->execute([$title, $module, $flag, $url, $file_url, $score, $id]);
    } elseif ($_POST['action'] == 'delete') {
        // 删除题目
        $id = $_POST['id'];

        // 从数据库删除记录
        $stmt = $pdo->prepare("DELETE FROM challenges WHERE id = ?");
        $stmt->execute([$id]);
    }
}

// 按模块分类题目
$modules = ['web' => 'Web', 'misc' => 'Misc', 'pwn' => 'Pwn', 're' => '逆向'];
$categorized_challenges = [];
foreach ($challenges as $challenge) {
    $categorized_challenges[$challenge['module']][] = $challenge;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理题目</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .form-container {
            margin-bottom: 30px;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-container input, .form-container select, .form-container button {
            margin-top: 10px;
            padding: 10px;
            width: 100%;
            box-sizing: border-box;
        }
        .card-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .card {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            width: 300px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }
        .card h3 {
            margin-top: 0;
        }
        .card p {
            margin: 5px 0;
        }
        .card form {
            margin-top: 10px;
        }
        .card input, .card select, .card button {
            margin-top: 5px;
            padding: 8px;
            width: 100%;
            box-sizing: border-box;
        }
        .card button {
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .card button:hover {
            background-color: #0056b3;
        }
        .card .delete-btn {
            background-color: #FF4C4C;
        }
        .card .delete-btn:hover {
            background-color: #c30000;
        }
    </style>
</head>
<body>
    <h1>管理题目</h1>

    <h2>添加题目</h2>
    <form class="form-container" method="POST" action="">
        <input type="hidden" name="action" value="add">
        题目名称: <input type="text" name="title" required>
        模块: 
        <select name="module">
            <option value="">-- 选择模块（可选） --</option>
            <option value="web">Web</option>
            <option value="misc">Misc</option>
            <option value="pwn">Pwn</option>
            <option value="re">逆向</option>
        </select>
        Flag: <input type="text" name="flag" placeholder="输入 Flag（可选）">
        URL: <input type="text" name="url" placeholder="输入题目 URL（任意形式，非必填）">
        文件下载地址: <input type="text" name="file_url" value="http://10.225.31.17:81/uploads/download.php?file=" placeholder="输入文件下载地址">
        分数值: <input type="number" name="score" min="0" value="0" placeholder="输入题目分数">
        <button type="submit">添加</button>
    </form>

    <h2>题目列表</h2>
    <div class="card-container">
        <?php foreach ($challenges as $challenge): ?>
            <div class="card">
                <h3><?php echo htmlspecialchars($challenge['title']); ?></h3>
                <p><strong>模块:</strong> <?php echo htmlspecialchars($challenge['module']); ?></p>
                <p><strong>分数:</strong> <?php echo htmlspecialchars($challenge['score']); ?></p>
                <?php if ($challenge['url']): ?>
                    <p><strong>链接:</strong> <a href="<?php echo htmlspecialchars($challenge['url']); ?>" target="_blank"><?php echo htmlspecialchars($challenge['url']); ?></a></p>
                <?php endif; ?>
                <?php if ($challenge['file_path']): ?>
                    <p><strong>文件地址:</strong> <a href="<?php echo htmlspecialchars($challenge['file_path']); ?>" target="_blank">下载</a></p>
                <?php endif; ?>

                <!-- 修改表单 -->
                <form method="POST" action="">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" value="<?php echo $challenge['id']; ?>">
                    题目名称: <input type="text" name="title" value="<?php echo htmlspecialchars($challenge['title']); ?>" required>
                    模块: 
                    <select name="module">
                        <option value="web" <?php echo $challenge['module'] == 'web' ? 'selected' : ''; ?>>Web</option>
                        <option value="misc" <?php echo $challenge['module'] == 'misc' ? 'selected' : ''; ?>>Misc</option>
                        <option value="pwn" <?php echo $challenge['module'] == 'pwn' ? 'selected' : ''; ?>>Pwn</option>
                        <option value="re" <?php echo $challenge['module'] == 're' ? 'selected' : ''; ?>>逆向</option>
                    </select>
                    Flag: <input type="text" name="flag" value="<?php echo htmlspecialchars($challenge['flag']); ?>">
                    URL: <input type="text" name="url" value="<?php echo htmlspecialchars($challenge['url']); ?>">
                    文件下载地址: <input type="text" name="file_url" value="<?php echo htmlspecialchars($challenge['file_path']); ?>">
                    分数值: <input type="number" name="score" min="0" value="<?php echo htmlspecialchars($challenge['score']); ?>">
                    <button type="submit">修改</button>
                </form>

                <!-- 删除表单 -->
                <form method="POST" action="">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?php echo $challenge['id']; ?>">
                    <button type="submit" class="delete-btn">删除</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
