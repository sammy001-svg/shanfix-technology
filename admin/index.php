<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Shanfix Admin</title>
    <link rel="stylesheet" href="../admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="admin-body">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="admin-logo">Shanfix Admin</div>
        <nav class="admin-nav">
            <a href="index.php" class="admin-nav-item active">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="products.php" class="admin-nav-item">
                <i class="fas fa-box"></i> Products
            </a>
            <a href="invoices.php" class="admin-nav-item">
                <i class="fas fa-file-invoice"></i> Invoices
            </a>
            <a href="receipts.php" class="admin-nav-item">
                <i class="fas fa-receipt"></i> Receipts
            </a>
            <a href="adverts.php" class="admin-nav-item">
                <i class="fas fa-ad"></i> Adverts
            </a>
        </nav>
        <div class="admin-sidebar-footer">
            <a href="login.php" class="admin-nav-item admin-footer-link" onclick="sessionStorage.clear()">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
        <header class="admin-header">
            <h1 class="admin-page-title">Dashboard Overview</h1>
            <div class="admin-user-profile">
                <span>Welcome, Admin</span>
                <div class="admin-avatar">A</div>
            </div>
        </header>

        <section class="admin-content">
            <div class="admin-stats-grid">
                <div class="admin-stat-card">
                    <div class="stat-icon stat-icon-sales">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <div class="stat-info">
                        <span id="stat_monthly_sales" class="stat-value">KES 0</span>
                        <span class="stat-label">Monthly Sales</span>
                    </div>
                </div>
                <div class="admin-stat-card">
                    <div class="stat-icon stat-icon-pending">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <span id="stat_pending_balances" class="stat-value">KES 0</span>
                        <span class="stat-label">Pending Balances</span>
                    </div>
                </div>
                <div class="admin-stat-card">
                    <div class="stat-icon stat-icon-yearly">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-info">
                        <span id="stat_yearly_sales" class="stat-value">KES 0</span>
                        <span class="stat-label">Total Yearly Sales</span>
                    </div>
                </div>
            </div>

            <div class="banners-grid mb-20">
                <div class="admin-card">
                    <h2 class="mb-15">Monthly Statement</h2>
                    <div class="flex-end-gap">
                        <div class="form-group mb-0">
                            <label>Select Month</label>
                            <select id="statement_month" class="form-control" title="Select Month">
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
                        </div>
                        <button class="admin-btn admin-btn-secondary" onclick="generateStatement('month')">
                            <i class="fas fa-download"></i> Download Monthly
                        </button>
                    </div>
                </div>
                <div class="admin-card">
                    <h2 class="mb-15">Yearly Statement</h2>
                    <div class="flex-end-gap">
                        <div class="form-group mb-0">
                            <label>Select Year</label>
                            <select id="statement_year" class="form-control" title="Select Year">
                                <option value="2026">2026</option>
                                <option value="2025">2025</option>
                                <option value="2024">2024</option>
                             </select>
                        </div>
                        <button class="admin-btn admin-btn-primary" onclick="generateStatement('year')">
                            <i class="fas fa-download"></i> Download Yearly
                        </button>
                    </div>
                </div>
            </div>

            <div class="admin-card">
                <h2 class="mb-15">Recent Activity</h2>
                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Activity</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="dashboard_activity">
                            <!-- Populated via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>

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

    <!-- PDF Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="../admin.js?v=2"></script>
</body>
</html>
