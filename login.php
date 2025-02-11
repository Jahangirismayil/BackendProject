<?php
session_start();
include 'db.php'; // Veritabanı bağlantısını dahil et

$message = ""; // Kullanıcıya hata/bilgi mesajı vermek için değişken

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        // Kullanıcıyı veritabanından sorgula (SQL Injection'a karşı güvenli sorgu)
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Kullanıcı bulundu, şifreyi doğrula
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                // Şifre doğruysa, oturum başlat
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];

                // Başarılı giriş sonrası yönlendirme
                header("Location: dashboard.php");
                exit();
            } else {
                $message = "Hatalı şifre!";
            }
        } else {
            $message = "Kullanıcı bulunamadı!";
        }

        $stmt->close();
    } else {
        $message = "Kullanıcı adı ve şifre boş bırakılamaz!";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Giriş Yap</h2>

    <?php if (!empty($message)) { ?>
        <p style="color: red;"><?php echo $message; ?></p>
    <?php } ?>

    <form method="POST">
        Kullanıcı Adı: <input type="text" name="username" required><br><br>
        Şifre: <input type="password" name="password" required><br><br>
        <input type="submit" value="Giriş Yap">
    </form>
</body>
</html>