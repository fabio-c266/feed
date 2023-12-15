const loginForm = document.getElementById('login-form');
const registerForm = document.getElementById('register-form');

const baseUrl = 'http://localhost:8000'

loginForm?.addEventListener('submit', async (event) => {
    event.preventDefault();

    const login = document.getElementById('login')?.value ?? '';
    const password = document.getElementById('password')?.value ?? '';

    if (login.length === 0) {
        return toostify('error', 'Campo login é obrigatório.');
    }

    if (password.length === 0) {
        return toostify('error', 'Campo senha é obrigatório.');
    }

    const userData = {
        login,
        password
    };

    try {
        const response = await fetch(`${baseUrl}/auth/login`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(userData)
        })

        const { data } = await response.json();

        if (response.status != 200) {
            return toostify('error', data.message);
        }

        localStorage.setItem('token', data.token);
        window.location.href = '/posts.html';
    } catch (error) {
        return toostify('error', 'Ocorreu um erro no sistema. Tente novamente em instantes.');
    }
})

registerForm?.addEventListener('submit', async (event) => {
    event.preventDefault();

    const username = document.getElementById('username')?.value ?? '';
    const email = document.getElementById('email')?.value ?? '';
    const password = document.getElementById('password')?.value ?? '';
    const passwordConfirm = document.getElementById('password-confirm')?.value ?? '';

    if (username.length <= 5 || username.length > 16) {
        return toostify('error', 'O nome do usuário deve ter no mínimo 1 caracteri e no máximo 16.');
    }

    if (email.length <= 0) {
        return toostify('error', 'Email inválido.');
    }

    const passwordRegex = /^(?=.*\d).{8,}$/;

    if (!passwordRegex.test(password)) {
        return toostify('error', 'A senha deve ter no mínimo 8 caracteris e por o 1 menos um número.');
    }

    if (password !== passwordConfirm) {
        return toostify('error', 'A senhas não são iguais.');
    }

    const userData = {
        username,
        email,
        password
    };

    try {
        const response = await fetch(`${baseUrl}/users`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(userData)
        })

        const { data } = await response.json();

        if (response.status != 201) {
            return toostify('error', data.message);
        }

        window.location.href = '/index.html';
    } catch (error) {
        return toostify('error', 'Ocorreu um erro no sistema. Tente novamente em instantes.');
    }
})