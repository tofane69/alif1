document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('theme-toggle');
    const currentTheme = localStorage.getItem('theme') ? localStorage.getItem('theme') : null;

    if (currentTheme) {
        document.body.setAttribute('data-bs-theme', currentTheme);
    }

    themeToggle.addEventListener('click', function() {
        let theme = document.body.getAttribute('data-bs-theme');
        if (theme === 'dark') {
            document.body.setAttribute('data-bs-theme', 'light');
            localStorage.setItem('theme', 'light');
        } else {
            document.body.setAttribute('data-bs-theme', 'dark');
            localStorage.setItem('theme', 'dark');
        }
    });

    // Initialize Summernote editor
    // We will add more specific configurations later
    // $('.summernote').summernote();

    // Handle reply button clicks in comment section
    const replyButtons = document.querySelectorAll('.reply-btn');
    replyButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const commentId = this.getAttribute('data-comment-id');
            const formContainer = document.getElementById('reply-form-' + commentId);
            // Hide all other open forms
            document.querySelectorAll('.reply-form-container').forEach(f => f.style.display = 'none');
            // Show the target form
            if (formContainer) {
                formContainer.style.display = 'block';
            }
        });
    });

    // Handle cancel reply button clicks
    const cancelButtons = document.querySelectorAll('.cancel-reply');
    cancelButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const commentId = this.getAttribute('data-comment-id');
            const formContainer = document.getElementById('reply-form-' + commentId);
            if (formContainer) {
                formContainer.style.display = 'none';
            }
        });
    });

    // Like button AJAX logic
    document.querySelectorAll('.like-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const likeableType = this.dataset.likeableType;
            const likeableId = this.dataset.likeableId;
            const url = `${URL_ROOT}/likes/toggle/${likeableType}/${likeableId}`;

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update likes count
                    const likesCountSpan = this.querySelector('.likes-count');
                    likesCountSpan.textContent = data.likes_count;

                    // Update icon style
                    const icon = this.querySelector('i');
                    if (data.user_has_liked) {
                        icon.classList.remove('fa-regular');
                        icon.classList.add('fa-solid', 'text-primary');
                    } else {
                        icon.classList.remove('fa-solid', 'text-primary');
                        icon.classList.add('fa-regular');
                    }
                } else {
                    alert(data.error || 'An error occurred.');
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });

    // Notifications AJAX Logic
    function fetchNotifications() {
        // Check if the notification dropdown exists on the page
        const notificationCount = document.getElementById('notification-count');
        if(!notificationCount) return;

        fetch(`${URL_ROOT}/notifications/fetchUnread`)
            .then(response => response.json())
            .then(data => {
                // Update badge count
                if (data.count > 0) {
                    notificationCount.textContent = data.count;
                    notificationCount.style.display = 'block';
                } else {
                    notificationCount.style.display = 'none';
                }

                // Update dropdown menu
                const notificationMenu = document.getElementById('notification-menu');
                // Clear previous items except the last two (divider and "view all")
                while (notificationMenu.children.length > 2) {
                    notificationMenu.removeChild(notificationMenu.firstChild);
                }

                if (data.notifications.length > 0) {
                    data.notifications.forEach(notif => {
                        const listItem = document.createElement('li');
                        const link = document.createElement('a');
                        link.href = `${URL_ROOT}/${notif.link}`;
                        link.className = 'dropdown-item';
                        link.innerHTML = `<small>${notif.message}</small>`;
                        listItem.appendChild(link);
                        notificationMenu.prepend(listItem);
                    });
                } else {
                     const listItem = document.createElement('li');
                     listItem.innerHTML = '<span class="dropdown-item-text text-muted text-center">هیچ اعلان جدیدی وجود ندارد.</span>';
                     notificationMenu.prepend(listItem);
                }
            })
            .catch(error => console.error('Error fetching notifications:', error));
    }

    // Fetch notifications on page load and then every 30 seconds
    if (document.getElementById('notificationDropdown')) {
        fetchNotifications();
        setInterval(fetchNotifications, 30000);
    }


    // Voice Recording Logic
    let mediaRecorder;
    let audioChunks = [];

    document.querySelectorAll('.comment-form').forEach(form => {
        const startBtn = form.querySelector('.start-recording-btn');
        const stopBtn = form.querySelector('.stop-recording-btn');
        const audioPreview = form.querySelector('.audio-preview');
        const voiceInput = form.querySelector('.voice-note-input');

        startBtn.addEventListener('click', async () => {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                mediaRecorder = new MediaRecorder(stream);

                mediaRecorder.ondataavailable = event => {
                    audioChunks.push(event.data);
                };

                mediaRecorder.onstop = () => {
                    const audioBlob = new Blob(audioChunks, { type: 'audio/wav' });
                    const audioUrl = URL.createObjectURL(audioBlob);
                    audioPreview.src = audioUrl;
                    audioPreview.style.display = 'inline-block';

                    // Create a File object and add it to the hidden file input
                    const audioFile = new File([audioBlob], 'voice-note.wav', { type: 'audio/wav' });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(audioFile);
                    voiceInput.files = dataTransfer.files;

                    audioChunks = []; // Reset for next recording
                };

                mediaRecorder.start();
                startBtn.style.display = 'none';
                stopBtn.style.display = 'inline-block';

            } catch (err) {
                console.error("Error accessing microphone:", err);
                alert('دسترسی به میکروفون امکان‌پذیر نیست. لطفا مجوز لازم را بدهید.');
            }
        });

        stopBtn.addEventListener('click', () => {
            if (mediaRecorder && mediaRecorder.state === 'recording') {
                mediaRecorder.stop();
                startBtn.style.display = 'inline-block';
                stopBtn.style.display = 'none';
            }
        });
    });
});