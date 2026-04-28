<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipts - Shanfix Admin</title>
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
                <i class="fas fa-box"></i> Products & Categories
            </a>
            <a href="invoices.php" class="admin-nav-item">
                <i class="fas fa-file-invoice"></i> Invoices
            </a>
            <a href="receipts.php" class="admin-nav-item active">
                <i class="fas fa-receipt"></i> Receipts
            </a>
            <a href="adverts.php" class="admin-nav-item">
                <i class="fas fa-ad"></i> Adverts
            </a>
            <div class="admin-nav-divider"></div>
            <a href="../index.php" class="admin-nav-item">
                <i class="fas fa-external-link-alt"></i> View Portal
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
            <h1 class="admin-page-title">Receipts Management</h1>
            <div class="admin-user-profile">
                <span>Welcome, Admin</span>
                <div class="admin-avatar">A</div>
            </div>
        </header>

        <section class="admin-content">
            <div class="admin-card">
            <div class="justify-between-center mb-20">
                    <h2>Paid Invoices</h2>
                    <p class="text-sm">Only invoices with "Paid" status appear here.</p>
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

    <!-- Receipt Preview Modal -->
    <div id="receiptModal" class="admin-modal">
        <div class="admin-modal-content max-w-850">
            <div class="admin-modal-header">
                <h3>Official Receipt Preview</h3>
                <span class="close-modal" onclick="closeReceiptModal()">&times;</span>
            </div>
            <div class="admin-modal-body">
                <div id="receiptViewArea">
                    <!-- Receipt template clone here -->
                </div>
            </div>
            <div class="admin-modal-footer">
                <button class="admin-btn" onclick="closeReceiptModal()">Close</button>
                <button id="downloadBtnInReceiptModal" class="admin-btn admin-btn-secondary">
                    <i class="fas fa-download"></i> Download Receipt PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Hidden Receipt Template for PDF -->
    <div id="receiptTemplateContainer" style="display: none;">
        <div id="receiptTemplate" class="invoice-preview-template receipt-theme">
            <div class="invoice-header bg-receipt-dark">
                <div class="invoice-logo-container">
                    <img src="../assets/shanfix-logo.png" alt="Shanfix Technology">
                </div>
                <h3 class="invoice-id-header">OFFICIAL RECEIPT</h3>
            </div>
            
            <div class="invoice-meta-grid">
                <div class="invoice-meta-col">
                    <h4>Receipt From:</h4>
                    <p><strong>Shanfix Technology Limited</strong></p>
                    <p>Nairobi, Kenya</p>
                    <p>info@shanfix.com</p>
                </div>
                <div class="invoice-meta-col text-right">
                    <h4 class="border-transparent">Receipt Details:</h4>
                    <p>Receipt #: <strong id="rec_id">SF-REC-1001</strong></p>
                    <p>Invoice Ref: <span id="rec_inv_ref">SF-1001</span></p>
                    <p>Date Paid: <span id="rec_date">February 16, 2026</span></p>
                </div>
            </div>

            <div class="receipt-thanks-box">
                <p class="m-0 color-receipt font-600">Received with thanks from:</p>
                <h2 id="rec_client_name" class="m-0 color-black">Client Name</h2>
                <p class="m-0">Payment for: <span id="rec_description">Services rendered</span></p>
            </div>

            <table class="invoice-items-table">
                <thead>
                    <tr class="bg-gray-light">
                        <th class="w-10">Qty</th>
                        <th>Description</th>
                        <th class="w-20">Amount</th>
                    </tr>
                </thead>
 Elisa                <tbody id="rec_items_body">
                    <!-- Items -->
                </tbody>
            </table>

            <div class="flex-end mt-20">
                <div class="receipt-summary-box">
                    <p class="m-0 opacity-8 font-0-9">AMOUNT PAID</p>
                    <h2 id="rec_total_amount" class="m-5-0">KES 0.00</h2>
                </div>
            </div>

            <div class="mt-40 border-dashed-ccc pt-10 flex-between-end">
                <div>
                    <p class="font-0-8 font-italic">Thank you for your business!</p>
                </div>
                <div class="text-center">
                    <div class="signature-line"></div>
                    <p class="font-0-7">Authorized Signature</p>
                </div>
            </div>

            <div class="invoice-footer border-top-receipt mt-20">
                <p>Shanfix Technology - Excellence in Printing & Branding</p>
            </div>
        </div>
    </div>

    <!-- PDF Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script id="receiptInitTrigger">
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof initReceiptPage === 'function') initReceiptPage();
        });
    </script>
    <script src="../admin.js?v=2"></script>
</body>
</html>
