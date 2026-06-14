<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reviewModalLabel">Commission Completed!</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                
                <div class="payment-section mb-4 text-center">
                    <a href="#" target="_blank" class="btn btn-success btn-lg w-100 fw-bold">
                        <i class="bi bi-credit-card-fill me-2"></i>Proceed to Payment
                    </a>
                </div>
                
                <hr class="my-4 text-muted">
                
                <form id="reviewForm">
                    <input type="hidden" id="modalCommissionId" value="">
                    
                    <div class="mb-3">
                        <label class="form-label d-block fw-bold">Rate the Artist:</label>
                        <div class="star-rating d-flex gap-2 fs-2 text-secondary" style="cursor: pointer;">
                            <span class="star-node" data-value="1">&#9733;</span>
                            <span class="star-node" data-value="2">&#9733;</span>
                            <span class="star-node" data-value="3">&#9733;</span>
                            <span class="star-node" data-value="4">&#9733;</span>
                            <span class="star-node" data-value="5">&#9733;</span>
                        </div>
                        <input type="hidden" id="selectedRating" value="0" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="reviewComment" class="form-label fw-bold">Leave a Comment (Optional):</label>
                        <textarea class="form-control" id="reviewComment" rows="4" placeholder="Tell us about your experience with the artist..."></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">Submit Review</button>
                </form>
                
            </div>
        </div>
    </div>
</div>