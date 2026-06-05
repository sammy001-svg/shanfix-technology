<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php'); exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - Shanfix Admin</title>
    <link rel="stylesheet" href="../admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Outfit:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .ticket-type-row { display:grid; grid-template-columns:1fr 100px 80px 80px auto; gap:8px; align-items:center; background:var(--glass-bg); border:1px solid var(--glass-border); border-radius:10px; padding:10px 14px; margin-bottom:8px; }
        .tt-remove { background:none; border:none; color:var(--red); cursor:pointer; font-size:1rem; padding:4px; }
        .bookings-panel { margin-top:16px; }
        .booking-row { display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px solid var(--glass-border); font-size:0.85rem; }
        .booking-row:last-child { border-bottom:none; }
    </style>
</head>
<body class="admin-body">
<div class="admin-layout-wrapper">
    <aside class="admin-sidebar">
        <div class="admin-logo">Shanfix <span>Admin</span></div>
        <nav class="admin-nav">
            <a href="index.php" class="admin-nav-item"><i class="fas fa-chart-line"></i> <span>Insights</span></a>
            <a href="clients.php" class="admin-nav-item"><i class="fas fa-users"></i> <span>Clients</span></a>
            <a href="categories.php" class="admin-nav-item"><i class="fas fa-tags"></i> <span>Categories</span></a>
            <a href="services.php" class="admin-nav-item"><i class="fas fa-concierge-bell"></i> <span>Services</span></a>
            <a href="products.php" class="admin-nav-item"><i class="fas fa-box"></i> <span>Catalog</span></a>
            <a href="orders.php" class="admin-nav-item"><i class="fas fa-shopping-bag"></i> <span>Orders</span></a>
            <a href="invoices.php" class="admin-nav-item"><i class="fas fa-file-invoice"></i> <span>Billing</span></a>
            <a href="receipts.php" class="admin-nav-item"><i class="fas fa-receipt"></i> <span>Receipts</span></a>
            <a href="portfolio.php" class="admin-nav-item"><i class="fas fa-briefcase"></i> <span>Portfolio</span></a>
            <a href="blog.php" class="admin-nav-item"><i class="fas fa-newspaper"></i> <span>Blog</span></a>
            <a href="events.php" class="admin-nav-item active"><i class="fas fa-ticket-alt"></i> <span>Events</span></a>
            <a href="testimonials.php" class="admin-nav-item"><i class="fas fa-quote-right"></i> <span>Testimonials</span></a>
            <a href="adverts.php" class="admin-nav-item"><i class="fas fa-ad"></i> <span>Adverts</span></a>
            <a href="tickets.php" class="admin-nav-item"><i class="fas fa-life-ring"></i> <span>Support</span></a>
            <a href="messages.php" class="admin-nav-item"><i class="fas fa-inbox"></i> <span>Inbox</span></a>
            <div class="admin-nav-divider"></div>
            <a href="settings.php" class="admin-nav-item"><i class="fas fa-cog"></i> <span>Settings</span></a>
            <a href="../index.php" class="admin-nav-item"><i class="fas fa-external-link-alt"></i> <span>Live Site</span></a>
        </nav>
        <div class="admin-sidebar-footer">
            <a href="login.php" class="admin-nav-item admin-footer-link" onclick="sessionStorage.clear()"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
        </div>
    </aside>

    <main class="admin-main">
        <header class="admin-header">
            <div>
                <h1 class="admin-page-title">Events</h1>
                <p class="admin-subtitle">Create and manage ticketed events</p>
            </div>
            <div style="display:flex; gap:10px;">
                <a href="../event-ticketing.php" target="_blank" class="admin-btn admin-btn-secondary"><i class="fas fa-external-link-alt"></i> View Public Page</a>
                <button class="admin-btn admin-btn-primary" onclick="openEventModal()"><i class="fas fa-plus"></i> New Event</button>
            </div>
        </header>

        <section class="admin-content">
            <div class="admin-card glass-card">
                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Event</th>
                                <th>Date</th>
                                <th>Venue</th>
                                <th>Bookings</th>
                                <th>Revenue</th>
                                <th>Status</th>
                                <th style="text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="eventsTableBody">
                            <tr><td colspan="7" style="text-align:center; padding:30px; color:var(--text-low);"><i class="fas fa-spinner fa-spin"></i> Loading…</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
