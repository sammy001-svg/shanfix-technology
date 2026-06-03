<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Center - Shanfix Admin</title>
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
                <a href="clients.php" class="admin-nav-item">
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
                <a href="tickets.php" class="admin-nav-item active">
                    <i class="fas fa-life-ring"></i> <span>Support</span>
                </a>
                <a href="messages.php" class="admin-nav-item">
                    <i class="fas fa-inbox"></i> <span>Inbox</span>
                    <span id="sidebarMsgBadge" style="display:none; background:#ef4444; color:#fff; font-size:0.65rem; font-weight:800; padding:2px 6px; border-radius:20px; margin-left:auto;"></span>
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
                    <h1 class="admin-page-title">Client Support Center</h1>
                    <p class="admin-subtitle">Manage service requests and technical assistance threads</p>
                </div>
                <div class="admin-user-profile">
                    <div class="admin-header-actions">
                        <button class="icon-btn"><i class="fas fa-bell"></i></button>
                    </div>
                    <div class="admin-avatar">A</div>
                </div>
            </header>

            <section class="admin-content">
                <div class="admin-card glass-card">
                    <div class="flex-between mb-30">
                        <h2 style="margin:0;">Support Interactions</h2>
                        <div class="flex-gap">
                            <select id="ticketStatusFilter" class="form-control-sm" style="width: 150px;">
                                <option value="all">All Tickets</option>
                                <option value="open">Open Only</option>
                                <option value="closed">Resolved</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="admin-table-container">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Ticket Ref</th>
                                    <th>Client Identity</th>
                                    <th>Subject / Topic</th>
                                    <th>Priority</th>
                                    <th>Current Status</th>
                                    <th style="text-align: right;">Portal Action</th>
                                </tr>
                            </thead>
                            <tbody id="adminTicketsBody">
                                <!-- Populated via admin.js -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Ticket Detail Modal -->
    <div id="ticketModal" class="admin-modal">
        <div class="admin-modal-content" style="max-width: 800px;">
            <div class="admin-modal-header">
                <h3 id="modalTicketTitle" class="admin-modal-title">Ticket Conversation</h3>
                <span class="admin-modal-close" onclick="closeTicketModal()">&times;</span>
            </div>
            <div class="admin-modal-body">
                <div id="ticketThread" class="ticket-thread-container">
                    <!-- Conversation loads here -->
                </div>

                <div class="reply-section mt-20">
                    <label class="form-group label" style="margin-bottom:10px; display:block;">Admin Official Response</label>
                    <textarea id="adminReplyMessage" class="form-control" rows="4" placeholder="Type your response to the client..." style="background: rgba(255,255,255,0.02);"></textarea>
                    <div class="mt-15" style="display:flex; justify-content:flex-end;">
                        <button class="admin-btn admin-btn-primary" onclick="submitAdminReply()">
                            <i class="fas fa-paper-plane mr-1"></i> Dispatch Response
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../admin.js?v=13"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof initTicketsPage === 'function') initTicketsPage();
        });
    </script>
</body>
</html>
