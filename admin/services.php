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
    <title>Service Management - Shanfix Admin</title>
    <link rel="stylesheet" href="../admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Outfit:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="admin-body">
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
            <a href="tickets.php" class="admin-nav-item"><i class="fas fa-life-ring"></i> <span>Support</span></a>
            <div class="admin-nav-divider"></div>
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
            <h1 class="admin-page-title">Service Management</h1>
            <div class="admin-header-actions">
                <button class="admin-btn admin-btn-primary" onclick="openServiceModal()">
                    <i class="fas fa-plus"></i> New Rate Card
                </button>
            </div>
        </header>

        <section class="admin-content">
            <div id="servicesContainer">
                <!-- Services loaded by admin.js -->
                <div class="loading-state py-50 text-center">
                    <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                    <p class="mt-15">Loading service catalog...</p>
                </div>
            </div>
        </section>
    </main>

    <!-- Service Rate Card Modal -->
    <div id="serviceModal" class="admin-modal">
        <div class="admin-modal-content">
            <div class="admin-modal-header">
                <h3 class="admin-modal-title" id="serviceModalTitle">Add New Rate Card</h3>
                <span class="admin-modal-close" onclick="closeServiceModal()">&times;</span>
            </div>
            <div class="admin-modal-body">
                <form id="serviceForm">
                    <input type="hidden" id="s_id">
                    <div class="form-row">
                        <div class="form-group col-6">
                            <label>Service Title</label>
                            <input type="text" id="s_name" class="form-control" required placeholder="e.g. Starter Hosting">
                        </div>
                        <div class="form-group col-6">
                            <label>Price (KES)</label>
                            <input type="number" id="s_price" class="form-control" required placeholder="5500">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-6">
                            <label>Category</label>
                            <select id="s_category" class="form-control" required></select>
                        </div>
                        <div class="form-group col-6">
                            <label>Status</label>
                            <select id="s_status" class="form-control">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <input type="text" id="s_desc" class="form-control" placeholder="Short tagline for the service">
                    </div>
                    <div class="form-group">
                        <label>Features (One per line)</label>
                        <textarea id="s_features" class="form-control" rows="5" placeholder="Free Domain&#10;50GB SSD Storage&#10;Unlimited Bandwidth"></textarea>
                    </div>
                    <div class="form-group">
                        <label class="flex-align-center gap-10 cursor-pointer">
                            <input type="checkbox" id="s_featured"> Mark as Featured/Most Popular
                        </label>
                    </div>
                </form>
            </div>
            <div class="admin-modal-footer">
                <button class="admin-btn admin-btn-secondary" onclick="closeServiceModal()">Cancel</button>
                <button type="submit" form="serviceForm" class="admin-btn admin-btn-primary">Save Rate Card</button>
            </div>
        </div>
    </div>

    <script src="../admin.js?v=13"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof initServicesPage === 'function') initServicesPage();
        });
    </script>
</body>
</html>
