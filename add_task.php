<?php
session_start();
include 'db.php'; // Veritabanı bağlantısını dahil et

// Giriş yapmamış kullanıcıyı login sayfasına yönlendir
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Formdan gelen verileri al ve güvenli hale getir
    $task_name = trim($_POST['task_name']);
    $task_description = trim($_POST['task_description']);
    $status = $_POST['status'];
    $due_date = $_POST['due_date'];
    $priority = $_POST['priority'];
    $user_id = $_SESSION['user_id']; // Kullanıcı ID

    // SQL Injection'a karşı hazırlıklı sorgu (prepared statement)
    $stmt = $conn->prepare("INSERT INTO tasks (user_id, task_name, task_description, status, due_date, priority) 
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $user_id, $task_name, $task_description, $status, $due_date, $priority);

    if ($stmt->execute()) {
        header("Location: tasks.php?success=1"); // Başarılı ekleme sonrası yönlendirme
        exit();
    } else {
        echo "Hata: " . $conn->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Görev Ekle</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Yeni Görev Ekle</h2>
    <form method="POST">
        Görev Adı: <input type="text" name="task_name" required><br><br>
        Görev Açıklaması: <textarea name="task_description" required></textarea><br><br>
        Durum: 
        <select name="status">
            <option value="pending">Beklemede</option>
            <option value="completed">Tamamlandı</option>
        </select><br><br>
        Görev Tarihi: <input type="datetime-local" name="due_date" required><br><br>
        Öncelik: 
        <select name="priority">
            <option value="high">Yüksek</option>
            <option value="medium">Orta</option>
            <option value="low">Düşük</option>
        </select><br><br>
        <input type="submit" value="Görev Ekle">
    </form>
</body>
</html>