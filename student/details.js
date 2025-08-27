document.querySelector('.circle-btn').addEventListener('click', () => {
    document.getElementById('sideOverlay').classList.add('open');
});

function closeOverlay() {
    document.getElementById('sideOverlay').classList.remove('open');
}

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('.collapsible-btn').forEach(button => {
        button.addEventListener('click', () => {
            button.classList.toggle('active');
            const content = button.nextElementSibling;
            content.style.display = content.style.display === 'block' ? 'none' : 'block';
        });
    });
});

function closeCollapsibleContent() {
    document.querySelectorAll('.collapsible-btn').forEach(button => {
        button.classList.remove('active');
        const content = button.nextElementSibling;
        content.style.display = 'none';
    });
}

document.querySelectorAll('.tab-btn').forEach(button => {
    button.addEventListener('click', () => {
        if (button.dataset.tab) {
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            button.classList.add('active');
            document.getElementById(button.dataset.tab).classList.add('active');
            closeCollapsibleContent();
        }
    });
});

function confirmUnenroll() {
    document.getElementById('more').classList.add('active');
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelector('.tab-btn[data-tab="more"]').classList.add('active');
    document.querySelectorAll('.tab-content').forEach(content => {
        if (content.id !== 'more') {
            content.classList.remove('active');
        }
    });
}

function closePopup() {
    document.getElementById('unenrollPopup').style.display = 'none';
    resetTabs();
}

function resetTabs() {
    document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.getElementById('course').classList.add('active');
    document.querySelector('.tab-btn[data-tab="course"]').classList.add('active');
    closeCollapsibleContent();
}

function processUnenroll(moduleId, studentId) {
    const formData = new FormData();
    formData.append('moduleId', moduleId);
    formData.append('studentId', studentId);
    
    const popup = document.getElementById('unenrollPopup');
    popup.style.display = 'block';
    
    const modalContent = popup.querySelector('.modal-content');
    modalContent.innerHTML = '<p>Processing your request...</p>';
    
    fetch('unenroll.php', {method: 'POST',body: formData})
    .then(response => response.json())
    .then(data => {
        if (data.status) {
            modalContent.innerHTML = `<p>${data.message}</p><p>Redirecting to course list...</p>`;
            setTimeout(() => {
                window.location.href = 'index.php';
            }, 2000);
        } else {
            modalContent.innerHTML = `<p>${data.message}</p><button type="button" class="cancel-btn" onclick="closePopup()">Close</button>`;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        modalContent.innerHTML = '<p>An error occurred. Please try again later.</p><button type="button" class="cancel-btn" onclick="closePopup()">Close</button>';
    });
}
function showLogoutModal() {
    const modal = document.getElementById("logoutModal");
    modal.style.display = "flex";
    modal.style.animation = "fadeIn 0.3s ease-in-out";
}

function closeLogoutModal() {
    const modal = document.getElementById("logoutModal");
    modal.style.display = "none";
}

function confirmLogout() {
    window.location.href = "../logout.php";
}