</div>

<!-- Event Modal -->
<div id="eventModal" class="admin-modal">
    <div class="admin-modal-content" style="max-width:720px;">
        <div class="admin-modal-header">
            <h3 class="admin-modal-title" id="evModalTitle">New Event</h3>
            <span class="admin-modal-close" onclick="closeEventModal()">&times;</span>
        </div>
        <div class="admin-modal-body" style="max-height:70vh; overflow-y:auto;">
            <form id="eventForm">
                <input type="hidden" id="ev_id">
                <div class="form-grid">
                    <div class="form-group" style="grid-column:1/-1;">
                        <label>Event Title *</label>
                        <input type="text" id="ev_title" class="form-control" required placeholder="e.g. Nairobi Tech Summit 2026">
                    </div>
                    <div class="form-group">
                        <label>Event Date & Time *</label>
                        <input type="datetime-local" id="ev_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>End Date & Time (optional)</label>
                        <input type="datetime-local" id="ev_end" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Venue Name</label>
                        <input type="text" id="ev_venue" class="form-control" placeholder="KICC, Nairobi">
                    </div>
                    <div class="form-group">
                        <label>Venue Address</label>
                        <input type="text" id="ev_venue_addr" class="form-control" placeholder="City Hall Way, Nairobi">
                    </div>
                    <div class="form-group">
                        <label>Organizer</label>
                        <input type="text" id="ev_organizer" class="form-control" value="Shanfix Technology">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select id="ev_status" class="form-control admin-select-custom">
                            <option value="published">Published</option>
                            <option value="draft">Draft</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <div class="form-group" style="grid-column:1/-1;">
                        <label>Event Image URL</label>
                        <input type="url" id="ev_image" class="form-control" placeholder="https://…">
                    </div>
                    <div class="form-group" style="grid-column:1/-1;">
                        <label>Description</label>
                        <textarea id="ev_desc" class="form-control" rows="3" placeholder="Describe the event…"></textarea>
                    </div>
                    <div class="form-group" style="display:flex; align-items:center; gap:10px; padding-bottom:4px;">
                        <input type="checkbox" id="ev_featured" style="width:18px; height:18px; accent-color:var(--p);">
                        <label for="ev_featured" style="cursor:pointer; font-weight:600;">Feature on event page</label>
                    </div>
                </div>

                <!-- Ticket Types -->
                <div style="margin-top:20px; border-top:1px solid var(--glass-border); padding-top:20px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                        <h4 style="margin:0; font-size:0.95rem;">Ticket Types</h4>
                        <button type="button" class="admin-btn-sm admin-btn-secondary" onclick="addTicketTypeRow()"><i class="fas fa-plus"></i> Add Type</button>
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 100px 80px 80px 36px; gap:8px; padding:0 0 6px; font-size:0.75rem; font-weight:700; color:var(--text-low);">
                        <span>Name</span><span>Price (KES)</span><span>Capacity</span><span>Status</span><span></span>
                    </div>
                    <div id="ticketTypesContainer"></div>
                </div>
            </form>

            <!-- Bookings sub-panel (shown when editing) -->
            <div id="bookingsPanel" class="bookings-panel" style="display:none;">
                <h4 style="margin:0 0 12px; font-size:0.95rem; border-top:1px solid var(--glass-border); padding-top:20px;">Recent Bookings</h4>
                <div id="bookingsList"></div>
            </div>
        </div>
        <div class="admin-modal-footer">
            <button class="admin-btn admin-btn-secondary" onclick="closeEventModal()">Cancel</button>
            <button class="admin-btn admin-btn-primary" onclick="saveEvent()"><i class="fas fa-save"></i> Save Event</button>
        </div>
    </div>
</div>

