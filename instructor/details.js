let isEditing = false;
let originalValues = {};
let foundStudent = null;
document.addEventListener("DOMContentLoaded", function () {
    initializeTabs();
    initializeEditFunctionality();
    initializeAlerts();
});

function initializeTabs() {
    document.querySelectorAll('.tab-btn').forEach(button => {
        button.addEventListener('click', () => {
            if (button.dataset.tab) {
                document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
                button.classList.add('active');
                const tabContent = document.getElementById(button.dataset.tab);
                if (tabContent) {
                    tabContent.classList.add('active');
                }
            }
        });
    });
}

function initializeEditFunctionality() {
    const moduleForm = document.getElementById('moduleForm');
    if (moduleForm) {
        moduleForm.addEventListener('submit', handleFormSubmission);
    }
}
function createNewQuiz(moduleId){
    window.location.href = 'quiz.php?action=create&module=' + moduleId;
}
function toggleEdit() {
    const editBtn = document.getElementById('editModuleBtn');
    const formInputs = document.querySelectorAll('.form-input, .form-textarea');
    const formActions = document.getElementById('formActions');
    if (!isEditing) {
        isEditing = true;
        originalValues = {};
        formInputs.forEach(input => {
            originalValues[input.name] = input.value;
            input.removeAttribute('readonly');
        });

        editBtn.innerHTML = '<i class="fas fa-times"></i> Cancel Edit';
        editBtn.classList.add('cancel');
        if (formActions) {
            formActions.classList.add('show');
        }
        showAlert('success', 'Edit mode enabled. Make your changes and click "Save Changes" to update.'); 
    } else {
        cancelEdit();
    }
}

function cancelEdit() {
    const editBtn = document.getElementById('editModuleBtn');
    const formInputs = document.querySelectorAll('.form-input, .form-textarea');
    const formActions = document.getElementById('formActions');
    formInputs.forEach(input => {
        if (originalValues[input.name] !== undefined) {
            input.value = originalValues[input.name];
        }
        input.setAttribute('readonly', true);
    });
    editBtn.innerHTML = '<i class="fas fa-edit"></i> Edit Module Info';
    editBtn.classList.remove('cancel');
    if (formActions) {
        formActions.classList.remove('show');
    }
    isEditing = false;
    originalValues = {};
    hideAlerts();
}

function handleFormSubmission(e) {
    e.preventDefault();
    if (!isEditing) {
        showAlert('error', 'Please click "Edit Module Info" to enable editing first.');
        return;
    }
    const formData = new FormData(e.target);
    const title = formData.get('title');
    if (!title || title.trim() === '') {
        showAlert('error', 'Title is required.');
        return;
    }
    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text();
    })
    .then(html => {
        if (html.includes('alert-success') && html.includes('display: block')) {
            showAlert('success', 'Module information updated successfully!');
            setTimeout(() => {
                cancelEdit();
                window.location.reload();
            }, 1500);
        } else if (html.includes('alert-error') && html.includes('display: block')) {
            showAlert('error', 'Failed to update module information. Please try again.');
        } else {
            showAlert('success', 'Module information updated successfully!');
            setTimeout(() => {
                cancelEdit();
            }, 1500);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'An error occurred while updating the module. Please try again.');
    });
}
function confirmDeleteModule() {
    if (isEditing) {
        showAlert('error', 'Please save or cancel your changes before deleting the module.');
        return;
    }
    const modal = document.getElementById('deleteConfirmModal');
    const titleElement = document.getElementById('moduleTitle');
    const currentTitle = document.getElementById('title').value;
    if (titleElement) {
        titleElement.textContent = currentTitle;
    }
    if (modal) {
        modal.style.display = 'block';
    }
}
function deleteModule() {
    const confirmModal = document.getElementById('deleteConfirmModal');
    const processingModal = document.getElementById('processingModal');
    if (confirmModal) {
        confirmModal.style.display = 'none';
    }
    if (processingModal) {
        processingModal.style.display = 'block';
    }
    const urlParams = new URLSearchParams(window.location.search);
    const moduleId = urlParams.get('id');
    if (!moduleId) {
        if (processingModal) {
            processingModal.style.display = 'none';
        }
        showAlert('error', 'Module ID not found');
        return;
    }
    const formData = new FormData();
    formData.append('action', 'delete_module');
    formData.append('module_id', moduleId);
    fetch('delete_module.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (processingModal) {
            processingModal.style.display = 'none';
        }

        if (data.status) {
            showAlert('success', data.message || 'Module deleted successfully! Redirecting to modules page...');
            
            setTimeout(() => {
                window.location.href = 'index.php';
            }, 2000);
        } else {
            showAlert('error', data.message || 'Failed to delete module. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (processingModal) {
            processingModal.style.display = 'none';
        }
        showAlert('error', 'An error occurred while deleting the module. Please try again.');
    });
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteConfirmModal');
    if (modal) {
        modal.style.display = 'none';
    }
}
function initializeAlerts() {
    const successAlert = document.getElementById('successAlert');
    const errorAlert = document.getElementById('errorAlert');
    if (successAlert && successAlert.style.display === 'block') {
        setTimeout(() => {
            hideAlerts();
        }, 5000);
    }
    if (errorAlert && errorAlert.style.display === 'block') {
        setTimeout(() => {
            hideAlerts();
        }, 8000);
    }
}

