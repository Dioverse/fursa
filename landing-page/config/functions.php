<?php 
    
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    

    date_default_timezone_set('Europe/London');

    
    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';
    require 'guzzle/vendor/autoload.php';
    
    $logFile = dirname(__DIR__) . '/backup_log.txt';
    
    function sendMail($receiver, $body, $subject, $attachment=null){
        $mail = new PHPMailer(true);

        try {
            
            $mail->isSMTP();       
            $mail->Host       = 'smtp.hostinger.com';        
            $mail->SMTPAuth   = true;               
            $mail->Username   = 'welcome@fursaenergy.com'; //'newsletter@binaltechnologies.com';// noreply@trainify360.co.uk
            $mail->Password   = 'fkwMtyLY85BMDYw84WyQ'; // 'Od8*aTfRbbj+'; //;        
            $mail->Port       = 587;               
            $mail->isHTML(true);
            $mail->setFrom('welcome@fursaenergy.com', 'Fursa Energy'); 
            $mail->addAddress($receiver);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            
            if ($attachment !== null) {
                $mail->addAttachment($attachment);
            }
            
            $mail->send();
            return true;
        } catch (Exception $e) {
            return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
    
    
    function logMessage($message) {
        global $logFile;
        file_put_contents($logFile, "[".date('Y-m-d H:i:s')."] " . $message . PHP_EOL, FILE_APPEND);
    }
    

    class DatabaseHandler {
        private $conn;
    
        public function __construct($conn) {
            $this->conn = $conn;
        }
        // Change Password
    
        function changePassword($userId, $oldPassword, $newPassword, $confirmPassword) {
            if ($newPassword !== $confirmPassword) {
                return "New password and confirm password do not match.";
            }
        
            $user = $this->singleFetchData('users', ['id' => $userId]);
            if (!password_verify($oldPassword, $user['password'])) {
                return "Old password is incorrect.";
            }
        
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            if ($this->updateData('users', ['password'=>$hashedPassword], ['id'=>$userId])) {
                return true;
            } else {
                return "Failed to change password. Please try again.";
            }
        }
        
        function getDepartmentPermissions($dept_id) {
            $permissions = [
                'learners' => ['create', 'edit', 'delete', 'view'],
                'admin' => ['view', 'create', 'edit', 'delete'],
                'material' => ['create', 'view'],
                'event' => ['create', 'edit', 'delete', 'view'],
                'summary' => ['view'],
                'perfomance' => ['view'],
                'certificate' => ['view', 'create'],
                'study' => ['view'],
                'enrollment' => ['view', 'approve', 'reject'],
                'course' => ['allocate'],
                'group' => ['view', 'create', 'delete', 'update', 'assign'],
                'payment' => ['view', 'create'],
                'method' => ['create'],
                'matrix' => ['grouping', 'export'],
                'matrices' => ['view'],
                'validity' => ['view', 'create', 'delete'],
            ];
            
            $menuPermissions = [];
        
            foreach ($permissions as $menu => $actions) {
                foreach ($actions as $action) {
                    $query = $this->fetchWithSQL("SELECT permission FROM `department_permissions` WHERE dept_id = $dept_id AND menu = '$menu' AND action = '$action'");
                    $menuPermissions[$menu][$action] = isset($query[0]['permission']) ? $query[0]['permission'] : 0;
                }
            }
        
            return $menuPermissions;
        }
        
        function checkPermissionForPage($page_name, $department) {
            $viewPageQuery = $this->fetchWithSQL("SELECT permission FROM `department_permissions` WHERE dept_id = '$department' AND page = '$page_name' AND action = 'view' ");
            $viewPage = isset($viewPageQuery[0]['permission'])? $viewPageQuery[0]['permission'] : 0;
            return $viewPage;
        }
        
        function getFirstLettersOfWords($input) {
            $words = explode(' ', $input);
            $firstLetters = array_map(function ($word) {
                return !empty($word) ? $word[0] : ''; 
            }, $words);
            return implode('', $firstLetters);
        }
        
        function generatePassword($length = 8) {
            $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_';
            $charLength = strlen($chars);
        
            $password = '';
        
            for ($i = 0; $i < $length; $i++) {
                $index = rand(0, $charLength - 1);
                $password .= $chars[$index];
            }
        
            return $password;
        }
        
        function addIntervalToDate($interval) {
            $currentDate = new DateTime();
            $currentDate->modify($interval);
            return $currentDate->format('Y-m-d H:i:s');
        }
    
        
        function convertMinutesToHoursAndMinutes($minutes) {
            $hours = floor($minutes / 60);
            $remainingMinutes = (int)$minutes % 60;
        
            return sprintf('%02d:%02d', (int)$hours, (int)$remainingMinutes);
        }
    
        function totalCourses(){
            $allCourses = $this->fetchData('courses',['status' => 1]);;
            return count($allCourses);
        }
        function myCourses(){
            $user_id = $_SESSION['user_id'];
            return count($this->fetchWithSQL("SELECT courses.*, reports.is_complete FROM courses LEFT JOIN course_requests ON course_requests.course_id = courses.id LEFT JOIN reports ON reports.course_id = courses.id AND course_requests.user_id = reports.user_id WHERE course_requests.status = 1 AND course_requests.user_id = $user_id AND (reports.is_complete IS NULL ) ORDER BY course_requests.created_at desc "));
        }
        function completedCourses(){
            return count($this->fetchDataWithJoin('reports', 'courses', 'reports.course_id = courses.id', 'courses.*', array('reports.is_complete' => '1', 'reports.user_id' => $_SESSION['user_id'])));
        }
        
        function allMyCourses($user_id){
            return count($this->fetchData('course_requests', ['user_id' => $user_id, 'status' => 1]));
        }
    
    
        // Insert data into the database
        public function insertData($table, $data) {
            $columns = implode(", ", array_keys($data));
            $values ="'" . implode("', '", array_values($data)) . "'";
            
            $query = "INSERT INTO $table ($columns) VALUES ($values)";
            $result = $this->conn->query($query);
            if($result == false){
                return false;
            }
            return true;
        }
        
        public function insertTrimData($table, $data) {
            // Trim data
            $data = array_map('trim', $data);
        
            // Prepare column names and placeholders
            $columns = implode(", ", array_keys($data));
            $placeholders = implode(", ", array_fill(0, count($data), '?'));
        
            // Create the SQL query
            $query = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        
            // Prepare the statement
            $stmt = $this->conn->prepare($query);
        
            if ($stmt === false) {
                return false;
            }
        
            // Determine types and bind parameters
            $values = array_values($data);
            foreach ($values as $index => $value) {
                if (is_int($value)) {
                    $type = PDO::PARAM_INT;
                } elseif (is_bool($value)) {
                    $type = PDO::PARAM_BOOL;
                } elseif (is_null($value)) {
                    $type = PDO::PARAM_NULL;
                } else {
                    $type = PDO::PARAM_STR;
                }
                $stmt->bindValue($index + 1, $value, $type);
            }
        
            // Execute the statement
            $result = $stmt->execute();
        
            return $result;
        }
    
    
        // Fetch data from the database
        public function fetchData($table, $conditions = array()) {
            $query = "SELECT * FROM $table";
        
            if (!empty($conditions)) {
                $query .= " WHERE " . implode(" AND ", array_map(function($key, $value) {
                    return "$key = '$value'";
                }, array_keys($conditions), $conditions));
            }
        
            $result = $this->conn->query($query);
        
            return $result->fetchAll(PDO::FETCH_ASSOC);
        }
        public function singleFetchData($table, $conditions = array()) {
            $query = "SELECT * FROM $table";
        
            if (!empty($conditions)) {
                $query .= " WHERE " . implode(" AND ", array_map(function($key, $value) {
                    return "$key = '$value'";
                }, array_keys($conditions), $conditions));
            }
        
            $result = $this->conn->query($query);
        
            return $result->fetch(PDO::FETCH_ASSOC);
        }
        public function fetchDataWithJoin($table1, $table2, $joinCondition, $columns = '*', $conditions = array()) {
            $query = "SELECT $columns FROM $table1
                      INNER JOIN $table2 ON $joinCondition";
        
            if (!empty($conditions)) {
                $query .= " WHERE " . implode(" AND ", array_map(function($key, $value) {
                    return "$key = '$value'";
                }, array_keys($conditions), $conditions));
            }
        
            $result = $this->conn->query($query);
        
            return $result->fetchAll(PDO::FETCH_ASSOC);
        }
        public function fetchWithSQL($query) {
        
            $result = $this->conn->query($query);
        
            return $result->fetchAll(PDO::FETCH_ASSOC);
        }
        
        public function fetchWithPagination($query, $currentPage, $itemsPerPage = 16, $pageIdentifier = 'page'){
            $offset = ($currentPage-1) * $itemsPerPage;
            $paginatedSql = "$query LIMIT $itemsPerPage OFFSET $offset";
            
            $result = $this->conn->query($paginatedSql);
            $data = [];
            while ($row = $result->fetchAll(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
        
            $totalCount = $this->conn->query("SELECT COUNT(*) as total FROM ($query) as count")->fetch(PDO::FETCH_ASSOC)['total'];
        
            
            $totalPages = ceil($totalCount / $itemsPerPage);
        
            return [
                'data' => $data,
                'currentPage' => $currentPage,
                'totalPages' => $totalPages,
                'itemsPerPage' => $itemsPerPage,
                'totalCount' => $totalCount,
            ];
        }
     
    
        // public function generatePaginationLinks($currentPage, $totalPages) {
        //     $queryParameters = $_GET; // Get existing query parameters
    
        //     echo '<ul class="pagination d-flex justify-content-start pagination-xsm m-0 font-semibold">';
            
        //     // Previous button
        //     echo '<li class="page-item ' . ($currentPage == 1 ? 'disabled' : '') . '">';
        //     $queryParameters['page'] = ($currentPage - 1);
        //     echo '<a class="page-link" href="' . ($_SERVER['PHP_SELF'] . '?' . http_build_query($queryParameters)) . '" aria-label="Previous">';
        //     echo '<span aria-hidden="true" class="material-icons">chevron_left</span>';
        //     echo '<span>Prev</span>';
        //     echo '</a>';
        //     echo '</li>';
            
        //     // Page links
        //     $startPage = max(1, $currentPage - 5); // Start showing pages from 2 pages before the current page
        //     $endPage = min($totalPages, $currentPage + 5); // End showing pages 2 pages after the current page
        //     if ($startPage > 1) {
        //         echo '<li class="page-item">';
        //         echo '<a class="page-link" href="#">...</a>'; // Ellipsis to indicate hidden pages
        //         echo '</li>';
        //     }
        //     for ($i = $startPage; $i <= $endPage; $i++) {
        //         echo '<li class="page-item ' . ($currentPage == $i ? 'active' : '') . '">';
        //         $queryParameters['page'] = $i;
        //         echo '<a class="page-link" href="' . ($_SERVER['PHP_SELF'] . '?' . http_build_query($queryParameters)) . '" aria-label="Page ' . $i . '">';
        //         echo '<span>' . $i . '</span>';
        //         echo '</a>';
        //         echo '</li>';
        //     }
        //     if ($endPage < $totalPages) {
        //         echo '<li class="page-item">';
        //         echo '<a class="page-link" href="#">...</a>'; // Ellipsis to indicate hidden pages
        //         echo '</li>';
        //     }
            
        //     // Next button
        //     echo '<li class="page-item ' . ($currentPage == $totalPages ? 'disabled' : '') . '">';
        //     $queryParameters['page'] = ($currentPage + 1);
        //     echo '<a class="page-link" href="' . ($_SERVER['PHP_SELF'] . '?' . http_build_query($queryParameters)) . '" aria-label="Next">';
        //     echo '<span>Next</span>';
        //     echo '<span aria-hidden="true" class="material-icons">chevron_right</span>';
        //     echo '</a>';
        //     echo '</li>';
            
        //     echo '</ul>';
    
        // }
        
        public function generatePaginationLinks($currentPage, $totalPages, $pageIdentifier = 'page') {
            $queryParameters = $_GET; // Get existing query parameters
        
            echo '<ul class="pagination d-flex justify-content-start pagination-xsm m-0 font-semibold">';
        
            // Previous button
            echo '<li class="page-item ' . ($currentPage == 1 ? 'disabled' : '') . '">';
            $queryParameters[$pageIdentifier] = ($currentPage - 1);
            echo '<a class="page-link" href="' . ($_SERVER['PHP_SELF'] . '?' . http_build_query($queryParameters)) . '" aria-label="Previous">';
            echo '<span aria-hidden="true" class="material-icons">chevron_left</span>';
            echo '<span>Prev</span>';
            echo '</a>';
            echo '</li>';
        
            // Page links
            $startPage = max(1, $currentPage - 5);
            $endPage = min($totalPages, $currentPage + 5);
            if ($startPage > 1) {
                echo '<li class="page-item"><a class="page-link" href="#">...</a></li>';
            }
            for ($i = $startPage; $i <= $endPage; $i++) {
                echo '<li class="page-item ' . ($currentPage == $i ? 'active' : '') . '">';
                $queryParameters[$pageIdentifier] = $i;
                echo '<a class="page-link" href="' . ($_SERVER['PHP_SELF'] . '?' . http_build_query($queryParameters)) . '" aria-label="Page ' . $i . '">';
                echo '<span>' . $i . '</span>';
                echo '</a>';
                echo '</li>';
            }
            if ($endPage < $totalPages) {
                echo '<li class="page-item"><a class="page-link" href="#">...</a></li>';
            }
        
            // Next button
            echo '<li class="page-item ' . ($currentPage == $totalPages ? 'disabled' : '') . '">';
            $queryParameters[$pageIdentifier] = ($currentPage + 1);
            echo '<a class="page-link" href="' . ($_SERVER['PHP_SELF'] . '?' . http_build_query($queryParameters)) . '" aria-label="Next">';
            echo '<span>Next</span>';
            echo '<span aria-hidden="true" class="material-icons">chevron_right</span>';
            echo '</a>';
            echo '</li>';
        
            echo '</ul>';
        }
    
    
        
        public function getByID($table, $id){
            $query = "SELECT * FROM $table WHERE md5(id) = '$id'";
        
            $result = $this->conn->query($query);
        
            return $result->fetch(PDO::FETCH_ASSOC);
        }
    
    
        // Update data in the database
       public function updateData($table, $data, $conditions) {
            $data['updated_at'] = date('Y-m-d H:i:s');
            $set = array();
        
            foreach ($data as $key => $value) {
                $set[] = "$key = '$value'";
            }
        
            $where = array();
        
            foreach ($conditions as $key => $value) {
                $where[] = "$key = '$value'";
            }
        
            $query = "UPDATE $table SET " . implode(", ", $set) . " WHERE " . implode(" AND ", $where);
        
            if ($this->conn->query($query)) {
                return true;
            } else {
                return false;
            }
        }
        public function updateWithSQL($query) {
        
            if ($this->conn->query($query)) {
                return true;
            } else {
                return false;
            }
        }
    
    
        // Delete data from the database
        public function deleteData($table, $conditions) {
            
            $where = array();
            
            foreach ($conditions as $key => $value) {
                $where[] = "$key = '$value'";
            }
        
        
            $query = "DELETE FROM $table WHERE ".  implode(" AND ", $where);
            
            if ($this->conn->query($query)) {
                return true;
            } else {
                return false;
            }
        }
    
        
        public function authenticateUser($email, $password){
            $email = isset($_POST['email']) ? $_POST['email'] : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            
            $stmt = "SELECT * FROM users WHERE email = '$email'";
            $user = $this->conn->query($stmt);
            $user = $user->fetch();
            
            // return password_verify($password, $user['password']);
        
            if ($user && password_verify($password, $user['password'])) {
                return true;
            } else {
                return false;
            }
        } 
        
        
        public function isLoggedIn() {
            return isset($_SESSION['user_id']);
        }
        
        public function logout() {
            session_unset();
            session_destroy();
            header("Location: ../auth/login");
            exit();
        }
        
        public function getInitials($firstName, $lastName) {
            $firstInitial = substr($firstName, 0, 1);
            $lastInitial = substr($lastName, 0, 1);
            $initials = $firstInitial . $lastInitial;
            return $initials;
        }
        
        public function getTimeAgo($dateString) {
            if(empty($dateString)){
                return "----";
            }
            $date = new DateTime($dateString);
            $currentDate = new DateTime();
            $interval = $currentDate->diff($date);
        
            if ($interval->days > 0 && $interval->days < 2) {
                return $interval->days . " day ago";
            } elseif($interval->days > 0 && $interval->days > 1){
                 return $interval->days . " days ago";
            } elseif ($interval->h > 0) {
                return $interval->h . " hours ago";
            } elseif ($interval->i > 0) {
                return $interval->i . " minutes ago";
            } else {
                return $interval->s . " seconds ago";
            }
        }
        function calculatePercentage($partial, $total) {
            if ($total == 0) {
                return 0;
            }
        
            $percentage = ($partial / $total) * 100;
            $formattedPercentage = round($percentage);
            return $formattedPercentage;
        }
        function scorePercentage($partial, $total) {
            if ($total == 0) {
                return 0;
            }
        
            $percentage = ($partial / $total) * 100;
            // $formattedPercentage = round($percentage);
            return $percentage;
        }
        function calculateAverage($arr) {
            if (empty($arr)) {
                return 0; 
            }
        
            $sum = array_sum($arr);
            $average = $sum / count($arr);
        
            return $average;
        }
        public function averageRating($courseID){
            $data = $this->fetchWithSql("SELECT SUM(star) as allStar, COUNT(*) AS total FROM course_reviews WHERE course_id = $courseID");
            $allStar = $data[0]['allStar'];
            $total = $data[0]['total'];
            $averageReview = ($total == 0) ? 0 : round($allStar / $total, 1);
            return (floor($averageReview));
        }
    
        function removeSymbols($input) {
            $filteredText = preg_replace('/[^\pL\pN\s]/u', '', $input);
            $filteredText = preg_replace('/\s+/', ' ', $filteredText);
            $filteredText = trim($filteredText);
        
            return $filteredText;
        }
        
        function filterText($input) {
            // Allow only letters and whitespace
            $filteredText = preg_replace('/[^\pL\s]/u', '', $input);
            $filteredText = preg_replace('/\s+/', '', $filteredText);
            $filteredText = trim($filteredText);
        
            return $filteredText;
        }
    
    
    
        function generateRandomNumber($count) {
            $randomNumberString = '';
        
            for ($i = 0; $i < $count; $i++) {
                $randomNumberString .= mt_rand(0, 9);
            }
        
            return (int)$randomNumberString;
        }
        
        function generateRandomLetters($count) {
            $letters = '';
            for ($i = 0; $i < $count; $i++) {
                $randomAscii = rand(69, 90); // ASCII codes for uppercase letters
                $letters .= chr($randomAscii);
            }
            return $letters;
        }
        
        public function truncateSentence($sentence, $length, $append = '...') {
            if (strlen($sentence) <= $length) {
                return $sentence; // If the sentence is already shorter than the specified length, return it as is.
            } else {
                $truncated = substr($sentence, 0, $length); // Truncate the sentence to the desired length.
                $lastSpace = strrpos($truncated, ' '); // Find the last space in the truncated string.
                
                if ($lastSpace !== false) {
                    $truncated = substr($truncated, 0, $lastSpace); // Trim the string at the last space.
                }
        
                return $truncated . $append; // Append the specified characters (e.g., '...').
            }
        }
    
    
    
    
        
        public function updateOrInsertRecord($user_id, $course_id, $db_column_name, $column_value, $group = "")
        {
            
            $course = $this->singleFetchData('courses', ['id' => $course_id ]);
            $user = $this->singleFetchData('users', ['id' => $_SESSION['user_id'] ]);
            try {
                $stmt = $this->conn->prepare("SELECT * FROM reports WHERE user_id = :user_id AND course_id = :course_id");
                $stmt->bindParam(':user_id', $user_id);
                $stmt->bindParam(':course_id', $course_id);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
                if (!$result) {
                   
                    $stmt = $this->conn->prepare( "INSERT INTO reports (user_id, course_id, group_id) VALUES (:user_id, :course_id, :group_id)" );
                    $stmt->bindParam(':user_id', $user_id);
                    $stmt->bindParam(':course_id', $course_id);
                    $stmt->bindParam(':group_id', $group);
                    $stmt->execute();
                    
                    $createActivity = $this->insertData('activities', [
                        'company_id'    => $user['company_id'],
                        'branch_id'     => $user['branch_id'],
                        'user_id'       => $user['id'],
                        'action'        => $user['fullname']." Started ". $course['title'] .' Course',
                        'audit_type'    => $user == 4 ? 'learner' : 'Admin'
                    ]);
                    
                } else {
                    $currentDate = date('Y-m-d H:i:s');
                    if ($db_column_name === 'end') {
                        $stmt = $this->conn->prepare("UPDATE reports SET is_complete = true, progress_measure = 100, completed_at = :completed_at  WHERE user_id = :user_id AND course_id = :course_id");
                        $stmt->bindParam(':user_id', $user_id);
                        $stmt->bindParam(':course_id', $course_id);
                        $stmt->bindParam(':completed_at', $currentDate);
                        $stmt->execute();
                        
                        $createActivity = $this->insertData('activities', [
                            'company_id'    => $user['company_id'],
                            'branch_id'     => $user['branch_id'],
                            'user_id'       => $user['id'],
                            'action'        => $user['fullname']." Complete ". $course['title'] .' Course',
                            'audit_type'    => $user == 4 ? 'learner' : 'Admin'
                        ]);
                    }elseif($db_column_name !='start'){
    
                        if ($db_column_name === 'session_time') {
                            $time = preg_replace('/[a-zA-Z]/', '', $column_value);
                            $column_value = floatval($time);
                        }
    
                        $stmt = $this->conn->prepare("UPDATE reports SET $db_column_name = :column_value WHERE user_id = :user_id AND course_id = :course_id");
                        $stmt->bindParam(':column_value', $column_value);
                        $stmt->bindParam(':user_id', $user_id);
                        $stmt->bindParam(':course_id', $course_id);
                        $stmt->execute();
                        
                        $createActivity = $this->insertData('activities', [
                            'company_id'    => $user['company_id'],
                            'branch_id'     => $user['branch_id'],
                            'user_id'       => $user['id'],
                            'action'        => $user['fullname']." Progressed on ". $course['title'] .' Course',
                            'audit_type'    => $user == 4 ? 'learner' : 'Admin'
                        ]);
                    }
                }
    
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
                return false; // Failure
            }
        }
        
         public function insertCourse($title, $details, $type, $imagePath, $filePath, $hours, $category, $lesson, $level, $status, $tag, $story,$mins)
        {
            try {
                $stmt = $this->conn->prepare(
                    "INSERT INTO courses (title, details, image, ratings, hours, mins, lessons, author, level, type, link, status, category, tag, story_link)
            VALUES (:title, :details, :image, :ratings, :hours, :mins, :lessons, :author, :level, :type, :link, :status, :category, :tag, :story_link)"
                );
    
                $stmt->execute([
                    ':title' => $title,
                    ':details' => $details,
                    ':type' => $type,
                    ':link' => $filePath,
                    ':image' => $imagePath,
                    ':ratings' => 5,
                    ':hours' => $hours,
                    ':mins' => $mins,
                    ':lessons' => $lesson,
                    ':author' => 'Super Admin',
                    ':level' => $level,
                    ':category' => $category,
                    ':tag' => $tag,
                    ':status' => $status,
                    ':story_link' => $story
                ]);
    
    
                // Check for successful insertion
                if ($stmt->rowCount() > 0) {
                    return true; // Success
                } else {
                    return false; // No rows affected, something might have gone wrong
                }
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
                return false; // Failure
            }
        }
        
        
        
    }


?>