<script src="../admin.js?v=20"></script>
<script>
const API = 'api/events.php';

async function loadEvents() {
    const tbody = document.getElementById('eventsTableBody');
    try {
        const data = await fetch(API).then(r => r.json());
        if (!data.success || !data.events.length) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align:center; padding:30px; color:var(--text-low);">No events yet.</td></tr>';
            return;
        }
        tbody.innerHTML = data.events.map(ev => {
            const d    = new Date(ev.event_date);
            const date = d.toLocaleDateString('en-KE', { day:'numeric', month:'short', year:'numeric' });
            const statusColors = { published:'badge-paid', draft:'badge-pending', cancelled:'badge-cancelled', completed:'badge-info' };
            return `<tr>
                <td><div style="font-weight:700;">${ev.title}</div>${ev.is_featured ? '<span class="badge" style="background:rgba(245,158,11,0.15);color:#d97706;font-size:0.7rem;">Featured</span>' : ''}</td>
                <td>${date}</td>
                <td>${ev.venue || '—'}</td>
                <td><strong>${ev.booking_count || 0}</strong></td>
                <td>KES ${parseFloat(ev.revenue || 0).toLocaleString()}</td>
                <td><span class="badge ${statusColors[ev.status] || 'badge-pending'}">${ev.status}</span></td>
                <td style="text-align:right;">
                    <a href="../event-book.php?id=${ev.id}" target="_blank" class="admin-btn-sm admin-btn-secondary" title="Preview"><i class="fas fa-eye"></i></a>
                    <button class="admin-btn-sm admin-btn-secondary" onclick="editEvent(${ev.id})" title="Edit"><i class="fas fa-edit"></i></button>
                    <button class="admin-btn-sm admin-btn-danger" onclick="deleteEvent(${ev.id})" title="Delete"><i class="fas fa-trash"></i></button>
                </td>
            </tr>`;
        }).join('');
    } catch(e) {
        tbody.innerHTML = '<tr><td colspan="7" style="text-align:center; color:var(--red); padding:20px;">Failed to load events.</td></tr>';
    }
}

let _ttCounter = 0;
function addTicketTypeRow(tt) {
    const id  = ++_ttCounter;
    const row = document.createElement('div');
    row.className = 'ticket-type-row';
    row.id = 'tt-row-' + id;
    row.innerHTML = `
        <input type="text"   class="form-control tt-name"     placeholder="e.g. VIP"       value="${tt ? tt.name : ''}" style="padding:8px 10px; font-size:0.82rem;">
        <input type="number" class="form-control tt-price"    placeholder="Price"           value="${tt ? tt.price : ''}" style="padding:8px 10px; font-size:0.82rem;">
        <input type="number" class="form-control tt-capacity" placeholder="Cap (blank=∞)"  value="${tt && tt.capacity ? tt.capacity : ''}" style="padding:8px 10px; font-size:0.82rem;">
        <select class="form-control admin-select-custom tt-status" style="padding:8px 10px; font-size:0.82rem;">
            <option value="active"   ${!tt || tt.status==='active'   ? 'selected' : ''}>Active</option>
            <option value="paused"   ${tt && tt.status==='paused'   ? 'selected' : ''}>Paused</option>
            <option value="sold_out" ${tt && tt.status==='sold_out' ? 'selected' : ''}>Sold Out</option>
        </select>
        ${tt && tt.id ? `<input type="hidden" class="tt-id" value="${tt.id}">` : ''}
        <button type="button" class="tt-remove" onclick="this.closest('.ticket-type-row').remove()"><i class="fas fa-times"></i></button>`;
    document.getElementById('ticketTypesContainer').appendChild(row);
}

