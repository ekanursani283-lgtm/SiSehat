<?php
require_once '../config/auth.php';
requireAdmin('../');
require_once '../config/koneksi.php';
include '../config/header.php';

// Total keseluruhan
$totalPasien = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) n FROM pasien"))['n'];
$totalDokter = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) n FROM dokter"))['n'];
$totalJanji  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) n FROM janji_temu"))['n'];
$totalPendapatan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total_biaya),0) n FROM pembayaran WHERE status_bayar='Lunas'"))['n'];

// Kunjungan per bulan (12 bulan terakhir)
$kunjunganBulan = [];
for ($i = 11; $i >= 0; $i--) {
    $bulan = date('Y-m', strtotime("-$i months"));
    $label = date('M Y', strtotime("-$i months"));
    $total = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT COUNT(*) n FROM janji_temu WHERE DATE_FORMAT(tgl_janji,'%Y-%m') = '$bulan'"))['n'];
    $kunjunganBulan[] = ['label' => $label, 'total' => (int)$total];
}

// Pendapatan per bulan (12 bulan terakhir)
$pendapatanBulan = [];
for ($i = 11; $i >= 0; $i--) {
    $bulan = date('Y-m', strtotime("-$i months"));
    $label = date('M Y', strtotime("-$i months"));
    $total = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT COALESCE(SUM(total_biaya),0) n FROM pembayaran WHERE status_bayar='Lunas' AND DATE_FORMAT(tgl_bayar,'%Y-%m') = '$bulan'"))['n'];
    $pendapatanBulan[] = ['label' => $label, 'total' => (float)$total];
}

// Janji temu per status
$statusJanji = mysqli_query($conn, "SELECT status, COUNT(*) n FROM janji_temu GROUP BY status");
$dataStatus = [];
while ($r = mysqli_fetch_assoc($statusJanji)) {
    $dataStatus[] = $r;
}

