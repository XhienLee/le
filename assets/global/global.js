
function showPasswordModal() {
    const modal = document.getElementById("passwordModal");
    modal.style.display = "block";
    document.body.style.overflow = "hidden";
    document.getElementById('passwordForm').reset();
    document.getElementById('passwordMessage').innerHTML = '';
}

function closePasswordModal() {
    const modal = document.getElementById("passwordModal");
    modal.style.display = "none";
    document.body.style.overflow = "";
    document.getElementById('passwordForm').reset();
    document.getElementById('passwordMessage').innerHTML = '';
}

window.onclick = function(event) {
    const passwordModal = document.getElementById("passwordModal");
    const logoutModal = document.getElementById("logoutModal");
    if (event.target === passwordModal) {
        closePasswordModal();
    }
    if (event.target === logoutModal) {
        closeLogoutModal();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('passwordForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const currentPassword = document.getElementById('current_password').value;
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const messageDiv = document.getElementById('passwordMessage');
        const loadingDiv = document.getElementById('passwordLoading');
        const submitBtn = document.querySelector('.password-update-btn');
        messageDiv.innerHTML = '';
        if (newPassword !== confirmPassword) {
            messageDiv.innerHTML = '<div class="password-message password-error">New passwords do not match!</div>';
            return;
        }
        
        if (newPassword.length < 6) {
            messageDiv.innerHTML = '<div class="password-message password-error">Password must be at least 6 characters long!</div>';
            return;
        }
        
        if (currentPassword === newPassword) {
            messageDiv.innerHTML = '<div class="password-message password-error">New password must be different from current password!</div>';
            return;
        }
        loadingDiv.style.display = 'block';
        submitBtn.disabled = true;
        submitBtn.textContent = 'Updating...';
        const formData = new FormData();
        formData.append('current_password', currentPassword);
        formData.append('new_password', newPassword);
        formData.append('confirm_password', confirmPassword);
        formData.append('action', 'update_password');
        fetch('../functions/update_password.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            loadingDiv.style.display = 'none';
            submitBtn.disabled = false;
            submitBtn.textContent = 'Update Password';
            if (data.success) {
                messageDiv.innerHTML = '<div class="password-message password-success">' + data.message + '</div>';
                document.getElementById('passwordForm').reset();
                setTimeout(() => {
                    closePasswordModal();
                    //alert('Password updated successfully!');
                }, 2000);
            } else {
                messageDiv.innerHTML = '<div class="password-message password-error">' + data.message + '</div>';
            }
        })
        .catch(error => {
            loadingDiv.style.display = 'none';
            submitBtn.disabled = false;
            submitBtn.textContent = 'Update Password';
            console.error('Error:', error);
            messageDiv.innerHTML = '<div class="password-message password-error">An error occurred. Please try again.</div>';
        });
    });
});