function toostify(type, text) {
    const types = {
        'error': 'red',
        'success': 'greeen'
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