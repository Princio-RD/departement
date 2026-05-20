document.addEventListener('DOMContentLoaded', function () {
  const calendarEl = document.getElementById('calendar');
  if (!calendarEl || typeof FullCalendar === 'undefined') {
    return;
  }

  let events = [];
  try {
    events = JSON.parse(calendarEl.getAttribute('data-events') || '[]');
  } catch (e) {
    events = [];
  }

  const calendar = new FullCalendar.Calendar(calendarEl, {
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,listWeek'
    },
    initialView: 'dayGridMonth',
    navLinks: true,
    nowIndicator: true,
    weekNumbers: true,
    weekNumberCalculation: 'ISO',
    selectable: false,
    dayMaxEvents: true,
    locale: 'fr',
    events: events,
    eventClick: function (info) {
      const p = info.event.extendedProps || {};
      alert([
        info.event.title,
        'Statut: ' + (p.status || '—'),
        'Jours: ' + (p.jours || 0),
        'Motif: ' + (p.motif || '—')
      ].join('\n'));
    }
  });

  calendar.render();
});
