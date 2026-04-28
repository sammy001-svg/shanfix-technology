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
        initClientAuth();
    }

    // Check for auth (simple mock)
    if (window.location.pathname.includes('/admin/') && !window.location.pathname.includes('login.php')) {
        checkAuth();
    }

    // Sidebar active state
    updateNavActive();

    // Specific page init
    if (document.getElementById('productForm')) initProductPage();
    if (document.getElementById('invoiceForm')) initInvoicePage();
    if (document.getElementById('stat_monthly_sales')) initDashboard();
    if (document.getElementById('receiptsTableBody')) initReceiptPage();
    if (document.getElementById('adminTicketsBody')) initTicketsPage();
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
                tbody.innerHTML = '<tr><td colspan="6" style="text-align:center">No support tickets found in database.</td></tr>';
                return;
            }

            data.tickets.forEach(ticket => {
                const statusClass = ticket.status.toLowerCase() === 'open' ? 'badge-pending' : 'badge-paid';
                tbody.innerHTML += `
                    <tr>
                        <td><strong>${ticket.ticket_ref}</strong></td>
                        <td>${ticket.clientEmail}</td>
                        <td>${ticket.subject}</td>
                        <td>${ticket.priority}</td>
                        <td><span class="badge ${statusClass}">${ticket.status}</span></td>
                        <td>
                            <button class="admin-btn-sm admin-btn-primary" onclick="viewTicketDetails('${ticket.ticket_ref}')">View & Reply</button>
                            ${ticket.status.toLowerCase() === 'open' ? `<button class="admin-btn-sm admin-btn-secondary" onclick="closeTicket('${ticket.id}')">Close</button>` : ''}
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
            const response = await fetch(`../client/api/ticket_details.php?ref=${ref}`);
            const data = await response.json();

            if (data.success) {
                currentTicketId = data.ticket.id;
                document.getElementById('modalTicketTitle').textContent = `Ticket: ${data.ticket.ticket_ref} - ${data.ticket.subject}`;
                
                const thread = document.getElementById('ticketThread');
                thread.innerHTML = `
                    <div class="ticket-bubble bubble-client">
                        <span class="bubble-meta">${data.ticket.client_name} (${data.ticket.created_at})</span>
                        ${data.ticket.message}
                    </div>
                `;

                data.replies.forEach(reply => {
                    const type = reply.is_admin_reply ? 'admin' : 'client';
                    thread.innerHTML += `
                        <div class="ticket-bubble bubble-${type}">
                            <span class="bubble-meta">${reply.author_name} (${reply.created_at})</span>
                            ${reply.message}
                        </div>
                    `;
                });

                document.getElementById('ticketModal').style.display = 'block';
                thread.scrollTop = thread.scrollHeight;
            }
        } catch (error) {
            alert('Could not load ticket details.');
        }
    };

    window.submitAdminReply = async function() {
        const message = document.getElementById('adminReplyMessage').value.trim();
        if (!message) return;

        try {
            const response = await fetch('../client/api/ticket_reply.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ ticket_id: currentTicketId, message: message })
            });
            const data = await response.json();

            if (data.success) {
                document.getElementById('adminReplyMessage').value = '';
                const ticket_ref = document.getElementById('modalTicketTitle').textContent.split(': ')[1].split(' - ')[0];
                viewTicketDetails(ticket_ref); // Refresh thread
            }
        } catch (error) {
            alert('Failed to send reply.');
        }
    };

    window.closeTicketModal = function() {
        document.getElementById('ticketModal').style.display = 'none';
    };

    window.closeTicket = async function(id) {
        if(confirm('Mark this ticket as closed?')) {
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



function handleLogin(form) {
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const email = document.getElementById('email').value;
        const pass = document.getElementById('password').value;

        // Mock login
        if (email === 'admin@shanfix.com' && pass === 'admin123') {
            sessionStorage.setItem('isAdmin', 'true');
            window.location.href = 'index.php';
        } else {
            alert('Invalid credentials!');
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

// PRODUCT MANAGEMENT
function initProductPage() {
    const productForm = document.getElementById('productForm');
    const categoryForm = document.getElementById('categoryForm');
    
    // Load data from localStorage or provide premium defaults
    let categories = JSON.parse(localStorage.getItem('admin_categories')) || ['T-shirt Branding', 'Corporate Stationery', 'Signage & Banners', 'Promotional Items'];
    let products = JSON.parse(localStorage.getItem('admin_products')) || [
        {
            id: 1,
            name: 'Premium Cotton Branded T-Shirt',
            price: '1500',
            category: 'T-shirt Branding',
            image: 'assets/printing/tshirt-branding.jpg',
            description: 'High-quality 100% cotton t-shirts with vibrant, durable screen printing or embroidery.'
        },
        {
            id: 2,
            name: 'Executive Business Cards',
            price: '2500',
            category: 'Corporate Stationery',
            image: 'assets/printing/business-cards.jpg',
            description: 'Premium 400gsm matte laminated business cards with spot UV finish. Pack of 100.'
        },
        {
            id: 3,
            name: 'Retractable Roll-up Banner',
            price: '8500',
            category: 'Signage & Banners',
            image: 'assets/printing/rollup-banner.jpg',
            description: 'Standard 85cm x 200cm roll-up banner with high-resolution PVC print and carrying bag.'
        },
        {
            id: 4,
            name: 'Branded Executive Notebook',
            price: '1800',
            category: 'Promotional Items',
            image: 'assets/printing/notebook.jpg',
            description: 'A5 leather-bound notebook with foil-stamped company logo and ribbon marker.'
        }
    ];

    // Ensure defaults are saved if they didn't exist
    if (!localStorage.getItem('admin_categories')) localStorage.setItem('admin_categories', JSON.stringify(categories));
    if (!localStorage.getItem('admin_products')) localStorage.setItem('admin_products', JSON.stringify(products));
    
    // Initial renders
    renderProducts(products);
    renderCategories(categories);
    populateCategoryDropdown(categories);

    // Handle Category Submission
    if (categoryForm) {
        categoryForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const catName = document.getElementById('cat_name').value.trim();
            if (catName && !categories.includes(catName)) {
                categories.push(catName);
                localStorage.setItem('admin_categories', JSON.stringify(categories));
                renderCategories(categories);
                populateCategoryDropdown(categories);
                categoryForm.reset();
            }
        });
    }

    // Handle Product Submission
    if (productForm) {
        productForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const imageFile = document.getElementById('p_image').files[0];
            let imageData = 'assets/service-placeholder.jpg';

            if (imageFile) {
                imageData = await new Promise((resolve) => {
                    const reader = new FileReader();
                    reader.onload = (e) => resolve(e.target.result);
                    reader.readAsDataURL(imageFile);
                });
            }

            const newProduct = {
                id: Date.now(),
                name: document.getElementById('p_name').value,
                price: document.getElementById('p_price').value,
                category: document.getElementById('p_category').value,
                image: imageData,
                description: document.getElementById('p_desc').value
            };

            products.push(newProduct);
            localStorage.setItem('admin_products', JSON.stringify(products));
            renderProducts(products);
            productForm.reset();
        });
    }
}

function renderCategories(categories) {
    const tableBody = document.getElementById('categoryTableBody');
    if (!tableBody) return;
    
    tableBody.innerHTML = categories.map(cat => `
        <tr>
            <td><strong>${cat}</strong></td>
            <td>
                <button class="admin-btn admin-btn-secondary" onclick="deleteCategory('${cat}')">Delete</button>
            </td>
        </tr>
    `).join('');
}

function populateCategoryDropdown(categories) {
    const dropdown = document.getElementById('p_category');
    if (!dropdown) return;
    dropdown.innerHTML = categories.map(cat => `<option value="${cat}">${cat}</option>`).join('');
}

function renderProducts(products) {
    const tableBody = document.getElementById('productTableBody');
    if (!tableBody) return;
    
    tableBody.innerHTML = products.map(p => `
        <tr>
            <td>${p.id}</td>
            <td><strong>${p.name}</strong></td>
            <td>${p.category}</td>
            <td>KES ${p.price}</td>
            <td>
                <button class="admin-btn admin-btn-secondary" onclick="deleteProduct(${p.id})">Delete</button>
            </td>
        </tr>
    `).join('');
}

window.deleteCategory = (catName) => {
    if (confirm(`Are you sure you want to delete category "${catName}"?`)) {
        let categories = JSON.parse(localStorage.getItem('admin_categories')) || [];
        categories = categories.filter(c => c !== catName);
        localStorage.setItem('admin_categories', JSON.stringify(categories));
        
        // Refresh UI
        renderCategories(categories);
        populateCategoryDropdown(categories);
    }
};

window.deleteProduct = (id) => {
    let products = JSON.parse(localStorage.getItem('admin_products')) || [];
    products = products.filter(p => p.id !== id);
    localStorage.setItem('admin_products', JSON.stringify(products));
    renderProducts(products);
};

// INVOICE GENERATION
function initInvoicePage() {
    const form = document.getElementById('invoiceForm');
    if (!form) return;

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

    // Load existing invoices from localStorage if any
    let invoices = JSON.parse(localStorage.getItem('admin_invoices')) || [
        {
            id: 'SF-1001',
            client: 'John Doe',
            phone: '+254 711 222 333',
            email: 'john@example.com',
            items: [{desc: 'Business Cards', qty: 100, price: 25}],
            amount: 2500,
            date: 'February 10th 2026',
            delivery: '2 Days',
            status: 'Paid'
        },
        {
            id: 'SF-1002',
            client: 'Apex Corp',
            phone: '+254 722 333 444',
            email: 'info@apex.com',
            items: [{desc: 'Company Profiles', qty: 1, price: 15000}],
            amount: 15000,
            date: 'February 12th 2026',
            delivery: '5 Days',
            status: 'Pending'
        }
    ];
    
    renderInvoices(invoices);

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        
        // Collect items
        const itemRows = document.querySelectorAll('.invoice-item-row, .invoice-item-row-dynamic');
        const items = Array.from(itemRows).map(row => ({
            qty: parseFloat(row.querySelector('.item-qty').value),
            desc: row.querySelector('.item-desc').value,
            price: parseFloat(row.querySelector('.item-price').value)
        }));

        const totalAmount = items.reduce((sum, item) => sum + (item.qty * item.price), 0);
        const invId = `SF-${1000 + invoices.length + 1}`;

        const newInvoice = {
            id: invId,
            client: document.getElementById('client_name').value,
            phone: document.getElementById('client_phone').value,
            email: document.getElementById('client_email').value,
            items: items,
            amount: totalAmount,
            date: new Date().toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }),
            delivery: document.getElementById('delivery_time').value,
            status: 'Pending'
        };

        invoices.push(newInvoice);
        localStorage.setItem('admin_invoices', JSON.stringify(invoices));
        renderInvoices(invoices);
        form.reset();
        
        // Auto-Preview
        window.viewInvoice(invId);
    });

    window.downloadBtnInModal = document.getElementById('downloadBtnInModal');
}

function renderInvoices(invoices) {
    const tableBody = document.getElementById('invoiceTableBody');
    if (!tableBody) return;
    
    tableBody.innerHTML = invoices.map(inv => `
        <tr>
            <td><a href="#" class="inv-id-link" onclick="openPaymentModal('${inv.id}'); return false;">#${inv.id}</a></td>
            <td><strong>${inv.client}</strong></td>
            <td>${inv.items && inv.items.length > 0 ? inv.items[0].desc : (inv.item || 'Service')}</td>
            <td>KES ${inv.amount.toLocaleString()}</td>
            <td><span class="status-badge status-${inv.status.toLowerCase().replace(' ', '-')}">${inv.status}</span></td>
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
}

function initDashboardStats() {
    const stored = localStorage.getItem('admin_invoices');
    const invoices = stored ? JSON.parse(stored) : [];
    
    if (invoices.length === 0) return;

    const now = new Date();
    const currentMonth = now.getMonth();
    const currentYear = now.getFullYear();

    let monthlyTotal = 0;
    let pendingTotal = 0;
    let yearlyTotal = 0;

    invoices.forEach(inv => {
        const invDate = helperParseInvDate(inv.date);
        const invMonth = invDate.getMonth();
        const invYear = invDate.getFullYear();

        const amount = parseFloat(inv.amount || 0);
        const paid = parseFloat(inv.paidAmount || (inv.status === 'Paid' ? amount : 0));
        const balance = amount - paid;

        // Yearly Sales (Total value of "recorded" sales)
        if (invYear === currentYear) {
            if (inv.status === 'Paid' || inv.status === 'Partially Paid') {
                yearlyTotal += amount;
            }

            // Monthly Sales (Total value of "recorded" sales)
            if (invMonth === currentMonth) {
                if (inv.status === 'Paid' || inv.status === 'Partially Paid') {
                    monthlyTotal += amount;
                }
            }
        }
        
        // Pending balances (Sum of ALL outstanding amounts)
        if (balance > 0) {
            pendingTotal += balance;
        }
    });

    // Update Dashboard UI
    document.getElementById('stat_monthly_sales').textContent = `KES ${monthlyTotal.toLocaleString()}`;
    document.getElementById('stat_pending_balances').textContent = `KES ${pendingTotal.toLocaleString()}`;
    document.getElementById('stat_yearly_sales').textContent = `KES ${yearlyTotal.toLocaleString()}`;
}

function updateActivityTable() {
    const stored = localStorage.getItem('admin_invoices');
    const invoices = (stored ? JSON.parse(stored) : []).slice(-5).reverse(); // Last 5
    const activityBody = document.getElementById('dashboard_activity');
    if (!activityBody) return;

    if (invoices.length === 0) {
        activityBody.innerHTML = '<tr><td colspan="3" style="text-align: center;">No recent activity</td></tr>';
        return;
    }

    activityBody.innerHTML = invoices.map(inv => `
        <tr>
            <td>Invoice #${inv.id} generated for ${inv.client}</td>
            <td>${inv.date}</td>
            <td><span class="text-${inv.status === 'Paid' ? 'primary' : 'secondary'}">${inv.status}</span></td>
        </tr>
    `).join('');
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

function initReceiptPage() {
    const stored = localStorage.getItem('admin_invoices');
    const invoices = (stored ? JSON.parse(stored) : []);
    const tableBody = document.getElementById('receiptsTableBody');
    if (!tableBody) return;

    // Filter for invoices with ANY payment made
    const paidInvoices = invoices.filter(inv => (inv.paidAmount || 0) > 0);

    if (paidInvoices.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="6" class="text-center">No payments recorded yet</td></tr>';
        return;
    }

    tableBody.innerHTML = paidInvoices.map(inv => {
        const balance = inv.amount - (inv.paidAmount || 0);
        return `
            <tr>
                <td>#${inv.id}</td>
                <td>${inv.client}</td>
                <td>KES ${inv.amount.toLocaleString()}</td>
                <td><strong class="st-summary-paid">KES ${(inv.paidAmount || 0).toLocaleString()}</strong></td>
                <td><span class="${balance > 0 ? 'st-summary-pending' : 'st-summary-paid'}">KES ${balance.toLocaleString()}</span></td>
                <td>${inv.date}</td>
                <td>
                    <button class="admin-btn admin-btn-secondary" onclick="viewReceipt('${inv.id}')">
                        <i class="fas fa-receipt"></i> View Receipt
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

window.viewReceipt = (id) => {
    const stored = localStorage.getItem('admin_invoices');
    const invoices = stored ? JSON.parse(stored) : [];
    const inv = invoices.find(i => i.id === id);
    if (!inv) return;

    populateReceiptTemplate(inv);
    
    document.getElementById('receiptModal').style.display = 'block';
    
    // Set up download button
    document.getElementById('downloadBtnInReceiptModal').onclick = () => {
        generateReceiptPDF(inv);
    };
};

window.closeReceiptModal = () => {
    document.getElementById('receiptModal').style.display = 'none';
};

function populateReceiptTemplate(inv) {
    document.getElementById('rec_id').textContent = `SF-REC-${inv.id.split('-')[1]}`;
    document.getElementById('rec_inv_ref').textContent = inv.id;
    document.getElementById('rec_date').textContent = new Date().toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
    document.getElementById('rec_client_name').textContent = inv.client;
    document.getElementById('rec_total_amount').textContent = `KES ${(inv.paidAmount || 0).toLocaleString()}.00`;
    
    const itemsBody = document.getElementById('rec_items_body');
    itemsBody.innerHTML = (inv.items || []).map(item => `
        <tr>
            <td>${item.qty}</td>
            <td>${item.desc}</td>
            <td>KES ${(item.qty * item.price).toLocaleString()}</td>
        </tr>
    `).join('');

    // Remove existing summary if any and add a breakdown
    const existingBreakdown = document.getElementById('rec_payment_breakdown');
    if (existingBreakdown) existingBreakdown.remove();

    const breakdownHtml = `
        <div id="rec_payment_breakdown" class="mt-20 border-top pt-10" style="font-size: 0.9rem;">
            <div class="justify-between-center mb-5">
                <span>Original Invoice Total:</span>
                <span>KES ${inv.amount.toLocaleString()}.00</span>
            </div>
            <div class="justify-between-center mb-5">
                <span>Amount Paid to Date:</span>
                <strong class="st-summary-paid">KES ${(inv.paidAmount || 0).toLocaleString()}.00</strong>
            </div>
            <div class="justify-between-center">
                <span>Remaining Balance:</span>
                <strong class="st-summary-pending">KES ${(inv.amount - (inv.paidAmount || 0)).toLocaleString()}.00</strong>
            </div>
        </div>
    `;
    
    const summaryBox = document.querySelector('.receipt-summary-box').parentElement;
    summaryBox.insertAdjacentHTML('beforebegin', breakdownHtml);
}

function generateReceiptPDF(inv) {
    const element = document.getElementById('receiptTemplate');
    const filename = `Receipt_${inv.id}_${inv.client.replace(' ', '_')}.pdf`;
    
    const opt = {
        margin:       0,
        filename:     filename,
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2, useCORS: true },
        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };

    const container = document.getElementById('receiptTemplateContainer');
    container.style.display = 'block';

    html2pdf().set(opt).from(element).save().then(() => {
        container.style.display = 'none';
    }).catch(err => {
        console.error('Receipt PDF Error:', err);
        container.style.display = 'none';
    });
}

// ADVERTS MANAGEMENT
function initAdvertsPage() {
    console.log('Adverts Page Initialized');
    // Placeholder for future dynamic advert management logic
}
