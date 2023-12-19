const baseUrl = 'http://192.168.100.57:8000'

window.onscroll = () => {
    const headerElement = document.querySelector('header');
    if (window.pageYOffset > headerElement.offsetTop) {
        headerElement.classList.add('sticky')
    } else {
        headerElement.classList.remove('sticky')
    }
}

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

function formatDate(date) {
    const currentDate = new Date()
    const elapsed = Math.abs(currentDate - new Date(date))

    const days = Math.floor(elapsed / 8.786e7)
    const hours = Math.floor(elapsed / 3600000)
    const minutes = Math.floor(elapsed / 60000)
    const years = Math.floor(days / 365);

    if (years > 0) {
        return `há ${years} ano${years > 1 ? 's' : ''}`;
    } else if (days > 0) {
        return `há ${days} dia${days > 1 ? 's' : ''}`;
    } else if (hours > 0) {
        return `há ${hours} hora${hours > 1 ? 's' : ''}`;
    } else if (minutes > 0) {
        return `há ${minutes} minuto${minutes > 1 ? 's' : ''}`;
    } else {
        return 'Agora mesmo';
    }
}

function toostify(type, text) {
    const types = {
        'error': 'red',
        'success': '#8ca103'
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