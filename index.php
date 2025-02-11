<?php
session_start();
?>
<head>
    <link rel="stylesheet" href="style.css">
</head>
<h2>Giriş Yap</h2>
<form method="POST" action="login.php">
    Kullanıcı Adı: <input type="text" name="username" required><br><br>
    Şifre: <input type="password" name="password" required><br><br>
    <input type="submit" value="Giriş Yap">
</form>

<h2>Yeni Hesap Oluştur</h2>
<a href="register.php">Hesap oluşturmak için tıklayın</a>