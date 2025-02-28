<?php
session_start();
require 'db.php';

// 检查是否登录
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// 查询所有用户的分数，同时包括用户ID
$query = "
    SELECT users.id AS user_id, 
           users.username, 
           SUM(challenges.score) AS total_score
    FROM users 
    LEFT JOIN submissions 
        ON users.id = submissions.user_id 
        AND submissions.status = 'correct'
    LEFT JOIN challenges 
        ON submissions.challenge_id = challenges.id
    GROUP BY users.id
    ORDER BY total_score DESC, users.username ASC;
";
$stmt = $pdo->query($query);
$leaderboard = $stmt->fetchAll();

// 查询所有题目
$challenges_query = "SELECT * FROM challenges ORDER BY id ASC";
$challenges_stmt = $pdo->query($challenges_query);
$challenges = $challenges_stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用户排行榜</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-image: url('https://img1.baidu.com/it/u=110481173,3352985316&fm=253&fmt=auto&app=120&f=JPEG?w=1422&h=800');
            background-size: cover;
            color: #fff;
        }
        h1 {
            text-align: center;
            color: #fff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px auto;
            text-align: left;
            background-color: rgba(0, 0, 0, 0.7);
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            color: #fff;
        }
        th {
            background-color: #333;
        }
        tr:nth-child(even) {
            background-color: rgba(255, 255, 255, 0.1);
        }
        tr:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        .rank {
            font-weight: bold;
            color: #4A90E2;
        }
        .checkmark {
            color: #32CD32;
        }
        .empty {
            color: #D3D3D3;
        }
    </style>
</head>
<body>
    <h1>用户排行榜</h1>
    <table>
        <thead>
            <tr>
                <th>排名</th>
                <th>用户名</th>
                <th>总分</th>
                <?php foreach ($challenges as $challenge): ?>
                    <th><?php echo htmlspecialchars($challenge['title']); ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php $rank = 1; foreach ($leaderboard as $user): ?>
                <tr>
                    <td class="rank">#<?php echo $rank++; ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo $user['total_score'] ? $user['total_score'] : 0; ?> 分</td>
                    <?php foreach ($challenges as $challenge): ?>
                        <td>
                            <?php 
                            // 检查该用户是否完成了该题目
                            $check_query = "
                                SELECT COUNT(*) 
                                FROM submissions 
                                WHERE user_id = :user_id 
                                AND challenge_id = :challenge_id 
                                AND status = 'correct'
                            ";
                            $check_stmt = $pdo->prepare($check_query);
                            $check_stmt->execute([
                                ':user_id' => $user['user_id'], // 修正为正确的用户ID
                                ':challenge_id' => $challenge['id']
                            ]);
                            $completed = $check_stmt->fetchColumn();
                            if ($completed > 0) {
                                echo '<span class="checkmark">✔</span>';
                            } else {
                                echo '<span class="empty">✘</span>';
                            }
                            ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
