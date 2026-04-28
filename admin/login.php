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
            
            <div class="unified-auth-header">
                <div class="login-logo">Shanfix Portal</div>
                <div class="auth-tabs">
                    <button class="auth-tab-btn active" onclick="switchAuthTab('client')">Client Portal</button>
                    <button class="auth-tab-btn" onclick="switchAuthTab('admin')">Admin</button>
                </div>
            </div>

            <!-- Client Login / Registration Area -->
            <div id="clientAuthArea" class="auth-area active">
                <p class="login-subtitle">Access your client dashboard</p>
                
                <!-- Client Login Form -->
                <form id="clientLoginForm" class="active-form">
                    <div class="form-group">
                        <label for="client_email">Email Address</label>
                        <input type="email" id="client_email" class="form-control" placeholder="you@company.com" required>
                    </div>
                    <div class="form-group">
                        <label for="client_password">Password</label>
                        <input type="password" id="client_password" class="form-control" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="login-btn">Login to Portal</button>
                    <div class="login-footer mt-15">
                        <p>Don't have an account? <a href="#" onclick="toggleClientMode('register'); return false;">Create Client Portal</a></p>
                    </div>
                </form>

                <!-- Client Registration Form -->
                <form id="clientRegForm" class="hidden-form">
                    <div class="form-group">
                        <label for="reg_name">Company / Full Name</label>
                        <input type="text" id="reg_name" class="form-control" placeholder="Your Business" required>
                    </div>
                    <div class="form-group">
                        <label for="reg_email">Email Address</label>
                        <input type="email" id="reg_email" class="form-control" placeholder="you@company.com" required>
                    </div>
                    <div class="form-group">
                        <label for="reg_password">Create Password</label>
                        <input type="password" id="reg_password" class="form-control" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="login-btn">Create Portal Account</button>
                    <div class="login-footer mt-15">
                        <p>Already have an account? <a href="#" onclick="toggleClientMode('login'); return false;">Login Here</a></p>
                    </div>
                </form>
            </div>

            <!-- Admin Area -->
            <div id="adminAuthArea" class="auth-area hidden-form">
                <p class="login-subtitle">System Administrator Login</p>
                
                <form id="adminLoginForm">
                    <div class="form-group">
                        <label for="email">Admin Email</label>
                        <input type="email" id="email" class="form-control" placeholder="admin@shanfix.com" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" class="form-control" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="login-btn">Sign In as Admin</button>
                </form>
                <div class="login-footer mt-15">
                    <p>Default: admin@shanfix.com / admin123</p>
                </div>
            </div>

        </div>
    </div>
    <script src="../admin.js?v=3"></script>
</body>
</html>
