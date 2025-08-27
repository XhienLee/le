document.addEventListener('DOMContentLoaded', function() {
    const ValidationUtils = {
        isValidEmail: function(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        },
        isValidName: function(name) {
            const nameRegex = /^[a-zA-Z\s\-']+$/;
            return nameRegex.test(name.trim()) && name.trim().length >= 2;
        },
        isValidDate: function(dateString) {
            if (!dateString) return false;
            const dateRegex = /^\d{4}-\d{2}-\d{2}$|^\d{2}\/\d{2}\/\d{4}$|^\d{2}-\d{2}-\d{4}$/;
            if (!dateRegex.test(dateString)) return false;
            const date = new Date(dateString);
            const now = new Date();
            const minAge = new Date(now.getFullYear() - 100, now.getMonth(), now.getDate());
            const maxAge = new Date(now.getFullYear() - 16, now.getMonth(), now.getDate());
            return date >= minAge && date <= maxAge && !isNaN(date.getTime());
        },
        isValidPhone: function(phone) {
            if (!phone || phone.trim() === '') return true;
            const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
            return phoneRegex.test(phone.replace(/[\s\-\(\)]/g, ''));
        },
        isValidCSVFile: function(file) {
            if (!file) return { valid: false, message: 'No file selected' };
            const maxSize = 5 * 1024 * 1024; // 5MB
            const allowedTypes = ['text/csv', 'application/vnd.ms-excel'];
            if (file.size > maxSize) {
                return { valid: false, message: 'File size must be less than 5MB' };
            }
            if (!file.name.toLowerCase().endsWith('.csv')) {
                return { valid: false, message: 'Only CSV files are allowed' };
            }
            return { valid: true, message: 'File is valid' };
        },

        addInputValidation: function(input, validationType) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'validation-error';
            errorDiv.style.color = '#e74c3c';
            errorDiv.style.fontSize = '12px';
            errorDiv.style.marginTop = '4px';
            errorDiv.style.display = 'none';
            input.parentNode.appendChild(errorDiv);

            const validateInput = () => {
                const value = input.value.trim();
                let isValid = true;
                let message = '';

                switch (validationType) {
                    case 'name':
                        if (value && !this.isValidName(value)) {
                            isValid = false;
                            message = 'Name must contain only letters, spaces, hyphens, and apostrophes (min 2 characters)';
                        }
                        break;
                    case 'required-name':
                        if (!value) {
                            isValid = false;
                            message = 'This field is required';
                        } else if (!this.isValidName(value)) {
                            isValid = false;
                            message = 'Name must contain only letters, spaces, hyphens, and apostrophes (min 2 characters)';
                        }
                        break;
                    case 'date':
                        if (value && !this.isValidDate(value)) {
                            isValid = false;
                            message = 'Please enter a valid date (age must be between 13-100 years)';
                        }
                        break;
                    case 'required-date':
                        if (!value) {
                            isValid = false;
                            message = 'Date of birth is required';
                        } else if (!this.isValidDate(value)) {
                            isValid = false;
                            message = 'Please enter a valid date (age must be between 13-100 years)';
                        }
                        break;
                    case 'email':
                        if (value && !this.isValidEmail(value)) {
                            isValid = false;
                            message = 'Please enter a valid email address';
                        }
                        break;
                }

                if (isValid) {
                    input.style.borderColor = '#28a745';
                    errorDiv.style.display = 'none';
                } else {
                    input.style.borderColor = '#e74c3c';
                    errorDiv.textContent = message;
                    errorDiv.style.display = 'block';
                }

                return isValid;
            };

            input.addEventListener('blur', validateInput);
            input.addEventListener('input', () => {
                if (input.style.borderColor === 'rgb(231, 76, 60)') {
                    setTimeout(validateInput, 300);
                }
            });

            return validateInput;
        }
    };

    const addBtn = document.getElementById('addStudent');
    const manageBtn = document.getElementById('manageStudent');
    const addUsersSection = document.getElementById('addUsersSection');
    const manageUsersSection = document.getElementById('manageUsersSection');
    
    addBtn.classList.add('active');
    manageBtn.classList.remove('active');
    addUsersSection.style.display = 'block';
    manageUsersSection.style.display = 'none';
    
    addBtn.addEventListener('click', function () {
        addBtn.classList.add('active');
        manageBtn.classList.remove('active');
        addUsersSection.style.display = 'block';
        manageUsersSection.style.display = 'none';
    });

    manageBtn.addEventListener('click', function () {
        manageBtn.classList.add('active');
        addBtn.classList.remove('active');
        manageUsersSection.style.display = 'block';
        addUsersSection.style.display = 'none';
    });

    const tabButtons = document.querySelectorAll('.tab-button');
    tabButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const parentSection = this.closest('.section-container') || document;
            const sectionTabButtons = parentSection.querySelectorAll('.tab-button');
            sectionTabButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            const sectionTabPanes = parentSection.querySelectorAll('.tab-pane');
            sectionTabPanes.forEach(pane => pane.classList.remove('active'));
            const tabId = this.getAttribute('data-type');
            const targetPane = document.getElementById(tabId);
            if (targetPane) {
                targetPane.classList.add('active');
            }
        });
    });

    const typeBtns = document.querySelectorAll('.add-type-btn, .manage-type-btn');
    typeBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const type = this.getAttribute('data-type');
            const parentSection = this.closest('.box-container') || document;
            const buttonClass = this.classList.contains('add-type-btn') ? '.add-type-btn' : '.manage-type-btn';
            const sectionTypeBtns = parentSection.querySelectorAll(buttonClass);
            sectionTypeBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            if (this.classList.contains('add-type-btn')) {
                const sectionUserSections = parentSection.querySelectorAll('.user-section');
                sectionUserSections.forEach(section => {
                    section.classList.remove('active');
                    const sectionId = section.id.split('-')[0];
                    if (type === 'students' && sectionId === 'student' ||
                        type === 'instructors' && sectionId === 'instructor' ||
                        type === 'admins' && sectionId === 'admin') {
                        section.classList.add('active');
                    }
                });
            } else {
                const tabPanes = parentSection.querySelectorAll('.tab-pane');
                tabPanes.forEach(pane => {
                    pane.classList.remove('active');
                    if (pane.id === type) {
                        pane.classList.add('active');
                    }
                });
            }
        });
    });

    const dropAreas = document.querySelectorAll('.drop-area');
    dropAreas.forEach(area => {
        const fileInput = area.querySelector('.file-input');
        const fileInfo = area.querySelector('.file-info');
        const filename = area.querySelector('.filename');
        
        area.addEventListener('click', (e) => {
            if (e.target === area || e.target.classList.contains('drop-text') || 
                e.target.closest('.download-icon')) {
                fileInput.click();
            }
        });

        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                const validation = ValidationUtils.isValidCSVFile(this.files[0]);
                
                if (validation.valid) {
                    filename.textContent = this.files[0].name;
                    fileInfo.style.display = 'block';
                    area.style.borderColor = '#28a745';
                    const existingError = area.querySelector('.file-validation-error');
                    if (existingError) {
                        existingError.remove();
                    }
                } else {
                    filename.textContent = '';
                    fileInfo.style.display = 'none';
                    area.style.borderColor = '#e74c3c';
                    
                    let errorDiv = area.querySelector('.file-validation-error');
                    if (!errorDiv) {
                        errorDiv = document.createElement('div');
                        errorDiv.className = 'file-validation-error';
                        errorDiv.style.color = '#e74c3c';
                        errorDiv.style.fontSize = '12px';
                        errorDiv.style.marginTop = '8px';
                        area.appendChild(errorDiv);
                    }
                    errorDiv.textContent = validation.message;
                    this.value = '';
                }
            } else {
                filename.textContent = '';
                fileInfo.style.display = 'none';
                area.style.borderColor = '';
            }
        });

        area.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });

        area.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
        });

        area.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');

            if (e.dataTransfer.files.length > 0) {
                const file = e.dataTransfer.files[0];
                const validation = ValidationUtils.isValidCSVFile(file);
                
                if (validation.valid) {
                    fileInput.files = e.dataTransfer.files;
                    filename.textContent = file.name;
                    fileInfo.style.display = 'block';
                    area.style.borderColor = '#28a745';
                    const existingError = area.querySelector('.file-validation-error');
                    if (existingError) {
                        existingError.remove();
                    }
                } else {
                    filename.textContent = '';
                    fileInfo.style.display = 'none';
                    area.style.borderColor = '#e74c3c';
                    
                    let errorDiv = area.querySelector('.file-validation-error');
                    if (!errorDiv) {
                        errorDiv = document.createElement('div');
                        errorDiv.className = 'file-validation-error';
                        errorDiv.style.color = '#e74c3c';
                        errorDiv.style.fontSize = '12px';
                        errorDiv.style.marginTop = '8px';
                        area.appendChild(errorDiv);
                    }
                    errorDiv.textContent = validation.message;
                }
            }
        });
    });

    const manualButtons = document.querySelectorAll('.manual-btn');
    manualButtons.forEach(button => {
        button.addEventListener('click', () => {
            const section = button.closest('.user-section');
            const type = section.id.split('-')[0];
            const resultsArea = section.querySelector('.results-area');
            
            let formFields = `
                <div class="form-group">
                    <label for="${type}-first-name">First Name: <span style="color: #e74c3c;">*</span></label>
                    <input type="text" id="${type}-first-name" name="first_name" required>
                </div>
                <div class="form-group">
                    <label for="${type}-middle-name">Middle Name:</label>
                    <input type="text" id="${type}-middle-name" name="middle_name">
                </div>
                <div class="form-group">
                    <label for="${type}-last-name">Last Name: <span style="color: #e74c3c;">*</span></label>
                    <input type="text" id="${type}-last-name" name="last_name" required>
                </div>
                <div class="form-group">
                    <label for="${type}-dob">Date of Birth: <span style="color: #e74c3c;">*</span></label>
                    <input type="date" id="${type}-dob" name="date_of_birth" required>
                </div>
                <div class="form-group">
                    <label for="${type}-email">Email:</label>
                    <input type="email" id="${type}-email" name="email">
                </div>
                <div class="form-group">
                    <label for="${type}-phone">Phone:</label>
                    <input type="tel" id="${type}-phone" name="phone">
                </div>
            `;
            
            resultsArea.innerHTML = `
                <form id="${type}-manual-form" class="manual-form">
                    <h3>Manual ${type.charAt(0).toUpperCase() + type.slice(1)} Entry</h3>
                    <p style="font-size: 12px; color: #666; margin-bottom: 15px;">Fields marked with <span style="color: #e74c3c;">*</span> are required</p>
                    ${formFields}
                    <button type="submit" class="btn btn-primary">Add ${type.charAt(0).toUpperCase() + type.slice(1)}</button>
                </form>
            `;
            const form = document.getElementById(`${type}-manual-form`);
            const firstNameInput = form.querySelector(`#${type}-first-name`);
            const middleNameInput = form.querySelector(`#${type}-middle-name`);
            const lastNameInput = form.querySelector(`#${type}-last-name`);
            const dobInput = form.querySelector(`#${type}-dob`);
            const emailInput = form.querySelector(`#${type}-email`);
            const phoneInput = form.querySelector(`#${type}-phone`);
            
            const validators = {
                firstName: ValidationUtils.addInputValidation(firstNameInput, 'required-name'),
                middleName: ValidationUtils.addInputValidation(middleNameInput, 'name'),
                lastName: ValidationUtils.addInputValidation(lastNameInput, 'required-name'),
                dob: ValidationUtils.addInputValidation(dobInput, 'required-date'),
                email: ValidationUtils.addInputValidation(emailInput, 'email'),
                phone: ValidationUtils.addInputValidation(phoneInput, 'phone')
            };
            
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                let isFormValid = true;
                Object.values(validators).forEach(validator => {
                    if (!validator()) {
                        isFormValid = false;
                    }
                });
                
                if (!isFormValid) {
                    displayError(resultsArea, 'Please fix the validation errors before submitting.');
                    return;
                }
                
                const formData = new FormData(form);
                const userData = {};
                formData.forEach((value, key) => {
                    userData[key] = value.trim();
                });
                userData.type = type;
                if (!userData.email) {
                    userData.email = generateEmail(userData.first_name, userData.middle_name, userData.last_name);
                }

                userData.name = [userData.first_name, userData.middle_name, userData.last_name].filter(Boolean).join(' ');
                
                displayResults(resultsArea, {
                    success: true,
                    message: `${type.charAt(0).toUpperCase() + type.slice(1)} added successfully`,
                    users: [userData]
                });
            });
        });
    });

    const importButtons = document.querySelectorAll('.import-btn');
    importButtons.forEach(button => {
        button.addEventListener('click', () => {
            const section = button.closest('.user-section');
            const type = section.id.split('-')[0];
            const resultsArea = section.querySelector('.results-area');
            const fileInput = section.querySelector('.file-input');
            
            if (!fileInput.files || fileInput.files.length === 0) {
                displayError(resultsArea, 'Please select a file to import.');
                return;
            }
            
            const file = fileInput.files[0];
            const validation = ValidationUtils.isValidCSVFile(file);
            
            if (!validation.valid) {
                displayError(resultsArea, validation.message);
                return;
            }
            
            processCSVFile(file, resultsArea, type);
        });
    });
});

