const baseUrl = 'http://localhost:8000'

async function getUser() {
    const token = localStorage.getItem('token')

    if (!token) {
        return window.location.href = '/index.html';
    }

    try {
        const response = await fetch(`${baseUrl}/users`, {
            method: 'GET',
            headers: {
                Authorization: `Bearer ${token}`
            }
        })

        const { data } = await response.json();

        if (response.status === 401) {
            return window.location.href = '/index.html';
        }

        if (response.status !== 200) {
            return toostify('error', data.message);
        }

        return data;
    } catch (error) {
        toostify('error', 'Ocorreu um erro no sistema. Tente novamente em instantes.');
    }
}

async function updateUser(newUserData) {
    const token = localStorage.getItem('token')

    if (!token) {
        return window.location.href = '/index.html';
    }

    try {
        const response = await fetch(`${baseUrl}/users`, {
            method: 'PUT',
            headers: {
                Authorization: `Bearer ${token}`
            },
            body: JSON.stringify(newUserData)
        })

        const { data } = await response.json();

        if (response.status === 401) {
            return window.location.href = '/index.html';
        }

        if (response.status !== 200) {
            return toostify('error', data.message);
        }

        return data;
    } catch (error) {
        toostify('error', 'Ocorreu um erro no sistema. Tente novamente em instantes.');
    }
}