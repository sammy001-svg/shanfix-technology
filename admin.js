/**
 * ADMIN BACKEND LOGIC
 * Handles dashboard interactions, data persistence (mocked), and UI updates.
 */

document.addEventListener('DOMContentLoaded', () => {
    initAdmin();
});

function initAdmin() {
    // Check if we are on the login page
    const loginForm = document.getElementById('adminLoginForm');
    if (loginForm) {
        handleLogin(loginForm);
    }

    // Check for auth (simple mock)
    if (window.location.pathname.includes('/admin/') && !window.location.pathname.includes('login.php')) {
        checkAuth();
        _loadUnreadBadge();
    }

    // Sidebar active state
    updateNavActive();

    // Specific page init
    if (document.getElementById('messagesTableBody')) initMessagesPage();
    if (document.getElementById('slidesTableBody')) initAdvertsPage();
    if (document.getElementById('productModal')) initProductsPage();
    if (document.getElementById('categoryModal')) initCategoriesPage();
    if (document.getElementById('invoiceTableBody')) initBillingPage();
    if (document.getElementById('receiptsGrid')) initReceiptsPage();
    if (document.getElementById('adminTicketsBody')) initTicketsPage();
    if (document.getElementById('clientTableBody')) initClientsPage();
    if (document.getElementById('orderTableBody')) initOrdersPage();
    if (document.getElementById('portfolioGrid')) initPortfolioPage();
    if (document.getElementById('blogTableBody'))  initBlogPage();
}

function initTicketsPage() {
    const tbody = document.getElementById('adminTicketsBody');
    if (!tbody) return;

    let currentTicketId = null;

    async function renderTickets() {
        try {
            const response = await fetch('api/tickets.php');
            const data = await response.json();
            
            tbody.innerHTML = '';
            if (!data.success || data.tickets.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding: 3rem; color: var(--text-low);">No support tickets found in database.</td></tr>';
                return;
            }

            data.tickets.forEach(ticket => {
                const statusClass = ticket.status.toLowerCase() === 'open' ? 'badge-pending' : 'badge-paid';
                tbody.innerHTML += `
                    <tr>
                        <td><strong style="color:white;">${ticket.ticket_ref}</strong></td>
                        <td>
                            <div style="font-weight:600; color:white;">${ticket.clientName}</div>
                            <div style="font-size:0.8rem; color:var(--text-low);">${ticket.clientEmail}</div>
                        </td>
                        <td>${ticket.subject}</td>
                        <td><span class="status-badge" style="background:rgba(255,255,255,0.05); color:var(--text-low);">${ticket.priority}</span></td>
                        <td><span class="status-badge ${ticket.status.toLowerCase() === 'open' ? 'status-pending' : 'status-active'}">${ticket.status}</span></td>
                        <td style="text-align:right;">
                            <div class="flex-end-gap-sm">
                                <button class="admin-btn-sm admin-btn-primary" onclick="viewTicketDetails('${ticket.ticket_ref}')">
                                    <i class="fas fa-comment-dots"></i> View & Reply
                                </button>
                                ${ticket.status.toLowerCase() === 'open' ? `
                                <button class="admin-btn-sm admin-btn-secondary" style="border-color: #ef444455; color: #fca5a5;" onclick="closeTicket('${ticket.id}')">
                                    <i class="fas fa-check-circle"></i> Resolve
                                </button>` : ''}
                            </div>
                        </td>
                    </tr>
                `;
            });
        } catch (error) {
            console.error('Fetch Tickets Error:', error);
        }
    }

    window.viewTicketDetails = async function(ref) {
        try {
            const response = await fetch(`api/tickets.php?ref=${ref}`);
            const data = await response.json();

            if (data.success) {
                currentTicketId = data.ticket.id;
                document.getElementById('modalTicketTitle').innerHTML = `<span style="color:var(--p);">[${data.ticket.ticket_ref}]</span> ${data.ticket.subject} <small style="display:block; font-size:0.7rem; color:var(--text-low);">Client: ${data.ticket.client_name}</small>`;
                
                const thread = document.getElementById('ticketThread');
                thread.innerHTML = `
                    <div class="ticket-bubble bubble-client">
                        <span class="bubble-meta">${data.ticket.client_name} (${new Date(data.ticket.created_at).toLocaleString()})</span>
                        ${data.ticket.message}
                    </div>
                `;

                data.replies.forEach(reply => {
                    const isMe = reply.is_admin_reply == 1;
                    thread.innerHTML += `
                        <div class="ticket-bubble bubble-${isMe ? 'admin' : 'client'}">
                            <span class="bubble-meta">${isMe ? 'Shanfix Support' : data.ticket.client_name} (${new Date(reply.created_at).toLocaleString()})</span>
                            ${reply.message}
                        </div>
                    `;
                });

                document.getElementById('ticketModal').classList.add('active');
                setTimeout(() => thread.scrollTop = thread.scrollHeight, 100);
            }
        } catch (error) {
            alert('Could not load ticket conversation.');
        }
    };

    window.submitAdminReply = async function() {
        const messageInput = document.getElementById('adminReplyMessage');
        const message = messageInput.value.trim();
        if (!message || !currentTicketId) return;

        const btn = event.target;
        const original = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        try {
            const response = await fetch('api/tickets.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'add_reply', ticket_id: currentTicketId, message: message })
            });
            const data = await response.json();

            if (data.success) {
                messageInput.value = '';
                // Get ref from the title text
                const titleText = document.getElementById('modalTicketTitle').textContent;
                const ref = titleText.match(/\[(.*?)\]/)[1];
                viewTicketDetails(ref); // Refresh thread
            }
        } catch (error) {
            alert('Failed to send reply.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = original;
        }
    };

    window.closeTicketModal = function() {
        document.getElementById('ticketModal').style.display = 'none';
        document.getElementById('ticketModal').classList.remove('active');
    };

    window.closeTicket = async function(id) {
        if(confirm('Are you sure you want to mark this ticket as resolved/closed?')) {
            try {
                const response = await fetch('api/tickets.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'close', ticket_id: id })
                });
                const data = await response.json();
                if (data.success) renderTickets();
            } catch (error) {
                alert('Failed to close ticket.');
            }
        }
    };

    renderTickets();
}



async function handleLogin(form) {
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        const btn = form.querySelector('button');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Authorizing...';
        btn.disabled = true;

        try {
            const response = await fetch('api/login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password })
            });
            const data = await response.json();

            if (data.success) {
                sessionStorage.setItem('isAdmin', 'true');
                sessionStorage.setItem('adminName', data.admin_name);
                window.location.href = 'index.php';
            } else {
                alert(data.message || 'Invalid credentials!');
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        } catch (error) {
            alert('Server connection failed. Please try again.');
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    });
}

function checkAuth() {
    if (sessionStorage.getItem('isAdmin') !== 'true') {
        window.location.href = 'login.php';
    }
}

function updateNavActive() {
    const currentPath = window.location.pathname;
    const navItems = document.querySelectorAll('.admin-nav-item');
    navItems.forEach(item => {
        if (currentPath.includes(item.getAttribute('href'))) {
            item.classList.add('active');
        }
    });
}

