<?php 
// Includes the authentication check file to ensure the user is logged in.
include 'header.php'; 
?>
<?php // Displays the main heading for the page. ?>
<h1 class="mb-4">Mark Attendance</h1>
    <?php // The main card container for the attendance form. ?>
    <div class="card">
        <?php // Card header section. ?>
        <div class="card-header">
            <?php // Displays the title with the current date. ?>
            <h3 class="panel-title">Mark Attendance for <?php echo date("Y-m-d"); ?></h3>
        </div>
        <?php // Card body containing the filter and attendance forms. ?>
        <div class="card-body">
            <?php // --- FILTER FORM --- ?>
            <?php // This form allows users to filter students by course and batch. ?>
            <form action="index.php" method="get" class="row g-3 align-items-center mb-4">
                <?php // Course selection dropdown. ?>
                <div class="col-auto">
                    <label for="course_id" class="form-label">Course</label>
                    <select name="course_id" id="course_id" class="form-select">
                        <option value="">All Courses</option>
                        <?php
                        // Ensures the database connection is available.
                        if (empty($conn)) include 'db.php';
                        // Fetches all courses from the database.
                        $course_query = "SELECT * FROM courses";
                        $course_result = $conn->query($course_query);
                        // Loops through each course and creates an option element.
                        while($course_row = $course_result->fetch_assoc()){
                            // Checks if this course was the one previously selected to keep it selected.
                            $selected = isset($_GET['course_id']) && $_GET['course_id'] == $course_row['course_id'] ? 'selected' : '';
                            echo "<option value='{$course_row['course_id']}' $selected>{$course_row['course_name']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <?php // Batch selection dropdown. ?>
                <div class="col-auto">
                    <label for="batch_id" class="form-label">Batch</label>
                    <select name="batch_id" id="batch_id" class="form-select">
                        <option value="">All Batches</option>
                        <?php
                        // Fetches all batches from the database.
                        $batch_query = "SELECT * FROM batches";
                        $batch_result = $conn->query($batch_query);
                        // Loops through each batch and creates an option element.
                        while($batch_row = $batch_result->fetch_assoc()){
                            // Checks if this batch was the one previously selected.
                            $selected = isset($_GET['batch_id']) && $_GET['batch_id'] == $batch_row['batch_id'] ? 'selected' : '';
                            echo "<option value='{$batch_row['batch_id']}' $selected>{$batch_row['batch_name']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <?php // Filter button. ?>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary mt-4">Filter</button>
                </div>
            </form>
            <?php // --- ATTENDANCE FORM --- ?>
            <?php // This form submits the attendance data. ?>
            <form action="index.php" method="post">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Gets the selected course and batch IDs from the URL.
                        $course_id = isset($_GET['course_id']) ? $_GET['course_id'] : '';
                        $batch_id = isset($_GET['batch_id']) ? $_GET['batch_id'] : '';

                        // Base query to select students.
                        $query = "SELECT * FROM students";
                        $conditions = [];
                        // If a course is selected, add a condition to the query.
                        if(!empty($course_id)){
                            $conditions[] = "course_id = '$course_id'";
                        }
                        // If a batch is selected, add a condition to the query.
                        if(!empty($batch_id)){
                            $conditions[] = "batch_id = '$batch_id'";
                        }
                        // If there are any conditions, append them to the query using WHERE.
                        if(count($conditions) > 0){
                            $query .= " WHERE " . implode(' AND ', $conditions);
                        }
                        
                        // Executes the student query.
                        $result = $conn->query($query);
                        // Loops through each student to display them in the table.
                        while($row = $result->fetch_assoc()){
                        ?>
                        <tr>
                            <?php // Displays the student's name. ?>
                            <td><?php echo $row['name']; ?></td>
                            <?php // Radio buttons for marking attendance. ?>
                            <td>
                                <input type="radio" name="attendance_status[<?php echo $row['student_id']; ?>]" value="Present" required> Present
                                <input type="radio" name="attendance_status[<?php echo $row['student_id']; ?>]" value="Absent"> Absent
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php // Submit button to save the attendance. ?>
                <input type="submit" name="submit" value="Save Attendance" class="btn btn-primary">
            </form>
        </div>
    </div>

    <?php
    // --- FORM SUBMISSION LOGIC ---
    // Checks if the 'Save Attendance' button was clicked.
    if(isset($_POST['submit'])){
        // Gets the current date and time.
        $date = date("Y-m-d");
        $datetime = date("Y-m-d H:i:s");
        // Loops through each submitted attendance status.
        foreach($_POST['attendance_status'] as $student_id => $status){
            // Checks if an attendance record for this student on this date already exists.
            $query = "SELECT * FROM attendance WHERE student_id = '$student_id' AND date = '$date'";
            $result = $conn->query($query);
            // If a record exists, update it.
            if($result->num_rows > 0){
                $query = "UPDATE attendance SET status = '$status', time_marked = '$datetime' WHERE student_id = '$student_id' AND date = '$date'";
            } else {
                // If no record exists, insert a new one.
                $query = "INSERT INTO attendance (student_id, date, status, time_marked) VALUES ('$student_id', '$date', '$status', '$datetime')";
            }
            // Executes the insert or update query.
            $conn->query($query);
        }
        // Displays a success message.
        echo "<div class='alert alert-success mt-4'>Attendance saved successfully.</div>";
    }
    ?>

<?php 
// Includes the footer file to close the HTML structure.
include 'footer.php'; 
?>
