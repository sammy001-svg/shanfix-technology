<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - Shanfix Admin</title>
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
            <a href="categories.php" class="admin-nav-item active"><i class="fas fa-tags"></i> <span>Categories</span></a>
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
            <h1 class="admin-page-title">Service Categories</h1>
            <div class="admin-header-actions">
                <button class="admin-btn admin-btn-primary" onclick="openCategoryModal()">
                    <i class="fas fa-plus"></i> Add Category
                </button>
            </div>
        </header>

        <section class="admin-content">
            <div class="admin-card">
                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Visual</th>
                                <th>Category Name</th>
                                <th>Description</th>
                                <th>Products</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="categoryTableBody">
                            <!-- Categories loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>

    <!-- Category Modal -->
    <div id="categoryModal" class="admin-modal">
        <div class="admin-modal-content">
            <div class="admin-modal-header">
                <h3 class="admin-modal-title" id="catModalTitle">Add New Category</h3>
                <span class="admin-modal-close" onclick="closeCategoryModal()">&times;</span>
            </div>
            <div class="admin-modal-body">
                <form id="categoryForm">
                    <input type="hidden" id="cat_id">
                    <input type="hidden" id="cat_existing_image">
                    <div class="form-group">
                        <label>Category Name</label>
                        <input type="text" id="cat_name" class="form-control" required placeholder="e.g. Website Branding">
                    </div>
                    <div class="form-group">
                        <label>Category Thumbnail</label>
                        <input type="file" id="cat_image" class="form-control" accept="image/*">
                        <div id="catImagePreview" class="mt-10"></div>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea id="cat_desc" class="form-control" rows="3" placeholder="Briefly describe what this category includes..."></textarea>
                    </div>
                </form>
            </div>
            <div class="admin-modal-footer">
                <button class="admin-btn admin-btn-secondary" onclick="closeCategoryModal()">Cancel</button>
                <button type="submit" form="categoryForm" class="admin-btn admin-btn-primary">Save Category</button>
            </div>
        </div>
    </div>

    <script src="../admin.js?v=13"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof initCategoriesPage === 'function') initCategoriesPage();
        });
    </script>
</body>
</html>
