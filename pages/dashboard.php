<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('../includes/db.php');
$user_id = $_SESSION['user_id'];

// Ambil data pengguna untuk menampilkan nama
$stmt_user = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt_user->execute([$user_id]);
$user = $stmt_user->fetch(PDO::FETCH_ASSOC);

// Pagination
$limit = 5; // Jumlah data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Halaman saat ini
$offset = ($page - 1) * $limit; // Hitung offset

// Ambil total jumlah data absensi
$stmt_count = $pdo->prepare("SELECT COUNT(*) AS total FROM attendance WHERE user_id = ?");
$stmt_count->execute([$user_id]);
$total_data = $stmt_count->fetch(PDO::FETCH_ASSOC)['total'];

// Hitung total halaman
$total_pages = ceil($total_data / $limit);

// Ambil data absensi dengan limit dan offset
$stmt = $pdo->prepare("SELECT * FROM attendance WHERE user_id = :user_id LIMIT :limit OFFSET :offset");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();


$attendances = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Toko Mas Ilham</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <h3>Menu</h3>
            <ul>
                <li><a href="absen.php">Absen Hari Ini</a></li>
                <li><a href="download.php">Download Absen Bulan Ini</a></li>
            </ul>
        </div>
        <div class="main-content">
            <h2>Dashboard</h2>
            <?php if ($user) : ?>
                <h3>Selamat datang, <?php echo htmlspecialchars($user['username']); ?>!</h3>
            <?php else : ?>
                <h3>Selamat datang!</h3>
            <?php endif; ?>

            <h3>Riwayat Absen</h3>

            <?php if (empty($attendances)) : ?>
                <p>Anda belum mengisi absen untuk bulan ini.</p>
            <?php else : ?>
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Alasan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attendances as $attendance) : ?>
                            <tr>
                                <td><?php echo $attendance['date']; ?></td>
                                <td><?php echo $attendance['status']; ?></td>
                                <td><?php echo $attendance['alasan'] ?: '-'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="pagination">
                    <?php if ($page > 1) : ?>
                        <a href="?page=<?php echo $page - 1; ?>">Previous</a>
                    <?php endif; ?>

                    <?php if ($page < $total_pages) : ?>
                        <a href="?page=<?php echo $page + 1; ?>">Next</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
