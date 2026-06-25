<?php
function inisial($n) {
    $b = preg_replace('/^dr\.?\s*/i', '', $n);
    $k = explode(' ', trim($b));
    $i = strtoupper(substr($k[0], 0, 1));
    if (count($k) > 1) $i .= strtoupper(substr(end($k), 0, 1));
    return $i;
}
?>

<div class="page-head">
  <h1>📅 Buat Janji Temu</h1>
  <a href="index.php" class="btn btn-light">&larr; Kembali</a>
</div>

<form method="POST" action="tambah.php" id="formJanji">
  <input type="hidden" name="tgl_janji" id="tgl_janji_hidden">
  <input type="hidden" name="id_dokter" id="id_dokter_hidden">

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

    <!-- KIRI -->
    <div>
      <div class="card">
        <h2>1. Pilih Pasien</h2>
        <div class="form-group">
          <label>Nama Pasien</label>
          <select name="id_pasien" required>
            <option value="">-- Pilih Pasien --</option>
            <?php while($p = mysqli_fetch_assoc($daftarPasien)): ?>
            <option value="<?= $p['id_pasien'] ?>"><?= htmlspecialchars($p['nama_pasien']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Keluhan</label>
          <textarea name="keluhan" rows="3" placeholder="Jelaskan keluhan pasien..."></textarea>
        </div>
      </div>

      <div class="card">
        <h2>2. Pilih Dokter</h2>
        <div id="dokterGrid" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
          <?php while($d = mysqli_fetch_assoc($daftarDokter)): ?>
          <div class="dokter-option"
               id="dokter_<?= $d['id_dokter'] ?>"
               onclick="pilihDokter(<?= $d['id_dokter'] ?>, '<?= htmlspecialchars($d['nama_dokter'], ENT_QUOTES) ?>')"
               style="background:#fff;border:2px solid #e0f0f0;border-radius:16px;padding:16px;text-align:center;cursor:pointer;transition:all 0.2s;">
            <div style="width:50px;height:50px;border-radius:50%;background:linear-gradient(135deg,#C2E7EB,#00A3A6);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:18px;margin:0 auto 10px;font-family:'Montserrat',sans-serif;">
              <?= inisial($d['nama_dokter']) ?>
            </div>
            <div style="font-weight:700;font-size:13px;color:#14302F;"><?= htmlspecialchars($d['nama_dokter']) ?></div>
            <div style="font-size:12px;color:#4B6664;margin-top:3px;"><?= htmlspecialchars($d['spesialisasi']) ?></div>
          </div>
          <?php endwhile; ?>
        </div>
        <p id="dokterAlert" style="color:#E2574C;font-size:13px;margin-top:10px;display:none;">
          ⚠️ Pilih dokter terlebih dahulu!
        </p>
      </div>
    </div>

    <!-- KANAN: Kalender -->
    <div>
      <div class="calendar-wrap">
        <h2 style="margin-bottom:18px;">3. Pilih Tanggal &amp; Jam</h2>
        <div class="calendar-header">
          <button type="button" class="cal-nav" id="calPrev">&#8249;</button>
          <h3 id="calMonthLabel"></h3>
          <button type="button" class="cal-nav" id="calNext">&#8250;</button>
        </div>
        <div class="cal-grid" id="calGrid"></div>
        <div class="slot-section" id="slotSection" style="display:none;">
          <h4 id="slotTitle">Pilih Jam</h4>
          <div class="slot-grid" id="slotGrid"></div>
        </div>
        <p id="calHint" style="color:#93a8a6;font-size:13px;margin-top:14px;text-align:center;">
          👆 Klik tanggal untuk melihat slot waktu tersedia
        </p>
      </div>

      <div class="card" id="confirmCard" style="display:none;">
        <h2>4. Konfirmasi Booking</h2>
        <div id="summaryBox" style="background:#F0F9FA;border-radius:14px;padding:16px;margin-bottom:16px;font-size:14px;line-height:2;color:#14302F;"></div>
        <button type="submit" class="btn btn-primary" style="width:100%;padding:14px;font-size:15px;">
          ✅ Konfirmasi Janji Temu
        </button>
      </div>
    </div>

  </div>
</form>

<script src="/sisehat/assets/js/kalender.js"></script>