<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adverts - Shanfix Admin</title>
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
            <a href="receipts.php" class="admin-nav-item">
                <i class="fas fa-receipt"></i> Receipts
            </a>
            <a href="adverts.php" class="admin-nav-item active">
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
            <h1 class="admin-page-title">Manage Advertisements</h1>
            <div class="admin-user-profile">
                <span>Welcome, Admin</span>
                <div class="admin-avatar">A</div>
            </div>
        </header>

        <section class="admin-content">
            <div class="admin-card">
                <h2 class="mb-15">Hero Carousel Adverts</h2>
                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Slide #</th>
                                <th>Headline</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Innovative Tech Solutions</td>
                                <td><span class="text-primary">Active</span></td>
                                <td><button class="admin-btn admin-btn-primary">Edit</button></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Premium Printing & Branding</td>
                                <td><span class="text-primary">Active</span></td>
                                <td><button class="admin-btn admin-btn-primary">Edit</button></td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Expert Business Consultancy</td>
                                <td><span class="text-primary">Active</span></td>
                                <td><button class="admin-btn admin-btn-primary">Edit</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button class="admin-btn admin-btn-secondary mt-20">Add New Slide</button>
            </div>

            <div class="admin-card">
                <h2 class="mb-15">Page Banners</h2>
                <p class="text-muted">Manage banners appearing on services pages.</p>
                <div class="banners-grid">
                    <div class="banner-placeholder">
                        <i class="fas fa-info-circle banner-icon text-secondary"></i>
                        <div>Service Banner 1</div>
                    </div>
                    <div class="banner-placeholder">
                        <i class="fas fa-plus-circle banner-icon text-muted"></i>
                        <div>Add Banner</div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <script src="../admin.js?v=2"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof initAdvertsPage === 'function') initAdvertsPage();
        });
    </script>
</body>
</html>
