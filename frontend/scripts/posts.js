let user = null;

getUser().then(userData => {
    if (!userData) return;

    const avatarUrl = userData.avatar_name === null ? 'assets/default-avatar.png' : `${baseUrl}/images?name=${userData.avatar_name}`
    document.querySelector('.avatar').src = avatarUrl;
    document.querySelector('.menu-username').textContent = userData.username;

    user = userData;
})