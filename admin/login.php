<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Shanfix Technology</title>
    <link rel="stylesheet" href="../admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Outfit:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --security-1: #083f0c;
            --security-2: #22c55e;
            --security-3: #1e3a8a;
        }

        .login-wrapper {
            min-height: 100vh;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #030816;
            position: relative;
            overflow: hidden;
            padding: 2rem;
            font-family: 'Inter', sans-serif;
        }

        /* Security Animated Blobs */
        .sec-blob {
            position: absolute;
            width: 600px;
            height: 600px;
            background: var(--security-1);
            filter: blur(140px);
            border-radius: 50%;
            z-index: 1;
            opacity: 0.15;
            animation: drift 25s infinite alternate ease-in-out;
        }

        .sec-blob:nth-child(2) {
            background: var(--security-2);
            width: 500px;
            height: 500px;
            right: -150px;
            bottom: -150px;
            animation-duration: 20s;
            animation-delay: -7s;
        }

        .sec-blob:nth-child(3) {
            background: var(--security-3);
            width: 450px;
            height: 450px;
            left: -100px;
            top: -100px;
            animation-duration: 30s;
            animation-delay: -12s;
        }

        @keyframes drift {
            from { transform: translate(-10%, -10%) scale(1); }
            to { transform: translate(10%, 10%) scale(1.1); }
        }

        .login-card {
            background: rgba(255, 255, 255, 0.02);
            -webkit-backdrop-filter: blur(35px);
            backdrop-filter: blur(35px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 40px;
            width: 100%;
            max-width: 500px;
            padding: 4.5rem;
            box-shadow: 0 50px 120px rgba(0, 0, 0, 0.7);
            position: relative;
            z-index: 10;
        }

        .login-card::after {
            content: '';
            position: absolute;
            inset: -1px;
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.3), transparent 50%, rgba(30, 58, 138, 0.2));
            border-radius: 40px;
            z-index: -1;
            pointer-events: none;
        }

        .login-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .login-logo {
            font-family: 'Outfit', sans-serif;
            font-size: 2.6rem;
            font-weight: 800;
            color: white;
            margin: 0;
            letter-spacing: -1.5px;
        }

        .login-logo span {
            background: linear-gradient(to right, #22c55e, #10b981);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .login-subtitle {
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.95rem;
            margin-top: 0.5rem;
            font-weight: 500;
        }

        /* Admin Input Groups */
        .admin-input-premium {
            position: relative;
            margin-bottom: 2rem;
        }

        .admin-input-premium i {
            position: absolute;
            left: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.2);
            font-size: 1.1rem;
            transition: 0.3s;
        }

        .admin-input-premium .form-control {
            width: 100%;
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-radius: 20px;
            padding: 1.4rem 1.5rem 1.4rem 3.5rem;
            color: white !important;
            font-size: 1rem;
            outline: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-sizing: border-box;
        }

        .admin-input-premium .form-control:focus {
            background: rgba(255, 255, 255, 0.1) !important;
            border-color: #22c55e !important;
            box-shadow: 0 0 0 5px rgba(34, 197, 94, 0.2) !important;
        }

        .admin-input-premium .form-control:focus ~ i {
            color: #22c55e;
            transform: translateY(-50%) scale(1.1);
        }

        .admin-input-premium label {
            position: absolute;
            left: 3.5rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.3);
            pointer-events: none;
            transition: 0.3s ease;
        }

        .admin-input-premium .form-control:focus ~ label,
        .admin-input-premium .form-control:not(:placeholder-shown) ~ label {
            transform: translateY(-3rem) scale(0.85);
            color: #22c55e;
            font-weight: 700;
        }

        .admin-login-btn {
            width: 100%;
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            color: white;
            border: none;
            padding: 1.3rem;
            border-radius: 20px;
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 1.1rem;
            cursor: pointer;
            box-shadow: 0 10px 30px rgba(34, 197, 94, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            margin-top: 1rem;
        }

        .admin-login-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 45px rgba(34, 197, 94, 0.4);
            filter: brightness(1.1);
        }

        .admin-login-btn i {
            font-size: 1.2rem;
        }

        .login-footer {
            margin-top: 3.5rem;
            color: rgba(255, 255, 255, 0.2);
            font-size: 0.85rem;
            text-align: center;
        }

        .login-footer i { margin-right: 0.5rem; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-card {
            animation: fadeIn 0.8s ease-out;
        }
    </style>
</head>

<body class="admin-body">
    <div class="login-wrapper">
        <!-- Background Decor -->
        <div class="sec-blob"></div>
        <div class="sec-blob"></div>
        <div class="sec-blob"></div>

        <div class="login-card">
            
            <div class="login-header">
                <h1 class="login-logo">Shanfix <span>Admin</span></h1>
                <p class="login-subtitle">Secure System Administrator Environment</p>
            </div>

            <form id="adminLoginForm">
                <div class="admin-input-premium">
                    <input type="email" id="email" class="form-control" placeholder=" " required>
                    <label for="email">Admin Identifier</label>
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="admin-input-premium">
                    <input type="password" id="password" class="form-control" placeholder=" " required>
                    <label for="password">Security Token</label>
                    <i class="fas fa-key"></i>
                </div>
                
                <button type="submit" class="admin-login-btn">
                    <span>Authorize Entry</span> <i class="fas fa-fingerprint"></i>
                </button>

                <div class="login-footer">
                    <p><i class="fas fa-circle-info"></i> Protected by Shanfix Security Layer</p>
                    <p style="margin-top: 0.5rem; opacity: 0.5;">Access is restricted to authorized personnel only.</p>
                </div>
            </form>

        </div>
    </div>
    <script src="../admin.js?v=4"></script>
</body>
</html>
