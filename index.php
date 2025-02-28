<?php
session_start();
require 'db.php';

// 检查是否登录
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // 跳转到登录页面
    exit;
}

$user_id = $_SESSION['user_id'];

// 查询模块并获取所有题目
$module = isset($_GET['module']) ? $_GET['module'] : 'web'; // 默认模块为 web
$stmt = $pdo->prepare("SELECT * FROM challenges WHERE module = ?");
$stmt->execute([$module]);
$challenges = $stmt->fetchAll();

// 查询用户已完成的题目（status = correct）
$completed_stmt = $pdo->prepare("SELECT challenge_id FROM submissions WHERE user_id = ? AND status = 'correct'");
$completed_stmt->execute([$user_id]);
$completed_challenges = $completed_stmt->fetchAll(PDO::FETCH_COLUMN);

// 定义模块列表
$modules = [
    'web' => 'Web',
    'misc' => 'Misc',
    'pwn' => 'Pwn',
    're' => 'Reverse'
];
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>题目模块</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: url('https://img1.baidu.com/it/u=110481173,3352985316&fm=253&fmt=auto&app=120&f=JPEG?w=1422&h=800') no-repeat center center fixed; /* 替换此URL */
            background-size: cover;
            color: #ffffff;
        }

        /* 顶部导航栏样式 */
        .navbar {
            background: linear-gradient(to right, rgba(0, 123, 205, 0.8), rgba(0, 100, 20, 0.8)); /* 渐变色背景 */
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 2px solid rgba(255, 255, 255, 0.2); /* 加点边框 */
        }

        .navbar h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }

        .navbar ul {
            list-style: none;
            display: flex;
            margin: 0;
            padding: 0;
            gap: 20px;
        }

        .navbar ul li {
            display: inline;
        }

        .navbar ul li a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .navbar ul li a:hover {
            background-color: rgba(255, 255, 255, 0.2); /* 鼠标悬停高亮 */
        }

        /* 主内容布局 */
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.6); /* 半透明黑色背景 */
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .module-switch {
            margin-bottom: 20px;
            text-align: center;
        }

        .module-switch a {
            margin-right: 15px;
            padding: 10px 15px;
            color: #FFD700;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.2); /* 模块按钮背景 */
            transition: all 0.3s ease;
        }

        .module-switch a:hover {
            background: rgba(255, 255, 255, 0.4);
            color: white;
        }

        .challenge-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .challenge-card {
            background: linear-gradient(145deg, rgba(74, 144, 226, 0.9), rgba(50, 100, 200, 0.9)); /* 默认蓝色渐变背景 */
            color: white;
            padding: 20px;
            width: 220px;
            text-align: center;
            border-radius: 15px;
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .challenge-card.completed {
            background: linear-gradient(145deg, rgba(255, 100, 200, 0.9), rgba(255, 150, 200, 0.9)); /* 已完成粉色渐变背景 */
        }

        .challenge-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.4); /* 鼠标悬停效果 */
        }

        .challenge-card h3 {
            margin-top: 0;
            font-size: 20px;
        }

        .challenge-card a {
            color: white;
            text-decoration: underline;
        }

        .challenge-card a:hover {
            color: #FFD700;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            color: #ddd;
            padding: 10px 0;
            background-color: rgba(0, 0, 0, 0.8);
            border-top: 1px solid #333;
        }
    </style>
</head>
<body>
    <!-- 顶部导航栏 -->
    <div class="navbar">
        <h1>喂不饱ctf平台</h1>
        <ul>
            <li><a href="index.php?module=web">题目模块</a></li>
            <li><a href="leaderboard.php">用户排行榜</a></li>
            <li><a href="login.php">退出登录</a></li>
        </ul>
    </div>

    <!-- 主内容 -->
    <div class="container">
        <!-- 模块切换 -->
        <div class="module-switch">
            <?php foreach ($modules as $key => $value): ?>
                <a href="index.php?module=<?php echo $key; ?>"><?php echo $value; ?></a>
            <?php endforeach; ?>
        </div>

        <!-- 当前模块标题 -->
        <h2 style="text-align:center;"><?php echo $modules[$module]; ?> 模块</h2>

        <!-- 题目展示 -->
        <div class="challenge-container">
            <?php foreach ($challenges as $challenge): ?>
                <div class="challenge-card <?php echo in_array($challenge['id'], $completed_challenges) ? 'completed' : ''; ?>">
                    <h3><?php echo htmlspecialchars($challenge['title']); ?></h3>
                    <p>模块: <?php echo htmlspecialchars($challenge['module']); ?></p>
                    <p><a href="download.php?id=<?php echo $challenge['id']; ?>">下载文件</a></p>
                    <p>URL: <a href="challenge.php?id=<?php echo $challenge['id']; ?>" target="_blank">查看详情</a></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 页脚 -->
    <div class="footer">
    公告栏
  <br>在主站的index.php中添加内容（注意使用br标签）
    </div>
</body>
</html>
