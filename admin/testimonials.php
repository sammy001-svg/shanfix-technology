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
    <title>Testimonials - Shanfix Admin</title>
    <link rel="stylesheet" href="../admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Outfit:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .stars { color:#f59e0b; font-size:0.9rem; }
        .t-quote { max-width:380px; font-style:italic; color:var(--text-low); font-size:0.82rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .toggle-active { cursor:pointer; padding:4px 12px; border-radius:20px; font-size:0.75rem; font-weight:700; border:none; transition:all .2s; }
        .toggle-active.on  { background:rgba(16,185,129,0.15); color:#10b981; }
        .toggle-active.off { background:rgba(239,68,68,0.15);  color:#ef4444; }
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
            <a href="events.php" class="admin-nav-item"><i class="fas fa-ticket-alt"></i> <span>Events</span></a>
            <a href="testimonials.php" class="admin-nav-item active"><i class="fas fa-quote-right"></i> <span>Testimonials</span></a>
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
                <h1 class="admin-page-title">Testimonials</h1>
                <p class="admin-subtitle">Manage client reviews shown on the homepage and About page</p>
            </div>
            <button class="admin-btn admin-btn-primary" onclick="openModal()">
                <i class="fas fa-plus"></i> Add Testimonial
            </button>
        </header>

        <section class="admin-content">
            <div class="admin-card glass-card">
                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Author</th>
                                <th>Quote</th>
                                <th>Rating</th>
                                <th>Order</th>
                                <th>Status</th>
                                <th style="text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="testimonialsTableBody">
                            <tr><td colspan="6" style="text-align:center; padding:30px; color:var(--text-low);"><i class="fas fa-spinner fa-spin"></i> Loading…</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
</div>

<!-- Modal -->
<div id="testModal" class="admin-modal">
    <div class="admin-modal-content" style="max-width:560px;">
        <div class="admin-modal-header">
            <h3 class="admin-modal-title" id="modalTitle">Add Testimonial</h3>
            <span class="admin-modal-close" onclick="closeModal()">&times;</span>
        </div>
        <div class="admin-modal-body">
            <form id="testForm">
                <input type="hidden" id="t_id">
                <div class="form-group">
                    <label>Quote / Review *</label>
                    <textarea id="t_quote" class="form-control" rows="4" required placeholder="What the client said…"></textarea>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Client Name *</label>
                        <input type="text" id="t_author" class="form-control" required placeholder="Jane Doe">
                    </div>
                    <div class="form-group">
                        <label>Role / Title</label>
                        <input type="text" id="t_role" class="form-control" placeholder="CEO">
                    </div>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Company</label>
                        <input type="text" id="t_company" class="form-control" placeholder="Acme Ltd">
                    </div>
                    <div class="form-group">
                        <label>Rating (1–5)</label>
                        <select id="t_rating" class="form-control admin-select-custom">
                            <option value="5">★★★★★ (5)</option>
                            <option value="4">★★★★☆ (4)</option>
                            <option value="3">★★★☆☆ (3)</option>
                            <option value="2">★★☆☆☆ (2)</option>
                            <option value="1">★☆☆☆☆ (1)</option>
                        </select>
                    </div>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Sort Order</label>
                        <input type="number" id="t_sort" class="form-control" value="0" min="0">
                    </div>
                    <div class="form-group" style="display:flex; align-items:flex-end; gap:10px; padding-bottom:4px;">
                        <input type="checkbox" id="t_active" checked style="width:18px; height:18px; accent-color:var(--p);">
                        <label for="t_active" style="cursor:pointer; font-weight:600;">Show on website</label>
                    </div>
                </div>
            </form>
        </div>
        <div class="admin-modal-footer">
            <button class="admin-btn admin-btn-secondary" onclick="closeModal()">Cancel</button>
            <button class="admin-btn admin-btn-primary" onclick="saveTestimonial()"><i class="fas fa-save"></i> Save</button>
        </div>
    </div>
</div>

<script src="../admin.js?v=20"></script>
<script>
const API = 'api/testimonials.php';

async function loadTestimonials() {
    const tbody = document.getElementById('testimonialsTableBody');
    try {
        const res  = await fetch(API);
        const data = await res.json();
        if (!data.success || !data.testimonials.length) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding:30px; color:var(--text-low);">No testimonials yet. Click "Add Testimonial" to get started.</td></tr>';
            return;
        }
        tbody.innerHTML = data.testimonials.map(t => `
            <tr>
                <td>
                    <div style="font-weight:700;">${t.author}</div>
                    <div style="font-size:0.75rem; color:var(--text-low);">${t.role || ''}${t.company ? (t.role ? ' · ' : '') + t.company : ''}</div>
                </td>
                <td><div class="t-quote">"${t.quote}"</div></td>
                <td><span class="stars">${'★'.repeat(parseInt(t.rating))}${'☆'.repeat(5 - parseInt(t.rating))}</span></td>
                <td>${t.sort_order}</td>
                <td>
                    <button class="toggle-active ${t.is_active ? 'on' : 'off'}" onclick="toggleActive(${t.id}, ${t.is_active})">
                        ${t.is_active ? 'Active' : 'Hidden'}
                    </button>
                </td>
                <td style="text-align:right;">
                    <button class="admin-btn-sm admin-btn-secondary" onclick="editTestimonial(${t.id})"><i class="fas fa-edit"></i></button>
                    <button class="admin-btn-sm admin-btn-danger" onclick="deleteTestimonial(${t.id})"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
        `).join('');
    } catch(e) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; color:var(--red); padding:20px;">Failed to load testimonials.</td></tr>';
    }
}

let _allTestimonials = [];
async function editTestimonial(id) {
    const res  = await fetch(API);
    const data = await res.json();
    const t    = (data.testimonials || []).find(x => x.id == id);
    if (!t) return;
    document.getElementById('t_id').value       = t.id;
    document.getElementById('t_quote').value    = t.quote;
    document.getElementById('t_author').value   = t.author;
    document.getElementById('t_role').value     = t.role || '';
    document.getElementById('t_company').value  = t.company || '';
    document.getElementById('t_rating').value   = t.rating || 5;
    document.getElementById('t_sort').value     = t.sort_order || 0;
    document.getElementById('t_active').checked = !!parseInt(t.is_active);
    document.getElementById('modalTitle').textContent = 'Edit Testimonial';
    document.getElementById('testModal').classList.add('active');
}

async function saveTestimonial() {
    const id = document.getElementById('t_id').value;
    const body = {
        action:     id ? 'update' : 'create',
        id:         id ? parseInt(id) : undefined,
        quote:      document.getElementById('t_quote').value.trim(),
        author:     document.getElementById('t_author').value.trim(),
        role:       document.getElementById('t_role').value.trim(),
        company:    document.getElementById('t_company').value.trim(),
        rating:     parseInt(document.getElementById('t_rating').value),
        sort_order: parseInt(document.getElementById('t_sort').value) || 0,
        is_active:  document.getElementById('t_active').checked,
    };
    const res  = await fetch(API, { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(body) });
    const data = await res.json();
    if (data.success) { closeModal(); loadTestimonials(); }
    else alert(data.message || 'Save failed.');
}

async function deleteTestimonial(id) {
    if (!confirm('Delete this testimonial?')) return;
    await fetch(API, { method:'DELETE', headers:{'Content-Type':'application/json'}, body: JSON.stringify({id}) });
    loadTestimonials();
}

async function toggleActive(id, current) {
    const res  = await fetch(API);
    const data = await res.json();
    const t    = (data.testimonials || []).find(x => x.id == id);
    if (!t) return;
    await fetch(API, { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({
        action:'update', id, quote:t.quote, author:t.author, role:t.role||'', company:t.company||'',
        rating:t.rating, sort_order:t.sort_order, is_active: !parseInt(current)
    })});
    loadTestimonials();
}

function openModal() {
    document.getElementById('testForm').reset();
    document.getElementById('t_id').value = '';
    document.getElementById('t_active').checked = true;
    document.getElementById('modalTitle').textContent = 'Add Testimonial';
    document.getElementById('testModal').classList.add('active');
}
function closeModal() { document.getElementById('testModal').classList.remove('active'); }

document.addEventListener('DOMContentLoaded', () => { checkAuth(); loadTestimonials(); });
</script>
</body>
</html>
