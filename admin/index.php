<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard - Shanfix Admin</title>
    <link rel="stylesheet" href="../admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Outfit:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="admin-body">
    <div class="admin-layout-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="admin-logo">Shanfix <span>Admin</span></div>
            <nav class="admin-nav">
                <a href="index.php" class="admin-nav-item active">
                    <i class="fas fa-chart-line"></i> <span>Insights</span>
                </a>
                <a href="clients.php" class="admin-nav-item">
                    <i class="fas fa-users"></i> <span>Clients</span>
                </a>
                <a href="categories.php" class="admin-nav-item"><i class="fas fa-tags"></i> <span>Categories</span></a>
                <a href="services.php" class="admin-nav-item"><i class="fas fa-concierge-bell"></i> <span>Services</span></a>
                <a href="products.php" class="admin-nav-item">
                    <i class="fas fa-box"></i> <span>Catalog</span>
                </a>
                <a href="orders.php" class="admin-nav-item">
                    <i class="fas fa-shopping-bag"></i> <span>Orders</span>
                </a>
                <a href="invoices.php" class="admin-nav-item">
                    <i class="fas fa-file-invoice"></i> <span>Billing</span>
                </a>
                <a href="receipts.php" class="admin-nav-item">
                    <i class="fas fa-receipt"></i> <span>Receipts</span>
                </a>
                <a href="portfolio.php" class="admin-nav-item"><i class="fas fa-briefcase"></i> <span>Portfolio</span></a>
            <a href="blog.php" class="admin-nav-item"><i class="fas fa-newspaper"></i> <span>Blog</span></a>
            <a href="adverts.php" class="admin-nav-item">
                    <i class="fas fa-ad"></i> <span>Adverts</span>
                </a>
                <a href="tickets.php" class="admin-nav-item">
                    <i class="fas fa-life-ring"></i> <span>Support</span>
                </a>
                <a href="messages.php" class="admin-nav-item">
                    <i class="fas fa-inbox"></i> <span>Inbox</span>
                    <span id="sidebarMsgBadge" style="display:none; background:#ef4444; color:#fff; font-size:0.65rem; font-weight:800; padding:2px 6px; border-radius:20px; margin-left:auto;"></span>
                </a>
                <div class="admin-nav-divider"></div>
                <a href="../index.php" class="admin-nav-item">
                    <i class="fas fa-external-link-alt"></i> <span>Live Site</span>
                </a>
            </nav>
            <div class="admin-sidebar-footer">
                <a href="login.php" class="admin-nav-item admin-footer-link" onclick="sessionStorage.clear()">
                    <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <header class="admin-header">
                <div class="admin-header-left">
                    <h1 class="admin-page-title">Performance Overview</h1>
                    <p class="admin-subtitle">Real-time business insights and analytics</p>
                </div>
                <div class="admin-user-profile">
                    <div class="admin-header-actions">
                        <button class="icon-btn"><i class="fas fa-bell"></i></button>
                        <button class="icon-btn"><i class="fas fa-search"></i></button>
                    </div>
                    <div class="admin-avatar">A</div>
                </div>
            </header>

            <section class="admin-content">
                <!-- Analytics Cards -->
                <div class="admin-stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">
                    <div class="admin-stat-card glass-card">
                        <div class="stat-main">
                            <div class="stat-icon-box bg-indigo">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div class="stat-details">
                                <span class="stat-label">Total Revenue</span>
                                <h2 id="stat_yearly_sales" class="stat-value">KES 0</h2>
                                <span id="trend_yearly" class="stat-trend text-low"><i class="fas fa-minus"></i> vs last year</span>
                            </div>
                        </div>
                    </div>

                    <div class="admin-stat-card glass-card">
                        <div class="stat-main">
                            <div class="stat-icon-box bg-emerald">
                                <i class="fas fa-chart-pie"></i>
                            </div>
                            <div class="stat-details">
                                <span class="stat-label">Monthly Sales</span>
                                <h2 id="stat_monthly_sales" class="stat-value">KES 0</h2>
                                <span id="trend_monthly" class="stat-trend text-low"><i class="fas fa-minus"></i> vs last month</span>
                            </div>
                        </div>
                    </div>

                    <div class="admin-stat-card glass-card">
                        <div class="stat-main">
                            <div class="stat-icon-box bg-amber">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-details">
                                <span class="stat-label">Outstanding</span>
                                <h2 id="stat_pending_balances" class="stat-value">KES 0</h2>
                                <span id="trend_outstanding" class="stat-trend text-low"><i class="fas fa-minus"></i> unpaid invoices</span>
                            </div>
                        </div>
                    </div>

                    <div class="admin-stat-card glass-card">
                        <div class="stat-main">
                            <div class="stat-icon-box bg-purple" style="background: rgba(139,92,246,0.15);">
                                <i class="fas fa-percentage" style="color: #a78bfa;"></i>
                            </div>
                            <div class="stat-details">
                                <span class="stat-label">Collection Rate</span>
                                <h2 id="stat_collection_rate" class="stat-value">0%</h2>
                                <span id="trend_collection" class="stat-trend text-low"><i class="fas fa-minus"></i> of invoices paid</span>
                            </div>
                        </div>
                    </div>

                    <div class="admin-stat-card glass-card">
                        <div class="stat-main">
                            <div class="stat-icon-box" style="background: rgba(6,182,212,0.15);">
                                <i class="fas fa-users" style="color: #06b6d4;"></i>
                            </div>
                            <div class="stat-details">
                                <span class="stat-label">Total Clients</span>
                                <h2 id="stat_total_clients" class="stat-value">0</h2>
                                <span id="trend_clients" class="stat-trend text-low"><i class="fas fa-minus"></i> new this month</span>
                            </div>
                        </div>
                    </div>

                    <div class="admin-stat-card glass-card">
                        <div class="stat-main">
                            <div class="stat-icon-box" style="background: rgba(16,185,129,0.15);">
                                <i class="fas fa-server" style="color: #10b981;"></i>
                            </div>
                            <div class="stat-details">
                                <span class="stat-label">Active Services</span>
                                <h2 id="stat_active_services" class="stat-value">0</h2>
                                <span id="stat_expiring_soon" class="stat-trend text-low"><i class="fas fa-minus"></i> expiring in 7 days</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Charts Area -->
                <div class="dashboard-grid">
                    <div class="admin-card chart-main-card glass-card">
                        <div class="card-header">
                            <h3>Revenue Trends</h3>
                            <div class="card-actions">
                                <select id="revenueRange" class="form-control-sm">
                                    <option value="6">Last 6 Months</option>
                                    <option value="12">Last 12 Months</option>
                                </select>
                            </div>
                        </div>
                        <div class="chart-container">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>

                    <div class="admin-card chart-side-card glass-card">
                        <div class="card-header">
                            <h3>Order Distribution</h3>
                        </div>
                        <div class="chart-container doughnut-container">
                            <canvas id="orderStatusChart"></canvas>
                        </div>
                        <div id="chartLegend" class="custom-legend"></div>
                    </div>
                </div>

                <!-- Secondary Charts -->
                <div class="dashboard-grid">
                    <div class="admin-card chart-main-card glass-card">
                        <div class="card-header">
                            <h3>Collection by Month</h3>
                            <span class="text-low" style="font-size:0.8rem;">Paid vs Outstanding (KES)</span>
                        </div>
                        <div class="chart-container">
                            <canvas id="collectionChart"></canvas>
                        </div>
                    </div>

                    <div class="admin-card chart-side-card glass-card">
                        <div class="card-header">
                            <h3>Support Overview</h3>
                        </div>
                        <div class="chart-container doughnut-container">
                            <canvas id="ticketSummaryChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Client Growth + Expiring Services -->
                <div class="dashboard-grid">
                    <div class="admin-card chart-main-card glass-card">
                        <div class="card-header">
                            <h3>Client Growth</h3>
                            <span class="text-low" style="font-size:0.8rem;">New signups — last 6 months</span>
                        </div>
                        <div class="chart-container">
                            <canvas id="clientGrowthChart"></canvas>
                        </div>
                    </div>

                    <div class="admin-card chart-side-card glass-card">
                        <div class="flex-between mb-20">
                            <h3>Expiring Services</h3>
                            <span class="text-low" style="font-size:0.8rem;">Due within 7 days</span>
                        </div>
                        <div id="expiringServicesList" style="max-height:280px; overflow-y:auto;">
                            <p class="text-low" style="text-align:center; padding:20px;">Loading...</p>
                        </div>
                        <div style="margin-top:16px; border-top:1px solid var(--glass-border); padding-top:16px;">
                            <button class="admin-btn admin-btn-secondary" style="width:100%;" onclick="sendRenewalReminders()">
                                <i class="fas fa-bell"></i> Send Renewal Reminders
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Bottom Row -->
                <div class="dashboard-grid grid-60-40">
                    <div class="admin-card glass-card">
                        <div class="flex-between mb-20">
                            <h3>Recent Transactions</h3>
                            <a href="invoices.php" class="link-btn">View All</a>
                        </div>
                        <div class="admin-table-container transparent-table">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Reference</th>
                                        <th>Client</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="dashboard_activity">
                                    <!-- Populated via JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="admin-card glass-card">
                        <h3>Quick Reports</h3>
                        <p class="text-low mb-20">Generate on-demand financial statements</p>
                        
                        <div class="report-box mb-20">
                            <label>Monthly Audit</label>
                            <div class="flex-gap mt-10">
                                <select id="statement_month" class="form-control-sm">
                                    <option value="0">January</option>
                                    <option value="1">February</option>
                                    <option value="2">March</option>
                                    <option value="3">April</option>
                                    <option value="4">May</option>
                                    <option value="5">June</option>
                                    <option value="6">July</option>
                                    <option value="7">August</option>
                                    <option value="8">September</option>
                                    <option value="9">October</option>
                                    <option value="10">November</option>
                                    <option value="11">December</option>
                                </select>
                                <button class="admin-btn-sm admin-btn-secondary" onclick="generateStatement('month')">
                                    <i class="fas fa-download"></i>
                                </button>
                            </div>
                        </div>

                        <div class="report-box">
                            <label>Annual Statement</label>
                            <div class="flex-gap mt-10">
                                <select id="statement_year" class="form-control-sm">
                                    <option value="2026">2026</option>
                                    <option value="2025">2025</option>
                                </select>
                                <button class="admin-btn-sm admin-btn-primary" onclick="generateStatement('year')">
                                    <i class="fas fa-file-pdf"></i> Generate
                                </button>
                            </div>
                        </div>

                        <div class="report-box mt-20" style="border-top: 1px solid var(--glass-border); padding-top: 20px;">
                            <label>Open Tickets</label>
                            <p class="text-low mt-5" style="font-size:0.8rem;"><span id="stat_open_tickets_report">0</span> support tickets require attention</p>
                            <a href="tickets.php" class="admin-btn admin-btn-secondary mt-10" style="width:100%; display:flex; align-items:center; justify-content:center; gap:8px; text-decoration:none;">
                                <i class="fas fa-life-ring"></i> View Support Queue
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Financial Statement Template (Hidden) -->
    <div id="statementTemplateContainer" class="hidden-template">
        <div id="statementTemplate" class="invoice-preview-template">
            <div class="invoice-header">
                <div class="invoice-logo-container">
                    <img src="../assets/shanfix-logo.png" alt="Shanfix Technology">
                </div>
                <h3 class="invoice-id-header" id="st_title">FINANCIAL STATEMENT</h3>
            </div>
            
            <div class="invoice-meta-grid">
                <div class="invoice-meta-col">
                    <h4>Provider</h4>
                    <p><strong>Shanfix Technology Limited</strong></p>
                    <p>Financial Report System</p>
                </div>
                <div class="invoice-meta-col" style="text-align: right;">
                    <h4 class="border-transparent">Report Details</h4>
                    <p>Period: <strong id="st_period">February 2026</strong></p>
                    <p>Generated: <span id="st_gen_date">February 16, 2026</span></p>
                </div>
            </div>

            <table class="invoice-items-table">
                <thead>
                    <tr>
                        <th class="w-15">Date</th>
                        <th class="w-20">Invoice #</th>
                        <th>Client / Description</th>
                        <th class="w-15">Status</th>
                        <th class="w-15">Amount</th>
                    </tr>
                </thead>
                <tbody id="st_items_body">
                    <!-- Transactions here -->
                </tbody>
            </table>

            <div class="mt-10 border-top-thick pt-10">
                <table class="invoice-summary-table st-summary-table">
                    <tr>
                        <td class="label-cell">TOTAL SALES</td>
                        <td id="st_total_sales" style="text-align: right; font-weight: 700;">KES 0.00</td>
                    </tr>
                    <tr>
                        <td class="label-cell">TOTAL PAID</td>
                        <td id="st_total_paid" class="text-right font-700 st-summary-paid">KES 0.00</td>
                    </tr>
                    <tr>
                        <td class="label-cell">PENDING BALANCE</td>
                        <td id="st_pending_balance" class="text-right st-summary-pending">KES 0.00</td>
                    </tr>
                </table>
            </div>

            <div class="invoice-footer" style="margin-top: auto;">
                <p>Generated by Shanfix Admin Panel. Confidential Financial Document.</p>
            </div>
        </div>
    </div>

    <!-- Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="../admin.js?v=13"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof initDashboard === 'function') initDashboard();
        });
    </script>
</body>
</html>