async function editEvent(id) {
    const data = await fetch(`${API}?id=${id}`).then(r => r.json());
    if (!data.success) return;
    const ev = data.event;

    document.getElementById('ev_id').value        = ev.id;
    document.getElementById('ev_title').value     = ev.title;
    document.getElementById('ev_date').value      = ev.event_date?.slice(0, 16) || '';
    document.getElementById('ev_end').value       = ev.end_date?.slice(0, 16) || '';
    document.getElementById('ev_venue').value     = ev.venue || '';
    document.getElementById('ev_venue_addr').value= ev.venue_address || '';
    document.getElementById('ev_organizer').value = ev.organizer || 'Shanfix Technology';
    document.getElementById('ev_image').value     = ev.image_url || '';
    document.getElementById('ev_desc').value      = ev.description || '';
    document.getElementById('ev_status').value    = ev.status;
    document.getElementById('ev_featured').checked= !!parseInt(ev.is_featured);

    const container = document.getElementById('ticketTypesContainer');
    container.innerHTML = '';
    _ttCounter = 0;
    (ev.ticket_types || []).forEach(tt => addTicketTypeRow(tt));

    // Show bookings
    const bkPanel = document.getElementById('bookingsPanel');
    const bkList  = document.getElementById('bookingsList');
    if (ev.booking_stats && parseInt(ev.booking_stats.count) > 0) {
        bkPanel.style.display = 'block';
        const bRes = await fetch(`../api/events.php?id=${id}`).catch(() => null);
        // Simple placeholder — just show count
        bkList.innerHTML = `<div class="booking-row"><span>${ev.booking_stats.count} paid booking(s)</span><strong>KES ${parseFloat(ev.booking_stats.revenue||0).toLocaleString()}</strong></div>`;
    } else {
        bkPanel.style.display = 'none';
    }

    document.getElementById('evModalTitle').textContent = 'Edit Event';
    document.getElementById('eventModal').classList.add('active');
}

async function saveEvent() {
    const id = document.getElementById('ev_id').value;
    const ticketRows = document.querySelectorAll('#ticketTypesContainer .ticket-type-row');
    const ticketTypes = Array.from(ticketRows).map(row => ({
        id:          row.querySelector('.tt-id')?.value || null,
        name:        row.querySelector('.tt-name').value.trim(),
        price:       parseFloat(row.querySelector('.tt-price').value) || 0,
        capacity:    row.querySelector('.tt-capacity').value || null,
        status:      row.querySelector('.tt-status').value,
        description: '',
    }));

    const body = {
        action:        id ? 'update' : 'create',
        id:            id ? parseInt(id) : undefined,
        title:         document.getElementById('ev_title').value.trim(),
        description:   document.getElementById('ev_desc').value.trim(),
        event_date:    document.getElementById('ev_date').value,
        end_date:      document.getElementById('ev_end').value || null,
        venue:         document.getElementById('ev_venue').value.trim(),
        venue_address: document.getElementById('ev_venue_addr').value.trim(),
        organizer:     document.getElementById('ev_organizer').value.trim(),
        image_url:     document.getElementById('ev_image').value.trim(),
        status:        document.getElementById('ev_status').value,
        is_featured:   document.getElementById('ev_featured').checked,
        ticket_types:  ticketTypes,
    };

    const res  = await fetch(API, { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(body) });
    const data = await res.json();
    if (data.success) { closeEventModal(); loadEvents(); }
    else alert(data.message || 'Save failed.');
}

async function deleteEvent(id) {
    if (!confirm('Delete this event and all its bookings?')) return;
    await fetch(API, { method:'DELETE', headers:{'Content-Type':'application/json'}, body: JSON.stringify({id}) });
    loadEvents();
}

function openEventModal() {
    document.getElementById('eventForm').reset();
    document.getElementById('ev_id').value = '';
    document.getElementById('ev_status').value = 'published';
    document.getElementById('ticketTypesContainer').innerHTML = '';
    document.getElementById('bookingsPanel').style.display = 'none';
    _ttCounter = 0;
    addTicketTypeRow(); // start with one empty row
    document.getElementById('evModalTitle').textContent = 'New Event';
    document.getElementById('eventModal').classList.add('active');
}

function closeEventModal() { document.getElementById('eventModal').classList.remove('active'); }

document.addEventListener('DOMContentLoaded', () => { checkAuth(); loadEvents(); });
</script>
</body>
</html>
