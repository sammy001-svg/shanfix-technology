<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoices - Shanfix Admin</title>
    <link rel="stylesheet" href="../admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="admin-body">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="admin-logo">Shanfix Admin</div>
        <nav class="admin-nav">
            <a href="index.php" class="admin-nav-item">
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
                <h2 class="mb-15">Generate New Invoice</h2>
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
                        <h4 class="mb-10">Items & Services</h4>
                        <div class="invoice-item-row mb-10">
                            <div class="form-group">
                                <label>Qty</label>
                                <input type="number" class="form-control item-qty" value="1" min="1" required title="Quantity" placeholder="1">
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <input type="text" class="form-control item-desc" required placeholder="Service/Product name">
                            </div>
                            <div class="form-group">
                                <label>Unit Price (KES)</label>
                                <input type="number" class="form-control item-price" required placeholder="0.00">
                            </div>
                        </div>
                    </div>
                    
                    <div class="admin-form-actions mt-15">
                        <button type="button" class="admin-btn" onclick="addInvoiceItemRow()">
                            <i class="fas fa-plus"></i> Add Item
                        </button>
                        <button type="submit" class="admin-btn admin-btn-secondary">
                            <i class="fas fa-magic"></i> Generate & Preview Invoice
                        </button>
                    </div>
                </form>
            </div>

            <div class="admin-card">
                <h2 class="mb-15">Recent Invoices</h2>
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
                                <td><span class="text-primary">Paid</span></td>
                            </tr>
                            <tr>
                                <td>#SF-1002</td>
                                <td>Apex Corp</td>
                                <td>Company Profiles</td>
                                <td>KES 15,000</td>
                                <td><span class="text-secondary">Pending</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>

    <!-- Invoice View Modal -->
    <div id="invoiceModal" class="admin-modal">
        <div class="admin-modal-content">
            <div class="admin-modal-header">
                <h3>Invoice Preview</h3>
                <span class="admin-modal-close" onclick="closeInvoiceModal()">&times;</span>
            </div>
            <div class="admin-modal-body">
                <div id="invoiceViewArea" class="invoice-paper-preview">
                    <!-- Standardized template will be injected here -->
                </div>
            </div>
            <div class="admin-modal-footer">
                <button class="admin-btn" onclick="closeInvoiceModal()">Close</button>
                <button id="downloadBtnInModal" class="admin-btn admin-btn-secondary">
                    <i class="fas fa-download"></i> Download PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Hidden Template for PDF Generation -->
    <div id="invoiceTemplateContainer">
        <div id="invoiceTemplate" class="invoice-preview-template">
            <div class="invoice-header">
                <div class="invoice-logo-container">
                    <img src="../assets/shanfix-logo.png" alt="Shanfix Technology">
                </div>
                <h3 class="invoice-id-header">Invoice: <span id="tpl_inv_id">000350/2026</span></h3>
            </div>

            <div class="invoice-meta-grid">
                <div class="invoice-meta-col text-right">
                    <h4 class="border-transparent">Report Details</h4>
                    <p><strong>Shanfix Technology Limited</strong></p>
                    <p>Email: info@shanfixtechnology.com</p>
                    <p>Business No: +254 751 869 165</p>
                </div>
                <div class="invoice-meta-col">
                    <h4>Delivered To</h4>
                    <p><strong id="tpl_client_name">Client Name</strong></p>
                    <p id="tpl_client_phone">+254 7XX XXX XXX</p>
                    <p id="tpl_client_email">client@email.com</p>
                </div>
            </div>

            <table class="invoice-info-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Requester</th>
                        <th>Delivery Time</th>
                        <th>Terms of Payment</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td id="tpl_date">February 16th 2026</td>
                        <td id="tpl_requester">Requester Name</td>
                        <td id="tpl_delivery">1 days</td>
                        <td id="tpl_terms">70% Prior<br>30% Upon Delivery</td>
                    </tr>
                </tbody>
            </table>

            <table class="invoice-items-table">
                <thead>
                    <tr>
                        <th class="w-10">Qty</th>
                        <th>Description</th>
                        <th class="w-20">Unit Price</th>
                        <th class="w-20">Total</th>
                    </tr>
                </thead>
 Elisa                <tbody id="tpl_items_body">
                    <!-- Dynamic items here -->
                </tbody>
            </table>

            <table class="invoice-summary-table">
                <tr class="invoice-summary-row">
                    <td id="tpl_footer_text" class="invoice-summary-details">
                        Shanfix Technology<br>
                        Email: info@shanfixtechnology.com<br>
                        Business No: +254 751 869 165<br>
                        <strong>TILL NO. 5698666</strong>
                    </td>
                    <td class="total-cell invoice-total-display">
                        <div class="total-label-small">TOTAL</div>
                        KES <span id="tpl_total_amount">0.00</span>
                    </td>
                </tr>
            </table>

            <div class="invoice-footer">
                <p>Thank you for choosing Shanfix Technology!</p>
            </div>
        </div>
    </div>

    <!-- Payment Update Modal -->
    <div id="paymentModal" class="admin-modal">
        <div class="admin-modal-content max-w-500">
            <div class="admin-modal-header">
                <h3>Update Payment Status</h3>
                <span class="close-modal" onclick="closePaymentModal()">&times;</span>
            </div>
            <div class="admin-modal-body">
                <div class="payment-info-box mb-15">
                    <p>Invoice: <strong id="pay_inv_id">#SF-1001</strong></p>
                    <p>Total Amount: <strong id="pay_total">KES 0.00</strong></p>
                </div>
                <div class="form-group">
                    <label>Amount Paid (KES)</label>
                    <input type="number" id="pay_amount_input" class="form-control" placeholder="Enter amount paid" title="Amount Paid">
                </div>
                <div class="payment-calc mt-10">
                    <p>Current Balance: <strong id="pay_balance" class="st-summary-pending">KES 0.00</strong></p>
                </div>
            </div>
            <div class="admin-modal-footer">
                <button class="admin-btn" onclick="closePaymentModal()">Cancel</button>
                <button class="admin-btn admin-btn-secondary" onclick="savePaymentUpdate()">
                    <i class="fas fa-save"></i> Save Payment
                </button>
            </div>
        </div>
    </div>

    <!-- PDF Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="../admin.js?v=2"></script>
</body>
</html>
