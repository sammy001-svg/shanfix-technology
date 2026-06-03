<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inbox - Shanfix Admin</title>
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
            <a href="index.php" class="admin-nav-item"><i class="fas fa-chart-line"></i> <span>Insights</span></a>
            <a href="clients.php" class="admin-nav-item"><i class="fas fa-users"></i> <span>Clients</span></a>
            <a href="categories.php" class="admin-nav-item"><i class="fas fa-tags"></i> <span>Categories</span></a>
            <a href="services.php" class="admin-nav-item"><i class="fas fa-concierge-bell"></i> <span>Services</span></a>
            <a href="products.php" class="admin-nav-item"><i class="fas fa-box"></i> <span>Catalog</span></a>
            <a href="orders.php" class="admin-nav-item"><i class="fas fa-shopping-bag"></i> <span>Orders</span></a>
            <a href="invoices.php" class="admin-nav-item"><i class="fas fa-file-invoice"></i> <span>Billing</span></a>
            <a href="receipts.php" class="admin-nav-item"><i class="fas fa-receipt"></i> <span>Receipts</span></a>
            <a href="adverts.php" class="admin-nav-item"><i class="fas fa-ad"></i> <span>Adverts</span></a>
            <a href="tickets.php" class="admin-nav-item"><i class="fas fa-life-ring"></i> <span>Support</span></a>
            <a href="messages.php" class="admin-nav-item active">
                <i class="fas fa-inbox"></i> <span>Inbox</span>
                <span id="sidebarMsgBadge" style="display:none; background:#ef4444; color:#fff; font-size:0.65rem; font-weight:800; padding:2px 6px; border-radius:20px; margin-left:auto;"></span>
            </a>
            <div class="admin-nav-divider"></div>
            <a href="../index.php" class="admin-nav-item"><i class="fas fa-external-link-alt"></i> <span>Live Site</span></a>
        </nav>
        <div class="admin-sidebar-footer">
            <a href="login.php" class="admin-nav-item admin-footer-link" onclick="sessionStorage.clear()">
                <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
            </a>
        </div>
    </aside>

    <!-- Main -->
    <main class="admin-main">
        <header class="admin-header">
            <div class="admin-header-left">
                <h1 class="admin-page-title">Contact Inbox</h1>
                <p class="admin-subtitle">Messages submitted via the public contact form</p>
            </div>
            <div class="admin-user-profile">
                <div class="admin-header-actions">
                    <select id="msgFilter" class="form-control-sm" onchange="filterMessages(this.value)">
                        <option value="all">All Messages</option>
                        <option value="unread">Unread</option>
                        <option value="read">Read</option>
                        <option value="replied">Replied</option>
                    </select>
                </div>
                <div class="admin-avatar">A</div>
            </div>
        </header>

        <section class="admin-content">
            <div class="admin-card glass-card">
                <div id="msgStats" style="display:flex; gap:20px; margin-bottom:24px; flex-wrap:wrap;">
                    <span class="status-badge" id="stat_total" style="font-size:0.85rem; padding:6px 14px;">Total: 0</span>
                    <span class="status-badge badge-pending" id="stat_unread" style="font-size:0.85rem; padding:6px 14px;">Unread: 0</span>
                    <span class="status-badge badge-paid" id="stat_replied" style="font-size:0.85rem; padding:6px 14px;">Replied: 0</span>
                </div>

                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>From</th>
                                <th>Subject</th>
                                <th>Received</th>
                                <th style="text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="messagesTableBody">
                            <tr><td colspan="5" style="text-align:center; padding:2rem; color:var(--text-low);">Loading messages...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
</div>

<!-- Message Detail + Reply Modal -->
<div id="msgModal" class="admin-modal">
    <div class="admin-modal-content" style="max-width:700px;">
        <div class="admin-modal-header">
            <h3 id="msgModalTitle" class="admin-modal-title">Message</h3>
            <span class="admin-modal-close" onclick="closeMsgModal()">&times;</span>
        </div>
        <div class="admin-modal-body">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:20px;">
                <div>
                    <p class="text-low" style="font-size:0.75rem; text-transform:uppercase; margin-bottom:4px;">From</p>
                    <p id="msg_from" style="font-weight:700; margin:0;"></p>
                    <a id="msg_email_link" href="#" style="font-size:0.85rem; color:var(--p);"></a>
                </div>
                <div>
                    <p class="text-low" style="font-size:0.75rem; text-transform:uppercase; margin-bottom:4px;">Received</p>
                    <p id="msg_date" style="margin:0; font-size:0.9rem;"></p>
                </div>
            </div>

            <div style="background:var(--glass-bg); border-radius:12px; padding:20px; margin-bottom:20px; border:1px solid var(--glass-border);">
                <p class="text-low" style="font-size:0.75rem; text-transform:uppercase; margin-bottom:8px;">Message</p>
                <p id="msg_body" style="margin:0; line-height:1.7; white-space:pre-wrap;"></p>
            </div>

            <div id="replyAlreadySent" style="display:none; background:rgba(34,197,94,0.08); border-radius:12px; padding:16px; margin-bottom:20px; border-left:4px solid #22c55e;">
                <p class="text-low" style="font-size:0.75rem; text-transform:uppercase; margin-bottom:8px;">Previous Reply</p>
                <p id="msg_prev_reply" style="margin:0; line-height:1.7; white-space:pre-wrap; font-size:0.9rem;"></p>
            </div>

            <div id="replySection">
                <h4 style="margin-bottom:12px;">Reply to this message</h4>
                <div class="form-group mb-15">
                    <textarea id="replyText" class="form-control" rows="5" placeholder="Type your reply..."></textarea>
                </div>
                <div style="display:flex; justify-content:flex-end; gap:10px;">
                    <button class="admin-btn admin-btn-secondary" onclick="closeMsgModal()">Close</button>
                    <button class="admin-btn admin-btn-primary" id="sendReplyBtn" onclick="sendReply()">
                        <i class="fas fa-paper-plane"></i> Send Reply
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../admin.js?v=15"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        checkAuth();
        if (typeof initMessagesPage === 'function') initMessagesPage();
    });
</script>
</body>
</html>
