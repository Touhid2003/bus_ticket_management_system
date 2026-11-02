// ---------- Sidebar toggle (mobile) ----------
document.getElementById('sidebarToggle')?.addEventListener('click', () => {
  document.getElementById('sidebar').classList.toggle('open');
});
document.addEventListener('click', (e) => {
  const sb = document.getElementById('sidebar');
  if (sb.classList.contains('open') && !sb.contains(e.target) && !e.target.closest('#sidebarToggle')) {
    sb.classList.remove('open');
  }
});

// ---------- Chart data (PHP পর্বে এগুলো ডাটাবেস থেকে আসবে) ----------
const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
const bookingsData = [420,510,480,620,700,660,780,850,790,910,980,1120];
const revenueData  = [3.1,3.8,3.5,4.6,5.2,4.9,5.8,6.4,5.9,6.9,7.4,8.5];

new Chart(document.getElementById('bookingsChart'), {
  type: 'bar',
  data: { labels: months, datasets: [{ label: 'Bookings', data: bookingsData,
          backgroundColor: '#93b4f8', hoverBackgroundColor: '#2563eb', borderRadius: 6 }] },
  options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: '#e3e8f0' } }, x: { grid: { display: false } } } }
});

new Chart(document.getElementById('revenueChart'), {
  type: 'line',
  data: { labels: months, datasets: [{ label: 'Revenue (৳ Lakh)', data: revenueData, borderColor: '#0f9d6e',
          backgroundColor: 'rgba(15,157,110,.14)', fill: true, tension: .35, pointBackgroundColor: '#fff', pointBorderWidth: 2 }] },
  options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: '#e3e8f0' } }, x: { grid: { display: false } } } }
});

// ---------- Recent Bookings: live search filter ----------
document.getElementById('globalSearch')?.addEventListener('input', function () {
  const q = this.value.toLowerCase();
  document.querySelectorAll('#bookingsTable tbody tr').forEach(tr => {
    tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
  });
});
