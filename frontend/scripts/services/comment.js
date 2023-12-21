async function getComments(idPublic) {
    const token = localStorage.getItem('token')

    if (!token) {
        return window.location.href = '/index.html';
    }

    try {
        const response = await fetch(`${baseUrl}/comments/all?post_id_public=${idPublic}`, {
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

async function setNewComment(commentData) {
    const token = localStorage.getItem('token')

    if (!token) {
        return window.location.href = '/index.html';
    }

    try {
        const response = await fetch(`${baseUrl}/comments`, {
            method: 'POST',
            headers: {
                Authorization: `Bearer ${token}`
            },
            body: JSON.stringify(commentData)
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

async function updateComment(idPublic, newCommentData) {
    const token = localStorage.getItem('token')

    if (!token) {
        return window.location.href = '/index.html';
    }

    try {
        const response = await fetch(`${baseUrl}/comments?comment_id_public=${idPublic}`, {
            method: 'PUT',
            headers: {
                Authorization: `Bearer ${token}`
            },
            body: JSON.stringify(newCommentData)
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

async function deleteComment(idPublic) {
    const token = localStorage.getItem('token');

    if (!token) {
        return window.location.href = '/index.html';
    }

    try {
        const response = await fetch(`${baseUrl}/comments?comment_id_public=${idPublic}`, {
            method: 'DELETE',
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