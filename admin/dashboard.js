
document.addEventListener('DOMContentLoaded', () => {
  const totalPropertiesEl = document.getElementById('total-properties');
  const totalRoomsEl = document.getElementById('total-rooms');
  const totalRevenueEl = document.getElementById('total-revenue');
  const propertySummaryContainer = document.getElementById('property-summary-container');
  const propertySummaryTemplate = document.getElementById('property-summary-template');
  const propertySelect = document.getElementById('property-select');
  const bookingDetailsBody = document.getElementById('booking-details-body');
  const calendarEl = document.getElementById('calendar');

  let properties = [];

const formatCurrency = (val) => {
  const formatted = new Intl.NumberFormat('en-IN', {
    style: 'decimal',
    maximumFractionDigits: 2,
  }).format(val);
  return `Rs ${formatted}`;
};


  const fetchDashboardData = async () => {
    try {
      const res = await fetch('get_dashboard_data.php');
      const data = await res.json();

      if (data.success) {
        properties = data.properties;
        renderDashboardSummary();
        populatePropertySelect();
        if (properties.length > 0) {
          propertySelect.value = properties[0].id;
          renderBookingDetails(properties[0].id);
          renderCalendar(properties[0].id);
        }
      } else {
        console.error('Failed to load data:', data.message);
      }
    } catch (err) {
      console.error('Error fetching dashboard data:', err);
    }
  };

  const calculateTotals = () => {
    const totalProperties = properties.length;
    let totalRooms = 0;
    let totalRevenue = 0;

    properties.forEach((prop) => {
      totalRooms += prop.rooms.length;
      prop.rooms.forEach((room) => {
        totalRevenue += room.revenue;
      });
    });

    return { totalProperties, totalRooms, totalRevenue };
  };

  const renderDashboardSummary = () => {
    const { totalProperties, totalRooms, totalRevenue } = calculateTotals();

    totalPropertiesEl.textContent = totalProperties;
    totalRoomsEl.textContent = totalRooms;
    totalRevenueEl.textContent = formatCurrency(totalRevenue);

    propertySummaryContainer.innerHTML = '';
    properties.forEach((prop) => {
      const clone = propertySummaryTemplate.content.cloneNode(true);
      const container = clone.querySelector('.property-summary-item');
      clone.querySelector('h4').textContent = prop.name;
      clone.querySelector('.rooms-available').textContent = prop.rooms.length;
      const propRevenue = prop.rooms.reduce((acc, r) => acc + r.revenue, 0);
      clone.querySelector('.property-revenue').textContent = formatCurrency(propRevenue);

      const roomRevenueList = clone.querySelector('.room-revenue-list');
      prop.rooms.forEach(room => {
        const li = document.createElement('li');
        li.textContent = `${room.name}: ${formatCurrency(room.revenue)}`;
        roomRevenueList.appendChild(li);
      });

      container.addEventListener('click', () => {
        propertySelect.value = prop.id;
        propertySelect.dispatchEvent(new Event('change'));
      });

      propertySummaryContainer.appendChild(clone);
    });
  };

  const populatePropertySelect = () => {
    propertySelect.innerHTML = '';
    properties.forEach(prop => {
      const option = document.createElement('option');
      option.value = prop.id;
      option.textContent = prop.name;
      propertySelect.appendChild(option);
    });
  };

  const renderBookingDetails = (propertyId) => {
    bookingDetailsBody.innerHTML = '';
    const prop = properties.find(p => p.id == propertyId);
    if (!prop) return;

    prop.bookings.forEach(booking => {
      const tr = document.createElement('tr');
      const bookedRoomsNames = booking.roomIds.map(id => {
        const r = prop.rooms.find(rm => rm.id === id);
        return r ? r.name : "Unknown Room";
      });

      tr.innerHTML = `
        <td>${booking.name}</td>
        <td>${booking.phone}</td>
        <td>${formatCurrency(booking.payment)}</td>
        <td>${booking.paymentMethod || 'N/A'}</td>
        <td>${bookedRoomsNames.join(', ')}</td>
      `;
      bookingDetailsBody.appendChild(tr);
    });
  };

  const generateCalendarGrid = (year, month) => {
    const date = new Date(year, month, 1);
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const startDay = date.getDay();
    return { daysInMonth, startDay };
  };

  const renderCalendar = (propertyId) => {
    calendarEl.innerHTML = '';
    const prop = properties.find(p => p.id == propertyId);
    if (!prop) return;

    let bookedDatesSet = new Set();
    prop.bookings.forEach(book => {
      book.bookedDates.forEach(dateStr => bookedDatesSet.add(dateStr));
    });

    const today = new Date();
    const year = today.getFullYear();
    const month = today.getMonth();
    const monthName = today.toLocaleString('default', {month:'long', year:'numeric'});

    const caption = document.createElement('div');
    caption.className = 'col-span-7 mb-2 font-bold text-indigo-700';
    caption.textContent = monthName;
    calendarEl.appendChild(caption);

    const weekdays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    weekdays.forEach(day => {
      const el = document.createElement('div');
      el.textContent = day;
      el.className = 'font-semibold text-indigo-500';
      calendarEl.appendChild(el);
    });

    const { daysInMonth, startDay } = generateCalendarGrid(year, month);

    for (let i = 0; i < startDay; i++) {
      const emptyCell = document.createElement('div');
      emptyCell.className = 'pointer-events-none';
      calendarEl.appendChild(emptyCell);
    }

    for (let d = 1; d <= daysInMonth; d++) {
      const dateStr = `${year}-${(month + 1).toString().padStart(2, '0')}-${d.toString().padStart(2, '0')}`;
      const el = document.createElement('div');

      if (bookedDatesSet.has(dateStr)) {
        el.textContent = d;
        el.className = 'booked-day rounded';
        el.title = `Booked on ${dateStr}`;
      } else {
        el.textContent = d;
        el.className = 'available-day rounded';
        el.title = `Available on ${dateStr}`;
      }

      calendarEl.appendChild(el);
    }

    calendarEl.classList.remove('fade-in');
    void calendarEl.offsetWidth;
    calendarEl.classList.add('fade-in');
  };

  propertySelect.addEventListener('change', (e) => {
    const selectedId = e.target.value;
    renderBookingDetails(selectedId);
    renderCalendar(selectedId);
  });

  fetchDashboardData();
});

