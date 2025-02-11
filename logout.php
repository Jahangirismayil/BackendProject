<?php
session_start();

if (isset($_SESSION['user_id'])) {
    session_unset(); // Tüm oturum verilerini temizle
    session_destroy(); // Oturumu sonlandır
}

// Kullanıcıyı login sayfasına yönlendir ve mesaj göster
header("Location: login.php?logout=1");
exit();
?>