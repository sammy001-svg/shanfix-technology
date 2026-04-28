<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoices - Shanfix Admin</title>
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
                <a href="index.php" class="admin-nav-item">
                    <i class="fas fa-home"></i> <span>Dashboard</span>
                </a>
                <a href="products.php" class="admin-nav-item">
                    <i class="fas fa-box"></i> <span>Products & Categories</span>
                </a>
                <a href="invoices.php" class="admin-nav-item active">
                    <i class="fas fa-file-invoice"></i> <span>Invoices</span>
                </a>
                <a href="receipts.php" class="admin-nav-item">
                    <i class="fas fa-receipt"></i> <span>Receipts</span>
                </a>
                <a href="adverts.php" class="admin-nav-item">
                    <i class="fas fa-ad"></i> <span>Adverts</span>
                </a>
                <div class="admin-nav-divider"></div>
                <a href="../index.php" class="admin-nav-item">
                    <i class="fas fa-external-link-alt"></i> <span>View Portal</span>
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
                <h1 class="admin-page-title">Invoice Generation</h1>
                <div class="admin-user-profile">
                    <span>Welcome, Admin</span>
                    <div class="admin-avatar">A</div>
                </div>
            </header>

            <section class="admin-content">
                <div class="admin-card">
                    <h2 class="mb-20">Generate New Invoice</h2>
                    <form id="invoiceForm">
                        <div class="banners-grid">
                            <div class="form-group">
                                <label for="client_name">Client Name</label>
                                <input type="text" id="client_name" class="form-control" required placeholder="Customer Name">
                            </div>
                            <div class="form-group">
                                <label for="client_phone">Client Phone</label>
                                <input type="text" id="client_phone" class="form-control" required placeholder="+254 7XX XXX XXX">
                            </div>
                            <div class="form-group">
                                <label for="client_email">Client Email</label>
                                <input type="email" id="client_email" class="form-control" required placeholder="customer@email.com">
                            </div>
                            <div class="form-group">
                                <label for="delivery_time">Time of Delivery</label>
                                <input type="text" id="delivery_time" class="form-control" required placeholder="e.g. 3 Working Days">
                            </div>
                        </div>

                        <div id="invoiceItemsContainer" class="mt-20">
                            <h4 class="mb-15">Items & Services</h4>
                            <div class="invoice-item-row mb-15">
                                <div class="form-group" style="flex: 0 0 100px;">
                                    <label>Qty</label>
                                    <input type="number" class="form-control item-qty" value="1" min="1" required title="Quantity" placeholder="1">
                                </div>
                                <div class="form-group" style="flex: 1;">
                                    <label>Description</label>
                                    <input type="text" class="form-control item-desc" required placeholder="Service/Product name">
                                </div>
                                <div class="form-group" style="flex: 0 0 200px;">
                                    <label>Unit Price (KES)</label>
                                    <input type="number" class="form-control item-price" required placeholder="0.00">
                                </div>
                            </div>
                        </div>
                        
                        <div class="admin-form-actions mt-20">
                            <button type="button" class="admin-btn admin-btn-secondary" onclick="addInvoiceItemRow()">
                                <i class="fas fa-plus"></i> Add Item
                            </button>
                            <button type="submit" class="admin-btn admin-btn-primary">
                                <i class="fas fa-magic"></i> Generate & Preview Invoice
                            </button>
                        </div>
                    </form>
                </div>

                <div class="admin-card">
                    <h2 class="mb-20">Recent Invoices</h2>
                    <div class="admin-table-container">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Client</th>
                                    <th>Item</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="invoiceTableBody">
                                <tr>
                                    <td>#SF-1001</td>
                                    <td>John Doe</td>
                                    <td>Business Cards</td>
                                    <td>KES 2,500</td>
                                    <td><span class="status-badge status-paid">Paid</span></td>
                                </tr>
                                <tr>
                                    <td>#SF-1002</td>
                                    <td>Apex Corp</td>
                                    <td>Company Profiles</td>
                                    <td>KES 15,000</td>
                                    <td><span class="status-badge status-pending">Pending</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Invoice View Modal -->
    <div id="invoiceModal" class="admin-modal">
        <div class="admin-modal-content">
            <div class="admin-modal-header">
                <h3 class="admin-modal-title">Invoice Preview</h3>
                <span class="admin-modal-close" onclick="closeInvoiceModal()">&times;</span>
            </div>
            <div class="admin-modal-body">
                <div id="invoiceViewArea" class="invoice-paper-preview">
                    <!-- Standardized template will be injected here -->
                </div>
            </div>
            <div class="admin-modal-footer">
                <button class="admin-btn admin-btn-secondary" onclick="closeInvoiceModal()">Close</button>
                <button id="downloadBtnInModal" class="admin-btn admin-btn-primary">
                    <i class="fas fa-download"></i> Download PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Hidden Template for PDF Generation -->
    <div id="invoiceTemplateContainer" class="hidden-template">
        <div id="invoiceTemplate" class="invoice-preview-template">
            <div class="invoice-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #eee; padding-bottom: 2rem; margin-bottom: 2rem;">
                <div class="invoice-logo-container">
                    <img src="../assets/shanfix-logo.png" alt="Shanfix Technology" style="height: 60px;">
                </div>
                <h3 class="invoice-id-header" style="margin: 0; color: #1e293b;">Invoice: <span id="tpl_inv_id">000350/2026</span></h3>
            </div>

            <div class="invoice-meta-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; margin-bottom: 3rem;">
                <div class="invoice-meta-col">
                    <h4 style="color: #64748b; margin-bottom: 1rem; border-bottom: 1px solid #eee; padding-bottom: 0.5rem;">From</h4>
                    <p><strong>Shanfix Technology Limited</strong></p>
                    <p>Email: info@shanfixtechnology.com</p>
                    <p>Business No: +254 751 869 165</p>
                </div>
                <div class="invoice-meta-col">
                    <h4 style="color: #64748b; margin-bottom: 1rem; border-bottom: 1px solid #eee; padding-bottom: 0.5rem;">Delivered To</h4>
                    <p><strong id="tpl_client_name">Client Name</strong></p>
                    <p id="tpl_client_phone">+254 7XX XXX XXX</p>
                    <p id="tpl_client_email">client@email.com</p>
                </div>
            </div>

            <table class="invoice-info-table" style="width: 100%; border-collapse: collapse; margin-bottom: 3rem;">
                <thead>
                    <tr style="background: #f8fafc;">
                        <th style="padding: 1rem; border: 1px solid #e2e8f0; text-align: left;">Date</th>
                        <th style="padding: 1rem; border: 1px solid #e2e8f0; text-align: left;">Requester</th>
                        <th style="padding: 1rem; border: 1px solid #e2e8f0; text-align: left;">Delivery Time</th>
                        <th style="padding: 1rem; border: 1px solid #e2e8f0; text-align: left;">Terms of Payment</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td id="tpl_date" style="padding: 1rem; border: 1px solid #e2e8f0;">February 16th 2026</td>
                        <td id="tpl_requester" style="padding: 1rem; border: 1px solid #e2e8f0;">Requester Name</td>
                        <td id="tpl_delivery" style="padding: 1rem; border: 1px solid #e2e8f0;">1 days</td>
                        <td id="tpl_terms" style="padding: 1rem; border: 1px solid #e2e8f0;">70% Prior, 30% Upon Delivery</td>
                    </tr>
                </tbody>
            </table>

            <table class="invoice-items-table" style="width: 100%; border-collapse: collapse; margin-bottom: 3rem;">
                <thead>
                    <tr style="background: #f8fafc;">
                        <th style="padding: 1rem; border: 1px solid #e2e8f0; text-align: left; width: 10%;">Qty</th>
                        <th style="padding: 1rem; border: 1px solid #e2e8f0; text-align: left;">Description</th>
                        <th style="padding: 1rem; border: 1px solid #e2e8f0; text-align: left; width: 20%;">Unit Price</th>
                        <th style="padding: 1rem; border: 1px solid #e2e8f0; text-align: left; width: 20%;">Total</th>
                    </tr>
                </thead>
                <tbody id="tpl_items_body">
                    <!-- Dynamic items here -->
                </tbody>
            </table>

            <div style="display: flex; justify-content: flex-end; margin-bottom: 4rem;">
                <div style="width: 300px; padding: 2rem; background: #f8fafc; border-radius: 8px; text-align: right;">
                    <p style="margin: 0; color: #64748b; font-size: 0.9rem;">TOTAL AMOUNT</p>
                    <h2 style="margin: 0.5rem 0 0; color: #1e293b;">KES <span id="tpl_total_amount">0.00</span></h2>
                </div>
            </div>

            <div class="invoice-footer" style="text-align: center; color: #94a3b8; font-size: 0.9rem; border-top: 1px solid #eee; pt-2rem;">
                <p>Thank you for choosing Shanfix Technology!</p>
                <p><strong>TILL NO. 5698666</strong></p>
            </div>
        </div>
    </div>

    <!-- Payment Update Modal -->
    <div id="paymentModal" class="admin-modal">
        <div class="admin-modal-content" style="max-width: 500px;">
            <div class="admin-modal-header">
                <h3 class="admin-modal-title">Update Payment Status</h3>
                <span class="admin-modal-close" onclick="closePaymentModal()">&times;</span>
            </div>
            <div class="admin-modal-body">
                <div class="payment-info-box mb-20" style="background: rgba(255,255,255,0.02); padding: 1.5rem; border-radius: 12px; border: 1px solid var(--glass-border);">
                    <p>Invoice: <strong id="pay_inv_id">#SF-1001</strong></p>
                    <p>Total Amount: <strong id="pay_total">KES 0.00</strong></p>
                </div>
                <div class="form-group">
                    <label>Amount Paid (KES)</label>
                    <input type="number" id="pay_amount_input" class="form-control" placeholder="Enter amount paid" title="Amount Paid">
                </div>
                <div class="payment-calc mt-15">
                    <p>Current Balance: <strong id="pay_balance" style="color: var(--accent);">KES 0.00</strong></p>
                </div>
            </div>
            <div class="admin-modal-footer">
                <button class="admin-btn admin-btn-secondary" onclick="closePaymentModal()">Cancel</button>
                <button class="admin-btn admin-btn-primary" onclick="savePaymentUpdate()">
                    <i class="fas fa-save"></i> Save Payment
                </button>
            </div>
        </div>
    </div>

    <!-- PDF Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="../admin.js?v=10"></script>
</body>
</html>
