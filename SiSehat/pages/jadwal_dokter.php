<?php
require_once '../config/auth.php';
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../config/koneksi.php';
include '../config/header.php';

$sudahLogin = isset($_SESSION['id_user']);
$idPasien = ($sudahLogin && $_SESSION['role'] === 'Pasien') ? (int) ($_SESSION['id_pasien'] ?? 0) : 0;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Booking hanya untuk pasien yang sudah login
    if (!$sudahLogin || $_SESSION['role'] !== 'Pasien') {
        header('Location: ../login.php');
        exit;
    }

    $idDokter = (int) $_POST['id_dokter'];
    $tgl      = mysqli_real_escape_string($conn, $_POST['tgl_janji']);
    $keluhan  = mysqli_real_escape_string($conn, $_POST['keluhan']);

    if ($idDokter <= 0 || empty($_POST['tgl_janji'])) {
        $error = 'Silakan pilih dokter, tanggal, dan jam terlebih dahulu.';
    } else {
        $cek = mysqli_prepare($conn,
            "SELECT id_janji FROM janji_temu WHERE id_dokter = ? AND tgl_janji = ? AND status != 'Batal'"
        );
        mysqli_stmt_bind_param($cek, 'is', $idDokter, $tgl);
        mysqli_stmt_execute($cek);
        $bentrok = mysqli_stmt_get_result($cek);

        if (mysqli_num_rows($bentrok) > 0) {
            $error = 'Maaf, slot jam tersebut baru saja terisi. Silakan pilih jam lain.';
        } else {
            $stmt = mysqli_prepare($conn,
                "INSERT INTO janji_temu (id_pasien, id_dokter, tgl_janji, keluhan, status) VALUES (?, ?, ?, ?, 'Menunggu')"
            );
            mysqli_stmt_bind_param($stmt, 'iiss', $idPasien, $idDokter, $tgl, $keluhan);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            header('Location: janji_temu_saya.php?sukses=booking');
            exit;
        }
    }
}

$daftarDokter = mysqli_query($conn, "SELECT id_dokter, nama_dokter, spesialisasi, no_hp FROM dokter ORDER BY nama_dokter");
$dokterList = mysqli_fetch_all($daftarDokter, MYSQLI_ASSOC);

$namaHariList = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
$hariIni = $namaHariList[date('w')];
$tanggalIni = date('Y-m-d');

// Ambil semua jadwal aktif untuk dicek per dokter (apakah buka hari ini + jam berapa)
$jadwalHariIni = [];
$resJadwal = mysqli_query($conn,
    "SELECT id_dokter, jam_mulai, jam_selesai FROM jadwal_dokter WHERE hari = '" . mysqli_real_escape_string($conn, $hariIni) . "' AND is_aktif = 1"
);
while ($row = mysqli_fetch_assoc($resJadwal)) {
    $jadwalHariIni[$row['id_dokter']][] = $row;
}
?>

<div class="page-head">
  <h1>Jadwal Dokter</h1>
</div>

<?php if (!$sudahLogin): ?>
  <div class="alert" style="background:#E8F8F8; color:#007A7D; border:1px solid #00A3A6;">
    Anda dapat melihat jadwal dokter tanpa login. Untuk membuat janji temu, silakan
    <a href="../login.php" style="color:#00A3A6; font-weight:700; text-decoration:underline;">login</a> terlebih dahulu.
  </div>
<?php endif; ?>

