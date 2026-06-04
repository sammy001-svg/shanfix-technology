<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | Shanfix Technology</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="client.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .login-wrapper { min-height:100vh; display:flex; align-items:center; justify-content:center; background:#052607; position:relative; overflow:hidden; padding:2rem; }
        .blob { position:absolute; width:500px; height:500px; border-radius:50%; z-index:1; opacity:0.2; filter:blur(120px); animation:move 20s infinite alternate; }
        .blob:nth-child(1) { background:#22c55e; }
        .blob:nth-child(2) { background:#3b82f6; width:600px; height:600px; right:-100px; top:-100px; animation-duration:25s; animation-delay:-5s; }
        .blob:nth-child(3) { background:#10b981; width:400px; height:400px; left:-50px; bottom:-50px; animation-duration:30s; animation-delay:-10s; }
        @keyframes move { from { transform:translate(0,0) scale(1); } to { transform:translate(100px,100px) scale(1.2); } }
        .login-card { background:rgba(255,255,255,0.03); backdrop-filter:blur(30px); border:1px solid rgba(255,255,255,0.1); border-radius:40px; width:100%; max-width:460px; padding:3.5rem; box-shadow:0 40px 100px rgba(0,0,0,0.5); position:relative; z-index:10; }
    </style>
</head>
<body class="client-portal-body">
<div class="login-wrapper">
    <div class="blob"></div><div class="blob"></div><div class="blob"></div>
    <div class="login-card">
        <div style="text-align:center; margin-bottom:2.5rem;">
            <div style="width:64px; height:64px; background:rgba(99,102,241,0.15); border-radius:20px; display:flex; align-items:center; justify-content:center; margin:0 auto 1.5rem; border:1px solid rgba(99,102,241,0.3);">
                <i class="fas fa-lock-open" style="font-size:1.5rem; color:#a5b4fc;"></i>
            </div>
            <h1 style="font-family:'Outfit',sans-serif; font-size:1.8rem; font-weight:800; color:#fff; margin:0 0 8px;">Forgot Password?</h1>
            <p style="color:rgba(255,255,255,0.4); font-size:0.9rem; margin:0;">Enter your email and we'll send a reset link.</p>
        </div>

        <div id="successMsg" style="display:none; text-align:center; padding:1.5rem; background:rgba(34,197,94,0.1); border:1px solid rgba(34,197,94,0.3); border-radius:16px; margin-bottom:1.5rem;">
            <i class="fas fa-paper-plane" style="color:#22c55e; font-size:2rem; margin-bottom:1rem; display:block;"></i>
            <p style="color:#86efac; font-weight:600; margin:0;">Check your inbox! A reset link has been sent if that email is registered.</p>
        </div>

        <form id="forgotForm">
            <div class="input-group-premium" style="margin-bottom:1.5rem;">
                <input type="email" id="reset_email" class="form-control" placeholder=" " required autocomplete="email">
                <label for="reset_email">Your Email Address</label>
                <i class="fas fa-at"></i>
            </div>

            <div id="forgotError" style="display:none; color:#fca5a5; font-size:0.85rem; padding:10px 14px; background:rgba(239,68,68,0.1); border-radius:10px; margin-bottom:1rem;"></div>

            <button type="submit" id="forgotBtn" class="portal-btn-premium" style="width:100%; margin-bottom:1.5rem;">
                <span>Send Reset Link</span> <i class="fas fa-paper-plane"></i>
            </button>

            <p style="text-align:center; color:rgba(255,255,255,0.4); font-size:0.85rem;">
                <a href="login.php" style="color:#86efac; text-decoration:none; font-weight:600;">
                    <i class="fas fa-arrow-left" style="margin-right:6px;"></i>Back to Login
                </a>
            </p>
        </form>
    </div>
</div>
<script>
document.getElementById('forgotForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn     = document.getElementById('forgotBtn');
    const errBox  = document.getElementById('forgotError');
    const orig    = btn.innerHTML;
    btn.innerHTML = '<span>Sending...</span> <i class="fas fa-spinner fa-spin"></i>';
    btn.disabled  = true;
    errBox.style.display = 'none';

    try {
        const res  = await fetch('api/forgot.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email: document.getElementById('reset_email').value.trim() })
        });
        const data = await res.json();
        if (data.success) {
            document.getElementById('forgotForm').style.display = 'none';
            document.getElementById('successMsg').style.display = 'block';
        } else {
            errBox.textContent    = data.message;
            errBox.style.display  = 'block';
        }
    } catch (err) {
        errBox.textContent   = 'Connection error. Please try again.';
        errBox.style.display = 'block';
    } finally {
        btn.innerHTML = orig;
        btn.disabled  = false;
    }
});
</script>
</body>
</html>
