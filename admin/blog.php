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
    <title>Blog & News - Shanfix Admin</title>
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
            <a href="portfolio.php" class="admin-nav-item"><i class="fas fa-briefcase"></i> <span>Portfolio</span></a>
            <a href="blog.php" class="admin-nav-item active"><i class="fas fa-newspaper"></i> <span>Blog</span></a>
            <a href="adverts.php" class="admin-nav-item"><i class="fas fa-ad"></i> <span>Adverts</span></a>
            <a href="tickets.php" class="admin-nav-item"><i class="fas fa-life-ring"></i> <span>Support</span></a>
            <a href="messages.php" class="admin-nav-item">
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

    <main class="admin-main">
        <header class="admin-header">
            <div class="admin-header-left">
                <h1 class="admin-page-title">Blog & News</h1>
                <p class="admin-subtitle">Publish articles, updates and insights for your audience</p>
            </div>
            <div class="admin-header-actions">
                <a href="../blog.php" target="_blank" class="admin-btn admin-btn-secondary">
                    <i class="fas fa-external-link-alt"></i> View Blog
                </a>
                <button class="admin-btn admin-btn-primary" onclick="openPostEditor()">
                    <i class="fas fa-plus"></i> New Post
                </button>
            </div>
        </header>

        <section class="admin-content">
            <!-- Stats row -->
            <div class="admin-stats-grid" style="margin-bottom:2rem;">
                <div class="admin-stat-card glass-card">
                    <div class="stat-icon" style="background:rgba(99,102,241,0.1); color:var(--p);"><i class="fas fa-file-alt"></i></div>
                    <div><div class="stat-label">Total Posts</div><div class="stat-value" id="blog_total">0</div></div>
                </div>
                <div class="admin-stat-card glass-card">
                    <div class="stat-icon" style="background:rgba(34,197,94,0.1); color:#22c55e;"><i class="fas fa-globe"></i></div>
                    <div><div class="stat-label">Published</div><div class="stat-value" id="blog_published">0</div></div>
                </div>
                <div class="admin-stat-card glass-card">
                    <div class="stat-icon" style="background:rgba(245,158,11,0.1); color:#f59e0b;"><i class="fas fa-pencil-alt"></i></div>
                    <div><div class="stat-label">Drafts</div><div class="stat-value" id="blog_drafts">0</div></div>
                </div>
                <div class="admin-stat-card glass-card">
                    <div class="stat-icon" style="background:rgba(6,182,212,0.1); color:#06b6d4;"><i class="fas fa-eye"></i></div>
                    <div><div class="stat-label">Total Views</div><div class="stat-value" id="blog_views">0</div></div>
                </div>
            </div>

            <div class="admin-card glass-card">
                <div class="flex-between mb-20">
                    <div class="flex-align-center gap-10">
                        <h3>All Posts</h3>
                    </div>
                    <select id="blogStatusFilter" class="form-control-sm" onchange="filterPosts(this.value)">
                        <option value="all">All Statuses</option>
                        <option value="published">Published</option>
                        <option value="draft">Drafts</option>
                    </select>
                </div>
                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Post</th>
                                <th>Category</th>
                                <th>Author</th>
                                <th>Views</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th style="text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="blogTableBody">
                            <tr><td colspan="7" style="text-align:center; padding:2.5rem; color:var(--text-low);">Loading posts...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
</div>

