<?php
session_start();
include 'db.php'; // Veritabanı bağlantısını dahil et

// Eğer kullanıcı giriş yapmamışsa, login sayfasına yönlendir
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $task_id = $_GET['id'];

    // Görev bilgilerini güvenli şekilde al
    $stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $task_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $task = $result->fetch_assoc();
    } else {
        echo "Görev bulunamadı!";
        exit();
    }

    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $task_name = trim($_POST['task_name']);
    $task_description = trim($_POST['task_description']);
    $status = $_POST['status'];
    $due_date = $_POST['due_date'];
    $priority = $_POST['priority'];

    // Görevi güvenli şekilde güncelle
    $stmt_update = $conn->prepare("UPDATE tasks SET task_name = ?, task_description = ?, status = ?, due_date = ?, priority = ? WHERE id = ? AND user_id = ?");
    $stmt_update->bind_param("ssssiii", $task_name, $task_description, $status, $due_date, $priority, $task_id, $_SESSION['user_id']);

    if ($stmt_update->execute()) {
        // Güncelleme başarılı, dashboard.php'ye yönlendir
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Hata: " . $conn->error;
    }

    $stmt_update->close();
}
?>

<h2>Görev Düzenle</h2>
<form method="POST">
    Görev Adı: <input type="text" name="task_name" value="<?php echo htmlspecialchars($task['task_name']); ?>" required><br><br>
    Görev Açıklaması: <textarea name="task_description" required><?php echo htmlspecialchars($task['task_description']); ?></textarea><br><br>
    Durum: 
    <select name="status">
        <option value="pending" <?php echo $task['status'] == 'pending' ? 'selected' : ''; ?>>Beklemede</option>
        <option value="completed" <?php echo $task['status'] == 'completed' ? 'selected' : ''; ?>>Tamamlandı</option>
    </select><br><br>
    Görev Tarihi: <input type="datetime-local" name="due_date" value="<?php echo date('Y-m-d\TH:i', strtotime($task['due_date'])); ?>" required><br><br>
    Öncelik: 
    <select name="priority">
        <option value="high" <?php echo $task['priority'] == 'high' ? 'selected' : ''; ?>>Yüksek</option>
        <option value="medium" <?php echo $task['priority'] == 'medium' ? 'selected' : ''; ?>>Orta</option>
        <option value="low" <?php echo $task['priority'] == 'low' ? 'selected' : ''; ?>>Düşük</option>
    </select><br><br>
    <input type="submit" value="Görev Güncelle">
</form>