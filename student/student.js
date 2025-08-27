document.addEventListener('DOMContentLoaded', () => {
    const courseContainer = document.querySelector('.course-grid');
    const dropdown = document.querySelector('.dropdown');
    const dropdownBtn = dropdown.querySelector('.dropdown-btn');
    const dropdownMenu = dropdown.querySelector('.dropdown-menu');
    const dropdownItems = dropdown.querySelectorAll('.dropdown-item');
    const searchInput = document.querySelector('.search-input');
    if (!courseContainer) {
        console.error('Required element not found: .course-grid');
        return;
    }
    const originalOrder = Array.from(courseContainer.children);
    let currentFilter = 'all';
    let currentSearchQuery = '';
    if (dropdown) {
        dropdownBtn.addEventListener('click', () => {
            dropdown.classList.toggle('open');
        });
        dropdownItems.forEach(item => {
            item.addEventListener('click', () => {
                const selectedText = item.textContent;
                dropdownBtn.textContent = selectedText;
                dropdown.classList.remove('open');
                
                currentFilter = item.getAttribute('data-filter');
                applyFilters();
            });
        });
        document.addEventListener('click', (e) => {
            if (!dropdown.contains(e.target)) {
                dropdown.classList.remove('open');
            }
        });
    }
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            currentSearchQuery = searchInput.value.toLowerCase().trim();
            applyFilters();
        });
    }
    function applyFilters() {
        let filteredCourses = originalOrder;
        if (currentFilter !== 'all') {
            filteredCourses = filteredCourses.filter(course => {
                const statusElement = course.querySelector('.status:nth-child(3)');
                if (statusElement) {
                    const statusText = statusElement.textContent.toLowerCase();
                    return currentFilter === 'in-progress' 
                        ? statusText.includes('in-progress') || statusText.includes('in progress')
                        : statusText.includes('completed');
                }
                return false;
            });
        }
        if (currentSearchQuery) {
            filteredCourses = filteredCourses.filter(course => {
                const title = course.querySelector('.course-title').textContent.toLowerCase();
                return title.includes(currentSearchQuery);
            });
        }
        applyCurrentFilters(filteredCourses);
    }
    function applyCurrentFilters(courses) {
        originalOrder.forEach(course => {
            course.style.display = 'none';
        });
        courses.forEach(course => {
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
let currentQuestion = 0;
let totalQuestions = 0;
let quizAnswers = {};

document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('quizForm')) {
        initializeQuiz();
    }
});

function initializeQuiz() {
    const questionContainers = document.querySelectorAll('.question-container');
    totalQuestions = questionContainers.length;
    if (totalQuestions > 0) {
        document.getElementById('totalQuestions').textContent = totalQuestions;
        createIndicators();
        updateProgress();
        updateNavigation();
        bindQuizEvents();
    }
}

function bindQuizEvents() {
    document.getElementById('prevBtn').addEventListener('click', previousQuestion);
    document.getElementById('nextBtn').addEventListener('click', nextQuestion);
    const radioButtons = document.querySelectorAll('input[type="radio"]');
    radioButtons.forEach(radio => {
        radio.addEventListener('change', handleAnswerChange);
    });
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('indicator-dot')) {
            const questionNum = parseInt(e.target.dataset.question);
            goToQuestion(questionNum);
        }
    });
    document.getElementById('quizForm').addEventListener('submit', handleSubmit);
}

function handleAnswerChange(e) {
    const questionContainer = e.target.closest('.question-container');
    const questionId = questionContainer.dataset.questionId;
    const questionNum = parseInt(questionContainer.dataset.question);
    quizAnswers[questionId] = e.target.value;
    questionContainer.querySelectorAll('.quiz-option').forEach(option => {
        option.classList.remove('selected');
    });
    e.target.closest('.quiz-option').classList.add('selected');
    updateIndicator(questionNum, true);
    updateNavigation();
}

function nextQuestion() {
    if (currentQuestion < totalQuestions - 1) {
        goToQuestion(currentQuestion + 1);
    }
}

function previousQuestion() {
    if (currentQuestion > 0) {
        goToQuestion(currentQuestion - 1);
    }
}

function goToQuestion(questionNum) {
    if (questionNum < 0 || questionNum >= totalQuestions) return;
    const currentContainer = document.querySelector('.question-container[data-question="' + currentQuestion + '"]');
    if (currentContainer) {
        currentContainer.style.display = 'none';
    }
    const targetContainer = document.querySelector('.question-container[data-question="' + questionNum + '"]');
    if (targetContainer) {
        targetContainer.style.display = 'block';
    }
    currentQuestion = questionNum;
    updateProgress();
    updateNavigation();
    updateCurrentIndicator();
}