function getInitial(name) {
    if (!name) return '';
    return name.trim().charAt(0).toLowerCase();
}

function generateEmail(firstName, middleName, lastName) {
    return [getInitial(firstName), getInitial(middleName), lastName.trim().toLowerCase()].filter(Boolean).join('') + '@cybersense.com';
}

function processCSVFile(file, resultsArea, type) {
    displayMessage(resultsArea, 'Processing CSV file...', 'info-message');
    const reader = new FileReader();
    
    reader.onload = function(event) {
        try {
            const csvData = event.target.result;            
            if (!csvData || csvData.trim().length === 0) {
                throw new Error('CSV file is empty');
            }
            
            const parsedData = parseCSV(csvData);
            if (parsedData.length < 2) {
                throw new Error('CSV file must contain at least a header row and one data row');
            }
            
            const headers = parsedData[0];
            const dataRows = parsedData.slice(1).filter(row => row.some(cell => cell.trim() !== ''));
            
            if (dataRows.length === 0) {
                throw new Error('No valid data rows found in CSV file');
            }
            
            validateHeaders(headers, type);
            const processedData = processData(dataRows, headers, type);

            displayResults(resultsArea, {
                success: true,
                message: `Successfully imported ${dataRows.length} ${type}${dataRows.length !== 1 ? 's' : ''}`,
                users: processedData
            });
        } catch (error) {
            displayError(resultsArea, `Error processing CSV: ${error.message}`);
        }
    };
    
    reader.onerror = function() {
        displayError(resultsArea, 'Error reading file. Please try again.');
    };
    
    reader.readAsText(file);
}

