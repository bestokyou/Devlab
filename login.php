<?php
session_start(); // ต้องเริ่ม session ก่อนใช้งาน
// login.php
require_once 'config.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        // เพิ่มการสร้าง remember me cookie
        if (isset($_POST['remember_me'])) {
            $token = bin2hex(random_bytes(32)); // สร้าง token แบบสุ่ม
            // บันทึก token ลงในฐานข้อมูล
            $stmt = $pdo->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
            $stmt->execute([$token, $user['id']]);
            // สร้าง cookie ที่หมดอายุใน 30 วัน
            setcookie('remember_token', $token, time() + (86400 * 30), '/');
        }
        header('Location: dashboard.php');
        exit();
    }else {
        $error = "Invalid email or password";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="icon" type="image/png" href="icon1.png">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@300;400;500;600;700&family=IBM+Plex+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&family=Noto+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
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
        </style>
</head>
<body class="bg-gray-900">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md w-96">
            <h2 class="text-2xl mb-6 text-center">Login</h2>
            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>
            <form method="POST" class="space-y-4">
                <div>
                    <input type="email" name="email" placeholder="Email" required 
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <input type="password" name="password" placeholder="Password" required 
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                </div>
                <div class="flex items-center">
        <input type="checkbox" name="remember_me" id="remember_me" 
               class="h-4 w-4 text-blue-500 border-gray-300 rounded">
        <label for="remember_me" class="ml-2 text-gray-600">
            Remember me
        </label>
    </div>
                <button type="submit" 
                        class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600">
                    Login
                </button>
            </form>
            <p class="mt-4 text-center">
            ยังไม่มีบัญชีใช่ไหม? <a href="register.php" class="text-blue-500">Register</a>
            </p>
        </div>
    </div>
</body>
</html>