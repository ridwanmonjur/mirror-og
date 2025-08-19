document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const userCheckboxes = document.getElementsByClassName('user-checkbox');
    const sendButton = document.getElementById('sendButton');

    selectAllCheckbox.addEventListener('change', function() {
        Array.from(userCheckboxes).forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
        updateSendButton();
    });

    Array.from(userCheckboxes).forEach(checkbox => {
        checkbox?.addEventListener('change', function() {
            const allChecked = Array.from(userCheckboxes).every(cb => cb.checked);
            selectAllCheckbox.checked = allChecked;
            updateSendButton();
        });
    });

    function updateSendButton() {
        const hasChecked = Array.from(userCheckboxes).some(cb => cb.checked);
        sendButton.disabled = !hasChecked;
    }

    document.getElementById('betaUsersForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const checkedIds = Array.from(userCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);

        if (checkedIds.length === 0) {
            window.toastError('Please select at least one user.');
            return;
        }

        this.submit();
    });
});