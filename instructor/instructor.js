document.addEventListener('DOMContentLoaded', () => {
    const courseContainer = document.querySelector('.course-grid');
    const sortBtn = document.querySelector('.sort-btn');
    const searchInput = document.querySelector('.search-input');
    
    if (!courseContainer) {
        console.error('Required element not found: .course-grid');
        return;
    }
    
    const originalOrder = Array.from(courseContainer.children);
    let currentSearchQuery = '';
    
    if (sortBtn) {
        sortBtn.addEventListener('click', () => {
            const courseCards = Array.from(courseContainer.children);
            
            if (sortBtn.classList.contains('active')) {
                applySearch(originalOrder);
                sortBtn.textContent = 'Sort by module name';
            } else {
                const sortedCards = courseCards.sort((a, b) => {
                    const titleA = a.querySelector('.course-title').textContent.toLowerCase();
                    const titleB = b.querySelector('.course-title').textContent.toLowerCase();
                    return titleA.localeCompare(titleB);
                });
                applySearch(sortedCards);
                sortBtn.textContent = 'Reset order';
            }
            sortBtn.classList.toggle('active');
        });
    }
    
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            currentSearchQuery = searchInput.value.toLowerCase().trim();
            applySearch();
        });
    }
    
    function applySearch(cardsToFilter = null) {
        let filteredCourses = cardsToFilter || originalOrder;
        
        if (currentSearchQuery) {
            filteredCourses = filteredCourses.filter(course => {
                const title = course.querySelector('.course-title').textContent.toLowerCase();
                return title.includes(currentSearchQuery);
            });
        }
        
        originalOrder.forEach(course => {
            course.style.display = 'none';
        });
        
        filteredCourses.forEach(course => {
            course.style.display = '';
        });
    }

    const createModuleForm = document.getElementById('createModuleForm');
    if (createModuleForm) {
        setupRealTimeValidation();
    }
});

