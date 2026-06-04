<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shanfix Technology | Client command center</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <!-- Styles -->
    <link rel="stylesheet" href="client.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="client-portal-body">
    
    <!-- Sidebar -->
    <aside class="portal-sidebar">
        <div class="sidebar-logo">
            <img src="../assets/shanfix-logo.png" alt="Shanfix Logo" style="height: 40px; margin-bottom: 12px; filter: brightness(0) invert(1);">
            <div class="outfit" style="font-size: 1.8rem; letter-spacing: -1px;">Shanfix <span style="color:var(--color-accent)">Portal</span></div>
        </div>
        
        <nav class="portal-nav" style="margin-top: 2.5rem; flex-grow: 1;">
            <a href="#" class="nav-item active" data-tab="dashboard">
                <i class="fas fa-layer-group"></i> <span>Dashboard</span>
            </a>
            <a href="#" class="nav-item" data-tab="billing">
                <i class="fas fa-credit-card"></i> <span>Billing</span>
            </a>
            <a href="#" class="nav-item" data-tab="services">
                <i class="fas fa-box-open"></i> <span>My Services</span>
            </a>
            <a href="#" class="nav-item" data-tab="support">
                <i class="fas fa-headset"></i> <span>Support</span>
            </a>
            <a href="#" class="nav-item" data-tab="settings">
                <i class="fas fa-user-gear"></i> <span>Account</span>
            </a>
        </nav>

        <div style="padding: 2rem;">
            <div style="background: rgba(255,255,255,0.05); border-radius: 20px; padding: 1.5rem; margin-bottom: 1.5rem;">
                <p style="font-size: 0.8rem; color: rgba(255,255,255,0.5); margin-bottom: 0.5rem;">Need help?</p>
                <a href="tel:+254700000000" style="color: var(--color-accent); font-weight: 700; text-decoration: none; font-size: 0.9rem;">
                    <i class="fas fa-phone-alt mr-2"></i> +254 7XX XXX XXX
                </a>
            </div>
            <button id="logoutBtn" class="portal-btn-primary portal-btn-danger" style="width: 100%; border-radius: 16px; padding: 1rem;">
                <i class="fas fa-power-off"></i> <span>Sign Out</span>
            </button>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="portal-main">
        
        <!-- Header -->
        <header class="glass-header">
            <div style="display: flex; flex-direction: column;">
                <h1 id="welcomeText" class="outfit" style="margin: 0; font-size: 1.8rem; color: var(--p);">Dashboard</h1>
                <div id="headerSubtitle" style="margin: 4px 0 0 0; font-size: 0.9rem; color: var(--text-mid); display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-sparkles" style="color: var(--color-accent);"></i> Your account is currently healthy.
                </div>
            </div>
            
            <div style="display: flex; gap: 1.5rem; align-items: center;">
                <!-- Modern Search Bar -->
                <div style="position: relative; display: flex; align-items: center;">
                    <i class="fas fa-search" style="position: absolute; left: 1.2rem; color: var(--text-low); font-size: 0.9rem;"></i>
                    <input id="serviceSearchInput" type="text" placeholder="Search services..." style="padding: 0.8rem 1.2rem 0.8rem 2.8rem; border-radius: 50px; border: 1px solid var(--border); background: white; width: 260px; font-size: 0.9rem; transition: all 0.3s;" onfocus="this.style.width='320px'; this.style.borderColor='var(--s)'" onblur="this.style.width='260px'; this.style.borderColor='var(--border)'">
                </div>

                <!-- Notifications -->
                <div id="notificationBell" style="position: relative; cursor: pointer; width: 44px; height: 44px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow-sm); border: 1px solid var(--border);" title="Notifications">
                    <i class="fas fa-bell" style="color: var(--text-mid);"></i>
                    <span id="notificationBadge" style="display:none; position: absolute; top: -4px; right: -4px; min-width: 18px; height: 18px; background: var(--red); color: white; font-size: 0.6rem; font-weight: 800; border-radius: 9px; border: 2px solid white; align-items: center; justify-content: center; padding: 0 3px;"></span>
                </div>

                <div class="user-profile">
                    <div class="avatar"><i class="fas fa-user-astronaut"></i></div>
                    <div style="display: flex; flex-direction: column;">
                        <strong id="headerClientName" style="color: var(--p); font-size: 0.95rem;">Loading...</strong>
                        <span id="headerClientEmail" style="color: var(--text-low); font-size: 0.75rem;">loading@email.com</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <div id="content-container">
            
            <!-- Dashboard Tab -->
            <section id="tab-dashboard" class="portal-tab-content active">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(239, 68, 68, 0.05); color: var(--red);"><i class="fas fa-wallet"></i></div>
                        <div class="stat-value" id="dashPendingBalance">Ksh 0.00</div>
                        <div class="stat-label">Pending Dues</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(34, 197, 94, 0.05); color: var(--s);"><i class="fas fa-microchip"></i></div>
                        <div class="stat-value" id="dashActiveServices">0</div>
                        <div class="stat-label">Provisioned Services</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(89, 224, 10, 0.05); color: var(--color-accent);"><i class="fas fa-bolt"></i></div>
                        <div class="stat-value" id="dashOpenTickets">0</div>
                        <div class="stat-label">Active Interactions</div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2.5rem; align-items: start;">
                    <div class="data-card">
                        <h3 class="outfit" style="margin: 0 0 2rem 0; display: flex; align-items: center; gap: 12px; color: var(--p);">
                            <i class="fas fa-list-check" style="color:var(--s)"></i> Recent Activity
                        </h3>
                        <div style="overflow-x: auto;">
                            <table class="premium-table" id="recentInvoicesTable">
                                <thead>
                                    <tr>
                                        <th>Ref #</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th style="text-align: right;">Portal Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Injected via JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="data-card" style="padding: 2rem;">
                        <h3 class="outfit" style="margin: 0 0 1.5rem 0; font-size: 1.2rem; color: var(--p);">Quick Actions</h3>
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <button class="portal-btn-primary" style="width: 100%; justify-content: flex-start; background: var(--bg); color: var(--p); box-shadow: none; border: 1px solid var(--border);" onclick="switchTab('support')">
                                <i class="fas fa-plus-circle" style="color: var(--s)"></i> New Support Ticket
                            </button>
                            <button class="portal-btn-primary" style="width: 100%; justify-content: flex-start; background: var(--bg); color: var(--p); box-shadow: none; border: 1px solid var(--border);" onclick="switchTab('billing')">
                                <i class="fas fa-credit-card" style="color: var(--red)"></i> Pay Outstanding
                            </button>
                            <button class="portal-btn-primary" style="width: 100%; justify-content: flex-start; background: var(--bg); color: var(--p); box-shadow: none; border: 1px solid var(--border);" onclick="switchTab('services')">
                                <i class="fas fa-rocket" style="color: var(--color-accent)"></i> Upgrade Service
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Other tabs follow the same improved pattern via JS if needed, 
                 but they are already structurally good in index.php -->
            
            <!-- Billing Tab -->
            <section id="tab-billing" class="portal-tab-content">
                <div class="data-card">
                    <h3 class="outfit" style="color: var(--p); margin-bottom: 2rem;">Billing & Invoicing</h3>
                    <div style="overflow-x: auto;">
                        <table class="premium-table" id="allInvoicesTable">
                            <thead>
                                <tr>
                                    <th>Invoice ID</th>
                                    <th>Date</th>
                                    <th>Due Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th style="text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Injected via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Services Tab -->
            <section id="tab-services" class="portal-tab-content">
                <div class="data-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem;">
                        <h3 class="outfit" style="color: var(--p); margin: 0;">Service Infrastructure</h3>
                        <button class="portal-btn-primary" onclick="switchTab('support'); document.getElementById('ticketSubject').value='New Service Request'; document.getElementById('ticketPriority').value='Medium';">
                            <i class="fas fa-plus"></i> Request Service
                        </button>
                    </div>
                    <div class="stats-grid" id="servicesGrid">
                        <!-- Injected via JS -->
                    </div>
                </div>
            </section>

            <!-- Support Tab -->
            <section id="tab-support" class="portal-tab-content">
                <div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 3rem;">
                    <div class="data-card">
                        <h3 class="outfit" style="color: var(--p);">Get Assistance</h3>
                        <form id="newTicketForm">
                            <div class="form-group-premium">
                                <input type="text" id="ticketSubject" class="form-control" placeholder=" " required>
                                <label for="ticketSubject">Subject</label>
                            </div>
                            <div class="form-group-premium">
                                <select id="ticketPriority" class="form-control" required style="padding-top: 1.5rem;">
                                    <option value="Low">Low</option>
                                    <option value="Medium" selected>Medium</option>
                                    <option value="High">High</option>
                                </select>
                                <label for="ticketPriority">Priority</label>
                            </div>
                            <div class="form-group-premium">
                                <textarea id="ticketMessage" class="form-control" placeholder=" " required rows="6"></textarea>
                                <label for="ticketMessage">Description</label>
                            </div>
                            <button type="submit" class="portal-btn-primary" style="width: 100%;">
                                <i class="fas fa-paper-plane"></i> Send Request
                            </button>
                        </form>
                    </div>
                    
                    <div class="data-card">
                        <h3 class="outfit" style="color: var(--p);">History</h3>
                        <div id="ticketList">
                            <!-- Injected via JS -->
                        </div>
                    </div>
                </div>
            </section>

            <!-- Settings Tab -->
            <section id="tab-settings" class="portal-tab-content">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem;">
                    <div class="data-card">
                        <h3 class="outfit" style="color: var(--p);">User Profile</h3>
                        <form id="profileForm">
                            <div class="form-group-premium">
                                <input type="text" id="settingName" class="form-control" placeholder=" " required>
                                <label for="settingName">Full Name</label>
                            </div>
                            <div class="form-group-premium">
                                <input type="email" id="settingEmail" class="form-control" placeholder=" " readonly>
                                <label for="settingEmail">Email (read-only)</label>
                            </div>
                            <div class="form-group-premium">
                                <input type="tel" id="settingPhone" class="form-control" placeholder=" ">
                                <label for="settingPhone">Phone Number</label>
                            </div>
                            <div class="form-group-premium">
                                <input type="text" id="settingCompany" class="form-control" placeholder=" ">
                                <label for="settingCompany">Company / Organisation</label>
                            </div>
                            <button type="submit" id="profileSaveBtn" class="portal-btn-primary">
                                <i class="fas fa-save"></i> Update Profile
                            </button>
                        </form>
                    </div>

                    <div class="data-card">
                        <h3 class="outfit" style="color: var(--p);">Security</h3>
                        <form id="securityForm">
                            <div class="form-group-premium">
                                <input type="password" id="currentPassword" class="form-control" placeholder=" " required>
                                <label for="currentPassword">Current Password</label>
                            </div>
                            <div class="form-group-premium">
                                <input type="password" id="newPassword" class="form-control" placeholder=" " required>
                                <label for="newPassword">New Password</label>
                            </div>
                            <div class="form-group-premium">
                                <input type="password" id="confirmPassword" class="form-control" placeholder=" " required>
                                <label for="confirmPassword">Confirm New Password</label>
                            </div>
                            <button type="submit" id="securitySaveBtn" class="portal-btn-primary" style="background: var(--p);">
                                <i class="fas fa-shield-alt"></i> Change Password
                            </button>
                        </form>
                    </div>
                </div>
            </section>

        </div>
    </main>

    <!-- Ticket Detail Modal -->
    <div id="ticketModal" class="portal-modal">
        <div class="portal-modal-content" style="max-width: 800px;">
            <div class="portal-modal-header">
                <h3 id="modalTicketTitle" class="outfit" style="color: var(--p);">Ticket Conversation</h3>
                <button class="close-modal" onclick="closeTicketModal()">&times;</button>
            </div>
            <div class="portal-modal-body">
                <div id="ticketThread" class="ticket-thread-container">
                    <!-- Conversation loads here -->
                </div>

                <div class="reply-section mt-20" id="clientReplyArea">
                    <div class="form-group-premium">
                        <textarea id="clientReplyMessage" class="form-control" placeholder=" " rows="4" style="border: 1px solid var(--border);"></textarea>
                        <label for="clientReplyMessage">Your Response</label>
                    </div>
                    <div class="mt-15" style="display:flex; justify-content:flex-end;">
                        <button class="portal-btn-primary" onclick="submitClientReply()" style="width: auto; padding: 0.8rem 2rem;">
                            <i class="fas fa-paper-plane mr-1"></i> Send Reply
                        </button>
                    </div>
                </div>
                <div id="ticketClosedMsg" style="display:none; text-align:center; padding: 1rem; background: #fee2e2; color: #b91c1c; border-radius: 12px; margin-top: 1rem;">
                    This ticket has been closed. Please open a new ticket for further assistance.
                </div>
            </div>
        </div>
    </div>

    <!-- M-PESA Payment Modal -->
    <div id="mpesaModal" class="portal-modal">
        <div class="portal-modal-content" style="max-width:460px;">
            <div class="portal-modal-header">
                <h3 class="outfit" style="color:var(--p); display:flex; align-items:center; gap:10px;">
                    <i class="fas fa-mobile-alt" style="color:#22c55e;"></i> Pay with M-PESA
                </h3>
                <button class="close-modal" onclick="closeMpesaModal()">&times;</button>
            </div>
            <div class="portal-modal-body">

                <!-- Step 1: Enter phone -->
                <div id="mpesaStep1">
                    <div style="background:linear-gradient(135deg,rgba(34,197,94,0.08),rgba(22,163,74,0.04)); border-radius:16px; padding:20px; margin-bottom:24px; border:1px solid rgba(34,197,94,0.2);">
                        <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                            <span style="color:var(--text-low); font-size:0.85rem;">Invoice</span>
                            <strong id="mpesa_inv_ref" style="color:var(--p);">—</strong>
                        </div>
                        <div style="display:flex; justify-content:space-between;">
                            <span style="color:var(--text-low); font-size:0.85rem;">Amount Due</span>
                            <strong id="mpesa_inv_amount" style="font-size:1.2rem; color:#22c55e;">—</strong>
                        </div>
                    </div>

                    <div class="form-group-premium" style="margin-bottom:20px;">
                        <input type="tel" id="mpesaPhone" class="form-control" placeholder=" " autocomplete="tel">
                        <label for="mpesaPhone">M-PESA Phone Number</label>
                    </div>
                    <p style="font-size:0.78rem; color:var(--text-low); margin:-12px 0 20px;">Format: 07XX XXX XXX or 254 7XX XXX XXX</p>

                    <div id="mpesaInitError" style="display:none; color:#ef4444; font-size:0.85rem; padding:10px 14px; background:rgba(239,68,68,0.07); border-radius:10px; margin-bottom:16px;"></div>

                    <button id="mpesaSendBtn" class="portal-btn-primary" style="width:100%; background:linear-gradient(135deg,#16a34a,#22c55e);" onclick="initiateMpesa()">
                        <i class="fas fa-paper-plane"></i> Send STK Push
                    </button>
                </div>

                <!-- Step 2: Waiting for PIN -->
                <div id="mpesaStep2" style="display:none; text-align:center; padding:10px 0 20px;">
                    <div style="font-size:3.5rem; margin-bottom:16px;">📱</div>
                    <h4 style="color:var(--p); margin-bottom:8px;">Check Your Phone</h4>
                    <p style="color:var(--text-low); font-size:0.9rem; line-height:1.6;">
                        An M-PESA prompt was sent to<br>
                        <strong id="mpesaPhoneDisplay" style="color:var(--p);"></strong>
                    </p>
                    <p style="color:var(--text-low); font-size:0.85rem; margin:12px 0;">Enter your M-PESA PIN to confirm payment.</p>
                    <div style="margin:20px 0;">
                        <i class="fas fa-circle-notch fa-spin" style="color:#22c55e; font-size:1.8rem;"></i>
                        <p style="color:#94a3b8; font-size:0.78rem; margin-top:8px;">Waiting for confirmation...</p>
                    </div>
                    <button class="portal-btn-primary" style="width:100%; background:var(--bg); color:var(--text-low); border:1px solid var(--border); box-shadow:none;" onclick="cancelMpesaWait()">
                        Cancel
                    </button>
                </div>

                <!-- Step 3: Success -->
                <div id="mpesaStep3" style="display:none; text-align:center; padding:10px 0 20px;">
                    <div style="font-size:4rem; margin-bottom:16px;">✅</div>
                    <h3 style="color:#16a34a; margin-bottom:8px;">Payment Confirmed!</h3>
                    <p style="color:var(--text-low); font-size:0.9rem;">Your invoice has been paid successfully.</p>
                    <p id="mpesaReceiptNum" style="font-weight:800; color:var(--p); font-size:1rem; margin:16px 0; background:rgba(99,102,241,0.08); padding:10px 16px; border-radius:10px; display:inline-block;"></p>
                    <button class="portal-btn-primary" style="width:100%; margin-top:8px;" onclick="closeMpesaModal()">
                        <i class="fas fa-check"></i> Done
                    </button>
                </div>

                <!-- Step 4: Failed -->
                <div id="mpesaStep4" style="display:none; text-align:center; padding:10px 0 20px;">
                    <div style="font-size:3.5rem; margin-bottom:16px;">❌</div>
                    <h4 style="color:#ef4444; margin-bottom:8px;">Payment Not Completed</h4>
                    <p id="mpesaFailMsg" style="color:var(--text-low); font-size:0.9rem; margin-bottom:20px;"></p>
                    <button class="portal-btn-primary" style="width:100%; background:linear-gradient(135deg,#16a34a,#22c55e);" onclick="resetMpesaModal()">
                        <i class="fas fa-redo"></i> Try Again
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- Hidden Invoice PDF Template -->
    <div id="invoicePdfContainer" style="display:none; position:fixed; left:-9999px;">
        <div id="invoicePdfContent" style="background:white; padding:40px; width:794px; font-family:'Inter',sans-serif; color:#1e293b;">
            <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:2px solid #f1f5f9; padding-bottom:20px; margin-bottom:30px;">
                <img src="../assets/shanfix-logo.png" style="height:50px;">
                <div style="text-align:right;">
                    <h2 style="margin:0; color:#6366f1;">INVOICE</h2>
                    <p style="margin:5px 0; font-weight:700;" id="pdf_ref">#REF</p>
                    <span id="pdf_status_badge" style="font-size:0.75rem; font-weight:700; padding:3px 10px; border-radius:20px; background:#dcfce7; color:#166534;">PAID</span>
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:40px; margin-bottom:30px;">
                <div>
                    <h4 style="color:#64748b; margin-bottom:8px; text-transform:uppercase; font-size:0.7rem;">Billed From</h4>
                    <p style="margin:0;"><strong>Shanfix Technology Ltd</strong></p>
                    <p style="margin:2px 0; color:#64748b;">info@shanfixtechnology.com</p>
                    <p style="margin:2px 0; color:#64748b;">M-PESA Till: 5698666</p>
                </div>
                <div>
                    <h4 style="color:#64748b; margin-bottom:8px; text-transform:uppercase; font-size:0.7rem;">Billed To</h4>
                    <p style="margin:0;"><strong id="pdf_client_name">Client Name</strong></p>
                    <p style="margin:2px 0; color:#64748b;" id="pdf_client_email">email</p>
                    <p style="margin:2px 0; color:#64748b;" id="pdf_client_phone"></p>
                    <p style="margin:2px 0; color:#64748b;" id="pdf_client_company"></p>
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:40px; margin-bottom:30px; font-size:0.85rem;">
                <div><span style="color:#64748b;">Issue Date: </span><strong id="pdf_issue_date"></strong></div>
                <div><span style="color:#64748b;">Due Date: </span><strong id="pdf_due_date"></strong></div>
            </div>

            <table style="width:100%; border-collapse:collapse; margin-bottom:30px; font-size:0.9rem;">
                <thead>
                    <tr style="background:#f8fafc;">
                        <th style="padding:10px 12px; border-bottom:1px solid #e2e8f0; text-align:left;">Description</th>
                        <th style="padding:10px 12px; border-bottom:1px solid #e2e8f0; text-align:center; width:60px;">Qty</th>
                        <th style="padding:10px 12px; border-bottom:1px solid #e2e8f0; text-align:right; width:120px;">Unit Price</th>
                        <th style="padding:10px 12px; border-bottom:1px solid #e2e8f0; text-align:right; width:120px;">Total</th>
                    </tr>
                </thead>
                <tbody id="pdf_items_body"></tbody>
            </table>

            <div style="display:flex; justify-content:flex-end;">
                <div style="width:260px;">
                    <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #f1f5f9; font-size:0.9rem;">
                        <span style="color:#64748b;">Subtotal</span><span id="pdf_subtotal">KES 0</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #f1f5f9; font-size:0.9rem;">
                        <span style="color:#64748b;">VAT (16%)</span><span id="pdf_tax">KES 0</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; padding:12px 0; font-weight:800; font-size:1.1rem; color:#6366f1;">
                        <span>Total</span><span id="pdf_total">KES 0</span>
                    </div>
                </div>
            </div>

            <div id="pdf_terms_block" style="margin-top:30px; padding-top:20px; border-top:1px solid #f1f5f9; font-size:0.8rem; color:#64748b;">
                <p id="pdf_terms"></p>
                <p style="text-align:center; margin-top:20px;">Thank you for choosing Shanfix Technology!</p>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="client.js"></script>
</body>
</html>
