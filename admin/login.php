<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shanfix Technology - Login Portal</title>
    <link rel="stylesheet" href="../admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="admin-body">
    <div class="login-wrapper">
        <div class="login-card unified-login-card">
            
            <div class="login-header">
                <div class="login-logo">Shanfix Admin</div>
                <p class="login-subtitle">System Administrator Login</p>
            </div>

            <form id="adminLoginForm">
                <div class="form-group">
                    <label for="email">Admin Email</label>
                    <input type="email" id="email" class="form-control" placeholder="admin@shanfix.com" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" class="form-control" placeholder="••••••••" required>
                </div>
                <button type="submit" class="login-btn">Sign In</button>
                <div class="login-footer mt-15">
                    <p>Default: admin@shanfix.com / admin123</p>
                </div>
            </form>

        </div>
    </div>
    <script src="../admin.js?v=3"></script>
</body>
</html>
