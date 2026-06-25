<?php
require_once '../../config/auth.php';
requireAdmin('../../');
require_once '../../config/koneksi.php';
include '../../config/header.php';

$result = mysqli_query($conn, "SELECT * FROM dokter ORDER BY id_dokter ASC");
$list = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Ilustrasi SVG dokter berbeda-beda
$avatarSVG = [
  // Dokter laki-laki 1 (baju putih, rambut hitam)
  '<svg viewBox="0 0 100 120" xmlns="http://www.w3.org/2000/svg">
    <circle cx="50" cy="35" r="22" fill="#FFDAB9"/>
    <rect x="28" y="55" width="44" height="50" rx="8" fill="#fff"/>
    <rect x="38" y="55" width="24" height="50" rx="4" fill="#E8F4FD"/>
    <circle cx="50" cy="35" r="18" fill="#FFDAB9"/>
    <ellipse cx="50" cy="20" rx="18" ry="12" fill="#2C2C2C"/>
    <rect x="42" y="57" width="16" height="6" rx="3" fill="#00A3A6"/>
    <circle cx="38" cy="72" r="4" fill="#E74C3C"/>
    <line x1="38" y1="76" x2="38" y2="88" stroke="#E74C3C" stroke-width="2"/>
    <line x1="33" y1="88" x2="43" y2="88" stroke="#E74C3C" stroke-width="2"/>
  </svg>',
  // Dokter perempuan 1 (hijab biru)
  '<svg viewBox="0 0 100 120" xmlns="http://www.w3.org/2000/svg">
    <circle cx="50" cy="35" r="22" fill="#FFDAB9"/>
    <rect x="28" y="55" width="44" height="50" rx="8" fill="#fff"/>
    <rect x="38" y="55" width="24" height="50" rx="4" fill="#EBF5FB"/>
    <circle cx="50" cy="38" r="18" fill="#FFDAB9"/>
    <ellipse cx="50" cy="22" rx="20" ry="16" fill="#2980B9"/>
    <ellipse cx="50" cy="30" rx="22" ry="12" fill="#2980B9"/>
    <rect x="42" y="57" width="16" height="6" rx="3" fill="#00A3A6"/>
    <path d="M38 70 Q50 65 62 70" stroke="#00A3A6" stroke-width="2" fill="none"/>
  </svg>',
  // Dokter laki-laki 2 (kumis, baju biru)
  '<svg viewBox="0 0 100 120" xmlns="http://www.w3.org/2000/svg">
    <circle cx="50" cy="35" r="22" fill="#F5CBA7"/>
    <rect x="28" y="55" width="44" height="50" rx="8" fill="#2980B9"/>
    <rect x="38" y="55" width="24" height="50" rx="4" fill="#fff"/>
    <circle cx="50" cy="35" r="18" fill="#F5CBA7"/>
    <ellipse cx="50" cy="20" rx="18" ry="12" fill="#1A1A2E"/>
    <ellipse cx="50" cy="44" rx="7" ry="3" fill="#8B6914"/>
    <rect x="42" y="57" width="16" height="6" rx="3" fill="#E74C3C"/>
    <circle cx="62" cy="72" r="4" fill="#F39C12"/>
    <line x1="62" y1="76" x2="62" y2="88" stroke="#F39C12" stroke-width="2"/>
  </svg>',
  // Dokter perempuan 2 (rambut panjang)
  '<svg viewBox="0 0 100 120" xmlns="http://www.w3.org/2000/svg">
    <circle cx="50" cy="35" r="22" fill="#FDEBD0"/>
    <rect x="28" y="55" width="44" height="50" rx="8" fill="#fff"/>
    <rect x="38" y="55" width="24" height="50" rx="4" fill="#D5F5E3"/>
    <circle cx="50" cy="35" r="18" fill="#FDEBD0"/>
    <ellipse cx="50" cy="18" rx="18" ry="14" fill="#8B4513"/>
    <rect x="30" y="25" width="8" height="30" rx="4" fill="#8B4513"/>
    <rect x="62" y="25" width="8" height="30" rx="4" fill="#8B4513"/>
    <rect x="42" y="57" width="16" height="6" rx="3" fill="#27AE60"/>
    <path d="M42 72 L50 68 L58 72" stroke="#27AE60" stroke-width="2" fill="none"/>
  </svg>',
  // Dokter laki-laki 3 (kacamata)
  '<svg viewBox="0 0 100 120" xmlns="http://www.w3.org/2000/svg">
    <circle cx="50" cy="35" r="22" fill="#FAD7A0"/>
    <rect x="28" y="55" width="44" height="50" rx="8" fill="#1A5276"/>
    <rect x="38" y="55" width="24" height="50" rx="4" fill="#fff"/>
    <circle cx="50" cy="35" r="18" fill="#FAD7A0"/>
    <ellipse cx="50" cy="20" rx="18" ry="12" fill="#2C2C2C"/>
    <circle cx="42" cy="36" r="6" fill="none" stroke="#2C2C2C" stroke-width="2"/>
    <circle cx="58" cy="36" r="6" fill="none" stroke="#2C2C2C" stroke-width="2"/>
    <line x1="48" y1="36" x2="52" y2="36" stroke="#2C2C2C" stroke-width="2"/>
    <rect x="42" y="57" width="16" height="6" rx="3" fill="#8E44AD"/>
  </svg>',
];

if (isset($_GET['sukses'])) {
    $pesanSukses = match ($_GET['sukses']) {
        'tambah' => 'Data dokter berhasil ditambahkan.',
        'edit'   => 'Data dokter berhasil diperbarui.',
        'hapus'  => 'Data dokter berhasil dihapus.',
        default  => '',
    };
}
?>

<div class="page-head">
  <h1>Data Dokter</h1>
  <a href="tambah.php" class="btn btn-primary">+ Tambah Dokter</a>
</div>

<?php if (!empty($pesanSukses)): ?>
  <div class="alert alert-success"><?= htmlspecialchars($pesanSukses) ?></div>
<?php endif; ?>

<div class="doctor-grid">
  <?php if (empty($list)): ?>
    <p style="color:var(--ink-soft);">Belum ada data dokter.</p>
  <?php else: ?>
    <?php foreach ($list as $i => $d): ?>
      <div class="doctor-card">
        <div class="doctor-avatar" style="width:80px; height:96px; margin:0 auto 12px;">
          <?= $avatarSVG[$i % count($avatarSVG)] ?>
        </div>
        <div class="doctor-name"><?= htmlspecialchars($d['nama_dokter']) ?></div>
        <div class="doctor-spec"><?= htmlspecialchars($d['spesialisasi']) ?></div>
        <div style="font-size:12px; color:var(--ink-soft); margin-bottom:10px;">
          <?= htmlspecialchars($d['no_hp'] ?? '-') ?>
        </div>
        <div class="form-actions" style="justify-content:center; gap:8px;">
          <a href="edit.php?id=<?= $d['id_dokter'] ?>" class="btn btn-light btn-sm">Edit</a>
          <a href="hapus.php?id=<?= $d['id_dokter'] ?>" class="btn btn-danger btn-sm"
             onclick="return confirm('Hapus data dokter ini?')">Hapus</a>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<?php include '../../config/footer.php'; ?>