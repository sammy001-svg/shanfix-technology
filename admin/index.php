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
                <a href="adverts.php" class="admin-nav-item">
                    <i class="fas fa-ad"></i> <span>Adverts</span>
                </a>
                <a href="tickets.php" class="admin-nav-item">
                    <i class="fas fa-life-ring"></i> <span>Support</span>
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
                <div class="admin-stats-grid">
                    <div class="admin-stat-card glass-card">
                        <div class="stat-main">
                            <div class="stat-icon-box bg-indigo">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div class="stat-details">
                                <span class="stat-label">Total Revenue</span>
                                <h2 id="stat_yearly_sales" class="stat-value">KES 0</h2>
                                <span class="stat-trend text-success"><i class="fas fa-arrow-up"></i> 12.5% vs last year</span>
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
                                <span class="stat-trend text-success"><i class="fas fa-arrow-up"></i> 8.2% vs last month</span>
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
                                <span class="stat-trend text-danger"><i class="fas fa-arrow-up"></i> 2.4% increase</span>
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
