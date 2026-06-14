document.addEventListener('DOMContentLoaded', () => {
    const modalElement = document.getElementById('reviewModal');
    const stars = document.querySelectorAll('.star-node');
    const ratingInput = document.getElementById('selectedRating');
    const reviewForm = document.getElementById('reviewForm');

    // 1. STAR HOVER & CLICK EFFECTS
    stars.forEach(star => {
        star.addEventListener('mouseover', function() {
            const val = parseInt(this.getAttribute('data-value'));
            stars.forEach(s => {
                if (parseInt(s.getAttribute('data-value')) <= val) s.classList.add('hover');
                else s.classList.remove('hover');
            });
        });

        star.addEventListener('mouseout', function() {
            stars.forEach(s => s.classList.remove('hover'));
        });

        star.addEventListener('click', function() {
            const val = parseInt(this.getAttribute('data-value'));
            ratingInput.value = val;
            stars.forEach(s => {
                if (parseInt(s.getAttribute('data-value')) <= val) s.classList.add('selected');
                else s.classList.remove('selected');
            });
        });
    });

    // 2. SUBMIT FORM TO BACKEND
    if (reviewForm) {
        reviewForm.onsubmit = function(e) {
            e.preventDefault();
            const rating = parseInt(ratingInput.value);
            if (rating === 0) {
                alert("Please select a rating between 1 and 5 stars.");
                return;
            }

            const dataPayload = {
                // Accessing the hidden input inside review_modal.php
                commission_id: parseInt(document.getElementById('modalCommissionId').value), 
                rating: rating,
                comment: document.getElementById('reviewComment').value
            };

            // Replace 'submit_review.php' with your correct relative API endpoint path if needed
            fetch('api/commissions/submit_review.php', { 
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(dataPayload)
            })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    alert(result.message);
                    const bsModalInstance = bootstrap.Modal.getInstance(modalElement);
                    if (bsModalInstance) bsModalInstance.hide();
                    location.reload(); 
                } else {
                    alert("Error: " + result.message);
                }
            })
            .catch(err => console.error("Network communication error:", err));
        };
    }
});