<?php if ($error): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="doctor-grid">
  <?php foreach ($dokterList as $d):
      $jadwalDokterIni = $jadwalHariIni[$d['id_dokter']] ?? [];
      $bukaHariIni = count($jadwalDokterIni) > 0;
  ?>
    <div class="doctor-card-wrap">
      <div class="doctor-card doctor-pilih" data-id="<?= $d['id_dokter'] ?>" style="cursor:pointer;">
        <div class="doctor-name"><?= htmlspecialchars($d['nama_dokter']) ?></div>
        <div class="doctor-spec"><?= htmlspecialchars($d['spesialisasi']) ?></div>
        <div style="font-size:12px; color:var(--ink-soft); margin-bottom:8px;"><?= htmlspecialchars($d['no_hp']) ?></div>
        <span class="badge <?= $bukaHariIni ? 'badge-selesai' : 'badge-batal' ?>">
          <?= $bukaHariIni ? 'Buka Hari Ini' : 'Tutup Hari Ini' ?>
        </span>
        <?php if ($bukaHariIni): ?>
          <div style="font-size:11px; color:var(--ink-soft); margin-top:6px;">
            <?php foreach ($jadwalDokterIni as $j): ?>
              <?= substr($j['jam_mulai'], 0, 5) ?>–<?= substr($j['jam_selesai'], 0, 5) ?><br>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <div class="doctor-expand" id="expand-<?= $d['id_dokter'] ?>" style="display:none;">
        <?php if (!$bukaHariIni): ?>
          <p style="font-size:13px; color:var(--ink-soft); padding:14px 0;">
            Dokter ini tidak praktik hari ini (<?= $hariIni ?>).
          </p>
        <?php else: ?>
          <p style="font-size:13px; color:var(--ink-soft); margin-bottom:8px;">
            Slot tersedia hari ini, <?= $hariIni ?>, <?= date('d M Y') ?>:
          </p>
          <div class="slot-grid slot-hari-ini" data-id-dokter="<?= $d['id_dokter'] ?>"></div>
        <?php endif; ?>
        <button type="button" class="btn btn-light btn-sm btn-kalender-lengkap" data-id="<?= $d['id_dokter'] ?>" data-nama="<?= htmlspecialchars($d['nama_dokter']) ?>" data-spesialisasi="<?= htmlspecialchars($d['spesialisasi']) ?>" style="margin-top:10px;">
          Lihat Kalender Lengkap
        </button>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<form method="POST" action="jadwal_dokter.php" id="formJanji" style="display:none;">
  <input type="hidden" name="id_dokter" id="idDokterHidden">
  <input type="hidden" name="tgl_janji" id="tgl_janji_hidden">

  <div class="card">
    <h2 id="dokterTerpilihLabel">Pilih Tanggal &amp; Jam</h2>
  </div>

  <div class="calendar-wrap" style="margin-bottom:24px;">
    <div class="calendar-header">
      <button type="button" class="cal-nav" id="calPrev">&lsaquo;</button>
      <h3 id="calMonthLabel"></h3>
      <button type="button" class="cal-nav" id="calNext">&rsaquo;</button>
    </div>
    <div class="cal-grid" id="calGrid"></div>

    <h2 style="margin:22px 0 4px;">Pilih Jam</h2>
    <p style="font-size:13px; color:var(--ink-soft); margin-bottom:4px;" id="slotHint">Pilih tanggal terlebih dahulu.</p>
    <div class="slot-grid" id="slotGrid"></div>
  </div>

  <div class="card">
    <h2>Keluhan</h2>
    <div class="form-group">
      <label>Keluhan</label>
      <textarea name="keluhan" rows="3" placeholder="Jelaskan keluhan Anda" required></textarea>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary" id="btnSimpan" disabled>Simpan Janji Temu</button>
      <button type="button" class="btn btn-light" id="btnTutupForm">Tutup</button>
    </div>
  </div>
</form>

<script>
const bulanNama = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
const hariSingkat = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
const tanggalIni = '<?= $tanggalIni ?>';
const sudahLogin = <?= $sudahLogin ? 'true' : 'false' ?>;

let idDokterTerpilih = null;
let selectedDate = null;
let selectedSlot = null;
let slotTersedia = [];
let slotTerisi = [];

function pad(n) { return String(n).padStart(2, '0'); }

function cekLoginDulu() {
  if (!sudahLogin) {
    if (confirm('Silakan login terlebih dahulu untuk membuat janji temu. Login sekarang?')) {
      window.location.href = '../login.php';
    }
    return false;
  }
  return true;
}

