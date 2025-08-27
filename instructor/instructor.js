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
    
    // Sort functionality
    if (sortBtn) {
        sortBtn.addEventListener('click', () => {
            const courseCards = Array.from(courseContainer.children);
            
            if (sortBtn.classList.contains('active')) {
                // Reset to original order
                applySearch(originalOrder);
                sortBtn.textContent = 'Sort by module name';
            } else {
                // Sort alphabetically
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
    
    // Search functionality
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
        
        // Hide all courses first
        originalOrder.forEach(course => {
            course.style.display = 'none';
        });
        
        // Show filtered courses
        filteredCourses.forEach(course => {
            course.style.display = '';
        });
    }
});

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
}

function closeCreateModuleModal() {
    const modal = document.getElementById("createModuleModal");
    modal.style.display = "none";
    document.getElementById("createModuleForm").reset();
}

document.addEventListener('DOMContentLoaded', () => {
    const createModuleForm = document.getElementById('createModuleForm');
    if (createModuleForm) {
        createModuleForm.addEventListener('submit', function(e) {
            const title = document.getElementById('moduleTitle').value.trim();
            if (!title) {
                e.preventDefault();
                alert('Module title is required!');
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
    }
});

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