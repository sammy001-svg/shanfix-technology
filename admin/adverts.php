<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adverts & Banners - Shanfix Admin</title>
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
            <a href="adverts.php" class="admin-nav-item active"><i class="fas fa-ad"></i> <span>Adverts</span></a>
            <a href="tickets.php" class="admin-nav-item"><i class="fas fa-life-ring"></i> <span>Support</span></a>
            <a href="messages.php" class="admin-nav-item"><i class="fas fa-inbox"></i> <span>Inbox</span><span id="sidebarMsgBadge" style="display:none; background:#ef4444; color:#fff; font-size:0.65rem; font-weight:800; padding:2px 6px; border-radius:20px; margin-left:auto;"></span></a>
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
                <h1 class="admin-page-title">Adverts & Banners</h1>
                <p class="admin-subtitle">Manage homepage hero slides and advertisement banners</p>
            </div>
            <div class="admin-user-profile">
                <div class="admin-avatar">A</div>
            </div>
        </header>

        <section class="admin-content">

            <!-- ── Hero Slides ─────────────────────────────────────── -->
            <div class="admin-card glass-card mb-40">
                <div class="flex-between mb-20">
                    <div>
                        <h3><i class="fas fa-images" style="color:var(--p); margin-right:8px;"></i> Hero Carousel Slides</h3>
                        <p class="text-low" style="font-size:0.85rem; margin-top:4px;">These appear in the rotating hero section at the top of the public homepage.</p>
                    </div>
                    <button class="admin-btn admin-btn-primary" onclick="openSlideModal()">
                        <i class="fas fa-plus"></i> Add Slide
                    </button>
                </div>

                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th style="width:50px;">Order</th>
                                <th>Headline</th>
                                <th>Subtitle</th>
                                <th>CTA Buttons</th>
                                <th>Background</th>
                                <th>Status</th>
                                <th style="text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="slidesTableBody">
                            <tr><td colspan="7" style="text-align:center; padding:2rem; color:var(--text-low);">Loading slides...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ── Ad Banners ──────────────────────────────────────── -->
            <div class="admin-card glass-card">
                <div class="flex-between mb-20">
                    <div>
                        <h3><i class="fas fa-image" style="color:var(--p); margin-right:8px;"></i> Advertisement Banners</h3>
                        <p class="text-low" style="font-size:0.85rem; margin-top:4px;">Image banners shown in the carousel below the feature cards on the homepage.</p>
                    </div>
                    <button class="admin-btn admin-btn-primary" onclick="openBannerModal()">
                        <i class="fas fa-upload"></i> Upload Banner
                    </button>
                </div>

                <div id="bannersGrid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(240px, 1fr)); gap:20px;">
                    <p class="text-low" style="grid-column:1/-1; text-align:center; padding:2rem;">Loading banners...</p>
                </div>
            </div>

        </section>
    </main>
</div>