// === EXPAND KARTU DOKTER (slot hari ini) ===
document.querySelectorAll('.doctor-pilih').forEach(card => {
  card.addEventListener('click', async () => {
    const id = card.dataset.id;
    const expandEl = document.getElementById('expand-' + id);

    document.querySelectorAll('.doctor-expand').forEach(el => {
      if (el !== expandEl) el.style.display = 'none';
    });
    document.querySelectorAll('.doctor-pilih').forEach(c => c.classList.remove('selected'));

    const sedangTerbuka = expandEl.style.display === 'block';
    if (sedangTerbuka) {
      expandEl.style.display = 'none';
      return;
    }

    card.classList.add('selected');
    expandEl.style.display = 'block';

    const slotGridHariIni = expandEl.querySelector('.slot-hari-ini');
    if (slotGridHariIni && !slotGridHariIni.dataset.loaded) {
      slotGridHariIni.dataset.loaded = '1';
      await muatSlotHariIni(id, slotGridHariIni);
    }
  });
});

async function muatSlotHariIni(idDokter, container) {
  container.innerHTML = '<p style="font-size:12px; color:var(--ink-soft);">Memuat slot...</p>';
  const data = await ambilDataSlot(idDokter, tanggalIni);

  container.innerHTML = '';
  if (!data.jadwal || data.slot.length === 0) {
    container.innerHTML = '<p style="font-size:12px; color:var(--ink-soft);">Tidak ada slot tersedia.</p>';
    return;
  }

  data.slot.forEach((jam, i) => {
    const taken = data.terisi.includes(jam);
    const el = document.createElement('div');
    el.className = 'slot' + (taken ? ' taken' : '');
    el.style.animationDelay = (i * 0.03) + 's';
    el.textContent = jam;
    if (!taken) {
      el.addEventListener('click', () => bookingHariIni(idDokter, jam));
    }
    container.appendChild(el);
  });
}

async function ambilDataSlot(idDokter, tanggalStr) {
  try {
    const res = await fetch(`cek_slot.php?id_dokter=${idDokter}&tanggal=${tanggalStr}`);
    return await res.json();
  } catch (e) {
    return { jadwal: false, slot: [], terisi: [] };
  }
}

