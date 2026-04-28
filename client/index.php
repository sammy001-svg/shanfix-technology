<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shanfix Technology | Client command center</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <!-- Styles -->
    <link rel="stylesheet" href="client.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="client-portal-body">
    
    <!-- Sidebar -->
    <aside class="portal-sidebar">
        <div class="sidebar-logo">
            <img src="../assets/shanfix-logo.png" alt="Shanfix Logo" style="height: 40px; margin-bottom: 12px; filter: brightness(0) invert(1);">
            <div class="outfit" style="font-size: 1.8rem; letter-spacing: -1px;">Shanfix <span style="color:var(--color-accent)">Portal</span></div>
        </div>
        
        <nav class="portal-nav" style="margin-top: 2.5rem; flex-grow: 1;">
            <a href="#" class="nav-item active" data-tab="dashboard">
                <i class="fas fa-layer-group"></i> <span>Dashboard</span>
            </a>
            <a href="#" class="nav-item" data-tab="billing">
                <i class="fas fa-credit-card"></i> <span>Billing</span>
            </a>
            <a href="#" class="nav-item" data-tab="services">
                <i class="fas fa-box-open"></i> <span>My Services</span>
            </a>
            <a href="#" class="nav-item" data-tab="support">
                <i class="fas fa-headset"></i> <span>Support</span>
            </a>
            <a href="#" class="nav-item" data-tab="settings">
                <i class="fas fa-user-gear"></i> <span>Account</span>
            </a>
        </nav>

        <div style="padding: 2rem;">
            <div style="background: rgba(255,255,255,0.05); border-radius: 20px; padding: 1.5rem; margin-bottom: 1.5rem;">
                <p style="font-size: 0.8rem; color: rgba(255,255,255,0.5); margin-bottom: 0.5rem;">Need help?</p>
                <a href="tel:+254700000000" style="color: var(--color-accent); font-weight: 700; text-decoration: none; font-size: 0.9rem;">
                    <i class="fas fa-phone-alt mr-2"></i> +254 7XX XXX XXX
                </a>
            </div>
            <button id="logoutBtn" class="portal-btn-primary portal-btn-danger" style="width: 100%; border-radius: 16px; padding: 1rem;">
                <i class="fas fa-power-off"></i> <span>Sign Out</span>
            </button>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="portal-main">
        
        <!-- Header -->
        <header class="glass-header">
            <div style="display: flex; flex-direction: column;">
                <h1 id="welcomeText" class="outfit" style="margin: 0; font-size: 1.8rem; color: var(--p);">Dashboard</h1>
                <div id="headerSubtitle" style="margin: 4px 0 0 0; font-size: 0.9rem; color: var(--text-mid); display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-sparkles" style="color: var(--color-accent);"></i> Your account is currently healthy.
                </div>
            </div>
            
            <div style="display: flex; gap: 1.5rem; align-items: center;">
                <!-- Modern Search Bar -->
                <div style="position: relative; display: flex; align-items: center;">
                    <i class="fas fa-search" style="position: absolute; left: 1.2rem; color: var(--text-low); font-size: 0.9rem;"></i>
                    <input type="text" placeholder="Search services..." style="padding: 0.8rem 1.2rem 0.8rem 2.8rem; border-radius: 50px; border: 1px solid var(--border); background: white; width: 260px; font-size: 0.9rem; transition: all 0.3s;" onfocus="this.style.width='320px'; this.style.borderColor='var(--s)'" onblur="this.style.width='260px'; this.style.borderColor='var(--border)'">
                </div>

                <!-- Notifications -->
                <div style="position: relative; cursor: pointer; width: 44px; height: 44px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow-sm); border: 1px solid var(--border);">
                    <i class="fas fa-bell" style="color: var(--text-mid);"></i>
                    <span style="position: absolute; top: 0; right: 0; width: 12px; height: 12px; background: var(--red); border-radius: 50%; border: 2px solid white;"></span>
                </div>

                <div class="user-profile">
                    <div class="avatar"><i class="fas fa-user-astronaut"></i></div>
                    <div style="display: flex; flex-direction: column;">
                        <strong id="headerClientName" style="color: var(--p); font-size: 0.95rem;">Loading...</strong>
                        <span id="headerClientEmail" style="color: var(--text-low); font-size: 0.75rem;">loading@email.com</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <div id="content-container">
            
            <!-- Dashboard Tab -->
            <section id="tab-dashboard" class="portal-tab-content active">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(239, 68, 68, 0.05); color: var(--red);"><i class="fas fa-wallet"></i></div>
                        <div class="stat-value" id="dashPendingBalance">Ksh 0.00</div>
                        <div class="stat-label">Pending Dues</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(34, 197, 94, 0.05); color: var(--s);"><i class="fas fa-microchip"></i></div>
                        <div class="stat-value" id="dashActiveServices">0</div>
                        <div class="stat-label">Provisioned Services</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(89, 224, 10, 0.05); color: var(--color-accent);"><i class="fas fa-bolt"></i></div>
                        <div class="stat-value" id="dashOpenTickets">0</div>
                        <div class="stat-label">Active Interactions</div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2.5rem; align-items: start;">
                    <div class="data-card">
                        <h3 class="outfit" style="margin: 0 0 2rem 0; display: flex; align-items: center; gap: 12px; color: var(--p);">
                            <i class="fas fa-list-check" style="color:var(--s)"></i> Recent Activity
                        </h3>
                        <div style="overflow-x: auto;">
                            <table class="premium-table" id="recentInvoicesTable">
                                <thead>
                                    <tr>
                                        <th>Ref #</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th style="text-align: right;">Portal Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Injected via JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="data-card" style="padding: 2rem;">
                        <h3 class="outfit" style="margin: 0 0 1.5rem 0; font-size: 1.2rem; color: var(--p);">Quick Actions</h3>
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <button class="portal-btn-primary" style="width: 100%; justify-content: flex-start; background: var(--bg); color: var(--p); box-shadow: none; border: 1px solid var(--border);" onclick="switchTab('support')">
                                <i class="fas fa-plus-circle" style="color: var(--s)"></i> New Support Ticket
                            </button>
                            <button class="portal-btn-primary" style="width: 100%; justify-content: flex-start; background: var(--bg); color: var(--p); box-shadow: none; border: 1px solid var(--border);" onclick="switchTab('billing')">
                                <i class="fas fa-credit-card" style="color: var(--red)"></i> Pay Outstanding
                            </button>
                            <button class="portal-btn-primary" style="width: 100%; justify-content: flex-start; background: var(--bg); color: var(--p); box-shadow: none; border: 1px solid var(--border);" onclick="switchTab('services')">
                                <i class="fas fa-rocket" style="color: var(--color-accent)"></i> Upgrade Service
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Other tabs follow the same improved pattern via JS if needed, 
                 but they are already structurally good in index.php -->
            
            <!-- Billing Tab -->
            <section id="tab-billing" class="portal-tab-content">
                <div class="data-card">
                    <h3 class="outfit" style="color: var(--p); margin-bottom: 2rem;">Billing & Invoicing</h3>
                    <div style="overflow-x: auto;">
                        <table class="premium-table" id="allInvoicesTable">
                            <thead>
                                <tr>
                                    <th>Invoice ID</th>
                                    <th>Date</th>
                                    <th>Due Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th style="text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Injected via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Services Tab -->
            <section id="tab-services" class="portal-tab-content">
                <div class="data-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem;">
                        <h3 class="outfit" style="color: var(--p); margin: 0;">Service Infrastructure</h3>
                        <button class="portal-btn-primary">
                            <i class="fas fa-plus"></i> New Subscription
                        </button>
                    </div>
                    <div class="stats-grid" id="servicesGrid">
                        <!-- Injected via JS -->
                    </div>
                </div>
            </section>

            <!-- Support Tab -->
            <section id="tab-support" class="portal-tab-content">
                <div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 3rem;">
                    <div class="data-card">
                        <h3 class="outfit" style="color: var(--p);">Get Assistance</h3>
                        <form id="newTicketForm">
                            <div class="form-group-premium">
                                <input type="text" id="ticketSubject" class="form-control" placeholder=" " required>
                                <label for="ticketSubject">Subject</label>
                            </div>
                            <div class="form-group-premium">
                                <select id="ticketPriority" class="form-control" required style="padding-top: 1.5rem;">
                                    <option value="Low">Low</option>
                                    <option value="Medium" selected>Medium</option>
                                    <option value="High">High</option>
                                </select>
                                <label for="ticketPriority">Priority</label>
                            </div>
                            <div class="form-group-premium">
                                <textarea id="ticketMessage" class="form-control" placeholder=" " required rows="6"></textarea>
                                <label for="ticketMessage">Description</label>
                            </div>
                            <button type="submit" class="portal-btn-primary" style="width: 100%;">
                                <i class="fas fa-paper-plane"></i> Send Request
                            </button>
                        </form>
                    </div>
                    
                    <div class="data-card">
                        <h3 class="outfit" style="color: var(--p);">History</h3>
                        <div id="ticketList">
                            <!-- Injected via JS -->
                        </div>
                    </div>
                </div>
            </section>

            <!-- Settings Tab -->
            <section id="tab-settings" class="portal-tab-content">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem;">
                    <div class="data-card">
                        <h3 class="outfit" style="color: var(--p);">User Profile</h3>
                        <form>
                            <div class="form-group-premium">
                                <input type="text" id="settingName" class="form-control" placeholder=" ">
                                <label for="settingName">Name</label>
                            </div>
                            <div class="form-group-premium">
                                <input type="email" id="settingEmail" class="form-control" placeholder=" " readonly>
                                <label for="settingEmail">Email</label>
                            </div>
                            <button type="button" class="portal-btn-primary">Update Profile</button>
                        </form>
                    </div>
                    
                    <div class="data-card">
                        <h3 class="outfit" style="color: var(--p);">Security</h3>
                        <form>
                            <div class="form-group-premium">
                                <input type="password" class="form-control" placeholder=" ">
                                <label>New Password</label>
                            </div>
                            <button type="button" class="portal-btn-primary" style="background: var(--p);">Save Security</button>
                        </form>
                    </div>
                </div>
            </section>

        </div>
    </main>

    <!-- Ticket Detail Modal -->
    <div id="ticketModal" class="portal-modal">
        <div class="portal-modal-content" style="max-width: 800px;">
            <div class="portal-modal-header">
                <h3 id="modalTicketTitle" class="outfit" style="color: var(--p);">Ticket Conversation</h3>
                <button class="close-modal" onclick="closeTicketModal()">&times;</button>
            </div>
            <div class="portal-modal-body">
                <div id="ticketThread" class="ticket-thread-container">
                    <!-- Conversation loads here -->
                </div>

                <div class="reply-section mt-20" id="clientReplyArea">
                    <div class="form-group-premium">
                        <textarea id="clientReplyMessage" class="form-control" placeholder=" " rows="4" style="border: 1px solid var(--border);"></textarea>
                        <label for="clientReplyMessage">Your Response</label>
                    </div>
                    <div class="mt-15" style="display:flex; justify-content:flex-end;">
                        <button class="portal-btn-primary" onclick="submitClientReply()" style="width: auto; padding: 0.8rem 2rem;">
                            <i class="fas fa-paper-plane mr-1"></i> Send Reply
                        </button>
                    </div>
                </div>
                <div id="ticketClosedMsg" style="display:none; text-align:center; padding: 1rem; background: #fee2e2; color: #b91c1c; border-radius: 12px; margin-top: 1rem;">
                    This ticket has been closed. Please open a new ticket for further assistance.
                </div>
            </div>
        </div>
    </div>

    <script src="client.js"></script>
</body>
</html>