<!-- ── Post Editor Modal ─────────────────────────────────────────────────── -->
<div id="postEditorModal" class="modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); z-index:1000; align-items:flex-start; justify-content:center; overflow-y:auto; padding:2rem;">
    <div class="admin-card glass-card" style="width:900px; max-width:98vw; border-radius:20px; margin:auto;">
        <div class="flex-between mb-20">
            <h3 id="editorTitle">New Post</h3>
            <button class="icon-btn" onclick="closePostEditor()"><i class="fas fa-times"></i></button>
        </div>
        <form id="postForm" enctype="multipart/form-data">
            <input type="hidden" id="post_id" name="id">
            <input type="hidden" id="post_existing_image" name="existing_image">

            <div class="form-group mb-15">
                <label class="form-label">Post Title <span style="color:var(--red)">*</span></label>
                <input type="text" id="post_title" name="title" class="form-control" placeholder="Enter a compelling headline..." required style="font-size:1.1rem; font-weight:600;">
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <input type="text" id="post_category" name="category" class="form-control" placeholder="e.g. Technology, News, Tutorial" list="categoryList">
                    <datalist id="categoryList">
                        <option value="News">
                        <option value="Technology">
                        <option value="Tutorial">
                        <option value="Case Study">
                        <option value="Company Update">
                        <option value="Industry Insights">
                    </datalist>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select id="post_status" name="status" class="form-control">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                    </select>
                </div>
            </div>

            <div class="form-group mb-15">
                <label class="form-label">Excerpt <span class="text-low">(short summary for listing page)</span></label>
                <textarea id="post_excerpt" name="excerpt" class="form-control" rows="2" placeholder="Brief description shown in blog listing..."></textarea>
            </div>

            <div class="form-group mb-15">
                <label class="form-label">Featured Image</label>
                <div id="postImgPreview" style="margin-bottom:8px;"></div>
                <input type="file" id="post_image" name="featured_image" class="form-control" accept="image/*" onchange="previewImg(this, 'postImgPreview')">
            </div>

            <div class="form-group mb-20">
                <label class="form-label">Content <span style="color:var(--red)">*</span></label>
                <div id="postEditorToolbar" style="display:flex; gap:6px; flex-wrap:wrap; margin-bottom:8px; padding:10px; background:rgba(255,255,255,0.03); border:1px solid var(--glass-border); border-radius:12px 12px 0 0;">
                    <button type="button" onclick="execCmd('bold')" class="admin-btn-sm admin-btn-secondary" title="Bold"><i class="fas fa-bold"></i></button>
                    <button type="button" onclick="execCmd('italic')" class="admin-btn-sm admin-btn-secondary" title="Italic"><i class="fas fa-italic"></i></button>
                    <button type="button" onclick="execCmd('underline')" class="admin-btn-sm admin-btn-secondary" title="Underline"><i class="fas fa-underline"></i></button>
                    <div style="width:1px; background:var(--glass-border); margin:0 4px;"></div>
                    <button type="button" onclick="execCmd('formatBlock','h2')" class="admin-btn-sm admin-btn-secondary" title="Heading 2">H2</button>
                    <button type="button" onclick="execCmd('formatBlock','h3')" class="admin-btn-sm admin-btn-secondary" title="Heading 3">H3</button>
                    <button type="button" onclick="execCmd('formatBlock','p')" class="admin-btn-sm admin-btn-secondary" title="Paragraph">P</button>
                    <div style="width:1px; background:var(--glass-border); margin:0 4px;"></div>
                    <button type="button" onclick="execCmd('insertUnorderedList')" class="admin-btn-sm admin-btn-secondary" title="Bullet List"><i class="fas fa-list-ul"></i></button>
                    <button type="button" onclick="execCmd('insertOrderedList')" class="admin-btn-sm admin-btn-secondary" title="Numbered List"><i class="fas fa-list-ol"></i></button>
                    <div style="width:1px; background:var(--glass-border); margin:0 4px;"></div>
                    <button type="button" onclick="insertLink()" class="admin-btn-sm admin-btn-secondary" title="Insert Link"><i class="fas fa-link"></i></button>
                    <button type="button" onclick="execCmd('removeFormat')" class="admin-btn-sm admin-btn-secondary" title="Clear Formatting"><i class="fas fa-eraser"></i></button>
                </div>
                <div id="postContentEditor"
                     contenteditable="true"
                     style="min-height:320px; padding:20px; background:rgba(255,255,255,0.03); border:1px solid var(--glass-border); border-top:none; border-radius:0 0 12px 12px; color:var(--text-main); font-size:0.95rem; line-height:1.8; outline:none;"
                     placeholder="Start writing your post here...">
                </div>
                <textarea id="post_content" name="content" style="display:none;" required></textarea>
            </div>

            <div style="display:flex; gap:10px; justify-content:flex-end; padding-top:16px; border-top:1px solid var(--glass-border);">
                <button type="button" class="admin-btn admin-btn-secondary" onclick="closePostEditor()">Cancel</button>
                <button type="button" class="admin-btn admin-btn-secondary" onclick="savePostAs('draft')">
                    <i class="fas fa-save"></i> Save Draft
                </button>
                <button type="button" class="admin-btn admin-btn-primary" onclick="savePostAs('published')">
                    <i class="fas fa-globe"></i> Publish
                </button>
            </div>
        </form>
    </div>
</div>

<script src="../admin.js?v=18"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        checkAuth();
        _loadUnreadBadge();
        if (typeof initBlogPage === 'function') initBlogPage();
    });

    function execCmd(cmd, val) {
        document.getElementById('postContentEditor').focus();
        document.execCommand(cmd, false, val || null);
    }
    function insertLink() {
        const url = prompt('Enter URL:');
        if (url) document.execCommand('createLink', false, url);
    }
    window.savePostAs = function(status) {
        document.getElementById('post_status').value   = status;
        document.getElementById('post_content').value  = document.getElementById('postContentEditor').innerHTML;
        const fd  = new FormData(document.getElementById('postForm'));
        fd.set('action', document.getElementById('post_id').value ? 'update' : 'create');
        fd.set('status', status);
        const btn = status === 'published'
            ? document.querySelector('[onclick="savePostAs(\'published\')"]')
            : document.querySelector('[onclick="savePostAs(\'draft\')"]');
        const orig = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled  = true;
        fetch('api/blog.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    closePostEditor();
                    if (typeof loadPosts === 'function') loadPosts();
                    alert(data.message);
                } else alert(data.message);
            })
            .catch(() => alert('Save failed.'))
            .finally(() => { btn.innerHTML = orig; btn.disabled = false; });
    };
</script>
</body>
</html>
