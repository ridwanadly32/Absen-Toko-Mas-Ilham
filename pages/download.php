<?php
// Mengimpor Composer autoloader
require '../vendor/autoload.php'; // Pastikan path ke autoload.php sudah benar

// Mulai session dan cek apakah user sudah login
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('../includes/db.php');
$user_id = $_SESSION['user_id'];

// Ambil username dari database
$stmt_user = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt_user->execute([$user_id]);
$user = $stmt_user->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Pengguna tidak ditemukan.";
    exit();
}

$username = $user['username']; // Ambil username untuk digunakan di nama file

// Ambil data absensi bulan ini
$month = date("Y-m");
$stmt = $pdo->prepare("SELECT * FROM attendance WHERE user_id = ? AND date LIKE ?");
$stmt->execute([$user_id, "$month%"]);
$attendances = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Jika tidak ada data absensi
if (empty($attendances)) {
    echo "Tidak ada data absensi untuk bulan ini.";
    exit();
}

// Membuat objek FPDF
$pdf = new \FPDF();  // Perhatikan penggunaan nama kelas lengkap jika perlu

$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

// Judul PDF
$pdf->Cell(200, 10, "Absensi Bulan: $month", 0, 1, 'C');
$pdf->Ln(10);

// Menambahkan header tabel
$pdf->Cell(50, 10, 'Tanggal', 1, 0, 'C');
$pdf->Cell(50, 10, 'Status', 1, 0, 'C');
$pdf->Cell(90, 10, 'Alasan', 1, 1, 'C');

// Menambahkan data absensi ke dalam tabel
foreach ($attendances as $attendance) {
    $pdf->Cell(50, 10, $attendance['date'], 1, 0, 'C');
    $pdf->Cell(50, 10, $attendance['status'], 1, 0, 'C');
    $pdf->Cell(90, 10, $attendance['alasan'] ?: '-', 1, 1, 'C');
}

// Penamaan file PDF dengan username
$filename = "absensi_{$username}_{$month}.pdf";

// Output PDF untuk di-download
$pdf->Output('D', $filename);
exit();
?>
