/**
 * CLIENT PORTAL LOGIC
 * Handles dashboard data hydration, tab switching, and ticket mocking.
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Verify Client Authentication
    const isClient = sessionStorage.getItem('isClient');
    if (isClient !== 'true') {
        window.location.href = '../admin/login.php';
        return;
    }

    const clientEmail = sessionStorage.getItem('client_email');
    const clientName = sessionStorage.getItem('client_name');
    
    // 2. Set Profile Data
    document.getElementById('headerClientName').textContent = clientName;
    document.getElementById('headerClientEmail').textContent = clientEmail;
    document.getElementById('welcomeText').textContent = `Welcome back, ${clientName.split(' ')[0]}!`;

    // 3. Init Navigation Tabs
    const navItems = document.querySelectorAll('.portal-nav-item');
    navItems.forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            navItems.forEach(n => n.classList.remove('active'));
            document.querySelectorAll('.portal-tab-content').forEach(t => t.classList.remove('active'));
            
            item.classList.add('active');
            const tabId = item.getAttribute('data-tab');
            document.getElementById(`tab-${tabId}`).classList.add('active');
        });
    });

    // 4. Logout Handler
    document.getElementById('logoutBtn').addEventListener('click', () => {
        sessionStorage.removeItem('isClient');
        sessionStorage.removeItem('client_email');
        sessionStorage.removeItem('client_name');
        window.location.href = '../admin/login.php';
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
            
            alert('Your support ticket has been submitted!');
            ticketForm.reset();
            loadClientTickets(clientEmail);
        });
    }
});

function loadClientInvoices(email) {
    // We fetch ALL invoices from the admin system 
    // BUT we only show the ones addressed to this specific client's email!
    const allInvoices = JSON.parse(localStorage.getItem('admin_invoices')) || [];
    const myInvoices = allInvoices.filter(inv => inv.customerEmail.toLowerCase() === email.toLowerCase());

    const recentTable = document.querySelector('#recentInvoicesTable tbody');
    const allTable = document.querySelector('#allInvoicesTable tbody');
    
    recentTable.innerHTML = '';
    allTable.innerHTML = '';

    let pendingBalance = 0;

    if (myInvoices.length === 0) {
        recentTable.innerHTML = '<tr><td colspan="5">No recent invoices found.</td></tr>';
        allTable.innerHTML = '<tr><td colspan="6">Your billing history is empty.</td></tr>';
    } else {
        myInvoices.forEach((inv, index) => {
            const statusClass = inv.status === 'Paid' ? 'badge-paid' : 'badge-pending';
            
            if(inv.status !== 'Paid') pendingBalance += parseFloat(inv.total);

            // Populate ALL invoices tab
            allTable.innerHTML += `
                <tr>
                    <td><strong>${inv.id}</strong></td>
                    <td>${inv.date}</td>
                    <td>${inv.dueDate}</td>
                    <td>Ksh ${inv.total.toLocaleString()}</td>
                    <td><span class="badge ${statusClass}">${inv.status}</span></td>
                    <td>
                        <button class="portal-btn-sm portal-btn-outline" onclick="alert('Viewing invoice ${inv.id} PDF...')"><i class="fas fa-file-pdf"></i> View</button>
                    </td>
                </tr>
            `;

            // Populate RECENT activity (limit to 3)
            if (index < 3) {
                recentTable.innerHTML += `
                    <tr>
                        <td><strong>${inv.id}</strong></td>
                        <td>${inv.date}</td>
                        <td>Ksh ${inv.total.toLocaleString()}</td>
                        <td><span class="badge ${statusClass}">${inv.status}</span></td>
                        <td>
                            <button class="portal-btn-sm portal-btn-outline" onclick="alert('Viewing invoice...')">View</button>
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
    ticketList.innerHTML = '';

    let openTickets = 0;

    if(myTickets.length === 0) {
        ticketList.innerHTML = '<p>You have no ticket history.</p>';
    } else {
        myTickets.forEach(t => {
            const statusBadge = t.status === 'Open' ? 'badge-open' : 'badge-closed';
            if(t.status === 'Open') openTickets++;

            ticketList.innerHTML += `
                <div class="ticket-item">
                    <div class="ticket-item-header">
                        <div>
                            <h4>${t.subject} <span class="badge ${statusBadge}">${t.status}</span></h4>
                            <span class="ticket-meta">${t.id} • Opened ${t.date} • Priority: ${t.priority}</span>
                        </div>
                        <button class="portal-btn-sm portal-btn-outline" onclick="alert('Viewing ticket chat...')">View Reply</button>
                    </div>
                    <p>${t.message}</p>
                </div>
            `;
        });
    }

    document.getElementById('dashOpenTickets').textContent = openTickets;
}

function loadClientServices() {
    // Mocking active services since there's no complex DB relationship yet
    const servicesGrid = document.getElementById('servicesGrid');
    
    const mockServices = [
        { name: "Premium Hosting Plan", cycle: "Annually", exp: "Oct 12, 2026", icon: "fa-server" },
        { name: "Bulk SMS (10K Units)", cycle: "Prepaid", exp: "Never", icon: "fa-comment-alt" },
        { name: "maintenance.shanfix.com", cycle: "Monthly", exp: "Nov 01, 2026", icon: "fa-globe" }
    ];

    document.getElementById('dashActiveServices').textContent = mockServices.length;

    servicesGrid.innerHTML = '';
    mockServices.forEach(srv => {
        servicesGrid.innerHTML += `
            <div class="service-card">
                <div class="service-card-header">
                    <div class="service-card-icon"><i class="fas ${srv.icon}"></i></div>
                    <div>
                        <h4>${srv.name}</h4>
                        <span>${srv.cycle} Billing</span>
                    </div>
                </div>
                <hr>
                <div class="flex-between">
                    <span style="font-size:0.8rem; color:#718096">Renews: ${srv.exp}</span>
                    <button class="portal-btn-sm portal-btn-outline">Manage</button>
                </div>
            </div>
        `;
    });
}
