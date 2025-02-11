<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

// Eğer form gönderildiyse
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Formdan alınan veriler
    $email = $_POST['email'];
    $password = $_POST['password'];
    $email_provider = $_POST['email_provider'];  // "icloud" veya "gmail" (formdan alınabilir)
    
    // Burada doğrulama kodu oluşturuluyor
    $verification_code = rand(100000, 999999);

    // PHPMailer instance oluşturuluyor
    $mail = new PHPMailer(true);

    try {
        // iCloud için SMTP ayarları
        if ($email_provider == 'icloud') {
            // iCloud için SMTP ayarları
            $mail->isSMTP();
            $mail->Host = 'smtp.mail.me.com';  // iCloud SMTP sunucusu
            $mail->SMTPAuth = true;
            $mail->Username = 'your-email@icloud.com';  // iCloud adresinizi buraya yazın
            $mail->Password = 'your-app-password';  // Uygulama şifrenizi buraya yazın
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;  // TLS portu
        }

        // Gönderen ve alıcı ayarları
        $mail->setFrom('your-email@icloud.com', 'Your Name');  // Kendi iCloud e-posta adresinizi buraya yazın
        $mail->addAddress($email, 'User');  // Alıcı (kullanıcının mail adresi buraya gelecek)

        // İçerik
        $mail->isHTML(true);
        $mail->Subject = 'E-posta Doğrulama';
        $mail->Body    = 'E-posta doğrulama kodunuz: ' . $verification_code;

        // Maili gönder
        $mail->send();
        echo 'Doğrulama kodu gönderildi';

        // Burada doğrulama kodunu kullanıcıya gösterebilirsiniz
    } catch (Exception $e) {
        echo "Mail gönderilemedi. Hata: {$mail->ErrorInfo}";
    }

    // Kullanıcıyı kaydetme işlemi burada yapılabilir
    // Örneğin, şifreyi güvenli bir şekilde saklama ve kullanıcıyı veritabanına ekleme
}
?>



<!-- Form kısmı -->
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Formu</title>
    <style>
 body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        label {
            font-size: 14px;
            color: #333;
            margin-bottom: 5px;
            display: block;
        }

        input[type="email"], input[type="password"], select, input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        input[type="email"]:focus, input[type="password"]:focus, select:focus {
            border-color: #4CAF50;
            outline: none;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .form-container p {
            text-align: center;
            color: #666;
        }

        .form-container p a {
            color: #4CAF50;
            text-decoration: none;
        }

        .form-container p a:hover {
            text-decoration: underline;
        }

    </style>
</head>
<body>

    <div class="form-container">
        <h2>Kaydol</h2>
        <form action="register.php" method="POST">
            <label for="email">E-posta adresi:</label>
            <input type="email" id="email" name="email" required><br>

            <label for="password">Şifre:</label>
            <input type="password" id="password" name="password" required><br>

            <label for="email_provider">E-posta sağlayıcısını seçin:</label>
            <select name="email_provider" id="email_provider">
                <option value="icloud">iCloud</option>
                <option value="gmail">Gmail</option>
            </select><br>

            <input type="submit" value="Kaydol">
        </form>
    </div>

</body>
</html>