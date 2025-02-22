<?php
require_once 'config.php';
checkLogin();
// Fetch total scores and rankings for all users
$stmt = $pdo->prepare("
    SELECT 
        u.username,
        SUM(p.score) as total_score,
        COUNT(DISTINCT CASE WHEN p.completed = 1 THEN p.lesson_id END) as completed_lessons,
        SUM(CASE WHEN p.language = 'html' AND p.completed = 1 THEN p.score ELSE 0 END) as html_score,
        SUM(CASE WHEN p.language = 'css' AND p.completed = 1 THEN p.score ELSE 0 END) as css_score,
        SUM(CASE WHEN p.language = 'php' AND p.completed = 1 THEN p.score ELSE 0 END) as php_score
    FROM users u
    LEFT JOIN progress p ON u.id = p.user_id
    GROUP BY u.id, u.username
    ORDER BY total_score DESC, completed_lessons DESC
");
$stmt->execute();
$rankings = $stmt->fetchAll();
// Get current user's ranking
$currentUserRank = 0;
$currentUsername = $_SESSION['username'];
foreach ($rankings as $index => $rank) {
    if ($rank['username'] === $currentUsername) {
        $currentUserRank = $index + 1;
        break;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Leaderboard - DevLab</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@300;400;500;600;700&family=IBM+Plex+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --font-thai: 'IBM Plex Sans Thai', 'Noto Sans Thai', sans-serif;
            --font-english: 'IBM Plex Sans', 'Noto Sans', sans-serif;
        }
        body {
            font-family: var(--font-thai);
        }
        .font-english {
            font-family: var(--font-english);
        }
        .rank-card {
            transition: transform 0.2s ease;
        }
        .rank-card:hover {
            transform: translateY(-2px);
        }
        .animate-shine {
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            background-size: 200% 100%;
            animation: shine 1.5s infinite;
        }
        @keyframes shine {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
    </style>
</head>
<body class="bg-gray-900">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8 pb-6 border-b bg-white shadow p-5 rounded">
            <div class="flex items-center">
                <a href="dashboard.php">
                    <img src="img/devlab.png" alt="DevLab Logo" class="h-10 mr-4">
                </a>
                <h1 class="text-2xl font-bold">Leaderboard</h1>
            </div>
            <div class="flex items-center gap-4">
                <a href="dashboard.php" class="text-blue-500 hover:underline">← Back to Dashboard</a>
                <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Logout</a>
            </div>
        </div>
        <!-- Your Ranking Card -->
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg shadow-lg p-6 mb-8">
            <div class="text-white">
                <h2 class="text-xl font-semibold mb-2">การจัดอันดับของคุณ</h2>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-6xl font-bold">#<?php echo $currentUserRank; ?></p>
                        <p class="text-2xl"><?php echo htmlspecialchars($currentUsername); ?></p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm">คะแนนรวม</p>
                        <p class="text-7xl font-bold">
                            <?php 
                            foreach ($rankings as $rank) {
                                if ($rank['username'] === $currentUsername) {
                                    echo number_format($rank['total_score']);
                                    break;
                                }
                            }
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Top 3 Players -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <?php for ($i = 0; $i < min(3, count($rankings)); $i++): 
                $medalColors = [
                    'bg-gradient-to-r from-yellow-300 to-yellow-500', // Gold
                    'bg-gradient-to-r from-gray-300 to-gray-400',     // Silver
                    'bg-gradient-to-r from-yellow-600 to-yellow-700'  // Bronze
                ];
                $medalNames = ['🥇 Gold', '🥈 Silver', '🥉 Bronze'];
            ?>
                <div class="<?php echo $medalColors[$i]; ?> rounded-lg p-6 shadow-lg rank-card">
                    <div class="text-white">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-sm font-semibold"><?php echo $medalNames[$i]; ?></p>
                                <h3 class="text-xl font-bold"><?php echo htmlspecialchars($rankings[$i]['username']); ?></h3>
                            </div>
                            <p class="text-3xl font-bold">#<?php echo $i + 1; ?></p>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span>คะแนนรวม:</span>
                                <span class="font-bold"><?php echo number_format($rankings[$i]['total_score']); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span>เสร็จสิ้น:</span>
                                <span class="font-bold"><?php echo $rankings[$i]['completed_lessons']; ?> บทเรียน</span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
        <!-- Full Rankings Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6 border-b">
                <h2 class="text-xl font-bold">อันดับทั้งหมด</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">อันดับ</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ชื่อ</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">คะแนน HTML </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">คะแนน CSS </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">คะแนน PHP </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">คะแนนรวม</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">บทเรียนที่เสร็จสิ้น</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($rankings as $index => $rank): ?>
                            <tr class="<?php echo $rank['username'] === $currentUsername ? 'bg-blue-100' : ''; ?> hover:bg-gray-100">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">#<?php echo $index + 1; ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?php echo htmlspecialchars($rank['username']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?php echo number_format($rank['html_score']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?php echo number_format($rank['css_score']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?php echo number_format($rank['php_score']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?php echo number_format($rank['total_score']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?php echo $rank['completed_lessons']; ?></div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>