<?php
require_once 'config.php';
checkLogin();
// Fetch all CSS lessons
$stmt = $pdo->prepare("
    SELECT l.*, 
           CASE WHEN p.completed = 1 THEN true ELSE false END as is_completed,
           CASE WHEN prev.completed = 1 OR l.language = 'css' AND l.id = 21 THEN true ELSE false END as is_available
    FROM lessons l
    LEFT JOIN progress p ON l.id = p.lesson_id AND p.user_id = ? AND p.language = l.language
    LEFT JOIN progress prev ON prev.lesson_id = l.id - 1 AND prev.user_id = ? AND prev.language = l.language
    WHERE l.language = 'css'
    ORDER BY l.order_num
");
$stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
$lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Get total completed lessons
$completedLessons = array_filter($lessons, function($lesson) {
    return $lesson['is_completed'];
});
$progress = count($completedLessons) / count($lessons) * 100;
?>
<!DOCTYPE html>
<html>
<head>
    <title>CSS Lessons - DevLab</title>
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
        .lesson-card {
            transition: all 0.3s ease;
        }
        .lesson-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .css-gradient {
            background: linear-gradient(135deg,rgb(245, 185, 57) 0%,rgb(231, 63, 34) 100%);
        }
    </style>
</head>
<body class="bg-gray-900">
    <div class="container mx-auto px-4 ">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8 pb-6 border-b bg-white shadow p-5 rounded">
            <div class="flex items-center">
            <a href="dashboard.php">
            <img src="img/devlab.png" alt="DevLab Logo" class="h-10 mr-4">
            </a>
                <h1 class="text-2xl font-bold">ภาพรวมหลักสูตร CSS</h1>
            </div>
            <div class="flex items-center gap-4">
                <a href="dashboard.php" class="text-blue-500 hover:underline">← กลับไปยังแดชบอร์ด</a>
                <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Logout</a>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- CSS Learning Resources Card -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold">แหล่งการเรียนรู้ CSS</h2>
            <a href="css_Knowledge.php" 
               class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-lg transition-colors flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                </svg>
                สื่อการเรียนรู้
            </a>
        </div>
    </div>
        <!-- Progress Overview -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-xl font-semibold">ความคืบหน้าการเรียนรู้ CSS ของคุณ</h2>
                <p class="text-gray-600">
                    <?php echo count($completedLessons); ?> of <?php echo count($lessons); ?> lessons completed
                </p>
            </div>
            <img src="img/css.png" alt="CSS Logo" class="h-12">
        </div>
        <div class="w-full bg-gray-200 rounded-full h-4">
            <div class="css-gradient h-4 rounded-full transition-all duration-500" 
                 style="width: <?php echo $progress; ?>%">
            </div>
        </div>
    </div>
</div>
        <!-- Learning Path Guide -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <h3 class="text-lg font-semibold mb-4">สถานะ</h3>
            <div class="flex gap-4">
                <div class="flex items-center">
                    <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                    <span>เสร็จสิ้น</span>
                </div>
                <div class="flex items-center">
                    <span class="w-3 h-3 bg-blue-500 rounded-full mr-2"></span>
                    <span>ยังไม่เสร็จ</span>
                </div>
                <div class="flex items-center">
                    <span class="w-3 h-3 bg-gray-300 rounded-full mr-2"></span>
                    <span>ล็อค</span>
                </div>
            </div>
        </div>
        <!-- Lessons Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($lessons as $lesson): ?>
    <div class="lesson-card bg-white rounded-lg shadow-md overflow-hidden relative <?php echo (!$lesson['is_completed'] && !$lesson['is_available']) ? 'opacity-50' : ''; ?>">
        <div class="p-6">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-lg font-semibold">
                    <?php echo htmlspecialchars($lesson['title']); ?>
                </h3>
            </div>
            <div class="text-gray-600 mb-4 prose">
                <?php 
                $description = strip_tags($lesson['description']);
                echo substr($description, 0, 100) . (strlen($description) > 100 ? '...' : ''); 
                ?>
            </div>
            <div class="mt-4 space-y-2">
                <?php if ($lesson['is_completed']): ?>
                    <span class="block text-green-600 text-sm mb-2">
                        ✓ เสร็จสมบูรณ์
                    </span>
                <?php endif; ?>
                <?php if ($lesson['is_available']): ?>
                    <?php if ($lesson['is_completed']): ?>
                        <div class="flex gap-2">
                            <a href="css_lesson.php?language=css&lesson=<?php echo $lesson['id']; ?>" 
                               class="flex-1 text-center bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition">
                               ทบทวนบทเรียน
                            </a>
                        </div>
                    <?php else: ?>
                        <a href="css_lesson.php?css&lesson=<?php echo $lesson['id']; ?>" 
                           class="block text-center bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">
                           เริ่มบทเรียน
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <button disabled 
                            class="w-full bg-gray-300 text-gray-500 px-4 py-2 rounded cursor-not-allowed">
                            บทเรียนก่อนหน้าให้เสร็จสิ้นก่อน
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endforeach; ?>
        </div>
    </div>
    <!-- Help Button -->
    <div class="fixed bottom-4 right-4">
        <button onclick="alert('Need help with PHP? Contact our support team for assistance!')" 
                class="bg-blue-500 text-white p-4 rounded-full shadow-lg hover:bg-blue-600 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </button>
    </div>
</body>
</html>