function bookingHariIni(idDokter, jam) {
  if (!cekLoginDulu()) return;

  idDokterTerpilih = idDokter;
  document.getElementById('idDokterHidden').value = idDokter;
  document.getElementById('tgl_janji_hidden').value = `${tanggalIni} ${jam}:00`;
  document.getElementById('dokterTerpilihLabel').textContent = `Konfirmasi Janji Temu — Hari Ini, ${jam}`;
  document.getElementById('btnSimpan').disabled = false;
  document.getElementById('formJanji').style.display = 'block';
  document.querySelector('.calendar-wrap').style.display = 'none';
  document.getElementById('formJanji').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// === KALENDER LENGKAP ===
document.querySelectorAll('.btn-kalender-lengkap').forEach(btn => {
  btn.addEventListener('click', (e) => {
    e.stopPropagation();

    if (!cekLoginDulu()) return;

    idDokterTerpilih = btn.dataset.id;
    document.getElementById('idDokterHidden').value = idDokterTerpilih;
    document.getElementById('dokterTerpilihLabel').textContent =
      'Pilih Tanggal & Jam — ' + btn.dataset.nama + ' (' + btn.dataset.spesialisasi + ')';

    document.querySelector('.calendar-wrap').style.display = 'block';
    document.getElementById('formJanji').style.display = 'block';

    selectedDate = null;
    selectedSlot = null;
    slotTersedia = [];
    slotTerisi = [];
    renderCalendar();
    renderSlots();
    updateHidden();

    document.getElementById('formJanji').scrollIntoView({ behavior: 'smooth', block: 'start' });
  });
});

document.getElementById('btnTutupForm').addEventListener('click', () => {
  document.getElementById('formJanji').style.display = 'none';
  document.querySelectorAll('.doctor-pilih').forEach(c => c.classList.remove('selected'));
  document.querySelectorAll('.doctor-expand').forEach(el => el.style.display = 'none');
});

let today = new Date();
let viewYear = today.getFullYear();
let viewMonth = today.getMonth();

function renderCalendar() {
  document.getElementById('calMonthLabel').textContent = bulanNama[viewMonth] + ' ' + viewYear;
  const grid = document.getElementById('calGrid');
  grid.innerHTML = '';

  hariSingkat.forEach(h => {
    const el = document.createElement('div');
    el.className = 'cal-dow';
    el.textContent = h;
    grid.appendChild(el);
  });

  const firstDay = new Date(viewYear, viewMonth, 1).getDay();
  const totalDays = new Date(viewYear, viewMonth + 1, 0).getDate();
  const todayStr = today.toDateString();

  for (let i = 0; i < firstDay; i++) {
    grid.appendChild(document.createElement('div'));
  }

  for (let d = 1; d <= totalDays; d++) {
    const cellDate = new Date(viewYear, viewMonth, d);
    const el = document.createElement('div');
    el.className = 'cal-day';
    el.textContent = d;

    const isPast = cellDate < new Date(today.getFullYear(), today.getMonth(), today.getDate());
    if (isPast) {
      el.classList.add('disabled');
    } else {
      el.addEventListener('click', () => selectDate(cellDate, el));
    }
    if (cellDate.toDateString() === todayStr) el.classList.add('today');
    if (selectedDate && cellDate.toDateString() === selectedDate.toDateString()) el.classList.add('selected');

    grid.appendChild(el);
  }
}

function selectDate(date, el) {
  document.querySelectorAll('.cal-day.selected').forEach(x => x.classList.remove('selected'));
  el.classList.add('selected');
  selectedDate = date;
  selectedSlot = null;
  muatSlotTerisi();
  updateHidden();
}

async function muatSlotTerisi() {
  const hint = document.getElementById('slotHint');
  const slotGrid = document.getElementById('slotGrid');

  if (!idDokterTerpilih || !selectedDate) {
    slotTersedia = [];
    slotTerisi = [];
    renderSlots();
    return;
  }

  hint.textContent = 'Memuat slot tersedia...';
  slotGrid.innerHTML = '';

  const y = selectedDate.getFullYear();
  const m = pad(selectedDate.getMonth() + 1);
  const d = pad(selectedDate.getDate());
  const tanggalStr = `${y}-${m}-${d}`;

  const data = await ambilDataSlot(idDokterTerpilih, tanggalStr);
  slotTersedia = data.jadwal ? data.slot : [];
  slotTerisi = data.terisi || [];
  renderSlots(!data.jadwal);
}

function renderSlots(tutupHariItu) {
  const slotGrid = document.getElementById('slotGrid');
  const hint = document.getElementById('slotHint');
  slotGrid.innerHTML = '';

  if (!idDokterTerpilih) {
    hint.textContent = 'Pilih dokter terlebih dahulu.';
    return;
  }
  if (!selectedDate) {
    hint.textContent = 'Pilih tanggal terlebih dahulu.';
    return;
  }
  if (tutupHariItu || slotTersedia.length === 0) {
    hint.textContent = 'Dokter tidak praktik pada tanggal ini.';
    return;
  }

  hint.textContent = 'Slot tersedia untuk ' + selectedDate.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long' });

  slotTersedia.forEach((jam, i) => {
    const taken = slotTerisi.includes(jam);
    const el = document.createElement('div');
    el.className = 'slot' + (taken ? ' taken' : '');
    el.style.animationDelay = (i * 0.03) + 's';
    el.textContent = jam;
    if (!taken) {
      el.addEventListener('click', () => selectSlot(jam, el));
    }
    slotGrid.appendChild(el);
  });
}

function selectSlot(jam, el) {
  if (!cekLoginDulu()) return;

  document.querySelectorAll('.slot.selected').forEach(x => x.classList.remove('selected'));
  el.classList.add('selected');
  selectedSlot = jam;
  updateHidden();
}

function updateHidden() {
  const btn = document.getElementById('btnSimpan');
  if (selectedDate && selectedSlot) {
    const y = selectedDate.getFullYear();
    const m = pad(selectedDate.getMonth() + 1);
    const d = pad(selectedDate.getDate());
    document.getElementById('tgl_janji_hidden').value = `${y}-${m}-${d} ${selectedSlot}:00`;
    btn.disabled = false;
  } else {
    btn.disabled = true;
  }
}

document.getElementById('calPrev').addEventListener('click', () => {
  viewMonth--;
  if (viewMonth < 0) { viewMonth = 11; viewYear--; }
  renderCalendar();
});
document.getElementById('calNext').addEventListener('click', () => {
  viewMonth++;
  if (viewMonth > 11) { viewMonth = 0; viewYear++; }
  renderCalendar();
});
</script>

<?php include '../config/footer.php'; ?>