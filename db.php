<?php
$servername = "localhost";
$username = "root";
$password = "";  // Boş bırak, çünkü şifre yok
$dbname = "task_manager_db"; // Veritabanı adı

// Veritabanı bağlantısı
$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantı kontrolü
if ($conn->connect_error) {
    // Hata raporlama işlemi
    error_log("Bağlantı hatası: " . $conn->connect_error);
    die("Veritabanı bağlantısı sağlanamadı. Lütfen daha sonra tekrar deneyin.");
}
?>