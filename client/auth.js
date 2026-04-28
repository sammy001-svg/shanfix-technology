/**
 * COMPONENT: Client Portal Authentication
 * Re-engineered to use MySQL backend APIs (register.php and login.php)
 */

document.addEventListener('DOMContentLoaded', () => {
    initClientAuth();
});

function toggleClientMode(mode) {
    const loginForm = document.getElementById('clientLoginForm');
    const regForm = document.getElementById('clientRegForm');
    
    if (mode === 'register') {
        loginForm.classList.remove('active-form');
        loginForm.classList.add('hidden-form');
        regForm.classList.remove('hidden-form');
        regForm.classList.add('active-form');
    } else {
        regForm.classList.remove('active-form');
        regForm.classList.add('hidden-form');
        loginForm.classList.remove('hidden-form');
        loginForm.classList.add('active-form');
    }
}

function initClientAuth() {
    const clientForm = document.getElementById('clientLoginForm');
    const regForm = document.getElementById('clientRegForm');

    if (clientForm) {
        clientForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = document.getElementById('client_email').value;
            const password = document.getElementById('client_password').value;
            
            const submitBtn = clientForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span>Verifying...</span> <i class="fas fa-spinner fa-spin"></i>';
            submitBtn.disabled = true;

            try {
                const response = await fetch('api/login.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password })
                });

                const text = await response.text();
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    console.error('Server response was not JSON:', text);
                    alert('Server Error: The response from the server was invalid. Please check the console for details.');
                    return;
                }

                if (data.success) {
                    sessionStorage.setItem('isClient', 'true');
                    sessionStorage.setItem('client_email', data.user.email);
                    sessionStorage.setItem('client_name', data.user.name);
                    window.location.href = 'index.php';
                } else {
                    alert(data.message || 'Invalid portal credentials!');
                }
            } catch (error) {
                console.error('Auth Error:', error);
                alert('Connection to server failed. Please try again.');
            } finally {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });
    }

    if (regForm) {
        regForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = document.getElementById('reg_email').value;
            const name = document.getElementById('reg_name').value;
            const password = document.getElementById('reg_password').value;
            
            const submitBtn = regForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span>Processing Request...</span> <i class="fas fa-spinner fa-spin"></i>';
            submitBtn.disabled = true;

            try {
                const response = await fetch('api/register.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ name, email, password })
                });

                const text = await response.text();
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    console.error('Server response was not JSON:', text);
                    alert('Server Error: Registration response was invalid. Please check the console for details.');
                    return;
                }

                if (data.success) {
                    alert(data.message || 'Portal Account Created Successfully! You can now login.');
                    toggleClientMode('login');
                } else {
                    alert(data.message || 'Registration failed.');
                }
            } catch (error) {
                console.error('Registration Error:', error);
                alert('Connection to server failed. Please try again.');
            } finally {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });
    }
}
