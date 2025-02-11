<?php
session_start();
include 'db.php'; // Veritabanı bağlantısını dahil et
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Eğer kullanıcı giriş yapmamışsa, login sayfasına yönlendir
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Kullanıcı kaydı sonrası doğrulama kodu gönderme
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $verification_code = rand(100000, 999999); // 6 haneli rastgele doğrulama kodu

    // Kullanıcıya doğrulama kodu gönder
    try {
        // PHPMailer ayarları
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'your-email@gmail.com'; // Kendi e-posta adresini buraya yaz
        $mail->Password = 'your-email-password'; // E-posta şifreni buraya yaz
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Alıcı, gönderici ve içerik
        $mail->setFrom('your-email@gmail.com', 'Task Manager');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Hesap Doğrulama Kodu';
        $mail->Body    = "Hesabınızı doğrulamak için şu kodu kullanın: <b>$verification_code</b>";

        // E-postayı gönder
        $mail->send();
        echo 'Doğrulama kodu e-postanıza gönderildi.';

        // Veritabanında doğrulama kodunu güncelle
        $sql = "UPDATE users SET verification_code = '$verification_code' WHERE email = '$email'";
        if ($conn->query($sql) === TRUE) {
            echo "Doğrulama kodu veritabanına kaydedildi.";
        } else {
            echo "Hata: " . $conn->error;
        }
    } catch (Exception $e) {
        echo "E-posta gönderilemedi. Hata: {$mail->ErrorInfo}";
    }
}
?>

<h2>Hesap Doğrulama</h2>
<form method="POST">
    E-posta: <input type="email" name="email" required><br><br>
    <input type="submit" value="Doğrulama Kodu Gönder">
</form>