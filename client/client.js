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

    // 5. Data Hydration
    loadInvoices();
    loadTickets();
    loadServices();

    // 6. Form Handlers (Support Tickets)
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
        if(!recentBody || !allBody) return;

        recentBody.innerHTML = '';
        allBody.innerHTML = '';
        let totalUnpaid = 0;

        if (!data.success || data.invoices.length === 0) {
            recentBody.innerHTML = '<tr><td colspan="5" style="text-align:center; color: var(--text-low); padding: 2rem;">No transaction history found.</td></tr>';
            allBody.innerHTML = '<tr><td colspan="6" style="text-align:center; color: var(--text-low); padding: 2rem;">You currently have no invoices in the database.</td></tr>';
        } else {
            data.invoices.forEach((inv, idx) => {
                const isPaid = inv.status.toLowerCase() === 'paid';
                const badge = isPaid ? 'badge-paid' : 'badge-pending';
                if(!isPaid) totalUnpaid += parseFloat(inv.total);

                const row = `
                    <tr>
                        <td style="font-weight:700; color:var(--p)">${inv.id}</td>
                        <td>${inv.date}</td>
                        <td style="font-weight:700;">Ksh ${parseFloat(inv.total).toLocaleString()}</td>
                        <td><span class="badge ${badge}">${inv.status}</span></td>
                        <td style="text-align:right;">
                            <button class="portal-btn-primary" style="padding: 0.5rem 1rem; width: auto; font-size: 0.75rem;" onclick="alert('Displaying invoice PDF...')">
                                <i class="fas fa-file-invoice mr-1"></i> View
                            </button>
                        </td>
                    </tr>
                `;
                allBody.innerHTML += row;
                if(idx < 4) recentBody.innerHTML += row;
            });
        }

        const balDisplay = document.getElementById('dashPendingBalance');
        if(balDisplay) balDisplay.textContent = `Ksh ${totalUnpaid.toLocaleString()}`;
    } catch (error) {
        console.error('Invoice Load Error:', error);
    }
}

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
                            <button class="portal-btn-primary" style="padding:0.4rem 1rem; width:auto; font-size:0.7rem; background:white; color:var(--p); border: 1px solid var(--border);" onclick="alert('Opening conversation...')">Respond</button>
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

/**
 * Service Mock Loader
 */
function loadServices() {
    const grid = document.getElementById('servicesGrid');
    if(!grid) return;
    
    const mocks = [
        { name: "Enterprise Hosting", icon: "fa-server", type: "Server", status: "Active", val: "Online" },
        { name: "Bulk Messaging", icon: "fa-comments", type: "SMS API", status: "Active", val: "5K Units" },
        { name: "Security Suite", icon: "fa-shield-alt", type: "Firewall", status: "Active", val: "Protected" }
    ];

    const countDisplay = document.getElementById('dashActiveServices');
    if(countDisplay) countDisplay.textContent = mocks.length;
    grid.innerHTML = '';
    
    mocks.forEach(m => {
        grid.innerHTML += `
            <div class="stat-card" style="padding:1.5rem; border-radius: 24px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1.5rem;">
                    <div class="stat-icon" style="margin:0; width:48px; height:48px;"><i class="fas ${m.icon}"></i></div>
                    <span style="font-size:0.7rem; font-weight:800; color:var(--s); background:rgba(11, 181, 11, 0.1); padding: 4px 10px; border-radius: 10px;">${m.status}</span>
                </div>
                <h4 style="margin:0; color:var(--p);">${m.name}</h4>
                <div style="font-size:0.8rem; color:var(--text-low); margin-top: 4px;">${m.type}</div>
                <div style="margin-top: 1.5rem; display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-weight:700; font-size:1.1rem; color:var(--p);">${m.val}</span>
                    <i class="fas fa-arrow-right" style="color:var(--text-low); cursor:pointer;"></i>
                </div>
            </div>
        `;
    });
}
