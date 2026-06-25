const BULAN = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
const HARI = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
const JAM = ['08:00','08:30','09:00','09:30','10:00','10:30','11:00','13:00','13:30','14:00','14:30','15:00'];

let today = new Date();
let viewYear = today.getFullYear();
let viewMonth = today.getMonth();
let selectedDate = null;
let selectedSlot = null;
let selectedDokter = null;
let selectedDokterNama = '';

function renderCalendar() {
  document.getElementById('calMonthLabel').textContent = BULAN[viewMonth] + ' ' + viewYear;
  const grid = document.getElementById('calGrid');
  grid.innerHTML = '';
  HARI.forEach(h => {
    const el = document.createElement('div');
    el.className = 'cal-dow';
    el.textContent = h;
    grid.appendChild(el);
  });
  const firstDay = new Date(viewYear, viewMonth, 1).getDay();
  const totalDays = new Date(viewYear, viewMonth + 1, 0).getDate();
  for (let i = 0; i < firstDay; i++) grid.appendChild(document.createElement('div'));
  for (let d = 1; d <= totalDays; d++) {
    const cellDate = new Date(viewYear, viewMonth, d);
    const el = document.createElement('div');
    el.className = 'cal-day';
    el.textContent = d;
    const isPast = cellDate < new Date(today.getFullYear(), today.getMonth(), today.getDate());
    if (isPast) { el.classList.add('disabled'); }
    else { el.addEventListener('click', () => selectDate(cellDate, el)); }
    if (cellDate.toDateString() === today.toDateString()) el.classList.add('today');
    if (selectedDate && cellDate.toDateString() === selectedDate.toDateString()) el.classList.add('selected');
    grid.appendChild(el);
  }
}

function selectDate(date, el) {
  document.querySelectorAll('.cal-day').forEach(x => x.classList.remove('selected'));
  el.classList.add('selected');
  selectedDate = date;
  selectedSlot = null;
  renderSlots();
  updateHidden();
}

function renderSlots() {
  const section = document.getElementById('slotSection');
  const grid = document.getElementById('slotGrid');
  const title = document.getElementById('slotTitle');
  grid.innerHTML = '';
  section.style.display = 'block';
  document.getElementById('calHint').style.display = 'none';
  const tgl = selectedDate.toLocaleDateString('id-ID', {weekday:'long',day:'numeric',month:'long',year:'numeric'});
  title.textContent = '🕐 Slot untuk ' + tgl;
  JAM.forEach((jam, i) => {
    const taken = Math.random() < 0.2;
    const el = document.createElement('div');
    el.className = 'slot' + (taken ? ' taken' : '');
    el.style.animationDelay = (i * 0.04) + 's';
    el.textContent = jam;
    if (!taken) el.addEventListener('click', () => selectSlot(jam, el));
    grid.appendChild(el);
  });
}

function selectSlot(jam, el) {
  document.querySelectorAll('.slot').forEach(x => x.classList.remove('selected'));
  el.classList.add('selected');
  selectedSlot = jam;
  updateHidden();
}

function pilihDokter(id, nama) {
  document.querySelectorAll('.dokter-option').forEach(x => {
    x.style.borderColor = '#e0f0f0';
    x.style.background = '#fff';
    x.style.transform = 'scale(1)';
  });
  const el = document.getElementById('dokter_' + id);
  if (el) {
    el.style.borderColor = '#00A3A6';
    el.style.background = '#F0F9FA';
    el.style.transform = 'scale(1.04)';
  }
  selectedDokter = id;
  selectedDokterNama = nama;
  document.getElementById('id_dokter_hidden').value = id;
  document.getElementById('dokterAlert').style.display = 'none';
  updateHidden();
}

function updateHidden() {
  const card = document.getElementById('confirmCard');
  const summary = document.getElementById('summaryBox');
  if (selectedDate && selectedSlot && selectedDokter) {
    const y = selectedDate.getFullYear();
    const m = String(selectedDate.getMonth() + 1).padStart(2, '0');
    const d = String(selectedDate.getDate()).padStart(2, '0');
    document.getElementById('tgl_janji_hidden').value = `${y}-${m}-${d} ${selectedSlot}:00`;
    const tglStr = selectedDate.toLocaleDateString('id-ID', {weekday:'long',day:'numeric',month:'long',year:'numeric'});
    summary.innerHTML = `
      <div>🩺 <strong>Dokter:</strong> ${selectedDokterNama}</div>
      <div>📅 <strong>Tanggal:</strong> ${tglStr}</div>
      <div>🕐 <strong>Jam:</strong> ${selectedSlot}</div>
    `;
    card.style.display = 'block';
  } else {
    card.style.display = 'none';
  }
}

document.addEventListener('DOMContentLoaded', function() {
  renderCalendar();
  document.getElementById('calPrev').addEventListener('click', () => {
    viewMonth--; if (viewMonth < 0) { viewMonth = 11; viewYear--; } renderCalendar();
  });
  document.getElementById('calNext').addEventListener('click', () => {
    viewMonth++; if (viewMonth > 11) { viewMonth = 0; viewYear++; } renderCalendar();
  });
  document.getElementById('formJanji').addEventListener('submit', function(e) {
    if (!selectedDokter) {
      e.preventDefault();
      document.getElementById('dokterAlert').style.display = 'block';
      return;
    }
    if (!document.getElementById('tgl_janji_hidden').value) {
      e.preventDefault();
      alert('Pilih tanggal dan jam terlebih dahulu!');
    }
  });
});