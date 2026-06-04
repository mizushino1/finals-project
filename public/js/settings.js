document.addEventListener('DOMContentLoaded', () => {

    // ── Elements ──
    const form              = document.getElementById('settingsForm');
    const alertBox          = document.getElementById('settingsAlert');
    const uploadImageBtn    = document.getElementById('uploadImageBtn');
    const fileInput         = document.getElementById('avatarFileInput');
    const avatarPreview     = document.getElementById('avatarPreview');
    const avatarPlaceholder = document.getElementById('avatarPreviewPlaceholder');
    const removeAvatarBtn   = document.getElementById('removeAvatarBtn');
    const deleteAvatarFlag  = document.getElementById('deleteAvatarFlag');
    const textarea          = document.getElementById('artistDescText');
    const counter           = document.getElementById('charCounter');
    const dropZone          = document.getElementById('dropZone');
    const clearFormBtn      = document.getElementById('clearForm');
    const artistBox         = document.getElementById('artistHighlightBox');

    // ── 1. Hydrate form from api/profile/fetch.php ──
    async function hydrateForm() {
        try {
            const res  = await fetch(`${BASE_URL}api/profile/fetch.php`);
            const data = await res.json();

            if (!data.success) return;

            const d = data.data;

            // Set both values and placeholders to meet form data rules
            setValue('settingsFirstName',  d.first_name  ?? '');
            setValue('settingsMiddleName', d.middle_name ?? '');
            setValue('settingsLastName',   d.last_name   ?? '');
            setValue('settingsUsername',   d.username    ?? '');
            setValue('settingsEmail',      d.email       ?? '');
            setValue('settingsPhone',      d.phone       ?? '');

            // User Specific fields
            if (d.card_number !== undefined) {
                setValue('settingsCardNumber', d.card_number ?? '');
            }

            // Artist Specific fields
            if (d.role === 'artist') {
                setValue('settingsStartingRate', d.starting_rate ?? '');
                setValue('artistDescText',       d.artist_description ?? '');

                const availToggle = document.getElementById('settingsIsAvailable');
                if (availToggle) availToggle.checked = d.is_available == 1;

                if (artistBox) artistBox.classList.remove('d-none');
            }

            // Avatar Handling
            if (d.avatar_url) {
                if (avatarPreview) {
                    // Appending unique cache parameter forces a live binary fetch request
                    const dynamicBuster = '?t=' + new Date().getTime();
                    avatarPreview.src = BASE_URL + d.avatar_url + dynamicBuster;
                    avatarPreview.classList.remove('d-none');
                }
                if (avatarPlaceholder) {
                    avatarPlaceholder.classList.add('d-none');
                    avatarPlaceholder.classList.remove('d-inline-flex');
                }
            } else {
                if (avatarPreview) {
                    avatarPreview.src = '';
                    avatarPreview.classList.add('d-none');
                }
                if (avatarPlaceholder) {
                    avatarPlaceholder.classList.remove('d-none');
                    avatarPlaceholder.classList.add('d-inline-flex');
                }
            }

            // Keep the counter updated after injection
            updateCounter();

        } catch (err) {
            console.error('Failed to load profile:', err);
        }
    }

    // Explicitly defines active value and baseline visual placeholders
    function setValue(id, val) {
        const el = document.getElementById(id);
        if (el) {
            el.value = val ?? '';        // This satisfies the 'required' check
            el.placeholder = val ?? '';  // This handles your visual background layout
        }
    }

    // ── 2. Submit form via fetch() ──
    if (form) {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = new FormData(form);

            try {
                const res  = await fetch(`${BASE_URL}api/profile/update.php`, {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                // Display response message
                showAlert(data.success ? 'success' : 'danger', data.message);

                // Seamlessly synchronize form with newly saved database records
                if (data.success) {
                    hydrateForm();
                }

            } catch (err) {
                showAlert('danger', 'Something went wrong. Please try again.');
            }
        });
    }

    function showAlert(type, message) {
        if (!alertBox) return;
        alertBox.className   = `alert alert-${type}`;
        alertBox.textContent = message;
        alertBox.classList.remove('d-none');

        // Smoothly scrolls to the top of the card so the user can read the error/success message
        alertBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

        setTimeout(() => alertBox.classList.add('d-none'), 4000);
    }

    // ── 3. Avatar: select image ──
    if (uploadImageBtn && fileInput) {
        uploadImageBtn.addEventListener('click', () => fileInput.click());
    }

    if (fileInput) {
        fileInput.addEventListener('change', function () {
            processFile(this.files[0]);
        });
    }

    function processFile(file) {
        if (!file) return;

        // Strict mime-type validation match checking
        if (!['image/jpeg', 'image/png', 'image/jpg'].includes(file.type.toLowerCase())) {
            showAlert('danger', 'Only JPG and PNG files are allowed');
            return;
        }

        const reader = new FileReader();
        reader.onload = function () {
            // Target the avatar preview element securely
            if (avatarPreview) {
                avatarPreview.src = this.result; // Dynamic local Base64 string stream
                avatarPreview.classList.remove('d-none');
            }
            
            // Cleanly toggle the placeholder element if it exists in the DOM
            if (avatarPlaceholder) {
                avatarPlaceholder.classList.add('d-none');
                avatarPlaceholder.classList.remove('d-inline-flex');
            }

            // Lower the removal flag value to let PHP know we are committing a file change
            if (deleteAvatarFlag) {
                deleteAvatarFlag.value = '0';
            }
        };
        reader.readAsDataURL(file);
    }

    // ── 4. Avatar: drag and drop ──
    if (dropZone && fileInput) {
        ['dragenter', 'dragover'].forEach(ev => {
            dropZone.addEventListener(ev, e => { 
                e.preventDefault(); 
                dropZone.classList.add('highlight'); 
            });
        });
        ['dragleave', 'drop'].forEach(ev => {
            dropZone.addEventListener(ev, e => { 
                e.preventDefault(); 
                dropZone.classList.remove('highlight'); 
            });
        });
        dropZone.addEventListener('drop', e => {
            const files = e.dataTransfer.files;
            if (files.length) {
                fileInput.files = files;
                processFile(files[0]);
            }
        });
    }

    // ── 5. Avatar: remove ──
    if (removeAvatarBtn) {
        removeAvatarBtn.addEventListener('click', () => {
            if (fileInput)         fileInput.value          = '';
            if (avatarPreview)   { avatarPreview.src        = '';   avatarPreview.classList.add('d-none'); }
            if (avatarPlaceholder) {
                avatarPlaceholder.classList.remove('d-none');
                avatarPlaceholder.classList.add('d-inline-flex');
            }
            if (deleteAvatarFlag)  deleteAvatarFlag.value   = '1';
        });
    }

    // ── 6. Character counter ──
    function updateCounter() {
        if (textarea && counter) {
            counter.textContent = `${textarea.value.length}/250`;
        }
    }
    if (textarea) textarea.addEventListener('input', updateCounter);

    // ── 7. Password eye toggles ──
    document.querySelectorAll('.eye-toggle-icon').forEach(icon => {
        icon.addEventListener('click', function () {
            const input = this.parentElement.querySelector('input');
            const i     = this.querySelector('i');
            if (!input) return;
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            if (i) {
                i.className = show ? 'bi bi-eye' : 'bi bi-eye-slash';
            }
        });
    });

    // ── 8. Cancel button ──
    if (clearFormBtn) {
        clearFormBtn.addEventListener('click', () => {
            if (confirm('Discard all changes?')) window.location.reload();
        });
    }

    // ── Init ──
    hydrateForm();
});