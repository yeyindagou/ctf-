<?php
require 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // 查询题目信息
    $stmt = $pdo->prepare("SELECT * FROM challenges WHERE id = ?");
    $stmt->execute([$id]);
    $challenge = $stmt->fetch();

    if ($challenge) {
        $fileUrl = $challenge['file_path']; // 数据库中的文件下载地址
        if ($fileUrl) {
            // 跳转到文件下载地址
            header('Location: ' . $fileUrl);
            exit;
        } else {
            echo "文件下载地址不存在。";
        }
    } else {
        echo "题目未找到。";
    }
} else {
    echo "无效的请求。";
}
