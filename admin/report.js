let currentPage = 1;
let currentLimit = 25;
let allActivities = [];
let filteredActivities = [];

document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('activitiesTable')) {
        bindEvents();
        loadActivities();
    }
});

function bindEvents() {
    const userTypeFilter = document.getElementById('activityUserTypeFilter');
    const userIdFilter = document.getElementById('activityUserIdFilter');

    if (userTypeFilter) userTypeFilter.addEventListener('change', applyFilters);
    if (userIdFilter) userIdFilter.addEventListener('input', debounce(applyFilters, 500));

    const clearFiltersBtn = document.getElementById('clearFiltersBtn');
    const exportActivitiesBtn = document.getElementById('exportActivitiesBtn');
    const refreshActivitiesBtn = document.getElementById('refreshActivitiesBtn');
    const activitiesPerPage = document.getElementById('activitiesPerPage');
    
    if (clearFiltersBtn) clearFiltersBtn.addEventListener('click', clearFilters);
    if (exportActivitiesBtn) exportActivitiesBtn.addEventListener('click', exportActivities);
    if (refreshActivitiesBtn) refreshActivitiesBtn.addEventListener('click', refreshActivities);
    if (activitiesPerPage) {
        activitiesPerPage.addEventListener('change', function(e) {
            currentLimit = parseInt(e.target.value);
            currentPage = 1;
            renderCurrentPage();
        });
    }
    window.onclick = function(event) {
        const modal = document.getElementById('detailsModal');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    };
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

async function loadActivities() {
    try {
        showLoader();
        const formData = new FormData();
        formData.append('action', 'get_activities');
        const response = await fetch('activity_log.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();
        if (result.success) {
            allActivities = result.data || [];
            applyFilters();
        } else {
            showError('Failed to load activities: ' + result.message);
        }
    } catch (error) {
        showError('Error loading activities: ' + error.message);
    } finally {
        hideLoader();
    }
}

function applyFilters() {
    const userTypeFilter = document.getElementById('activityUserTypeFilter')?.value || '';
    const userIdFilter = document.getElementById('activityUserIdFilter')?.value || '';
    filteredActivities = allActivities.filter(activity => {
        if (userTypeFilter && activity.user_type !== userTypeFilter) {
            return false;
        }
        if (userIdFilter && !activity.user_id.toString().toLowerCase().includes(userIdFilter.toLowerCase())) {
            return false;
        }
        return true;
    });
    currentPage = 1;
    renderCurrentPage();
}

function clearFilters() {
    const filters = ['activityUserTypeFilter','activityUserIdFilter', ];
    filters.forEach(filterId => {
        const element = document.getElementById(filterId);
        if (element) element.value = '';
    });
    filteredActivities = [...allActivities];
    currentPage = 1;
    renderCurrentPage();
}

function renderCurrentPage() {
    const startIndex = (currentPage - 1) * currentLimit;
    const endIndex = startIndex + currentLimit;
    const pageActivities = filteredActivities.slice(startIndex, endIndex);
    renderActivities(pageActivities);
    renderPagination();
    updateResultsInfo();
}

function renderActivities(activities) {
    const container = document.getElementById('activitiesTableBody');
    if (!container) return;
    if (activities.length === 0) {
        container.innerHTML = `
            <tr>
                <td colspan="7" style="text-align: center; padding: 20px; color: #666;">
                    No activities found matching your criteria.
                </td>
            </tr>
        `;
        return;
    }
    
    container.innerHTML = activities.map(activity => `
        <tr>
            <input type="hidden" class="activity-checkbox" value="${activity.activityLogId}">
            <td>${escapeHtml(activity.activityLogId)}</td>
            <td>
                <span class="user-type-badge user-type-${activity.user_type}">
                    ${escapeHtml(activity.user_type)}
                </span>
            </td>
            <td>${escapeHtml(activity.user_id)}</td>
            <td>
                <span class="activity-type-badge activity-${activity.activity_type}">
                    ${formatActivityType(activity.activity_type)}
                </span>
            </td>
            <td>
                <div class="activity-details">
                    ${formatActivityDetails(activity.activity_details)}
                </div>
            </td>
            <td>${formatDateTime(activity.created_at)}</td>
            <td>
                <div class="action-buttons">
                    <button class="btn-sm btn-info" onclick="viewDetails('${activity.activityLogId}')" title="View Details">
                        View
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

function renderPagination() {
    const container = document.getElementById('activityPagination');
    if (!container) return;
    const totalPages = Math.ceil(filteredActivities.length / currentLimit);
    let paginationHTML = '';
    if (currentPage > 1) {
        paginationHTML += `<button onclick="goToPage(${currentPage - 1})" class="pagination-btn">Previous</button>`;
    }
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);
    if (startPage > 1) {
        paginationHTML += `<button onclick="goToPage(1)" class="pagination-btn">1</button>`;
        if (startPage > 2) {
            paginationHTML += `<span class="pagination-ellipsis">...</span>`;
        }
    }
    
    for (let i = startPage; i <= endPage; i++) {
        paginationHTML += `
            <button onclick="goToPage(${i})" 
                    class="pagination-btn ${i === currentPage ? 'active' : ''}">${i}</button>
        `;
    }
    
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            paginationHTML += `<span class="pagination-ellipsis">...</span>`;
        }
        paginationHTML += `<button onclick="goToPage(${totalPages})" class="pagination-btn">${totalPages}</button>`;
    }
    
    if (currentPage < totalPages) {
        paginationHTML += `<button onclick="goToPage(${currentPage + 1})" class="pagination-btn">Next</button>`;
    }
    
    container.innerHTML = paginationHTML;
}

function updateResultsInfo() {
    const info = document.getElementById('activitiesResultsInfo');
    if (!info) return;
    const start = ((currentPage - 1) * currentLimit) + 1;
    const end = Math.min(currentPage * currentLimit, filteredActivities.length);
    info.textContent = `Showing ${start}-${end} of ${filteredActivities.length} activities`;
}

function goToPage(page) {
    currentPage = page;
    renderCurrentPage();
}

function viewDetails(activityLogId) {
    const activity = allActivities.find(act => act.activityLogId === activityLogId);
    if (activity) {
        showModal('Activity Details', `
            <div class="activity-detail-view">
                <h4>Activity ID: ${activityLogId}</h4>
                <div class="detail-content">
                    ${formatActivityDetailsExpanded(activity.activity_details)}
                </div>
            </div>
        `);
    }
}

function refreshActivities() {
    loadActivities();
}

function exportActivities() {
    try {
        showLoader();
        const csvData = [];
        const headers = ['Activity ID', 'User Type', 'User ID', 'Activity Type', 'Activity Details', 'Created At'];
        csvData.push(headers);
        filteredActivities.forEach(activity => {
            const row = [
                activity.activityLogId,
                activity.user_type,
                activity.user_id,
                activity.activity_type,
                activity.activity_details.replace(/"/g, '""'),
                activity.created_at
            ];
            csvData.push(row);
        });
        const csvString = csvData.map(row => row.map(cell => `"${cell}"`).join(',')).join('\n');
        const blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `activity_logs_${new Date().toISOString().split('T')[0]}.csv`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
        showSuccess('Activities exported successfully');
    } catch (error) {
        showError('Error exporting activities: ' + error.message);
    } finally {
        hideLoader();
    }
}
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatActivityType(type) {
    return type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
}

function formatActivityDetails(details) {
    try {
        const parsed = JSON.parse(details);
        if (typeof parsed === 'object') {
            const preview = Object.keys(parsed).slice(0, 3).map(key => 
                `${key}: ${parsed[key]}`
            ).join(', ');
            return `<span class="details-preview">${preview}...</span>`;
        }
    } catch (e) {}
    
    const truncated = details.length > 100 ? details.substring(0, 100) + '...' : details;
    return `<span class="details-preview">${escapeHtml(truncated)}</span>`;
}

function formatActivityDetailsExpanded(details) {
    try {
        const parsed = JSON.parse(details);
        if (typeof parsed === 'object') {
            return `<pre>${JSON.stringify(parsed, null, 2)}</pre>`;
        }
    } catch (e) {}
    
    return `<div class="plain-text-details">${escapeHtml(details)}</div>`;
}

function formatDateTime(datetime) {
    const date = new Date(datetime);
    return date.toLocaleString();
}

function showLoader() {
    const loader = document.getElementById('loader');
    if (loader) loader.style.display = 'block';
}

function hideLoader() {
    const loader = document.getElementById('loader');
    if (loader) loader.style.display = 'none';
}

function showSuccess(message) {
    showToast(message, 'success');
}

function showError(message) {
    showToast(message, 'error');
}

function showToast(message, type = 'info') {
    const toast = document.getElementById('toast');
    if (toast) {
        toast.textContent = message;
        toast.className = `toast toast-${type} show`;
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }
}

function showModal(title, content) {
    const modal = document.getElementById('detailsModal');
    if (modal) {
        const titleElement = modal.querySelector('.modal-title');
        const bodyElement = modal.querySelector('.modal-body');
        if (titleElement) titleElement.textContent = title;
        if (bodyElement) bodyElement.innerHTML = content;
        modal.style.display = 'flex';
    }
}