function parseCSV(text) {
    const rows = [];
    const lines = text.split(/\r?\n/);
    
    for (let line of lines) {
        if (!line.trim()) continue;
        
        const result = [];
        let i = 0;
        
        while (i < line.length) {
            let char = line[i];
            if (char === '"') {
                let value = '';
                i++;
                while (i < line.length) {
                    if (line[i] === '"' && line[i + 1] === '"') {
                        value += '"';
                        i += 2;
                    } else if (line[i] === '"') {
                        i++;
                        break;
                    } else {
                        value += line[i++];
                    }
                }
                result.push(value);
                if (line[i] === ',') i++;
            } else {
                let value = '';
                while (i < line.length && line[i] !== ',') {
                    value += line[i++];
                }
                result.push(value.trim());
                if (line[i] === ',') i++;
            }
        }
        
        if (result.length > 0 && result.some(v => v.trim() !== '')) {
            rows.push(result);
        }
    }
    return rows;
}

function validateHeaders(headers, type) {
    let requiredHeaders = ['first_name', 'last_name', 'date_of_birth'];
    const lowercaseHeaders = headers.map(h => h.toLowerCase().trim());
    
    for (const required of requiredHeaders) {
        if (!lowercaseHeaders.includes(required.toLowerCase())) {
            throw new Error(`Missing required column: ${required.replace('_', ' ')}`);
        }
    }
    const headerCounts = {};
    lowercaseHeaders.forEach(header => {
        headerCounts[header] = (headerCounts[header] || 0) + 1;
        if (headerCounts[header] > 1) {
            throw new Error(`Duplicate column found: ${header}`);
        }
    });
}

