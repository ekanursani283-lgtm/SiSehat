<?php
require_once '../config/auth.php';
if (session_status() === PHP_SESSION_NONE) session_start();

include '../config/header.php';
?>

<div class="page-head">
    <h1>Layanan Klinik Sani Sehat</h1>
</div>

<div class="card">
    <h2>🏥 Pemeriksaan Umum</h2>
    <p>
        Melayani konsultasi dan pemeriksaan kesehatan umum untuk berbagai keluhan
        seperti demam, flu, batuk, sakit kepala, dan pemeriksaan kesehatan rutin.
    </p>
</div>

<div class="card">
    <h2>👶 Pemeriksaan Anak</h2>
    <p>
        Pelayanan kesehatan khusus anak mulai dari pemeriksaan tumbuh kembang,
        imunisasi, hingga penanganan penyakit anak.
    </p>
</div>

<div class="card">
    <h2>🦷 Pemeriksaan Gigi</h2>
    <p>
        Melayani konsultasi kesehatan gigi dan mulut, pembersihan karang gigi,
        penambalan gigi, serta pemeriksaan rutin.
    </p>
</div>

<div class="card">
    <h2>🫀 Penyakit Dalam</h2>
    <p>
        Pemeriksaan dan konsultasi penyakit yang berkaitan dengan organ dalam,
        seperti diabetes, hipertensi, kolesterol, dan gangguan pencernaan.
    </p>
</div>

<div class="card">
    <h2>👂 THT</h2>
    <p>
        Pelayanan kesehatan Telinga, Hidung, dan Tenggorokan untuk berbagai
        keluhan seperti infeksi telinga, sinusitis, maupun radang tenggorokan.
    </p>
</div>

<div class="card">
    <h2>📋 Rekam Medis Digital</h2>
    <p>
        Semua riwayat pemeriksaan pasien tersimpan secara digital sehingga
        memudahkan dokter dalam memberikan pelayanan yang lebih cepat dan akurat.
    </p>
</div>

<div class="card">
    <h2>📅 Booking Janji Temu Online</h2>
    <p>
        Pasien dapat melihat jadwal dokter dan membuat janji temu secara online
        melalui sistem SiSehat tanpa perlu datang langsung ke klinik.
    </p>
</div>

<?php include '../config/footer.php'; ?>
