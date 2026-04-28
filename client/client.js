/**
 * SHANFIX CLIENT PORTAL LOGIC - MODERNIZED
 * Handles dashboard data hydration, tab switching, and enhanced interactivity.
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Verify Client Authentication
    const isClient = sessionStorage.getItem('isClient');
    if (isClient !== 'true') {
        window.location.href = 'login.php';
        return;
    }

    const clientEmail = sessionStorage.getItem('client_email');
    const clientName = sessionStorage.getItem('client_name');
    
    // 2. Set Profile Data
    document.getElementById('headerClientName').textContent = clientName;
    document.getElementById('headerClientEmail').textContent = clientEmail;
    document.getElementById('welcomeText').textContent = `Welcome back, ${clientName.split(' ')[0]}!`;
    
    // Settings hydration
    if(document.getElementById('settingName')) document.getElementById('settingName').value = clientName;
    if(document.getElementById('settingEmail')) document.getElementById('settingEmail').value = clientEmail;

    // 3. Init Navigation Tabs
    const navItems = document.querySelectorAll('.portal-nav-item');
    navItems.forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            const tabId = item.getAttribute('data-tab');
            switchTab(tabId);
        });
    });

    // 4. Logout Handler
    document.getElementById('logoutBtn').addEventListener('click', () => {
        if(confirm('Are you sure you want to securely sign out?')) {
            sessionStorage.removeItem('isClient');
            sessionStorage.removeItem('client_email');
            sessionStorage.removeItem('client_name');
            window.location.href = 'login.php';
        }
    });

    // 5. Hydrate Data
    loadClientInvoices(clientEmail);
    loadClientTickets(clientEmail);
    loadClientServices();

    // 6. Handle New Ticket Submission
    const ticketForm = document.getElementById('newTicketForm');
    if(ticketForm) {
        ticketForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const subject = document.getElementById('ticketSubject').value;
            const priority = document.getElementById('ticketPriority').value;
            const message = document.getElementById('ticketMessage').value;

            const existingTickets = JSON.parse(localStorage.getItem('portal_tickets')) || [];
            
            const newTicket = {
                id: 'TKT-' + Math.floor(Math.random() * 10000),
                clientEmail: clientEmail,
                clientName: clientName,
                subject,
                priority,
                message,
                status: 'Open',
                date: new Date().toLocaleDateString()
            };

            existingTickets.unshift(newTicket);
            localStorage.setItem('portal_tickets', JSON.stringify(existingTickets));
            
            // Custom alert/toast logic could go here
            alert('Your support ticket has been submitted successfully!');
            ticketForm.reset();
            loadClientTickets(clientEmail);
        });
    }

    // 7. Micro-interactions: Header blur on scroll
    const main = document.querySelector('.portal-main');
    main.addEventListener('scroll', () => {
        const header = document.querySelector('.portal-header');
        if (main.scrollTop > 20) {
            header.style.boxShadow = '0 10px 15px -3px rgba(0, 0, 0, 0.05)';
        } else {
            header.style.boxShadow = 'none';
        }
    });
});

/**
 * Global Tab Switcher with transition support
 */
function switchTab(tabId) {
    const navItems = document.querySelectorAll('.portal-nav-item');
    const tabs = document.querySelectorAll('.portal-tab-content');
    
    // Update Sidebar
    navItems.forEach(n => {
        n.classList.remove('active');
        if(n.getAttribute('data-tab') === tabId) n.classList.add('active');
    });

    // Update Content with smooth transition
    tabs.forEach(t => {
        t.style.opacity = '0';
        setTimeout(() => {
            t.classList.remove('active');
            if(t.id === `tab-${tabId}`) {
                t.classList.add('active');
                setTimeout(() => {
                    t.style.opacity = '1';
                }, 50);
            }
        }, 300);
    });

    // Update Header Text
    const headerTitle = document.getElementById('welcomeText');
    const headerSub = document.getElementById('headerSubtitle');
    
    const meta = {
        'dashboard': { title: 'Dashboard', sub: 'Welcome back to your workspace.' },
        'billing': { title: 'Billing & Payments', sub: 'Manage your invoices and payment history.' },
        'services': { title: 'My Services', sub: 'Overview of your active subscriptions.' },
        'support': { title: 'Support Center', sub: 'Get help with your technical issues.' },
        'settings': { title: 'Account Settings', sub: 'Manage your profile and security.' }
    };

    if(meta[tabId]) {
        headerTitle.textContent = meta[tabId].title;
        headerSub.textContent = meta[tabId].sub;
    }
}