function processData(dataRows, headers, type) {
    const processedData = [];
    const lowercaseHeaders = headers.map(h => h.toLowerCase().trim());
    const errors = [];
    
    for (let i = 0; i < dataRows.length; i++) {
        const row = dataRows[i];
        const userData = {};
        const rowNumber = i + 2;
        
        try {
            for (let j = 0; j < Math.min(headers.length, row.length); j++) {
                const header = lowercaseHeaders[j];
                const value = row[j] ? row[j].toString().trim() : '';
                
                if (value === '') {
                    continue;
                }
                if (header === 'email' && value) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(value)) {
                        throw new Error(`Invalid email format in row ${rowNumber}: ${value}`);
                    }
                }
                
                if ((header === 'first_name' || header === 'last_name' || header === 'middle_name') && value) {
                    const nameRegex = /^[a-zA-Z\s\-']+$/;
                    if (!nameRegex.test(value)) {
                        throw new Error(`Invalid name format in row ${rowNumber} (${header}): ${value}`);
                    }
                }
                
                userData[header] = value;
            }

            userData.name = [
                userData.first_name || '',
                userData.middle_name || '',
                userData.last_name || ''
            ].filter(Boolean).join(' ');
            
            userData.type = type;
            if (!userData.email && userData.first_name && userData.last_name) {
                userData.email = generateEmail(userData.first_name, userData.middle_name, userData.last_name);
            }
            
            validateRequiredFields(userData, type, rowNumber);
            processedData.push(userData);
            
        } catch (error) {
            errors.push(`Row ${rowNumber}: ${error.message}`);
        }
    }
    
    if (errors.length > 0) {
        throw new Error(`Validation errors found:\n${errors.join('\n')}`);
    }
    
    return processedData;
}

