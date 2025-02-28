<?php
session_start();
require 'db.php';

// 检查用户是否登录
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// 获取当前用户和题目 ID
$user_id = $_SESSION['user_id'];
if (isset($_GET['id'])) {
    $challenge_id = $_GET['id'];

    // 查询题目信息
    $stmt = $pdo->prepare("SELECT * FROM challenges WHERE id = ?");
    $stmt->execute([$challenge_id]);
    $challenge = $stmt->fetch();

    // 如果题目存在
    if ($challenge) {
        $result = null; // 用于存储提交结果

        // 检查是否已经正确提交过
        $stmt = $pdo->prepare("SELECT status FROM submissions WHERE user_id = ? AND challenge_id = ? AND status = 'correct'");
        $stmt->execute([$user_id, $challenge_id]);
        $alreadyCorrect = $stmt->fetch();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $submittedFlag = $_POST['flag'];

            if ($alreadyCorrect) {
                // 如果已经正确提交
                $result = "你已经正确提交过该 Flag，不能重复提交！";
            } else {
                // 判断提交的 Flag 是否正确
                $status = ($submittedFlag === $challenge['flag']) ? 'correct' : 'incorrect';

                // 插入或更新记录
                $stmt = $pdo->prepare("
                    INSERT INTO submissions (user_id, challenge_id, status, submitted_at)
                    VALUES (?, ?, ?, CURRENT_TIMESTAMP)
                    ON DUPLICATE KEY UPDATE status = VALUES(status), submitted_at = CURRENT_TIMESTAMP
                ");
                $stmt->execute([$user_id, $challenge_id, $status]);

                if ($status === 'correct') {
                    $result = "正确！🎉 恭喜你提交了正确的 Flag！";
                } else {
                    $result = "错误！❌ 提交的 Flag 不正确，请再试一次！";
                }
            }
        }
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo htmlspecialchars($challenge['title']); ?></title>
            <style>
                /* 页面背景 */
                body {
                    font-family: Arial, sans-serif;
                    background: url('https://img1.baidu.com/it/u=110481173,3352985316&fm=253&fmt=auto&app=120&f=JPEG?w=1422&h=800') no-repeat center center fixed;
                    background-size: cover;
                    margin: 0;
                    padding: 0;
                    height: 100vh;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                }

                /* 内容框 */
                .content {
                    background-color: rgba(255, 255, 255, 0.8);
                    padding: 30px;
                    border-radius: 10px;
                    width: 80%;
                    max-width: 600px;
                    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                }

                h1 {
                    text-align: center;
                    color: #333;
                }

                .info {
                    margin-top: 20px;
                    padding: 15px;
                    border: 1px solid #ddd;
                    border-radius: 5px;
                    background-color: #f9f9f9;
                }

                .result {
                    margin-top: 20px;
                    padding: 15px;
                    border-radius: 5px;
                    font-weight: bold;
                    text-align: center;
                }

                .correct {
                    background-color: #d4edda;
                    color: #155724;
                }

                .incorrect {
                    background-color: #f8d7da;
                    color: #721c24;
                }

                form {
                    margin-top: 20px;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                }

                input[type="text"] {
                    padding: 10px;
                    width: 80%;
                    margin-bottom: 10px;
                    border-radius: 5px;
                    border: 1px solid #ccc;
                    font-size: 16px;
                }

                button {
                    padding: 10px 20px;
                    background-color: #007BFF;
                    color: white;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    font-size: 16px;
                    transition: background-color 0.3s ease;
                }

                button:hover {
                    background-color: #0056b3;
                }

                a {
                    color: #007BFF;
                    text-decoration: none;
                    font-weight: bold;
                }

                a:hover {
                    text-decoration: underline;
                }
            </style>
        </head>
        <body>
            <div class="content">
                <h1><?php echo htmlspecialchars($challenge['title']); ?></h1>
                <div class="info">
                    <p><strong>模块：</strong> <?php echo htmlspecialchars($challenge['module']); ?></p>
                    <p><strong>分数：</strong> <?php echo htmlspecialchars($challenge['score']); ?> 分</p>
                    <?php if ($challenge['url']): ?>
                        <p><strong>题目链接：</strong> <a href="<?php echo htmlspecialchars($challenge['url']); ?>" target="_blank"><?php echo htmlspecialchars($challenge['url']); ?></a></p>
                    <?php endif; ?>
                </div>

                <?php if ($alreadyCorrect): ?>
                    <p>你已经正确提交过该题目。</p>
                <?php else: ?>
                    <!-- 提交 Flag 表单 -->
                    <form method="POST" action="">
                        <label for="flag">请输入 Flag：</label>
                        <input type="text" name="flag" id="flag" required>
                        <button type="submit">提交</button>
                    </form>
                <?php endif; ?>

                <!-- 显示结果 -->
                <?php if ($result): ?>
                    <div class="result <?php echo (strpos($result, '正确') !== false) ? 'correct' : 'incorrect'; ?>">
                        <?php echo $result; ?>
                    </div>
                <?php endif; ?>

                <div style="margin-top: 20px; text-align: center;">
                    <a href="http://10.225.31.17:81/">返回题目列表</a>
                </div>
            </div>
        </body>
        </html>
        <?php
    } else {
        echo "题目未找到！";
    }
} else {
    echo "无效的请求！";
}
?>