// --- CATEGORIES MANAGEMENT ---
function initCategoriesPage() {
    const tableBody = document.getElementById('categoryTableBody');
    const categoryForm = document.getElementById('categoryForm');
    const categoryModal = document.getElementById('categoryModal');
    if (!tableBody || !categoryForm) return;

    window.allCategories = [];

    async function loadCategories() {
        try {
            const response = await fetch('api/categories.php');
            const data = await response.json();
            if (data.success) {
                window.allCategories = data.categories;
                renderCategories(data.categories);
            }
        } catch (e) { console.error(e); }
    }

    function renderCategories(categories) {
        tableBody.innerHTML = categories.map(cat => `
            <tr>
                <td>
                    <div class="flex-align-center gap-10">
                        <img src="../${cat.image_url || 'assets/img/placeholder.png'}" 
                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 12px; border: 1px solid var(--glass-border);"
                             onerror="this.src='../assets/img/placeholder.png'">
                    </div>
                </td>
                <td><strong>${cat.name}</strong></td>
                <td class="text-low" style="max-width: 300px;">${cat.description || 'No description provided.'}</td>
                <td><span class="status-badge status-active">Managed</span></td>
                <td>
                    <div class="flex-align-center gap-10">
                        <button class="icon-btn" onclick="editCategory(${JSON.stringify(cat).replace(/"/g, '&quot;')})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="icon-btn" style="color: #ef4444;" onclick="deleteCategory(${cat.id})">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    window.openCategoryModal = () => {
        categoryForm.reset();
        document.getElementById('cat_id').value = '';
        document.getElementById('catModalTitle').textContent = 'Add New Category';
        document.getElementById('catImagePreview').innerHTML = '';
        categoryModal.classList.add('active');
    };

    window.closeCategoryModal = () => {
        categoryModal.classList.remove('active');
    };

    window.editCategory = (cat) => {
        document.getElementById('cat_id').value = cat.id;
        document.getElementById('cat_name').value = cat.name;
        document.getElementById('cat_desc').value = cat.description || '';
        document.getElementById('cat_existing_image').value = cat.image_url || '';
        
        if (cat.image_url) {
            document.getElementById('catImagePreview').innerHTML = `<img src="../${cat.image_url}" style="max-width: 100px; border-radius: 8px;">`;
        } else {
            document.getElementById('catImagePreview').innerHTML = '';
        }

        document.getElementById('catModalTitle').textContent = 'Update Category';
        categoryModal.classList.add('active');
    };

    categoryForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(categoryForm);
        formData.append('name', document.getElementById('cat_name').value);
        formData.append('description', document.getElementById('cat_desc').value);
        formData.append('existing_image', document.getElementById('cat_existing_image').value);
        
        const id = document.getElementById('cat_id').value;
        if (id) {
            formData.append('id', id);
            formData.append('action', 'update');
        } else {
            formData.append('action', 'create');
        }

        const imgInput = document.getElementById('cat_image');
        if (imgInput.files[0]) formData.append('image', imgInput.files[0]);

        try {
            const response = await fetch('api/categories.php', { method: 'POST', body: formData });
            const data = await response.json();
            if (data.success) {
                closeCategoryModal();
                loadCategories();
                alert(data.message);
            }
        } catch (e) { alert('Failed to save category.'); }
    });

    window.deleteCategory = async (id) => {
        if (confirm('Delete this category? This will also remove all products under it.')) {
            try {
                await fetch('api/categories.php', {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id })
                });
                loadCategories();
            } catch (e) {}
        }
    };

    loadCategories();
}

// --- PRODUCTS MANAGEMENT ---
function initProductsPage() {
    const catalogContainer = document.getElementById('catalogContainer');
    const productForm = document.getElementById('productForm');
    const productModal = document.getElementById('productModal');
    if (!catalogContainer || !productForm) return;

    window.allProducts = [];

    async function loadCatalog() {
        try {
            const [pRes, cRes] = await Promise.all([
                fetch('api/products.php'),
                fetch('api/categories.php')
            ]);
            const pData = await pRes.json();
            const cData = await cRes.json();

            if (pData.success && cData.success) {
                window.allProducts = pData.products;
                
                // Popoulate category dropdown
                const pCat = document.getElementById('p_category');
                pCat.innerHTML = cData.categories.map(c => `<option value="${c.id}">${c.name}</option>`).join('');

                renderCatalog(pData.products, cData.categories);
            }
        } catch (e) { console.error(e); }
    }

    function renderCatalog(products, categories) {
        if (products.length === 0) {
            catalogContainer.innerHTML = '<div class="admin-card text-center py-50"><p>No products found. Start by adding your first product!</p></div>';
            return;
        }

        catalogContainer.innerHTML = categories.map(cat => {
            const catProducts = products.filter(p => p.category_id == cat.id);
            if (catProducts.length === 0) return '';

            return `
                <div class="category-group">
                    <h2 class="category-title">
                        <i class="fas fa-folder-open"></i> ${cat.name} 
                        <span style="font-size: 0.9rem; font-weight: 400; color: var(--text-low);">(${catProducts.length} items)</span>
                    </h2>
                    <div class="product-grid">
                        ${catProducts.map(p => `
                            <div class="product-card">
                                <div class="product-image-container">
                                    <img src="../${p.image_url || 'assets/img/placeholder.png'}" onerror="this.src='../assets/img/placeholder.png'">
                                    ${p.is_featured ? '<span class="product-badge"><i class="fas fa-star" style="color: #fbbf24;"></i> Featured</span>' : ''}
                                </div>
                                <div class="product-details">
                                    <div class="product-name">${p.name}</div>
                                    <div class="product-price">KES ${parseFloat(p.price).toLocaleString()}</div>
                                    <p class="text-low mb-15" style="font-size: 0.85rem; height: 3.2em; overflow: hidden;">${p.description}</p>
                                    <div class="product-actions">
                                        <button class="btn-icon-outline" onclick="editProduct(${p.id})" title="Edit Product">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                        <button class="btn-icon-outline" style="color: #ef4444;" onclick="deleteProduct(${p.id})" title="Delete Product">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        }).join('');
    }

    window.openProductModal = () => {
        productForm.reset();
        document.getElementById('p_id').value = '';
        document.getElementById('prodModalTitle').textContent = 'New Product Catalog Entry';
        document.getElementById('primaryPreview').innerHTML = '';
        document.getElementById('galleryPreview').innerHTML = '';
        productModal.classList.add('active');
    };

    window.closeProductModal = () => {
        productModal.classList.remove('active');
    };

    window.editProduct = (id) => {
        const p = window.allProducts.find(item => item.id == id);
        if (!p) return;

        document.getElementById('p_id').value = p.id;
        document.getElementById('p_name').value = p.name;
        document.getElementById('p_price').value = p.price;
        document.getElementById('p_category').value = p.category_id;
        document.getElementById('p_status').value = p.status;
        document.getElementById('p_desc').value = p.description;
        document.getElementById('p_featured').checked = p.is_featured == 1;
        document.getElementById('p_existing_image').value = p.image_url || '';
        if (document.getElementById('p_features')) {
            document.getElementById('p_features').value = p.features || '';
        }

        if (p.image_url) {
            document.getElementById('primaryPreview').innerHTML = `<img src="../${p.image_url}" style="max-width: 100px; border-radius: 8px;">`;
        }

        // Preview additional gallery images
        if (p.additional_images && p.additional_images.length > 0) {
            document.getElementById('galleryPreview').innerHTML = p.additional_images.map(img => `
                <img src="../${img}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
            `).join('');
        }

        document.getElementById('prodModalTitle').textContent = 'Update Product Entry';
        productModal.classList.add('active');
    };

    productForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(productForm);
        formData.append('name', document.getElementById('p_name').value);
        formData.append('price', document.getElementById('p_price').value);
        formData.append('category_id', document.getElementById('p_category').value);
        formData.append('description', document.getElementById('p_desc').value);
        formData.append('status', document.getElementById('p_status').value);
        formData.append('is_featured', document.getElementById('p_featured').checked ? 1 : 0);
        formData.append('existing_image', document.getElementById('p_existing_image').value);
        if (document.getElementById('p_features')) {
            formData.append('features', document.getElementById('p_features').value);
        }
        
        const id = document.getElementById('p_id').value;
        if (id) {
            formData.append('id', id);
            formData.append('action', 'update');
        } else {
            formData.append('action', 'create');
        }

        const primaryImg = document.getElementById('p_primary_image');
        if (primaryImg.files[0]) formData.append('primary_image', primaryImg.files[0]);

        const galleryImgs = document.getElementById('p_gallery_images');
        if (galleryImgs.files.length > 0) {
            for (let i = 0; i < galleryImgs.files.length; i++) {
                formData.append('additional_images[]', galleryImgs.files[i]);
            }
        }

        try {
            const response = await fetch('api/products.php', { method: 'POST', body: formData });
            const data = await response.json();
            if (data.success) {
                closeProductModal();
                loadCatalog();
                alert(data.message);
            }
        } catch (e) { alert('Failed to save product.'); }
    });

    window.deleteProduct = async (id) => {
        if (confirm('Are you sure you want to delete this product?')) {
            try {
                await fetch(`api/products.php?id=${id}&action=delete`);
                loadCatalog();
            } catch (e) {}
        }
    };

    loadCatalog();
}

// ── CLIENT SUBSCRIPTIONS MANAGEMENT ─────────────────────────────────────

function initServicesPage() {
    if (!document.getElementById('subscriptionsTableBody')) return;

    let _allSubs     = [];
    let _allClients  = [];
    let _allProducts = [];

    async function loadAll() {
        try {
            const [sRes, cRes, pRes] = await Promise.all([
                fetch('api/services.php'),
                fetch('api/clients.php'),
                fetch('api/products.php')
            ]);
            const [sData, cData, pData] = await Promise.all([sRes.json(), cRes.json(), pRes.json()]);

            if (sData.success) { _allSubs = sData.services; renderSubs(_allSubs); updateStats(_allSubs); }
            if (cData.success) {
                _allClients = cData.clients;
                const sel = document.getElementById('assign_client');
                if (sel) sel.innerHTML = '<option value="">— Select Client —</option>' +
                    cData.clients.map(c => `<option value="${c.id}">${c.full_name} (${c.email})</option>`).join('');
            }
            if (pData.success) {
                _allProducts = pData.products;
                const pSel = document.getElementById('assign_product');
                if (pSel) pSel.innerHTML = '<option value="">— None / Custom —</option>' +
                    pData.products.map(p => `<option value="${p.id}" data-name="${p.name}" data-price="${p.price}">${p.name} — KES ${parseFloat(p.price).toLocaleString()}</option>`).join('');
            }
        } catch (e) { console.error('Load subscriptions error:', e); }
    }

    function updateStats(subs) {
        const now  = new Date();
        const week = new Date(now); week.setDate(week.getDate() + 7);
        document.getElementById('stat_total_subs').textContent  = subs.length;
        document.getElementById('stat_active_subs').textContent = subs.filter(s => s.status === 'active').length;
        document.getElementById('stat_due_subs').textContent    = subs.filter(s =>
            s.status === 'active' && s.next_due_date && new Date(s.next_due_date) <= week
        ).length;
    }

    function renderSubs(subs) {
        const tbody = document.getElementById('subscriptionsTableBody');
        if (!subs.length) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding:2.5rem; color:var(--text-low);">No subscriptions yet. Use "Assign Service" to add one.</td></tr>';
            return;
        }
        const now  = new Date();
        const week = new Date(now); week.setDate(week.getDate() + 7);
        const statusClasses = { active:'status-active', pending:'status-pending', suspended:'badge-pending', terminated:'badge-cancelled' };

        tbody.innerHTML = subs.map(s => {
            const due     = s.next_due_date ? new Date(s.next_due_date) : null;
            const isOverdue = due && due < now  && s.status === 'active';
            const isDueSoon = due && due <= week && !isOverdue && s.status === 'active';
            const dueStr  = due ? due.toLocaleDateString('en-KE', {day:'numeric', month:'short', year:'numeric'}) : '—';
            return `
                <tr>
                    <td>
                        <div style="font-weight:700; color:var(--text-main);">${s.client_name}</div>
                        <div style="font-size:0.75rem; color:var(--text-low);">${s.client_email}</div>
                    </td>
                    <td>
                        <strong>${s.service_name}</strong>
                        ${s.product_name ? `<div style="font-size:0.75rem; color:var(--text-low);">${s.product_name}</div>` : ''}
                    </td>
                    <td style="text-transform:capitalize;">${s.billing_cycle}</td>
                    <td>
                        <span style="color:${isOverdue ? '#ef4444' : isDueSoon ? '#f59e0b' : 'var(--text-main)'}; font-weight:${isOverdue || isDueSoon ? '700' : '400'};">
                            ${isOverdue ? '<i class="fas fa-exclamation-triangle"></i> ' : isDueSoon ? '<i class="fas fa-clock"></i> ' : ''}${dueStr}
                        </span>
                    </td>
                    <td><span class="status-badge ${statusClasses[s.status] || ''}">${s.status}</span></td>
                    <td style="text-align:right;">
                        <div class="flex-end-gap-sm">
                            ${s.product_price > 0 ? `
                            <button class="admin-btn-sm admin-btn-primary" onclick="generateSubInvoice(${s.id})" title="Generate Renewal Invoice">
                                <i class="fas fa-file-invoice"></i>
                            </button>` : ''}
                            <button class="icon-btn" onclick="openEditSub(${JSON.stringify(s).replace(/"/g, '&quot;')})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="icon-btn" style="color:#ef4444;" onclick="deleteSub(${s.id})" title="Delete">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    window.filterSubscriptions = (status) => {
        const filtered = status === 'all' ? _allSubs : _allSubs.filter(s => s.status === status);
        renderSubs(filtered);
    };

    // ── Assign modal ──────────────────────────────────────────────────────
    window.openAssignModal = () => {
        document.getElementById('assign_client').value  = '';
        document.getElementById('assign_product').value = '';
        document.getElementById('assign_name').value    = '';
        document.getElementById('assign_cycle').value   = 'monthly';
        document.getElementById('assign_status').value  = 'active';
        document.getElementById('assign_due').value     = '';
        document.getElementById('assignModal').classList.add('active');
    };
    window.closeAssignModal = () => document.getElementById('assignModal').classList.remove('active');

    window.onProductSelect = () => {
        const sel = document.getElementById('assign_product');
        const opt = sel.options[sel.selectedIndex];
        if (opt && opt.dataset.name) {
            document.getElementById('assign_name').value = opt.dataset.name;
        }
    };

    window.saveAssignment = async () => {
        const userId = document.getElementById('assign_client').value;
        const name   = document.getElementById('assign_name').value.trim();
        if (!userId || !name) { alert('Client and service name are required.'); return; }

        try {
            const res  = await fetch('api/services.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action:        'create',
                    user_id:       userId,
                    product_id:    document.getElementById('assign_product').value || null,
                    service_name:  name,
                    billing_cycle: document.getElementById('assign_cycle').value,
                    status:        document.getElementById('assign_status').value,
                    next_due_date: document.getElementById('assign_due').value || null
                })
            });
            const data = await res.json();
            if (data.success) { closeAssignModal(); loadAll(); alert(data.message); }
            else alert(data.message);
        } catch (e) { alert('Failed to assign service.'); }
    };

    // ── Edit modal ────────────────────────────────────────────────────────
    window.openEditSub = (s) => {
        document.getElementById('edit_sub_id').value     = s.id;
        document.getElementById('edit_sub_name').value   = s.service_name;
        document.getElementById('edit_sub_cycle').value  = s.billing_cycle;
        document.getElementById('edit_sub_status').value = s.status;
        document.getElementById('edit_sub_due').value    = s.next_due_date || '';
        document.getElementById('editSubModal').classList.add('active');
    };
    window.closeEditSubModal = () => document.getElementById('editSubModal').classList.remove('active');

    window.saveEditSub = async () => {
        const id = document.getElementById('edit_sub_id').value;
        try {
            const res  = await fetch('api/services.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action:        'update',
                    id:            id,
                    service_name:  document.getElementById('edit_sub_name').value,
                    billing_cycle: document.getElementById('edit_sub_cycle').value,
                    status:        document.getElementById('edit_sub_status').value,
                    next_due_date: document.getElementById('edit_sub_due').value || null
                })
            });
            const data = await res.json();
            if (data.success) { closeEditSubModal(); loadAll(); alert(data.message); }
            else alert(data.message);
        } catch (e) { alert('Update failed.'); }
    };

    // ── Invoice generation ────────────────────────────────────────────────
    window.generateSubInvoice = async (id) => {
        const sub = _allSubs.find(s => s.id == id);
        const confirmMsg = sub
            ? `Generate renewal invoice for "${sub.service_name}" (${sub.client_name})?`
            : 'Generate renewal invoice for this subscription?';
        if (!confirm(confirmMsg)) return;

        try {
            const res  = await fetch('api/services.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'generate_invoice', id })
            });
            const data = await res.json();
            alert(data.message);
            if (data.success) loadAll();
        } catch (e) { alert('Invoice generation failed.'); }
    };

    window.generateDueInvoices = async () => {
        const btn  = document.getElementById('genDueBtn');
        const orig = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
        btn.disabled  = true;
        try {
            const res  = await fetch('api/services.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'generate_due' })
            });
            const data = await res.json();
            alert(data.message);
            if (data.success && data.generated > 0) loadAll();
        } catch (e) { alert('Batch generation failed.'); }
        finally { btn.innerHTML = orig; btn.disabled = false; }
    };

    window.deleteSub = async (id) => {
        if (!confirm('Remove this subscription? The client will no longer see it in their portal.')) return;
        try {
            const res  = await fetch('api/services.php', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id })
            });
            const data = await res.json();
            if (data.success) loadAll();
            else alert(data.message);
        } catch (e) { alert('Delete failed.'); }
    };

    loadAll();
}


// INVOICE GENERATION
async function initInvoicePage() {
    const form = document.getElementById('invoiceForm');
    const clientSelect = document.getElementById('client_select');
    if (!form) return;

    // Load Clients into dropdown
    try {
        const response = await fetch('api/clients.php');
        const data = await response.json();
        if (data.success && clientSelect) {
            data.clients.forEach(c => {
                const opt = document.createElement('option');
                opt.value = c.id;
                opt.textContent = `${c.full_name} (${c.company || 'Individual'})`;
                opt.dataset.phone = c.phone;
                opt.dataset.email = c.email;
                clientSelect.appendChild(opt);
            });

            clientSelect.addEventListener('change', (e) => {
                const selected = e.target.options[e.target.selectedIndex];
                document.getElementById('client_phone').value = selected.dataset.phone || '';
                document.getElementById('client_email').value = selected.dataset.email || '';
            });
        }
    } catch (e) {}

    // Helper to add more item rows
    window.addInvoiceItemRow = () => {
        const container = document.getElementById('invoiceItemsContainer');
        const newRow = document.createElement('div');
        newRow.className = 'invoice-item-row-dynamic mb-10';
        newRow.innerHTML = `
            <div class="form-group"><input type="number" class="form-control item-qty" value="1" min="1" required title="Quantity" placeholder="Qty"></div>
            <div class="form-group"><input type="text" class="form-control item-desc" required placeholder="Description" title="Description"></div>
            <div class="form-group"><input type="number" class="form-control item-price" required placeholder="Price" title="Unit Price"></div>
            <button type="button" class="admin-btn btn-delete-row" onclick="this.parentElement.remove()" title="Remove Item">
                <i class="fas fa-times"></i>
            </button>
        `;
        container.appendChild(newRow);
    };

    loadInvoicesFromDB();

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        // Collect items
        const itemRows = document.querySelectorAll('.invoice-item-row, .invoice-item-row-dynamic');
        const items = Array.from(itemRows).map(row => ({
            qty: parseFloat(row.querySelector('.item-qty').value),
            desc: row.querySelector('.item-desc').value,
            price: parseFloat(row.querySelector('.item-price').value)
        }));

        const totalAmount = items.reduce((sum, item) => sum + (item.qty * item.price), 0);
        
        try {
            const response = await fetch('api/invoices.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'create',
                    user_id: clientSelect.value,
                    amount: totalAmount,
                    items: items // We should ideally save these to an items table too, but let's keep it simple for now
                })
            });
            const data = await response.json();
            if (data.success) {
                alert('Invoice generated successfully!');
                loadInvoicesFromDB();
                form.reset();
            } else {
                alert(data.message);
            }
        } catch (error) {
            alert('Failed to generate invoice');
        }
    });
}

async function loadInvoicesFromDB() {
    try {
        const response = await fetch('api/invoices.php');
        const data = await response.json();
        if (data.success) {
            renderInvoices(data.invoices);
        }
    } catch (e) {}
}

function renderInvoices(invoices) {
    const tableBody = document.getElementById('invoiceTableBody');
    if (!tableBody) return;
    
    tableBody.innerHTML = invoices.map(inv => `
        <tr>
            <td><a href="#" class="inv-id-link" onclick="openPaymentModal(${inv.id}); return false;">#${inv.reference}</a></td>
            <td><strong>${inv.client_name}</strong></td>
            <td>General Services</td>
            <td>KES ${parseFloat(inv.amount).toLocaleString()}</td>
            <td><span class="status-badge status-${inv.status.toLowerCase()}">${inv.status}</span></td>
        </tr>
    `).join('');
}

window.openPaymentModal = (id) => {
    const invoices = JSON.parse(localStorage.getItem('admin_invoices')) || [];
    const inv = invoices.find(i => i.id === id);
    if (!inv) return;

    document.getElementById('pay_inv_id').textContent = `#${inv.id}`;
    document.getElementById('pay_total').textContent = `KES ${inv.amount.toLocaleString()}.00`;
    document.getElementById('pay_total').setAttribute('data-value', inv.amount);
    
    const payInput = document.getElementById('pay_amount_input');
    payInput.value = inv.paidAmount || 0;
    
    const balance = inv.amount - (inv.paidAmount || 0);
    document.getElementById('pay_balance').textContent = `KES ${balance.toLocaleString()}.00`;
    
    document.getElementById('paymentModal').style.display = 'block';
    window.currentInvoiceId = id;
};

window.closePaymentModal = () => {
    document.getElementById('paymentModal').style.display = 'none';
};

window.savePaymentUpdate = () => {
    const invoices = JSON.parse(localStorage.getItem('admin_invoices')) || [];
    const index = invoices.findIndex(i => i.id === window.currentInvoiceId);
    if (index === -1) return;

    const paid = parseFloat(document.getElementById('pay_amount_input').value || 0);
    const amount = invoices[index].amount;

    invoices[index].paidAmount = paid;
    
    if (paid >= amount) {
        invoices[index].status = 'Paid';
    } else if (paid > 0) {
        invoices[index].status = 'Partially Paid';
    } else {
        invoices[index].status = 'Pending';
    }

    localStorage.setItem('admin_invoices', JSON.stringify(invoices));
    renderInvoices(invoices);
    closePaymentModal();
};