function validateRequiredFields(userData, type, rowNumber) {
    let requiredFields = ['first_name', 'last_name', 'date_of_birth'];
    
    for (const field of requiredFields) {
        if (!userData[field] || userData[field].trim() === '') {
            throw new Error(`Missing required field '${field.replace('_', ' ')}'`);
        }
    }
    
    if (userData.date_of_birth) {
        const dateRegex = /^\d{4}-\d{2}-\d{2}$|^\d{2}\/\d{2}\/\d{4}$|^\d{2}-\d{2}-\d{4}$/;
        if (!dateRegex.test(userData.date_of_birth)) {
            throw new Error(`Invalid date format for date of birth: ${userData.date_of_birth}. Use YYYY-MM-DD, MM/DD/YYYY or MM-DD-YYYY format.`);
        }
        const date = new Date(userData.date_of_birth);
        const now = new Date();
        const minAge = new Date(now.getFullYear() - 100, now.getMonth(), now.getDate());
        const maxAge = new Date(now.getFullYear() - 13, now.getMonth(), now.getDate());
        
        if (isNaN(date.getTime())) {
            throw new Error(`Invalid date: ${userData.date_of_birth}`);
        }
        
        if (date < minAge || date > maxAge) {
            throw new Error(`Date of birth indicates age outside acceptable range (13-100 years): ${userData.date_of_birth}`);
        }
    }
}

function displayError(resultsArea, message) {
    resultsArea.innerHTML = `<div class="error-message" style="color: #e74c3c; padding: 10px; border: 1px solid #e74c3c; border-radius: 4px; background-color: #fdf2f2;">${message}</div>`;
}

function displayMessage(resultsArea, message, className) {
    const styles = {
        'info-message': 'color: #3498db; background-color: #ebf3fd; border-color: #3498db;',
        'success-message': 'color: #27ae60; background-color: #eafaf1; border-color: #27ae60;',
        'warning-message': 'color: #f39c12; background-color: #fef9e7; border-color: #f39c12;'
    };
    
    const style = styles[className] || styles['info-message'];
    resultsArea.innerHTML = `<div class="${className}" style="${style} padding: 10px; border: 1px solid; border-radius: 4px;">${message}</div>`;
}

