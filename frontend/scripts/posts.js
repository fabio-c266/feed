const imagePreview = document.querySelector('.image-preview');
const newPostImageInput = document.getElementById('new-post-image')
const imagePreviewImage = document.querySelector('.image-preview img')
const newPostForm = document.getElementById('new-post-form')
const newCommentForm = document.getElementById('new-comment-form')
const clearImagePreviewButton = document.querySelector('.remove-image-btn')
const newPostContentElement = document.getElementById('new-feed-content')

let user = null;
let image = null;
let currentPostId = null;

getUser().then(userData => {
    if (!userData) return;

    const avatarUrl = userData.avatar_name === null ? 'assets/default-avatar.png' : `${baseUrl}/images?name=${userData.avatar_name}`
    document.querySelector('header .avatar').src = avatarUrl;
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

            const postFooter = document.createElement('div');
            postFooter.classList.add('post-footer')

            const commentsContainer = document.createElement('div')
            const postCommentsAmountElement = document.createElement('p')
            postCommentsAmountElement.classList.add('amount-comments')
            postCommentsAmountElement.classList.add(post.id_public)
            postCommentsAmountElement.textContent = post.commentsAmount

            commentsContainer.append(postCommentsAmountElement)
            commentsContainer.innerHTML += `<ion-icon class="comment-icon ${post.id_public}" name="chatbubble-outline" onclick="handleComments(event)"></ion-icon>`;

            postFooter.append(commentsContainer)
            postContainer.append(postContent, postFooter)

            postsContainer.append(postContainer)
        }
    })
}

function handleComments(event) {
    const postId = event?.target?.classList?.value?.split(' ')[1];

    if (!postId) return;
    currentPostId = postId;

    toggleOpenCommentModal()
    generateComments(postId)
}

function handleUpdateComment(event) {
    const commentId = event?.target?.classList?.value?.split(' ')[1];

    if (!commentId) return;

    const newContent = prompt('Digite o contéudo do novo comentário:');

    if (newContent) {
        updateComment(commentId, { content: newContent }).then((data) => {
            if (!data) return;

            toostify('success', 'Comentário atualizado!')
            generateComments(currentPostId);
        })
            .catch(() => { })
    }
}

function handleDeleteComment(event) {
    const commentId = event?.target?.classList?.value?.split(' ')[1];

    if (!commentId) return;

    const confirmation = confirm('Você realmente deseja deletar esse comentário? Ação irreversível.');

    if (confirmation) {
        deleteComment(commentId).then(() => {
            toostify('success', 'Comentário excluido com sucesso!');
            generateComments(currentPostId);
            handleCommentsAmountInPost('remove')
        })
    }
}

function generateComments(postId) {
    getComments(postId).then(comments => {
        const commentsContainer = document.querySelector('.comments')
        commentsContainer.innerHTML = ''

        if (comments.length === 0) {
            const textElement = document.createElement('p');
            textElement.textContent = 'Esse post não possui nenhum comentário, seja o primeiro!'

            return commentsContainer.append(textElement)
        }

        for (const comment of comments) {
            const commentElement = document.createElement('div');
            commentElement.classList.add('comment')

            const commentStartElement = document.createElement('div');
            commentStartElement.classList.add('comment-start')

            const userInfosElement = document.createElement('div');
            userInfosElement.classList.add('user-infos')
            const userAvatarElement = document.createElement('img')
            userAvatarElement.classList.add('avatar')

            userAvatarElement.src = comment.user.avatar_name === null ? 'assets/default-avatar.png' : `${baseUrl}/images?name=${comment.user.avatar_name}`
            const usernameElement = document.createElement('p');
            usernameElement.textContent = comment.user.username

            userInfosElement.append(userAvatarElement, usernameElement)

            const commentCreatedAtElement = document.createElement('p')
            commentCreatedAtElement.classList.add('comment-created-at')
            commentCreatedAtElement.textContent = formatDate(comment.created_at)
            commentStartElement.append(userInfosElement, commentCreatedAtElement)

            const commentContentElement = document.createElement('p')
            commentContentElement.textContent = comment.content

            commentElement.append(commentStartElement, commentContentElement)

            if (comment.user.id_public === user.id_public) {
                const commentFooterElement = document.createElement('div')
                commentFooterElement.classList.add('comment-footer')

                const pencilIconElement = `<ion-icon class="pencil-icon ${comment.id_public}" name="pencil-outline" onclick="handleUpdateComment(event)"></ion-icon>`
                const trashIconElement = `<ion-icon class="trash-icon ${comment.id_public}" name="trash" title="Deletar comentário" onclick="handleDeleteComment(event)"></ion-icon>`

                commentFooterElement.innerHTML += pencilIconElement;
                commentFooterElement.innerHTML += trashIconElement;
                commentElement.append(commentFooterElement)
            }

            commentsContainer.append(commentElement)
        }
    })
}

function toggleOpenPostModal() {
    openModal(document.querySelector('#add-post-modal'))
}

function toggleOpenCommentModal() {
    openModal(document.querySelector('#add-comment-modal'))

    handleComments()
}

function openModal(element) {
    const scrollTop = window.scrollY || document.documentElement.scrollTop;

    const zoomLevel = Math.round(window.devicePixelRatio * 100);
    const sum = zoomLevel <= 125 ? 355 : 264

    element.style.top = scrollTop === 0 ? '50vh' : `${scrollTop + sum}px`;

    document.querySelector('.shadow').classList.toggle('active');
    element.classList.toggle('open');

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

newCommentForm.addEventListener('submit', async (event) => {
    if (!currentPostId) return;
    event.preventDefault();

    const newCommentContent = document.querySelector('#new-comment-content');
    const content = newCommentContent.value ?? ''

    if (content.length === 0) {
        return toostify('error', 'Você não pode comentar algo vazio.')
    }

    const data = {
        post_id_public: currentPostId,
        content
    }

    const commented = await setNewComment(data)

    if (!commented) return;

    newCommentContent.value = '';
    generateComments(currentPostId)

    handleCommentsAmountInPost('add')
})

function handleCommentsAmountInPost(action) {
    try {
        const postCommentsAmountElement = document.querySelector(`p.amount-comments.${currentPostId}`)
        const currentAmount = Number(postCommentsAmountElement.textContent)
        let result = action === 'remove' ? currentAmount - 1 : currentAmount + 1

        postCommentsAmountElement.textContent = result;
    } catch { }
}

