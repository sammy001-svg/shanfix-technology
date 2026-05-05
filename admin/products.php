<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Catalog - Shanfix Admin</title>
    <link rel="stylesheet" href="../admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Outfit:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .category-group { margin-bottom: 40px; }
        .category-title { 
            font-family: 'Outfit', sans-serif;
            font-size: 1.5rem;
            color: var(--p);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid var(--glass-border);
            padding-bottom: 10px;
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }
        .product-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
            border-color: var(--p);
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.1);
        }
        .product-image-container {
            height: 180px;
            position: relative;
            background: #f1f5f9;
        }
        .product-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .product-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: rgba(0,0,0,0.5);
            color: white;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.7rem;
            backdrop-filter: blur(4px);
        }
        .product-details { padding: 20px; }
        .product-name { 
            font-weight: 700; 
            font-size: 1.1rem; 
            margin-bottom: 8px;
            color: var(--text-main);
        }
        .product-price { 
            color: var(--s); 
            font-weight: 800; 
            font-size: 1.2rem;
            margin-bottom: 15px;
        }
        .product-actions {
            display: flex;
            gap: 10px;
            border-top: 1px solid var(--glass-border);
            padding-top: 15px;
        }
        .btn-icon-outline {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: 1px solid var(--glass-border);
            color: var(--text-low);
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-icon-outline:hover {
            background: var(--p);
            color: white;
            border-color: var(--p);
        }
    </style>
</head>
<body class="admin-body">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="admin-logo">Shanfix <span>Admin</span></div>
        <nav class="admin-nav">
            <a href="index.php" class="admin-nav-item"><i class="fas fa-chart-line"></i> <span>Insights</span></a>
            <a href="clients.php" class="admin-nav-item"><i class="fas fa-users"></i> <span>Clients</span></a>
            <a href="categories.php" class="admin-nav-item"><i class="fas fa-tags"></i> <span>Categories</span></a>
            <a href="products.php" class="admin-nav-item active"><i class="fas fa-box"></i> <span>Catalog</span></a>
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
            <h1 class="admin-page-title">Product Catalog</h1>
            <div class="admin-header-actions">
                <button class="admin-btn admin-btn-primary" onclick="openProductModal()">
                    <i class="fas fa-plus"></i> New Product
                </button>
            </div>
        </header>

        <section class="admin-content">
            <div id="catalogContainer">
                <!-- Grouped products by category loaded here -->
                <div class="admin-card text-center py-50">
                    <i class="fas fa-spinner fa-spin fa-2x mb-20" style="color: var(--p);"></i>
                    <p>Building catalog view...</p>
                </div>
            </div>
        </section>
    </main>

    <!-- Product Modal -->
    <div id="productModal" class="admin-modal">
        <div class="admin-modal-content" style="max-width: 800px;">
            <div class="admin-modal-header">
                <h3 class="admin-modal-title" id="prodModalTitle">Add New Product</h3>
                <span class="admin-modal-close" onclick="closeProductModal()">&times;</span>
            </div>
            <div class="admin-modal-body">
                <form id="productForm">
                    <input type="hidden" id="p_id">
                    <input type="hidden" id="p_existing_image">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Product Name</label>
                            <input type="text" id="p_name" class="form-control" required placeholder="e.g. Luxury Business Cards">
                        </div>
                        <div class="form-group">
                            <label>Category</label>
                            <select id="p_category" class="form-control admin-select-custom" required>
                                <!-- Categories loaded via JS -->
                            </select>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label>Base Price (KES)</label>
                            <input type="number" id="p_price" class="form-control" required placeholder="2500">
                        </div>
                        <div class="form-group">
                            <label>Account Status</label>
                            <select id="p_status" class="form-control admin-select-custom">
                                <option value="active">Active</option>
                                <option value="discontinued">Discontinued</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Primary Showcase Image</label>
                        <input type="file" id="p_primary_image" class="form-control" accept="image/*">
                        <div id="primaryPreview" class="mt-10"></div>
                    </div>

                    <div class="form-group">
                        <label>Gallery Images (Upload multiple)</label>
                        <input type="file" id="p_gallery_images" class="form-control" accept="image/*" multiple>
                        <div id="galleryPreview" class="mt-10 flex-align-center gap-10 overflow-x-auto"></div>
                    </div>

                    <div class="form-group">
                        <label>Product Description</label>
                        <textarea id="p_desc" class="form-control" rows="4" required placeholder="Detailed specifications, features, and benefits..."></textarea>
                    </div>

                    <div class="form-group flex-align-center gap-10">
                        <input type="checkbox" id="p_featured" style="width: 20px; height: 20px;">
                        <label for="p_featured" style="margin-bottom: 0;">Feature this product on homepage</label>
                    </div>
                </form>
            </div>
            <div class="admin-modal-footer">
                <button class="admin-btn admin-btn-secondary" onclick="closeProductModal()">Cancel</button>
                <button type="submit" form="productForm" class="admin-btn admin-btn-primary">Save Product</button>
            </div>
        </div>
    </div>

    <script src="../admin.js?v=13"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof initProductsPage === 'function') initProductsPage();
        });
    </script>
</body>
</html>
