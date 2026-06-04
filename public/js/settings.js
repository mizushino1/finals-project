document.addEventListener('DOMContentLoaded', () => {
    const uploadImageBtn = document.getElementById('uploadImageBtn');
    const fileInput = document.getElementById('avatarFileInput');
    const avatarPreview = document.getElementById('avatarPreview');
    const avatarPlaceholder = document.getElementById('avatarPreviewPlaceholder');
    const removeAvatarBtn = document.getElementById('removeAvatarBtn');
    const textarea = document.getElementById('artistDescText');
    const counter = document.getElementById('charCounter');

    // Trigger file dialog window
    if (uploadImageBtn && fileInput) {
        uploadImageBtn.addEventListener('click', () => fileInput.click());
    }

    // Dynamic Client Preview Engine 
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.addEventListener('load', function() {
                    if (avatarPreview) {
                        avatarPreview.setAttribute('src', this.result);
                        avatarPreview.classList.remove('d-none');
                    }
                    if (avatarPlaceholder) {
                        avatarPlaceholder.classList.remove('d-inline-flex');
                        avatarPlaceholder.classList.add('d-none');
                    }
                });
                reader.readAsDataURL(file);
            }
        });
    }

    // Reset UI back to default icon placeholder
    if (removeAvatarBtn) {
        removeAvatarBtn.addEventListener('click', () => {
            if (fileInput) fileInput.value = ""; 
            if (avatarPreview) {
                avatarPreview.classList.add('d-none');
                avatarPreview.setAttribute('src', '');
            }
            if (avatarPlaceholder) {
                avatarPlaceholder.classList.remove('d-none');
                avatarPlaceholder.classList.add('d-inline-flex');
            }
        });
    }

    // Character Counter Tracking Logic
    if (textarea && counter) {
        const updateCounter = () => {
            counter.textContent = `${textarea.value.length}/250`;
        };
        updateCounter();
        textarea.addEventListener('input', updateCounter);
    }
});