function getTypeFromResultsArea(resultsArea) {
    const section = resultsArea.closest('.user-section');
    if (section) {
        return section.id.split('-')[0];
    }
    return '';
}

function displayResults(resultsArea, data) {
    if (!data.success) {
        displayError(resultsArea, data.message);
        return;
    }
    
    const type = getTypeFromResultsArea(resultsArea);
    let html = `<div class="success-message" style="color: #27ae60; background-color: #eafaf1; border: 1px solid #27ae60; border-radius: 4px; padding: 10px; margin-bottom: 15px;">${data.message}</div>`;
    
    if (data.users && data.users.length > 0) {
        data.users.forEach(user => {
            if (!user.type) {
                user.type = type;
            }
        });
        
        html += '<div style="overflow-x: auto;">';
        html += '<table style="width: 100%; border-collapse: collapse; margin: 10px 0;">';
        html += '<thead><tr style="background-color: #f8f9fa;">';
        
        const headers = Object.keys(data.users[0]);
        headers.forEach(header => {
            html += `<th style="border: 1px solid #dee2e6; padding: 8px; text-align: left;">${header.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}</th>`;
        });

        html += '</tr></thead><tbody>';
        data.users.forEach((user, index) => {
            const rowStyle = index % 2 === 0 ? 'background-color: #f8f9fa;' : '';
            html += `<tr style="${rowStyle}">`;
            headers.forEach(header => {
                html += `<td style="border: 1px solid #dee2e6; padding: 8px;">${user[header] || ''}</td>`;
            });
            html += '</tr>';
        });
        html += '</tbody></table>';
        html += '</div>';
        
        html += `
            <div class="action-buttons" data-type="${type}" style="margin-top: 15px;">
                <button type="button" class="btn btn-secondary cancel-btn" style="margin-right: 10px;">Cancel</button>
                <button type="button" class="btn btn-primary save-btn">Save ${type.charAt(0).toUpperCase() + type.slice(1)}s</button>
            </div>
        `;
    }
    
    resultsArea.innerHTML = html;
    
    const cancelBtn = resultsArea.querySelector('.cancel-btn');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', () => {
            resultsArea.innerHTML = '';
        });
    }
    
    const saveBtn = resultsArea.querySelector('.save-btn');
    if (saveBtn) {
        saveBtn.addEventListener('click', () => {
            saveBtn.disabled = true;
            saveBtn.textContent = 'Saving...';
            displayMessage(resultsArea, `Saving ${type} data...`, 'info-message');
            const requestData = {
                users: data.users,
                type: type,
                admin: window.sessionData ? window.sessionData.adminId : null
            };
            
            if (!requestData.users || requestData.users.length === 0) {
                displayError(resultsArea, 'No user data to save');
                saveBtn.disabled = false;
                saveBtn.textContent = `Save ${type.charAt(0).toUpperCase() + type.slice(1)}s`;
                return;
            }
            
            fetch('save.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(requestData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(result => {
                if (result.insert) {
                    const messages = result.insert;
                    let allMsg = '';
                    let hasErrors = false;
                    
                    messages.forEach(function(message) {
                        allMsg += message.message + "<br>";
                        if (!message.status) {
                            hasErrors = true;
                        }
                    });
                    const messageClass = hasErrors ? 'error-message' : 'success-message';
                    displayMessage(resultsArea, allMsg, messageClass);
                    if (!hasErrors) {
                        const section = resultsArea.closest('.user-section');
                        const fileInput = section.querySelector('.file-input');
                        if (fileInput) {
                            fileInput.value = '';
                            const filename = section.querySelector('.filename');
                            const fileInfo = section.querySelector('.file-info');
                            if (filename) filename.textContent = '';
                            if (fileInfo) fileInfo.style.display = 'none';
                        }
                    }
                }
            })
            .catch(error => {
                displayError(resultsArea, `Network error: ${error.message}. Please check your connection and try again.` );
            })
            .finally(() => {
                saveBtn.disabled = false;
                saveBtn.textContent = `Save ${type.charAt(0).toUpperCase() + type.slice(1)}s`;
            });
        });
    }
}