function loadClientInvoices(email) {
    const allInvoices = JSON.parse(localStorage.getItem('admin_invoices')) || [];
    const myInvoices = allInvoices.filter(inv => inv.customerEmail.toLowerCase() === email.toLowerCase());

    const recentTable = document.querySelector('#recentInvoicesTable tbody');
    const allTable = document.querySelector('#allInvoicesTable tbody');
    
    if(!recentTable || !allTable) return;

    recentTable.innerHTML = '';
    allTable.innerHTML = '';

    let pendingBalance = 0;

    if (myInvoices.length === 0) {
        recentTable.innerHTML = '<tr><td colspan="5" style="text-align:center; padding: 2rem; color: #64748b;">No recent invoices found.</td></tr>';
        allTable.innerHTML = '<tr><td colspan="6" style="text-align:center; padding: 2rem; color: #64748b;">Your billing history is empty.</td></tr>';
    } else {
        myInvoices.forEach((inv, index) => {
            const statusClass = inv.status === 'Paid' ? 'badge-paid' : 'badge-pending';
            if(inv.status !== 'Paid') pendingBalance += parseFloat(inv.total);

            const rowHtml = `
                <tr>
                    <td><strong>${inv.id}</strong></td>
                    <td>${inv.date}</td>
                    <td>${inv.dueDate || inv.date}</td>
                    <td>Ksh ${parseFloat(inv.total).toLocaleString()}</td>
                    <td><span class="badge ${statusClass}">${inv.status}</span></td>
                    <td>
                        <button class="portal-btn-sm portal-btn-outline" onclick="alert('Downloading invoice ${inv.id}...')"><i class="fas fa-download"></i></button>
                    </td>
                </tr>
            `;
            allTable.innerHTML += rowHtml;

            if (index < 3) {
                recentTable.innerHTML += `
                    <tr>
                        <td><strong>${inv.id}</strong></td>
                        <td>${inv.date}</td>
                        <td>Ksh ${parseFloat(inv.total).toLocaleString()}</td>
                        <td><span class="badge ${statusClass}">${inv.status}</span></td>
                        <td>
                            <button class="portal-btn-sm portal-btn-outline" onclick="alert('Viewing details...')">Details</button>
                        </td>
                    </tr>
                `;
            }
        });
    }

    document.getElementById('dashPendingBalance').textContent = `Ksh ${pendingBalance.toLocaleString()}`;
}

function loadClientTickets(email) {
    const allTickets = JSON.parse(localStorage.getItem('portal_tickets')) || [];
    const myTickets = allTickets.filter(t => t.clientEmail === email);

    const ticketList = document.getElementById('ticketList');
    if(!ticketList) return;

    ticketList.innerHTML = '';
    let openTicketsCount = 0;

    if(myTickets.length === 0) {
        ticketList.innerHTML = '<div style="padding: 2rem; text-align: center; color: #64748b;"><i class="fas fa-comment-slash" style="font-size: 2rem; margin-bottom: 10px; display: block;"></i><p>No support history found.</p></div>';
    } else {
        myTickets.forEach(t => {
            const statusBadge = t.status === 'Open' ? 'badge-open' : 'badge-closed';
            if(t.status === 'Open') openTicketsCount++;

            ticketList.innerHTML += `
                <div class="ticket-item" style="padding: 1.25rem; border-radius: 16px; border: 1px solid #f1f5f9; margin-bottom: 1rem; background: #fff;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                        <div>
                            <h4 style="margin: 0; font-family: 'Outfit'; font-size: 1.05rem;">${t.subject}</h4>
                            <span style="font-size: 0.75rem; color: #94a3b8;">${t.id} • Opened ${t.date}</span>
                        </div>
                        <span class="badge ${statusBadge}">${t.status}</span>
                    </div>
                    <p style="font-size: 0.85rem; color: #475569; margin: 0.5rem 0;">${t.message}</p>
                    <div style="margin-top: 10px; display: flex; gap: 10px;">
                         <button class="portal-btn-sm portal-btn-outline" style="font-size: 0.75rem;" onclick="alert('Opening chat...')">Reply</button>
                         <span style="font-size: 0.75rem; color: #94a3b8; align-self: center;">Priority: ${t.priority}</span>
                    </div>
                </div>
            `;
        });
    }

    document.getElementById('dashOpenTickets').textContent = openTicketsCount;
}

function loadClientServices() {
    const servicesGrid = document.getElementById('servicesGrid');
    if(!servicesGrid) return;
    
    const mockServices = [
        { name: "Premium Hosting Plan", cycle: "Annually", exp: "Oct 12, 2026", icon: "fa-server", status: "Active" },
        { name: "Bulk SMS (10K Units)", cycle: "Prepaid", exp: "Never", icon: "fa-comment-alt", status: "Active" },
        { name: "maintenance.shanfix.com", cycle: "Monthly", exp: "Nov 01, 2026", icon: "fa-globe", status: "Active" }
    ];

    document.getElementById('dashActiveServices').textContent = mockServices.length;

    servicesGrid.innerHTML = '';
    mockServices.forEach(srv => {
        servicesGrid.innerHTML += `
            <div class="service-card glass-card">
                <div class="service-card-header">
                    <div class="service-icon-box"><i class="fas ${srv.icon}"></i></div>
                    <span class="service-status">${srv.status}</span>
                </div>
                <h4>${srv.name}</h4>
                <p>${srv.cycle} Billing Cycle</p>
                <div class="service-footer">
                    <span class="renewal-date">Renews: ${srv.exp}</span>
                    <button class="portal-btn-sm portal-btn-outline">Manage</button>
                </div>
            </div>
        `;
    });
}
