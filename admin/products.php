<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Shanfix Admin</title>
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
            <a href="products.php" class="admin-nav-item active">
                <i class="fas fa-box"></i> Products & Categories
            </a>
            <a href="invoices.php" class="admin-nav-item">
                <i class="fas fa-file-invoice"></i> Invoices
            </a>
            <a href="receipts.php" class="admin-nav-item">
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
            <h1 class="admin-page-title">Manage Products</h1>
            <div class="admin-user-profile">
                <span>Welcome, Admin</span>
                <div class="admin-avatar">A</div>
            </div>
        </header>

        <section class="admin-content">
            <div class="admin-card">
                <h2 class="mb-15">Add New Product</h2>
                <form id="productForm">
                    <div class="banners-grid">
                        <div class="form-group">
                            <label for="p_name">Product Name</label>
                            <input type="text" id="p_name" class="form-control" required placeholder="e.g. Premium Business Cards">
                        </div>
                        <div class="form-group">
                            <label for="p_price">Price (KES)</label>
                            <input type="number" id="p_price" class="form-control" required placeholder="e.g. 2500">
                        </div>
                        <div class="form-group">
                            <label for="p_category">Category</label>
                            <select id="p_category" class="form-control" required>
                                <!-- Categories will be loaded here via JS -->
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="p_image">Upload Image</label>
                            <input type="file" id="p_image" class="form-control" accept="image/*">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="p_desc">Description</label>
                        <textarea id="p_desc" class="form-control" rows="3" required placeholder="Describe the product..."></textarea>
                    </div>
                <button type="submit" class="admin-btn admin-btn-primary">Add Product</button>
                </form>
            </div>

            <div class="banners-grid">
                <!-- Manage Categories Section -->
                <div class="admin-card">
                    <h2 class="mb-15">Manage Categories</h2>
                    <form id="categoryForm">
                        <div class="form-group">
                            <label for="cat_name">Category Name</label>
                            <div class="flex-end-gap">
                                <input type="text" id="cat_name" class="form-control" required placeholder="e.g. Tshirt Branding">
                                <button type="submit" class="admin-btn admin-btn-primary">Add</button>
                            </div>
                        </div>
                    </form>
                    <div class="admin-table-container mt-20">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Category Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="categoryTableBody">
                                <!-- Categories will be loaded here via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Product List Section -->
                <div class="admin-card">
                    <h2 class="mb-15">Product List</h2>
                    <div class="admin-table-container">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="productTableBody">
                                <!-- Products will be loaded here via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <script src="../admin.js?v=10"></script>
</body>
</html>
