function toggleHeaderMenu() {
    document.querySelector('.menu').classList.toggle('active');
}

function signOut() {
    localStorage.removeItem('token');
    window.location.href = '/index.html';
}

function moveToProfilePage() {
    window.location.href = '/profile.html';
}

function moveToHomePage() {
    window.location.href = '/posts.html';
}

function toostify(type, text) {
    const types = {
        'error': 'red',
        'success': 'green'
    }

    Toastify({
        text,
        duration: 3000,
        newWindow: false,
        close: true,
        gravity: "bottom",
        position: "right",
        stopOnFocus: true,
        style: {
            background: "#333",
            borderBottom: `3px solid ${types[type] ?? 'blue'}`,
            borderRadius: '8px'
        },
    }).showToast();
}