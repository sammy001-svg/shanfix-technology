<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing & Invoices - Shanfix Admin</title>
    <link rel="stylesheet" href="../admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .invoice-builder-grid {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 25px;
        }
        .item-list-table { width: 100%; border-collapse: collapse; }
        .item-list-table th { text-align: left; padding: 12px; color: var(--text-low); font-size: 0.8rem; text-transform: uppercase; border-bottom: 1px solid var(--glass-border); }
        .item-list-table td { padding: 12px; border-bottom: 1px solid var(--glass-border); }
        
        .totals-box {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 20px;
        }
        .total-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 0.95rem; }
        .total-row.grand-total { border-top: 1px solid var(--glass-border); padding-top: 15px; margin-top: 15px; font-weight: 800; font-size: 1.2rem; color: var(--p); }
        
        .client-type-toggle {
            display: flex;
            background: rgba(255,255,255,0.05);
            padding: 5px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .toggle-btn {
            flex: 1;
            padding: 10px;
            border: none;
            background: transparent;
            color: var(--text-low);
            cursor: pointer;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s;
        }
        .toggle-btn.active { background: var(--p); color: white; }
        
        .invoice-row-action { color: #ef4444; cursor: pointer; transition: transform 0.2s; }
        .invoice-row-action:hover { transform: scale(1.2); }
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
            <a href="invoices.php" class="admin-nav-item active"><i class="fas fa-file-invoice-dollar"></i> <span>Billing</span></a>
            <a href="receipts.php" class="admin-nav-item"><i class="fas fa-receipt"></i> <span>Receipts</span></a>
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
            <h1 class="admin-page-title">Billing Control Center</h1>
            <div class="admin-header-actions">
                <button class="admin-btn admin-btn-primary" onclick="openInvoiceModal()">
                    <i class="fas fa-plus"></i> Generate New Invoice
                </button>
            </div>
        </header>

        <section class="admin-content">
            <!-- Billing Stats -->
            <div class="admin-stats-grid">
                <div class="admin-stat-card">
                    <div class="stat-icon" style="background: rgba(99, 102, 241, 0.1); color: var(--p);"><i class="fas fa-file-invoice"></i></div>
                    <div>
                        <div class="stat-label">Total Invoiced</div>
                        <div class="stat-value" id="stat_total_invoiced">KES 0</div>
                    </div>
                </div>
                <div class="admin-stat-card">
                    <div class="stat-icon" style="background: rgba(34, 197, 94, 0.1); color: #22c55e;"><i class="fas fa-check-double"></i></div>
                    <div>
                        <div class="stat-label">Total Collected</div>
                        <div class="stat-value" id="stat_total_collected">KES 0</div>
                    </div>
                </div>
                <div class="admin-stat-card">
                    <div class="stat-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;"><i class="fas fa-exclamation-circle"></i></div>
                    <div>
                        <div class="stat-label">Pending Payments</div>
                        <div class="stat-value" id="stat_total_pending">KES 0</div>
                    </div>
                </div>
            </div>

            <div class="admin-card">
                <div class="flex-between mb-20">
                    <h2 class="section-title">Invoice Registry</h2>
                    <div class="flex-align-center gap-10">
                        <input type="text" class="form-control" placeholder="Search invoices..." style="width: 250px;">
                    </div>
                </div>
                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>REF #</th>
                                <th>Client Name</th>
                                <th>Date Issued</th>
                                <th>Due Date</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="invoiceTableBody">
                            <!-- Invoices loaded via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>

    <!-- Invoice Modal -->
    <div id="invoiceModal" class="admin-modal">
        <div class="admin-modal-content" style="max-width: 1100px;">
            <div class="admin-modal-header">
                <h3 class="admin-modal-title">Intelligent Billing Assistant</h3>
                <span class="admin-modal-close" onclick="closeInvoiceModal()">&times;</span>
            </div>
            <div class="admin-modal-body">
                <form id="invoiceForm">
                    <div class="invoice-builder-grid">
                        <div class="builder-main">
                            <div class="client-type-toggle">
                                <button type="button" class="toggle-btn active" onclick="setClientType('registered')">Registered Client</button>
                                <button type="button" class="toggle-btn" onclick="setClientType('guest')">Guest Client</button>
                            </div>

                            <!-- Registered Client Inputs -->
                            <div id="registeredClientSection">
                                <div class="form-group">
                                    <label>Select Registered Client</label>
                                    <select id="inv_client_id" class="form-control admin-select-custom">
                                        <!-- Populated via JS -->
                                    </select>
                                </div>
                            </div>

                            <!-- Guest Client Inputs -->
                            <div id="guestClientSection" style="display: none;">
                                <div class="form-grid">
                                    <div class="form-group"><label>Full Name</label><input type="text" id="inv_guest_name" class="form-control"></div>
                                    <div class="form-group"><label>Email Address</label><input type="email" id="inv_guest_email" class="form-control"></div>
                                    <div class="form-group"><label>Phone Number</label><input type="text" id="inv_guest_phone" class="form-control"></div>
                                </div>
                            </div>

                            <div class="mt-30">
                                <div class="flex-between mb-15">
                                    <h4 class="m-0">Billed Items & Services</h4>
                                    <button type="button" class="admin-btn admin-btn-secondary" onclick="addInvoiceItemRow()" style="padding: 8px 15px;">
                                        <i class="fas fa-plus"></i> Add Item
                                    </button>
                                </div>
                                <table class="item-list-table" id="invoiceItemsTable">
                                    <thead>
                                        <tr>
                                            <th>Description</th>
                                            <th style="width: 100px;">Qty</th>
                                            <th style="width: 150px;">Unit Price</th>
                                            <th style="width: 150px;">Total</th>
                                            <th style="width: 50px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="invoiceItemsBody">
                                        <!-- Item rows go here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="builder-side">
                            <div class="totals-box">
                                <div class="form-group">
                                    <label>Payment Terms</label>
                                    <select id="inv_terms" class="form-control admin-select-custom">
                                        <option value="70% Prior, 30% Upon Delivery">70% Prior, 30% Delivery</option>
                                        <option value="100% Prior Payment">100% Prior Payment</option>
                                        <option value="50% Prior, 50% Upon Delivery">50% Prior, 50% Delivery</option>
                                        <option value="Net 30 Days">Net 30 Days</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Due Date</label>
                                    <input type="date" id="inv_due_date" class="form-control">
                                </div>
                                
                                <div class="total-row"><span>Subtotal</span><span id="txt_subtotal">KES 0</span></div>
                                <div class="total-row">
                                    <span>Tax (16% VAT)</span>
                                    <input type="checkbox" id="chk_vat" checked onchange="calculateTotals()" style="width: 18px; height: 18px;">
                                </div>
                                <div class="total-row"><span>Tax Amount</span><span id="txt_tax">KES 0</span></div>
                                <div class="total-row grand-total"><span>Grand Total</span><span id="txt_grand_total">KES 0</span></div>
                                
                                <button type="submit" class="admin-btn admin-btn-primary w-100 mt-20" style="padding: 15px;">
                                    <i class="fas fa-file-export"></i> Finalize & Generate
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div id="previewModal" class="admin-modal">
        <div class="admin-modal-content" style="max-width: 900px;">
            <div class="admin-modal-header">
                <h3 class="admin-modal-title">Document Preview</h3>
                <span class="admin-modal-close" onclick="closePreviewModal()">&times;</span>
            </div>
            <div class="admin-modal-body" id="previewArea" style="background: #f8fafc; padding: 40px;">
                <!-- PDF Preview -->
            </div>
            <div class="admin-modal-footer">
                <button class="admin-btn admin-btn-secondary" onclick="closePreviewModal()">Close</button>
                <button class="admin-btn admin-btn-primary" id="downloadPdfBtn">
                    <i class="fas fa-download"></i> Download PDF
                </button>
            </div>
        </div>
    </div>

    <div id="hiddenTemplate" style="display: none;">
        <!-- Standard PDF Template -->
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="../admin.js?v=13"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof initBillingPage === 'function') initBillingPage();
        });
    </script>
</body>
</html>
