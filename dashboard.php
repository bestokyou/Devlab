<?php
// dashboard.php
require_once 'config.php';
checkLogin();
// ดึงคะแนนรวมของแต่ละภาษา
$stmt = $pdo->prepare("SELECT language, SUM(score) as total_score FROM progress WHERE user_id = ? GROUP BY language");
$stmt->execute([$_SESSION['user_id']]);
$scores = $stmt->fetchAll();
// ดึงบทเรียนที่ผ่านแล้ว
$completedLessons = $pdo->prepare("
    SELECT language, COUNT(*) as completed 
    FROM progress 
    WHERE user_id = ? AND completed = 1 
    GROUP BY language
");
$completedLessons->execute([$_SESSION['user_id']]);
$completed = $completedLessons->fetchAll(PDO::FETCH_KEY_PAIR);
// ฟังก์ชั่นสำหรับดึงคะแนนของแต่ละภาษา
function getLanguageScore($scores, $language) {
    $filtered = array_filter($scores, function($s) use ($language) { 
        return $s['language'] == $language; 
    });
    return !empty($filtered) ? current($filtered)['total_score'] : 0;
}
// ฟังก์ชั่นสำหรับดึงจำนวนบทเรียนที่ผ่านแล้ว
function getCompletedLessons($completed, $language) {
    return isset($completed[$language]) ? $completed[$language] : 0;
}
$totalScore = !empty($scores) ? array_sum(array_column($scores, 'total_score')) : 0;
$totalLessons = !empty($completed) ? array_sum($completed) : 0;
$maxScore = !empty($scores) ? max(array_column($scores, 'total_score')) : 0;
$completionRate = $totalLessons > 0 ? ($totalLessons / 60) * 100 : 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="icon" type="image/png" href="icon1.png">
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
<body class=" bg-gray-900">
    <div class="container mx-auto px-4  ">
    <div class="flex justify-between items-center mb-8 pb-6 border-b bg-white shadow p-5 mb-5 bg-body rounded">
            <div class="flex items-center">
            <a href="dashboard.php">
                <img src="img/devlab.png" alt="DevLab Logo" class="h-10 mr-4">
            </a>
            </div>
            <div class="flex items-center gap-9">
            ลงชื่อเข้าใช้โดย : <a href="profile.php" class="hover:underline bg-gray-100 px-6 py-1 "><span class="text-lg font-bold text-blue-500" ><?php echo htmlspecialchars($_SESSION['username']); ?></span>
            </a>
                <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Logout</a>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- HTML Card -->
            <div class="bg-white p-6 rounded-lg shadow-md">
            <img src="img/html.png" alt="DevLab Logo" class="h-10 mr-4 float-right">
                <h3 class="text-xl mb-4">HTML</h3>
                <div class="mb-4">
                    <p class="text-gray-600">ความคืบหน้า:
                        <?php echo getCompletedLessons($completed, 'html'); ?> บทเรียนเสร็จสิ้น
                    </p>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2">
                        <div class="bg-blue-600 h-2.5 rounded-full" 
                             style="width: <?php echo (getCompletedLessons($completed, 'html') / 20* 100); ?>%">
                        </div>
                    </div>
                </div>
                <p class="mb-4">คะแนน: <?php echo getLanguageScore($scores, 'html'); ?></p>
                <a href="dashboard_html_detail.php" class="bg-blue-500 text-white px-4 py-2 rounded block text-center hover:bg-blue-600">
                    เริ่มต้นการเรียนรู้ HTML
                </a>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
            <img src="img/css.png" alt="DevLab Logo" class="h-10 mr-4 float-right">
        <h3 class="text-xl mb-4">CSS</h3>
        <div class="mb-4">
            <p class="text-gray-600">ความคืบหน้า:
                <?php echo getCompletedLessons($completed, 'css'); ?> บทเรียนเสร็จสิ้น
            </p>
            <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2">
                <div class="bg-green-600 h-2.5 rounded-full" 
                     style="width: <?php echo (getCompletedLessons($completed, 'css') / 20 * 100); ?>%">
                </div>
            </div>
        </div>
            <p class="mb-4">คะแนน: <?php echo getLanguageScore($scores, 'css'); ?></p>
                <a href="dashboard_css_detail.php" class="bg-green-500 text-white px-4 py-2 rounded block text-center hover:bg-green-600">
                    เริ่มต้นการเรียนรู้ CS
                </a>
            </div>
    <div class="bg-white p-6 rounded-lg shadow-md">
            <img src="img/php.png" alt="DevLab Logo" class="h-10 mr-4 float-right">
        <h3 class="text-xl mb-4">PHP</h3>
        <div class="mb-4">
            <p class="text-gray-600">ความคืบหน้า:
                <?php echo getCompletedLessons($completed, 'php'); ?> บทเรียนเสร็จสิ้น
            </p>
            <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2">
                <div class="bg-yellow-600 h-2.5 rounded-full" 
                     style="width: <?php echo (getCompletedLessons($completed, 'php') / 20 * 100); ?>%">
                </div>
            </div>
        </div>
        <p class="mb-4">คะแนน: <?php echo getLanguageScore($scores, 'php'); ?></p>
        <a href="dashboard_php_detail.php" class="bg-yellow-500 text-white px-4 py-2 rounded block text-center hover:bg-yellow-600">
            เริ่มต้นการเรียนรู้ PHP
        </a>
        </div>
    </div>
<!-- Statistics Section -->
<div class="mt-20 mb-8">
        <h2 class="text-2xl font-bold text-white mb-6 text-center">สถิติการเรียนรู้ของคุณ</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Total Score Card -->
            <div class="bg-gradient-to-br from-purple-500 to-indigo-600 p-6 rounded-lg shadow-lg transform hover:scale-105 transition-all duration-300">
                <div class="text-white">
                    <h3 class="text-lg font-semibold mb-2">คะแนนรวม</h3>
                    <p class="text-3xl font-bold"><?php echo $totalScore; ?></p>
                </div>
            </div>
            <!-- Completed Lessons Card -->
            <div class="bg-gradient-to-br from-blue-500 to-teal-400 p-6 rounded-lg shadow-lg transform hover:scale-105 transition-all duration-300">
                <div class="text-white">
                    <h3 class="text-lg font-semibold mb-2">จำนวนบทเรียนที่สำเร็จทั้งหมด</h3>
                    <p class="text-3xl font-bold"><?php echo $totalLessons; ?>/60</p>
                </div>
            </div>
            <!-- Learning Streak Card -->
            <div class="bg-gradient-to-br from-green-500 to-emerald-400 p-6 rounded-lg shadow-lg transform hover:scale-105 transition-all duration-300">
                <div class="text-white">
                    <h3 class="text-lg font-semibold mb-2">คะแนนสูงสุด</h3>
                    <p class="text-3xl font-bold"><?php echo $maxScore; ?></p>
                </div>
            </div>
            <!-- Achievement Card -->
            <div class="bg-gradient-to-br from-pink-500 to-rose-400 p-6 rounded-lg shadow-lg transform hover:scale-105 transition-all duration-300">
                <div class="text-white">
                    <h3 class="text-lg font-semibold mb-2">อัตราความสำเร็จ</h3>
                    <p class="text-3xl font-bold"><?php echo round($completionRate, 1); ?>%</p>
                </div>
            </div>
            <!-- Leaderboard Card -->
        </div>
    </div>
        <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
    <a href="leaderboard.php" class="bg-gradient-to-br from-yellow-400 to-orange-500 p-6 rounded-lg shadow-lg transform hover:scale-105 transition-all duration-300">
                <div class="text-white">
                    <h3 class="text-lg font-semibold mb-2">Leaderboard</h3>
                    <p class="text-3xl font-bold">🏆</p>
                    <p class="text-sm mt-2">ดูอันดับผู้เรียน</p>
                </div>
            </a>
            </div>      
    <!-- Motivational Quote Section with Animation -->
    <div class="bg-white p-8 rounded-lg shadow-lg mt-8 mb-12 transform hover:shadow-2xl transition-all duration-300">
        <div class="text-center">
            <h3 class="text-2xl font-bold text-gray-800 mb-4 animate-fade-in">เรียนรู้อย่างต่อเนื่อง เติบโตอย่างต่อเนื่อง!</h3>
            <p class="text-gray-600 italic animate-slide-up">
                "สิ่งที่สวยงามเกี่ยวกับการเรียนรู้คือไม่มีใครสามารถพรากมันไปจากคุณได้"
            </p>
        </div>
    </div>
    <!-- Cartoon Characters Section -->
    <div class="relative mb-0 overflow-hidden h-28">
        <!-- Programming Character -->
        <div class="character-container absolute left-1/2 transform -translate-x-1/2 bottom-0 animate-float">
            <div class="character relative">
                <!-- Boy Character -->
            </div>
        </div>
        <!-- Floating Code Symbols -->
        <div class="code-symbols absolute w-full h-full">
            <span class="absolute text-blue-500 animate-float-1" style="left: 20%; top: 30%">&lt;/&gt;</span>
            <span class="absolute text-green-500 animate-float-2" style="left: 40%; top: 20%">{}</span>
            <span class="absolute text-purple-500 animate-float-3" style="left: 60%; top: 40%">#</span>
            <span class="absolute text-yellow-500 animate-float-2" style="left: 80%; top: 25%">( )</span>
        </div>
    </div>
    </div>
    <style>
        @keyframes fade-in {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slide-up {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0) translateX(-50%); }
            50% { transform: translateY(-10px) translateX(-50%); }
        }
        @keyframes bob {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }
        @keyframes wave {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-5deg); }
            75% { transform: rotate(5deg); }
        }
        @keyframes glow {
            0%, 100% { filter: brightness(1); }
            50% { filter: brightness(1.2); }
        }
        @keyframes float-1 {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(10deg); }
        }
        @keyframes float-2 {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(-10deg); }
        }
        @keyframes float-3 {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-10px) rotate(5deg); }
        }
        .animate-fade-in {
            animation: fade-in 1s ease-out;
        }
        .animate-slide-up {
            animation: slide-up 1s ease-out;
        }
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        .animate-bob {
            animation: bob 2s ease-in-out infinite;
        }
        .animate-wave {
            animation: wave 2s ease-in-out infinite;
        }
        .animate-glow {
            animation: glow 2s ease-in-out infinite;
        }
        .animate-float-1 {
            animation: float-1 3s ease-in-out infinite;
            font-size: 2rem;
            font-weight: bold;
        }
        .animate-float-2 {
            animation: float-2 4s ease-in-out infinite;
            font-size: 2rem;
            font-weight: bold;
        }
        .animate-float-3 {
            animation: float-3 3.5s ease-in-out infinite;
            font-size: 2rem;
            font-weight: bold;
        }
        .code-symbols span {
            opacity: 0.7;
        }
    </style>
    <style>
        @keyframes fade-in {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slide-up {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .animate-fade-in {
            animation: fade-in 1s ease-out;
        }
        .animate-slide-up {
            animation: slide-up 1s ease-out;
        }
    </style>
    </div>
</body>
</html>