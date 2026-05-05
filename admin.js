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
    }

    // Sidebar active state
    updateNavActive();

    // Specific page init
    if (document.getElementById('productModal')) initProductsPage();
    if (document.getElementById('categoryModal')) initCategoriesPage();
    if (document.getElementById('invoiceTableBody')) initBillingPage();
    if (document.getElementById('receiptsGrid')) initReceiptsPage();
    if (document.getElementById('adminTicketsBody')) initTicketsPage();
    if (document.getElementById('clientTableBody')) initClientsPage();
    if (document.getElementById('orderTableBody')) initOrdersPage();
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

// --- SERVICES MANAGEMENT ---
function initServicesPage() {
    const servicesContainer = document.getElementById('servicesContainer');
    const serviceForm = document.getElementById('serviceForm');
    const serviceModal = document.getElementById('serviceModal');
    if (!servicesContainer || !serviceForm) return;

    window.allServices = [];

    async function loadServices() {
        try {
            const [pRes, cRes] = await Promise.all([
                fetch('api/products.php'),
                fetch('api/categories.php')
            ]);
            const pData = await pRes.json();
            const cData = await cRes.json();

            if (pData.success && cData.success) {
                window.allServices = pData.products;
                
                // Populate category dropdown
                const sCat = document.getElementById('s_category');
                sCat.innerHTML = cData.categories.map(c => `<option value="${c.id}">${c.name}</option>`).join('');

                renderServices(pData.products, cData.categories);
            }
        } catch (e) { console.error(e); }
    }

    function renderServices(products, categories) {
        if (products.length === 0) {
            servicesContainer.innerHTML = '<div class="admin-card text-center py-50"><p>No services found. Start by adding your first rate card!</p></div>';
            return;
        }

        // Filter products that are "services" (usually by category name or a flag, but here we show all as rate cards)
        servicesContainer.innerHTML = categories.map(cat => {
            const catServices = products.filter(p => p.category_id == cat.id);
            if (catServices.length === 0) return '';

            return `
                <div class="category-group mb-40">
                    <h2 class="category-title" style="border-bottom: 2px solid var(--glass-border); padding-bottom: 15px; margin-bottom: 25px; display: flex; align-items: center; gap: 15px;">
                        <i class="fas fa-layer-group text-primary"></i> ${cat.name}
                        <span class="badge" style="font-size: 0.8rem; background: var(--glass-bg);">${catServices.length} Rates</span>
                    </h2>
                    <div class="product-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px;">
                        ${catServices.map(s => {
                            const isFeatured = s.is_featured == 1;
                            return `
                            <div class="product-card service-card-admin ${isFeatured ? 'featured-service' : ''}" style="position: relative; border: ${isFeatured ? '2px solid var(--p)' : '1px solid var(--glass-border)'}; background: ${isFeatured ? 'rgba(99, 102, 241, 0.05)' : 'var(--glass-bg)'};">
                                ${isFeatured ? '<div class="popular-ribbon">Most Popular</div>' : ''}
                                <div class="product-details" style="padding: 25px;">
                                    <div class="flex-between mb-15">
                                        <div class="product-name" style="font-size: 1.2rem; font-weight: 800; font-family: 'Outfit';">${s.name}</div>
                                        <span class="badge ${s.status === 'active' ? 'badge-paid' : 'badge-pending'}" style="text-transform: uppercase; font-size: 0.7rem;">${s.status}</span>
                                    </div>
                                    <div class="product-price" style="font-size: 1.8rem; font-weight: 900; margin-bottom: 10px; color: var(--text-main);">
                                        <span style="font-size: 0.9rem; font-weight: 500; color: var(--text-low);">KES</span> ${parseFloat(s.price).toLocaleString()}
                                    </div>
                                    <p class="text-low mb-20" style="font-size: 0.9rem; line-height: 1.6; height: 3.2em; overflow: hidden;">${s.description || 'Professional solution for your business.'}</p>
                                    
                                    <div class="service-features-list mb-25" style="border-top: 1px solid var(--glass-border); padding-top: 20px;">
                                        ${(s.features || '').split('\n').filter(f => f.trim()).map(f => `
                                            <div class="feature-item-mini" style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px; font-size: 0.85rem; color: var(--text-main);">
                                                <i class="fas fa-check-circle text-success"></i> ${f.trim()}
                                            </div>
                                        `).slice(0, 4).join('')}
                                        ${(s.features || '').split('\n').filter(f => f.trim()).length > 4 ? '<div class="text-low" style="font-size: 0.75rem; margin-top: 5px;">+ more features included</div>' : ''}
                                    </div>

                                    <div class="product-actions" style="display: flex; gap: 10px;">
                                        <button class="admin-btn admin-btn-secondary" style="flex: 1;" onclick="editService(${s.id})">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="admin-btn" style="border-color: #ef444455; color: #ef4444;" onclick="deleteService(${s.id})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            `;
                        }).join('')}
                    </div>
                </div>
            `;
        }).join('');
    }

    window.openServiceModal = () => {
        serviceForm.reset();
        document.getElementById('s_id').value = '';
        document.getElementById('serviceModalTitle').textContent = 'Add New Service Rate Card';
        serviceModal.classList.add('active');
    };

    window.closeServiceModal = () => {
        serviceModal.classList.remove('active');
    };

    window.editService = (id) => {
        const s = window.allServices.find(item => item.id == id);
        if (!s) return;

        document.getElementById('s_id').value = s.id;
        document.getElementById('s_name').value = s.name;
        document.getElementById('s_price').value = s.price;
        document.getElementById('s_category').value = s.category_id;
        document.getElementById('s_status').value = s.status;
        document.getElementById('s_desc').value = s.description;
        document.getElementById('s_features').value = s.features || '';
        document.getElementById('s_featured').checked = s.is_featured == 1;

        document.getElementById('serviceModalTitle').textContent = 'Update Rate Card';
        serviceModal.classList.add('active');
    };

    serviceForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData();
        formData.append('name', document.getElementById('s_name').value);
        formData.append('price', document.getElementById('s_price').value);
        formData.append('category_id', document.getElementById('s_category').value);
        formData.append('description', document.getElementById('s_desc').value);
        formData.append('features', document.getElementById('s_features').value);
        formData.append('status', document.getElementById('s_status').value);
        formData.append('is_featured', document.getElementById('s_featured').checked ? 1 : 0);
        
        const id = document.getElementById('s_id').value;
        if (id) {
            formData.append('id', id);
            formData.append('action', 'update');
        } else {
            formData.append('action', 'create');
        }

        try {
            const response = await fetch('api/products.php', { method: 'POST', body: formData });
            const data = await response.json();
            if (data.success) {
                closeServiceModal();
                loadServices();
                alert(data.message);
            }
        } catch (e) { alert('Failed to save service.'); }
    });

    window.deleteService = async (id) => {
        if (confirm('Are you sure you want to delete this service rate card? This will permanently remove it from the catalog.')) {
            try {
                const response = await fetch(`api/products.php?id=${id}&action=delete`, { method: 'DELETE' });
                const data = await response.json();
                if (data.success) {
                    loadServices();
                } else {
                    alert(data.message || 'Deletion failed.');
                }
            } catch (e) {
                alert('Connection error. Could not delete service.');
            }
        }
    };

    loadServices();
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

// Helper to parse dates with ordinal suffixes (e.g., "February 10th 2026")
function helperParseInvDate(dateStr) {
    if (!dateStr) return new Date();
    // Remove st, nd, rd, th (case insensitive) followed by a space or end of string
    const cleaned = dateStr.replace(/(\d+)(st|nd|rd|th)\b/gi, '$1');
    return new Date(cleaned);
}

function initDashboard() {
    initDashboardStats();
    updateActivityTable();
    initDashboardCharts();
}

async function initDashboardStats() {
    try {
        const response = await fetch('api/invoices.php');
        const data = await response.json();
        
        if (!data.success || !data.invoices || data.invoices.length === 0) {
            console.log('No dashboard data available');
            return;
        }

        const invoices = data.invoices;
        const now = new Date();
        const currentMonth = now.getMonth();
        const currentYear = now.getFullYear();

        let monthlyTotal = 0;
        let pendingTotal = 0;
        let yearlyTotal = 0;

        invoices.forEach(inv => {
            const invDate = new Date(inv.created_at);
            const invMonth = invDate.getMonth();
            const invYear = invDate.getFullYear();

            const amount = parseFloat(inv.amount || 0);
            
            // Yearly Sales
            if (invYear === currentYear) {
                yearlyTotal += amount;
                // Monthly Sales
                if (invMonth === currentMonth) {
                    monthlyTotal += amount;
                }
            }
            
            // Pending balances (Unpaid invoices)
            if (inv.status.toLowerCase() !== 'paid') {
                pendingTotal += amount;
            }
        });

        // Update Dashboard UI
        if (document.getElementById('stat_monthly_sales')) {
            document.getElementById('stat_monthly_sales').textContent = `KES ${monthlyTotal.toLocaleString()}`;
            document.getElementById('stat_pending_balances').textContent = `KES ${pendingTotal.toLocaleString()}`;
            document.getElementById('stat_yearly_sales').textContent = `KES ${yearlyTotal.toLocaleString()}`;
        }
    } catch (error) {
        console.error('Stats Error:', error);
    }
}

async function updateActivityTable() {
    const activityBody = document.getElementById('dashboard_activity');
    if (!activityBody) return;

    try {
        const response = await fetch('api/invoices.php');
        const data = await response.json();
        
        if (!data.success || !data.invoices || data.invoices.length === 0) {
            activityBody.innerHTML = '<tr><td colspan="4" style="text-align: center; padding: 20px;">No recent transactions</td></tr>';
            return;
        }

        const recent = data.invoices.slice(0, 8); // Show up to 8
        activityBody.innerHTML = recent.map(inv => {
            const clientName = inv.user_id ? inv.reg_client_name : inv.guest_name;
            const statusClass = inv.status.toLowerCase() === 'paid' ? 'badge-paid' : 'badge-pending';
            return `
                <tr>
                    <td><span class="text-low" style="font-size:0.8rem;">#</span>${inv.reference}</td>
                    <td><div style="font-weight:600;">${clientName}</div><div style="font-size:0.7rem; color:var(--text-low);">${inv.user_id ? 'Registered' : 'Guest'}</div></td>
                    <td><strong>KES ${parseFloat(inv.amount).toLocaleString()}</strong></td>
                    <td><span class="badge ${statusClass}">${inv.status.toUpperCase()}</span></td>
                </tr>
            `;
        }).join('');
    } catch (error) {
        console.error('Activity Table Error:', error);
    }
}

async function initDashboardCharts() {
    const revenueCtx = document.getElementById('revenueChart');
    const statusCtx = document.getElementById('orderStatusChart');
    if (!revenueCtx || !statusCtx) return;

    try {
        const [invRes, orderRes] = await Promise.all([
            fetch('api/invoices.php'),
            fetch('api/orders.php')
        ]);
        
        const invData = await invRes.json();
        const orderData = await orderRes.json();

        // Process Revenue Data (Last 6 Months)
        const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        const now = new Date();
        const chartLabels = [];
        const chartData = [];
        
        for (let i = 5; i >= 0; i--) {
            const d = new Date(now.getFullYear(), now.getMonth() - i, 1);
            chartLabels.push(months[d.getMonth()]);
            
            const monthTotal = invData.invoices
                .filter(inv => {
                    const invDate = new Date(inv.created_at);
                    return invDate.getMonth() === d.getMonth() && invDate.getFullYear() === d.getFullYear();
                })
                .reduce((sum, inv) => sum + parseFloat(inv.amount), 0);
            
            chartData.push(monthTotal);
        }

        // Initialize Revenue Chart
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Revenue',
                    data: chartData,
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
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
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(255,255,255,0.05)' },
                        ticks: { color: '#94a3b8', font: { size: 10 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#94a3b8', font: { size: 10 } }
                    }
                }
            }
        });

        // Process Order Status Data
        const statuses = ['Pending', 'Processing', 'Ready', 'Delivered'];
        const statusCounts = statuses.map(s => 
            orderData.orders.filter(o => o.status === s).length
        );

        // Initialize Status Chart
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: statuses,
                datasets: [{
                    data: statusCounts,
                    backgroundColor: [
                        'rgba(245, 158, 11, 0.7)',
                        'rgba(99, 102, 241, 0.7)',
                        'rgba(16, 185, 129, 0.7)',
                        'rgba(148, 163, 184, 0.7)'
                    ],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#94a3b8',
                            usePointStyle: true,
                            padding: 20,
                            font: { size: 12 }
                        }
                    }
                }
            }
        });

    } catch (error) {
        console.error('Chart Init Error:', error);
    }
}

window.generateStatement = (type) => {
    const stored = localStorage.getItem('admin_invoices');
    const invoices = stored ? JSON.parse(stored) : [];
    
    // Ensure html2pdf is loaded
    if (typeof html2pdf === 'undefined') {
        alert('PDF library is loading. Please wait...');
        return;
    }

    const year = parseInt(document.getElementById('statement_year').value);
    const month = type === 'month' ? parseInt(document.getElementById('statement_month').value) : null;
    
    const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    const periodName = type === 'month' ? `${monthNames[month]} ${year}` : `Year ${year}`;

    // Filter invoices
    const filtered = invoices.filter(inv => {
        const d = helperParseInvDate(inv.date);
        if (type === 'month') {
            return d.getFullYear() === year && d.getMonth() === month;
        } else {
            return d.getFullYear() === year;
        }
    });

    if (filtered.length === 0) {
        alert(`No transactions found for ${periodName}`);
        return;
    }

    // Populate statement template
    document.getElementById('st_title').textContent = `${type.toUpperCase()} FINANCIAL STATEMENT`;
    document.getElementById('st_period').textContent = periodName;
    document.getElementById('st_gen_date').textContent = new Date().toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });

    const itemsBody = document.getElementById('st_items_body');
    itemsBody.innerHTML = '';

    let totalSales = 0;
    let totalPaid = 0;

    filtered.forEach(inv => {
        const amount = parseFloat(inv.amount || 0);
        const paid = parseFloat(inv.paidAmount || (inv.status === 'Paid' ? amount : 0));
        
        totalSales += amount;
        totalPaid += paid;

        itemsBody.innerHTML += `
            <tr>
                <td>${new Date(inv.date).toLocaleDateString()}</td>
                <td>#${inv.id}</td>
                <td>${inv.client} - ${inv.items ? inv.items[0].desc : (inv.item || 'Service')}</td>
                <td>${inv.status}</td>
                <td style="text-align: right;">${inv.amount.toLocaleString()}</td>
            </tr>
        `;
    });

    const pendingBalance = totalSales - totalPaid;

    document.getElementById('st_total_sales').textContent = `KES ${totalSales.toLocaleString()}.00`;
    document.getElementById('st_total_paid').textContent = `KES ${totalPaid.toLocaleString()}.00`;
    document.getElementById('st_pending_balance').textContent = `KES ${pendingBalance.toLocaleString()}.00`;

    // Generate PDF
    const element = document.getElementById('statementTemplate');
    const opt = {
        margin:       0,
        filename:     `Shanfix_Statement_${periodName.replace(' ', '_')}.pdf`,
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2, useCORS: true },
        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };

    const container = document.getElementById('statementTemplateContainer');
    container.style.display = 'block';

    html2pdf().set(opt).from(element).save().then(() => {
        container.style.display = 'none';
        alert(`${periodName} statement generated successfully.`);
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
        tableBody.innerHTML = clients.map(c => `
            <tr>
                <td>
                    <div class="flex-align-center gap-10">
                        <div class="admin-avatar-sm">${c.full_name.charAt(0)}</div>
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
                <td><span class="status-badge status-${c.status.toLowerCase()}">${c.status}</span></td>
                <td>${new Date(c.created_at).toLocaleDateString()}</td>
                <td style="text-align: right;">
                    <div class="flex-end-gap-sm">
                        <button class="icon-btn" onclick="editClient(${JSON.stringify(c).replace(/"/g, '&quot;')})" title="Edit Profile">
                            <i class="fas fa-user-edit"></i>
                        </button>
                        <button class="icon-btn" onclick="resetClientPassword(${c.id}, '${c.full_name}')" title="Reset Password">
                            <i class="fas fa-shield-alt"></i>
                        </button>
                        <button class="icon-btn" style="color: #ef4444;" onclick="deleteClient(${c.id}, '${c.full_name}')" title="Delete Client">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

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
                
                // Prompt to generate PDF if it was a new registration
                if (action === 'create') {
                    if (confirm(data.message + "\n\nWould you like to generate the Onboarding PDF for this client?")) {
                        generateOnboardingPDF({
                            full_name: payload.full_name,
                            email: payload.email,
                            password: payload.password || 'Client@123'
                        });
                    }
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
                            <button class="icon-btn" onclick="previewInvoice(${JSON.stringify(i).replace(/"/g, '&quot;')})"><i class="fas fa-eye"></i></button>
                            <button class="icon-btn" style="color: #22c55e;" onclick="downloadInvoice(${JSON.stringify(i).replace(/"/g, '&quot;')})"><i class="fas fa-download"></i></button>
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
        
        const items = [];
        document.querySelectorAll('#invoiceItemsBody tr').forEach(row => {
            items.push({
                desc: row.querySelector('.item-desc').value,
                qty: row.querySelector('.item-qty').value,
                price: row.querySelector('.item-price').value
            });
        });

        if (items.length === 0) return alert('Please add at least one item.');

        const totals = calculateTotals();
        const payload = {
            action: 'create',
            user_id: currentClientType === 'registered' ? document.getElementById('inv_client_id').value : null,
            guest_name: currentClientType === 'guest' ? document.getElementById('inv_guest_name').value : null,
            guest_email: currentClientType === 'guest' ? document.getElementById('inv_guest_email').value : null,
            guest_phone: currentClientType === 'guest' ? document.getElementById('inv_guest_phone').value : null,
            items: items,
            subtotal: totals.subtotal,
            tax_amount: totals.tax,
            total_amount: totals.grandTotal,
            terms: document.getElementById('inv_terms').value,
            due_date: document.getElementById('inv_due_date').value
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
            }
        } catch (e) { alert('Failed to generate invoice.'); }
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

// ADVERTS MANAGEMENT
function initAdvertsPage() {
    console.log('Adverts Page Initialized');
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
