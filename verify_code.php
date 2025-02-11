<?php
session_start();
include 'db.php'; // Veritabanı bağlantısını dahil et

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $verification_code = $_POST['verification_code'];

    // Doğrulama kodunu veritabanından al
    $sql = "SELECT * FROM users WHERE verification_code = '$verification_code' AND user_id = " . $_SESSION['user_id'];
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Kod doğruysa, kullanıcıyı onayla
        $sql = "UPDATE users SET is_verified = 1 WHERE user_id = " . $_SESSION['user_id'];
        if ($conn->query($sql) === TRUE) {
            echo "Hesabınız başarıyla doğrulandı!";
        } else {
            echo "Hata: " . $conn->error;
        }
    } else {
        echo "Geçersiz doğrulama kodu!";
    }
}
?>

<h2>Doğrulama Kodu Girin</h2>
<form method="POST">
    Doğrulama Kodu: <input type="text" name="verification_code" required><br><br>
    <input type="submit" value="Doğrula">
</form>