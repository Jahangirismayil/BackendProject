<?php
session_start();
include 'db.php'; // Veritabanı bağlantısını dahil et

// Eğer kullanıcı giriş yapmamışsa, login sayfasına yönlendir
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $task_id = $_GET['id'];

    // Görevi silmek için SQL sorgusunu güvenli şekilde hazırlayalım
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $task_id, $_SESSION['user_id']);

    if ($stmt->execute()) {
        // Silme başarılı, dashboard.php'ye yönlendir
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Hata: " . $conn->error;
    }

    $stmt->close();
} else {
    echo "Geçersiz görev ID'si!";
}
?>