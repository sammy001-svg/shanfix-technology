/**
 * SHANFIX CLIENT PORTAL - MODERN LOGIC
 * Re-engineered for the premium dashboard experience with MySQL backend.
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Authentication Layer
    const isClient = sessionStorage.getItem('isClient');
    if (isClient !== 'true') {
        window.location.href = 'login.php';
        return;
    }

    const email = sessionStorage.getItem('client_email');
    const name = sessionStorage.getItem('client_name');
    
    // 2. UI Initialization
    if(document.getElementById('headerClientName')) document.getElementById('headerClientName').textContent = name;
    if(document.getElementById('headerClientEmail')) document.getElementById('headerClientEmail').textContent = email;
    if(document.getElementById('welcomeText')) document.getElementById('welcomeText').textContent = `Welcome, ${name.split(' ')[0]}`;
    
    // Fill settings
    if(document.getElementById('settingName')) document.getElementById('settingName').value = name;
    if(document.getElementById('settingEmail')) document.getElementById('settingEmail').value = email;

    // 3. Navigation Engine
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            const tabId = item.getAttribute('data-tab');
            switchTab(tabId);
        });
    });

    // 4. Global Action Handlers
    const logoutBtn = document.getElementById('logoutBtn');
    if(logoutBtn) {
        logoutBtn.addEventListener('click', () => {
            if(confirm('Ready to end your session?')) {
                sessionStorage.clear();
                window.location.href = 'login.php';
            }
        });
    }

    // Load profile when settings tab is opened
    document.querySelectorAll('.nav-item').forEach(item => {
        if (item.getAttribute('data-tab') === 'settings') {
            item.addEventListener('click', loadProfile, { once: false });
        }
    });

    // 5. Data Hydration
    loadInvoices();
    loadTickets();
    loadServices();
    loadNotifications();

    // Notification bell click — jump to support tab
    const bellBtn = document.getElementById('notificationBell');
    if (bellBtn) {
        bellBtn.addEventListener('click', () => {
            switchTab('support');
            loadNotifications();
        });
    }

    // Service search bar
    const serviceSearch = document.getElementById('serviceSearchInput');
    if (serviceSearch) {
        serviceSearch.addEventListener('input', () => {
            const q = serviceSearch.value.toLowerCase();
            document.querySelectorAll('#servicesGrid .stat-card').forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(q) ? '' : 'none';
            });
        });
    }

    // 6. Profile Update Handler
    const profileForm = document.getElementById('profileForm');
    if (profileForm) {
        profileForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('profileSaveBtn');
            const original = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            btn.disabled = true;

            try {
                const res = await fetch('api/profile.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'update_profile',
                        full_name: document.getElementById('settingName').value,
                        phone: document.getElementById('settingPhone').value,
                        company: document.getElementById('settingCompany').value
                    })
                });
                const data = await res.json();
                if (data.success) {
                    sessionStorage.setItem('client_name', data.full_name);
                    if (document.getElementById('headerClientName')) {
                        document.getElementById('headerClientName').textContent = data.full_name;
                    }
                    alert(data.message);
                } else {
                    alert(data.message || 'Failed to update profile.');
                }
            } catch (err) {
                alert('Connection error. Please try again.');
            } finally {
                btn.innerHTML = original;
                btn.disabled = false;
            }
        });
    }

    // 7. Password Change Handler
    const securityForm = document.getElementById('securityForm');
    if (securityForm) {
        securityForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const newPass = document.getElementById('newPassword').value;
            const confirmPass = document.getElementById('confirmPassword').value;
            if (newPass !== confirmPass) {
                alert('New passwords do not match.');
                return;
            }

            const btn = document.getElementById('securitySaveBtn');
            const original = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
            btn.disabled = true;

            try {
                const res = await fetch('api/profile.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'change_password',
                        current_password: document.getElementById('currentPassword').value,
                        new_password: newPass,
                        confirm_password: confirmPass
                    })
                });
                const data = await res.json();
                if (data.success) {
                    alert(data.message);
                    securityForm.reset();
                } else {
                    alert(data.message || 'Failed to change password.');
                }
            } catch (err) {
                alert('Connection error. Please try again.');
            } finally {
                btn.innerHTML = original;
                btn.disabled = false;
            }
        });
    }

    // 8. Form Handlers (Support Tickets)
    const ticketForm = document.getElementById('newTicketForm');
    if(ticketForm) {
        ticketForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const subject = document.getElementById('ticketSubject').value;
            const priority = document.getElementById('ticketPriority').value;
            const message = document.getElementById('ticketMessage').value;

            const submitBtn = ticketForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span>Logging ticket...</span> <i class="fas fa-spinner fa-spin"></i>';
            submitBtn.disabled = true;

            try {
                const response = await fetch('api/tickets.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ subject, priority, message })
                });

                const data = await response.json();

                if (data.success) {
                    alert('Your ticket has been logged in our database. Our team will review it shortly.');
                    ticketForm.reset();
                    loadTickets(); // Refresh list
                } else {
                    alert(data.message || 'Failed to submit ticket.');
                }
            } catch (error) {
                console.error('Ticket Error:', error);
                alert('Connection to server failed. Please try again.');
            } finally {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });
    }
});

/**
 * Tab Switching Logic
 */