window.sendRenewalReminders = async () => {
    const btn = document.getElementById('sendRenewalsBtn');
    if (!btn) return;
    const original = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
    btn.disabled = true;

    try {
        const res = await fetch('api/renewals.php', { method: 'GET' });
        const data = await res.json();
        alert(data.message || (data.success ? 'Reminders sent.' : 'Failed.'));
    } catch (e) {
        alert('Connection error. Could not send reminders.');
    } finally {
        btn.innerHTML = original;
        btn.disabled = false;
    }
};

// Helper to parse dates with ordinal suffixes (e.g., "February 10th 2026")
function helperParseInvDate(dateStr) {
    if (!dateStr) return new Date();
    // Remove st, nd, rd, th (case insensitive) followed by a space or end of string
    const cleaned = dateStr.replace(/(\d+)(st|nd|rd|th)\b/gi, '$1');
    return new Date(cleaned);
}

let _dashboardInvoices = [];

function initDashboard() {
    _loadDashboardData();
}

async function _loadDashboardData() {
    try {
        const [invRes, orderRes, ticketRes, analyticsRes] = await Promise.all([
            fetch('api/invoices.php'),
            fetch('api/orders.php'),
            fetch('api/tickets.php'),
            fetch('api/analytics.php')
        ]);
        const invData       = await invRes.json();
        const orderData     = await orderRes.json();
        const ticketData    = await ticketRes.json();
        const analyticsData = await analyticsRes.json();

        const invoices = invData.success ? invData.invoices : [];
        const orders   = orderData.success ? orderData.orders : [];
        const tickets  = ticketData.success ? ticketData.tickets : [];

        _dashboardInvoices = invoices;

        _renderDashboardStats(invoices, analyticsData);
        _renderActivityTable(invoices);
        _renderDashboardCharts(invoices, orders, tickets, analyticsData);
        _renderExpiringServices(analyticsData);
    } catch (error) {
        console.error('Dashboard load error:', error);
    }
}

function _renderDashboardStats(invoices, analytics) {
    if (!document.getElementById('stat_monthly_sales')) return;

    const now = new Date();
    const curMonth = now.getMonth();
    const curYear = now.getFullYear();
    const prevMonth = curMonth === 0 ? 11 : curMonth - 1;
    const prevMonthYear = curMonth === 0 ? curYear - 1 : curYear;

    let monthlyTotal = 0, prevMonthTotal = 0;
    let yearlyTotal = 0, prevYearTotal = 0;
    let pendingTotal = 0, paidCount = 0;

    invoices.forEach(inv => {
        const d = new Date(inv.created_at);
        const m = d.getMonth(), y = d.getFullYear();
        const amount = parseFloat(inv.amount || 0);
        const isPaid = inv.status.toLowerCase() === 'paid';

        if (y === curYear) {
            yearlyTotal += amount;
            if (m === curMonth) monthlyTotal += amount;
        } else if (y === curYear - 1) {
            prevYearTotal += amount;
        }

        if (y === prevMonthYear && m === prevMonth) prevMonthTotal += amount;

        if (!isPaid) pendingTotal += amount;
        else paidCount++;
    });

    const collectionRate = invoices.length > 0 ? Math.round((paidCount / invoices.length) * 100) : 0;

    document.getElementById('stat_monthly_sales').textContent = `KES ${monthlyTotal.toLocaleString()}`;
    document.getElementById('stat_pending_balances').textContent = `KES ${pendingTotal.toLocaleString()}`;
    document.getElementById('stat_yearly_sales').textContent = `KES ${yearlyTotal.toLocaleString()}`;
    document.getElementById('stat_collection_rate').textContent = `${collectionRate}%`;

    function setTrend(id, value, label, higherIsBetter = true) {
        const el = document.getElementById(id);
        if (!el || value === null) return;
        const isPositive = value >= 0;
        const isGood = higherIsBetter ? isPositive : !isPositive;
        el.innerHTML = `<i class="fas fa-arrow-${isPositive ? 'up' : 'down'}"></i> ${Math.abs(value).toFixed(1)}% ${label}`;
        el.className = `stat-trend ${isGood ? 'text-success' : 'text-danger'}`;
    }

    const monthTrend = prevMonthTotal > 0 ? ((monthlyTotal - prevMonthTotal) / prevMonthTotal * 100) : null;
    const yearTrend = prevYearTotal > 0 ? ((yearlyTotal - prevYearTotal) / prevYearTotal * 100) : null;
    const pendingCount = invoices.length - paidCount;

    setTrend('trend_monthly', monthTrend, 'vs last month');
    setTrend('trend_yearly', yearTrend, 'vs last year');

    const outstandingEl = document.getElementById('trend_outstanding');
    if (outstandingEl) {
        outstandingEl.textContent = `${pendingCount} unpaid invoice${pendingCount !== 1 ? 's' : ''}`;
        outstandingEl.className = `stat-trend ${pendingCount === 0 ? 'text-success' : 'text-danger'}`;
    }

    const collEl = document.getElementById('trend_collection');
    if (collEl) {
        collEl.textContent = `${paidCount} of ${invoices.length} invoices paid`;
        collEl.className = `stat-trend ${collectionRate >= 80 ? 'text-success' : collectionRate >= 50 ? 'text-low' : 'text-danger'}`;
    }

    // New analytics cards
    if (analytics && analytics.success) {
        const clientEl = document.getElementById('stat_total_clients');
        if (clientEl) clientEl.textContent = analytics.total_clients.toLocaleString();

        const clientTrendEl = document.getElementById('trend_clients');
        if (clientTrendEl) {
            const n = analytics.new_this_month;
            const prev = analytics.new_last_month;
            const arrow = n >= prev ? 'up' : 'down';
            const cls   = n >= prev ? 'text-success' : 'text-danger';
            clientTrendEl.innerHTML = `<i class="fas fa-arrow-${arrow}"></i> ${n} new this month`;
            clientTrendEl.className = `stat-trend ${cls}`;
        }

        const svcEl = document.getElementById('stat_active_services');
        if (svcEl) svcEl.textContent = analytics.active_services.toLocaleString();

        const expEl = document.getElementById('stat_expiring_soon');
        if (expEl) {
            const cnt = analytics.expiring_services.length;
            expEl.innerHTML = cnt > 0
                ? `<i class="fas fa-exclamation-triangle" style="color:#f59e0b;"></i> ${cnt} expiring in 7 days`
                : `<i class="fas fa-check-circle" style="color:#10b981;"></i> None expiring soon`;
            expEl.className = `stat-trend ${cnt > 0 ? 'text-danger' : 'text-success'}`;
        }

        const openTickEl = document.getElementById('stat_open_tickets_report');
        if (openTickEl) openTickEl.textContent = analytics.open_tickets;
    }
}

function _renderActivityTable(invoices) {
    const activityBody = document.getElementById('dashboard_activity');
    if (!activityBody) return;

    if (!invoices.length) {
        activityBody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:20px;">No recent transactions</td></tr>';
        return;
    }

    activityBody.innerHTML = invoices.slice(0, 8).map(inv => {
        const clientName = inv.user_id ? inv.reg_client_name : inv.guest_name;
        const statusClass = inv.status.toLowerCase() === 'paid' ? 'badge-paid' : 'badge-pending';
        return `
            <tr>
                <td><span class="text-low" style="font-size:0.8rem;">#</span>${inv.reference}</td>
                <td><div style="font-weight:600;">${clientName}</div><div style="font-size:0.7rem;color:var(--text-low);">${inv.user_id ? 'Registered' : 'Guest'}</div></td>
                <td><strong>KES ${parseFloat(inv.amount).toLocaleString()}</strong></td>
                <td><span class="badge ${statusClass}">${inv.status.toUpperCase()}</span></td>
            </tr>
        `;
    }).join('');
}

function _renderExpiringServices(analytics) {
    const list = document.getElementById('expiringServicesList');
    if (!list) return;

    if (!analytics || !analytics.success || analytics.expiring_services.length === 0) {
        list.innerHTML = '<p style="text-align:center; color:var(--text-low); padding:20px;"><i class="fas fa-check-circle" style="color:#10b981; margin-right:6px;"></i>No services expiring in the next 7 days.</p>';
        return;
    }

    list.innerHTML = analytics.expiring_services.map(svc => {
        const due = new Date(svc.next_due_date).toLocaleDateString('en-KE', { day:'numeric', month:'short' });
        const price = svc.price ? `KES ${parseFloat(svc.price).toLocaleString()}` : '';
        return `
            <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px solid var(--glass-border);">
                <div>
                    <div style="font-weight:600; font-size:0.9rem;">${svc.service_name}</div>
                    <div style="font-size:0.75rem; color:var(--text-low);">${svc.full_name}</div>
                </div>
                <div style="text-align:right;">
                    <div style="font-size:0.75rem; font-weight:700; color:#f59e0b;">${due}</div>
                    ${price ? `<div style="font-size:0.7rem; color:var(--text-low);">${price}</div>` : ''}
                </div>
            </div>
        `;
    }).join('');
}

function _renderDashboardCharts(invoices, orders, tickets, analytics) {
    const revenueCtx = document.getElementById('revenueChart');
    const statusCtx = document.getElementById('orderStatusChart');
    const collectionCtx = document.getElementById('collectionChart');
    const ticketCtx = document.getElementById('ticketSummaryChart');

    const MONTH_NAMES = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];

    // --- Revenue trend chart (re-renderable) ---
    let revenueChart = null;

    function buildRevenueChart(numMonths) {
        const now = new Date();
        const labels = [], data = [];
        for (let i = numMonths - 1; i >= 0; i--) {
            const d = new Date(now.getFullYear(), now.getMonth() - i, 1);
            labels.push(MONTH_NAMES[d.getMonth()]);
            data.push(invoices
                .filter(inv => {
                    const id = new Date(inv.created_at);
                    return id.getMonth() === d.getMonth() && id.getFullYear() === d.getFullYear();
                })
                .reduce((sum, inv) => sum + parseFloat(inv.amount), 0)
            );
        }

        if (revenueChart) revenueChart.destroy();
        if (!revenueCtx) return;

        revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Revenue (KES)',
                    data,
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99,102,241,0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointBackgroundColor: '#6366f1',
                    pointBorderColor: 'rgba(255,255,255,0.5)',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: ctx => `KES ${ctx.parsed.y.toLocaleString()}` } }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(255,255,255,0.05)' },
                        ticks: {
                            color: '#94a3b8', font: { size: 10 },
                            callback: v => `KES ${v >= 1000 ? (v / 1000).toFixed(0) + 'K' : v}`
                        }
                    },
                    x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 10 } } }
                }
            }
        });
    }

    const rangeSelect = document.getElementById('revenueRange');
    buildRevenueChart(parseInt(rangeSelect?.value || 6));
    if (rangeSelect) {
        rangeSelect.addEventListener('change', () => buildRevenueChart(parseInt(rangeSelect.value)));
    }

    // --- Order status doughnut ---
    if (statusCtx) {
        const statuses = ['Pending', 'Processing', 'Ready', 'Delivered'];
        const counts = statuses.map(s => orders.filter(o => o.status.toLowerCase() === s.toLowerCase()).length);
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: statuses,
                datasets: [{ data: counts, backgroundColor: ['rgba(245,158,11,0.7)','rgba(99,102,241,0.7)','rgba(16,185,129,0.7)','rgba(148,163,184,0.7)'], borderWidth: 0, hoverOffset: 10 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '75%',
                plugins: { legend: { position: 'bottom', labels: { color: '#94a3b8', usePointStyle: true, padding: 20, font: { size: 12 } } } }
            }
        });
    }

    // --- Collection by Month (stacked bar) ---
    if (collectionCtx) {
        const now = new Date();
        const labels = [], paidData = [], pendingData = [];
        for (let i = 5; i >= 0; i--) {
            const d = new Date(now.getFullYear(), now.getMonth() - i, 1);
            labels.push(MONTH_NAMES[d.getMonth()]);
            const monthInvs = invoices.filter(inv => {
                const id = new Date(inv.created_at);
                return id.getMonth() === d.getMonth() && id.getFullYear() === d.getFullYear();
            });
            paidData.push(monthInvs.filter(inv => inv.status.toLowerCase() === 'paid').reduce((s, inv) => s + parseFloat(inv.amount), 0));
            pendingData.push(monthInvs.filter(inv => inv.status.toLowerCase() !== 'paid').reduce((s, inv) => s + parseFloat(inv.amount), 0));
        }

        new Chart(collectionCtx, {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    { label: 'Collected', data: paidData, backgroundColor: 'rgba(16,185,129,0.7)', borderRadius: 4, borderSkipped: false },
                    { label: 'Outstanding', data: pendingData, backgroundColor: 'rgba(245,158,11,0.5)', borderRadius: 4, borderSkipped: false }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { color: '#94a3b8', usePointStyle: true, padding: 15, font: { size: 11 } } },
                    tooltip: { callbacks: { label: ctx => `${ctx.dataset.label}: KES ${ctx.parsed.y.toLocaleString()}` } }
                },
                scales: {
                    y: {
                        stacked: true, beginAtZero: true,
                        grid: { color: 'rgba(255,255,255,0.05)' },
                        ticks: { color: '#94a3b8', font: { size: 10 }, callback: v => `KES ${v >= 1000 ? (v / 1000).toFixed(0) + 'K' : v}` }
                    },
                    x: { stacked: true, grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 10 } } }
                }
            }
        });
    }

    // --- Support Overview (open vs resolved donut) ---
    if (ticketCtx) {
        const open = tickets.filter(t => t.status.toLowerCase() === 'open').length;
        const closed = tickets.filter(t => t.status.toLowerCase() !== 'open').length;
        new Chart(ticketCtx, {
            type: 'doughnut',
            data: {
                labels: ['Open', 'Resolved'],
                datasets: [{ data: [open, closed], backgroundColor: ['rgba(239,68,68,0.7)', 'rgba(16,185,129,0.7)'], borderWidth: 0, hoverOffset: 10 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '75%',
                plugins: {
                    legend: { position: 'bottom', labels: { color: '#94a3b8', usePointStyle: true, padding: 20, font: { size: 12 } } }
                }
            }
        });
    }

    // --- Client Growth Bar Chart ---
    const clientGrowthCtx = document.getElementById('clientGrowthChart');
    if (clientGrowthCtx && analytics && analytics.success) {
        const growthLabels = analytics.client_growth.map(r => r.month);
        const growthData   = analytics.client_growth.map(r => parseInt(r.count));

        new Chart(clientGrowthCtx, {
            type: 'bar',
            data: {
                labels: growthLabels,
                datasets: [{
                    label: 'New Clients',
                    data: growthData,
                    backgroundColor: 'rgba(6,182,212,0.6)',
                    borderColor: 'rgba(6,182,212,1)',
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: ctx => `${ctx.parsed.y} new client${ctx.parsed.y !== 1 ? 's' : ''}` } }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0, color: '#94a3b8', font: { size: 10 } },
                        grid: { color: 'rgba(255,255,255,0.05)' }
                    },
                    x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 10 } } }
                }
            }
        });
    }
}

