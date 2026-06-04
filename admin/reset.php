<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Shanfix Admin</title>
    <link rel="stylesheet" href="../admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Outfit:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .login-wrapper { min-height:100vh; width:100%; display:flex; align-items:center; justify-content:center; background:#030816; position:relative; overflow:hidden; padding:2rem; font-family:'Inter',sans-serif; }
        .sec-blob { position:absolute; width:600px; height:600px; background:#083f0c; filter:blur(140px); border-radius:50%; z-index:1; opacity:0.15; animation:drift 25s infinite alternate ease-in-out; }
        .sec-blob:nth-child(2) { background:#22c55e; width:500px; right:-150px; bottom:-150px; animation-duration:20s; animation-delay:-7s; }
        .sec-blob:nth-child(3) { background:#1e3a8a; width:450px; left:-100px; top:-100px; animation-duration:30s; animation-delay:-12s; }
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
        .admin-login-btn:disabled { opacity:0.6; }
    </style>
</head>
<body class="admin-body">
<div class="login-wrapper">
    <div class="sec-blob"></div><div class="sec-blob"></div><div class="sec-blob"></div>
    <div class="login-card">
        <div style="text-align:center; margin-bottom:3rem;">
            <h1 style="font-family:'Outfit',sans-serif; font-size:2rem; font-weight:800; color:white; margin:0 0 8px; letter-spacing:-1px;">Shanfix <span style="background:linear-gradient(to right,#22c55e,#10b981); -webkit-background-clip:text; -webkit-text-fill-color:transparent;">Admin</span></h1>
            <p style="color:rgba(255,255,255,0.4); font-size:0.9rem; margin:0;">Set your new administrator password</p>
        </div>

        <div id="invalidMsg" style="display:none; text-align:center; padding:1.5rem; background:rgba(239,68,68,0.08); border:1px solid rgba(239,68,68,0.2); border-radius:16px; margin-bottom:1.5rem;">
            <i class="fas fa-exclamation-circle" style="color:#fca5a5; font-size:1.8rem; margin-bottom:1rem; display:block;"></i>
            <p style="color:#fca5a5; font-weight:600; margin:0 0 12px;">This reset link is invalid or has expired.</p>
            <a href="forgot.php" style="color:#86efac; text-decoration:none; font-size:0.85rem;">Request a new link →</a>
        </div>

        <div id="successMsg" style="display:none; text-align:center; padding:1.5rem; background:rgba(34,197,94,0.08); border:1px solid rgba(34,197,94,0.2); border-radius:16px; margin-bottom:1.5rem;">
            <i class="fas fa-check-circle" style="color:#22c55e; font-size:1.8rem; margin-bottom:1rem; display:block;"></i>
            <p style="color:#86efac; font-weight:600; margin:0 0 12px;">Password updated successfully!</p>
            <a href="login.php" style="color:#22c55e; font-weight:700; text-decoration:none;">Back to Login →</a>
        </div>

        <form id="resetForm">
            <input type="hidden" id="reset_token" value="<?= htmlspecialchars($_GET['token'] ?? '') ?>">
            <div class="admin-input-premium">
                <input type="password" id="new_password" class="form-control" placeholder=" " required minlength="8" autocomplete="new-password">
                <label for="new_password">New Password</label>
                <i class="fas fa-key"></i>
            </div>
            <div class="admin-input-premium">
                <input type="password" id="confirm_password" class="form-control" placeholder=" " required autocomplete="new-password">
                <label for="confirm_password">Confirm Password</label>
                <i class="fas fa-key"></i>
            </div>
            <div id="resetError" style="display:none; color:#fca5a5; font-size:0.85rem; padding:10px 14px; background:rgba(239,68,68,0.08); border-radius:10px; margin-bottom:1rem;"></div>
            <button type="submit" id="resetBtn" class="admin-login-btn">
                <span>Update Password</span> <i class="fas fa-shield-alt"></i>
            </button>
        </form>
    </div>
</div>
<script>
(function() {
    if (!document.getElementById('reset_token').value.trim()) {
        document.getElementById('resetForm').style.display = 'none';
        document.getElementById('invalidMsg').style.display = 'block';
    }
})();

document.getElementById('resetForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn    = document.getElementById('resetBtn');
    const errBox = document.getElementById('resetError');
    const orig   = btn.innerHTML;
    btn.innerHTML = '<span>Updating...</span> <i class="fas fa-spinner fa-spin"></i>';
    btn.disabled  = true;
    errBox.style.display = 'none';

    const password = document.getElementById('new_password').value;
    const confirm  = document.getElementById('confirm_password').value;
    if (password !== confirm) {
        errBox.textContent   = 'Passwords do not match.';
        errBox.style.display = 'block';
        btn.innerHTML = orig; btn.disabled = false;
        return;
    }
    try {
        const res  = await fetch('api/reset.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ token: document.getElementById('reset_token').value.trim(), password, confirm })
        });
        const data = await res.json();
        if (data.success) {
            document.getElementById('resetForm').style.display = 'none';
            document.getElementById('successMsg').style.display = 'block';
        } else if (data.message.includes('invalid') || data.message.includes('expired')) {
            document.getElementById('resetForm').style.display = 'none';
            document.getElementById('invalidMsg').style.display = 'block';
        } else {
            errBox.textContent   = data.message;
            errBox.style.display = 'block';
        }
    } catch(err) {
        errBox.textContent   = 'Connection error.';
        errBox.style.display = 'block';
    } finally { btn.innerHTML = orig; btn.disabled = false; }
});
</script>
</body>
</html>