<!-- ── Slide Modal ──────────────────────────────────────────────────────── -->
<div id="slideModal" class="modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:1000; align-items:center; justify-content:center;">
    <div class="admin-card glass-card" style="width:600px; max-width:95vw; max-height:90vh; overflow-y:auto; border-radius:20px;">
        <div class="flex-between mb-20">
            <h3 id="slideModalTitle">Add Hero Slide</h3>
            <button class="icon-btn" onclick="closeSlideModal()"><i class="fas fa-times"></i></button>
        </div>
        <form id="slideForm" enctype="multipart/form-data">
            <input type="hidden" id="slide_id" name="id">
            <input type="hidden" id="slide_existing_image" name="existing_image">
            <input type="hidden" name="type" value="slide">

            <div class="form-group mb-15">
                <label class="form-label">Headline <span style="color:var(--red)">*</span></label>
                <input type="text" id="slide_headline" name="headline" class="form-control" placeholder="e.g. Innovative Technology Solutions for" required>
            </div>
            <div class="form-group mb-15">
                <label class="form-label">Subtitle</label>
                <textarea id="slide_subtitle" name="subtitle" class="form-control" rows="3" placeholder="Supporting description text..."></textarea>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                <div class="form-group">
                    <label class="form-label">Button 1 Text</label>
                    <input type="text" id="slide_btn1_text" name="btn1_text" class="form-control" value="Explore Services">
                </div>
                <div class="form-group">
                    <label class="form-label">Button 1 Link</label>
                    <input type="text" id="slide_btn1_link" name="btn1_link" class="form-control" value="#services">
                </div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                <div class="form-group">
                    <label class="form-label">Button 2 Text <span class="text-low">(optional)</span></label>
                    <input type="text" id="slide_btn2_text" name="btn2_text" class="form-control" placeholder="Get in Touch">
                </div>
                <div class="form-group">
                    <label class="form-label">Button 2 Link</label>
                    <input type="text" id="slide_btn2_link" name="btn2_link" class="form-control" placeholder="contact.php">
                </div>
            </div>

            <div class="form-group mb-15">
                <label class="form-label">Background Image <span class="text-low">(JPG/PNG/WebP, landscape)</span></label>
                <div id="slideImgPreview" style="margin-bottom:8px;"></div>
                <input type="file" id="slide_image" name="image" class="form-control" accept="image/*"
                       onchange="previewImg(this, 'slideImgPreview')">
                <p class="text-low" style="font-size:0.75rem; margin-top:4px;">Leave empty to use the default gradient background.</p>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:20px;">
                <div class="form-group">
                    <label class="form-label">Sort Order</label>
                    <input type="number" id="slide_sort_order" name="sort_order" class="form-control" value="0" min="0">
                </div>
                <div class="form-group" style="display:flex; align-items:flex-end; gap:10px; padding-bottom:2px;">
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:0.9rem;">
                        <input type="checkbox" id="slide_is_active" name="is_active" checked style="width:16px; height:16px; accent-color:var(--p);">
                        Active (visible on site)
                    </label>
                </div>
            </div>

            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" class="admin-btn admin-btn-secondary" onclick="closeSlideModal()">Cancel</button>
                <button type="submit" class="admin-btn admin-btn-primary">
                    <i class="fas fa-save"></i> Save Slide
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ── Banner Modal ─────────────────────────────────────────────────────── -->
<div id="bannerModal" class="modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:1000; align-items:center; justify-content:center;">
    <div class="admin-card glass-card" style="width:480px; max-width:95vw; max-height:90vh; overflow-y:auto; border-radius:20px;">
        <div class="flex-between mb-20">
            <h3 id="bannerModalTitle">Upload Ad Banner</h3>
            <button class="icon-btn" onclick="closeBannerModal()"><i class="fas fa-times"></i></button>
        </div>
        <form id="bannerForm" enctype="multipart/form-data">
            <input type="hidden" id="banner_id" name="id">
            <input type="hidden" id="banner_existing_image" name="existing_image">
            <input type="hidden" name="type" value="banner">

            <div class="form-group mb-15">
                <label class="form-label">Banner Title <span class="text-low">(optional)</span></label>
                <input type="text" id="banner_title" name="title" class="form-control" placeholder="e.g. Ring Back Tone Promotion">
            </div>
            <div class="form-group mb-15">
                <label class="form-label">Image <span id="bannerImgRequired" style="color:var(--red)">*</span></label>
                <div id="bannerImgPreview" style="margin-bottom:8px;"></div>
                <input type="file" id="banner_image" name="image" class="form-control" accept="image/*"
                       onchange="previewImg(this, 'bannerImgPreview')">
            </div>
            <div class="form-group mb-15">
                <label class="form-label">Click URL <span class="text-low">(optional)</span></label>
                <input type="text" id="banner_link" name="link_url" class="form-control" placeholder="#services or https://...">
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:20px;">
                <div class="form-group">
                    <label class="form-label">Sort Order</label>
                    <input type="number" id="banner_sort_order" name="sort_order" class="form-control" value="0" min="0">
                </div>
                <div class="form-group" style="display:flex; align-items:flex-end; padding-bottom:2px;">
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:0.9rem;">
                        <input type="checkbox" id="banner_is_active" name="is_active" checked style="width:16px; height:16px; accent-color:var(--p);">
                        Active
                    </label>
                </div>
            </div>

            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" class="admin-btn admin-btn-secondary" onclick="closeBannerModal()">Cancel</button>
                <button type="submit" class="admin-btn admin-btn-primary">
                    <i class="fas fa-save"></i> Save Banner
                </button>
            </div>
        </form>
    </div>
</div>

<script src="../admin.js?v=14"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        checkAuth();
        if (typeof initAdvertsPage === 'function') initAdvertsPage();
    });
</script>
</body>
</html>
