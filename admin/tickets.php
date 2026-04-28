<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Tickets - Shanfix Admin</title>
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
                <i class="fas fa-box"></i> Products
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
            <a href="tickets.php" class="admin-nav-item active">
                <i class="fas fa-life-ring"></i> Support Tickets
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
            <h1 class="admin-page-title">Client Support Tickets</h1>
            <div class="admin-user-profile">
                <span>Welcome, Admin</span>
                <div class="admin-avatar">A</div>
            </div>
        </header>

        <section class="admin-content">
            <div class="admin-card">
                <div class="flex-end-gap mb-15">
                    <h2>Manage Open Tickets</h2>
                    <button class="admin-btn admin-btn-secondary" onclick="clearAllTickets()"><i class="fas fa-trash"></i> Clear All</button>
                </div>
                
                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Ticket ID</th>
                                <th>Client Email</th>
                                <th>Subject</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="adminTicketsBody">
                            <!-- Populated via admin.js -->
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>

    <script src="../admin.js?v=4"></script>
</body>
</html>
