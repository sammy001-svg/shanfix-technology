/**
 * COMPONENT: Client Portal Authentication
 * Isolated login/registration logic for the end-user facing portal
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
        clientForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const email = document.getElementById('client_email').value;
            const pass = document.getElementById('client_password').value;
            
            let clients = JSON.parse(localStorage.getItem('portal_clients')) || [];
            let found = clients.find(c => c.email === email && c.password === pass);

            if (found) {
                sessionStorage.setItem('isClient', 'true');
                sessionStorage.setItem('client_email', email);
                sessionStorage.setItem('client_name', found.name);
                window.location.href = 'index.php'; // Correct relative path since we are in /client/login.php
            } else {
                alert('Invalid portal credentials!');
            }
        });
    }

    if (regForm) {
        regForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const email = document.getElementById('reg_email').value;
            const name = document.getElementById('reg_name').value;
            const pass = document.getElementById('reg_password').value;
            
            let clients = JSON.parse(localStorage.getItem('portal_clients')) || [];
            if(clients.find(c => c.email === email)) {
                alert('Account already exists! Please login.');
                return;
            }

            clients.push({ name, email, password: pass });
            localStorage.setItem('portal_clients', JSON.stringify(clients));
            alert('Portal Account Created Successfully! You can now login.');
            toggleClientMode('login');
        });
    }
}
