<?php
$host = '127.0.0.1';  // 数据库主机
$db = 'test'; // 数据库名称
$user = 'root';       // 数据库用户名
$pass = '123456';  // 数据库密码

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("连接失败: " . $e->getMessage());
}
?>

