@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Confirm role assignment action
        document.querySelectorAll('form[action*="permissions/assign-role"]').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                const roleSelect = form.querySelector('select[name="role"]');
                const role = roleSelect ? roleSelect.value : form.querySelector('input[name="role"]').value;
                if (!confirm('Are you sure you want to assign role: ' + role + '?')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
@endpush