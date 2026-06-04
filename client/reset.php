<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | Shanfix Technology</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="client.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .login-wrapper { min-height:100vh; display:flex; align-items:center; justify-content:center; background:#052607; position:relative; overflow:hidden; padding:2rem; }
        .blob { position:absolute; width:500px; height:500px; border-radius:50%; z-index:1; opacity:0.2; filter:blur(120px); animation:move 20s infinite alternate; }
        .blob:nth-child(1) { background:#22c55e; }
        .blob:nth-child(2) { background:#3b82f6; width:600px; right:-100px; top:-100px; animation-duration:25s; animation-delay:-5s; }
        .blob:nth-child(3) { background:#10b981; width:400px; left:-50px; bottom:-50px; animation-duration:30s; animation-delay:-10s; }
        @keyframes move { from { transform:translate(0,0) scale(1); } to { transform:translate(100px,100px) scale(1.2); } }
        .login-card { background:rgba(255,255,255,0.03); backdrop-filter:blur(30px); border:1px solid rgba(255,255,255,0.1); border-radius:40px; width:100%; max-width:460px; padding:3.5rem; box-shadow:0 40px 100px rgba(0,0,0,0.5); position:relative; z-index:10; }
    </style>
</head>
<body class="client-portal-body">
<div class="login-wrapper">
    <div class="blob"></div><div class="blob"></div><div class="blob"></div>
    <div class="login-card">
        <div style="text-align:center; margin-bottom:2.5rem;">
            <div style="width:64px; height:64px; background:rgba(34,197,94,0.15); border-radius:20px; display:flex; align-items:center; justify-content:center; margin:0 auto 1.5rem; border:1px solid rgba(34,197,94,0.3);">
                <i class="fas fa-shield-alt" style="font-size:1.5rem; color:#86efac;"></i>
            </div>
            <h1 style="font-family:'Outfit',sans-serif; font-size:1.8rem; font-weight:800; color:#fff; margin:0 0 8px;">Set New Password</h1>
            <p style="color:rgba(255,255,255,0.4); font-size:0.9rem; margin:0;">Choose a strong password for your account.</p>
        </div>

        <div id="invalidMsg" style="display:none; text-align:center; padding:1.5rem; background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.3); border-radius:16px; margin-bottom:1.5rem;">
            <i class="fas fa-exclamation-circle" style="color:#fca5a5; font-size:2rem; margin-bottom:1rem; display:block;"></i>
            <p style="color:#fca5a5; font-weight:600; margin:0;">This reset link is invalid or has expired.</p>
            <a href="forgot.php" style="color:#86efac; text-decoration:none; font-size:0.85rem; margin-top:10px; display:inline-block;">Request a new one →</a>
        </div>

        <div id="successMsg" style="display:none; text-align:center; padding:1.5rem; background:rgba(34,197,94,0.1); border:1px solid rgba(34,197,94,0.3); border-radius:16px; margin-bottom:1.5rem;">
            <i class="fas fa-check-circle" style="color:#22c55e; font-size:2rem; margin-bottom:1rem; display:block;"></i>
            <p style="color:#86efac; font-weight:600; margin:0 0 12px;">Password updated successfully!</p>
            <a href="login.php" class="portal-btn-premium" style="display:inline-flex; text-decoration:none;">Log In Now</a>
        </div>

        <form id="resetForm">
            <input type="hidden" id="reset_token" value="<?= htmlspecialchars($_GET['token'] ?? '') ?>">

            <div class="input-group-premium" style="margin-bottom:1.5rem;">
                <input type="password" id="new_password" class="form-control" placeholder=" " required minlength="8" autocomplete="new-password">
                <label for="new_password">New Password (min 8 chars)</label>
                <i class="fas fa-lock"></i>
            </div>
            <div class="input-group-premium" style="margin-bottom:1.5rem;">
                <input type="password" id="confirm_password" class="form-control" placeholder=" " required autocomplete="new-password">
                <label for="confirm_password">Confirm New Password</label>
                <i class="fas fa-lock"></i>
            </div>

            <div id="resetError" style="display:none; color:#fca5a5; font-size:0.85rem; padding:10px 14px; background:rgba(239,68,68,0.1); border-radius:10px; margin-bottom:1rem;"></div>

            <button type="submit" id="resetBtn" class="portal-btn-premium" style="width:100%; margin-bottom:1.5rem;">
                <span>Update Password</span> <i class="fas fa-check"></i>
            </button>
        </form>
    </div>
</div>
<script>
(function() {
    const token = document.getElementById('reset_token').value.trim();
    if (!token) {
        document.getElementById('resetForm').style.display = 'none';
        document.getElementById('invalidMsg').style.display = 'block';
    }
})();

document.getElementById('resetForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn     = document.getElementById('resetBtn');
    const errBox  = document.getElementById('resetError');
    const orig    = btn.innerHTML;
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
            body: JSON.stringify({
                token,
                password,
                confirm
            })
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
