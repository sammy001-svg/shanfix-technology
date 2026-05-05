<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clients - Shanfix Admin</title>
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
                    <i class="fas fa-chart-line"></i> <span>Insights</span>
                </a>
                <a href="clients.php" class="admin-nav-item active">
                    <i class="fas fa-users"></i> <span>Clients</span>
                </a>
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
                    <h1 class="admin-page-title">Client Directory</h1>
                    <p class="admin-subtitle">Manage and track all registered technology clients</p>
                </div>
                <div class="admin-header-right">
                    <button class="admin-btn admin-btn-primary" onclick="openClientModal()">
                        <i class="fas fa-plus"></i> Register Client
                    </button>
                </div>
            </header>

            <section class="admin-content">
                <div class="admin-card glass-card">
                    <div class="flex-between mb-30">
                        <div class="admin-search-wrapper">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" id="clientSearch" class="form-control" placeholder="Search by name, email or company...">
                        </div>
                    </div>
                    
                    <div class="admin-table-container">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Client Profile</th>
                                    <th>Organization</th>
                                    <th>Status</th>
                                    <th>Joined</th>
                                    <th style="text-align: right;">Management</th>
                                </tr>
                            </thead>
                            <tbody id="clientTableBody">
                                <!-- Populated via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Client Modal (Add/Edit) -->
    <div id="clientModal" class="admin-modal">
        <div class="admin-modal-content" style="max-width: 600px;">
            <div class="admin-modal-header">
                <h3 id="modalTitle" class="admin-modal-title">Register New Client</h3>
                <span class="admin-modal-close" onclick="closeClientModal()">&times;</span>
            </div>
            <div class="admin-modal-body">
                <form id="clientForm">
                    <input type="hidden" id="c_id">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" id="c_name" class="form-control" required placeholder="John Doe">
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" id="c_email" class="form-control" required placeholder="john@example.com">
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" id="c_phone" class="form-control" placeholder="+254 7XX XXX XXX">
                        </div>
                        <div class="form-group">
                            <label>Company/Organization</label>
                            <input type="text" id="c_company" class="form-control" placeholder="Acme Inc.">
                        </div>
                        <div class="form-group" id="passwordGroup">
                            <label>Account Password</label>
                            <input type="text" id="c_password" class="form-control" placeholder="Set password (e.g. Client@123)">
                            <small class="text-low" style="font-size: 0.75rem; display: block; margin-top: 5px;">Leave empty to use default: Client@123</small>
                        </div>
                        <div class="form-group">
                            <label>Account Status</label>
                            <select id="c_status" class="form-control admin-select-custom">
                                <option value="active">Active</option>
                                <option value="suspended">Suspended</option>
                                <option value="pending">Pending Verification</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="admin-modal-footer">
                <button class="admin-btn admin-btn-secondary" onclick="closeClientModal()">Cancel</button>
                <button type="submit" form="clientForm" id="clientSubmitBtn" class="admin-btn admin-btn-primary">Save Client Profile</button>
            </div>
        </div>
    </div>

    <!-- Password Reset Modal -->
    <div id="passwordModal" class="admin-modal">
        <div class="admin-modal-content" style="max-width: 450px;">
            <div class="admin-modal-header">
                <h3 class="admin-modal-title">Reset Security Access</h3>
                <span class="admin-modal-close" onclick="closePasswordModal()">&times;</span>
            </div>
            <div class="admin-modal-body">
                <div class="alert-box mb-20">
                    <i class="fas fa-shield-alt"></i>
                    <p>Resetting access for: <strong id="reset_client_name"></strong></p>
                </div>
                <div class="form-group">
                    <label>Assigned Temporary Password</label>
                    <input type="text" id="new_password_input" class="form-control" value="Client@123">
                </div>
                <p class="text-low" style="font-size: 0.85rem;">The client will be required to change this password upon their next successful authentication.</p>
            </div>
            <div class="admin-modal-footer">
                <button class="admin-btn admin-btn-secondary" onclick="closePasswordModal()">Cancel</button>
                <button class="admin-btn admin-btn-primary" onclick="confirmPasswordReset()">Initialize Reset</button>
            </div>
        </div>
    </div>

    <!-- Welcome Letter Template (Hidden) -->
    <div id="welcomeLetterContainer" class="hidden-template">
        <div id="welcomeLetterTemplate" class="invoice-preview-template">
            <div class="invoice-header">
                <div class="invoice-logo-container">
                    <img src="../assets/shanfix-logo.png" alt="Shanfix Technology">
                </div>
                <h3 class="invoice-id-header">CLIENT ONBOARDING</h3>
            </div>
            
            <div class="invoice-meta-grid">
                <div class="invoice-meta-col">
                    <h4>Provider</h4>
                    <p><strong>Shanfix Technology Limited</strong></p>
                    <p>Infrastructure & Software Solutions</p>
                </div>
                <div class="invoice-meta-col" style="text-align: right;">
                    <h4 class="border-transparent">Onboarding Details</h4>
                    <p>Date: <strong id="wl_date"></strong></p>
                    <p>Reference: <strong id="wl_ref">SF-CLIENT-INIT</strong></p>
                </div>
            </div>

            <div class="mt-30">
                <h2 style="color: #1e293b; font-family: 'Outfit', sans-serif;">Welcome to Shanfix Technology</h2>
                <p style="margin-top: 15px; font-size: 1.1rem;">Dear <strong id="wl_name"></strong>,</p>
                <p style="margin-top: 10px;">Your administrative account has been successfully provisioned. You can now access your personalized client portal to track orders, manage subscriptions, and view financial statements.</p>
            </div>

            <div class="mt-30" style="background: #f8fafc; padding: 2rem; border-radius: 12px; border-left: 5px solid #6366f1;">
                <h4 style="margin-bottom: 15px; color: #6366f1;">PORTAL ACCESS CREDENTIALS</h4>
                <div style="display: grid; grid-template-columns: 150px 1fr; gap: 10px;">
                    <strong>Portal URL:</strong> <span>https://shanfixtechnology.com/client/login.php</span>
                    <strong>Username:</strong> <span id="wl_email" style="font-weight: 600;"></span>
                    <strong>Password:</strong> <span id="wl_pass" style="font-weight: 600; color: #ef4444;"></span>
                </div>
            </div>

            <div class="mt-30">
                <h4 style="margin-bottom: 10px;">Security Recommendations:</h4>
                <ul style="padding-left: 20px; list-style-type: disc;">
                    <li>Change your password immediately upon your first successful login.</li>
                    <li>Do not share these credentials with unauthorized personnel.</li>
                    <li>Ensure you are accessing the portal via a secure HTTPS connection.</li>
                </ul>
            </div>

            <div class="invoice-footer" style="margin-top: 60px; border-top: 1px solid #e2e8f0; padding-top: 20px;">
                <p>If you encounter any technical difficulties, please reach out to our support desk.</p>
                <p><strong>Shanfix Technology Limited - Quality Infrastructure Solutions</strong></p>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <script src="../admin.js?v=13"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof initClientsPage === 'function') initClientsPage();
        });
    </script>
</body>
</html>
