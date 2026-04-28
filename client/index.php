<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shanfix Client Portal | Modern Dashboard</title>
    <link rel="stylesheet" href="../index.css"> 
    <link rel="stylesheet" href="client.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="client-portal-body">
    
    <!-- Portal Sidebar -->
    <aside class="portal-sidebar">
        <div class="portal-logo">
            <img src="../assets/shanfix-logo.png" alt="Shanfix Logo" style="height: 32px;">
            <h2>Shanfix<span>Portal</span></h2>
        </div>
        <nav class="portal-nav">
            <a href="#" class="portal-nav-item active" data-tab="dashboard">
                <i class="fas fa-th-large"></i> Dashboard
            </a>
            <a href="#" class="portal-nav-item" data-tab="billing">
                <i class="fas fa-credit-card"></i> Billing & Invoices
            </a>
            <a href="#" class="portal-nav-item" data-tab="services">
                <i class="fas fa-box"></i> My Services
            </a>
            <a href="#" class="portal-nav-item" data-tab="support">
                <i class="fas fa-headset"></i> Support Center
            </a>
            <a href="#" class="portal-nav-item" data-tab="settings">
                <i class="fas fa-cog"></i> Account Settings
            </a>
        </nav>
        <div class="portal-sidebar-footer">
            <button id="logoutBtn" class="portal-btn-logout">
                <i class="fas fa-power-off"></i> Sign Out
            </button>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="portal-main">
        
        <!-- Header -->
        <header class="portal-header glass-card">
            <div class="portal-header-left">
                <h1 id="welcomeText">Dashboard</h1>
                <p id="headerSubtitle">Welcome back to your workspace.</p>
            </div>
            
            <div class="portal-header-right">
                <div class="header-actions">
                    <div class="action-btn" title="Search">
                        <i class="fas fa-search"></i>
                    </div>
                    <div class="action-btn" title="Notifications">
                        <i class="fas fa-bell"></i>
                        <span class="notification-dot"></span>
                    </div>
                </div>
                
                <div class="portal-profile" id="profileDropdownTrigger">
                    <div class="profile-avatar"><i class="fas fa-user"></i></div>
                    <div class="profile-info">
                        <strong id="headerClientName">Loading...</strong>
                        <span id="headerClientEmail">loading@...</span>
                    </div>
                    <i class="fas fa-chevron-down" style="font-size: 0.7rem; color: #64748b; margin-left: 8px;"></i>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <div class="portal-content-area">
            
            <!-- Dashboard Tab -->
            <section id="tab-dashboard" class="portal-tab-content active">
                <div class="portal-stats-grid">
                    <div class="portal-stat-card glass-card">
                        <div class="stat-icon pending-balance"><i class="fas fa-wallet"></i></div>
                        <div class="stat-details">
                            <h3>Unpaid Balance</h3>
                            <h2 id="dashPendingBalance">Ksh 0.00</h2>
                        </div>
                    </div>
                    <div class="portal-stat-card glass-card">
                        <div class="stat-icon active-services"><i class="fas fa-check-circle"></i></div>
                        <div class="stat-details">
                            <h3>Active Services</h3>
                            <h2 id="dashActiveServices">0</h2>
                        </div>
                    </div>
                    <div class="portal-stat-card glass-card">
                        <div class="stat-icon open-tickets"><i class="fas fa-life-ring"></i></div>
                        <div class="stat-details">
                            <h3>Support Tickets</h3>
                            <h2 id="dashOpenTickets">0</h2>
                        </div>
                    </div>
                </div>

                <div class="quick-actions-bar">
                    <div class="qa-card glass-card" onclick="switchTab('billing')">
                        <i class="fas fa-plus-circle"></i>
                        <span>Pay Invoice</span>
                    </div>
                    <div class="qa-card glass-card" onclick="switchTab('support')">
                        <i class="fas fa-ticket-alt"></i>
                        <span>Open Ticket</span>
                    </div>
                    <div class="qa-card glass-card" onclick="switchTab('services')">
                        <i class="fas fa-shopping-cart"></i>
                        <span>New Service</span>
                    </div>
                    <div class="qa-card glass-card" onclick="switchTab('settings')">
                        <i class="fas fa-user-edit"></i>
                        <span>Edit Profile</span>
                    </div>
                </div>

                <div class="portal-card glass-card mt-30">
                    <div class="flex-between">
                        <h3>Recent Transactions</h3>
                        <button class="portal-btn-sm portal-btn-outline" onclick="switchTab('billing')">View All</button>
                    </div>
                    <div class="portal-table-container">
                        <table class="portal-table" id="recentInvoicesTable">
                            <thead>
                                <tr>
                                    <th>Invoice ID</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Injected via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Billing Tab -->
            <section id="tab-billing" class="portal-tab-content">
                <div class="portal-card glass-card">
                    <div class="portal-card-header">
                        <h3>Billing & Payments</h3>
                        <p>Manage your invoices and payment history.</p>
                    </div>
                    <div class="portal-table-container">
                        <table class="portal-table" id="allInvoicesTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Date Issued</th>
                                    <th>Due Date</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
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
                <div class="portal-card glass-card">
                    <div class="portal-card-header flex-between">
                        <div>
                            <h3>Subscribed Services</h3>
                            <p>Overview of your active hosting, domains, and SMS packages.</p>
                        </div>
                        <button class="portal-btn portal-btn-primary" onclick="alert('Redirecting to service shop...')">
                            <i class="fas fa-plus"></i> Buy New
                        </button>
                    </div>
                    
                    <div class="services-grid" id="servicesGrid">
                        <!-- Injected via JS -->
                    </div>
                </div>
            </section>

            <!-- Support Tickets Tab -->
            <section id="tab-support" class="portal-tab-content">
                <div class="portal-grid-2">
                    
                    <div class="portal-card glass-card">
                        <h3>New Support Request</h3>
                        <p style="margin-bottom: 1.5rem; font-size: 0.9rem; color: #64748b;">Submit a ticket and our team will get back to you shortly.</p>
                        <form id="newTicketForm">
                            <div class="form-group-premium">
                                <label>Subject</label>
                                <input type="text" id="ticketSubject" class="form-control" placeholder="e.g. Website Down" required>
                            </div>
                            <div class="form-group-premium">
                                <label>Priority</label>
                                <select id="ticketPriority" class="form-control" required>
                                    <option value="Low">Low - Inquiry</option>
                                    <option value="Medium" selected>Medium - Issue</option>
                                    <option value="High">High - Urgent</option>
                                </select>
                            </div>
                            <div class="form-group-premium">
                                <label>Message</label>
                                <textarea id="ticketMessage" class="form-control" rows="4" placeholder="Describe your issue in detail..." required></textarea>
                            </div>
                            <button type="submit" class="portal-btn portal-btn-primary w-100">Submit Ticket</button>
                        </form>
                    </div>

                    <div class="portal-card glass-card">
                        <h3>Ticket History</h3>
                        <div class="ticket-list" id="ticketList" style="margin-top: 1rem;">
                            <!-- Injected via JS -->
                        </div>
                    </div>
                </div>
            </section>

            <!-- Settings Tab -->
            <section id="tab-settings" class="portal-tab-content">
                <div class="portal-grid-2">
                    <div class="portal-card glass-card">
                        <h3>Profile Information</h3>
                        <form id="profileForm" class="mt-15">
                            <div class="form-group-premium">
                                <label>Full Name</label>
                                <input type="text" id="settingName" class="form-control" value="Loading...">
                            </div>
                            <div class="form-group-premium">
                                <label>Email Address</label>
                                <input type="email" id="settingEmail" class="form-control" value="Loading..." readonly>
                            </div>
                            <div class="form-group-premium">
                                <label>Phone Number</label>
                                <input type="text" class="form-control" value="+254 7XX XXX XXX">
                            </div>
                            <button type="button" class="portal-btn portal-btn-primary" onclick="alert('Profile updated successfully!')">Save Changes</button>
                        </form>
                    </div>

                    <div class="portal-card glass-card">
                        <h3>Security</h3>
                        <p style="font-size: 0.9rem; color: #64748b;">Keep your account secure by updating your password regularly.</p>
                        <form class="mt-15">
                            <div class="form-group-premium">
                                <label>Current Password</label>
                                <input type="password" class="form-control">
                            </div>
                            <div class="form-group-premium">
                                <label>New Password</label>
                                <input type="password" class="form-control">
                            </div>
                            <button type="button" class="portal-btn portal-btn-outline">Update Password</button>
                        </form>
                    </div>
                </div>
            </section>

        </div>
    </main>

    <script src="client.js"></script>
</body>
</html>