function setupRealTimeValidation() {
    const form = document.getElementById('createModuleForm');
    const moduleTitle = document.getElementById('moduleTitle');
    const moduleDescription = document.getElementById('moduleDescription');
    const moduleImage = document.getElementById('moduleImage');
    const contentVideo = document.getElementById('contentVideo');
    const contentPdf = document.getElementById('contentPdf');

    const validationRules = {
        moduleTitle: {
            required: true,
            minLength: 3,
            maxLength: 100,
            pattern: /^[a-zA-Z0-9\s\-_.,!?()]+$/
        },
        moduleDescription: {
            maxLength: 500
        },
        moduleImage: {
            allowedTypes: ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'],
            maxSize: 5 * 1024 * 1024
        },
        contentVideo: {
            pattern: /^https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)$/
        },
        contentPdf: {
            allowedTypes: ['application/pdf'],
            maxSize: 10 * 1024 * 1024
        }
    };

    addErrorContainers();

    if (moduleTitle) {
        moduleTitle.addEventListener('input', () => validateField('moduleTitle'));
        moduleTitle.addEventListener('blur', () => validateField('moduleTitle'));
    }

    if (moduleDescription) {
        moduleDescription.addEventListener('input', () => validateField('moduleDescription'));
        moduleDescription.addEventListener('blur', () => validateField('moduleDescription'));
    }

    if (moduleImage) {
        moduleImage.addEventListener('change', () => validateField('moduleImage'));
    }

    if (contentVideo) {
        contentVideo.addEventListener('input', () => validateField('contentVideo'));
        contentVideo.addEventListener('blur', () => validateField('contentVideo'));
    }

    if (contentPdf) {
        contentPdf.addEventListener('change', () => validateField('contentPdf'));
    }

    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        Object.keys(validationRules).forEach(fieldName => {
            if (!validateField(fieldName)) {
                isValid = false;
            }
        });

        if (!isValid) {
            e.preventDefault();
            return;
        }

        const submitBtn = this.querySelector('.confirm-btn');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Creating...';
        submitBtn.disabled = true;
        
        setTimeout(() => {
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        }, 5000);
    });

    function addErrorContainers() {
        const fieldIds = ['moduleTitle', 'moduleDescription', 'moduleImage', 'contentVideo', 'contentPdf'];
        
        fieldIds.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field && !field.parentNode.querySelector('.error-message')) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message';
                errorDiv.style.cssText = `
                    color: #e74c3c;
                    font-size: 12px;
                    margin-top: 5px;
                    display: none;
                    animation: fadeIn 0.3s ease-in-out;
                `;
                field.parentNode.insertBefore(errorDiv, field.nextSibling);
            }
        });
    }

    function validateField(fieldName) {
        const field = document.getElementById(fieldName);
        const rules = validationRules[fieldName];
        const errorContainer = field.parentNode.querySelector('.error-message');
        let isValid = true;
        let errorMessage = '';

        if (!field || !rules) return true;

        const value = field.value.trim();

        if (rules.required && !value) {
            errorMessage = 'This field is required.';
            isValid = false;
        }
        else if (rules.minLength && value.length > 0 && value.length < rules.minLength) {
            errorMessage = `Minimum ${rules.minLength} characters required.`;
            isValid = false;
        }
        else if (rules.maxLength && value.length > rules.maxLength) {
            errorMessage = `Maximum ${rules.maxLength} characters allowed.`;
            isValid = false;
        }
        else if (rules.pattern && value && !rules.pattern.test(value)) {
            if (fieldName === 'moduleTitle') {
                errorMessage = 'Only letters, numbers, spaces, and basic punctuation allowed.';
            } else if (fieldName === 'contentVideo') {
                errorMessage = 'Please enter a valid URL starting with http:// or https://';
            }
            isValid = false;
        }
        else if (field.type === 'file' && field.files.length > 0) {
            const file = field.files[0];
            
            if (rules.allowedTypes && !rules.allowedTypes.includes(file.type)) {
                if (fieldName === 'moduleImage') {
                    errorMessage = 'Only JPG, PNG, and GIF images are allowed.';
                } else if (fieldName === 'contentPdf') {
                    errorMessage = 'Only PDF files are allowed.';
                }
                isValid = false;
            } else if (rules.maxSize && file.size > rules.maxSize) {
                const maxSizeMB = rules.maxSize / (1024 * 1024);
                errorMessage = `File size must be less than ${maxSizeMB}MB.`;
                isValid = false;
            }
        }

        if (errorContainer) {
            if (isValid || !value) {
                hideError(field, errorContainer);
            } else {
                showError(field, errorContainer, errorMessage);
            }
        }

        return isValid;
    }

    function showError(field, errorContainer, message) {
        field.style.borderColor = '#e74c3c';
        field.style.backgroundColor = '#fdf2f2';
        errorContainer.textContent = message;
        errorContainer.style.display = 'block';
        field.setAttribute('aria-invalid', 'true');
    }

    function hideError(field, errorContainer) {
        field.style.borderColor = '';
        field.style.backgroundColor = '';
        errorContainer.style.display = 'none';
        field.removeAttribute('aria-invalid');
    }

    function addCharacterCounter(fieldId, maxLength) {
        const field = document.getElementById(fieldId);
        if (!field) return;

        const counter = document.createElement('div');
        counter.className = 'character-counter';
        counter.style.cssText = `
            font-size: 11px;
            color: #666;
            text-align: right;
            margin-top: 3px;
        `;
        
        field.parentNode.appendChild(counter);
        
        function updateCounter() {
            const remaining = maxLength - field.value.length;
            counter.textContent = `${field.value.length}/${maxLength}`;
            
            if (remaining < 20) {
                counter.style.color = '#e74c3c';
            } else if (remaining < 50) {
                counter.style.color = '#f39c12';
            } else {
                counter.style.color = '#666';
            }
        }
        
        field.addEventListener('input', updateCounter);
        updateCounter();
    }

    addCharacterCounter('moduleTitle', 100);
    addCharacterCounter('moduleDescription', 500);

    if (moduleImage) {
        moduleImage.addEventListener('change', function() {
            const file = this.files[0];
            const preview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        });
    }
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

function openOverlay() {
    document.getElementById('sideOverlay').classList.add('open');
}

function closeOverlay() {
    document.getElementById('sideOverlay').classList.remove('open');
}

function showCreateModuleModal() {
    const modal = document.getElementById("createModuleModal");
    modal.style.display = "flex";
    modal.style.animation = "fadeIn 0.3s ease-in-out";
    
    setTimeout(() => {
        const fieldsToValidate = ['moduleTitle', 'moduleDescription', 'contentVideo'];
        fieldsToValidate.forEach(fieldName => {
            const field = document.getElementById(fieldName);
            if (field && field.value.trim()) {
                validateField(fieldName);
            }
        });
    }, 100);
}

function closeCreateModuleModal() {
    const modal = document.getElementById("createModuleModal");
    modal.style.display = "none";
    
    const errorMessages = modal.querySelectorAll('.error-message');
    errorMessages.forEach(error => error.style.display = 'none');
    
    const fields = modal.querySelectorAll('input, textarea');
    fields.forEach(field => {
        field.style.borderColor = '';
        field.style.backgroundColor = '';
        field.removeAttribute('aria-invalid');
    });
    
    const preview = document.getElementById('imagePreview');
    if (preview) preview.style.display = 'none';
}

document.addEventListener('click', (e) => {
    const createModuleModal = document.getElementById('createModuleModal');
    if (e.target === createModuleModal) {
        closeCreateModuleModal();
    }
    
    const logoutModal = document.getElementById('logoutModal');
    if (e.target === logoutModal) {
        closeLogoutModal();
    }
});