function showAlert(type, message) {
    const alertElement = document.getElementById(type === 'success' ? 'successAlert' : 'errorAlert');
    const otherAlert = document.getElementById(type === 'success' ? 'errorAlert' : 'successAlert');
    if (alertElement) {
        alertElement.textContent = message;
        alertElement.style.display = 'block';
    }
    
    if (otherAlert) {
        otherAlert.style.display = 'none';
    }
    
    setTimeout(() => {
        hideAlerts();
    }, type === 'success' ? 5000 : 8000);
}

function hideAlerts() {
    const successAlert = document.getElementById('successAlert');
    const errorAlert = document.getElementById('errorAlert');
    
    if (successAlert) successAlert.style.display = 'none';
    if (errorAlert) errorAlert.style.display = 'none';
}

function searchStudent() {
    const studentId = document.getElementById('student_id');
    if (!studentId) {
        console.error('Student ID input not found');
        return;
    }
    const studentIdValue = studentId.value.trim();
    if (!studentIdValue) {
        alert('Please enter a Student ID');
        return;
    }
    const resultDiv = document.getElementById('studentResult');
    const detailsDiv = document.getElementById('studentDetails');
    if (resultDiv) resultDiv.style.display = 'block';
    if (detailsDiv) detailsDiv.innerHTML = '<p>Searching for student...</p>';
    const formData = new FormData();
    formData.append('action', 'search_student');
    formData.append('student_id', studentIdValue);
    fetch('enroll_student.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        const enrollBtn = document.getElementById('enrollBtn');
        
        if (data.status && data.student) {
            foundStudent = data.student;
            displayStudentInfo(data.student);
        } else {
            if (detailsDiv) {
                detailsDiv.innerHTML = `<p style="color: #721c24;">${data.message || 'Student not found'}</p>`;
            }
            if (enrollBtn) {
                enrollBtn.style.display = 'none';
            }
            foundStudent = null;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const enrollBtn = document.getElementById('enrollBtn');
        
        if (detailsDiv) {
            detailsDiv.innerHTML = '<p style="color: #721c24;">An error occurred while searching for the student.</p>';
        }
        if (enrollBtn) {
            enrollBtn.style.display = 'none';
        }
        foundStudent = null;
    });
}

function displayStudentInfo(student) {
    const detailsDiv = document.getElementById('studentDetails');
    if (!detailsDiv) return;
    console.log(student);
    detailsDiv.innerHTML = `
        <div class="student-details">
            <p><strong>Student ID:</strong> ${escapeHtml(student.studentId)}</p>
            <p><strong>Name:</strong> ${escapeHtml(student.full_name || '')}</p>
            <p><strong>Email:</strong> ${escapeHtml(student.email || '')}</p>
            <button type="button" class="close-btn" onclick="clearSearch()">Close</button>
        </div>
    `;
}

