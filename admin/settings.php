<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Shanfix Admin</title>
    <link rel="stylesheet" href="../admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Outfit:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .settings-layout { display: grid; grid-template-columns: 220px 1fr; gap: 28px; align-items: start; }
        .settings-tabs { background: var(--admin-card-bg, #1e293b); border-radius: 14px; overflow: hidden; position: sticky; top: 80px; }
        .stab { display: flex; align-items: center; gap: 10px; padding: 14px 20px; cursor: pointer; color: #94a3b8; font-size: 0.88rem; font-weight: 600; border-left: 3px solid transparent; transition: all .2s; text-decoration: none; }
        .stab:hover { color: #f1f5f9; background: rgba(255,255,255,0.04); }
        .stab.active { color: #22c55e; background: rgba(34,197,94,0.08); border-left-color: #22c55e; }
        .stab i { width: 18px; text-align: center; }
        .settings-panel { display: none; }
        .settings-panel.active { display: block; }
        .settings-card { background: var(--admin-card-bg, #1e293b); border-radius: 14px; padding: 28px; margin-bottom: 20px; border: 1px solid rgba(255,255,255,0.06); }
        .settings-card h3 { font-size: 1rem; font-weight: 700; color: #f1f5f9; margin: 0 0 6px; font-family: 'Outfit', sans-serif; }
        .settings-card .card-desc { color: #64748b; font-size: 0.8rem; margin: 0 0 22px; }
        .settings-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 18px; }
        .s-field { display: flex; flex-direction: column; gap: 6px; }
        .s-field label { font-size: 0.8rem; font-weight: 600; color: #94a3b8; }
        .s-field input, .s-field select, .s-field textarea {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 8px;
            color: #f1f5f9;
            padding: 10px 12px;
            font-size: 0.875rem;
            width: 100%;
            outline: none;
            transition: border-color .2s;
            font-family: 'Inter', sans-serif;
        }
        .s-field input:focus, .s-field select:focus, .s-field textarea:focus { border-color: #22c55e; }
        .s-field input[type="password"] { letter-spacing: 2px; }
        .s-field textarea { resize: vertical; min-height: 80px; }
        .s-field select option { background: #1e293b; }
        .toggle-row { display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .toggle-row:last-child { border-bottom: none; }
        .toggle-label { display: flex; flex-direction: column; gap: 2px; }
        .toggle-label strong { font-size: 0.875rem; color: #e2e8f0; font-weight: 600; }
        .toggle-label span { font-size: 0.78rem; color: #64748b; }
        .toggle { position: relative; width: 44px; height: 24px; flex-shrink: 0; }
        .toggle input { opacity: 0; width: 0; height: 0; }
        .toggle-slider { position: absolute; inset: 0; background: rgba(255,255,255,0.1); border-radius: 24px; cursor: pointer; transition: background .25s; }
        .toggle-slider::before { content: ''; position: absolute; width: 18px; height: 18px; left: 3px; top: 3px; background: #fff; border-radius: 50%; transition: transform .25s; }
        .toggle input:checked + .toggle-slider { background: #22c55e; }
        .toggle input:checked + .toggle-slider::before { transform: translateX(20px); }
        .settings-save-bar { display: flex; align-items: center; justify-content: space-between; padding: 16px 0 0; }
        .settings-save-bar .save-note { font-size: 0.8rem; color: #64748b; }
        .btn-save { background: linear-gradient(135deg,#22c55e,#16a34a); color:#fff; border:none; border-radius:9px; padding:10px 26px; font-size:0.875rem; font-weight:700; cursor:pointer; transition:opacity .2s; display:flex;align-items:center;gap:8px; }
        .btn-save:hover { opacity: .88; }
        .btn-save .spinner { display:none; width:14px;height:14px;border:2px solid rgba(255,255,255,.4);border-top-color:#fff;border-radius:50%;animation:spin .7s linear infinite; }
        .btn-save.saving .spinner { display:inline-block; }
        .btn-save.saving .btn-label { display:none; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .toast-msg { position:fixed;top:24px;right:24px;z-index:9999;padding:12px 20px;border-radius:10px;font-size:0.875rem;font-weight:600;opacity:0;transform:translateY(-10px);transition:all .3s;pointer-events:none; }
        .toast-msg.show { opacity:1;transform:translateY(0); }
        .toast-msg.success { background:#166534;color:#bbf7d0;border:1px solid #22c55e44; }
        .toast-msg.error { background:#7f1d1d;color:#fecaca;border:1px solid #ef444444; }
        .env-badge { display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:20px;font-size:0.75rem;font-weight:700; }
        .env-badge.sandbox { background:rgba(251,191,36,.15);color:#fbbf24; }
        .env-badge.live { background:rgba(34,197,94,.15);color:#22c55e; }
        .section-divider { height:1px;background:rgba(255,255,255,0.06);margin:20px 0; }
        @media (max-width: 768px) {
            .settings-layout { grid-template-columns: 1fr; }
            .settings-tabs { position: static; display: flex; overflow-x: auto; border-radius: 10px; }
            .stab { flex-direction: column; gap: 4px; padding: 10px 14px; font-size: 0.75rem; white-space: nowrap; border-left: none; border-bottom: 3px solid transparent; }
            .stab.active { border-left: none; border-bottom-color: #22c55e; }
        }
    </style>
</head>
<body class="admin-body">
<div class="admin-layout-wrapper">

    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="admin-logo">Shanfix <span>Admin</span></div>
        <nav class="admin-nav">
            <a href="index.php" class="admin-nav-item"><i class="fas fa-chart-line"></i> <span>Insights</span></a>
            <a href="clients.php" class="admin-nav-item"><i class="fas fa-users"></i> <span>Clients</span></a>
            <a href="categories.php" class="admin-nav-item"><i class="fas fa-tags"></i> <span>Categories</span></a>
            <a href="services.php" class="admin-nav-item"><i class="fas fa-concierge-bell"></i> <span>Services</span></a>
            <a href="products.php" class="admin-nav-item"><i class="fas fa-box"></i> <span>Catalog</span></a>
            <a href="orders.php" class="admin-nav-item"><i class="fas fa-shopping-bag"></i> <span>Orders</span></a>
            <a href="invoices.php" class="admin-nav-item"><i class="fas fa-file-invoice"></i> <span>Billing</span></a>
            <a href="receipts.php" class="admin-nav-item"><i class="fas fa-receipt"></i> <span>Receipts</span></a>
            <a href="portfolio.php" class="admin-nav-item"><i class="fas fa-briefcase"></i> <span>Portfolio</span></a>
            <a href="blog.php" class="admin-nav-item"><i class="fas fa-newspaper"></i> <span>Blog</span></a>
                        <a href="events.php" class="admin-nav-item"><i class="fas fa-ticket-alt"></i> <span>Events</span></a>
            <a href="testimonials.php" class="admin-nav-item"><i class="fas fa-quote-right"></i> <span>Testimonials</span></a>
<a href="adverts.php" class="admin-nav-item"><i class="fas fa-ad"></i> <span>Adverts</span></a>
            <a href="tickets.php" class="admin-nav-item"><i class="fas fa-life-ring"></i> <span>Support</span></a>
            <a href="messages.php" class="admin-nav-item"><i class="fas fa-inbox"></i> <span>Inbox</span></a>
            <div class="admin-nav-divider"></div>
            <a href="settings.php" class="admin-nav-item active"><i class="fas fa-cog"></i> <span>Settings</span></a>
            <a href="../index.php" class="admin-nav-item"><i class="fas fa-external-link-alt"></i> <span>Live Site</span></a>
        </nav>
        <div class="admin-sidebar-footer">
            <a href="login.php" class="admin-nav-item admin-footer-link" onclick="sessionStorage.clear()">
                <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
        <div class="admin-topbar">
            <div>
                <h1 class="admin-page-title">Settings</h1>
                <p class="admin-page-subtitle">Configure your platform — email, payments, notifications, and more.</p>
            </div>
        </div>

        <div class="settings-layout">

            <!-- Left tabs -->
            <nav class="settings-tabs">
                <a class="stab active" data-panel="general">    <i class="fas fa-building"></i> General</a>
                <a class="stab" data-panel="email">             <i class="fas fa-envelope"></i> Email / SMTP</a>
                <a class="stab" data-panel="mpesa">             <i class="fas fa-mobile-alt"></i> M-PESA</a>
                <a class="stab" data-panel="notifications">     <i class="fas fa-bell"></i> Notifications</a>
                <a class="stab" data-panel="sms">               <i class="fas fa-sms"></i> Bulk SMS</a>
                <a class="stab" data-panel="social">            <i class="fas fa-share-alt"></i> Social Media</a>
                <a class="stab" data-panel="seo">               <i class="fas fa-search"></i> SEO & Analytics</a>
                <a class="stab" data-panel="security">          <i class="fas fa-shield-alt"></i> Security</a>
            </nav>

            <!-- Right panels -->
            <div class="settings-panels">

                <!-- ── GENERAL ── -->
                <div class="settings-panel active" id="panel-general">
                    <div class="settings-card">
                        <h3>Company Information</h3>
                        <p class="card-desc">Displayed in emails, invoices, and the public website.</p>
                        <div class="settings-grid">
                            <div class="s-field"><label>Company Name</label><input type="text" name="company_name" placeholder="Shanfix Technology"></div>
                            <div class="s-field"><label>Tagline</label><input type="text" name="company_tagline" placeholder="Premier IT Solutions…"></div>
                            <div class="s-field"><label>Phone Number</label><input type="tel" name="company_phone" placeholder="+254 700 000 000"></div>
                            <div class="s-field"><label>Email Address</label><input type="email" name="company_email" placeholder="info@shanfix.com"></div>
                            <div class="s-field" style="grid-column:1/-1"><label>Physical Address</label><input type="text" name="company_address" placeholder="Tana House, Karen - Nairobi, Kenya"></div>
                            <div class="s-field"><label>Website URL</label><input type="url" name="company_website" placeholder="https://shanfixtechnology.com"></div>
                        </div>
                        <div class="settings-save-bar">
                            <span class="save-note">Changes are applied immediately after saving.</span>
                            <button class="btn-save" data-group="general"><span class="spinner"></span><span class="btn-label"><i class="fas fa-save"></i> Save General</span></button>
                        </div>
                    </div>
                </div>

                <!-- ── EMAIL / SMTP ── -->
                <div class="settings-panel" id="panel-email">
                    <div class="settings-card">
                        <h3>SMTP Configuration</h3>
                        <p class="card-desc">Used for all outgoing email: invoices, password resets, notifications.</p>
                        <div class="settings-grid">
                            <div class="s-field"><label>SMTP Host</label><input type="text" name="smtp_host" placeholder="smtp.gmail.com"></div>
                            <div class="s-field"><label>Port</label><input type="number" name="smtp_port" placeholder="587"></div>
                            <div class="s-field">
                                <label>Encryption</label>
                                <select name="smtp_encryption">
                                    <option value="tls">TLS (recommended)</option>
                                    <option value="ssl">SSL</option>
                                    <option value="none">None</option>
                                </select>
                            </div>
                            <div class="s-field"><label>Username / Email</label><input type="text" name="smtp_username" placeholder="your@email.com"></div>
                            <div class="s-field"><label>Password</label><input type="password" name="smtp_password" placeholder="••••••••" autocomplete="new-password"></div>
                            <div class="s-field"><label>From Name</label><input type="text" name="smtp_from_name" placeholder="Shanfix Technology"></div>
                            <div class="s-field"><label>From Email</label><input type="email" name="smtp_from_email" placeholder="noreply@shanfix.com"></div>
                        </div>
                        <div class="settings-save-bar">
                            <span class="save-note">Password is encrypted and never displayed.</span>
                            <button class="btn-save" data-group="email"><span class="spinner"></span><span class="btn-label"><i class="fas fa-save"></i> Save Email</span></button>
                        </div>
                    </div>
                </div>

                <!-- ── M-PESA ── -->
                <div class="settings-panel" id="panel-mpesa">
                    <div class="settings-card">
                        <h3>Safaricom Daraja (M-PESA)</h3>
                        <p class="card-desc">Configure M-PESA STK Push for online payments. Get credentials from <a href="https://developer.safaricom.co.ke" target="_blank" style="color:#22c55e;">developer.safaricom.co.ke</a>.</p>
                        <div class="settings-grid">
                            <div class="s-field">
                                <label>Environment</label>
                                <select name="mpesa_environment">
                                    <option value="sandbox">Sandbox (Testing)</option>
                                    <option value="live">Live (Production)</option>
                                </select>
                            </div>
                            <div class="s-field"><label>Business Shortcode / Till</label><input type="text" name="mpesa_shortcode" placeholder="174379"></div>
                            <div class="s-field"><label>Consumer Key</label><input type="password" name="mpesa_consumer_key" placeholder="••••••••" autocomplete="new-password"></div>
                            <div class="s-field"><label>Consumer Secret</label><input type="password" name="mpesa_consumer_secret" placeholder="••••••••" autocomplete="new-password"></div>
                            <div class="s-field"><label>Passkey</label><input type="password" name="mpesa_passkey" placeholder="••••••••" autocomplete="new-password"></div>
                            <div class="s-field" style="grid-column:1/-1"><label>Callback URL</label><input type="url" name="mpesa_callback_url" placeholder="https://shanfixtechnology.com/api/mpesa/callback.php"></div>
                        </div>
                        <div class="settings-save-bar">
                            <span class="save-note">Sensitive keys are stored encrypted.</span>
                            <button class="btn-save" data-group="mpesa"><span class="spinner"></span><span class="btn-label"><i class="fas fa-save"></i> Save M-PESA</span></button>
                        </div>
                    </div>
                </div>

                <!-- ── NOTIFICATIONS ── -->
                <div class="settings-panel" id="panel-notifications">
                    <div class="settings-card">
                        <h3>Alert Channels</h3>
                        <p class="card-desc">Choose how you receive admin alerts.</p>
                        <div class="toggle-row">
                            <div class="toggle-label"><strong>Email Alerts</strong><span>Send alerts to the admin email address</span></div>
                            <label class="toggle"><input type="checkbox" name="notify_channel_email"><span class="toggle-slider"></span></label>
                        </div>
                        <div class="toggle-row">
                            <div class="toggle-label"><strong>SMS Alerts</strong><span>Send alerts via bulk SMS (requires SMS config)</span></div>
                            <label class="toggle"><input type="checkbox" name="notify_channel_sms"><span class="toggle-slider"></span></label>
                        </div>
                        <div class="section-divider"></div>
                        <div class="s-field" style="max-width:360px; margin-bottom:20px;">
                            <label>Admin Notification Email</label>
                            <input type="email" name="notify_admin_email" placeholder="admin@shanfix.com">
                        </div>
                        <h3 style="margin-bottom:6px;">Trigger Events</h3>
                        <p class="card-desc">Receive a notification when these events occur.</p>
                        <div class="toggle-row">
                            <div class="toggle-label"><strong>New Order</strong><span>Alert when a client places a new order</span></div>
                            <label class="toggle"><input type="checkbox" name="notify_new_order"><span class="toggle-slider"></span></label>
                        </div>
                        <div class="toggle-row">
                            <div class="toggle-label"><strong>New Support Ticket</strong><span>Alert when a client opens a ticket</span></div>
                            <label class="toggle"><input type="checkbox" name="notify_new_ticket"><span class="toggle-slider"></span></label>
                        </div>
                        <div class="toggle-row">
                            <div class="toggle-label"><strong>New Client Registration</strong><span>Alert when a new client signs up</span></div>
                            <label class="toggle"><input type="checkbox" name="notify_new_client"><span class="toggle-slider"></span></label>
                        </div>
                        <div class="settings-save-bar">
                            <span class="save-note"></span>
                            <button class="btn-save" data-group="notifications"><span class="spinner"></span><span class="btn-label"><i class="fas fa-save"></i> Save Notifications</span></button>
                        </div>
                    </div>
                </div>

                <!-- ── BULK SMS ── -->
                <div class="settings-panel" id="panel-sms">
                    <div class="settings-card">
                        <h3>SMS Provider</h3>
                        <p class="card-desc">Used for client notifications and bulk messaging campaigns.</p>
                        <div class="settings-grid">
                            <div class="s-field">
                                <label>Provider</label>
                                <select name="sms_provider">
                                    <option value="africastalking">Africa's Talking</option>
                                    <option value="twilio">Twilio</option>
                                    <option value="infobip">Infobip</option>
                                    <option value="vonage">Vonage</option>
                                </select>
                            </div>
                            <div class="s-field"><label>Username / Account SID</label><input type="text" name="sms_username" placeholder="sandbox or your username"></div>
                            <div class="s-field"><label>API Key / Auth Token</label><input type="password" name="sms_api_key" placeholder="••••••••" autocomplete="new-password"></div>
                            <div class="s-field"><label>Sender ID / From Number</label><input type="text" name="sms_sender_id" placeholder="Shanfix"></div>
                        </div>
                        <div class="settings-save-bar">
                            <span class="save-note">Test SMS from the <a href="clients.php" style="color:#22c55e;">Clients</a> page after saving.</span>
                            <button class="btn-save" data-group="sms"><span class="spinner"></span><span class="btn-label"><i class="fas fa-save"></i> Save SMS</span></button>
                        </div>
                    </div>
                </div>

                <!-- ── SOCIAL MEDIA ── -->
                <div class="settings-panel" id="panel-social">
                    <div class="settings-card">
                        <h3>Social Media Links</h3>
                        <p class="card-desc">Displayed in the website footer and used for social meta tags.</p>
                        <div class="settings-grid">
                            <div class="s-field"><label><i class="fab fa-facebook" style="color:#1877f2;margin-right:6px;"></i>Facebook</label><input type="url" name="social_facebook" placeholder="https://facebook.com/shanfixtechnology"></div>
                            <div class="s-field"><label><i class="fab fa-twitter" style="color:#1da1f2;margin-right:6px;"></i>Twitter / X</label><input type="url" name="social_twitter" placeholder="https://twitter.com/shanfixtech"></div>
                            <div class="s-field"><label><i class="fab fa-linkedin" style="color:#0a66c2;margin-right:6px;"></i>LinkedIn</label><input type="url" name="social_linkedin" placeholder="https://linkedin.com/company/shanfix"></div>
                            <div class="s-field"><label><i class="fab fa-instagram" style="color:#e1306c;margin-right:6px;"></i>Instagram</label><input type="url" name="social_instagram" placeholder="https://instagram.com/shanfixtech"></div>
                            <div class="s-field"><label><i class="fab fa-youtube" style="color:#ff0000;margin-right:6px;"></i>YouTube</label><input type="url" name="social_youtube" placeholder="https://youtube.com/@shanfix"></div>
                            <div class="s-field"><label><i class="fab fa-whatsapp" style="color:#25d366;margin-right:6px;"></i>WhatsApp Number</label><input type="tel" name="social_whatsapp" placeholder="+254700000000"></div>
                        </div>
                        <div class="settings-save-bar">
                            <span class="save-note"></span>
                            <button class="btn-save" data-group="social"><span class="spinner"></span><span class="btn-label"><i class="fas fa-save"></i> Save Social</span></button>
                        </div>
                    </div>
                </div>

                <!-- ── SEO & ANALYTICS ── -->
                <div class="settings-panel" id="panel-seo">
                    <div class="settings-card">
                        <h3>Google Analytics</h3>
                        <p class="card-desc">Tracking ID is automatically injected into every public page.</p>
                        <div class="settings-grid">
                            <div class="s-field"><label>GA4 Measurement ID</label><input type="text" name="google_analytics_id" placeholder="G-XXXXXXXXXX"></div>
                            <div class="s-field"><label>Search Console Verification</label><input type="text" name="google_search_console_verification" placeholder="google1234abcd5678.html or meta content value"></div>
                            <div class="s-field"><label>Meta (Facebook) Pixel ID</label><input type="text" name="meta_pixel_id" placeholder="123456789012345"></div>
                        </div>
                        <div class="settings-save-bar">
                            <span class="save-note">Leave blank to disable.</span>
                            <button class="btn-save" data-group="seo"><span class="spinner"></span><span class="btn-label"><i class="fas fa-save"></i> Save SEO</span></button>
                        </div>
                    </div>
                </div>

                <!-- ── SECURITY ── -->
                <div class="settings-panel" id="panel-security">
                    <div class="settings-card">
                        <h3>Session & Access</h3>
                        <p class="card-desc">Controls for admin session behaviour.</p>
                        <div class="settings-grid">
                            <div class="s-field">
                                <label>Admin Session Timeout (minutes)</label>
                                <input type="number" name="session_timeout_minutes" min="15" max="1440" placeholder="60">
                            </div>
                        </div>
                        <div class="section-divider"></div>
                        <h3 style="margin-bottom:6px;">Maintenance Mode</h3>
                        <p class="card-desc">Shows a "coming soon" page to visitors while you work on the site.</p>
                        <div class="toggle-row">
                            <div class="toggle-label"><strong>Enable Maintenance Mode</strong><span>Redirects all public pages to a maintenance notice</span></div>
                            <label class="toggle"><input type="checkbox" name="maintenance_mode"><span class="toggle-slider"></span></label>
                        </div>
                        <div class="settings-save-bar">
                            <span class="save-note" style="color:#f59e0b;"><i class="fas fa-exclamation-triangle"></i> Enabling maintenance mode affects all visitors.</span>
                            <button class="btn-save" data-group="security"><span class="spinner"></span><span class="btn-label"><i class="fas fa-save"></i> Save Security</span></button>
                        </div>
                    </div>
                </div>

            </div><!-- /settings-panels -->
        </div><!-- /settings-layout -->
    </main>
</div>

<div class="toast-msg" id="toastMsg"></div>

<script>
const API = 'api/settings.php';

// Tab switching
document.querySelectorAll('.stab').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('.stab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.settings-panel').forEach(p => p.classList.remove('active'));
        tab.classList.add('active');
        const panel = document.getElementById('panel-' + tab.dataset.panel);
        if (panel) panel.classList.add('active');
        loadGroup(tab.dataset.panel);
    });
});

// Toast
function toast(msg, type) {
    const el = document.getElementById('toastMsg');
    el.textContent = msg;
    el.className = 'toast-msg show ' + (type || 'success');
    setTimeout(() => el.classList.remove('show'), 3500);
}

// Load settings for a group
function loadGroup(group) {
    fetch(API + '?group=' + group)
        .then(r => r.json())
        .then(data => {
            if (!data.success) return;
            const panel = document.getElementById('panel-' + group);
            Object.entries(data.settings).forEach(([key, meta]) => {
                const el = panel ? panel.querySelector('[name="' + key + '"]') : null;
                if (!el) return;
                if (el.type === 'checkbox') {
                    el.checked = meta.value === '1' || meta.value === 'true';
                } else if (el.tagName === 'SELECT') {
                    el.value = meta.value || '';
                } else {
                    el.value = meta.value || '';
                }
            });
        })
        .catch(() => {});
}

// Save group
document.querySelectorAll('.btn-save').forEach(btn => {
    btn.addEventListener('click', () => {
        const group  = btn.dataset.group;
        const panel  = document.getElementById('panel-' + group);
        const fields = panel.querySelectorAll('[name]');
        const settings = {};
        fields.forEach(el => {
            if (el.type === 'checkbox') {
                settings[el.name] = el.checked ? '1' : '0';
            } else {
                settings[el.name] = el.value;
            }
        });
        btn.classList.add('saving');
        fetch(API, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ group, settings })
        })
        .then(r => r.json())
        .then(data => {
            btn.classList.remove('saving');
            if (data.success) {
                toast('✓ ' + group.charAt(0).toUpperCase() + group.slice(1) + ' settings saved.', 'success');
            } else {
                toast('Error: ' + (data.error || 'Could not save.'), 'error');
            }
        })
        .catch(() => {
            btn.classList.remove('saving');
            toast('Network error — please try again.', 'error');
        });
    });
});

// Load the default active group on page load
loadGroup('general');
</script>
</body>
</html>
