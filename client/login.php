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
        :root {
            --blob-1: #22c55e;
            --blob-2: #3b82f6;
            --blob-3: #10b981;
        }

        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #052607;
            position: relative;
            overflow: hidden;
            padding: 2rem;
        }

        /* Animated Background Blobs */
        .blob {
            position: absolute;
            width: 500px;
            height: 500px;
            background: var(--blob-1);
            filter: blur(120px);
            border-radius: 50%;
            z-index: 1;
            opacity: 0.2;
            animation: move 20s infinite alternate;
        }

        .blob:nth-child(2) {
            background: var(--blob-2);
            width: 600px;
            height: 600px;
            right: -100px;
            top: -100px;
            animation-duration: 25s;
            animation-delay: -5s;
        }

        .blob:nth-child(3) {
            background: var(--blob-3);
            width: 400px;
            height: 400px;
            left: -50px;
            bottom: -50px;
            animation-duration: 30s;
            animation-delay: -10s;
        }

        @keyframes move {
            from { transform: translate(0, 0) scale(1); }
            to { transform: translate(100px, 100px) scale(1.2); }
        }

        .login-card {
            background: rgba(255, 255, 255, 0.03);
            -webkit-backdrop-filter: blur(30px);
            backdrop-filter: blur(30px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 40px;
            width: 100%;
            max-width: 500px;
            padding: 4rem;
            box-shadow: 0 40px 100px rgba(0, 0, 0, 0.5);
            position: relative;
            z-index: 10;
        }

        .login-card::after {
            content: '';
            position: absolute;
            inset: -1px;
            background: linear-gradient(135deg, rgba(255,255,255,0.2), transparent 40%, transparent 60%, rgba(255,255,255,0.1));
            border-radius: 40px;
            z-index: -1;
            pointer-events: none;
        }

        .login-header img {
            height: 65px;
            margin-bottom: 2rem;
            filter: drop-shadow(0 0 15px rgba(34, 197, 94, 0.4));
        }

        .login-title {
            color: white;
            font-size: clamp(2rem, 5vw, 2.8rem);
            margin: 0;
            letter-spacing: -1.5px;
            font-weight: 800;
        }

        .login-subtitle {
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.95rem;
            margin: 0.5rem 0 3.5rem 0;
            font-weight: 500;
        }

        /* Input Modernization */
        .input-group-premium {
            position: relative;
            margin-bottom: 2rem;
        }

        .input-group-premium i {
            position: absolute;
            left: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.3);
            font-size: 1.1rem;
            transition: all 0.3s;
        }

        .input-group-premium .form-control {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            padding: 1.4rem 1.5rem 1.4rem 3.5rem;
            border-radius: 20px;
            width: 100%;
            outline: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 1rem;
            box-sizing: border-box;
        }

        .input-group-premium .form-control:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: #22c55e;
            box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.15);
        }

        .input-group-premium .form-control:focus ~ i {
            color: #22c55e;
            transform: translateY(-50%) scale(1.1);
        }

        .input-group-premium label {
            position: absolute;
            left: 3.5rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.4);
            pointer-events: none;
            transition: all 0.3s ease;
        }

        .input-group-premium .form-control:focus ~ label,
        .input-group-premium .form-control:not(:placeholder-shown) ~ label {
            transform: translateY(-2.8rem) scale(0.85);
            color: #22c55e;
            font-weight: 700;
        }

        .portal-btn-premium {
            background: linear-gradient(135deg, #22c55e 0%, #10b981 100%);
            color: white;
            border: none;
            width: 100%;
            padding: 1.3rem;
            border-radius: 20px;
            font-weight: 800;
            font-size: 1.1rem;
            cursor: pointer;
            box-shadow: 0 10px 25px -5px rgba(34, 197, 94, 0.4);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin-top: 1rem;
        }

        .portal-btn-premium:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px -5px rgba(34, 197, 94, 0.6);
            filter: brightness(1.1);
        }

        .portal-btn-premium i {
            font-size: 1rem;
            transition: transform 0.3s;
        }

        .portal-btn-premium:hover i {
            transform: translateX(5px);
        }

        .login-footer-text {
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.95rem;
            margin-top: 3rem;
            text-align: center;
            line-height: 1.6;
        }

        .login-footer-text a {
            color: #22c55e;
            text-decoration: none;
            font-weight: 800;
            position: relative;
        }

        .login-footer-text a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: #22c55e;
            transition: width 0.3s;
        }

        .login-footer-text a:hover::after {
            width: 100%;
        }

        .hidden-form { display: none; }
        .active-form { display: block; animation: zoomIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1); }

        @keyframes zoomIn {
            from { opacity: 0; transform: scale(0.95) translateY(10px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
    </style>
</head>
<body class="client-portal-body">

    <div class="login-wrapper">
        <!-- Background Blobs -->
        <div class="blob"></div>
        <div class="blob"></div>
        <div class="blob"></div>

        <div class="login-card">
            
            <div class="login-header" style="text-align: center;">
                <img src="../assets/shanfix-logo.png" alt="Shanfix Logo">
                <h2 class="login-title outfit">Shanfix <span style="background: linear-gradient(to right, #4ade80, #3b82f6); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Portal</span></h2>
                <p class="login-subtitle">Advanced Technology Partnership Portal</p>
            </div>

            <!-- Login Form -->
            <form id="clientLoginForm" class="active-form">
                <div class="input-group-premium">
                    <input type="email" id="client_email" class="form-control" placeholder=" " required>
                    <label for="client_email">Business Email</label>
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="input-group-premium" style="margin-bottom: 1rem;">
                    <input type="password" id="client_password" class="form-control" placeholder=" " required>
                    <label for="client_password">Access Token</label>
                    <i class="fas fa-key"></i>
                </div>
                
                <div style="margin-bottom: 3rem; text-align: right;">
                    <a href="#" style="font-size: 0.85rem; color: rgba(255,255,255,0.3); text-decoration: none; transition: 0.3s;" onmouseover="this.style.color='#22c55e'" onmouseout="this.style.color='rgba(255,255,255,0.3)'">Forgot credentials?</a>
                </div>

                <button type="submit" class="portal-btn-premium">
                    <span>Secure Sign In</span> <i class="fas fa-shield-halved"></i>
                </button>

                <p class="login-footer-text">
                    New technology partner? <br>
                    <a href="#" onclick="toggleForm('register'); return false;">Request Portal Access</a>
                </p>
            </form>

            <!-- Registration Form -->
            <form id="clientRegForm" class="hidden-form">
                <div class="input-group-premium">
                    <input type="text" id="reg_name" class="form-control" placeholder=" " required>
                    <label for="reg_name">Entity Name</label>
                    <i class="fas fa-building"></i>
                </div>
                <div class="input-group-premium">
                    <input type="email" id="reg_email" class="form-control" placeholder=" " required>
                    <label for="reg_email">Contact Email</label>
                    <i class="fas fa-at"></i>
                </div>
                <div class="input-group-premium">
                    <input type="password" id="reg_password" class="form-control" placeholder=" " required>
                    <label for="reg_password">Create Access Token</label>
                    <i class="fas fa-lock"></i>
                </div>
                
                <button type="submit" class="portal-btn-premium">
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