// Dokter paling banyak pasien
$topDokter = mysqli_query($conn,
    "SELECT d.nama_dokter, d.spesialisasi, COUNT(jt.id_janji) as total
     FROM dokter d
     LEFT JOIN janji_temu jt ON d.id_dokter = jt.id_dokter
     GROUP BY d.id_dokter
     ORDER BY total DESC LIMIT 5");
?>

<div class="page-head" id="laporanHeader">
  <h1>📊 Laporan & Statistik</h1>
  <div style="display:flex; align-items:center; gap:14px;">
    <span style="font-size:13px;color:var(--ink-soft);">Data per <?= date('d F Y') ?></span>
    <button onclick="window.print()" class="btn btn-primary btn-sm">🖨️ Cetak Laporan</button>
  </div>
</div>

<!-- Ringkasan -->
<div class="stat-grid" style="grid-template-columns:repeat(4,1fr);">
  <div class="stat-card">
    <div style="font-size:28px;margin-bottom:6px;">🧑</div>
    <div class="num"><?= $totalPasien ?></div>
    <div class="label">Total Pasien</div>
  </div>
  <div class="stat-card">
    <div style="font-size:28px;margin-bottom:6px;">🩺</div>
    <div class="num"><?= $totalDokter ?></div>
    <div class="label">Total Dokter</div>
  </div>
  <div class="stat-card">
    <div style="font-size:28px;margin-bottom:6px;">📅</div>
    <div class="num"><?= $totalJanji ?></div>
    <div class="label">Total Kunjungan</div>
  </div>
  <div class="stat-card">
    <div style="font-size:28px;margin-bottom:6px;">💰</div>
    <div class="num" style="font-size:20px;">Rp <?= number_format($totalPendapatan,0,',','.') ?></div>
    <div class="label">Total Pendapatan</div>
  </div>
</div>

<!-- Grafik Kunjungan per Bulan -->
<div class="card">
  <h2>📈 Kunjungan per Bulan</h2>
  <canvas id="chartKunjungan" height="100"></canvas>
</div>

<!-- Grafik Pendapatan per Bulan -->
<div class="card">
  <h2>💰 Pendapatan per Bulan</h2>
  <canvas id="chartPendapatan" height="100"></canvas>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

  <!-- Pie Chart Status Janji -->
  <div class="card">
    <h2>🔵 Status Janji Temu</h2>
    <canvas id="chartStatus" height="200"></canvas>
  </div>

  <!-- Top Dokter -->
  <div class="card">
    <h2>🏆 Top 5 Dokter Terbanyak Pasien</h2>
    <table>
      <thead>
        <tr><th>Dokter</th><th>Spesialisasi</th><th>Total Pasien</th></tr>
      </thead>
      <tbody>
        <?php while($r = mysqli_fetch_assoc($topDokter)): ?>
        <tr>
          <td><?= htmlspecialchars($r['nama_dokter']) ?></td>
          <td><?= htmlspecialchars($r['spesialisasi']) ?></td>
          <td>
            <span style="background:var(--teal-bg);color:var(--teal-dark);padding:3px 12px;border-radius:999px;font-weight:700;font-size:13px;">
              <?= $r['total'] ?>
            </span>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const labelsBulan = <?= json_encode(array_column($kunjunganBulan, 'label')) ?>;
const dataKunjungan = <?= json_encode(array_column($kunjunganBulan, 'total')) ?>;
const dataPendapatan = <?= json_encode(array_column($pendapatanBulan, 'total')) ?>;

const statusLabels = <?= json_encode(array_column($dataStatus, 'status')) ?>;
const statusData = <?= json_encode(array_column($dataStatus, 'n')) ?>;

// Warna
const teal = '#00A3A6';
const tealLight = 'rgba(0,163,166,0.15)';
const amber = '#FFB020';
const coral = '#E2574C';
const green = '#27AE60';

// Grafik Kunjungan
new Chart(document.getElementById('chartKunjungan'), {
  type: 'line',
  data: {
    labels: labelsBulan,
    datasets: [{
      label: 'Jumlah Kunjungan',
      data: dataKunjungan,
      borderColor: teal,
      backgroundColor: tealLight,
      borderWidth: 3,
      pointBackgroundColor: teal,
      pointRadius: 5,
      pointHoverRadius: 8,
      fill: true,
      tension: 0.4
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { display: false },
      tooltip: {
        callbacks: {
          label: ctx => ` ${ctx.raw} kunjungan`
        }
      }
    },
    scales: {
      y: {
        beginAtZero: true,
        ticks: { stepSize: 1 },
        grid: { color: 'rgba(0,0,0,0.05)' }
      },
      x: { grid: { display: false } }
    }
  }
});

// Grafik Pendapatan
new Chart(document.getElementById('chartPendapatan'), {
  type: 'bar',
  data: {
    labels: labelsBulan,
    datasets: [{
      label: 'Pendapatan (Rp)',
      data: dataPendapatan,
      backgroundColor: teal,
      borderRadius: 8,
      hoverBackgroundColor: '#007A7D'
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { display: false },
      tooltip: {
        callbacks: {
          label: ctx => ` Rp ${ctx.raw.toLocaleString('id-ID')}`
        }
      }
    },
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          callback: val => 'Rp ' + val.toLocaleString('id-ID')
        },
        grid: { color: 'rgba(0,0,0,0.05)' }
      },
      x: { grid: { display: false } }
    }
  }
});

// Pie Chart Status
new Chart(document.getElementById('chartStatus'), {
  type: 'doughnut',
  data: {
    labels: statusLabels,
    datasets: [{
      data: statusData,
      backgroundColor: [teal, amber, coral],
      borderWidth: 0,
      hoverOffset: 8
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: {
        position: 'bottom',
        labels: { padding: 20, font: { size: 13 } }
      }
    },
    cutout: '65%'
  }
});
</script>

<style media="print">
  .navbar, .footer, #laporanHeader button { display: none !important; }
  body { background: #fff !important; }
  .card, .stat-card { box-shadow: none !important; border: 1px solid #ddd !important; }
  .container { max-width: 100% !important; }
  canvas { max-height: 280px !important; }
  @page { margin: 1.5cm; }
</style>

<?php include '../config/footer.php'; ?>