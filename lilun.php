<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>8412测试理论题</title>
</head>
<body>
    <h1>8412测试理论题</h1>

    <?php
    // 定义问题和选项
    $questions = [
        [
            'question' => '在Linux中，哪个命令用于列出目录的内容？',
            'options' => ['A. dir', 'B. ls', 'C. show', 'D. view'],
            'correct' => 'B'
        ],
        [
            'question' => '哪个命令可以切换当前工作目录？',
            'options' => ['A. move', 'B. switch', 'C. cd', 'D. pwd'],
            'correct' => 'C'
        ],
        [
            'question' => '以下哪个命令用于查看当前用户的工作目录？',
            'options' => ['A. pwd', 'B. dir', 'C. path', 'D. where'],
            'correct' => 'A'
        ],
        [
            'question' => 'Linux中，哪个命令用于复制文件？',
            'options' => ['A. cp', 'B. mv', 'C. copy', 'D. duplicate'],
            'correct' => 'A'
        ],
        [
            'question' => '在Linux中，如何显示一个文件的内容？',
            'options' => ['A. open', 'B. cat', 'C. read', 'D. type'],
            'correct' => 'B'
        ],
        [
            'question' => '哪个命令可以显示当前系统时间和日期？',
            'options' => ['A. time', 'B. date', 'C. clock', 'D. cal'],
            'correct' => 'B'
        ],
        [
            'question' => '以下哪个命令可以删除一个文件？',
            'options' => ['A. remove', 'B. delete', 'C. rm', 'D. erase'],
            'correct' => 'C'
        ],
        [
            'question' => '哪个命令可以查看当前系统的运行进程？',
            'options' => ['A. jobs', 'B. top', 'C. process', 'D. run'],
            'correct' => 'B'
        ],
        [
            'question' => '在Linux中，如何更改文件权限？',
            'options' => ['A. chmod', 'B. chown', 'C. perm', 'D. rights'],
            'correct' => 'A'
        ],
        [
            'question' => '以下哪个命令用于查看文件的第一部分内容？',
            'options' => ['A. head', 'B. start', 'C. top', 'D. begin'],
            'correct' => 'A'
        ],
        [
            'question' => '在Windows中，哪个命令可以显示当前目录的内容？',
            'options' => ['A. ls', 'B. dir', 'C. show', 'D. list'],
            'correct' => 'B'
        ],
        [
            'question' => '如何在Windows命令提示符中更改目录？',
            'options' => ['A. move', 'B. switch', 'C. cd', 'D. pwd'],
            'correct' => 'C'
        ],
        [
            'question' => '哪个命令用于清除命令提示符窗口的内容？',
            'options' => ['A. erase', 'B. cls', 'C. clear', 'D. reset'],
            'correct' => 'B'
        ],
        [
            'question' => '在Windows中，如何查看当前的IP配置？',
            'options' => ['A. ipconfig', 'B. ifconfig', 'C. netstat', 'D. ping'],
            'correct' => 'A'
        ],
        [
            'question' => '以下哪个命令用于检查网络连接？',
            'options' => ['A. netstat', 'B. ping', 'C. ipconfig', 'D. traceroute'],
            'correct' => 'B'
        ],
        [
            'question' => '在Windows中，如何创建一个新的文件夹？',
            'options' => ['A. mkdir', 'B. folder', 'C. create', 'D. newdir'],
            'correct' => 'A'
        ],
        [
            'question' => '哪个命令用于重命名一个文件或文件夹？',
            'options' => ['A. rename', 'B. move', 'C. ren', 'D. change'],
            'correct' => 'C'
        ],
        [
            'question' => '如何查看硬盘上剩余的空间？',
            'options' => ['A. diskpart', 'B. dir', 'C. fsutil', 'D. chkdsk'],
            'correct' => 'D'
        ],
        [
            'question' => '在Windows中，哪个命令用于删除一个文件？',
            'options' => ['A. rm', 'B. del', 'C. delete', 'D. erase'],
            'correct' => 'B'
        ],
        [
            'question' => '哪个命令用于显示系统的版本信息？',
            'options' => ['A. version', 'B. ver', 'C. sysinfo', 'D. info'],
            'correct' => 'B'
        ]
    ];

    // 初始化得分
    $score = 0;

    // 检查是否有表单提交
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // 遍历问题并检查答案
        foreach ($questions as $index => $question) {
            $userAnswer = $_POST["q$index"] ?? null; // 用户的答案
            $correctAnswer = $question['correct']; // 正确答案

            echo "<p>问题 " . ($index + 1) . ": " . $question['question'] . "</p>";
            echo "<p>你的答案: " . ($userAnswer ?? "未回答") . "</p>";

            if ($userAnswer === $correctAnswer) {
                echo "<p style='color: green;'>回答正确！</p>";
                $score += 5;
            } else {
                echo "<p style='color: red;'>回答错误！正确答案是: $correctAnswer</p>";
            }
        }

        // 显示总分
        echo "<h2>测试结束！你的总分是：$score 分。</h2>";
    } else {
        // 显示答题表单
        echo '<form method="POST">';
        foreach ($questions as $index => $question) {
            echo "<p>问题 " . ($index + 1) . ": " . $question['question'] . "</p>";
            foreach ($question['options'] as $option) {
                $value = substr($option, 0, 1); // A/B/C/D
                echo "<label><input type='radio' name='q$index' value='$value'> $option</label><br>";
            }
        }
        echo '<br><button type="submit">提交答案</button>';
        echo '</form>';
    }
    ?>

</body>
</html>
