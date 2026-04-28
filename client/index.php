<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shanfix Client Portal</title>
    <!-- We will reuse the main site's CSS for grid and some core styles, plus a custom client.css -->
    <link rel="stylesheet" href="../index.css"> 
    <link rel="stylesheet" href="client.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Outfit:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="client-portal-body">
    
    <!-- Portal Sidebar -->
    <aside class="portal-sidebar">
        <div class="portal-logo">
            <h2>Shanfix<span>Portal</span></h2>
        </div>
        <nav class="portal-nav">
            <a href="#" class="portal-nav-item active" data-tab="dashboard">
                <i class="fas fa-home"></i> Dashboard Snapshot
            </a>
            <a href="#" class="portal-nav-item" data-tab="billing">
                <i class="fas fa-file-invoice-dollar"></i> Receipts & Invoices
            </a>
            <a href="#" class="portal-nav-item" data-tab="services">
                <i class="fas fa-server"></i> Active Subscriptions
            </a>
            <a href="#" class="portal-nav-item" data-tab="support">
                <i class="fas fa-life-ring"></i> Support Tickets
            </a>
        </nav>
        <div class="portal-sidebar-footer">
            <button id="logoutBtn" class="portal-btn-logout"><i class="fas fa-sign-out-alt"></i> Secure Logout</button>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="portal-main">
        
        <!-- Header -->
        <header class="portal-header glass-card">
            <div class="portal-header-left">
                <h1 id="welcomeText">Welcome back!</h1>
                <p>Here's what's happening with your account today.</p>
            </div>
            <div class="portal-header-right">
                <div class="portal-profile">
                    <div class="profile-avatar"><i class="fas fa-user-tie"></i></div>
                    <div class="profile-info">
                        <strong id="headerClientName">Loading...</strong>
                        <span id="headerClientEmail">loading@...</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <div class="portal-content-area">
            
            <!-- Dashboard Tab -->
            <section id="tab-dashboard" class="portal-tab-content active">
                <div class="portal-stats-grid">
                    <div class="portal-stat-card glass-card">
                        <div class="stat-icon pending-balance"><i class="fas fa-exclamation-circle"></i></div>
                        <div class="stat-details">
                            <h3>Pending Balance</h3>
                            <h2 id="dashPendingBalance">Ksh 0.00</h2>
                        </div>
                    </div>
                    <div class="portal-stat-card glass-card">
                        <div class="stat-icon active-services"><i class="fas fa-box-open"></i></div>
                        <div class="stat-details">
                            <h3>Active Services</h3>
                            <h2 id="dashActiveServices">0</h2>
                        </div>
                    </div>
                    <div class="portal-stat-card glass-card">
                        <div class="stat-icon open-tickets"><i class="fas fa-ticket-alt"></i></div>
                        <div class="stat-details">
                            <h3>Open Support Tickets</h3>
                            <h2 id="dashOpenTickets">0</h2>
                        </div>
                    </div>
                </div>

                <div class="portal-recent-activity glass-card mt-30">
                    <h3>Recent Invoices</h3>
                    <div class="portal-table-container">
                        <table class="portal-table" id="recentInvoicesTable">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
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
                        <h3>Your Billing History</h3>
                        <p>View, download, and pay your pending invoices.</p>
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
                            <h3>Active Subscriptions</h3>
                            <p>Manage your hosting, domains, and bulk SMS packages.</p>
                        </div>
                        <button class="portal-btn portal-btn-primary" onclick="alert('Redirecting to new service wizard...')"><i class="fas fa-plus"></i> Buy New Service</button>
                    </div>
                    
                    <div class="services-grid" id="servicesGrid">
                        <!-- Example Mock Services injected via JS -->
                    </div>
                </div>
            </section>

            <!-- Support Tickets Tab -->
            <section id="tab-support" class="portal-tab-content">
                <div class="portal-grid-2">
                    
                    <!-- Open New Ticket Form -->
                    <div class="portal-card glass-card">
                        <h3>Open a Support Ticket</h3>
                        <form id="newTicketForm" class="mt-15">
                            <div class="form-group-premium">
                                <input type="text" id="ticketSubject" class="form-control" placeholder=" " required>
                                <label for="ticketSubject">Issue Subject</label>
                            </div>
                            <div class="form-group-premium mt-15">
                                <select id="ticketPriority" class="form-control" required>
                                    <option value="" disabled selected>Select Priority</option>
                                    <option value="Low">Low - General Inquiry</option>
                                    <option value="Medium">Medium - Issue</option>
                                    <option value="High">High - Urgent / System Down</option>
                                </select>
                            </div>
                            <div class="form-group-premium mt-15">
                                <textarea id="ticketMessage" class="form-control" placeholder=" " required rows="4"></textarea>
                                <label for="ticketMessage">Detailed description of the issue</label>
                            </div>
                            <button type="submit" class="portal-btn portal-btn-primary mt-15 w-100">Submit Ticket</button>
                        </form>
                    </div>

                    <!-- Ticket History -->
                    <div class="portal-card glass-card">
                        <h3>Ticket History</h3>
                        <div class="ticket-list" id="ticketList">
                            <!-- Injected via JS -->
                        </div>
                    </div>

                </div>
            </section>

        </div>
    </main>

    <script src="client.js"></script>
</body>
</html>