window.generateStatement = (type) => {
    const invoices = _dashboardInvoices;

    if (typeof html2pdf === 'undefined') {
        alert('PDF library is loading. Please wait...');
        return;
    }

    const year = parseInt(document.getElementById('statement_year').value);
    const month = type === 'month' ? parseInt(document.getElementById('statement_month').value) : null;

    const monthNames = ["January","February","March","April","May","June","July","August","September","October","November","December"];
    const periodName = type === 'month' ? `${monthNames[month]} ${year}` : `Year ${year}`;

    const filtered = invoices.filter(inv => {
        const d = new Date(inv.created_at);
        return type === 'month'
            ? d.getFullYear() === year && d.getMonth() === month
            : d.getFullYear() === year;
    });

    if (filtered.length === 0) {
        alert(`No transactions found for ${periodName}`);
        return;
    }

    document.getElementById('st_title').textContent = `${type.toUpperCase()} FINANCIAL STATEMENT`;
    document.getElementById('st_period').textContent = periodName;
    document.getElementById('st_gen_date').textContent = new Date().toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });

    const itemsBody = document.getElementById('st_items_body');
    itemsBody.innerHTML = '';
    let totalSales = 0, totalPaid = 0;

    filtered.forEach(inv => {
        const amount = parseFloat(inv.amount || 0);
        const isPaid = inv.status.toLowerCase() === 'paid';
        const clientName = inv.user_id ? inv.reg_client_name : inv.guest_name;
        totalSales += amount;
        if (isPaid) totalPaid += amount;

        itemsBody.innerHTML += `
            <tr>
                <td>${new Date(inv.created_at).toLocaleDateString()}</td>
                <td>#${inv.reference}</td>
                <td>${clientName}</td>
                <td>${inv.status}</td>
                <td style="text-align:right;">KES ${amount.toLocaleString()}</td>
            </tr>
        `;
    });

    document.getElementById('st_total_sales').textContent = `KES ${totalSales.toLocaleString()}.00`;
    document.getElementById('st_total_paid').textContent = `KES ${totalPaid.toLocaleString()}.00`;
    document.getElementById('st_pending_balance').textContent = `KES ${(totalSales - totalPaid).toLocaleString()}.00`;

    const element = document.getElementById('statementTemplate');
    const opt = {
        margin: 0,
        filename: `Shanfix_Statement_${periodName.replace(/\s+/g, '_')}.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };

    const container = document.getElementById('statementTemplateContainer');
    container.style.display = 'block';

    html2pdf().set(opt).from(element).save().then(() => {
        container.style.display = 'none';
    }).catch(err => {
        console.error('Statement Generation Error:', err);
        container.style.display = 'none';
    });
};

window.generatePDF = (id) => {
    // ... existing generatePDF logic ...
    const stored = localStorage.getItem('admin_invoices');
    const invoices = stored ? JSON.parse(stored) : [];

    const inv = invoices.find(i => i.id.replace('#', '') === id.replace('#', '')) || invoices.find(i => i.id === id);

    if (!inv) {
        console.error('Invoice not found for PDF:', id);
        return;
    }

    if (typeof html2pdf === 'undefined') {
        alert('PDF library is loading. Please wait...');
        return;
    }

    populateTemplate(inv);
    const element = document.getElementById('invoiceTemplate');
    const opt = {
        margin:       0,
        filename:     `Invoice_${inv.id}.pdf`,
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2, useCORS: true },
        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };

    const container = document.getElementById('invoiceTemplateContainer');
    container.style.display = 'block';

    html2pdf().set(opt).from(element).toPdf().get('pdf').then(function (pdf) {
        const pageCount = pdf.internal.getNumberOfPages();
        if (pageCount > 1) {
            for (let i = pageCount; i > 1; i--) {
                pdf.deletePage(i);
            }
        }
    }).save().then(() => {
        container.style.display = 'none';
    }).catch(err => {
        console.error('PDF Generation Error:', err);
        container.style.display = 'none';
    });
};

// CLIENTS MANAGEMENT
function initClientsPage() {
    const clientForm = document.getElementById('clientForm');
    const tableBody = document.getElementById('clientTableBody');
    const searchInput = document.getElementById('clientSearch');
    const clientModal = document.getElementById('clientModal');
    
    if (!tableBody) return;

    // Local state for search
    let allClients = [];

    async function loadClients() {
        try {
            const response = await fetch('api/clients.php');
            const data = await response.json();
            if (data.success) {
                allClients = data.clients;
                renderClients(allClients);
            }
        } catch (e) {
            console.error('Load Clients Error:', e);
        }
    }

    function renderClients(clients) {
        if (!clients.length) {
            tableBody.innerHTML = '<tr><td colspan="5" style="text-align:center; padding:2.5rem; color:var(--text-low);">No clients found.</td></tr>';
            return;
        }
        const pending = clients.filter(c => c.status === 'inactive');
        const active  = clients.filter(c => c.status !== 'inactive');

        // Show pending approval banner
        const pendingBanner = pending.length > 0
            ? `<tr><td colspan="5" style="background:rgba(245,158,11,0.08); border-left:3px solid #f59e0b; padding:12px 16px; font-size:0.85rem; color:#d97706; font-weight:600;">
                   <i class="fas fa-clock" style="margin-right:8px;"></i>${pending.length} registration${pending.length > 1 ? 's' : ''} awaiting approval
               </td></tr>`
            : '';

        tableBody.innerHTML = pendingBanner + [...pending, ...active].map(c => {
            const isPending = c.status === 'inactive';
            return `
            <tr style="${isPending ? 'background:rgba(245,158,11,0.04);' : ''}">
                <td>
                    <div class="flex-align-center gap-10">
                        <div class="admin-avatar-sm" style="${isPending ? 'background:rgba(245,158,11,0.2); color:#d97706;' : ''}">${c.full_name.charAt(0)}</div>
                        <div>
                            <strong>${c.full_name}</strong><br>
                            <small class="text-low">${c.email}</small>
                        </div>
                    </div>
                </td>
                <td>
                    <strong>${c.company || 'Private Individual'}</strong><br>
                    <small class="text-low">${c.phone || 'No Phone'}</small>
                </td>
                <td><span class="status-badge ${isPending ? '' : 'status-' + c.status.toLowerCase()}" style="${isPending ? 'background:rgba(245,158,11,0.15); color:#d97706;' : ''}">${isPending ? 'Pending Approval' : c.status}</span></td>
                <td>${new Date(c.created_at).toLocaleDateString()}</td>
                <td style="text-align: right;">
                    <div class="flex-end-gap-sm">
                        ${isPending ? `
                        <button class="admin-btn-sm admin-btn-primary" onclick="approveClient(${c.id})" title="Approve">
                            <i class="fas fa-check"></i> Approve
                        </button>
                        <button class="admin-btn-sm" style="border-color:#ef444455; color:#fca5a5;" onclick="rejectClient(${c.id}, '${c.full_name}')" title="Reject">
                            <i class="fas fa-times"></i> Reject
                        </button>` : `
                        <button class="icon-btn" onclick="editClient(${JSON.stringify(c).replace(/"/g, '&quot;')})" title="Edit Profile">
                            <i class="fas fa-user-edit"></i>
                        </button>
                        <button class="icon-btn" onclick="resetClientPassword(${c.id}, '${c.full_name}')" title="Reset Password">
                            <i class="fas fa-shield-alt"></i>
                        </button>
                        <button class="icon-btn" style="color: #ef4444;" onclick="deleteClient(${c.id}, '${c.full_name}')" title="Delete Client">
                            <i class="fas fa-trash-alt"></i>
                        </button>`}
                    </div>
                </td>
            </tr>`;
        }).join('');
    }

    window.approveClient = async (id) => {
        try {
            const res  = await fetch('api/clients.php', {
                method: 'POST', headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'approve', id })
            });
            const data = await res.json();
            if (data.success) { loadClients(); alert(data.message); }
            else alert(data.message);
        } catch (e) { alert('Failed to approve client.'); }
    };

    window.rejectClient = async (id, name) => {
        const reason = prompt(`Reason for rejecting "${name}" (optional):`);
        if (reason === null) return; // cancelled
        try {
            const res  = await fetch('api/clients.php', {
                method: 'POST', headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'reject', id, reason })
            });
            const data = await res.json();
            if (data.success) { loadClients(); alert(data.message); }
            else alert(data.message);
        } catch (e) { alert('Failed to reject client.'); }
    };

    // Modal Controls
    window.openClientModal = () => {
        clientForm.reset();
        document.getElementById('c_id').value = '';
        document.getElementById('modalTitle').textContent = 'Register New Client';
        document.getElementById('passwordGroup').style.display = 'block';
        clientModal.classList.add('active');
    };

    window.closeClientModal = () => {
        clientModal.classList.remove('active');
    };

    window.editClient = (client) => {
        document.getElementById('c_id').value = client.id;
        document.getElementById('c_name').value = client.full_name;
        document.getElementById('c_email').value = client.email;
        document.getElementById('c_phone').value = client.phone || '';
        document.getElementById('c_company').value = client.company || '';
        document.getElementById('c_status').value = client.status;
        
        document.getElementById('modalTitle').textContent = 'Update Client Profile';
        document.getElementById('passwordGroup').style.display = 'none';
        clientModal.classList.add('active');
    };

    // Search Logic
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase();
            const filtered = allClients.filter(c => 
                c.full_name.toLowerCase().includes(query) || 
                c.email.toLowerCase().includes(query) || 
                (c.company && c.company.toLowerCase().includes(query))
            );
            renderClients(filtered);
        });
    }

    clientForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const action = document.getElementById('c_id').value ? 'update' : 'create';
        const payload = {
            action: action,
            id: document.getElementById('c_id').value,
            full_name: document.getElementById('c_name').value,
            email: document.getElementById('c_email').value,
            phone: document.getElementById('c_phone').value,
            company: document.getElementById('c_company').value,
            status: document.getElementById('c_status').value,
            password: document.getElementById('c_password').value
        };

        try {
            const response = await fetch('api/clients.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await response.json();
            if (data.success) {
                closeClientModal();
                loadClients();

                if (action === 'create' && data.client_id) {
                    openOnboardingWizard({
                        id:       data.client_id,
                        name:     payload.full_name,
                        email:    payload.email,
                        password: payload.password || 'Client@123'
                    });
                } else {
                    alert(data.message);
                }
            } else {
                alert(data.message);
            }
        } catch (e) {
            alert('Failed to save client data.');
        }
    });

    window.resetClientPassword = (id, name) => {
        document.getElementById('reset_client_name').textContent = name;
        window.currentResetId = id;
        document.getElementById('passwordModal').classList.add('active');
    };

    window.closePasswordModal = () => {
        document.getElementById('passwordModal').classList.remove('active');
    };

    window.confirmPasswordReset = async () => {
        const newPass = document.getElementById('new_password_input').value;
        try {
            const response = await fetch('api/clients.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'reset_password', id: window.currentResetId, new_password: newPass })
            });
            const data = await response.json();
            if (data.success) {
                alert(data.message);
                closePasswordModal();
            }
        } catch (e) {}
    };

    window.deleteClient = async (id, name) => {
        if (confirm(`Are you sure you want to permanently remove ${name}? This action cannot be undone.`)) {
            try {
                const response = await fetch('api/clients.php', {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id })
                });
                const data = await response.json();
                if (data.success) {
                    loadClients();
                    alert(data.message);
                }
            } catch (e) {
                alert('Deletion failed.');
            }
        }
    };

    window.generateOnboardingPDF = (client) => {
        const template = document.getElementById('welcomeLetterTemplate');
        if (!template) return;

        // Populate template
        document.getElementById('wl_name').textContent = client.full_name;
        document.getElementById('wl_email').textContent = client.email;
        document.getElementById('wl_pass').textContent = client.password;
        document.getElementById('wl_date').textContent = new Date().toLocaleDateString('en-US', { 
            month: 'long', day: 'numeric', year: 'numeric' 
        });

        const opt = {
            margin:       0,
            filename:     `Welcome_${client.full_name.replace(/\s+/g, '_')}.pdf`,
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        const container = document.getElementById('welcomeLetterContainer');
        container.style.display = 'block';

        html2pdf().set(opt).from(template).save().then(() => {
            container.style.display = 'none';
        }).catch(err => {
            console.error('PDF Generation Error:', err);
            container.style.display = 'none';
        });
    };

    // ── Onboarding wizard ─────────────────────────────────────────────────

    let _obClient = null; // { id, name, email, password }

    window.openOnboardingWizard = async (client) => {
        _obClient = client;
        document.getElementById('ob_client_headline').textContent =
            `Setting up ${client.name} (${client.email})`;

        // Reset wizard state
        document.getElementById('ob_assign_service').checked = false;
        document.getElementById('ob_gen_invoice').checked    = false;
        document.getElementById('ob_service_fields').style.display = 'none';
        document.getElementById('ob_invoice_fields').style.display = 'none';
        document.getElementById('ob_service_name').value    = '';
        document.getElementById('ob_due_date').value        = '';
        document.getElementById('ob_inv_amount').value      = '';
        document.getElementById('ob_inv_desc').value        = '';
        document.getElementById('ob_cycle').value           = 'monthly';

        // Load products into dropdown
        try {
            const res  = await fetch('api/products.php');
            const data = await res.json();
            const sel  = document.getElementById('ob_product');
            if (sel && data.success) {
                sel.innerHTML = '<option value="">— None / Custom —</option>' +
                    data.products.map(p =>
                        `<option value="${p.id}" data-name="${p.name}" data-price="${p.price}">${p.name} — KES ${parseFloat(p.price).toLocaleString()}</option>`
                    ).join('');
            }
        } catch (e) { /* non-critical */ }

        document.getElementById('onboardingModal').classList.add('active');
    };

    window.toggleObSection = (id, show) => {
        document.getElementById(id).style.display = show ? 'block' : 'none';
    };

    window.obProductSelect = () => {
        const sel = document.getElementById('ob_product');
        const opt = sel.options[sel.selectedIndex];
        if (opt && opt.dataset.name) {
            document.getElementById('ob_service_name').value = opt.dataset.name;
        }
        if (opt && opt.dataset.price) {
            if (document.getElementById('ob_gen_invoice').checked) {
                document.getElementById('ob_inv_amount').value = opt.dataset.price;
            }
        }
    };

    window.skipOnboarding = () => {
        document.getElementById('onboardingModal').classList.remove('active');
        _obClient = null;
    };

    window.sendObWelcomeEmail = async () => {
        if (!_obClient) return;
        const btn  = document.getElementById('ob_email_btn');
        const orig = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
        btn.disabled  = true;
        try {
            const res  = await fetch('api/onboard.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    client_id:    _obClient.id,
                    client_name:  _obClient.name,
                    client_email: _obClient.email,
                    send_email:   true
                })
            });
            const data = await res.json();
            alert(data.message);
        } catch (e) { alert('Email failed.'); }
        finally { btn.innerHTML = orig; btn.disabled = false; }
    };

    window.downloadObPDF = () => {
        if (!_obClient) return;
        generateOnboardingPDF({
            full_name: _obClient.name,
            email:     _obClient.email,
            password:  _obClient.password
        });
    };

    window.completeOnboarding = async () => {
        if (!_obClient) return;

        const assignService = document.getElementById('ob_assign_service').checked;
        const genInvoice    = document.getElementById('ob_gen_invoice').checked;

        if (assignService && !document.getElementById('ob_service_name').value.trim()) {
            alert('Please enter a service name, or uncheck "Assign a Service".');
            return;
        }
        if (genInvoice && !(parseFloat(document.getElementById('ob_inv_amount').value) > 0)) {
            alert('Please enter a valid invoice amount, or uncheck "Generate Initial Invoice".');
            return;
        }

        const btn  = document.getElementById('ob_complete_btn');
        const orig = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Setting up...';
        btn.disabled  = true;

        try {
            const payload = {
                client_id:      _obClient.id,
                client_name:    _obClient.name,
                client_email:   _obClient.email,
                assign_service: assignService,
                service_name:   document.getElementById('ob_service_name').value.trim(),
                product_id:     document.getElementById('ob_product').value || null,
                billing_cycle:  document.getElementById('ob_cycle').value,
                next_due_date:  document.getElementById('ob_due_date').value || null,
                generate_invoice: genInvoice,
                invoice_amount: parseFloat(document.getElementById('ob_inv_amount').value) || 0,
                invoice_desc:   document.getElementById('ob_inv_desc').value.trim()
            };

            const res  = await fetch('api/onboard.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            alert(data.message || (data.success ? 'Onboarding complete.' : 'Onboarding failed.'));
            if (data.success) skipOnboarding();
        } catch (e) {
            alert('Onboarding failed — connection error.');
        } finally {
            btn.innerHTML = orig;
            btn.disabled  = false;
        }
    };

    loadClients();
}

// ORDERS MANAGEMENT
function initOrdersPage() {
    const tableBody = document.getElementById('orderTableBody');
    if (!tableBody) return;

    async function loadOrders() {
        try {
            const response = await fetch('api/orders.php');
            const data = await response.json();
            if (data.success) {
                renderOrders(data.orders);
            }
        } catch (e) {}
    }

    function renderOrders(orders) {
        tableBody.innerHTML = orders.map(o => `
            <tr>
                <td>#ORD-${o.id}</td>
                <td><strong>${o.client_name || o.user_name || 'Guest'}</strong><br><small>${o.client_email}</small></td>
                <td>${o.items.map(i => `${i.quantity}x ${i.product_name}`).join(', ')}</td>
                <td>KES ${parseFloat(o.total_amount).toLocaleString()}</td>
                <td><span class="status-badge status-${o.status.toLowerCase()}">${o.status}</span></td>
                <td>${new Date(o.created_at).toLocaleDateString()}</td>
                <td>
                    <select onchange="updateOrderStatus(${o.id}, this.value)" class="form-control-sm">
                        <option value="pending" ${o.status === 'pending' ? 'selected' : ''}>Pending</option>
                        <option value="processing" ${o.status === 'processing' ? 'selected' : ''}>Processing</option>
                        <option value="ready" ${o.status === 'ready' ? 'selected' : ''}>Ready</option>
                        <option value="delivered" ${o.status === 'delivered' ? 'selected' : ''}>Delivered</option>
                    </select>
                    ${o.status === 'ready' ? `
                        <button class="admin-btn-sm admin-btn-primary" onclick="sendOrderReminder(${o.id})">
                            <i class="fas fa-bell"></i> Remind
                        </button>
                    ` : ''}
                </td>
            </tr>
        `).join('');
    }

    window.updateOrderStatus = async (id, status) => {
        try {
            const response = await fetch('api/orders.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'update_status', id: id, status: status })
            });
            const data = await response.json();
            if (data.success) loadOrders();
        } catch (e) {}
    };

    window.sendOrderReminder = async (id) => {
        try {
            const response = await fetch('api/orders.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'send_reminder', id: id })
            });
            const data = await response.json();
            if (data.success) alert(data.message);
        } catch (e) {}
    };

    loadOrders();
}

// --- BILLING & INVOICES MANAGEMENT ---
function initBillingPage() {
    const tableBody = document.getElementById('invoiceTableBody');
    const invoiceForm = document.getElementById('invoiceForm');
    const invoiceModal = document.getElementById('invoiceModal');
    if (!tableBody || !invoiceForm) return;

    let currentClientType = 'registered';
    window.availableProducts = [];

    async function loadInitialData() {
        try {
            const [cRes, pRes] = await Promise.all([
                fetch('api/clients.php'),
                fetch('api/products.php')
            ]);
            const cData = await cRes.json();
            const pData = await pRes.json();

            if (cData.success) {
                const sel = document.getElementById('inv_client_id');
                sel.innerHTML = '<option value="">-- Choose Client --</option>' + 
                    cData.clients.map(c => `<option value="${c.id}">${c.full_name} (${c.email})</option>`).join('');
            }
            if (pData.success) {
                window.availableProducts = pData.products;
            }
            loadInvoices();
        } catch (e) {}
    }

    async function loadInvoices() {
        try {
            const response = await fetch('api/invoices.php');
            const data = await response.json();
            if (data.success) {
                renderInvoices(data.invoices);
                updateBillingStats(data.invoices);
            }
        } catch (e) {}
    }

    function renderInvoices(invoices) {
        tableBody.innerHTML = invoices.map(i => {
            const clientName = i.user_id ? i.reg_client_name : i.guest_name;
            const statusClass = i.status === 'paid' ? 'status-paid' : 'status-pending';
            return `
                <tr>
                    <td><strong>#${i.reference}</strong></td>
                    <td>${clientName} ${i.user_id ? '<span class="badge badge-paid" style="font-size:0.6rem; padding:2px 5px;">REG</span>' : '<span class="badge badge-pending" style="font-size:0.6rem; padding:2px 5px;">GUEST</span>'}</td>
                    <td>${new Date(i.issue_date).toLocaleDateString()}</td>
                    <td>${new Date(i.due_date).toLocaleDateString()}</td>
                    <td><strong>KES ${parseFloat(i.amount).toLocaleString()}</strong></td>
                    <td><span class="status-badge ${statusClass}">${i.status.toUpperCase()}</span></td>
                    <td>
                        <div class="flex-align-center gap-10">
                            <button class="icon-btn" onclick="previewInvoice(${JSON.stringify(i).replace(/"/g, '&quot;')})" title="Preview"><i class="fas fa-eye"></i></button>
                            <button class="icon-btn" style="color:#22c55e;" onclick="downloadInvoice(${JSON.stringify(i).replace(/"/g, '&quot;')})" title="Download PDF"><i class="fas fa-download"></i></button>
                            <button class="icon-btn" style="color:var(--p);" onclick="emailInvoice(${JSON.stringify(i).replace(/"/g, '&quot;')})" title="Email to Client"><i class="fas fa-envelope"></i></button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function updateBillingStats(invoices) {
        const total = invoices.reduce((sum, i) => sum + parseFloat(i.amount), 0);
        const collected = invoices.filter(i => i.status === 'paid').reduce((sum, i) => sum + parseFloat(i.amount), 0);
        const pending = total - collected;

        document.getElementById('stat_total_invoiced').textContent = `KES ${total.toLocaleString()}`;
        document.getElementById('stat_total_collected').textContent = `KES ${collected.toLocaleString()}`;
        document.getElementById('stat_total_pending').textContent = `KES ${pending.toLocaleString()}`;
    }

    window.setClientType = (type) => {
        currentClientType = type;
        document.querySelectorAll('.toggle-btn').forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');
        
        document.getElementById('registeredClientSection').style.display = type === 'registered' ? 'block' : 'none';
        document.getElementById('guestClientSection').style.display = type === 'guest' ? 'block' : 'none';
    };

    window.addInvoiceItemRow = () => {
        const tbody = document.getElementById('invoiceItemsBody');
        const rowId = Date.now();
        const row = document.createElement('tr');
        row.id = `row_${rowId}`;
        row.innerHTML = `
            <td>
                <select class="form-control item-prod-select" onchange="handleProductSelect(${rowId}, this.value)">
                    <option value="">-- Custom Item --</option>
                    ${window.availableProducts.map(p => `<option value="${p.id}">${p.name}</option>`).join('')}
                </select>
                <input type="text" class="form-control mt-5 item-desc" placeholder="Item description...">
            </td>
            <td><input type="number" class="form-control item-qty" value="1" min="1" onchange="calculateTotals()"></td>
            <td><input type="number" class="form-control item-price" value="0" onchange="calculateTotals()"></td>
            <td><strong class="item-total">KES 0</strong></td>
            <td><i class="fas fa-times-circle invoice-row-action" onclick="removeInvoiceItemRow(${rowId})"></i></td>
        `;
        tbody.appendChild(row);
    };

    window.handleProductSelect = (rowId, prodId) => {
        const row = document.getElementById(`row_${rowId}`);
        const p = window.availableProducts.find(item => item.id == prodId);
        if (p) {
            row.querySelector('.item-desc').value = p.name;
            row.querySelector('.item-price').value = p.price;
        }
        calculateTotals();
    };

    window.removeInvoiceItemRow = (rowId) => {
        document.getElementById(`row_${rowId}`).remove();
        calculateTotals();
    };

    window.calculateTotals = () => {
        let subtotal = 0;
        document.querySelectorAll('#invoiceItemsBody tr').forEach(row => {
            const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            const total = qty * price;
            subtotal += total;
            row.querySelector('.item-total').textContent = `KES ${total.toLocaleString()}`;
        });

        const vatEnabled = document.getElementById('chk_vat').checked;
        const tax = vatEnabled ? (subtotal * 0.16) : 0;
        const grandTotal = subtotal + tax;

        document.getElementById('txt_subtotal').textContent = `KES ${subtotal.toLocaleString()}`;
        document.getElementById('txt_tax').textContent = `KES ${tax.toLocaleString()}`;
        document.getElementById('txt_grand_total').textContent = `KES ${grandTotal.toLocaleString()}`;
        
        return { subtotal, tax, grandTotal };
    };

    invoiceForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Front-end validation
        if (currentClientType === 'registered' && !document.getElementById('inv_client_id').value) {
            return alert('Please select a registered client before generating the invoice.');
        }
        if (currentClientType === 'guest' && !document.getElementById('inv_guest_name').value.trim()) {
            return alert('Please enter the guest client\'s name.');
        }

        const items = [];
        document.querySelectorAll('#invoiceItemsBody tr').forEach(row => {
            items.push({
                desc:  row.querySelector('.item-desc').value,
                qty:   row.querySelector('.item-qty').value,
                price: row.querySelector('.item-price').value
            });
        });

        if (items.length === 0) return alert('Please add at least one line item.');

        const totals = calculateTotals();

        // Loading state
        const submitBtn = document.querySelector('#invoiceModal button[type="submit"]') ||
                          document.getElementById('clientSubmitBtn');
        const origText  = submitBtn ? submitBtn.innerHTML : null;
        if (submitBtn) { submitBtn.disabled = true; submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...'; }

        const payload = {
            action:       'create',
            user_id:      currentClientType === 'registered' ? document.getElementById('inv_client_id').value : null,
            guest_name:   currentClientType === 'guest' ? document.getElementById('inv_guest_name').value : null,
            guest_email:  currentClientType === 'guest' ? document.getElementById('inv_guest_email').value : null,
            guest_phone:  currentClientType === 'guest' ? document.getElementById('inv_guest_phone').value : null,
            items:        items,
            subtotal:     totals.subtotal,
            tax_amount:   totals.tax,
            total_amount: totals.grandTotal,
            terms:        document.getElementById('inv_terms').value,
            due_date:     document.getElementById('inv_due_date').value
        };

        try {
            const response = await fetch('api/invoices.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await response.json();
            if (data.success) {
                alert('Invoice generated successfully!');
                closeInvoiceModal();
                loadInvoices();
            } else {
                alert(data.message || 'Failed to generate invoice. Please check all fields.');
            }
        } catch (err) {
            alert('Connection error. Could not reach the server — please try again.');
        } finally {
            if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = origText; }
        }
    });

    window.openInvoiceModal = () => {
        invoiceForm.reset();
        document.getElementById('invoiceItemsBody').innerHTML = '';
        addInvoiceItemRow();
        calculateTotals();
        invoiceModal.classList.add('active');
    };

    window.closeInvoiceModal = () => invoiceModal.classList.remove('active');

    window.previewInvoice = (inv) => {
        const area = document.getElementById('previewArea');
        const clientName = inv.user_id ? inv.reg_client_name : inv.guest_name;
        const clientEmail = inv.user_id ? inv.reg_client_email : inv.guest_email;
        
        area.innerHTML = `
            <div id="pdfContent" style="background:white; padding:40px; border-radius:8px; box-shadow:0 10px 30px rgba(0,0,0,0.05); color:#1e293b; font-family:'Inter', sans-serif;">
                <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:2px solid #f1f5f9; padding-bottom:20px; margin-bottom:30px;">
                    <img src="../assets/shanfix-logo.png" style="height:50px;">
                    <div style="text-align:right">
                        <h2 style="margin:0; color:var(--p);">INVOICE</h2>
                        <p style="margin:5px 0; font-weight:700;">#${inv.reference}</p>
                    </div>
                </div>
                
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:40px; margin-bottom:30px;">
                    <div>
                        <h4 style="color:#64748b; margin-bottom:10px; text-transform:uppercase; font-size:0.75rem;">Billed From</h4>
                        <p><strong>Shanfix Technology Ltd</strong><br>info@shanfixtechnology.com<br>+254 751 869 165</p>
                    </div>
                    <div>
                        <h4 style="color:#64748b; margin-bottom:10px; text-transform:uppercase; font-size:0.75rem;">Billed To</h4>
                        <p><strong>${clientName}</strong><br>${clientEmail || 'N/A'}<br>${inv.guest_phone || ''}</p>
                    </div>
                </div>

                <table style="width:100%; border-collapse:collapse; margin-bottom:30px;">
                    <thead>
                        <tr style="background:#f8fafc; text-align:left;">
                            <th style="padding:12px; border-bottom:1px solid #e2e8f0;">Description</th>
                            <th style="padding:12px; border-bottom:1px solid #e2e8f0;">Qty</th>
                            <th style="padding:12px; border-bottom:1px solid #e2e8f0;">Price</th>
                            <th style="padding:12px; border-bottom:1px solid #e2e8f0; text-align:right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${inv.items.map(item => `
                            <tr>
                                <td style="padding:12px; border-bottom:1px solid #f1f5f9;">${item.description}</td>
                                <td style="padding:12px; border-bottom:1px solid #f1f5f9;">${parseFloat(item.quantity)}</td>
                                <td style="padding:12px; border-bottom:1px solid #f1f5f9;">KES ${parseFloat(item.unit_price).toLocaleString()}</td>
                                <td style="padding:12px; border-bottom:1px solid #f1f5f9; text-align:right;">KES ${parseFloat(item.total_price).toLocaleString()}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>

                <div style="display:flex; justify-content:flex-end;">
                    <div style="width:250px;">
                        <div style="display:flex; justify-content:space-between; margin-bottom:10px;"><span>Subtotal:</span><span>KES ${parseFloat(inv.subtotal).toLocaleString()}</span></div>
                        <div style="display:flex; justify-content:space-between; margin-bottom:10px;"><span>VAT (16%):</span><span>KES ${parseFloat(inv.tax_amount).toLocaleString()}</span></div>
                        <div style="display:flex; justify-content:space-between; padding-top:10px; border-top:2px solid #f1f5f9; font-weight:800; font-size:1.1rem; color:var(--p);">
                            <span>Total Amount:</span><span>KES ${parseFloat(inv.amount).toLocaleString()}</span>
                        </div>
                    </div>
                </div>

                <div style="margin-top:50px; padding-top:20px; border-top:1px solid #f1f5f9; font-size:0.85rem; color:#64748b;">
                    <p><strong>Payment Terms:</strong> ${inv.terms_payment}</p>
                    <p><strong>M-PESA TILL NO: 5698666</strong></p>
                    <p style="text-align:center; margin-top:30px;">Thank you for your business!</p>
                </div>
            </div>
        `;
        
        document.getElementById('previewModal').classList.add('active');
        document.getElementById('downloadPdfBtn').onclick = () => downloadInvoice(inv);

        const emailBtn = document.getElementById('emailPdfBtn');
        if (emailBtn) emailBtn.onclick = () => emailInvoice(inv);
    };

    window.closePreviewModal = () => document.getElementById('previewModal').classList.remove('active');

    window.downloadInvoice = (inv) => {
        const element = document.getElementById('pdfContent');
        const opt = {
            margin: 10,
            filename: `Invoice_${inv.reference}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).save();
    };

    window.emailInvoice = async (inv) => {
        const clientName  = inv.user_id ? inv.reg_client_name  : inv.guest_name;
        const clientEmail = inv.user_id ? inv.reg_client_email : inv.guest_email;

        if (!clientEmail) {
            alert('No email address on file for this invoice.');
            return;
        }

        if (!confirm(`Send invoice #${inv.reference} to ${clientEmail}?`)) return;

        // Ensure preview content is rendered in the DOM
        if (!document.getElementById('pdfContent')) {
            window.previewInvoice(inv);
            document.getElementById('previewModal').classList.remove('active');
        }

        const emailBtn = document.getElementById('emailPdfBtn');
        const rowBtns  = document.querySelectorAll(`[title="Email to Client"]`);
        const origText = emailBtn ? emailBtn.innerHTML : '';
        if (emailBtn) { emailBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...'; emailBtn.disabled = true; }
        rowBtns.forEach(b => { b.disabled = true; });

        try {
            const element = document.getElementById('pdfContent');
            const opt = {
                margin: 10,
                filename: `Invoice_${inv.reference}.pdf`,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            const dataUri = await html2pdf().set(opt).from(element).outputPdf('datauristring');
            const base64  = dataUri.split(',')[1];

            const res  = await fetch('api/send_invoice.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    invoice_ref:     inv.reference,
                    recipient_email: clientEmail,
                    recipient_name:  clientName,
                    pdf_base64:      base64
                })
            });
            const data = await res.json();
            alert(data.message || (data.success ? 'Email sent!' : 'Failed to send.'));
        } catch (err) {
            console.error('Email invoice error:', err);
            alert('Failed to generate or send the invoice PDF.');
        } finally {
            if (emailBtn) { emailBtn.innerHTML = origText; emailBtn.disabled = false; }
            rowBtns.forEach(b => { b.disabled = false; });
        }
    };

    loadInitialData();
}