function updateProgress() {
    const progressPercent = (currentQuestion / (totalQuestions - 1)) * 100;
    document.getElementById('currentQuestionNum').textContent = currentQuestion + 1;
    document.getElementById('progressFill').style.width = Math.max(0, progressPercent) + '%';
    document.getElementById('progressPercent').textContent = Math.round(Math.max(0, progressPercent)) + '%';
}

function updateNavigation() {
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    prevBtn.disabled = (currentQuestion === 0);
    if (currentQuestion === totalQuestions - 1) {
        nextBtn.style.display = 'none';
        submitBtn.style.display = 'inline-block';
    } else {
        nextBtn.style.display = 'inline-block';
        submitBtn.style.display = 'none';
    }
    const currentContainer = document.querySelector('.question-container[data-question="' + currentQuestion + '"]');
    if (currentContainer) {
        const currentQuestionId = currentContainer.dataset.questionId;
        const isAnswered = quizAnswers.hasOwnProperty(currentQuestionId);
        
        nextBtn.disabled = !isAnswered;
        submitBtn.disabled = Object.keys(quizAnswers).length !== totalQuestions;
    }
}

function createIndicators() {
    const container = document.getElementById('questionIndicator');
    container.innerHTML = '';
    for (let i = 0; i < totalQuestions; i++) {
        const dot = document.createElement('div');
        dot.className = 'indicator-dot';
        if (i === 0) dot.classList.add('current');
        dot.dataset.question = i;
        dot.title = 'Go to question ' + (i + 1);
        container.appendChild(dot);
    }
}

function updateIndicator(questionNum, isAnswered) {
    const dot = document.querySelector('.indicator-dot[data-question="' + questionNum + '"]');
    if (dot && isAnswered) {
        dot.classList.add('answered');
    }
}

function updateCurrentIndicator() {
    document.querySelectorAll('.indicator-dot').forEach(dot => {
        dot.classList.remove('current');
    });
    const currentDot = document.querySelector('.indicator-dot[data-question="' + currentQuestion + '"]');
    if (currentDot) {
        currentDot.classList.add('current');
    }
}

function handleSubmit(e) {
    e.preventDefault();
    if (Object.keys(quizAnswers).length !== totalQuestions) {
        alert('Please answer all questions before submitting.');
        return;
    }
    if (confirm('Are you sure you want to submit your quiz? You cannot change your answers after submission.')) {
        e.target.submit();
    }
}

function laterQuiz() {
    if (confirm('Are you sure you want to take this quiz later? Your progress will not be saved.')) {
        window.location.href = 'index.php?take_later=1';
    }
}

function startQuizNow() {
    window.location.href = 'index.php?start_quiz=1';
}
function showResultModal(quizData) {
    const quizModal = document.getElementById('quizModal');
    if (quizModal) {
        quizModal.style.display = 'none';
    }
    const scorePercentage = (quizData.userScore / quizData.totalQuestions) * 100;
    const modalHTML = `
        <div id="resultModal" class="modal" style="display: flex;">
            <div class="modal-content result-modal-content">
                <div class="result-header">
                    <h1>${quizData.title} - Results</h1>
                    ${quizData.description ? `<p>${quizData.description}</p>` : ''}
                </div>

                <div class="result-content">
                    <div class="result-score">
                        Your Score: <span class="${quizData.passed ? 'result-passed' : 'result-failed'}">
                            ${quizData.userScore} / ${quizData.totalQuestions}
                            (${scorePercentage.toFixed(1)}%)
                        </span>
                    </div>
                    
                    <div class="result-feedback">
                        <h3>Feedback:</h3>
                        <p>${quizData.feedback}</p>
                    </div>
                    
                    ${!quizData.passed ? `
                        <div class="result-info">
                            <p>The passing score is ${quizData.passingScore}%. You may retake the quiz to improve your score.</p>
                        </div>
                    ` : ''}
                    
                    <div class="button-group">
                        <button class="back-btn" onclick="closeResultModal()">Continue</button>
                        ${!quizData.passed ? '<button class="retry-btn" onclick="retakeQuiz()">Retake Quiz</button>' : ''}
                    </div>
                </div>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    document.body.style.overflow = 'hidden';
}

function closeResultModal() {
    const modal = document.getElementById('resultModal');
    if (modal) {
        modal.remove();
    }
    document.body.style.overflow = '';
    const quizModal = document.getElementById('quizModal');
    if (quizModal) {
        quizModal.style.display = 'none';
    }
    window.location.href = 'index.php';
}

function retakeQuiz() {
    const modal = document.getElementById('resultModal');
    if (modal) {
        modal.remove();
    }
    document.body.style.overflow = '';
    window.location.reload();
}