function switchTab(id) {
    // Nav links
    document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
    const activeNav = document.querySelector(`.nav-item[data-tab="${id}"]`);
    if(activeNav) activeNav.classList.add('active');

    // Sections
    document.querySelectorAll('.portal-tab-content').forEach(s => {
        s.style.display = 'none';
        s.classList.remove('active');
    });

    const activeTab = document.getElementById(`tab-${id}`);
    if(activeTab) {
        activeTab.style.display = 'block';
        setTimeout(() => activeTab.classList.add('active'), 10);
    }

    // Update Header
    const titles = {
        'dashboard': 'Overview',
        'billing': 'Billing & Payments',
        'services': 'Active Services',
        'support': 'Support Center',
        'settings': 'Account Settings'
    };
    const welcome = document.getElementById('welcomeText');
    if(welcome) welcome.textContent = titles[id] || 'Dashboard';
}

/**
 * Billing Loader (Database Integration)
 */
async function loadInvoices() {
    try {
        const response = await fetch('api/invoices.php');
        const data = await response.json();

        const recentBody = document.querySelector('#recentInvoicesTable tbody');
        const allBody = document.querySelector('#allInvoicesTable tbody');
        if (!recentBody || !allBody) return;

        recentBody.innerHTML = '';
        allBody.innerHTML = '';
        let totalUnpaid = 0;

        if (!data.success || data.invoices.length === 0) {
            recentBody.innerHTML = '<tr><td colspan="5" style="text-align:center; color:var(--text-low); padding:2rem;">No transaction history found.</td></tr>';
            allBody.innerHTML = '<tr><td colspan="6" style="text-align:center; color:var(--text-low); padding:2rem;">You currently have no invoices in the database.</td></tr>';
        } else {
            data.invoices.forEach((inv, idx) => {
                const isPaid = inv.status.toLowerCase() === 'paid';
                const badge = isPaid ? 'badge-paid' : 'badge-pending';
                if (!isPaid) totalUnpaid += parseFloat(inv.total);

                const recentRow = `
                    <tr>
                        <td style="font-weight:700; color:var(--p)">${inv.id}</td>
                        <td>${inv.date}</td>
                        <td style="font-weight:700;">Ksh ${parseFloat(inv.total).toLocaleString()}</td>
                        <td><span class="badge ${badge}">${inv.status}</span></td>
                        <td style="text-align:right;">
                            <button class="portal-btn-primary" style="padding:0.5rem 1rem; width:auto; font-size:0.75rem;" onclick="downloadInvoicePDF('${inv.id}')">
                                <i class="fas fa-download"></i> PDF
                            </button>
                        </td>
                    </tr>
                `;
                const allRow = `
                    <tr>
                        <td style="font-weight:700; color:var(--p)">${inv.id}</td>
                        <td>${inv.date}</td>
                        <td>${inv.due_date || '—'}</td>
                        <td style="font-weight:700;">Ksh ${parseFloat(inv.total).toLocaleString()}</td>
                        <td><span class="badge ${badge}">${inv.status}</span></td>
                        <td style="text-align:right;">
                            <div style="display:flex; gap:6px; justify-content:flex-end; flex-wrap:wrap;">
                                ${!isPaid ? `
                                <button class="portal-btn-primary" style="padding:0.5rem 1rem; width:auto; font-size:0.75rem; background:linear-gradient(135deg,#16a34a,#22c55e);" onclick="openMpesaModal('${inv.id}', ${inv.total})">
                                    <i class="fas fa-mobile-alt"></i> Pay
                                </button>` : ''}
                                <button class="portal-btn-primary" style="padding:0.5rem 1rem; width:auto; font-size:0.75rem;" onclick="downloadInvoicePDF('${inv.id}')">
                                    <i class="fas fa-download"></i> PDF
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                allBody.innerHTML += allRow;
                if (idx < 4) recentBody.innerHTML += recentRow;
            });
        }

        const balDisplay = document.getElementById('dashPendingBalance');
        if (balDisplay) balDisplay.textContent = `Ksh ${totalUnpaid.toLocaleString()}`;
    } catch (error) {
        console.error('Invoice Load Error:', error);
    }
}

window.downloadInvoicePDF = async function(ref) {
    if (typeof html2pdf === 'undefined') {
        alert('PDF library is still loading. Please try again.');
        return;
    }

    try {
        const res = await fetch(`api/invoices.php?ref=${ref}`);
        const data = await res.json();
        if (!data.success) { alert('Could not load invoice details.'); return; }

        const inv = data.invoice;
        const isPaid = inv.status.toLowerCase() === 'paid';

        document.getElementById('pdf_ref').textContent = `#${inv.reference}`;
        document.getElementById('pdf_status_badge').textContent = inv.status.toUpperCase();
        document.getElementById('pdf_status_badge').style.background = isPaid ? '#dcfce7' : '#fef3c7';
        document.getElementById('pdf_status_badge').style.color = isPaid ? '#166534' : '#92400e';
        document.getElementById('pdf_client_name').textContent = inv.client_name || '';
        document.getElementById('pdf_client_email').textContent = inv.client_email || '';
        document.getElementById('pdf_client_phone').textContent = inv.client_phone || '';
        document.getElementById('pdf_client_company').textContent = inv.client_company || '';
        document.getElementById('pdf_issue_date').textContent = inv.issue_date || '';
        document.getElementById('pdf_due_date').textContent = inv.due_date || '';
        document.getElementById('pdf_subtotal').textContent = `KES ${parseFloat(inv.subtotal).toLocaleString()}`;
        document.getElementById('pdf_tax').textContent = `KES ${parseFloat(inv.tax_amount).toLocaleString()}`;
        document.getElementById('pdf_total').textContent = `KES ${parseFloat(inv.amount).toLocaleString()}`;

        const termsEl = document.getElementById('pdf_terms');
        if (termsEl) termsEl.textContent = inv.terms_payment || '';

        const itemsBody = document.getElementById('pdf_items_body');
        itemsBody.innerHTML = (inv.items || []).map(item => `
            <tr>
                <td style="padding:10px 12px; border-bottom:1px solid #f1f5f9;">${item.description}</td>
                <td style="padding:10px 12px; border-bottom:1px solid #f1f5f9; text-align:center;">${parseFloat(item.quantity)}</td>
                <td style="padding:10px 12px; border-bottom:1px solid #f1f5f9; text-align:right;">KES ${parseFloat(item.unit_price).toLocaleString()}</td>
                <td style="padding:10px 12px; border-bottom:1px solid #f1f5f9; text-align:right;">KES ${parseFloat(item.total_price).toLocaleString()}</td>
            </tr>
        `).join('');

        const container = document.getElementById('invoicePdfContainer');
        container.style.display = 'block';
        container.style.position = 'static';

        html2pdf().set({
            margin: 10,
            filename: `Invoice_${inv.reference}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        }).from(document.getElementById('invoicePdfContent')).save().then(() => {
            container.style.display = 'none';
            container.style.position = 'fixed';
        });
    } catch (err) {
        console.error('Invoice PDF Error:', err);
        alert('Failed to generate PDF.');
    }
};

/**
 * Support Ticket Loader (Database Integration)
 */
async function loadTickets() {
    try {
        const response = await fetch('api/tickets.php');
        const data = await response.json();
        
        const list = document.getElementById('ticketList');
        if(!list) return;

        list.innerHTML = '';
        let openCount = 0;

        if(!data.success || data.tickets.length === 0) {
            list.innerHTML = '<div style="text-align:center; padding: 2rem; color: var(--text-low);">No active support tickets found.</div>';
        } else {
            data.tickets.forEach(t => {
                const isOpen = t.status.toLowerCase() === 'open';
                if(isOpen) openCount++;
                list.innerHTML += `
                    <div style="background:#f8fafc; border-radius: 20px; padding: 1.5rem; margin-bottom: 1rem; border-left: 5px solid var(--s);">
                        <div style="display:flex; justify-content:space-between; align-items:start;">
                            <div>
                                <h4 style="margin:0; color:var(--p);">${t.subject}</h4>
                                <span style="font-size:0.75rem; color:var(--text-low);">${t.id} • ${t.date}</span>
                            </div>
                            <span class="badge ${isOpen ? 'badge-pending' : 'badge-paid'}" style="font-size:0.6rem;">${t.status}</span>
                        </div>
                        <p style="font-size:0.85rem; color:var(--text-mid); margin:1rem 0;">${t.message}</p>
                        <div style="display:flex; gap:10px;">
                            <button class="portal-btn-primary" style="padding:0.4rem 1rem; width:auto; font-size:0.7rem; background:white; color:var(--p); border: 1px solid var(--border);" onclick="viewTicketConversation('${t.id}')">View Conversation</button>
                        </div>
                    </div>
                `;
            });
        }

        const countDisplay = document.getElementById('dashOpenTickets');
        if(countDisplay) countDisplay.textContent = openCount;
    } catch (error) {
        console.error('Ticket Load Error:', error);
    }
}

let activeTicketId = null;

window.viewTicketConversation = async function(ref) {
    try {
        const response = await fetch(`api/ticket_details.php?ref=${ref}`);
        const data = await response.json();

        if (data.success) {
            activeTicketId = data.ticket.id;
            document.getElementById('modalTicketTitle').textContent = `${data.ticket.ticket_ref} - ${data.ticket.subject}`;
            
            const thread = document.getElementById('ticketThread');
            thread.innerHTML = `
                <div class="ticket-bubble bubble-client">
                    <span class="bubble-meta">You (${data.ticket.created_at})</span>
                    ${data.ticket.message}
                </div>
            `;

            data.replies.forEach(reply => {
                const isMe = !reply.is_admin_reply;
                thread.innerHTML += `
                    <div class="ticket-bubble bubble-${isMe ? 'client' : 'admin'}">
                        <span class="bubble-meta">${isMe ? 'You' : 'Shanfix Support'} (${reply.created_at})</span>
                        ${reply.message}
                    </div>
                `;
            });

            // Handle closed state
            const isClosed = data.ticket.status.toLowerCase() === 'closed';
            document.getElementById('clientReplyArea').style.display = isClosed ? 'none' : 'block';
            document.getElementById('ticketClosedMsg').style.display = isClosed ? 'block' : 'none';

            document.getElementById('ticketModal').style.display = 'block';
            thread.scrollTop = thread.scrollHeight;
        }
    } catch (error) {
        alert('Could not load conversation.');
    }
};

window.submitClientReply = async function() {
    const msg = document.getElementById('clientReplyMessage').value.trim();
    if (!msg) return;

    try {
        const response = await fetch('api/ticket_reply.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ ticket_id: activeTicketId, message: msg })
        });
        const data = await response.json();

        if (data.success) {
            document.getElementById('clientReplyMessage').value = '';
            const ref = document.getElementById('modalTicketTitle').textContent.split(' - ')[0];
            viewTicketConversation(ref); // Refresh
        }
    } catch (error) {
        alert('Failed to send reply.');
    }
};

window.closeTicketModal = function() {
    document.getElementById('ticketModal').style.display = 'none';
};

/**
 * Services Loader (Database Integration)
 */
async function loadServices() {
    const grid = document.getElementById('servicesGrid');
    if (!grid) return;

    try {
        const res = await fetch('api/services.php');
        const data = await res.json();

        const countDisplay = document.getElementById('dashActiveServices');

        if (!data.success || data.services.length === 0) {
            if (countDisplay) countDisplay.textContent = '0';
            grid.innerHTML = `
                <div style="grid-column:1/-1; text-align:center; padding:3rem; color:var(--text-low);">
                    <i class="fas fa-box-open" style="font-size:2rem; margin-bottom:1rem; display:block;"></i>
                    No active services found. Contact support to provision a service.
                </div>`;
            return;
        }

        const activeCount = data.services.filter(s => s.status === 'active').length;
        if (countDisplay) countDisplay.textContent = activeCount;

        const statusColors = {
            active: { bg: 'rgba(22,163,74,0.1)', color: 'var(--s)' },
            pending: { bg: 'rgba(245,158,11,0.1)', color: '#d97706' },
            suspended: { bg: 'rgba(239,68,68,0.1)', color: '#ef4444' },
            terminated: { bg: 'rgba(148,163,184,0.1)', color: '#94a3b8' }
        };

        grid.innerHTML = data.services.map(s => {
            const sc = statusColors[s.status] || statusColors.pending;
            const dueDate = s.next_due_date ? new Date(s.next_due_date).toLocaleDateString('en-KE', { day:'numeric', month:'short', year:'numeric' }) : '—';
            const isOverdue = s.next_due_date && new Date(s.next_due_date) < new Date() && s.status === 'active';
            return `
                <div class="stat-card" style="padding:1.5rem; border-radius:24px; ${isOverdue ? 'border: 1px solid rgba(239,68,68,0.4);' : ''}">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                        <div class="stat-icon" style="margin:0; width:48px; height:48px;">
                            <i class="fas fa-box"></i>
                        </div>
                        <span style="font-size:0.7rem; font-weight:800; color:${sc.color}; background:${sc.bg}; padding:4px 10px; border-radius:10px; text-transform:uppercase;">
                            ${s.status}
                        </span>
                    </div>
                    <h4 style="margin:0; color:var(--p);">${s.service_name}</h4>
                    <div style="font-size:0.8rem; color:var(--text-low); margin-top:4px;">
                        ${s.category_name || 'Service'} &bull; ${s.billing_cycle || 'one-time'}
                    </div>
                    <div style="margin-top:1.5rem; display:flex; justify-content:space-between; align-items:center;">
                        <div>
                            <div style="font-size:0.7rem; color:var(--text-low);">Next due</div>
                            <span style="font-weight:700; font-size:0.9rem; color:${isOverdue ? '#ef4444' : 'var(--p)'};">
                                ${isOverdue ? '<i class="fas fa-exclamation-triangle" style="color:#ef4444;"></i> ' : ''}${dueDate}
                            </span>
                        </div>
                        ${s.price ? `<span style="font-weight:800; color:var(--p);">KES ${parseFloat(s.price).toLocaleString()}</span>` : ''}
                    </div>
                </div>
            `;
        }).join('');
    } catch (err) {
        console.error('Services Load Error:', err);
    }
}

/**
 * Profile Loader (Settings Tab)
 */
async function loadProfile() {
    try {
        const res = await fetch('api/profile.php');
        const data = await res.json();
        if (data.success) {
            const u = data.user;
            if (document.getElementById('settingName'))    document.getElementById('settingName').value    = u.full_name || '';
            if (document.getElementById('settingEmail'))   document.getElementById('settingEmail').value   = u.email    || '';
            if (document.getElementById('settingPhone'))   document.getElementById('settingPhone').value   = u.phone    || '';
            if (document.getElementById('settingCompany')) document.getElementById('settingCompany').value = u.company  || '';
            // Cache phone for M-PESA pre-fill
            if (u.phone) sessionStorage.setItem('client_phone', u.phone);
        }
    } catch (err) {
        console.error('Profile Load Error:', err);
    }
}

// ── M-PESA STK Push Payment Flow ─────────────────────────────────────────

let _mpesaTransactionId = null;
let _mpesaPollInterval  = null;

function _mpesaShowStep(n) {
    [1, 2, 3, 4].forEach(i => {
        const el = document.getElementById(`mpesaStep${i}`);
        if (el) el.style.display = (i === n) ? 'block' : 'none';
    });
}

window.openMpesaModal = function(invoiceRef, amount) {
    document.getElementById('mpesa_inv_ref').textContent    = invoiceRef;
    document.getElementById('mpesa_inv_amount').textContent = `Ksh ${parseFloat(amount).toLocaleString()}`;

    // Pre-fill phone from session cache or profile phone input
    const cached = sessionStorage.getItem('client_phone') || '';
    const profilePhone = document.getElementById('settingPhone')?.value || '';
    document.getElementById('mpesaPhone').value = cached || profilePhone;

    const errBox = document.getElementById('mpesaInitError');
    if (errBox) errBox.style.display = 'none';

    const btn = document.getElementById('mpesaSendBtn');
    if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-paper-plane"></i> Send STK Push'; }

    _mpesaShowStep(1);
    document.getElementById('mpesaModal').style.display = 'block';
};

window.closeMpesaModal = function() {
    cancelMpesaWait();
    document.getElementById('mpesaModal').style.display = 'none';
    loadInvoices(); // Refresh billing table — status may have changed
};

window.resetMpesaModal = function() {
    const errBox = document.getElementById('mpesaInitError');
    if (errBox) errBox.style.display = 'none';
    _mpesaShowStep(1);
};

window.cancelMpesaWait = function() {
    if (_mpesaPollInterval) {
        clearInterval(_mpesaPollInterval);
        _mpesaPollInterval = null;
    }
};

window.initiateMpesa = async function() {
    const phone      = document.getElementById('mpesaPhone').value.trim();
    const invoiceRef = document.getElementById('mpesa_inv_ref').textContent.trim();
    const errBox     = document.getElementById('mpesaInitError');

    if (!phone) { errBox.textContent = 'Please enter your M-PESA phone number.'; errBox.style.display = 'block'; return; }

    const btn  = document.getElementById('mpesaSendBtn');
    const orig = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
    btn.disabled  = true;
    errBox.style.display = 'none';

    try {
        const res  = await fetch('api/mpesa/initiate.php', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({ invoice_ref: invoiceRef, phone })
        });
        const data = await res.json();

        if (data.success) {
            _mpesaTransactionId = data.transaction_id;
            document.getElementById('mpesaPhoneDisplay').textContent = data.phone_formatted;
            _mpesaShowStep(2);
            _startMpesaPolling();
        } else {
            errBox.textContent   = data.message || 'Failed to initiate payment. Try again.';
            errBox.style.display = 'block';
            btn.innerHTML = orig;
            btn.disabled  = false;
        }
    } catch (err) {
        errBox.textContent   = 'Connection error. Please try again.';
        errBox.style.display = 'block';
        btn.innerHTML = orig;
        btn.disabled  = false;
    }
};

function _startMpesaPolling() {
    let attempts = 0;

    _mpesaPollInterval = setInterval(async () => {
        attempts++;
        if (attempts > 40) { // 40 × 3 s = 2 min timeout
            clearInterval(_mpesaPollInterval);
            document.getElementById('mpesaFailMsg').textContent = 'Payment timed out. Please try again or contact support.';
            _mpesaShowStep(4);
            return;
        }

        try {
            const res  = await fetch(`api/mpesa/status.php?id=${_mpesaTransactionId}`);
            const data = await res.json();
            if (!data.success) return; // keep polling

            if (data.status === 'completed') {
                clearInterval(_mpesaPollInterval);
                document.getElementById('mpesaReceiptNum').textContent = data.receipt
                    ? `M-PESA Ref: ${data.receipt}`
                    : 'Payment received';
                _mpesaShowStep(3);
            } else if (data.status === 'failed' || data.status === 'cancelled') {
                clearInterval(_mpesaPollInterval);
                document.getElementById('mpesaFailMsg').textContent =
                    data.message || 'Payment was cancelled or failed. Please try again.';
                _mpesaShowStep(4);
            }
            // status === 'pending' → keep polling
        } catch (e) { /* network hiccup — keep polling */ }
    }, 3000);
}

/**
 * Notification Bell — loads unread counts and updates the badge
 */
async function loadNotifications() {
    try {
        const res  = await fetch('api/notifications.php');
        const data = await res.json();
        if (!data.success) return;

        const badge = document.getElementById('notificationBadge');
        const count = data.total;

        if (badge) {
            if (count > 0) {
                badge.style.display = 'flex';
                badge.textContent   = count > 9 ? '9+' : count;
            } else {
                badge.style.display = 'none';
            }
        }
    } catch (e) { /* silently fail */ }
}
