const avatarElement = document.querySelector('.avatar');
const usernameInput = document.getElementById('username-input')
const emailInput = document.getElementById('email-input')
const uploadField = document.getElementById('upload-field')
const profileForm = document.getElementById('profile-form')

let image = null;
let user = null;

getUser().then(userData => {

    if (!userData) return;

    const avatarUrl = userData.image_id === null ? 'assets/default-avatar.png' : `${baseUrl}/images?id=${userData.image_id}`
    avatarElement.src = avatarUrl;

    usernameInput.value = userData.username
    emailInput.value = userData.email

    user = userData
})

avatarElement.addEventListener('click', () => {
    uploadField.click();
})

uploadField.addEventListener('change', (event) => {
    const imageUploaded = event.target.files[0];
    image = imageUploaded;

    avatarElement.src = URL.createObjectURL(imageUploaded);
})

profileForm.addEventListener('submit', async (event) => {
    event.preventDefault()

    const username = usernameInput?.value
    let userData = {};

    if (image) {
        const formData = new FormData();
        formData.append('image', image)

        const imageUploaded = await uploadImage(formData);
        if (!imageUploaded) return;

        userData.image_id = imageUploaded.id
    }

    if (username !== user.username) {
        userData.username = username
    }

    if (JSON.stringify(userData) === '{}') {
        return toostify('error', 'Você precisa alterar o username ou o avatar.')
    }

    const newUserData = await updateUser(userData);

    if (!newUserData) return;

    usernameInput.value = newUserData.username
    user = newUserData
    image = null;

    toostify('success', 'Dados atualizados com sucesso!')
})

