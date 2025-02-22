<?php
// lesson_data.php
function getLesson($pdo, $language, $lesson_id) {
    $stmt = $pdo->prepare("SELECT * FROM lessons WHERE language = ? AND id = ?");
    $stmt->execute([$language, $lesson_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function checkLessonExists($pdo, $language, $lesson_id) {
    // แก้ไขตรงนี้เพื่อให้ตรงกับโครงสร้างฐานข้อมูล
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM lessons WHERE language = ? AND id = ?");
    $stmt->execute([$language, $lesson_id]);
    return $stmt->fetchColumn() > 0;
}
function getTotalLessons($pdo, $language) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM lessons WHERE language = ?");
    $stmt->execute([$language]);
    return $stmt->fetchColumn();
}
?>