// ── Navbar Avatar Hydration ──
document.addEventListener('DOMContentLoaded', () => {
    const avatarLinks = document.querySelectorAll('.nav-user-avatar');
    if (!avatarLinks.length) return;

    fetch(`${BASE_URL}api/profile/fetch.php`)
        .then(res => res.json())
        .then(data => {
            if (!data.success || !data.data?.avatar_url) return;

            const avatarUrl = BASE_URL + data.data.avatar_url;

            // Replace every nav-user-avatar icon with the actual image
            // (there are two: one mobile, one desktop)
            avatarLinks.forEach(link => {
                link.innerHTML = `
                    <img
                        src="${avatarUrl}"
                        alt="Avatar"
                        class="nav-avatar-img"
                        onerror="this.replaceWith(createDefaultAvatarIcon())"
                    >
                `;
            });
        })
        .catch(() => {
            // Silently fail — the default icon stays in place
        });
});

// Fallback icon builder used by onerror above
function createDefaultAvatarIcon() {
    const i = document.createElement('i');
    i.className = 'bi bi-person-circle text-light';
    i.style.fontSize = '1.75rem';
    i.style.lineHeight = '1';
    return i;
}