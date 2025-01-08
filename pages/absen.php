<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('../includes/db.php');

$user_id = $_SESSION['user_id'];
$today = date("Y-m-d");

// Cek apakah user sudah absen di hari ini
$stmt = $pdo->prepare("SELECT * FROM attendance WHERE user_id = ? AND date = ?");
$stmt->execute([$user_id, $today]);
$existing_attendance = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Jika sudah ada absensi di tanggal tersebut, tampilkan pesan error
    if ($existing_attendance) {
        $message = "Anda sudah absen hari ini!";
    } else {
        // Ambil data status dan alasan
        $status = $_POST['status'];
        $alasan = $_POST['alasan'] ?? NULL;

        // Insert data absensi baru
        $stmt = $pdo->prepare("INSERT INTO attendance (user_id, date, status, alasan) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $today, $status, $alasan]);

        $message = "Absen berhasil!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absen - Toko Mas Ilham</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="container">
        <h2>Absen Hari Ini: <?php echo date("d-m-Y"); ?></h2>

        <?php if (isset($message)) { echo "<p>$message</p>"; } ?>

        <!-- Jika sudah absen hari ini, tampilkan pesan, jika belum, tampilkan form -->
        <?php if (!$existing_attendance) { ?>
            <form method="POST">
                <label for="status">Status Kehadiran:</label><br>
                <input type="radio" name="status" value="Hadir" required> Hadir<br>
                <input type="radio" name="status" value="Tidak Hadir" required> Tidak Hadir<br><br>

                <label for="alasan">Alasan Tidak Hadir:</label><br>
                <textarea name="alasan"></textarea><br><br>

                <button type="submit">Absen</button>
            </form>
        <?php } else { ?>
            <p>Anda sudah mengisi absensi hari ini.</p>
        <?php } ?>

        <!-- Tambahkan tombol kembali ke dashboard -->
        <br>
        <a href="dashboard.php" class="btn-back">Kembali ke Dashboard</a>
    </div>
</body>
</html>
