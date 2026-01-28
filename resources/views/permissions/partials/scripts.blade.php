@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var editRoleModal = document.getElementById('editRoleModal');
        if (editRoleModal) {
            editRoleModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                if (!button) {
                    return;
                }
                var userId = button.getAttribute('data-user-id');
                var userName = button.getAttribute('data-user-name');
                var userEmail = button.getAttribute('data-user-email');
                var userRole = button.getAttribute('data-user-role');

                var idInput = editRoleModal.querySelector('#edit_role_user_id');
                var nameSpan = editRoleModal.querySelector('#edit_role_user_name');
                var emailSpan = editRoleModal.querySelector('#edit_role_user_email');
                var roleSelect = editRoleModal.querySelector('#edit_role_select');

                if (idInput) {
                    idInput.value = userId;
                }
                if (nameSpan) {
                    nameSpan.textContent = userName;
                }
                if (emailSpan) {
                    emailSpan.textContent = userEmail;
                }
                if (roleSelect && userRole) {
                    roleSelect.value = userRole;
                }
            });
        }

        // Screenshot Modal
        var screenshotModal = document.getElementById('screenshotModal');
        if (screenshotModal) {
            screenshotModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var imageUrl = button.getAttribute('data-image-url');
                var modalImage = screenshotModal.querySelector('#modalScreenshotImage');
                var downloadLink = screenshotModal.querySelector('#downloadScreenshotLink');
                
                if (modalImage) {
                    modalImage.src = imageUrl;
                }
                if (downloadLink) {
                    downloadLink.href = imageUrl;
                }
            });
        }

        var editRoleForm = document.getElementById('editRoleForm');
        if (editRoleForm) {
            editRoleForm.addEventListener('submit', function(e) {
                var roleSelect = editRoleForm.querySelector('select[name="role"]');
                var role = roleSelect ? roleSelect.value : '';
                if (!confirm('Are you sure you want to change role to: ' + role + '?')) {
                    e.preventDefault();
                }
            });
        }
    });
</script>
@endpush
