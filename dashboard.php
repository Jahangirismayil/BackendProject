<?php
session_start();
include 'db.php'; // Veritabanı bağlantısını dahil et

// Kullanıcı giriş yapmamışsa yönlendir
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// Kullanıcının görevlerini güvenli şekilde al
$stmt = $conn->prepare("SELECT * FROM tasks WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Görev ekleme işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $task_name = trim($_POST['task_name']);
    $task_description = trim($_POST['task_description']);
    $status = $_POST['status'];
    $due_date = $_POST['due_date'];
    $priority = $_POST['priority'];

    if (!empty($task_name) && !empty($task_description)) {
        // Güvenli şekilde görevi veritabanına ekle
        $stmt_insert = $conn->prepare("INSERT INTO tasks (user_id, task_name, task_description, status, due_date, priority) 
                                       VALUES (?, ?, ?, ?, ?, ?)");
        $stmt_insert->bind_param("isssss", $user_id, $task_name, $task_description, $status, $due_date, $priority);
        
        if ($stmt_insert->execute()) {
            $message = "Görev başarıyla eklendi!";
            header("Location: dashboard.php?success=1");
            exit();
        } else {
            $message = "Hata oluştu: " . $conn->error;
        }

        $stmt_insert->close();
    } else {
        $message = "Görev adı ve açıklaması boş bırakılamaz!";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Görevlerim</title>
    <link rel="stylesheet" href="style.css"> <!-- CSS dosyanıza link verin -->
</head>
<body>
    <h2>Görevlerim</h2>

    <?php if (isset($_GET['success'])) { ?>
        <p style="color: green;">Görev başarıyla eklendi!</p>
    <?php } ?>

    <?php if (!empty($message)) { ?>
        <p style="color: red;"><?php echo $message; ?></p>
    <?php } ?>

    <!-- Görev Ekleme Formu -->
    <h3>Yeni Görev Ekle</h3>
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

    <!-- Görevlerin Listesi -->
    <?php
    if ($result->num_rows > 0) {
        echo "<table border='1'>
                <thead>
                    <tr>
                        <th>Görev Adı</th>
                        <th>Açıklama</th>
                        <th>Durum</th>
                        <th>Tarih</th>
                        <th>Öncelik</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['task_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['task_description']) . "</td>";
            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
            echo "<td>" . date('Y-m-d H:i', strtotime($row['due_date'])) . "</td>";
            echo "<td>" . ucfirst(htmlspecialchars($row['priority'])) . "</td>";
            echo "<td>
                    <a href='edit_task.php?id=" . $row['id'] . "'>Düzenle</a> | 
                    <a href='delete_task.php?id=" . $row['id'] . "'>Sil</a>
                  </td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>Henüz bir görev eklemediniz.</p>";
    }
    ?>
</body>
</html>