<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Shanfix Admin</title>
    <link rel="stylesheet" href="../admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Outfit:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .login-wrapper { min-height:100vh; width:100%; display:flex; align-items:center; justify-content:center; background:#030816; position:relative; overflow:hidden; padding:2rem; font-family:'Inter',sans-serif; }
        .sec-blob { position:absolute; width:600px; height:600px; background:#083f0c; filter:blur(140px); border-radius:50%; z-index:1; opacity:0.15; animation:drift 25s infinite alternate ease-in-out; }
        .sec-blob:nth-child(2) { background:#22c55e; width:500px; height:500px; right:-150px; bottom:-150px; animation-duration:20s; animation-delay:-7s; }
        .sec-blob:nth-child(3) { background:#1e3a8a; width:450px; height:450px; left:-100px; top:-100px; animation-duration:30s; animation-delay:-12s; }
        @keyframes drift { from { transform:translate(-10%,-10%) scale(1); } to { transform:translate(10%,10%) scale(1.1); } }
        .login-card { background:rgba(255,255,255,0.02); backdrop-filter:blur(35px); border:1px solid rgba(255,255,255,0.08); border-radius:40px; width:100%; max-width:460px; padding:4rem; box-shadow:0 50px 120px rgba(0,0,0,0.7); position:relative; z-index:10; animation:fadeIn 0.8s ease-out; }
        @keyframes fadeIn { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
        .admin-input-premium { position:relative; margin-bottom:2rem; }
        .admin-input-premium i { position:absolute; left:1.5rem; top:50%; transform:translateY(-50%); color:rgba(255,255,255,0.2); }
        .admin-input-premium .form-control { width:100%; background:rgba(255,255,255,0.05)!important; border:1px solid rgba(255,255,255,0.1)!important; border-radius:20px; padding:1.4rem 1.5rem 1.4rem 3.5rem; color:white!important; font-size:1rem; outline:none; transition:all 0.3s; box-sizing:border-box; }
        .admin-input-premium .form-control:focus { background:rgba(255,255,255,0.1)!important; border-color:#22c55e!important; box-shadow:0 0 0 5px rgba(34,197,94,0.2)!important; }
        .admin-input-premium .form-control:focus~i { color:#22c55e; }
        .admin-input-premium label { position:absolute; left:3.5rem; top:50%; transform:translateY(-50%); color:rgba(255,255,255,0.3); pointer-events:none; transition:0.3s ease; }
        .admin-input-premium .form-control:focus~label, .admin-input-premium .form-control:not(:placeholder-shown)~label { transform:translateY(-3rem) scale(0.85); color:#22c55e; font-weight:700; }
        .admin-login-btn { width:100%; background:linear-gradient(135deg,#22c55e,#16a34a); color:white; border:none; padding:1.3rem; border-radius:20px; font-family:'Outfit',sans-serif; font-weight:800; font-size:1.1rem; cursor:pointer; box-shadow:0 10px 30px rgba(34,197,94,0.3); display:flex; align-items:center; justify-content:center; gap:1rem; transition:all 0.4s; margin-top:1rem; }
        .admin-login-btn:hover { transform:translateY(-4px); box-shadow:0 20px 45px rgba(34,197,94,0.4); }
        .admin-login-btn:disabled { opacity:0.6; transform:none; }
    </style>
</head>
<body class="admin-body">
<div class="login-wrapper">
    <div class="sec-blob"></div><div class="sec-blob"></div><div class="sec-blob"></div>
    <div class="login-card">
        <div style="text-align:center; margin-bottom:3rem;">
            <h1 style="font-family:'Outfit',sans-serif; font-size:2rem; font-weight:800; color:white; margin:0 0 8px; letter-spacing:-1px;">Shanfix <span style="background:linear-gradient(to right,#22c55e,#10b981); -webkit-background-clip:text; -webkit-text-fill-color:transparent;">Admin</span></h1>
            <p style="color:rgba(255,255,255,0.4); font-size:0.9rem; margin:0;">Reset your administrator password</p>
        </div>

        <div id="successMsg" style="display:none; text-align:center; padding:1.5rem; background:rgba(34,197,94,0.08); border:1px solid rgba(34,197,94,0.2); border-radius:16px; margin-bottom:1.5rem;">
            <i class="fas fa-paper-plane" style="color:#22c55e; font-size:1.8rem; margin-bottom:1rem; display:block;"></i>
            <p style="color:#86efac; font-weight:600; margin:0;">If that email is registered, a reset link has been sent.</p>
        </div>

        <form id="forgotForm">
            <div class="admin-input-premium">
                <input type="email" id="reset_email" class="form-control" placeholder=" " required autocomplete="email">
                <label for="reset_email">Admin Email Address</label>
                <i class="fas fa-user-shield"></i>
            </div>

            <div id="forgotError" style="display:none; color:#fca5a5; font-size:0.85rem; padding:10px 14px; background:rgba(239,68,68,0.08); border-radius:10px; margin-bottom:1rem;"></div>

            <button type="submit" id="forgotBtn" class="admin-login-btn">
                <span>Send Reset Link</span> <i class="fas fa-paper-plane"></i>
            </button>
        </form>

        <div style="text-align:center; margin-top:2rem;">
            <a href="login.php" style="color:rgba(255,255,255,0.3); font-size:0.85rem; text-decoration:none;">
                <i class="fas fa-arrow-left" style="margin-right:6px;"></i>Back to Login
            </a>
        </div>
    </div>
</div>
<script>
document.getElementById('forgotForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn    = document.getElementById('forgotBtn');
    const errBox = document.getElementById('forgotError');
    const orig   = btn.innerHTML;
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
            errBox.textContent   = data.message;
            errBox.style.display = 'block';
        }
    } catch (err) {
        errBox.textContent   = 'Connection error. Please try again.';
        errBox.style.display = 'block';
    } finally { btn.innerHTML = orig; btn.disabled = false; }
});
</script>
</body>
</html>