// --- RECEIPTS MANAGEMENT ---
function initReceiptsPage() {
    const grid = document.getElementById('receiptsGrid');
    if (!grid) return;

    async function loadReceipts() {
        try {
            const response = await fetch('api/receipts.php');
            const data = await response.json();
            if (data.success) {
                renderReceipts(data.receipts);
                updateReceiptStats(data.receipts);
            }
        } catch (e) {}
    }

    function renderReceipts(receipts) {
        if (receipts.length === 0) {
            grid.innerHTML = '<div class="admin-card text-center py-50 w-100"><p>No payment receipts found in the ledger.</p></div>';
            return;
        }

        grid.innerHTML = receipts.map(r => `
            <div class="receipt-mini-card">
                <div class="rec-header">
                    <span class="rec-ref">#${r.receipt_ref}</span>
                    <span class="rec-date">${new Date(r.created_at).toLocaleDateString()}</span>
                </div>
                <div class="rec-client">
                    <span class="rec-client-name">${r.client_name}</span>
                    <span class="rec-client-email">${r.client_email || 'No email provided'}</span>
                </div>
                <div class="rec-amount-box">
                    <span class="rec-label">Amount Paid</span>
                    <span class="rec-value">KES ${parseFloat(r.amount_paid).toLocaleString()}</span>
                </div>
                <div class="rec-actions">
                    <button class="btn-rec-action" onclick="viewReceipt(${JSON.stringify(r).replace(/"/g, '&quot;')})">
                        <i class="fas fa-eye"></i> Preview
                    </button>
                    <button class="btn-rec-action" style="color: #22c55e;" onclick="downloadReceipt(${JSON.stringify(r).replace(/"/g, '&quot;')})">
                        <i class="fas fa-download"></i> PDF
                    </button>
                </div>
                <div class="mt-10 text-low" style="font-size: 0.75rem; border-top: 1px solid var(--glass-border); pt-5;">
                    Linked Invoice: ${r.invoice_ref}
                </div>
            </div>
        `).join('');
    }

    function updateReceiptStats(receipts) {
        const total = receipts.reduce((sum, r) => sum + parseFloat(r.amount_paid), 0);
        document.getElementById('total_revenue').textContent = `KES ${total.toLocaleString()}`;
        document.getElementById('receipt_count').textContent = receipts.length;
        
        if (receipts.length > 0) {
            const last = new Date(receipts[0].created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            document.getElementById('last_collection_date').textContent = last;
        }
    }

    window.viewReceipt = (rec) => {
        const area = document.getElementById('receiptPreviewArea');
        
        area.innerHTML = `
            <div id="receiptPdfContent" style="background:white; padding:50px; border-radius:8px; color:#1e293b; font-family:'Inter', sans-serif; position:relative; overflow:hidden;">
                <!-- Watermark -->
                <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%) rotate(-45deg); font-size:10rem; color:rgba(34, 197, 94, 0.05); font-weight:900; pointer-events:none; z-index:0;">PAID</div>
                
                <div style="position:relative; z-index:1;">
                    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:3px solid #22c55e; padding-bottom:20px; margin-bottom:40px;">
                        <img src="../assets/shanfix-logo.png" style="height:60px;">
                        <div style="text-align:right">
                            <h1 style="margin:0; color:#166534; letter-spacing:2px; font-family:'Outfit', sans-serif;">OFFICIAL RECEIPT</h1>
                            <p style="margin:5px 0; font-weight:800; color:#1e293b;">#${rec.receipt_ref}</p>
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:40px; margin-bottom:40px;">
                        <div>
                            <h4 style="color:#64748b; margin-bottom:10px; text-transform:uppercase; font-size:0.75rem; border-bottom:1px solid #f1f5f9; pb-5;">Payment From</h4>
                            <p style="font-size:1.1rem; margin-bottom:5px;"><strong>${rec.client_name}</strong></p>
                            <p style="color:#64748b;">${rec.client_email || ''}</p>
                        </div>
                        <div style="text-align:right">
                            <h4 style="color:#64748b; margin-bottom:10px; text-transform:uppercase; font-size:0.75rem; border-bottom:1px solid #f1f5f9; pb-5;">Transaction Details</h4>
                            <p><strong>Date Paid:</strong> ${new Date(rec.created_at).toLocaleDateString()}</p>
                            <p><strong>Invoice Ref:</strong> ${rec.invoice_ref}</p>
                            <p><strong>Method:</strong> M-PESA/Cash</p>
                        </div>
                    </div>

                    <div style="background:#f8fafc; padding:25px; border-radius:12px; border-left:5px solid #22c55e; margin-bottom:40px;">
                        <p style="margin:0; color:#64748b; font-size:0.8rem; text-transform:uppercase; font-weight:700;">Purpose of Payment:</p>
                        <p style="margin:10px 0 0; font-size:1rem; font-weight:600;">Settlement for ${rec.items.length} itemized services on invoice ${rec.invoice_ref}</p>
                    </div>

                    <table style="width:100%; border-collapse:collapse; margin-bottom:40px;">
                        <thead>
                            <tr style="background:#f1f5f9; text-align:left;">
                                <th style="padding:12px; border-bottom:1px solid #e2e8f0;">Service Description</th>
                                <th style="padding:12px; border-bottom:1px solid #e2e8f0; text-align:right;">Amount (KES)</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${rec.items.map(item => `
                                <tr>
                                    <td style="padding:12px; border-bottom:1px solid #f1f5f9;">${item.description}</td>
                                    <td style="padding:12px; border-bottom:1px solid #f1f5f9; text-align:right;">${parseFloat(item.total_price).toLocaleString()}</td>
                                </tr>
                            `).join('')}
                            <tr style="font-weight:700;">
                                <td style="padding:12px; text-align:right;">Subtotal:</td>
                                <td style="padding:12px; text-align:right;">${parseFloat(rec.subtotal).toLocaleString()}</td>
                            </tr>
                            <tr style="font-weight:700; color:#64748b;">
                                <td style="padding:12px; text-align:right;">Tax (VAT):</td>
                                <td style="padding:12px; text-align:right;">${parseFloat(rec.tax_amount).toLocaleString()}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div style="display:flex; justify-content:flex-end;">
                        <div style="width:300px; padding:25px; background:#22c55e; color:white; border-radius:12px; text-align:right; box-shadow:0 10px 20px rgba(34, 197, 94, 0.15);">
                            <p style="margin:0; opacity:0.8; font-size:0.85rem; text-transform:uppercase;">Total Amount Paid</p>
                            <h1 style="margin:10px 0 0; font-size:2.2rem; font-family:'Outfit', sans-serif;">KES ${parseFloat(rec.amount_paid).toLocaleString()}</h1>
                        </div>
                    </div>

                    <div style="margin-top:60px; display:flex; justify-content:space-between; align-items:flex-end;">
                        <div style="color:#94a3b8; font-style:italic; font-size:0.9rem;">
                            <p>Generated by Shanfix Admin Systems</p>
                            <p>Thank you for choosing Shanfix Technology!</p>
                        </div>
                        <div style="text-align:center; width:200px;">
                            <div style="border-bottom:2px solid #e2e8f0; margin-bottom:10px;"></div>
                            <p style="margin:0; color:#64748b; font-size:0.75rem; text-transform:uppercase; font-weight:700;">Authorized Signature</p>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.getElementById('receiptModal').classList.add('active');
        document.getElementById('downloadReceiptBtn').onclick = () => downloadReceipt(rec);
    };

    window.closeReceiptModal = () => document.getElementById('receiptModal').classList.remove('active');

    window.downloadReceipt = (rec) => {
        const element = document.getElementById('receiptPdfContent');
        const opt = {
            margin: 10,
            filename: `Receipt_${rec.receipt_ref}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).save();
    };

    window.exportLedger = () => {
        alert('Exporting verified ledger to CSV...');
    };

    loadReceipts();
}

