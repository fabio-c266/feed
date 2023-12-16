let user = null;

getUser().then(userData => {
    if (!userData) return;

    const avatarUrl = userData.image_id === null ? 'assets/default-avatar.png' : `${baseUrl}/images?id=${userData.image_id}`
    document.querySelector('.avatar').src = avatarUrl;
    document.querySelector('.menu-username').textContent = userData.username;

    user = userData;
})