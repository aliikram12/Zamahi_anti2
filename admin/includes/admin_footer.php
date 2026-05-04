        </div><!-- .admin-content -->
    </div><!-- .admin-main -->

    <!-- Global Delete Confirmation Modal -->
    <div class="admin-modal-overlay" id="deleteConfirmModal" style="z-index:9999;">
        <div class="admin-modal" style="max-width:420px;text-align:center;">
            <div style="width:64px;height:64px;background:rgba(231,76,60,0.08);color:#e74c3c;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:1.4rem;border:1px solid rgba(231,76,60,0.15);">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3 style="margin-bottom:10px;">Confirm Deletion</h3>
            <p style="font-size:0.88rem;color:#888;margin-bottom:24px;line-height:1.6;" id="deleteConfirmText">Are you sure you want to permanently delete this item? This action cannot be undone.</p>
            
            <form id="globalDeleteForm" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?? generateCsrfToken() ?>">
                <input type="hidden" name="action" value="delete" id="globalDeleteAction">
                <input type="hidden" name="item_id" value="" id="globalDeleteId">
                <!-- For bookings which use 'booking_id' and 'delete_booking' -->
                <input type="hidden" name="booking_id" value="" id="globalBookingId">
                
                <div style="display:flex;gap:12px;">
                    <button type="submit" class="btn-admin btn-danger" style="flex:1;justify-content:center;border-radius:100px;">Yes, Delete</button>
                    <button type="button" class="btn-admin btn-outline-gold" onclick="closeDeleteModal()" style="flex:1;justify-content:center;border-radius:100px;">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function confirmDelete(id, action = 'delete', message = null, isBooking = false) {
        const modal = document.getElementById('deleteConfirmModal');
        const form = document.getElementById('globalDeleteForm');
        const idInput = document.getElementById('globalDeleteId');
        const actionInput = document.getElementById('globalDeleteAction');
        const bookingIdInput = document.getElementById('globalBookingId');
        const text = document.getElementById('deleteConfirmText');

        if (message) text.innerText = message;
        else text.innerText = "Are you sure you want to permanently delete this item? This action cannot be undone.";

        actionInput.value = action;
        
        if (isBooking) {
            bookingIdInput.value = id;
            idInput.value = "";
            idInput.name = "unused_id";
            bookingIdInput.name = "booking_id";
        } else {
            idInput.value = id;
            bookingIdInput.value = "";
            idInput.name = "item_id";
            bookingIdInput.name = "unused_booking_id";
        }

        modal.classList.add('active');
    }

    function closeDeleteModal() {
        document.getElementById('deleteConfirmModal').classList.remove('active');
    }
    </script>
</body>
</html>
