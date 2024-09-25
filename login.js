document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const login = document.getElementById('login').value.trim();
    const password = document.getElementById('password').value.trim();

    if (login === 'admin' && password === 'password123') {
        localStorage.setItem('loggedInUser', JSON.stringify({ username: login }));
        window.location.href = 'index.html';
    } else {
        alert('Credenciales inválidas');
    }
});
