<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - Shanfix Admin</title>
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
                <a href="index.php" class="admin-nav-item">
                    <i class="fas fa-chart-line"></i> <span>Insights</span>
                </a>
                <a href="clients.php" class="admin-nav-item">
                    <i class="fas fa-users"></i> <span>Clients</span>
                </a>
                <a href="products.php" class="admin-nav-item">
                    <i class="fas fa-box"></i> <span>Catalog</span>
                </a>
                <a href="orders.php" class="admin-nav-item active">
                    <i class="fas fa-shopping-bag"></i> <span>Orders</span>
                </a>
                <a href="invoices.php" class="admin-nav-item">
                    <i class="fas fa-file-invoice"></i> <span>Billing</span>
                </a>
                <a href="receipts.php" class="admin-nav-item">
                    <i class="fas fa-receipt"></i> <span>Receipts</span>
                </a>
                <a href="adverts.php" class="admin-nav-item">
                    <i class="fas fa-ad"></i> <span>Adverts</span>
                </a>
                <a href="tickets.php" class="admin-nav-item">
                    <i class="fas fa-life-ring"></i> <span>Support</span>
                </a>
                <div class="admin-nav-divider"></div>
                <a href="../index.php" class="admin-nav-item">
                    <i class="fas fa-external-link-alt"></i> <span>Live Site</span>
                </a>
            </nav>
            <div class="admin-sidebar-footer">
                <a href="login.php" class="admin-nav-item admin-footer-link" onclick="sessionStorage.clear()">
                    <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <header class="admin-header">
                <h1 class="admin-page-title">Orders Management</h1>
                <div class="admin-user-profile">
                    <span>Welcome, Admin</span>
                    <div class="admin-avatar">A</div>
                </div>
            </header>

            <section class="admin-content">
                <div class="admin-card">
                    <div class="flex-between-center mb-20">
                        <h2>Client Orders</h2>
                        <div class="flex-gap">
                            <select id="orderStatusFilter" class="form-control" style="width: 150px;">
                                <option value="all">All Orders</option>
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="ready">Ready</option>
                                <option value="delivered">Delivered</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="admin-table-container">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Client</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="orderTableBody">
                                <!-- Populated via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Order Details Modal -->
    <div id="orderDetailsModal" class="admin-modal">
        <div class="admin-modal-content">
            <div class="admin-modal-header">
                <h3 class="admin-modal-title">Order Details</h3>
                <span class="admin-modal-close" onclick="closeOrderModal()">&times;</span>
            </div>
            <div class="admin-modal-body" id="orderDetailsArea">
                <!-- Order info injected here -->
            </div>
            <div class="admin-modal-footer">
                <button class="admin-btn admin-btn-secondary" onclick="closeOrderModal()">Close</button>
            </div>
        </div>
    </div>

    <script src="../admin.js?v=13"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof initOrdersPage === 'function') initOrdersPage();
        });
    </script>
</body>
</html>
