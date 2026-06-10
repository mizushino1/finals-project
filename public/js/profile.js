document.addEventListener('DOMContentLoaded', function () {
    // Global Elements
    const followBtn = document.getElementById('btn-follow-action');
    const profileTabs = document.querySelectorAll('#profileTabs button');

    // 1. Initialize Profile Interactive Features
    if (followBtn) {
        followBtn.addEventListener('click', handleFollowToggle);
    }

    if (profileTabs.length > 0) {
        initTabListeners();
    }

    // Load dynamic backend profile data asynchronously on load
    checkForExtendedProfileData();
});

/**
 * Handles the AJAX Toggle for the "Favorite Artist" Action
 */
function handleFollowToggle(event) {
    const button = event.currentTarget;
    const artistId = button.getAttribute('data-artist-id');
    const isFollowing = button.getAttribute('data-following') === '1';

    // Prevent spam clicking during processing
    button.disabled = true;

    // Target endpoint context based on directory structures
    const endpoint = '../../api/favorite_action.php'; 

    // Prepare payload data
    const payload = {
        artist_id: parseInt(artistId, 10),
        action: isFollowing ? 'unfavorite' : 'favorite'
    };

    fetch(endpoint, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(payload)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response returned an execution error.');
        }
        return response.json();
    })
    .then(result => {
        if (result.success) {
            // Toggle visual attributes instantly based on response state
            if (isFollowing) {
                button.setAttribute('data-following', '0');
                button.className = 'btn btn-follow';
                button.innerHTML = '<i class="fas fa-plus me-1"></i> Favorite Artist';
            } else {
                button.setAttribute('data-following', '1');
                button.className = 'btn btn-success'; 
                button.innerHTML = '<i class="fas fa-check me-1"></i> Favorited';
            }

            // Selector to adjust UI follower counts on-the-fly
            const followerCountElement = document.querySelector('.profile-stat:first-child .profile-stat-value');
            if (followerCountElement) {
                let currentCount = parseInt(followerCountElement.innerText.replace(/,/g, ''), 10) || 0;
                currentCount = isFollowing ? Math.max(0, currentCount - 1) : currentCount + 1;
                followerCountElement.innerText = currentCount.toLocaleString();
            }
        } else {
            alert(result.message || 'An error occurred while tracking this action.');
        }
    })
    .catch(error => {
        console.error('Operation Failed:', error);
        alert('Could not update profile configurations. Please check connection records.');
    })
    .finally(() => {
        button.disabled = false;
    });
}

/**
 * Initializes Lazy-loading or Tab interaction events 
 */
function initTabListeners() {
    const triggerTabList = [].slice.call(document.querySelectorAll('#profileTabs button'));
    triggerTabList.forEach(function (tabEl) {
        tabEl.addEventListener('shown.bs.tab', function (event) {
            const targetPaneId = event.target.getAttribute('data-bs-target');
            
            if (targetPaneId === '#pane-artworks') {
                console.log('Artworks pane active. Ready to append components.');
            }
        });
    });
}

/**
 * Fetch context from profile/fetch.php to sync image placeholders if updated asynchronously
 */
function checkForExtendedProfileData() {
    const isEditPage = window.location.pathname.includes('edit.php');
    const isOwnProfileWithoutFollowBtn = !document.getElementById('btn-follow-action');

    if (isEditPage || isOwnProfileWithoutFollowBtn) {
        fetch('./api/profile/fetch.php')   // <-- fixed path
            .then(res => res.json())
            .then(resData => {
                if (resData.success && resData.data) {
                    const avatarContainer = document.querySelector('.profile-avatar-container');
                    if (avatarContainer) {
                        if (resData.data.avatar_url && !resData.data.avatar_url.includes('default-')) {
                            avatarContainer.innerHTML = `
                                <img src="${resData.data.avatar_url}"
                                     alt="User avatar"
                                     class="profile-avatar"
                                     style="width:100%; height:100%; object-fit:cover;">`;
                        } else {
                            avatarContainer.innerHTML = `
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="profile-avatar" style="width:100%; height:100%; background:#e9ecef; padding:2rem; box-sizing:border-box;">
                                    <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                                </svg>`;
                        }
                    }
                }
            })
            .catch(err => console.warn("Background configuration lookup skipped:", err));
    }
}