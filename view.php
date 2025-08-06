<?php 
// Includes the header file, which contains the authentication check, HTML head, and navigation bar.
include 'header.php'; 
?>
<?php // Main heading for the page. ?>
<h1 class="mb-4">View Attendance Records</h1>
    <?php // Card container for the filter form and attendance table. ?>
    <div class="card">
        <?php // Card header containing the filter form. ?>
        <div class="card-header">
            <?php // --- FILTER FORM --- ?>
            <?php // This form allows filtering the attendance records by date, course, and batch. ?>
            <form action="view.php" method="get" class="row g-3 align-items-center">
                <?php // Date selection input. ?>
                <div class="col-auto">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" name="date" id="date" class="form-control" value="<?php echo isset($_GET['date']) ? $_GET['date'] : date('Y-m-d'); ?>">
                </div>
                <?php // Course selection dropdown. ?>
                <div class="col-auto">
                    <label for="course_id" class="form-label">Course</label>
                    <select name="course_id" id="course_id" class="form-select">
                        <option value="">All Courses</option>
                        <?php
                        // Ensures the database connection is available.
                        if (empty($conn)) include 'db.php';
                        // Fetches all courses to populate the dropdown.
                        $course_query = "SELECT * FROM courses";
                        $course_result = $conn->query($course_query);
                        // Loops through each course to create an option.
                        while($course_row = $course_result->fetch_assoc()){
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
                        // Fetches all batches to populate the dropdown.
                        $batch_query = "SELECT * FROM batches";
                        $batch_result = $conn->query($batch_query);
                        // Loops through each batch to create an option.
                        while($batch_row = $batch_result->fetch_assoc()){
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
        </div>
        <?php // Card body containing the attendance records table. ?>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Time Marked</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // --- DATA FETCHING LOGIC ---
                    // Gets the filter values from the URL, with default values if not set.
                    $date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
                    $course_id = isset($_GET['course_id']) ? $_GET['course_id'] : '';
                    $batch_id = isset($_GET['batch_id']) ? $_GET['batch_id'] : '';

                    // Base query to select attendance records, joining with the students table to get names.
                    $query = "SELECT attendance.*, students.name FROM attendance 
                              JOIN students ON attendance.student_id = students.student_id";
                    
                    // Array to hold the filter conditions.
                    $conditions = [];
                    // Adds a condition to filter by date.
                    $conditions[] = "attendance.date = '$date'";
                    // If a course is selected, add a condition for it.
                    if(!empty($course_id)){
                        $conditions[] = "students.course_id = '$course_id'";
                    }
                    // If a batch is selected, add a condition for it.
                    if(!empty($batch_id)){
                        $conditions[] = "students.batch_id = '$batch_id'";
                    }
                    // If any conditions exist, append them to the query.
                    if(count($conditions) > 0){
                        $query .= " WHERE " . implode(' AND ', $conditions);
                    }

                    // Executes the final query.
                    $result = $conn->query($query);
                    // Loops through each result to display it in a table row.
                    while($row = $result->fetch_assoc()){
                    ?>
                    <tr>
                        <?php // Displays the student's name. ?>
                        <td><?php echo $row['name']; ?></td>
                        <?php // Displays the attendance status. ?>
                        <td><?php echo $row['status']; ?></td>
                        <?php // Displays the date of the attendance record. ?>
                        <td><?php echo $row['date']; ?></td>
                        <?php // Displays the time the attendance was marked, formatted to show hours and minutes AM/PM. ?>
                        <td><?php echo date('h:i A', strtotime($row['time_marked'])); ?></td>
                        <?php // Action buttons for editing and deleting the record. ?>
                        <td>
                            <a href="update.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
<?php 
// Includes the footer file.
include 'footer.php'; 
?>
