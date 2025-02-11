<?php
session_start();
include 'db.php'; // Veritabanı bağlantısını dahil et

// Kullanıcı oturumu açık mı kontrol et
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// Kullanıcı bilgilerini güvenli şekilde al
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Şifre güncelleme işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_password'])) {
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Mevcut şifreyi doğrula
    if (password_verify($current_password, $user['password'])) {
        // Yeni şifre ile doğrulama işlemi
        if ($new_password === $confirm_password) {
            // Şifrenin yeterince güçlü olup olmadığını kontrol edelim
            if (strlen($new_password) < 8) {
                $message = "<div class='error-message'>Yeni şifre en az 8 karakter olmalıdır!</div>";
            } else {
                // Yeni şifreyi bcrypt ile şifrele
                $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

                // Şifreyi güvenli şekilde güncelle
                $stmt_update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt_update->bind_param("si", $hashed_password, $user_id);

                if ($stmt_update->execute()) {
                    $message = "<div class='success-message'>Şifreniz başarıyla güncellendi! Lütfen yeniden giriş yapın.</div>";

                    // Oturumu sonlandır ve giriş sayfasına yönlendir
                    session_unset();
                    session_destroy();
                    header("Location: login.php");
                    exit();
                } else {
                    $message = "<div class='error-message'>Hata oluştu: " . $conn->error . "</div>";
                }

                $stmt_update->close();
            }
        } else {
            $message = "<div class='error-message'>Yeni şifre ve doğrulama şifresi eşleşmiyor!</div>";
        }
    } else {
        $message = "<div class='error-message'>Mevcut şifre hatalı!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Profilim</title>
    <link rel="stylesheet" href="style.css"> <!-- CSS dosyanıza link verin -->
</head>
<body>
    <div class="profile-container">
        <h2>Profilim</h2>
        
        <!-- Hata veya Bilgilendirme Mesajı -->
        <?php echo $message; ?>

        <div class="profile-info">
            <p><strong>Kullanıcı Adı:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
        </div>

        <h3>Şifreyi Güncelle</h3>
        <form method="POST">
            <label for="current_password">Mevcut Şifre:</label>
            <input type="password" name="current_password" required><br><br>

            <label for="new_password">Yeni Şifre:</label>
            <input type="password" name="new_password" required><br><br>

            <label for="confirm_password">Yeni Şifreyi Onayla:</label>
            <input type="password" name="confirm_password" required><br><br>

            <input type="submit" name="update_password" value="Şifreyi Güncelle">
        </form>
    </div>
</body>
</html>