function enrollStudent() {
    if (!foundStudent) {
        alert('No student selected for enrollment');
        return;
    }
    const urlParams = new URLSearchParams(window.location.search);
    const moduleId = urlParams.get('id');

    if (!moduleId) {
        alert('Module ID not found');
        return;
    }
    const popup = document.getElementById('enrollPopup');
    if (popup) {
        popup.style.display = 'block';
        const modalContent = popup.querySelector('.modal-content');
        if (modalContent) {
            modalContent.innerHTML = '<div class="modal-title">Processing...</div><p>Processing enrollment...</p>';
        }
    }
    const formData = new FormData();
    formData.append('action', 'enroll_student');
    formData.append('student_id', foundStudent.studentId || foundStudent.student_id);
    formData.append('module_id', moduleId);
    fetch('enroll_student.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        const popup = document.getElementById('enrollPopup');
        const modalContent = popup ? popup.querySelector('.modal-content') : null;
        
        if (data.status) {
            if (modalContent) {
                modalContent.innerHTML = `
                    <div class="modal-title">Success!</div>
                    <p>${escapeHtml(data.message)}</p>
                    <p>Refreshing page...</p>
                `;
            }
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            if (modalContent) {
                modalContent.innerHTML = `
                    <div class="modal-title">Error</div>
                    <p>${escapeHtml(data.message)}</p>
                    <button type="button" class="cancel-btn" onclick="closePopup()">Close</button>
                `;
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const popup = document.getElementById('enrollPopup');
        const modalContent = popup ? popup.querySelector('.modal-content') : null;
        
        if (modalContent) {
            modalContent.innerHTML = `
                <div class="modal-title">Error</div>
                <p>An error occurred while enrolling the student.</p>
                <button type="button" class="cancel-btn" onclick="closePopup()">Close</button>
            `;
        }
    });
}

function unenrollStudent(studentId) {
    if (!studentId) {
        alert('Student ID is required for unenrollment');
        return;
    }
    
    const urlParams = new URLSearchParams(window.location.search);
    const moduleId = urlParams.get('id');

    if (!moduleId) {
        alert('Module ID not found');
        return;
    }
    const popup = document.getElementById('unenrollPopup');
    if (popup) {
        popup.style.display = 'block';
        const modalContent = popup.querySelector('.modal-content');
        if (modalContent) {
            modalContent.innerHTML = `
                <div class="modal-title">Confirm Unenrollment</div>
                <p>Are you sure you want to unenroll this student from the module?</p>
                <div class="modal-actions">
                    <button type="button" class="confirm-btn" onclick="processUnenrollment('${studentId}', '${moduleId}', 0)">Yes, Unenroll</button>
                    <button type="button" class="cancel-btn" onclick="closeUnenrollPopup()">Cancel</button>
                </div>
            `;
        }
    }
}

function processUnenrollment(studentId, moduleId, forceUnenroll = 0) {
    const popup = document.getElementById('unenrollPopup');
    const modalContent = popup ? popup.querySelector('.modal-content') : null;
    
    if (modalContent) {
        modalContent.innerHTML = '<div class="modal-title">Processing...</div><p>Processing unenrollment...</p>';
    }

    const formData = new FormData();
    formData.append('action', 'unenroll_student');
    formData.append('student_id', studentId);
    formData.append('module_id', moduleId);
    formData.append('force_unenroll', forceUnenroll);

    fetch('enroll_student.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.status) {
            if (modalContent) {
                modalContent.innerHTML = `
                    <div class="modal-title">Success!</div>
                    <p>${escapeHtml(data.message)}</p>
                    <p>Refreshing page...</p>
                `;
            }
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            if (data.message && data.message.includes('Student have not complete the module yet')) {
                if (modalContent) {
                    modalContent.innerHTML = `
                        <div class="modal-title">Module Not Completed</div>
                        <p>${escapeHtml(data.message)}</p>
                        <div class="modal-actions">
                            <button type="button" class="confirm-btn" onclick="processUnenrollment('${studentId}', '${moduleId}', 1)">Yes, Force Unenroll</button>
                            <button type="button" class="cancel-btn" onclick="closeUnenrollPopup()">No, Cancel</button>
                        </div>
                    `;
                }
            } else {
                if (modalContent) {
                    modalContent.innerHTML = `
                        <div class="modal-title">Error</div>
                        <p>${escapeHtml(data.message)}</p>
                        <button type="button" class="cancel-btn" onclick="closeUnenrollPopup()">Close</button>
                    `;
                }
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (modalContent) {
            modalContent.innerHTML = `
                <div class="modal-title">Error</div>
                <p>An error occurred while unenrolling the student.</p>
                <button type="button" class="cancel-btn" onclick="closeUnenrollPopup()">Close</button>
            `;
        }
    });
}

function clearSearch() {
    const studentIdInput = document.getElementById('student_id');
    const resultDiv = document.getElementById('studentResult');
    const detailsDiv = document.getElementById('studentDetails');
    const enrollBtn = document.getElementById('enrollBtn');
    
    if (studentIdInput) studentIdInput.value = '';
    if (resultDiv) resultDiv.style.display = 'none';
    if (detailsDiv) detailsDiv.innerHTML = '';
    if (enrollBtn) enrollBtn.style.display = 'none';
    
    foundStudent = null;
}

function closePopup() {
    const popup = document.getElementById('enrollPopup');
    if (popup) {
        popup.style.display = 'none';
    }
}

function closeUnenrollPopup() {
    const popup = document.getElementById('unenrollPopup');
    if (popup) {
        popup.style.display = 'none';
    }
}

function escapeHtml(text) {
    const map = {'&': '&amp;','<': '&lt;','>': '&gt;','"': '&quot;',"'": '&#039;'};
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}
function toggleEdit() {
    const editBtn = document.getElementById('editModuleBtn');
    const formInputs = document.querySelectorAll('.form-input, .form-textarea');
    const formActions = document.getElementById('formActions');
    const pdfDisplaySection = document.getElementById('pdf_display_section');
    const pdfUploadSection = document.getElementById('pdf_upload_section');
    if (!isEditing) {
        isEditing = true;
        originalValues = {};
        formInputs.forEach(input => {
            if (input.type !== 'file') {
                originalValues[input.name] = input.value;
                input.removeAttribute('readonly');
            }
        });
        if (pdfDisplaySection) pdfDisplaySection.style.display = 'none';
        if (pdfUploadSection) pdfUploadSection.style.display = 'block';

        editBtn.innerHTML = '<i class="fas fa-times"></i> Cancel Edit';
        editBtn.classList.add('cancel');
        if (formActions) {
            formActions.classList.add('show');
        }
        showAlert('success', 'Edit mode enabled. Make your changes and click "Save Changes" to update.'); 
    } else {
        cancelEdit();
    }
}

function cancelEdit() {
    const editBtn = document.getElementById('editModuleBtn');
    const formInputs = document.querySelectorAll('.form-input, .form-textarea');
    const formActions = document.getElementById('formActions');
    const pdfDisplaySection = document.getElementById('pdf_display_section');
    const pdfUploadSection = document.getElementById('pdf_upload_section');
    const pdfFileInput = document.getElementById('pdf_file');
    
    formInputs.forEach(input => {
        if (input.type !== 'file' && originalValues[input.name] !== undefined) {
            input.value = originalValues[input.name];
            input.setAttribute('readonly', true);
        }
    });
    if (pdfDisplaySection) pdfDisplaySection.style.display = 'block';
    if (pdfUploadSection) pdfUploadSection.style.display = 'none';
    if (pdfFileInput) pdfFileInput.value = '';
    
    editBtn.innerHTML = '<i class="fas fa-edit"></i> Edit Module Info';
    editBtn.classList.remove('cancel');
    if (formActions) {
        formActions.classList.remove('show');
    }
    isEditing = false;
    originalValues = {};
    hideAlerts();
}
function handleFormSubmission(e) {
    e.preventDefault();
    if (!isEditing) {
        showAlert('error', 'Please click "Edit Module Info" to enable editing first.');
        return;
    }
    const formData = new FormData(e.target);
    const title = formData.get('title');
    if (!title || title.trim() === '') {
        showAlert('error', 'Title is required.');
        return;
    }
    const pdfFile = document.getElementById('pdf_file').files[0];
    if (pdfFile) {
        if (pdfFile.type !== 'application/pdf') {
            showAlert('error', 'Please select a valid PDF file.');
            return;
        }
        const maxSize = 10 * 1024 * 1024;
        if (pdfFile.size > maxSize) {
            showAlert('error', 'PDF file size must be less than 10MB.');
            return;
        }
    }
    showAlert('success', 'Updating module information...');
    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text();
    })
    .then(html => {
        if (html.includes('alert-success') && html.includes('display: block')) {
            showAlert('success', 'Module information updated successfully!');
            setTimeout(() => {
                cancelEdit();
                window.location.reload();
            }, 1500);
        } else if (html.includes('alert-error') && html.includes('display: block')) {
            showAlert('error', 'Failed to update module information. Please try again.');
        } else {
            showAlert('success', 'Module information updated successfully!');
            setTimeout(() => {
                cancelEdit();
                window.location.reload();
            }, 1500);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'An error occurred while updating the module. Please try again.');
    });
}
document.addEventListener('DOMContentLoaded', function() {
    const pdfFileInput = document.getElementById('pdf_file');
    if (pdfFileInput) {
        pdfFileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.type !== 'application/pdf') {
                    showAlert('error', 'Please select a valid PDF file.');
                    e.target.value = '';
                    return;
                }
                const maxSize = 10 * 1024 * 1024;
                if (file.size > maxSize) {
                    showAlert('error', 'PDF file size must be less than 10MB.');
                    e.target.value = '';
                    return;
                }
                
                showAlert('success', `Selected: ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`);
            }
        });
    }
});