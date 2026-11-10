// ---------- Sidebar toggle ----------
const sidebarToggle = document.getElementById('sidebarToggle');
const sidebar = document.getElementById('sidebar');

if (sidebarToggle && sidebar) {
  const mobileViewport = window.matchMedia('(max-width: 991.98px)');
  const updateToggleState = () => {
    const expanded = mobileViewport.matches
      ? document.body.classList.contains('sidebar-open')
      : !document.body.classList.contains('sidebar-collapsed');
    sidebarToggle.setAttribute('aria-expanded', String(expanded));
  };

  sidebarToggle.addEventListener('click', () => {
    if (mobileViewport.matches) {
      document.body.classList.toggle('sidebar-open');
    } else {
      document.body.classList.toggle('sidebar-collapsed');
    }
    updateToggleState();
  });

  document.addEventListener('click', (event) => {
    if (
      mobileViewport.matches &&
      document.body.classList.contains('sidebar-open') &&
      event.target instanceof Element &&
      !sidebar.contains(event.target) &&
      !sidebarToggle.contains(event.target)
    ) {
      document.body.classList.remove('sidebar-open');
      updateToggleState();
    }
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && document.body.classList.contains('sidebar-open')) {
      document.body.classList.remove('sidebar-open');
      updateToggleState();
    }
  });

  mobileViewport.addEventListener('change', () => {
    document.body.classList.remove('sidebar-open');
    updateToggleState();
  });

  updateToggleState();
}

// ---------- Chart data (PHP পর্বে এগুলো ডাটাবেস থেকে আসবে) ----------
const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
const bookingsData = [420, 510, 480, 620, 700, 660, 780, 850, 790, 910, 980, 1120];
const revenueData = [3.1, 3.8, 3.5, 4.6, 5.2, 4.9, 5.8, 6.4, 5.9, 6.9, 7.4, 8.5];

new Chart(document.getElementById('bookingsChart'), {
  type: 'bar',
  data: {
    labels: months, datasets: [{
      label: 'Bookings', data: bookingsData,
      backgroundColor: '#93b4f8', hoverBackgroundColor: '#2563eb', borderRadius: 6
    }]
  },
  options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: '#e3e8f0' } }, x: { grid: { display: false } } } }
});

new Chart(document.getElementById('revenueChart'), {
  type: 'line',
  data: {
    labels: months, datasets: [{
      label: 'Revenue (৳ Lakh)', data: revenueData, borderColor: '#0f9d6e',
      backgroundColor: 'rgba(15,157,110,.14)', fill: true, tension: .35, pointBackgroundColor: '#fff', pointBorderWidth: 2
    }]
  },
  options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: '#e3e8f0' } }, x: { grid: { display: false } } } }
});

// ---------- Recent Bookings: live search filter ----------
document.getElementById('globalSearch')?.addEventListener('input', function () {
  const q = this.value.toLowerCase();
  document.querySelectorAll('#bookingsTable tbody tr').forEach(tr => {
    tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
  });
});
