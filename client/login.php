<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner Login | Shanfix Technology</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <!-- Styles -->
    <link rel="stylesheet" href="client.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--p);
            background-image: 
                radial-gradient(circle at 10% 10%, rgba(34, 197, 94, 0.1) 0%, transparent 40%),
                radial-gradient(circle at 90% 90%, rgba(239, 68, 68, 0.05) 0%, transparent 40%),
                url('https://www.transparenttextures.com/patterns/carbon-fibre.png');
            padding: 2rem;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.02);
            -webkit-backdrop-filter: blur(25px);
            backdrop-filter: blur(25px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 40px;
            width: 100%;
            max-width: 480px;
            padding: 4.5rem;
            box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.6);
            position: relative;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--grad-accent);
            border-radius: 40px 40px 0 0;
        }

        .login-header img {
            height: 55px;
            margin-bottom: 1.5rem;
            filter: brightness(0) invert(1);
        }

        .login-title {
            color: white;
            font-size: 2.4rem;
            margin: 0;
            letter-spacing: -1px;
        }

        .login-subtitle {
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.9rem;
            margin: 0.5rem 0 3.5rem 0;
        }

        /* Specialized overrides for the dark login card inputs */
        .login-card .form-control {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .login-card .form-control:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--s);
            box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.2);
        }

        .login-card label {
            color: rgba(255, 255, 255, 0.5);
        }

        .login-card .form-control:focus + label,
        .login-card .form-control:not(:placeholder-shown) + label {
            color: var(--color-accent);
        }

        .login-footer-text {
            color: rgba(255, 255, 255, 0.3);
            font-size: 0.9rem;
            margin-top: 3.5rem;
            text-align: center;
            line-height: 1.6;
        }

        .login-footer-text a {
            color: var(--color-accent);
            text-decoration: none;
            font-weight: 700;
        }

        .hidden-form { display: none; }
        .active-form { display: block; animation: slideUp 0.5s ease-out; }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="client-portal-body">

    <div class="login-wrapper">
        <div class="login-card">
            
            <div class="login-header" style="text-align: center;">
                <img src="../assets/shanfix-logo.png" alt="Shanfix Logo">
                <h2 class="login-title outfit">Shanfix <span style="color:var(--color-accent)">Portal</span></h2>
                <p class="login-subtitle">Technology Partnership Management</p>
            </div>

            <!-- Login Form -->
            <form id="clientLoginForm" class="active-form">
                <div class="form-group-premium">
                    <input type="email" id="client_email" class="form-control" placeholder=" " required>
                    <label for="client_email">Business Email</label>
                </div>
                <div class="form-group-premium" style="margin-bottom: 1rem;">
                    <input type="password" id="client_password" class="form-control" placeholder=" " required>
                    <label for="client_password">Access Token</label>
                </div>
                
                <div style="margin-bottom: 3rem; text-align: right;">
                    <a href="#" style="font-size: 0.8rem; color: rgba(255,255,255,0.3); text-decoration: none;">Forgot credentials?</a>
                </div>

                <button type="submit" class="portal-btn-primary" style="width: 100%; padding: 1.3rem;">
                    <span>Secure Sign In</span> <i class="fas fa-arrow-right"></i>
                </button>

                <p class="login-footer-text">
                    New technology partner? <br>
                    <a href="#" onclick="toggleForm('register'); return false;">Request Portal Access</a>
                </p>
            </form>

            <!-- Registration Form -->
            <form id="clientRegForm" class="hidden-form">
                <div class="form-group-premium">
                    <input type="text" id="reg_name" class="form-control" placeholder=" " required>
                    <label for="reg_name">Entity Name</label>
                </div>
                <div class="form-group-premium">
                    <input type="email" id="reg_email" class="form-control" placeholder=" " required>
                    <label for="reg_email">Contact Email</label>
                </div>
                <div class="form-group-premium">
                    <input type="password" id="reg_password" class="form-control" placeholder=" " required>
                    <label for="reg_password">Create Access Token</label>
                </div>
                
                <button type="submit" class="portal-btn-primary" style="width: 100%; padding: 1.3rem;">
                    <span>Submit Request</span> <i class="fas fa-paper-plane"></i>
                </button>

                <p class="login-footer-text">
                    Already have access? <br>
                    <a href="#" onclick="toggleForm('login'); return false;">Partner Sign In</a>
                </p>
            </form>

        </div>
    </div>

    <script>
        function toggleForm(mode) {
            const loginForm = document.getElementById('clientLoginForm');
            const regForm = document.getElementById('clientRegForm');
            
            if (mode === 'register') {
                loginForm.classList.remove('active-form');
                loginForm.classList.add('hidden-form');
                regForm.classList.add('active-form');
                regForm.classList.remove('hidden-form');
            } else {
                regForm.classList.remove('active-form');
                regForm.classList.add('hidden-form');
                loginForm.classList.add('active-form');
                loginForm.classList.remove('hidden-form');
            }
        }
    </script>
    <script src="auth.js"></script>
</body>
</html>
