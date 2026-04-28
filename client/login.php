<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shanfix Client Portal - Login & Register</title>
    <link rel="stylesheet" href="../admin.css">
    <link rel="stylesheet" href="client.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* Standalone overrides for client login */
        .login-wrapper { background: var(--client-bg); }
        .login-card { border-radius: 20px; box-shadow: 0 20px 40px rgba(8, 63, 12, 0.05); }
        .login-logo { color: var(--client-primary); }
    </style>
</head>
<body class="client-portal-body" style="display:block; min-height: 100vh;">
    <div class="login-wrapper">
        <div class="login-card">
            
            <div class="unified-auth-header" style="justify-content: center; margin-bottom: 2rem;">
                <div class="login-logo" style="text-align:center; font-size: 2rem;">Shanfix <span style="color:var(--client-secondary)">Client Portal</span></div>
            </div>

            <!-- Client Login / Registration Area -->
            <div id="clientAuthArea" class="auth-area active">
                <p class="login-subtitle" style="text-align:center; margin-bottom: 2rem;">Access your client dashboard</p>
                
                <!-- Client Login Form -->
                <form id="clientLoginForm" class="active-form">
                    <div class="form-group form-group-premium">
                        <input type="email" id="client_email" class="form-control" placeholder=" " required>
                        <label for="client_email">Email Address</label>
                    </div>
                    <div class="form-group form-group-premium">
                        <input type="password" id="client_password" class="form-control" placeholder=" " required>
                        <label for="client_password">Password</label>
                    </div>
                    <button type="submit" class="login-btn portal-btn-primary" style="width:100%; border-radius: 12px; padding: 15px;">Login to Portal</button>
                    <div class="login-footer mt-15" style="text-align:center;">
                        <p>Don't have an account? <br><a href="#" onclick="toggleClientMode('register'); return false;" style="color:var(--client-secondary); font-weight:bold; display:inline-block; margin-top:10px;">Create Client Portal</a></p>
                    </div>
                </form>

                <!-- Client Registration Form -->
                <form id="clientRegForm" class="hidden-form">
                    <div class="form-group form-group-premium">
                        <input type="text" id="reg_name" class="form-control" placeholder=" " required>
                        <label for="reg_name">Company / Full Name</label>
                    </div>
                    <div class="form-group form-group-premium">
                        <input type="email" id="reg_email" class="form-control" placeholder=" " required>
                        <label for="reg_email">Email Address</label>
                    </div>
                    <div class="form-group form-group-premium">
                        <input type="password" id="reg_password" class="form-control" placeholder=" " required>
                        <label for="reg_password">Create Password</label>
                    </div>
                    <button type="submit" class="login-btn portal-btn-primary" style="width:100%; border-radius: 12px; padding: 15px;">Create Portal Account</button>
                    <div class="login-footer mt-15" style="text-align:center;">
                        <p>Already have an account? <br><a href="#" onclick="toggleClientMode('login'); return false;" style="color:var(--client-secondary); font-weight:bold; display:inline-block; margin-top:10px;">Login Here</a></p>
                    </div>
                </form>
            </div>

        </div>
    </div>
    
    <!-- We will use auth.js to hold the logic instead of client.js inside login -->
    <script src="auth.js"></script>
</body>
</html>
