<?php
function isActive($page) {
    return basename($_SERVER['PHP_SELF']) === $page ? 'active' : '';
}
function generateCards($courses) {
    echo '<div class="course-grid">';
    $i = 1;
    foreach ($courses as $key => $course) {
        $id = $course['moduleId'];
        $courseInfo = getCourseById($id);
        $instructor = getInstructorById($courseInfo['instructorId']);
        $statusClass = strtolower($course['status']) === 'completed' ? 'status-completed' : 'status-progress';
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
                <p class="status '.$statusClass.'">Status: '.ucfirst($course['status']).'</p>
            </div>
        </a>';
        $i++;
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
                <th>Name</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>";
    foreach ($students as $student) {
        $studentInfo = getStudentById($student['studentId']);
        $name = htmlspecialchars($studentInfo['full_name']);
        $email = htmlspecialchars($studentInfo['email']);
        $html .= "
            <tr>
                <td>$name</td>
                <td>$email</td>
            </tr>
        ";
    }
    $html .= "</tbody></table>";
    return $html;
}



function generateGradesTable($studentGrade) {
    if (!$studentGrade) {
        return "<p>No grades yet recorded.</p>";
    }
    $html = "<table class='grades-table'>";
    $html .= "
        <thead>
            <tr>
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
        $quizInfo = getQuizInfo($grd['quizId']);
        $quizTitle = htmlspecialchars($quizInfo['title'] ?? 'Unknown');
        $questionsCount = htmlspecialchars($quizInfo['total_questions'] ?? 'Unknown');
        $passing_score = htmlspecialchars($quizInfo['passing_score'] ?? 'Unknown');
        $duration = htmlspecialchars($quizInfo['duration_minutes'] ?? 'Unknown');
        $grade = htmlspecialchars($grd['grades'] ?? 'Unknown');
        $feedback = htmlspecialchars($grd['feedback'] ?? 'No feedback available.');
        $html .= "
            <tr>
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
    $html .= "
            <tr>
                <td>Total</td>
                <td>$total_questions</td>
                <td>$total_passing_score</td>
                <td>$total_duration</td>
                <td>$total_grade</td>
                <td></td>
            </tr>
        ";
    $html .= "</tbody></table>";
    return $html;
}

function generetaDropDownForQuizzes($quizzes) {
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
        $output .= '<a href="quiz.php?id=' . $quizId . '" class="quiz-btn">Take Quiz</a>';
        $output .= '<a href="quiz.php?id=' . $quizId . '&result=1" class="quiz-btn results-btn">View Results</a>';
        $output .= '</div>';
        $output .= '</div>';
    }
    return $output;
}