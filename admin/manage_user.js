document.addEventListener('DOMContentLoaded', function() {
    let currentPage = {students: 1, instructors: 1, admins: 1};
    const itemsPerPage = 10;
    let currentData = {students: [], instructors: [], admins: []};
    const tabButtons = document.querySelectorAll('.manage-type-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');
    function validateBirthday(dateOfBirth) {
        if (!dateOfBirth) {
            return { valid: false, message: 'Date of birth is required' };
        }
        
        const birthDate = new Date(dateOfBirth);
        const today = new Date();
        if (birthDate > today) {
            return { valid: false, message: 'Date of birth cannot be in the future' };
        }
        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        if (age < 16) {
            return { valid: false, message: 'User must be at least 16 years old' };
        }
        if (age > 100) {
            return { valid: false, message: 'User cannot be older than 100 years' };
        }
        return { valid: true, message: '' };
    }
    function validateName(name, fieldName = 'fullname', requireFullName = true) {
        if (!name || name.trim() === '') {
            return { valid: false, message: `${fieldName} is required` };
        }
        const trimmedName = name.trim();
        const nameRegex = /^[a-zA-Z\s'-]+$/;
        if (!nameRegex.test(trimmedName)) {
            return { valid: false, message: `${fieldName} can only contain letters, spaces, hyphens, and apostrophes` };
        }
        if (/\s{2,}|'{2,}|-{2,}/.test(trimmedName)) {
            return { valid: false, message: `${fieldName} cannot contain consecutive spaces or special characters` };
        }
        if (requireFullName) {
            const nameParts = trimmedName.split(/\s+/);
            if (nameParts.length < 2) {
                return { valid: false, message: `${fieldName} must include both first and last name` };
            }
            if (nameParts.some(part => part.length < 2)) {
                return { valid: false, message: `Each part of ${fieldName} must be at least 2 characters long` };
            }
        }
        
        return { valid: true, message: '' };
    }
    
    function validateFormData(formData) {
        const errors = [];
        if (formData.full_name !== undefined) {
            const fullNameValidation = validateName(formData.full_name);
            if (!fullNameValidation.valid) {
                errors.push(fullNameValidation.message);
            }
        }
        if (formData.date_of_birth !== undefined) {
            const birthdayValidation = validateBirthday(formData.date_of_birth);
            if (!birthdayValidation.valid) {
                errors.push(birthdayValidation.message);
            }
        }
        
        return {
            valid: errors.length === 0,
            errors: errors
        };
    }
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabPanes.forEach(pane => pane.classList.remove('active'));
            this.classList.add('active');
            const tabId = this.getAttribute('data-type');
            document.getElementById(tabId).classList.add('active');
            loadData(tabId);
        });
    });

    document.getElementById('studentSearchBtn').addEventListener('click', function() {
        searchData('students', document.getElementById('studentSearch').value);
    });
    
    document.getElementById('instructorSearchBtn').addEventListener('click', function() {
        searchData('instructors', document.getElementById('instructorSearch').value);
    });
    
    document.getElementById('adminSearchBtn').addEventListener('click', function() {
        searchData('admins', document.getElementById('adminSearch').value);
    });

    document.getElementById('studentSearch').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchData('students', this.value);
        }
    });
    
    document.getElementById('instructorSearch').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchData('instructors', this.value);
        }
    });
    
    document.getElementById('adminSearch').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchData('admins', this.value);
        }
    });
    
    const editModal = document.getElementById('editModal');
    const closeButtons = document.querySelectorAll('.close-modal');
    
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            editModal.style.display = 'none';
        });
    });
    
    document.getElementById('closeEditModal').addEventListener('click', function() {
        editModal.style.display = 'none';
    });
    
    document.getElementById('saveEditModal').addEventListener('click', function() {
        saveEditData();
    });
    
    window.addEventListener('click', function(event) {
        if (event.target === editModal) {
            editModal.style.display = 'none';
        }
    });
    
    loadData('students');
    
    function getInitial(name) {
        if (!name || typeof name !== 'string') return '';
        return name.trim().charAt(0).toLowerCase();
    }
    
    function generateEmail(firstName, middleName, lastName) {
        const firstInitial = getInitial(firstName);
        const middleInitial = getInitial(middleName);
        const lastNameLower = lastName ? lastName.trim().toLowerCase() : '';
        return [firstInitial, middleInitial, lastNameLower].join('') + '@cybersense.com';
    }

    function ensureEmailExists(userData) {
        if (!userData.email || userData.email.trim() === '') {
            userData.email = generateEmail(userData.first_name, userData.middle_name, userData.last_name);
        }
        return userData;
    }

    function processUserData(data) {
        return data.map(user => {
            return ensureEmailExists(user);
        });
    }
    
    function loadData(user_type) {
        const body = `action=load_data&user_type=${encodeURIComponent(user_type)}`;
        fetch('ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: body
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            
            const processedData = processUserData(data);
            currentData[user_type] = processedData;
            displayDataWithPagination(user_type, processedData);
        })
        .catch(error => {
            console.error('Error loading data:', error);
            document.getElementById('loader').style.display = 'none';
            document.getElementById(`${user_type}Data`).innerHTML = 'Error loading data. Please try again.';
            showToast('Failed to load data. Please try again.', 'error');
        });
    }

    function searchData(user_type, searchTerm) {
        const data = currentData[user_type] || [];
        if (!searchTerm.trim()) {
            displayDataWithPagination(user_type, data);
            return;
        }
        const lowerSearch = searchTerm.toLowerCase();
        const filteredData = data.filter(item => {
            return Object.values(item).some(value =>
                String(value).toLowerCase().includes(lowerSearch)
            );
        });
        displayDataWithPagination(user_type, filteredData);
    }
    
    function displayDataWithPagination(user_type, data) {
        const totalPages = Math.ceil(data.length / itemsPerPage);
        const startIndex = (currentPage[user_type] - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const currentPageData = data.slice(startIndex, endIndex);
        displayData(user_type, currentPageData);
        createPagination(user_type, totalPages);
    }
    
    function createPagination(user_type, totalPages) {
        const paginationContainer = document.getElementById(`${user_type}Pagination`);
        paginationContainer.innerHTML = '';
        
        if (totalPages <= 1) {
            return;
        }
        
        const prevButton = document.createElement('button');
        prevButton.textContent = 'Prev';
        prevButton.disabled = currentPage[user_type] === 1;
        prevButton.addEventListener('click', function() {
            if (currentPage[user_type] > 1) {
                currentPage[user_type]--;
                displayDataWithPagination(user_type, currentData[user_type]);
            }
        });
        paginationContainer.appendChild(prevButton);
        
        for (let i = 1; i <= totalPages; i++) {
            const pageButton = document.createElement('button');
            pageButton.textContent = i;
            
            if (i === currentPage[user_type]) {
                pageButton.classList.add('active');
            }
            pageButton.addEventListener('click', function() {
                currentPage[user_type] = i;
                displayDataWithPagination(user_type, currentData[user_type]);
            });
            
            paginationContainer.appendChild(pageButton);
        }
        
        const nextButton = document.createElement('button');
        nextButton.textContent = 'Next';
        nextButton.disabled = currentPage[user_type] === totalPages;
        nextButton.addEventListener('click', function() {
            if (currentPage[user_type] < totalPages) {
                currentPage[user_type]++;
                displayDataWithPagination(user_type, currentData[user_type]);
            }
        });
        paginationContainer.appendChild(nextButton);
    }
    
    function getIdFieldName(user_type) {
        if (user_type === 'students') {
            return 'studentId';
        } else if (user_type === 'instructors') {
            return 'instructorId';
        } else if (user_type === 'admins') {
            return 'adminId';
        }
        return 'id';
    }
    
    function displayData(user_type, data) {
        const container = document.getElementById(`${user_type}Data`);
        
        if (data.length === 0) {
            container.innerHTML = `<p>No ${user_type} found.</p>`;
            return;
        }
        
        const idField = getIdFieldName(user_type);
        
        const columnOrder = ['name', 'email', 'first_name', 'middle_name', 'last_name', 'date_of_birth', 'position', 'type'];
        const allKeys = [...new Set([
            ...columnOrder.filter(key => data.some(item => item.hasOwnProperty(key))),
            ...Object.keys(data[0]).filter(key => !columnOrder.includes(key) && key !== idField)
        ])];
        
        let html = '<table>';
        html += '<thead><tr>';
        allKeys.forEach(key => {html += `<th>${key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}</th>`;});
        html += '<th>Actions</th>';
        html += '</tr></thead>';
        html += '<tbody>';
        data.forEach(item => {
            html += '<tr>';
            allKeys.forEach(key => {
                html += `<td>${item[key] || ''}</td>`;
            });
            html += `<td>
                <button class="edit-btn" data-id="${item[idField]}" data-user_type="${user_type}">Edit</button>
                <button class="delete-btn" data-id="${item[idField]}" data-user_type="${user_type}">Delete</button>
            </td>`;
            html += '</tr>';
        });
        html += '</tbody>';
        html += '</table>';
        container.innerHTML = html;
        addActionButtonListeners(user_type);
    }
    
    function addActionButtonListeners(user_type) {
        const editButtons = document.querySelectorAll(`.edit-btn[data-user_type="${user_type}"]`);
        const deleteButtons = document.querySelectorAll(`.delete-btn[data-user_type="${user_type}"]`);
        
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                editDetails(user_type, id);
            });
        });
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                confirmDeleteUser(user_type, id);
            });
        });
    }
    
    function editDetails(user_type, id) {
        const idField = getIdFieldName(user_type);
        
        const itemId = id;
        const item = currentData[user_type].find(item => item[idField] == itemId);
        if (!item) {
            showToast('Item not found', 'error');
            return;
        }
        
        const itemWithEmail = ensureEmailExists({...item});
        
        let formHtml = `<form id="editForm" data-id="${id}" data-user_type="${user_type}">`;
        
        Object.keys(itemWithEmail).forEach(key => {
            if (key !== idField) {
                const fieldType = key === 'date_of_birth' ? 'date' : 'text';
                const readOnlyFields = ['email', 'createdBy', 'created_by', 'created_at', 'updated_at'];
                const isReadonly = readOnlyFields.includes(key) ? 'readonly' : '';
                
                if(key == 'enrollment_status'){
                    formHtml += `
                        <div class="form-group">
                            <label for="edit-${key}">Enrollment Status:</label>
                            <select id="edit-${key}" name="${key}">
                                <option value="active" ${itemWithEmail[key] === 'active' ? 'selected' : ''}>Active</option>
                                <option value="inactive" ${itemWithEmail[key] === 'inactive' ? 'selected' : ''}>Inactive</option>
                                <option value="suspended" ${itemWithEmail[key] === 'suspended' ? 'selected' : ''}>Suspended</option>
                            </select>
                        </div>
                    `;
                } else {
                    formHtml += `
                        <div class="form-group">
                            <label for="edit-${key}">${key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}:</label>
                            <input type="${fieldType}" id="edit-${key}" name="${key}" value="${itemWithEmail[key] || ''}" ${isReadonly}>
                            <div class="error-message" id="error-${key}" style="color: red; font-size: 12px; margin-top: 5px; display: none;"></div>
                        </div>
                    `;
                }
            }
        });
        
        formHtml += `<input type="hidden" name="orig${idField}" value="${id}">`;
        formHtml += '</form>';
        const singularType = user_type.endsWith('s') ? user_type.slice(0, -1) : user_type;
        document.querySelector('#editModal .modal-title').textContent = `Edit ${singularType.charAt(0).toUpperCase() + singularType.slice(1)}`;
        
        document.getElementById('editModalBody').innerHTML = formHtml;
        editModal.style.display = 'block';
        addRealTimeValidation();
        
        const firstNameInput = document.getElementById('edit-first_name');
        const middleNameInput = document.getElementById('edit-middle_name');
        const lastNameInput = document.getElementById('edit-last_name');
        const emailInput = document.getElementById('edit-email');

        function updateEmail() {
            if (firstNameInput && lastNameInput && emailInput) {
                const newEmail = generateEmail(
                    firstNameInput.value,
                    middleNameInput ? middleNameInput.value : '',
                    lastNameInput.value
                );
                emailInput.value = newEmail;
                
                const nameInput = document.getElementById('edit-name');
                if (nameInput) {
                    const nameParts = [
                        firstNameInput.value,
                        middleNameInput ? middleNameInput.value : '',
                        lastNameInput.value
                    ].filter(Boolean);
                    nameInput.value = nameParts.join(' ');
                }
            }
        }
        
        if (firstNameInput) firstNameInput.addEventListener('input', updateEmail);
        if (middleNameInput) middleNameInput.addEventListener('input', updateEmail);
        if (lastNameInput) lastNameInput.addEventListener('input', updateEmail);
    }

    function addRealTimeValidation() {
        const nameFields = ['edit-first_name', 'edit-last_name', 'edit-middle_name'];
        nameFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.addEventListener('blur', function() {
                    const fieldName = fieldId.replace('edit-', '').replace('_', ' ');
                    const validation = validateName(this.value);
                    const errorDiv = document.getElementById(`error-${fieldId.replace('edit-', '')}`);
                    
                    if (!validation.valid && this.value.trim() !== '') {
                        errorDiv.textContent = validation.message;
                        errorDiv.style.display = 'block';
                        this.style.borderColor = 'red';
                    } else {
                        errorDiv.style.display = 'none';
                        this.style.borderColor = '';
                    }
                });
            }
        });
        const dobField = document.getElementById('edit-date_of_birth');
        if (dobField) {
            dobField.addEventListener('blur', function() {
                const validation = validateBirthday(this.value);
                const errorDiv = document.getElementById('error-date_of_birth');
                
                if (!validation.valid && this.value.trim() !== '') {
                    errorDiv.textContent = validation.message;
                    errorDiv.style.display = 'block';
                    this.style.borderColor = 'red';
                } else {
                    errorDiv.style.display = 'none';
                    this.style.borderColor = '';
                }
            });
        }
    }

    function saveEditData() {
        const form = document.getElementById('editForm');
        const id = form.getAttribute('data-id');
        const user_type = form.getAttribute('data-user_type');
        const idField = getIdFieldName(user_type);
        const formData = {};
        const inputs = form.querySelectorAll('input, select');
        inputs.forEach(input => {
            formData[input.name] = input.value;
        });
        const validation = validateFormData(formData);
        if (!validation.valid) {
            showToast(validation.errors.join('. '), 'error');
            return;
        }
        
        let body = `action=save_user&user_type=${encodeURIComponent(user_type)}&${encodeURIComponent(idField)}=${encodeURIComponent(id)}`;
        
        inputs.forEach(input => {
            body += `&${encodeURIComponent(input.name)}=${encodeURIComponent(input.value)}`;
        });
        
        document.getElementById('loader').style.display = 'block';
        
        fetch('ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: body
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('loader').style.display = 'none';
            
            if (data.success) {
                editModal.style.display = 'none';
                loadData(user_type);
                showToast('Data updated successfully!', 'success');
            } else {
                showToast('Failed to update data. ' + (data.message || 'Please try again.'), 'error');
            }
        })
        .catch(error => {
            document.getElementById('loader').style.display = 'none';
            console.error('Error updating data:', error);
            showToast('Failed to update data. Please try again.', 'error');
        });
    }

    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        
        if (type === 'error') {
            toast.style.backgroundColor = '#f44336';
        } else {
            toast.style.backgroundColor = '#4CAF50';
        }
        
        toast.style.display = 'block';
        
        setTimeout(() => {
            toast.style.display = 'none';
        }, 3000);
    }

    const manageStudentBtn = document.getElementById('manageStudent');
    const addStudentBtn = document.getElementById('addStudent');
    const addUsersSection = document.getElementById('addUsersSection');
    const manageUsersSection = document.getElementById('manageUsersSection');
    
    manageStudentBtn.addEventListener('click', function() {
        manageStudentBtn.classList.add('active');
        addStudentBtn.classList.remove('active');
        addUsersSection.style.display = 'none';
        manageUsersSection.style.display = 'block';
    });
    
    addStudentBtn.addEventListener('click', function() {
        addStudentBtn.classList.add('active');
        manageStudentBtn.classList.remove('active');
        manageUsersSection.style.display = 'none';
        addUsersSection.style.display = 'block';
    });

    function confirmDeleteUser(user_type, id) {
        const adminId = getCurrentAdminId();
        const modal = document.createElement('div');
        modal.classList.add('modal');
        modal.style.display = 'block';
        modal.style.position = 'fixed';
        modal.style.zIndex = '1000';
        modal.style.left = '0';
        modal.style.top = '0';
        modal.style.width = '100%';
        modal.style.height = '100%';
        modal.style.overflow = 'auto';
        modal.style.backgroundColor = 'rgba(0,0,0,0.4)';
        
        const modalContent = document.createElement('div');
        modalContent.classList.add('modal-content');
        modalContent.style.backgroundColor = '#fefefe';
        modalContent.style.margin = '15% auto';
        modalContent.style.padding = '20px';
        modalContent.style.border = '1px solid #888';
        modalContent.style.width = '50%';
        modalContent.style.borderRadius = '5px';
        
        const singularType = user_type.endsWith('s') ? user_type.slice(0, -1) : user_type;
        modalContent.innerHTML = `
            <h2>Delete ${singularType.charAt(0).toUpperCase() + singularType.slice(1)}</h2>
            <p>Are you sure you want to delete this ${singularType}? This action cannot be undone.</p>
            <div class="form-group">
                <label for="deleteReason">Reason for deletion (required):</label>
                <textarea id="deleteReason" rows="3" style="width: 100%; margin-bottom: 15px;"></textarea>
            </div>
            <div class="button-group" style="text-align: right;">
                <button id="cancelDelete" style="margin-right: 10px; padding: 8px 15px; background-color: #ccc; border: none; border-radius: 4px; cursor: pointer;">Cancel</button>
                <button id="confirmDelete" style="padding: 8px 15px; background-color: #f44336; color: white; border: none; border-radius: 4px; cursor: pointer;">Delete</button>
            </div>
        `;
        
        modal.appendChild(modalContent);
        document.body.appendChild(modal);
        
        document.getElementById('cancelDelete').addEventListener('click', function() {
            document.body.removeChild(modal);
        });
        
        document.getElementById('confirmDelete').addEventListener('click', function() {
            const reason = document.getElementById('deleteReason').value.trim();
            
            if (!reason) {
                showToast('Please provide a reason for deletion', 'error');
                return;
            }
            
            deleteUser(user_type, id, reason, adminId, modal);
        });
        
        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                document.body.removeChild(modal);
            }
        });
    }

    function deleteUser(user_type, user_id, reason, admin_id, modal) {
        document.getElementById('loader').style.display = 'block'; 
        const formData = new FormData();
        formData.append('action', 'delete_user');
        formData.append('user_type', user_type);
        formData.append('user_id', user_id);
        formData.append('reason', reason);
        formData.append('admin_id', admin_id);
        
        fetch('ajax.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('loader').style.display = 'none';
            
            if (data.success) {
                document.body.removeChild(modal);
                loadData(user_type);
                showToast(`${user_type.slice(0, -1).charAt(0).toUpperCase() + user_type.slice(0, -1).slice(1)} deleted successfully`, 'success');
            } else {
                showToast('Failed to delete: ' + (data.message || 'Unknown error'), 'error');
            }
        })
        .catch(error => {
            document.getElementById('loader').style.display = 'none';
            console.error('Error deleting user:', error);
            showToast('Error occurred while deleting. Please try again.', 'error');
        });
    }
    
    function getCurrentAdminId() {
        const adminIdData = document.body.getAttribute('data-admin-id');
        if (adminIdData) {
            return adminIdData.trim();
        }
    }

});