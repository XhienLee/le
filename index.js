let currentUserType = 'student';

function openLoginModal() {
    document.getElementById('loginOverlay').classList.add('active');
    setTimeout(() => {
        const usernameField = document.getElementById('username');
        if (usernameField) {
            usernameField.focus();
        }
    }, 300);
}

function closeLoginModal() {
    document.getElementById('loginOverlay').classList.remove('active');
}

function showAlreadyLoggedInModal() {
    document.getElementById('alreadyLoggedInOverlay').classList.add('active');
}

function closeAlreadyLoggedInModal() {
    document.getElementById('alreadyLoggedInOverlay').classList.remove('active');
}

function redirectToDashboard() {
    window.location.href = 'index.php?redirect=1';
}

function selectUserType(type, element) {
    currentUserType = type;
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.action = `index.php?type=${type}`;
    }
    document.querySelectorAll('.user-type-btn').forEach(btn => btn.classList.remove('active'));
    if (element) {
        element.classList.add('active');
    }
    const titles = {
        student: 'Student Login',
        instructor: 'Instructor Login', 
        admin: 'Admin Login'
    };
    const titleElement = document.getElementById('loginTitle');
    if (titleElement) {
        titleElement.textContent = titles[type] || 'Login to CyberSense';
    }
    const url = new URL(window.location);
    url.searchParams.set('type', type);
    window.history.replaceState({}, '', url);
}

function togglePassword() {
    const passwordField = document.getElementById('password');
    const toggleBtn = document.querySelector('.password-toggle i');
    if (passwordField && toggleBtn) {
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            toggleBtn.className = 'fas fa-eye-slash';
        } else {
            passwordField.type = 'password';
            toggleBtn.className = 'fas fa-eye';
        }
    }
}

function contactSupport() {
    const subject = encodeURIComponent('Account Recovery Request');
    const body = encodeURIComponent(`--- ACCOUNT RECOVERY REQUEST ---
        Date of Birth: [yyyy-mm-dd]
        Full Name: [First Name, Middle Name, Last Name]
        Current Username: [Enter if remembered, leave blank if forgotten]
        Username Format: [lastname + birthday format]
    `);
    window.location.href = `mailto:cybersense@usep.edu.ph?subject=${subject}&body=${body}`;
}

document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const userType = urlParams.get('type');
    if (userType && userType.trim() !== '' && ['student', 'instructor', 'admin'].includes(userType)) {
        currentUserType = userType;
        const buttons = document.querySelectorAll('.user-type-btn');
        const targetButton = Array.from(buttons).find(btn => {
            const btnText = btn.textContent.toLowerCase().trim();
            return btnText.includes(userType.toLowerCase());
        });
        
        if (targetButton) {
            selectUserType(userType, targetButton);
        }
        setTimeout(() => {
            openLoginModal();
        }, 500);
    }

    const loginOverlay = document.getElementById('loginOverlay');
    if (loginOverlay) {
        loginOverlay.addEventListener('click', function(e) {
            if (e.target === this) {
                closeLoginModal();
            }
        });
    }

    const alreadyLoggedInOverlay = document.getElementById('alreadyLoggedInOverlay');
    if (alreadyLoggedInOverlay) {
        alreadyLoggedInOverlay.addEventListener('click', function(e) {
            if (e.target === this) {
                closeAlreadyLoggedInModal();
            }
        });
    }

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    });

    document.querySelectorAll('.feature-box').forEach(box => {
        box.style.opacity = '0';
        box.style.transform = 'translateY(20px)';
        box.style.transition = 'all 0.6s ease';
        observer.observe(box);
    });
});