const imagePreview = document.querySelector('.image-preview');
const newPostImageInput = document.getElementById('new-post-image')
const imagePreviewImage = document.querySelector('.image-preview img')
const newPostForm = document.getElementById('new-post-form')
const clearImagePreviewButton = document.querySelector('.remove-image-btn')
const newPostContentElement = document.getElementById('new-feed-content')

let user = null;
let image = null;

getUser().then(userData => {
    if (!userData) return;

    const avatarUrl = userData.avatar_name === null ? 'assets/default-avatar.png' : `${baseUrl}/images?name=${userData.avatar_name}`
    document.querySelector('.avatar').src = avatarUrl;
    document.querySelector('.menu-username').textContent = userData.username;

    user = userData;
})

handlerPosts();

function handlerPosts(posts) {
    const postsContainer = document.getElementById('posts');
    postsContainer.innerHTML = ''

    getPosts().then(posts => {
        for (const post of posts) {
            const postContainer = document.createElement('div');
            postContainer.classList.add('post')

            const postStart = document.createElement('div');
            postStart.classList.add('post-start')

            const userInfos = document.createElement('div')
            userInfos.classList.add('user-infos')

            const userAvatarElement = document.createElement('img')
            userAvatarElement.classList.add('avatar')

            userAvatarElement.src = post.user.avatar_name === null ? 'assets/default-avatar.png' : `${baseUrl}/images?name=${post.user.avatar_name}`
            const usernameElement = document.createElement('p');
            usernameElement.textContent = post.user.username

            userInfos.append(userAvatarElement, usernameElement)

            const postCreatedAtElement = document.createElement('p')
            postCreatedAtElement.classList.add('post-created-at')
            postCreatedAtElement.textContent = formatDate(post.created_at)
            postStart.append(userInfos, postCreatedAtElement)

            postContainer.append(postStart)

            if (post.image_name) {
                const postImage = document.createElement('img');
                postImage.classList.add('post-image')
                postImage.src = `${baseUrl}/images?name=${post.image_name}`;

                postContainer.append(postImage)
            }

            const postContent = document.createElement('p');
            postContent.textContent = post.content;
            postContainer.append(postContent)

            postsContainer.append(postContainer)
        }
    })
}

function toggleOpenPostModal() {
    const addPostModalElement = document.querySelector('#add-post-modal')

    const scrollTop = window.scrollY || document.documentElement.scrollTop;
    addPostModalElement.style.top = scrollTop === 0 ? '30%' : `${scrollTop + 340}px`;

    document.querySelector('.shadow').classList.toggle('active');
    addPostModalElement.classList.toggle('open');

    const bodyOverflow = document.body.style.overflow
    document.body.style.overflow = (bodyOverflow === 'hidden' ? 'visible' : 'hidden')
}

function clearPreviewImage() {
    clearImagePreviewButton.style.visibility = 'hidden'

    image = null;
    imagePreviewImage.src = '';
    imagePreviewImage.style.visibility = 'hidden'
}

imagePreview.addEventListener('click', () => {
    newPostImageInput.click();
})

newPostImageInput.addEventListener('change', (event) => {
    const imageUploaded = event.target.files[0];
    image = imageUploaded;

    imagePreviewImage.src = URL.createObjectURL(imageUploaded);
    imagePreviewImage.style.visibility = 'visible'

    clearImagePreviewButton.style.visibility = 'visible'
})

newPostForm.addEventListener('submit', async (event) => {
    event.preventDefault();

    const content = newPostContentElement?.value

    if (!content || content.length < 2) {
        return toostify('error', 'É necessário no mínimo um caracteri para o contéudo dessa postagem.')
    }

    let postData = {
        content,
    };

    if (image) {
        const formData = new FormData();
        formData.append('image', image)

        const imageUploaded = await uploadImage(formData);
        if (!imageUploaded) return;

        postData.image_name = imageUploaded.name;
    }

    const newPostData = await setNewPost(postData);

    if (!newPostData) return;

    clearPreviewImage()
    image = null;
    newPostContentElement.value = '';

    toggleOpenPostModal();

    handlerPosts();
    toostify('success', 'Postagem realizada com sucesso!')
})

