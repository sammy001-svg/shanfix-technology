<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipts Ledger - Shanfix Admin</title>
    <link rel="stylesheet" href="../admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .receipt-card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        .receipt-mini-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 25px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .receipt-mini-card:hover { transform: translateY(-5px); border-color: #22c55e; box-shadow: 0 10px 30px rgba(34, 197, 94, 0.1); }
        .receipt-mini-card::before {
            content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: #22c55e;
        }
        
        .rec-header { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .rec-ref { font-family: 'Outfit', sans-serif; font-weight: 800; color: var(--text-main); font-size: 1.1rem; }
        .rec-date { color: var(--text-low); font-size: 0.85rem; }
        
        .rec-client { margin-bottom: 15px; }
        .rec-client-name { font-weight: 700; color: var(--text-main); display: block; }
        .rec-client-email { color: var(--text-low); font-size: 0.8rem; }
        
        .rec-amount-box {
            background: rgba(34, 197, 94, 0.05);
            padding: 15px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .rec-label { font-size: 0.75rem; color: #166534; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; }
        .rec-value { font-size: 1.4rem; font-weight: 800; color: #166534; }
        
        .rec-actions { display: flex; gap: 10px; }
        .btn-rec-action {
            flex: 1;
            padding: 10px;
            border-radius: 10px;
            border: 1px solid var(--glass-border);
            background: transparent;
            color: var(--text-main);
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s;
        }
        .btn-rec-action:hover { background: var(--p); color: white; border-color: var(--p); }
        
        .ledger-summary {
            display: flex;
            gap: 30px;
            background: var(--card-bg);
            padding: 30px;
            border-radius: 24px;
            border: 1px solid var(--card-border);
            margin-bottom: 40px;
        }
        .summary-item { flex: 1; }
        .summary-label { color: var(--text-low); font-size: 0.9rem; margin-bottom: 5px; display: block; }
        .summary-val { font-size: 1.8rem; font-weight: 800; font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="admin-body">
    <aside class="admin-sidebar">
        <div class="admin-logo">Shanfix <span>Admin</span></div>
        <nav class="admin-nav">
            <a href="index.php" class="admin-nav-item"><i class="fas fa-chart-line"></i> <span>Insights</span></a>
            <a href="clients.php" class="admin-nav-item"><i class="fas fa-users"></i> <span>Clients</span></a>
            <a href="categories.php" class="admin-nav-item"><i class="fas fa-tags"></i> <span>Categories</span></a>
            <a href="products.php" class="admin-nav-item"><i class="fas fa-box"></i> <span>Catalog</span></a>
            <a href="orders.php" class="admin-nav-item"><i class="fas fa-shopping-bag"></i> <span>Orders</span></a>
            <a href="invoices.php" class="admin-nav-item"><i class="fas fa-file-invoice-dollar"></i> <span>Billing</span></a>
            <a href="receipts.php" class="admin-nav-item active"><i class="fas fa-receipt"></i> <span>Receipts</span></a>
            <a href="tickets.php" class="admin-nav-item"><i class="fas fa-life-ring"></i> <span>Support</span></a>
            <div class="admin-nav-divider"></div>
            <a href="../index.php" class="admin-nav-item"><i class="fas fa-external-link-alt"></i> <span>Live Site</span></a>
        </nav>
        <div class="admin-sidebar-footer">
            <a href="login.php" class="admin-nav-item admin-footer-link" onclick="sessionStorage.clear()">
                <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
            </a>
        </div>
    </aside>

    <main class="admin-main">
        <header class="admin-header">
            <h1 class="admin-page-title">Receipts Ledger</h1>
            <div class="admin-header-actions">
                <button class="admin-btn admin-btn-secondary" onclick="exportLedger()">
                    <i class="fas fa-file-excel"></i> Export CSV
                </button>
            </div>
        </header>

        <section class="admin-content">
            <div class="ledger-summary">
                <div class="summary-item">
                    <span class="summary-label">Total Revenue Collected</span>
                    <div class="summary-val" id="total_revenue" style="color: #22c55e;">KES 0</div>
                </div>
                <div class="summary-item" style="border-left: 1px solid var(--glass-border); padding-left: 30px;">
                    <span class="summary-label">Receipts Issued</span>
                    <div class="summary-val" id="receipt_count">0</div>
                </div>
                <div class="summary-item" style="border-left: 1px solid var(--glass-border); padding-left: 30px;">
                    <span class="summary-label">Last Collection</span>
                    <div class="summary-val" id="last_collection_date" style="font-size: 1.2rem; padding-top: 10px;">--</div>
                </div>
            </div>

            <div class="flex-between mb-20">
                <h2 class="section-title">Verified Transactions</h2>
                <div class="flex-align-center gap-10">
                    <select class="form-control" style="width: 200px;">
                        <option>All Time</option>
                        <option>This Month</option>
                        <option>Last 30 Days</option>
                    </select>
                </div>
            </div>

            <div class="receipt-card-grid" id="receiptsGrid">
                <!-- Receipt cards loaded here -->
            </div>
        </section>
    </main>

    <!-- Receipt Preview Modal -->
    <div id="receiptModal" class="admin-modal">
        <div class="admin-modal-content" style="max-width: 850px;">
            <div class="admin-modal-header">
                <h3 class="admin-modal-title">Official Electronic Receipt</h3>
                <span class="admin-modal-close" onclick="closeReceiptModal()">&times;</span>
            </div>
            <div class="admin-modal-body" id="receiptPreviewArea" style="background: #f1f5f9; padding: 40px;">
                <!-- Generated Template -->
            </div>
            <div class="admin-modal-footer">
                <button class="admin-btn admin-btn-secondary" onclick="closeReceiptModal()">Close</button>
                <button class="admin-btn admin-btn-primary" id="downloadReceiptBtn">
                    <i class="fas fa-print"></i> Download & Print
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="../admin.js?v=13"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof initReceiptsPage === 'function') initReceiptsPage();
        });
    </script>
</body>
</html>
