<?php
require_once '../config/auth.php';
requireLogin('../');
if ($_SESSION['role'] !== 'Pasien') { header('Location: ../index.php'); exit; }

require_once '../config/koneksi.php';
include '../config/header.php';

$idPasien = (int) $_SESSION['id_pasien'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

$daftarDokter = mysqli_query($conn, "SELECT id_dokter, nama_dokter, spesialisasi FROM dokter ORDER BY nama_dokter");
?>

<div class="page-head">
  <h1>Buat Janji Temu</h1>
  <a href="janji_temu_saya.php" class="btn btn-light">&larr; Kembali</a>
</div>

<?php if ($error): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST" action="buat_janji.php" id="formJanji">
  <input type="hidden" name="tgl_janji" id="tgl_janji_hidden" required>

  <div class="card">
    <h2>1. Pilih Dokter</h2>
    <div class="form-group">
      <label>Dokter</label>
      <select name="id_dokter" id="dokterSelect" required>
        <option value="">-- Pilih Dokter --</option>
        <?php while ($d = mysqli_fetch_assoc($daftarDokter)): ?>
          <option value="<?= $d['id_dokter'] ?>"><?= htmlspecialchars($d['nama_dokter']) ?> (<?= htmlspecialchars($d['spesialisasi']) ?>)</option>
        <?php endwhile; ?>
      </select>
    </div>
  </div>

  <div class="calendar-wrap" style="margin-bottom:24px;">
    <h2 style="margin-bottom:14px;">2. Pilih Tanggal</h2>
    <div class="calendar-header">
      <button type="button" class="cal-nav" id="calPrev">&lsaquo;</button>
      <h3 id="calMonthLabel"></h3>
      <button type="button" class="cal-nav" id="calNext">&rsaquo;</button>
    </div>
    <div class="cal-grid" id="calGrid"></div>

    <h2 style="margin:22px 0 4px;">3. Pilih Jam</h2>
    <p style="font-size:13px; color:var(--ink-soft); margin-bottom:4px;" id="slotHint">Pilih dokter dan tanggal terlebih dahulu.</p>
    <div class="slot-grid" id="slotGrid"></div>
  </div>

  <div class="card">
    <h2>4. Keluhan</h2>
    <div class="form-group">
      <label>Keluhan</label>
      <textarea name="keluhan" rows="3" placeholder="Jelaskan keluhan Anda" required></textarea>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary" id="btnSimpan" disabled>Simpan Janji Temu</button>
      <a href="janji_temu_saya.php" class="btn btn-light">Batal</a>
    </div>
  </div>
</form>

<script>
const bulanNama = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
const hariSingkat = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
const jamSlot = ['08:00','08:30','09:00','09:30','10:00','10:30','11:00','13:00','13:30','14:00','14:30'];

let today = new Date();
let viewYear = today.getFullYear();
let viewMonth = today.getMonth();
let selectedDate = null;
let selectedSlot = null;
let slotTerisi = [];

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

function pad(n) { return String(n).padStart(2, '0'); }

function selectDate(date, el) {
  document.querySelectorAll('.cal-day.selected').forEach(x => x.classList.remove('selected'));
  el.classList.add('selected');
  selectedDate = date;
  selectedSlot = null;
  muatSlotTerisi();
  updateHidden();
}

async function muatSlotTerisi() {
  const idDokter = document.getElementById('dokterSelect').value;
  const hint = document.getElementById('slotHint');
  const slotGrid = document.getElementById('slotGrid');

  if (!idDokter || !selectedDate) {
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

  try {
    const res = await fetch(`cek_slot.php?id_dokter=${idDokter}&tanggal=${tanggalStr}`);
    const data = await res.json();
    slotTerisi = data.terisi || [];
  } catch (e) {
    slotTerisi = [];
  }
  renderSlots();
}

function renderSlots() {
  const slotGrid = document.getElementById('slotGrid');
  const hint = document.getElementById('slotHint');
  slotGrid.innerHTML = '';

  if (!document.getElementById('dokterSelect').value) {
    hint.textContent = 'Pilih dokter terlebih dahulu.';
    return;
  }
  if (!selectedDate) {
    hint.textContent = 'Pilih tanggal terlebih dahulu.';
    return;
  }
  hint.textContent = 'Slot tersedia untuk ' + selectedDate.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long' });

  jamSlot.forEach((jam, i) => {
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

document.getElementById('dokterSelect').addEventListener('change', () => {
  selectedSlot = null;
  muatSlotTerisi();
  updateHidden();
});

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

renderCalendar();
</script>

<?php include '../config/footer.php'; ?>