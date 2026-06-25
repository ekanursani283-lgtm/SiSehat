<?php
// =============================================
// CEK SLOT — endpoint AJAX
// Mengembalikan JSON:
//  - jadwal: ada/tidak jadwal dokter di hari itu (dari tabel jadwal_dokter)
//  - jam_mulai, jam_selesai: rentang praktik hari itu (jika ada)
//  - slot: daftar semua slot jam (interval 1 jam) dalam rentang itu
//  - terisi: daftar jam yang sudah dibooking (status != Batal)
// =============================================
require_once '../config/auth.php';
requireLogin('../');
require_once '../config/koneksi.php';

header('Content-Type: application/json');

$idDokter = (int) ($_GET['id_dokter'] ?? 0);
$tanggal  = $_GET['tanggal'] ?? '';
$abaikan  = (int) ($_GET['abaikan'] ?? 0); // id_janji yang sedang diedit (supaya slot miliknya sendiri tidak dianggap "terisi")

if ($idDokter <= 0 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
    echo json_encode(['jadwal' => false, 'slot' => [], 'terisi' => []]);
    exit;
}

// Konversi tanggal jadi nama hari dalam Bahasa Indonesia
$namaHari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
$timestamp = strtotime($tanggal);
$hari = $namaHari[date('w', $timestamp)];

// Ambil jadwal dokter di hari itu (kalau dokter punya lebih dari satu sesi di hari yang sama, ambil yang pertama & aktif)
$stmt = mysqli_prepare($conn,
    "SELECT jam_mulai, jam_selesai FROM jadwal_dokter
     WHERE id_dokter = ? AND hari = ? AND is_aktif = 1
     ORDER BY jam_mulai ASC"
);
mysqli_stmt_bind_param($stmt, 'is', $idDokter, $hari);
mysqli_stmt_execute($stmt);
$jadwalResult = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($jadwalResult) === 0) {
    // Dokter tidak praktik di hari ini
    echo json_encode(['jadwal' => false, 'slot' => [], 'terisi' => []]);
    exit;
}

// Generate slot jam (interval 1 jam) dari semua sesi jadwal hari itu
$slot = [];
$jamMulaiAwal = null;
$jamSelesaiAkhir = null;

while ($jadwal = mysqli_fetch_assoc($jadwalResult)) {
    $mulai   = strtotime($jadwal['jam_mulai']);
    $selesai = strtotime($jadwal['jam_selesai']);

    if ($jamMulaiAwal === null || $mulai < $jamMulaiAwal) $jamMulaiAwal = $mulai;
    if ($jamSelesaiAkhir === null || $selesai > $jamSelesaiAkhir) $jamSelesaiAkhir = $selesai;

    for ($t = $mulai; $t < $selesai; $t += 3600) { // interval 1 jam = 3600 detik
        $slot[] = date('H:i', $t);
    }
}
$slot = array_values(array_unique($slot));
sort($slot);

// Ambil slot yang sudah terisi (status bukan Batal)
$stmt2 = mysqli_prepare($conn,
    "SELECT TIME_FORMAT(tgl_janji, '%H:%i') AS jam
     FROM janji_temu
     WHERE id_dokter = ?
       AND DATE(tgl_janji) = ?
       AND status != 'Batal'
       AND id_janji != ?"
);
mysqli_stmt_bind_param($stmt2, 'isi', $idDokter, $tanggal, $abaikan);
mysqli_stmt_execute($stmt2);
$result2 = mysqli_stmt_get_result($stmt2);

$terisi = [];
while ($row = mysqli_fetch_assoc($result2)) {
    $terisi[] = $row['jam'];
}

echo json_encode([
    'jadwal'      => true,
    'hari'        => $hari,
    'jam_mulai'   => date('H:i', $jamMulaiAwal),
    'jam_selesai' => date('H:i', $jamSelesaiAkhir),
    'slot'        => $slot,
    'terisi'      => $terisi,
]);