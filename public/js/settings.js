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
document.addEventListener('DOMContentLoaded', () => {
    // Elements Mapping
    const uploadImageBtn = document.getElementById('uploadImageBtn');
    const fileInput = document.getElementById('avatarFileInput');
    const avatarPreview = document.getElementById('avatarPreview');
    const avatarPlaceholder = document.getElementById('avatarPreviewPlaceholder');
    const removeAvatarBtn = document.getElementById('removeAvatarBtn');
    const deleteAvatarFlag = document.getElementById('deleteAvatarFlag');
    const textarea = document.getElementById('artistDescText');
    const counter = document.getElementById('charCounter');
    const dropZone = document.getElementById('dropZone');
    const clearFormBtn = document.getElementById('clearForm');
    const artistHighlightBox = document.getElementById('artistHighlightBox');

    // 1. Asynchronously load profile configuration data to populate fields securely
    hydrateFormFromAPI();

    // Trigger explicit OS file browse selector windows
    if (uploadImageBtn && fileInput) {
        uploadImageBtn.addEventListener('click', () => fileInput.click());
    }

    // Dynamic Image Selection Engine Check
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            processAvatarFileSelection(this.files[0]);
        });
    }

    // 2. Drag and Drop Upload Support Layer
    if (dropZone && fileInput) {
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, (e) => { e.preventDefault(); dropZone.classList.add('highlight'); }, false);
        });
        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, (e) => { e.preventDefault(); dropZone.classList.remove('highlight'); }, false);
        });
        dropZone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files.length) {
                fileInput.files = files;
                processAvatarFileSelection(files[0]);
            }
        });
    }

    function processAvatarFileSelection(file) {
        if (file && (file.type === "image/jpeg" || file.type === "image/png")) {
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
                if (deleteAvatarFlag) deleteAvatarFlag.value = "0"; // Cancel pending deletion flags
            });
            reader.readAsDataURL(file);
        }
    }

    // 3. Reset Engine Handling (Erasing avatar records cleanly)
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
            if (deleteAvatarFlag) {
                deleteAvatarFlag.value = "1"; // Send an explicit delete command to update.php
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

    // 4. Interactive Password Visibility Toggles
    const eyeIcons = document.querySelectorAll('.eye-toggle-icon');
    eyeIcons.forEach(icon => {
        icon.addEventListener('click', function() {
            const passwordInput = this.parentElement.querySelector('input');
            const iconSvg = this.querySelector('i');
            
            if (passwordInput && passwordInput.type === 'password') {
                passwordInput.type = 'text';
                iconSvg.classList.remove('bi-eye-slash');
                iconSvg.classList.add('bi-eye');
            } else if (passwordInput) {
                passwordInput.type = 'password';
                iconSvg.classList.remove('bi-eye');
                iconSvg.classList.add('bi-eye-slash');
            }
        });
    });

    // 5. Hydration Engine via api/profile/fetch.php
    function hydrateFormFromAPI() {
        fetch('../../api/profile/fetch.php')
            .then(response => response.json())
            .then(result => {
                if (result.success && result.data) {
                    const info = result.data;
                    
                    // Fallback injection to update UI form components
                    if (document.getElementById('settingsFormName')) {
                        document.getElementById('settingsFormName').value = info.first_name ? `${info.first_name} ${info.last_name ?? ''}`.trim() : '';
                    }
                    if (document.getElementById('settingsFormUsername')) {
                        document.getElementById('settingsFormUsername').value = info.username || '';
                    }
                    if (document.getElementById('settingsFormEmail')) {
                        document.getElementById('settingsFormEmail').value = info.email || '';
                    }
                    if (document.getElementById('settingsFormPhone')) {
                        document.getElementById('settingsFormPhone').value = info.card_number || ''; // links up with table context structures
                    }

                    // Hide artist field box if the target role context is simple user/client tier groups
                    if (info.role && info.role.toLowerCase() !== 'artist' && artistHighlightBox) {
                        artistHighlightBox.style.display = 'none';
                    }
                    if (textarea) updateCounter();
                }
            })
            .catch(err => console.warn("Automated runtime validation bypass:", err));
    }

    // Cancel interaction action binds
    if (clearFormBtn) {
        clearFormBtn.addEventListener('click', () => {
            if (confirm("Discard all uncommitted configuration modifications?")) {
                window.location.reload();
            }
        });
    }
});
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