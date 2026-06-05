<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php'); exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio - Shanfix Admin</title>
    <link rel="stylesheet" href="../admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Outfit:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="admin-body">
<div class="admin-layout-wrapper">
    <aside class="admin-sidebar">
        <div class="admin-logo">Shanfix <span>Admin</span></div>
        <nav class="admin-nav">
            <a href="index.php" class="admin-nav-item"><i class="fas fa-chart-line"></i> <span>Insights</span></a>
            <a href="clients.php" class="admin-nav-item"><i class="fas fa-users"></i> <span>Clients</span><span id="sidebarClientBadge" style="display:none; background:#f59e0b; color:#fff; font-size:0.65rem; font-weight:800; padding:2px 6px; border-radius:20px; margin-left:auto;"></span></a>
            <a href="categories.php" class="admin-nav-item"><i class="fas fa-tags"></i> <span>Categories</span></a>
            <a href="services.php" class="admin-nav-item"><i class="fas fa-concierge-bell"></i> <span>Services</span></a>
            <a href="products.php" class="admin-nav-item"><i class="fas fa-box"></i> <span>Catalog</span></a>
            <a href="orders.php" class="admin-nav-item"><i class="fas fa-shopping-bag"></i> <span>Orders</span></a>
            <a href="invoices.php" class="admin-nav-item"><i class="fas fa-file-invoice"></i> <span>Billing</span></a>
            <a href="receipts.php" class="admin-nav-item"><i class="fas fa-receipt"></i> <span>Receipts</span></a>
            <a href="portfolio.php" class="admin-nav-item active"><i class="fas fa-briefcase"></i> <span>Portfolio</span></a>
                        <a href="events.php" class="admin-nav-item"><i class="fas fa-ticket-alt"></i> <span>Events</span></a>
            <a href="testimonials.php" class="admin-nav-item"><i class="fas fa-quote-right"></i> <span>Testimonials</span></a>
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

    <main class="admin-main">
        <header class="admin-header">
            <div class="admin-header-left">
                <h1 class="admin-page-title">Portfolio Projects</h1>
                <p class="admin-subtitle">Manage case studies and project showcases on the public portfolio page</p>
            </div>
            <div class="admin-header-actions">
                <a href="../portfolio.php" target="_blank" class="admin-btn admin-btn-secondary">
                    <i class="fas fa-external-link-alt"></i> View Live
                </a>
                <button class="admin-btn admin-btn-primary" onclick="openProjectModal()">
                    <i class="fas fa-plus"></i> Add Project
                </button>
            </div>
        </header>

        <section class="admin-content">
            <div class="admin-card glass-card">
                <div id="portfolioGrid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:20px;">
                    <p class="text-low" style="grid-column:1/-1; text-align:center; padding:2rem;">Loading projects...</p>
                </div>
            </div>
        </section>
    </main>
</div>

<!-- Project Modal -->
<div id="projectModal" class="modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:1000; align-items:center; justify-content:center; overflow-y:auto; padding:2rem;">
    <div class="admin-card glass-card" style="width:680px; max-width:95vw; border-radius:20px;">
        <div class="flex-between mb-20">
            <h3 id="projectModalTitle">Add Portfolio Project</h3>
            <button class="icon-btn" onclick="closeProjectModal()"><i class="fas fa-times"></i></button>
        </div>
        <form id="projectForm" enctype="multipart/form-data">
            <input type="hidden" id="pf_id" name="id">
            <input type="hidden" id="pf_existing_image" name="existing_image">

            <div class="form-group mb-15">
                <label class="form-label">Project Title <span style="color:var(--red)">*</span></label>
                <input type="text" id="pf_title" name="title" class="form-control" placeholder="e.g. Skyline E-Commerce Platform" required>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                <div class="form-group">
                    <label class="form-label">Category Badge</label>
                    <input type="text" id="pf_badge" name="badge" class="form-control" placeholder="e.g. E-Commerce & Retail">
                </div>
                <div class="form-group">
                    <label class="form-label">Live URL <span class="text-low">(optional)</span></label>
                    <input type="url" id="pf_live_url" name="live_url" class="form-control" placeholder="https://...">
                </div>
            </div>
            <div class="form-group mb-15">
                <label class="form-label">Description</label>
                <textarea id="pf_desc" name="description" class="form-control" rows="4" placeholder="Brief project description..."></textarea>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:12px; margin-bottom:15px;">
                <div class="form-group">
                    <label class="form-label">Stat 1 Value</label>
                    <input type="text" id="pf_stat1_val" name="stat1_val" class="form-control" placeholder="+140%">
                </div>
                <div class="form-group">
                    <label class="form-label">Stat 1 Label</label>
                    <input type="text" id="pf_stat1_label" name="stat1_label" class="form-control" placeholder="Conversion">
                </div>
                <div class="form-group">
                    <label class="form-label">Stat 2 Value</label>
                    <input type="text" id="pf_stat2_val" name="stat2_val" class="form-control" placeholder="< 0.8s">
                </div>
                <div class="form-group">
                    <label class="form-label">Stat 2 Label</label>
                    <input type="text" id="pf_stat2_label" name="stat2_label" class="form-control" placeholder="Load Time">
                </div>
            </div>
            <div class="form-group mb-15">
                <label class="form-label">Project Image</label>
                <div id="pfImgPreview" style="margin-bottom:8px;"></div>
                <input type="file" id="pf_image" name="image" class="form-control" accept="image/*" onchange="previewImg(this,'pfImgPreview')">
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:15px; margin-bottom:20px; align-items:end;">
                <div class="form-group">
                    <label class="form-label">Sort Order</label>
                    <input type="number" id="pf_sort" name="sort_order" class="form-control" value="0" min="0">
                </div>
                <div class="form-group" style="display:flex; align-items:center; gap:8px; padding-bottom:2px;">
                    <input type="checkbox" id="pf_active" name="is_active" checked style="width:16px; height:16px; accent-color:var(--p);">
                    <label for="pf_active" style="cursor:pointer; font-size:0.9rem; margin:0;">Active</label>
                </div>
                <div class="form-group" style="display:flex; align-items:center; gap:8px; padding-bottom:2px;">
                    <input type="checkbox" id="pf_featured" name="is_featured" style="width:16px; height:16px; accent-color:#f59e0b;">
                    <label for="pf_featured" style="cursor:pointer; font-size:0.9rem; margin:0;">Featured</label>
                </div>
            </div>
            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" class="admin-btn admin-btn-secondary" onclick="closeProjectModal()">Cancel</button>
                <button type="submit" class="admin-btn admin-btn-primary"><i class="fas fa-save"></i> Save Project</button>
            </div>
        </form>
    </div>
</div>

<script src="../admin.js?v=16"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        checkAuth();
        _loadUnreadBadge();
        if (typeof initPortfolioPage === 'function') initPortfolioPage();
    });
</script>
</body>
</html>
