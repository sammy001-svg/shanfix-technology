<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Subscriptions - Shanfix Admin</title>
    <link rel="stylesheet" href="../admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Outfit:wght@500;600;700&display=swap" rel="stylesheet">
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
            <a href="services.php" class="admin-nav-item active"><i class="fas fa-concierge-bell"></i> <span>Services</span></a>
            <a href="products.php" class="admin-nav-item"><i class="fas fa-box"></i> <span>Catalog</span></a>
            <a href="orders.php" class="admin-nav-item"><i class="fas fa-shopping-bag"></i> <span>Orders</span></a>
            <a href="invoices.php" class="admin-nav-item"><i class="fas fa-file-invoice"></i> <span>Billing</span></a>
            <a href="receipts.php" class="admin-nav-item"><i class="fas fa-receipt"></i> <span>Receipts</span></a>
            <a href="portfolio.php" class="admin-nav-item"><i class="fas fa-briefcase"></i> <span>Portfolio</span></a>
            <a href="blog.php" class="admin-nav-item"><i class="fas fa-newspaper"></i> <span>Blog</span></a>
            <a href="adverts.php" class="admin-nav-item"><i class="fas fa-ad"></i> <span>Adverts</span></a>
            <a href="tickets.php" class="admin-nav-item"><i class="fas fa-life-ring"></i> <span>Support</span></a>
            <a href="messages.php" class="admin-nav-item">
                <i class="fas fa-inbox"></i> <span>Inbox</span>
                <span id="sidebarMsgBadge" style="display:none; background:#ef4444; color:#fff; font-size:0.65rem; font-weight:800; padding:2px 6px; border-radius:20px; margin-left:auto;"></span>
            </a>
            <div class="admin-nav-divider"></div>
            <a href="settings.php" class="admin-nav-item"><i class="fas fa-cog"></i> <span>Settings</span></a>
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
                <h1 class="admin-page-title">Client Subscriptions</h1>
                <p class="admin-subtitle">Manage active services provisioned to clients</p>
            </div>
            <div class="admin-header-actions">
                <button class="admin-btn admin-btn-secondary" onclick="generateDueInvoices()" id="genDueBtn">
                    <i class="fas fa-bolt"></i> Generate Due Invoices
                </button>
                <button class="admin-btn admin-btn-primary" onclick="openAssignModal()">
                    <i class="fas fa-plus"></i> Assign Service
                </button>
            </div>
        </header>

        <section class="admin-content">
            <!-- Stats -->
            <div class="admin-stats-grid" style="margin-bottom:2rem;">
                <div class="admin-stat-card glass-card">
                    <div class="stat-icon" style="background:rgba(99,102,241,0.1); color:var(--p);"><i class="fas fa-server"></i></div>
                    <div>
                        <div class="stat-label">Total Subscriptions</div>
                        <div class="stat-value" id="stat_total_subs">0</div>
                    </div>
                </div>
                <div class="admin-stat-card glass-card">
                    <div class="stat-icon" style="background:rgba(34,197,94,0.1); color:#22c55e;"><i class="fas fa-check-circle"></i></div>
                    <div>
                        <div class="stat-label">Active</div>
                        <div class="stat-value" id="stat_active_subs">0</div>
                    </div>
                </div>
                <div class="admin-stat-card glass-card">
                    <div class="stat-icon" style="background:rgba(245,158,11,0.1); color:#f59e0b;"><i class="fas fa-clock"></i></div>
                    <div>
                        <div class="stat-label">Due This Week</div>
                        <div class="stat-value" id="stat_due_subs">0</div>
                    </div>
                </div>
            </div>

            <div class="admin-card glass-card">
                <div class="flex-between mb-20">
                    <div class="flex-align-center gap-10">
                        <h3>All Subscriptions</h3>
                    </div>
                    <select id="subStatusFilter" class="form-control-sm" onchange="filterSubscriptions(this.value)">
                        <option value="all">All Statuses</option>
                        <option value="active">Active</option>
                        <option value="pending">Pending</option>
                        <option value="suspended">Suspended</option>
                        <option value="terminated">Terminated</option>
                    </select>
                </div>

                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Service</th>
                                <th>Billing</th>
                                <th>Next Due</th>
                                <th>Status</th>
                                <th style="text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="subscriptionsTableBody">
                            <tr><td colspan="6" style="text-align:center; padding:2.5rem; color:var(--text-low);">Loading subscriptions...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
