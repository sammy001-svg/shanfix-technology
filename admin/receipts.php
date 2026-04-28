<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipts - Shanfix Admin</title>
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
                <a href="invoices.php" class="admin-nav-item">
                    <i class="fas fa-file-invoice"></i> <span>Invoices</span>
                </a>
                <a href="receipts.php" class="admin-nav-item active">
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
                <h1 class="admin-page-title">Receipts Management</h1>
                <div class="admin-user-profile">
                    <span>Welcome, Admin</span>
                    <div class="admin-avatar">A</div>
                </div>
            </header>

            <section class="admin-content">
                <div class="admin-card">
                    <div class="flex-between-center mb-20">
                        <h2>Paid Invoices</h2>
                        <p class="text-low" style="font-size: 0.9rem;">Only invoices with "Paid" status appear here.</p>
                    </div>

                    <div class="admin-table-container">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Inv #</th>
                                    <th>Client</th>
                                    <th>Total</th>
                                    <th>Paid</th>
                                    <th>Balance</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="receiptsTableBody">
                                <!-- Populated via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Receipt Preview Modal -->
    <div id="receiptModal" class="admin-modal">
        <div class="admin-modal-content">
            <div class="admin-modal-header">
                <h3 class="admin-modal-title">Official Receipt Preview</h3>
                <span class="admin-modal-close" onclick="closeReceiptModal()">&times;</span>
            </div>
            <div class="admin-modal-body">
                <div id="receiptViewArea" class="invoice-paper-preview">
                    <!-- Receipt template clone here -->
                </div>
            </div>
            <div class="admin-modal-footer">
                <button class="admin-btn admin-btn-secondary" onclick="closeReceiptModal()">Close</button>
                <button id="downloadBtnInReceiptModal" class="admin-btn admin-btn-primary">
                    <i class="fas fa-download"></i> Download Receipt PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Hidden Receipt Template for PDF -->
    <div id="receiptTemplateContainer" class="hidden-template">
        <div id="receiptTemplate" class="invoice-preview-template">
            <div class="receipt-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #22c55e; padding-bottom: 2rem; margin-bottom: 2rem; background: #f0fdf4; margin: -4rem -4rem 2rem -4rem; padding: 4rem;">
                <div class="invoice-logo-container">
                    <img src="../assets/shanfix-logo.png" alt="Shanfix Technology" style="height: 60px;">
                </div>
                <h2 style="margin: 0; color: #166534; letter-spacing: 2px;">OFFICIAL RECEIPT</h2>
            </div>
            
            <div class="invoice-meta-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; margin-bottom: 3rem;">
                <div class="invoice-meta-col">
                    <h4 style="color: #64748b; margin-bottom: 1rem; border-bottom: 1px solid #eee; padding-bottom: 0.5rem;">Receipt From</h4>
                    <p><strong>Shanfix Technology Limited</strong></p>
                    <p>Nairobi, Kenya</p>
                    <p>info@shanfixtechnology.com</p>
                </div>
                <div class="invoice-meta-col text-right" style="text-align: right;">
                    <h4 style="color: #64748b; margin-bottom: 1rem; border-bottom: 1px solid #eee; padding-bottom: 0.5rem;">Receipt Details</h4>
                    <p>Receipt #: <strong id="rec_id">SF-REC-1001</strong></p>
                    <p>Invoice Ref: <span id="rec_inv_ref">SF-1001</span></p>
                    <p>Date Paid: <span id="rec_date">February 16, 2026</span></p>
                </div>
            </div>

            <div class="receipt-thanks-box" style="background: #f8fafc; padding: 2.5rem; border-radius: 12px; border-left: 5px solid #22c55e; margin-bottom: 3rem;">
                <p style="margin: 0; color: #64748b; font-size: 0.9rem; text-transform: uppercase; font-weight: 700;">Received with thanks from:</p>
                <h2 id="rec_client_name" style="margin: 0.5rem 0; color: #1e293b; font-size: 2rem;">Client Name</h2>
                <p style="margin: 0; color: #475569;">Payment for: <span id="rec_description" style="font-weight: 600;">Services rendered</span></p>
            </div>

            <table class="invoice-items-table" style="width: 100%; border-collapse: collapse; margin-bottom: 3rem;">
                <thead>
                    <tr style="background: #f8fafc;">
                        <th style="padding: 1rem; border: 1px solid #e2e8f0; text-align: left; width: 15%;">Qty</th>
                        <th style="padding: 1rem; border: 1px solid #e2e8f0; text-align: left;">Description</th>
                        <th style="padding: 1rem; border: 1px solid #e2e8f0; text-align: right; width: 25%;">Amount</th>
                    </tr>
                </thead>
                <tbody id="rec_items_body">
                    <!-- Dynamic items here -->
                </tbody>
            </table>

            <div style="display: flex; justify-content: flex-end; margin-bottom: 4rem;">
                <div style="width: 350px; padding: 2rem; background: #22c55e; color: white; border-radius: 12px; text-align: right; box-shadow: 0 10px 20px rgba(34, 197, 94, 0.2);">
                    <p style="margin: 0; opacity: 0.8; font-size: 0.9rem;">TOTAL AMOUNT PAID</p>
                    <h1 style="margin: 0.5rem 0 0; font-size: 2.5rem;">KES <span id="rec_total_amount">0.00</span></h1>
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: flex-end; border-top: 1px solid #eee; padding-top: 2rem;">
                <div style="color: #94a3b8; font-style: italic;">
                    <p>Thank you for your business!</p>
                </div>
                <div style="text-align: center; width: 250px;">
                    <div style="border-bottom: 1px solid #cbd5e1; margin-bottom: 0.5rem;"></div>
                    <p style="margin: 0; color: #64748b; font-size: 0.8rem;">Authorized Signature</p>
                </div>
            </div>

            <div class="invoice-footer" style="text-align: center; color: #94a3b8; font-size: 0.8rem; margin-top: 4rem;">
                <p>Shanfix Technology - Excellence in Printing & Branding</p>
            </div>
        </div>
    </div>

    <!-- PDF Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="../admin.js?v=4"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof initReceiptPage === 'function') initReceiptPage();
        });
    </script>
</body>
</html>