// ── ADVERTS & BANNERS MANAGEMENT ───────────────────────────────────────────

function initAdvertsPage() {
    if (!document.getElementById('slidesTableBody')) return;
    loadSlides();
    loadBanners();

    document.getElementById('slideForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const fd = new FormData(e.target);
        fd.set('action', document.getElementById('slide_id').value ? 'update' : 'create');
        const btn = e.target.querySelector('[type=submit]');
        const orig = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;
        try {
            const res = await fetch('api/adverts.php', { method: 'POST', body: fd });
            const data = await res.json();
            if (data.success) { closeSlideModal(); loadSlides(); alert(data.message); }
            else alert(data.message);
        } finally { btn.innerHTML = orig; btn.disabled = false; }
    });

    document.getElementById('bannerForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const fd = new FormData(e.target);
        fd.set('action', document.getElementById('banner_id').value ? 'update' : 'create');
        const btn = e.target.querySelector('[type=submit]');
        const orig = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;
        try {
            const res = await fetch('api/adverts.php', { method: 'POST', body: fd });
            const data = await res.json();
            if (data.success) { closeBannerModal(); loadBanners(); alert(data.message); }
            else alert(data.message);
        } finally { btn.innerHTML = orig; btn.disabled = false; }
    });
}

async function loadSlides() {
    const tbody = document.getElementById('slidesTableBody');
    if (!tbody) return;
    try {
        const res = await fetch('api/adverts.php?type=slide');
        const data = await res.json();
        if (!data.success || !data.items.length) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align:center; padding:2rem; color:var(--text-low);">No slides yet. Add your first hero slide.</td></tr>';
            return;
        }
        tbody.innerHTML = data.items.map(s => `
            <tr>
                <td style="text-align:center; font-weight:700;">${s.sort_order}</td>
                <td style="max-width:200px;">
                    <strong style="color:var(--text-main);">${s.headline}</strong>
                </td>
                <td style="max-width:200px; font-size:0.82rem; color:var(--text-low);">
                    ${s.subtitle ? s.subtitle.substring(0, 80) + (s.subtitle.length > 80 ? '...' : '') : '<em>—</em>'}
                </td>
                <td style="font-size:0.8rem;">
                    ${s.btn1_text ? `<span class="status-badge" style="margin-bottom:4px; display:inline-block;">${s.btn1_text}</span>` : ''}
                    ${s.btn2_text ? `<span class="status-badge" style="display:inline-block;">${s.btn2_text}</span>` : ''}
                </td>
                <td>
                    ${s.bg_image
                        ? `<img src="../${s.bg_image}" style="width:80px; height:45px; object-fit:cover; border-radius:8px; border:1px solid var(--glass-border);">`
                        : '<span class="text-low" style="font-size:0.8rem;">Default gradient</span>'}
                </td>
                <td><span class="status-badge ${s.is_active ? 'status-active' : 'status-inactive'}">${s.is_active ? 'Active' : 'Inactive'}</span></td>
                <td style="text-align:right;">
                    <div class="flex-end-gap-sm">
                        <button class="icon-btn" onclick="editSlide(${JSON.stringify(s).replace(/"/g, '&quot;')})" title="Edit"><i class="fas fa-edit"></i></button>
                        <button class="icon-btn" style="color:#ef4444;" onclick="deleteAdvert(${s.id}, 'slide')" title="Delete"><i class="fas fa-trash-alt"></i></button>
                    </div>
                </td>
            </tr>
        `).join('');
    } catch (e) { console.error('Load slides error:', e); }
}

