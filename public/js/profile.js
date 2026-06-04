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

    // Optional: If you want to load dynamic backend profile data asynchronously on load
    // (Useful if view.php only serves structural layouts or you need to match session states)
    checkForExtendedProfileData();
});

/**
 * Handles the AJAX Toggle for the "Favorite Artist" Action
 * @param {Event} event 
 */
function handleFollowToggle(event) {
    const button = event.currentTarget;
    const artistId = button.getAttribute('data-artist-id');
    const isFollowing = button.getAttribute('data-following') === '1';

    // Prevent spam clicking during processing
    button.disabled = true;

    // Determine target endpoint context based on your directory structures
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
                button.className = 'btn btn-success'; // Changes color to show active favorite status
                button.innerHTML = '<i class="fas fa-check me-1"></i> Favorited';
            }

            // Optional: Dynamic selector to adjust UI follower counts on-the-fly
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
            
            // Example: Hook tab shifts to execute conditional UI behaviors or data fetches
            if (targetPaneId === '#pane-artworks') {
                console.log('Artworks pane active. Ready to append components.');
            }
        });
    });
}

/**
 * Fetch context from profile/fetch.php to double-check background settings match view constraints
 */
function checkForExtendedProfileData() {
    // Only triggers validation check if checking personal configuration details
    if (window.location.pathname.includes('edit.php') || !document.getElementById('btn-follow-action')) {
        fetch('fetch.php')
            .then(res => res.json())
            .then(resData => {
                if (resData.success) {
                    console.log("Verified User Profile Session Mapping:", resData.data);
                    // Handle runtime client-side operations here if necessary
                }
            })
            .catch(err => console.warn("Background configuration lookup skipped: ", err));
    }
}