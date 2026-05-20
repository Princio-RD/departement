document.addEventListener('DOMContentLoaded', function () {
  const chartsWrap = document.querySelector('[data-monthly-data][data-weekday-data]');
  if (!chartsWrap || typeof Chart === 'undefined') {
    return;
  }

  let monthlyData = [];
  let weekdayData = [];

  try {
    monthlyData = JSON.parse(chartsWrap.getAttribute('data-monthly-data') || '[]');
  } catch (e) {
    monthlyData = [];
  }

  try {
    weekdayData = JSON.parse(chartsWrap.getAttribute('data-weekday-data') || '[]');
  } catch (e) {
    weekdayData = [];
  }

  const monthCanvas = document.getElementById('leaveByMonthChart');
  if (monthCanvas) {
    new Chart(monthCanvas, {
      type: 'bar',
      data: {
        labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
        datasets: [{
          label: 'Jours de congé',
          data: monthlyData,
          backgroundColor: '#7fa35c',
          borderRadius: 8,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: { precision: 0 }
          }
        }
      }
    });
  }

  const weekdayCanvas = document.getElementById('leaveByWeekdayChart');
  if (weekdayCanvas) {
    new Chart(weekdayCanvas, {
      type: 'doughnut',
      data: {
        labels: ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'],
        datasets: [{
          data: weekdayData,
          backgroundColor: ['#5a8f3d', '#6fa04f', '#82b35f', '#9cc56f', '#b6d681', '#d8e7aa', '#e7f1ce'],
          borderWidth: 0,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { position: 'bottom' }
        }
      }
    });
  }
});
