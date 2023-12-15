async function uploadImage(formData) {
    const token = localStorage.getItem('token')

    if (!token) {
        return window.location.href = '/index.html';
    }

    try {
        const response = await fetch(`${baseUrl}/images/upload`, {
            method: 'POST',
            headers: {
                Authorization: `Bearer ${token}`
            },
            body: formData
        })

        const { data } = await response.json();

        if (response.status === 401) {
            return window.location.href = '/index.html';
        }

        if (response.status !== 201) {
            return toostify('error', data.message);
        }

        return data;
    } catch (error) {
        toostify('error', 'Ocorreu um erro no sistema. Tente novamente em instantes.');
    }
}