<?php
// profile.php
require_once 'config.php';
checkLogin();
// Fetch user information
$stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
// Fetch completed lessons count for HTML
$htmlLessonsStmt = $pdo->prepare("
    SELECT COUNT(*) as completed_count 
    FROM progress 
    WHERE user_id = ? AND language = 'html' AND completed = 1
");
$htmlLessonsStmt->execute([$_SESSION['user_id']]);
$htmlLessons = $htmlLessonsStmt->fetch();
// Fetch completed lessons count for CSS
$cssLessonsStmt = $pdo->prepare("
    SELECT COUNT(*) as completed_count 
    FROM progress 
    WHERE user_id = ? AND language = 'css' AND completed = 1
");
$cssLessonsStmt->execute([$_SESSION['user_id']]);
$cssLessons = $cssLessonsStmt->fetch();
// Fetch completed lessons count for php
$phpLessonsStmt = $pdo->prepare("
    SELECT COUNT(*) as completed_count 
    FROM progress 
    WHERE user_id = ? AND language = 'php' AND completed = 1
");
$phpLessonsStmt->execute([$_SESSION['user_id']]);
$phpLessons = $phpLessonsStmt->fetch();
// Get total scores for each language
$scoresStmt = $pdo->prepare("
    SELECT language, SUM(score) as total_score 
    FROM progress 
    WHERE user_id = ? 
    GROUP BY language
");
$scoresStmt->execute([$_SESSION['user_id']]);
$scores = [];
while ($row = $scoresStmt->fetch()) {
    $scores[$row['language']] = $row['total_score'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@300;400;500;600;700&family=IBM+Plex+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&family=Noto+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Font settings */
        :root {
            --font-thai: 'IBM Plex Sans Thai', 'Noto Sans Thai', sans-serif;
            --font-english: 'IBM Plex Sans', 'Noto Sans', sans-serif;
        }
        body {
            font-family: var(--font-thai);
        }
        /* Use English font for specific elements */
        input, 
        .font-english {
            font-family: var(--font-english);
        }
        /* Combined font stack for mixed content */
        .mixed-text {
            font-family: var(--font-english), var(--font-thai);
        }
        </style>
</head>
<body class="bg-gray-900">
    <div class="container mx-auto px-4">
        <!-- Header with navigation -->
        <div class="flex justify-between items-center mb-8 pb-6 border-b bg-white shadow p-5 mb-5 rounded">
            <div class="flex items-center">
            <a href="dashboard.php">
                <img src="img/devlab.png" alt="DevLab Logo" class="h-10 mr-4">
            </a>
            </div>
            <div class="flex items-center gap-4">
                <a href="dashboard.php" class="text-blue-500 hover:text-blue-700">กลับไปยังแดชบอร์ด</a>
                <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Logout</a>
            </div>
        </div>
        <!-- Profile Content -->
        <div class="bg-white rounded-lg shadow-md p-6 mx-auto">
            <div class="text-center mb-6">
                <div class="w-24 h-24 bg-blue-500 rounded-full mx-auto mb-4 flex items-center justify-center">
                    <span class="text-3xl text-white uppercase"><?php echo substr($user['username'], 0, 1); ?></span>
                </div>
                <h1 class="text-2xl font-bold"><?php echo htmlspecialchars($user['username']); ?></h1>
                <p class="text-gray-600"><?php echo htmlspecialchars($user['email']); ?></p>
            </div>
            <div class="mt-4 flex justify-center">
            <a href="change_password.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">เปลี่ยนรหัสผ่าน</a>
        </div>
            <div class="grid grid-cols-2 md:grid-cols-2 gap-6 mt-8">
                <!-- HTML Progress -->
                <div class="bg-gray-100 p-6 rounded-lg">
                    <h2 class="text-xl font-semibold mb-4">ความก้าวหน้าของ HTML</h2>
                    <div class="space-y-2">
                        <p>บทเรียนที่เสร็จสิ้น: <?php echo $htmlLessons['completed_count']; ?> / 20</p>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-green-600 h-2.5 rounded-full" 
                                 style="width: <?php echo ($htmlLessons['completed_count'] / 20 * 100); ?>%"></div>
                        </div>
                        <p class="text-sm text-gray-600">คะแนนรวม: <?php echo isset($scores['html']) ? $scores['html'] : 0; ?></p>
                    </div>
                </div>
                <!-- CSS Progress -->
                <div class="bg-gray-100 p-6 rounded-lg">
                    <h2 class="text-xl font-semibold mb-4">ความก้าวหน้าของ CSS</h2>
                    <div class="space-y-2">
                        <p>บทเรียนที่เสร็จสิ้น: <?php echo $cssLessons['completed_count']; ?> / 20</p>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-green-600 h-2.5 rounded-full" 
                                 style="width: <?php echo ($cssLessons['completed_count'] / 20 * 100); ?>%"></div>
                        </div>
                        <p class="text-sm text-gray-600">คะแนนรวม: <?php echo isset($scores['css']) ? $scores['css'] : 0; ?></p>
                    </div>
                </div>
                <!-- PHP Progress -->
                <div class="bg-gray-100 p-6 rounded-lg">
                    <h2 class="text-xl font-semibold mb-4">ความก้าวหน้าของ PHP</h2>
                    <div class="space-y-2">
                        <p>บทเรียนที่เสร็จสิ้น: <?php echo isset($phpLessons['completed_count']) ? $phpLessons['completed_count'] : 0; ?> / 20</p>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-green-600 h-2.5 rounded-full" 
                                 style="width: <?php echo isset($phpLessons['completed_count']) ? ($phpLessons['completed_count'] / 20 * 100) : 0; ?>%"></div>
                        </div>
                        <p class="text-sm text-gray-600">คะแนนรวม: <?php echo isset($scores['php']) ? $scores['php'] : 0; ?></p>
                    </div>
                </div>
            </div>
            <!-- Overall Statistics -->
            <div class="mt-8 p-6 bg-gray-100 rounded-lg">
                <h2 class="text-xl font-semibold mb-4">ความคืบหน้าโดยรวม</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <p class="text-gray-600">จำนวนบทเรียนที่สำเร็จทั้งหมด</p>
                        <p class="text-2xl font-bold"><?php echo $htmlLessons['completed_count'] + $cssLessons['completed_count'] + $phpLessons['completed_count']; ?></p>
                    </div>
                    <div class="text-center">
                        <p class="text-gray-600">คะแนนรวม</p>
                        <p class="text-2xl font-bold"><?php echo array_sum($scores); ?></p>
                    </div>
                    <div class="text-center">
                        <p class="text-gray-600">จำนวนภาษา</p>
                        <p class="text-2xl font-bold">3</p>
                    </div>
                    <div class="text-center">
                        <p class="text-gray-600">ความคืบหน้า</p>
                        <p class="text-2xl font-bold"><?php echo round((($htmlLessons['completed_count'] + $cssLessons['completed_count']+ $phpLessons['completed_count']) / 60) * 100); ?>%</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>