async function loadBanners() {
    const grid = document.getElementById('bannersGrid');
    if (!grid) return;
    try {
        const res = await fetch('api/adverts.php?type=banner');
        const data = await res.json();
        if (!data.success || !data.items.length) {
            grid.innerHTML = '<p class="text-low" style="grid-column:1/-1; text-align:center; padding:2rem;">No banners yet. Upload your first ad banner.</p>';
            return;
        }
        grid.innerHTML = data.items.map(b => `
            <div class="admin-card" style="padding:0; overflow:hidden; border-radius:16px;">
                <div style="position:relative;">
                    <img src="../${b.image_url}" style="width:100%; height:140px; object-fit:cover; display:block;"
                         onerror="this.src='../assets/img/placeholder.png'">
                    <span class="status-badge ${b.is_active ? 'status-active' : 'status-inactive'}"
                          style="position:absolute; top:10px; right:10px;">${b.is_active ? 'Active' : 'Off'}</span>
                </div>
                <div style="padding:14px;">
                    <div style="font-weight:700; color:var(--text-main); margin-bottom:4px;">${b.title || 'Untitled Banner'}</div>
                    <div style="font-size:0.75rem; color:var(--text-low); margin-bottom:12px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">${b.link_url || 'No link'}</div>
                    <div class="flex-end-gap-sm">
                        <button class="admin-btn-sm admin-btn-secondary" onclick="editBanner(${JSON.stringify(b).replace(/"/g, '&quot;')})">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="admin-btn-sm" style="border-color:#ef444455; color:#fca5a5;" onclick="deleteAdvert(${b.id}, 'banner')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `).join('');
    } catch (e) { console.error('Load banners error:', e); }
}

window.openSlideModal = () => {
    document.getElementById('slideForm').reset();
    document.getElementById('slide_id').value = '';
    document.getElementById('slide_existing_image').value = '';
    document.getElementById('slideImgPreview').innerHTML = '';
    document.getElementById('slideModalTitle').textContent = 'Add Hero Slide';
    document.getElementById('slideModal').style.display = 'flex';
};
window.closeSlideModal = () => { document.getElementById('slideModal').style.display = 'none'; };

window.editSlide = (s) => {
    document.getElementById('slide_id').value = s.id;
    document.getElementById('slide_headline').value = s.headline;
    document.getElementById('slide_subtitle').value = s.subtitle || '';
    document.getElementById('slide_btn1_text').value = s.btn1_text || '';
    document.getElementById('slide_btn1_link').value = s.btn1_link || '';
    document.getElementById('slide_btn2_text').value = s.btn2_text || '';
    document.getElementById('slide_btn2_link').value = s.btn2_link || '';
    document.getElementById('slide_sort_order').value = s.sort_order;
    document.getElementById('slide_is_active').checked = !!parseInt(s.is_active);
    document.getElementById('slide_existing_image').value = s.bg_image || '';
    document.getElementById('slideImgPreview').innerHTML = s.bg_image
        ? `<img src="../${s.bg_image}" style="max-width:100%; height:80px; object-fit:cover; border-radius:8px; margin-bottom:4px;">`
        : '';
    document.getElementById('slideModalTitle').textContent = 'Edit Hero Slide';
    document.getElementById('slideModal').style.display = 'flex';
};

window.openBannerModal = () => {
    document.getElementById('bannerForm').reset();
    document.getElementById('banner_id').value = '';
    document.getElementById('banner_existing_image').value = '';
    document.getElementById('bannerImgPreview').innerHTML = '';
    document.getElementById('bannerImgRequired').style.display = 'inline';
    document.getElementById('bannerModalTitle').textContent = 'Upload Ad Banner';
    document.getElementById('bannerModal').style.display = 'flex';
};
window.closeBannerModal = () => { document.getElementById('bannerModal').style.display = 'none'; };

window.editBanner = (b) => {
    document.getElementById('banner_id').value = b.id;
    document.getElementById('banner_title').value = b.title || '';
    document.getElementById('banner_link').value = b.link_url || '';
    document.getElementById('banner_sort_order').value = b.sort_order;
    document.getElementById('banner_is_active').checked = !!parseInt(b.is_active);
    document.getElementById('banner_existing_image').value = b.image_url;
    document.getElementById('bannerImgPreview').innerHTML = `<img src="../${b.image_url}" style="max-width:100%; height:80px; object-fit:cover; border-radius:8px; margin-bottom:4px;">`;
    document.getElementById('bannerImgRequired').style.display = 'none';
    document.getElementById('bannerModalTitle').textContent = 'Edit Ad Banner';
    document.getElementById('bannerModal').style.display = 'flex';
};

window.deleteAdvert = async (id, type) => {
    const label = type === 'banner' ? 'banner' : 'slide';
    if (!confirm(`Delete this ${label}? This cannot be undone.`)) return;
    try {
        const res = await fetch('api/adverts.php', {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, type })
        });
        const data = await res.json();
        if (data.success) { type === 'banner' ? loadBanners() : loadSlides(); }
        else alert(data.message);
    } catch (e) { alert('Delete failed.'); }
};

window.previewImg = (input, containerId) => {
    const container = document.getElementById(containerId);
    if (!input.files[0]) { container.innerHTML = ''; return; }
    const reader = new FileReader();
    reader.onload = e => {
        container.innerHTML = `<img src="${e.target.result}" style="max-width:100%; height:80px; object-fit:cover; border-radius:8px; margin-bottom:4px;">`;
    };
    reader.readAsDataURL(input.files[0]);
};

// ── GLOBAL: unread messages badge (loads on every admin page) ─────────────

async function _loadUnreadBadge() {
    try {
        const [msgRes, clientRes] = await Promise.all([
            fetch('api/messages.php?unread_count=1'),
            fetch('api/clients.php?pending_count=1')
        ]);
        const msgData    = await msgRes.json();
        const clientData = await clientRes.json();

        const msgBadge    = document.getElementById('sidebarMsgBadge');
        const clientBadge = document.getElementById('sidebarClientBadge');

        if (msgBadge && msgData.success && msgData.count > 0) {
            msgBadge.textContent   = msgData.count;
            msgBadge.style.display = 'inline-block';
        }
        if (clientBadge && clientData.success && clientData.count > 0) {
            clientBadge.textContent   = clientData.count;
            clientBadge.style.display = 'inline-block';
        }
    } catch (e) { /* non-critical */ }
}

// ── MESSAGES / CONTACT INBOX ──────────────────────────────────────────────

function initMessagesPage() {
    if (!document.getElementById('messagesTableBody')) return;

    let _allMessages = [];
    let _activeId    = null;

    async function loadMessages() {
        try {
            const res  = await fetch('api/messages.php');
            const data = await res.json();
            if (data.success) {
                _allMessages = data.messages;
                renderMessages(_allMessages);
                updateStats(_allMessages);
            }
        } catch (e) { console.error('Messages load error:', e); }
    }

    function renderMessages(msgs) {
        const tbody = document.getElementById('messagesTableBody');
        if (!msgs.length) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; padding:2.5rem; color:var(--text-low);">No messages yet.</td></tr>';
            return;
        }
        tbody.innerHTML = msgs.map(m => {
            const isUnread  = m.status === 'unread';
            const statusMap = { unread: 'badge-pending', read: '', replied: 'badge-paid' };
            return `
                <tr style="${isUnread ? 'font-weight:700;' : ''}">
                    <td><span class="status-badge ${statusMap[m.status] || ''}">${m.status}</span></td>
                    <td>
                        <div style="font-weight:${isUnread ? '800' : '600'}; color:var(--text-main);">${m.name}</div>
                        <div style="font-size:0.78rem; color:var(--text-low);">${m.email}</div>
                    </td>
                    <td style="max-width:280px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">${m.subject}</td>
                    <td style="font-size:0.82rem; color:var(--text-low);">${new Date(m.created_at).toLocaleDateString('en-KE', {day:'numeric', month:'short', year:'numeric'})}</td>
                    <td style="text-align:right;">
                        <div class="flex-end-gap-sm">
                            <button class="admin-btn admin-btn-primary admin-btn-sm" onclick="openMessage(${m.id})">
                                <i class="fas fa-${m.status === 'replied' ? 'reply' : 'envelope-open'}"></i>
                                ${m.status === 'replied' ? 'View Reply' : 'Open'}
                            </button>
                            <button class="icon-btn" style="color:#ef4444;" onclick="deleteMessage(${m.id})" title="Delete">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function updateStats(msgs) {
        document.getElementById('stat_total').textContent   = `Total: ${msgs.length}`;
        document.getElementById('stat_unread').textContent  = `Unread: ${msgs.filter(m => m.status === 'unread').length}`;
        document.getElementById('stat_replied').textContent = `Replied: ${msgs.filter(m => m.status === 'replied').length}`;
    }

    window.filterMessages = (status) => {
        const filtered = status === 'all' ? _allMessages : _allMessages.filter(m => m.status === status);
        renderMessages(filtered);
    };

    window.openMessage = async (id) => {
        const m = _allMessages.find(x => x.id == id);
        if (!m) return;
        _activeId = id;

        document.getElementById('msgModalTitle').textContent = m.subject;
        document.getElementById('msg_from').textContent      = m.name;
        document.getElementById('msg_email_link').textContent = m.email;
        document.getElementById('msg_email_link').href        = `mailto:${m.email}`;
        document.getElementById('msg_date').textContent      = new Date(m.created_at).toLocaleString('en-KE');
        document.getElementById('msg_body').textContent      = m.message;
        document.getElementById('replyText').value           = '';

        if (m.reply_message) {
            document.getElementById('replyAlreadySent').style.display = 'block';
            document.getElementById('msg_prev_reply').textContent      = m.reply_message;
        } else {
            document.getElementById('replyAlreadySent').style.display = 'none';
        }

        document.getElementById('msgModal').classList.add('active');

        if (m.status === 'unread') {
            await fetch('api/messages.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'mark_read', id })
            });
            m.status = 'read';
            updateStats(_allMessages);
            _loadUnreadBadge();
        }
    };

    window.closeMsgModal = () => {
        document.getElementById('msgModal').classList.remove('active');
        loadMessages();
    };

    window.sendReply = async () => {
        const reply = document.getElementById('replyText').value.trim();
        if (!reply) { alert('Please enter a reply message.'); return; }

        const btn  = document.getElementById('sendReplyBtn');
        const orig = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
        btn.disabled  = true;

        try {
            const res  = await fetch('api/messages.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'reply', id: _activeId, reply })
            });
            const data = await res.json();
            alert(data.message || (data.success ? 'Reply sent.' : 'Failed.'));
            if (data.success) closeMsgModal();
        } catch (e) {
            alert('Connection error.');
        } finally {
            btn.innerHTML = orig;
            btn.disabled  = false;
        }
    };

    window.deleteMessage = async (id) => {
        if (!confirm('Delete this message permanently?')) return;
        try {
            const res  = await fetch('api/messages.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'delete', id })
            });
            const data = await res.json();
            if (data.success) loadMessages();
        } catch (e) { alert('Delete failed.'); }
    };

    loadMessages();
}

// SUPPORT TICKETS MANAGEMENT
function initTicketsPage() {
    const tableBody = document.getElementById('adminTicketsBody');
    const statusFilter = document.getElementById('ticketStatusFilter');
    const modal = document.getElementById('ticketModal');
    if (!tableBody) return;

    let allTickets = [];
    window.activeTicketId = null;

    async function loadTickets() {
        try {
            const response = await fetch('api/tickets.php');
            const data = await response.json();
            if (data.success) {
                allTickets = data.tickets;
                filterAndRender();
            }
        } catch (e) {
            console.error('Tickets Load Error:', e);
        }
    }

    function filterAndRender() {
        const status = statusFilter.value;
        const filtered = status === 'all' ? allTickets : allTickets.filter(t => t.status.toLowerCase() === status);
        renderTickets(filtered);
    }

    function renderTickets(tickets) {
        if (tickets.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="6" class="text-center py-30">No support tickets found matching your filter.</td></tr>';
            return;
        }

        tableBody.innerHTML = tickets.map(t => `
            <tr>
                <td><strong>#${t.ticket_ref}</strong></td>
                <td>
                    <strong>${t.clientName}</strong><br>
                    <small class="text-low">${t.clientEmail}</small>
                </td>
                <td style="max-width: 300px;">
                    <div style="font-weight: 600; color: var(--text-main);">${t.subject}</div>
                </td>
                <td><span class="priority-pill priority-${t.priority.toLowerCase()}">${t.priority.toUpperCase()}</span></td>
                <td><span class="status-badge status-${t.status.toLowerCase()}">${t.status.toUpperCase()}</span></td>
                <td style="text-align: right;">
                    <button class="admin-btn admin-btn-secondary admin-btn-sm" onclick="viewTicket('${t.ticket_ref}')">
                        <i class="fas fa-comment-dots"></i> Open Thread
                    </button>
                </td>
            </tr>
        `).join('');
    }

    window.viewTicket = async (ref) => {
        try {
            const response = await fetch(`api/tickets.php?ref=${ref}`);
            const data = await response.json();
            if (data.success) {
                const t = data.ticket;
                window.activeTicketId = t.id;
                document.getElementById('modalTicketTitle').textContent = `Ticket #${t.ticket_ref}: ${t.subject}`;
                
                const thread = document.getElementById('ticketThread');
                let threadHtml = `
                    <div class="ticket-message ticket-client-message">
                        <div class="message-header">
                            <strong>${t.client_name}</strong>
                            <span>${new Date(t.created_at).toLocaleString()}</span>
                        </div>
                        <div class="message-body">${t.message}</div>
                    </div>
                `;

                threadHtml += data.replies.map(r => `
                    <div class="ticket-message ${r.is_admin_reply == 1 ? 'ticket-admin-message' : 'ticket-client-message'}">
                        <div class="message-header">
                            <strong>${r.author_name} ${r.is_admin_reply == 1 ? '<span class="badge badge-paid">Staff</span>' : ''}</strong>
                            <span>${new Date(r.created_at).toLocaleString()}</span>
                        </div>
                        <div class="message-body">${r.message}</div>
                    </div>
                `).join('');

                thread.innerHTML = threadHtml;
                thread.scrollTop = thread.scrollHeight;
                
                modal.classList.add('active');
            }
        } catch (e) {
            console.error('View Ticket Error:', e);
        }
    };

    window.closeTicketModal = () => {
        modal.classList.remove('active');
        window.activeTicketId = null;
        document.getElementById('adminReplyMessage').value = '';
    };

    window.submitAdminReply = async () => {
        const msg = document.getElementById('adminReplyMessage').value.trim();
        if (!msg) return alert('Please enter a response.');

        try {
            const response = await fetch('api/tickets.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'add_reply',
                    ticket_id: window.activeTicketId,
                    message: msg
                })
            });
            const data = await response.json();
            if (data.success) {
                document.getElementById('adminReplyMessage').value = '';
                // Reload thread
                const ref = document.getElementById('modalTicketTitle').textContent.split(':')[0].replace('Ticket #', '');
                viewTicket(ref);
                loadTickets(); // Refresh list to show status change
            } else {
                alert(data.message);
            }
        } catch (e) {
            alert('Failed to send response.');
        }
    };

    statusFilter.addEventListener('change', filterAndRender);
    loadTickets();
}

// ── TESTIMONIALS MANAGEMENT ───────────────────────────────────────────────

