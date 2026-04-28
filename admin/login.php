<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Shanfix Technology</title>
    <link rel="stylesheet" href="../admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Outfit:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="admin-body">
    <div class="login-wrapper">
        <div class="login-card">
            
            <div class="login-header">
                <h1 class="login-logo">Shanfix <span>Admin</span></h1>
                <p class="login-subtitle">Secure System Administrator Login</p>
            </div>

            <form id="adminLoginForm">
                <div class="form-group">
                    <label for="email">Administrator Email</label>
                    <input type="email" id="email" class="form-control" placeholder="admin@shanfix.com" required>
                </div>
                <div class="form-group">
                    <label for="password">Security Password</label>
                    <input type="password" id="password" class="form-control" placeholder="••••••••" required>
                </div>
                
                <button type="submit" class="login-btn">
                    <i class="fas fa-shield-halved"></i> Authorize Access
                </button>

                <div class="login-footer">
                    <p><i class="fas fa-circle-info"></i> Default: admin@shanfix.com / admin123</p>
                </div>
            </form>

        </div>
    </div>
    <script src="../admin.js?v=4"></script>
</body>
</html>
