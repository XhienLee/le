<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../functions/session.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs Management</title>
    <link rel="stylesheet" href="report.css">
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>Activity Logs Management</h1>
            <p>Monitor and manage system activities across all user types</p>
        </div>

        <div class="content">
            <div class="activity-filters">
                <h3>Filter Activities</h3>
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="activityUserTypeFilter">User Type</label>
                        <select id="activityUserTypeFilter">
                            <option value="">All User Types</option>
                            <option value="students">Students</option>
                            <option value="instructors">Instructors</option>
                            <option value="admins">Admins</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="activityUserIdFilter">User ID</label>
                        <input type="text" id="activityUserIdFilter" placeholder="Search by User ID">
                    </div>
                <div class="filter-actions">
                    <button id="clearFiltersBtn" class="btn-sm btn-secondary">Clear Filters</button>
                </div>
            </div>

            <div class="activities-header">
                <div class="results-info">
                    <span id="activitiesResultsInfo">Loading activities...</span>
                </div>
                <div class="activities-actions">
                    <div class="per-page-selector">
                        <label for="activitiesPerPage">Show:</label>
                        <select id="activitiesPerPage">
                            <option value="25" selected>25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="200">200</option>
                        </select>
                    </div>
                    <button id="refreshActivitiesBtn" class="btn-sm btn-info">Refresh</button>
                    <button id="exportActivitiesBtn" class="btn-sm btn-success"> Export CSV</button>
                    <button class="btn-sm btn-success"> <a href="index.php" style="text-decoration: none; color: white;">Back to Dashboard </a></button>
                </div>
            </div>
            <div class="activities-table-container">
                <table id="activitiesTable" class="activities-table">
                    <thead>
                        <tr>
                            <th>Activity ID</th>
                            <th>User Type</th>
                            <th>User ID</th>
                            <th>Activity Type</th>
                            <th>Details</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="activitiesTableBody">
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 40px;">
                                <div class="loader-spinner"></div>
                                <p style="margin-top: 15px;">Loading activities...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="pagination">
                <div id="activityPagination"></div>
            </div>
        </div>
    </div>

    <div id="detailsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-close" onclick="this.closest('.modal').style.display='none'">&times;</span>
                <h2 class="modal-title">Activity Details</h2>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>

    <div id="toast" class="toast"></div>
    <div id="loader" class="loader">
        <div class="loader-spinner"></div>
    </div>
    <script src="report.js"></script>
</body>
</html>