function initTestimonialsPage() {
    const tbody = document.getElementById('testimonialsTableBody');
    if (!tbody) return;

    async function loadTestimonials() {
        try {
            const res  = await fetch('api/testimonials.php');
            const data = await res.json();
            if (!data.success || !data.testimonials.length) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding:2rem; color:var(--text-low);">No testimonials yet.</td></tr>';
                return;
            }
            tbody.innerHTML = data.testimonials.map(t => `
                <tr>
                    <td><strong>${t.author}</strong></td>
                    <td><span style="font-size:0.82rem;">${[t.role, t.company].filter(Boolean).join(', ') || '—'}</span></td>
                    <td style="max-width:300px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; color:var(--text-low); font-size:0.85rem;">"${t.quote}"</td>
                    <td>${'★'.repeat(parseInt(t.rating))}${'☆'.repeat(5 - parseInt(t.rating))}</td>
                    <td><span class="status-badge ${t.is_active ? 'status-active' : 'status-inactive'}">${t.is_active ? 'Active' : 'Hidden'}</span></td>
                    <td style="text-align:right;">
                        <div class="flex-end-gap-sm">
                            <button class="icon-btn" onclick="editTestimonial(${JSON.stringify(t).replace(/"/g, '&quot;')})"><i class="fas fa-edit"></i></button>
                            <button class="icon-btn" style="color:#ef4444;" onclick="deleteTestimonial(${t.id})"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </td>
                </tr>
            `).join('');
        } catch (e) { console.error('Testimonials load error:', e); }
    }

    document.getElementById('testimonialForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const id     = document.getElementById('tm_id').value;
        const action = id ? 'update' : 'create';
        const btn    = e.target.querySelector('[type=submit]');
        const orig   = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled  = true;
        try {
            const res  = await fetch('api/testimonials.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action,
                    id: id || undefined,
                    quote:      document.getElementById('tm_quote').value,
                    author:     document.getElementById('tm_author').value,
                    role:       document.getElementById('tm_role').value,
                    company:    document.getElementById('tm_company').value,
                    rating:     document.getElementById('tm_rating').value,
                    sort_order: document.getElementById('tm_sort').value,
                    is_active:  document.getElementById('tm_active').checked ? 1 : 0,
                })
            });
            const data = await res.json();
            if (data.success) { closeTestimonialModal(); loadTestimonials(); alert(data.message); }
            else alert(data.message);
        } finally { btn.innerHTML = orig; btn.disabled = false; }
    });

    window.openTestimonialModal = () => {
        document.getElementById('testimonialForm').reset();
        document.getElementById('tm_id').value = '';
        document.getElementById('tm_active').checked = true;
        document.getElementById('testimonialModalTitle').textContent = 'Add Testimonial';
        document.getElementById('testimonialModal').style.display = 'flex';
    };
    window.closeTestimonialModal = () => { document.getElementById('testimonialModal').style.display = 'none'; };

    window.editTestimonial = (t) => {
        document.getElementById('tm_id').value     = t.id;
        document.getElementById('tm_quote').value  = t.quote;
        document.getElementById('tm_author').value = t.author;
        document.getElementById('tm_role').value   = t.role   || '';
        document.getElementById('tm_company').value= t.company|| '';
        document.getElementById('tm_rating').value = t.rating;
        document.getElementById('tm_sort').value   = t.sort_order;
        document.getElementById('tm_active').checked = !!parseInt(t.is_active);
        document.getElementById('testimonialModalTitle').textContent = 'Edit Testimonial';
        document.getElementById('testimonialModal').style.display = 'flex';
    };

    window.deleteTestimonial = async (id) => {
        if (!confirm('Delete this testimonial?')) return;
        try {
            const res  = await fetch('api/testimonials.php', { method:'DELETE', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ id }) });
            const data = await res.json();
            if (data.success) loadTestimonials();
        } catch (e) { alert('Delete failed.'); }
    };

    loadTestimonials();
}

// ── PORTFOLIO MANAGEMENT ──────────────────────────────────────────────────

function initPortfolioPage() {
    const grid = document.getElementById('portfolioGrid');
    if (!grid) return;

    async function loadProjects() {
        try {
            const res  = await fetch('api/portfolio.php');
            const data = await res.json();
            if (!data.success || !data.projects.length) {
                grid.innerHTML = '<p class="text-low" style="grid-column:1/-1; text-align:center; padding:2rem;">No portfolio projects yet. Add your first case study.</p>';
                return;
            }
            grid.innerHTML = data.projects.map(p => `
                <div class="admin-card" style="padding:0; overflow:hidden; border-radius:16px; border:1px solid var(--glass-border);">
                    <div style="position:relative; background:#1e293b; height:160px; overflow:hidden;">
                        ${p.image_url
                            ? `<img src="../${p.image_url}" style="width:100%; height:100%; object-fit:cover; display:block;" onerror="this.style.display='none'">`
                            : `<div style="height:100%; display:flex; align-items:center; justify-content:center; color:var(--text-low);"><i class="fas fa-image" style="font-size:2rem;"></i></div>`}
                        <div style="position:absolute; top:10px; left:10px; display:flex; gap:6px;">
                            <span class="status-badge ${p.is_active ? 'status-active' : 'status-inactive'}">${p.is_active ? 'Active' : 'Off'}</span>
                            ${p.is_featured ? '<span class="status-badge" style="background:rgba(245,158,11,0.2); color:#d97706;"><i class="fas fa-star"></i> Featured</span>' : ''}
                        </div>
                    </div>
                    <div style="padding:16px;">
                        ${p.badge ? `<span style="font-size:0.7rem; font-weight:700; color:var(--p); text-transform:uppercase; letter-spacing:1px;">${p.badge}</span>` : ''}
                        <div style="font-weight:700; font-size:1rem; color:var(--text-main); margin:6px 0;">${p.title}</div>
                        <div style="font-size:0.78rem; color:var(--text-low); margin-bottom:12px; height:2.4em; overflow:hidden;">${p.description || ''}</div>
                        ${(p.stat1_val || p.stat2_val) ? `
                        <div style="display:flex; gap:12px; margin-bottom:12px;">
                            ${p.stat1_val ? `<div style="font-size:0.75rem;"><strong style="color:var(--p);">${p.stat1_val}</strong><br><span class="text-low">${p.stat1_label}</span></div>` : ''}
                            ${p.stat2_val ? `<div style="font-size:0.75rem;"><strong style="color:var(--p);">${p.stat2_val}</strong><br><span class="text-low">${p.stat2_label}</span></div>` : ''}
                        </div>` : ''}
                        <div class="flex-end-gap-sm">
                            ${p.live_url ? `<a href="${p.live_url}" target="_blank" class="admin-btn-sm admin-btn-secondary" style="text-decoration:none;"><i class="fas fa-external-link-alt"></i></a>` : ''}
                            <button class="admin-btn-sm admin-btn-secondary" onclick="editProject(${JSON.stringify(p).replace(/"/g, '&quot;')})"><i class="fas fa-edit"></i> Edit</button>
                            <button class="admin-btn-sm" style="border-color:#ef444455; color:#fca5a5;" onclick="deleteProject(${p.id})"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </div>
            `).join('');
        } catch (e) { console.error('Portfolio load error:', e); }
    }

    document.getElementById('projectForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const fd  = new FormData(e.target);
        fd.set('action', document.getElementById('pf_id').value ? 'update' : 'create');
        const btn  = e.target.querySelector('[type=submit]');
        const orig = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled  = true;
        try {
            const res  = await fetch('api/portfolio.php', { method: 'POST', body: fd });
            const data = await res.json();
            if (data.success) { closeProjectModal(); loadProjects(); alert(data.message); }
            else alert(data.message);
        } finally { btn.innerHTML = orig; btn.disabled = false; }
    });

    window.openProjectModal = () => {
        document.getElementById('projectForm').reset();
        document.getElementById('pf_id').value = '';
        document.getElementById('pf_existing_image').value = '';
        document.getElementById('pfImgPreview').innerHTML = '';
        document.getElementById('projectModalTitle').textContent = 'Add Portfolio Project';
        document.getElementById('projectModal').style.display = 'flex';
    };
    window.closeProjectModal = () => { document.getElementById('projectModal').style.display = 'none'; };

    window.editProject = (p) => {
        document.getElementById('pf_id').value         = p.id;
        document.getElementById('pf_title').value       = p.title;
        document.getElementById('pf_badge').value       = p.badge      || '';
        document.getElementById('pf_desc').value        = p.description || '';
        document.getElementById('pf_live_url').value    = p.live_url   || '';
        document.getElementById('pf_stat1_val').value   = p.stat1_val  || '';
        document.getElementById('pf_stat1_label').value = p.stat1_label || '';
        document.getElementById('pf_stat2_val').value   = p.stat2_val  || '';
        document.getElementById('pf_stat2_label').value = p.stat2_label || '';
        document.getElementById('pf_sort').value        = p.sort_order;
        document.getElementById('pf_active').checked   = !!parseInt(p.is_active);
        document.getElementById('pf_featured').checked = !!parseInt(p.is_featured);
        document.getElementById('pf_existing_image').value = p.image_url || '';
        document.getElementById('pfImgPreview').innerHTML = p.image_url
            ? `<img src="../${p.image_url}" style="max-width:100%; height:80px; object-fit:cover; border-radius:8px; margin-bottom:4px;">`
            : '';
        document.getElementById('projectModalTitle').textContent = 'Edit Portfolio Project';
        document.getElementById('projectModal').style.display = 'flex';
    };

    window.deleteProject = async (id) => {
        if (!confirm('Delete this portfolio project?')) return;
        try {
            const res  = await fetch('api/portfolio.php', { method:'DELETE', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ id }) });
            const data = await res.json();
            if (data.success) loadProjects();
            else alert(data.message);
        } catch (e) { alert('Delete failed.'); }
    };

    loadProjects();
}

// ── BLOG / NEWS MANAGEMENT ────────────────────────────────────────────────

let _allPosts = [];

function initBlogPage() {
    if (!document.getElementById('blogTableBody')) return;

    window.loadPosts = async function() {
        try {
            const res  = await fetch('api/blog.php');
            const data = await res.json();
            if (!data.success) return;
            _allPosts = data.posts;
            renderPosts(_allPosts);
            updateBlogStats(_allPosts);
        } catch (e) { console.error('Blog load error:', e); }
    };

    function updateBlogStats(posts) {
        const published = posts.filter(p => p.status === 'published').length;
        const drafts    = posts.filter(p => p.status === 'draft').length;
        const views     = posts.reduce((s, p) => s + parseInt(p.views || 0), 0);
        const el = (id, v) => { const e = document.getElementById(id); if (e) e.textContent = v.toLocaleString(); };
        el('blog_total', posts.length);
        el('blog_published', published);
        el('blog_drafts', drafts);
        el('blog_views', views);
    }

    function renderPosts(posts) {
        const tbody = document.getElementById('blogTableBody');
        if (!posts.length) {
            tbody.innerHTML = '<tr><td colspan="7" style="text-align:center; padding:2.5rem; color:var(--text-low);">No posts yet. Click "New Post" to write your first article.</td></tr>';
            return;
        }
        tbody.innerHTML = posts.map(p => {
            const isPub    = p.status === 'published';
            const dateStr  = isPub && p.published_at
                ? new Date(p.published_at).toLocaleDateString('en-KE', {day:'numeric', month:'short', year:'numeric'})
                : new Date(p.created_at).toLocaleDateString('en-KE', {day:'numeric', month:'short', year:'numeric'});
            return `
            <tr>
                <td style="max-width:280px;">
                    <div style="display:flex; align-items:center; gap:12px;">
                        ${p.featured_image
                            ? `<img src="../${p.featured_image}" style="width:52px; height:36px; object-fit:cover; border-radius:8px; flex-shrink:0;" onerror="this.style.display='none'">`
                            : `<div style="width:52px; height:36px; background:rgba(99,102,241,0.1); border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0;"><i class="fas fa-newspaper" style="color:var(--p); font-size:0.85rem;"></i></div>`}
                        <div>
                            <strong style="display:block; line-height:1.3;">${p.title}</strong>
                            <span style="font-size:0.72rem; color:var(--text-low);">/blog/${p.slug}</span>
                        </div>
                    </div>
                </td>
                <td><span class="status-badge" style="background:rgba(99,102,241,0.1); color:var(--p);">${p.category || 'News'}</span></td>
                <td style="font-size:0.85rem; color:var(--text-low);">${p.author_name || '—'}</td>
                <td style="font-size:0.85rem;">${parseInt(p.views || 0).toLocaleString()}</td>
                <td><span class="status-badge ${isPub ? 'status-active' : ''}" style="${!isPub ? 'background:rgba(245,158,11,0.1); color:#d97706;' : ''}">${isPub ? 'Published' : 'Draft'}</span></td>
                <td style="font-size:0.82rem; color:var(--text-low);">${dateStr}</td>
                <td style="text-align:right;">
                    <div class="flex-end-gap-sm">
                        ${isPub ? `<a href="../post.php?slug=${p.slug}" target="_blank" class="icon-btn" title="View Live"><i class="fas fa-external-link-alt"></i></a>` : ''}
                        <button class="icon-btn" onclick="editPost(${p.id})" title="Edit"><i class="fas fa-edit"></i></button>
                        <button class="icon-btn" style="color:#ef4444;" onclick="deletePost(${p.id})" title="Delete"><i class="fas fa-trash-alt"></i></button>
                    </div>
                </td>
            </tr>`;
        }).join('');
    }

    window.filterPosts = (status) => {
        renderPosts(status === 'all' ? _allPosts : _allPosts.filter(p => p.status === status));
    };

    window.openPostEditor = () => {
        document.getElementById('postForm').reset();
        document.getElementById('post_id').value              = '';
        document.getElementById('post_existing_image').value  = '';
        document.getElementById('postImgPreview').innerHTML   = '';
        document.getElementById('postContentEditor').innerHTML = '';
        document.getElementById('editorTitle').textContent    = 'New Post';
        document.getElementById('postEditorModal').style.display = 'flex';
    };

    window.closePostEditor = () => {
        document.getElementById('postEditorModal').style.display = 'none';
    };

    window.editPost = async (id) => {
        try {
            const res  = await fetch(`api/blog.php?id=${id}`);
            const data = await res.json();
            if (!data.success) { alert('Could not load post.'); return; }
            const p = data.post;
            document.getElementById('post_id').value              = p.id;
            document.getElementById('post_title').value           = p.title;
            document.getElementById('post_category').value        = p.category || '';
            document.getElementById('post_status').value          = p.status;
            document.getElementById('post_excerpt').value         = p.excerpt || '';
            document.getElementById('post_existing_image').value  = p.featured_image || '';
            document.getElementById('postContentEditor').innerHTML = p.content;
            document.getElementById('postImgPreview').innerHTML   = p.featured_image
                ? `<img src="../${p.featured_image}" style="max-width:100%; height:80px; object-fit:cover; border-radius:8px; margin-bottom:4px;">`
                : '';
            document.getElementById('editorTitle').textContent    = 'Edit Post';
            document.getElementById('postEditorModal').style.display = 'flex';
        } catch (e) { alert('Failed to load post.'); }
    };

    window.deletePost = async (id) => {
        if (!confirm('Permanently delete this post?')) return;
        try {
            const res  = await fetch('api/blog.php', { method:'DELETE', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ id }) });
            const data = await res.json();
            if (data.success) loadPosts();
            else alert(data.message);
        } catch (e) { alert('Delete failed.'); }
    };

    loadPosts();
}
