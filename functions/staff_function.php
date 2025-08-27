<?php
function isActive($page) {
    return basename($_SERVER['PHP_SELF']) === $page ? 'active' : '';
}
function generateCards($courses) {
    echo '<div class="course-grid">';
    foreach ($courses as $key => $course) {
        $id = $course['moduleId'];
        $courseInfo = getCourseById($id);
        $instructor = getInstructorById($courseInfo['instructorId']);
        $file = $courseInfo['module_image_path'];
        if(!file_exists($file)){
            $file = "../assets/images/module/default-image.jpeg";;
        }
        echo '<a href="details.php?id='.$id.'" class="course-card">
            <div class="course-image">
                <img src="'.$file.'" alt="'.$courseInfo['title'].'" onerror="handleImageError(this)">
            </div>
            <div class="course-details">
                <h3 class="course-title">'.$courseInfo['title'].'</h3>
                <p class="instructor">Instructor: '.$instructor['full_name'].'</p>
            </div>
        </a>';
    }
    echo '</div>';
}
function generateStudentTable($students) {
    if (!$students) {
        return "<p>No other participant in this course who is in-progress.</p>";
    }
    $html = "<table class='student-table'>";
    $html .= "<thead>
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>";
    foreach ($students as $student) {
        $studentId = htmlspecialchars($student['studentId']);
        $name = htmlspecialchars($student['full_name']);
        $email = htmlspecialchars($student['email']);
        $html .= "
            <tr>
                <td>$studentId</td>
                <td>$name</td>
                <td>$email</td>
                <td>
                    <button type=\"button\" class=\"unenroll-btn\" onclick=\"unenrollStudent('$studentId')\">Unenroll</button>
                </td>
            </tr>
        ";
    }
    $html .= "</tbody></table>";
    return $html;
}




function generateGradesTable($studentGrade) {
    if (!$studentGrade) {
        return "<p>No grades yet recorded in this module.</p>";
    }
    $html = "<table class='grades-table'>";
    $html .= "
        <thead>
            <tr>
                <th>QuizId</th>
                <th>StudentId</th>
                <th>Quiz</th>
                <th>Total Question</th>
                <th>Passing Score</th>
                <th>Duration (Minutes)</th>
                <th>Grade</th>
                <th>Feedback</th>
            </tr>
        </thead>
        <tbody>
    ";
    $total_questions = 0;
    $total_passing_score = 0;
    $total_duration = 0;
    $total_grade = 0;
    foreach ($studentGrade as $grd) {
        $studentId = $grd['studentId'];
        $quizId = $grd['quizId'];
        $quizInfo = getQuizInfo($quizId);
        $quizTitle = htmlspecialchars($quizInfo['title'] ?? 'Unknown');
        $questionsCount = htmlspecialchars($quizInfo['total_questions'] ?? 'Unknown');
        $passing_score = htmlspecialchars($quizInfo['passing_score'] ?? 'Unknown');
        $duration = htmlspecialchars($quizInfo['duration_minutes'] ?? 'Unknown');
        $grade = htmlspecialchars($grd['grades'] ?? 'Unknown');
        $feedback = htmlspecialchars($grd['feedback'] ?? 'No feedback available.');
        $html .= "
            <tr>
                <td>$quizId</td>
                <td>$studentId</td>
                <td>$quizTitle</td>
                <td>$questionsCount</td>
                <td>$passing_score</td>
                <td>$duration</td>
                <td>$grade</td>
                <td>$feedback</td>
            </tr>
        ";
        $total_questions += $questionsCount;
        $total_passing_score += $passing_score;
        $total_duration += $duration;
        $total_grade += $grade;
    }
    $html .= "</tbody></table>";
    return $html;
}

function generetaDropDownForQuizzes($quizzes, $moduleId) {
    $output = '';
    if (empty($quizzes)) {
        return '<p>No quizzes available for this course.</p>';
    }
    foreach ($quizzes as $index => $quiz) {
        $quizId = htmlspecialchars($quiz['quizId']);
        $quizTitle = htmlspecialchars($quiz['title']);
        $quizDesc = htmlspecialchars($quiz['description'] ?? 'No description available');
        $output .= '<div class="quiz-item">';
        $output .= '<h3>Quiz ' . ($index + 1) . ': ' . $quizTitle . '</h3>';
        $output .= '<p> '. $quizDesc . '</p>';
        $output .= '<div class="quiz-actions">';
        $output .= '<a href="quiz.php?action=edit&id=' . $quizId . '&module=' . $moduleId . '" class="edit-btn results-btn" style="text-decoration: none; color: white;">Edit Quiz</a>';
        $output .= '<a href="quiz.php?action=delete&id=' . $quizId . '&module=' . $moduleId . '" class="delete-btn results-btn" style="text-decoration: none; color: white;">Delete Quiz</a>';
        $output .= '</div>';
        $output .= '</div>';
    }
    return $output;
}