</div>

<!-- ── Assign Service Modal ────────────────────────────────────────────── -->
<div id="assignModal" class="admin-modal">
    <div class="admin-modal-content" style="max-width:600px;">
        <div class="admin-modal-header">
            <h3 class="admin-modal-title">Assign Service to Client</h3>
            <span class="admin-modal-close" onclick="closeAssignModal()">&times;</span>
        </div>
        <div class="admin-modal-body">
            <form id="assignForm">
                <div class="form-row">
                    <div class="form-group col-12 mb-15">
                        <label class="form-label">Client <span style="color:var(--red)">*</span></label>
                        <select id="assign_client" class="form-control" required>
                            <option value="">— Select Client —</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-12 mb-15">
                        <label class="form-label">Catalog Product <span class="text-low">(optional — auto-fills name &amp; price)</span></label>
                        <select id="assign_product" class="form-control" onchange="onProductSelect()">
                            <option value="">— None / Custom —</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-12 mb-15">
                        <label class="form-label">Service Name <span style="color:var(--red)">*</span></label>
                        <input type="text" id="assign_name" class="form-control" placeholder="e.g. Business Hosting Plan" required>
                    </div>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                    <div class="form-group">
                        <label class="form-label">Billing Cycle</label>
                        <select id="assign_cycle" class="form-control">
                            <option value="monthly">Monthly</option>
                            <option value="yearly">Yearly</option>
                            <option value="one-time">One-time</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select id="assign_status" class="form-control">
                            <option value="active">Active</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                </div>
                <div class="form-group mb-15">
                    <label class="form-label">Next Due Date</label>
                    <input type="date" id="assign_due" class="form-control">
                    <p class="text-low mt-5" style="font-size:0.75rem;">Leave blank for one-time services.</p>
                </div>
            </form>
        </div>
        <div class="admin-modal-footer">
            <button class="admin-btn admin-btn-secondary" onclick="closeAssignModal()">Cancel</button>
            <button class="admin-btn admin-btn-primary" onclick="saveAssignment()">
                <i class="fas fa-link"></i> Assign Service
            </button>
        </div>
    </div>
</div>

<!-- ── Edit Subscription Modal ─────────────────────────────────────────── -->
<div id="editSubModal" class="admin-modal">
    <div class="admin-modal-content" style="max-width:520px;">
        <div class="admin-modal-header">
            <h3 class="admin-modal-title">Edit Subscription</h3>
            <span class="admin-modal-close" onclick="closeEditSubModal()">&times;</span>
        </div>
        <div class="admin-modal-body">
            <input type="hidden" id="edit_sub_id">
            <div class="form-group mb-15">
                <label class="form-label">Service Name</label>
                <input type="text" id="edit_sub_name" class="form-control">
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                <div class="form-group">
                    <label class="form-label">Billing Cycle</label>
                    <select id="edit_sub_cycle" class="form-control">
                        <option value="monthly">Monthly</option>
                        <option value="yearly">Yearly</option>
                        <option value="one-time">One-time</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select id="edit_sub_status" class="form-control">
                        <option value="active">Active</option>
                        <option value="pending">Pending</option>
                        <option value="suspended">Suspended</option>
                        <option value="terminated">Terminated</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Next Due Date</label>
                <input type="date" id="edit_sub_due" class="form-control">
            </div>
        </div>
        <div class="admin-modal-footer">
            <button class="admin-btn admin-btn-secondary" onclick="closeEditSubModal()">Cancel</button>
            <button class="admin-btn admin-btn-primary" onclick="saveEditSub()">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </div>
    </div>
</div>

<script src="../admin.js?v=15"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        checkAuth();
        if (typeof initServicesPage === 'function') initServicesPage();
    });
</script>
</body>
</html>
