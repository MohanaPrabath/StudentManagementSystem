<?php 
// Includes the header file, which handles authentication and the page's head section.
include 'header.php'; 
?>
<?php // Main heading for the reports page. ?>
<h1 class="mb-4">Attendance Reports</h1>
    <?php // Card container for the report generation form. ?>
    <div class="card">
        <?php // Card header for the form. ?>
        <div class="card-header">
            <?php // --- REPORT FILTER FORM --- ?>
            <?php // This form allows users to select the type of report and filter by date and course. ?>
            <form action="reports.php" method="get" class="row g-3 align-items-center">
                <?php // Dropdown to select the report type (Daily or Monthly). ?>
                <div class="col-auto">
                    <label for="report_type" class="form-label">Report Type</label>
                    <select name="report_type" id="report_type" class="form-select">
                        <option value="daily" <?php echo (isset($_GET['report_type']) && $_GET['report_type'] == 'daily') ? 'selected' : ''; ?>>Daily</option>
                        <option value="monthly" <?php echo (isset($_GET['report_type']) && $_GET['report_type'] == 'monthly') ? 'selected' : ''; ?>>Monthly</option>
                    </select>
                </div>
                <?php // Input to select the month and year for the report. ?>
                <div class="col-auto">
                    <label for="month" class="form-label">Select Month</label>
                    <input type="month" name="month" id="month" class="form-control" value="<?php echo isset($_GET['month']) ? $_GET['month'] : date('Y-m'); ?>">
                </div>
                <?php // Dropdown to filter the report by a specific course. ?>
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
                <?php // Button to submit the form and generate the report. ?>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary mt-4">Generate Report</button>
                </div>
            </form>
        </div>
        <?php // Card body where the generated report will be displayed. ?>
        <div class="card-body">
            <?php
            // --- REPORT GENERATION LOGIC ---
            // Checks if the necessary form fields are submitted.
            if(isset($_GET['report_type']) && isset($_GET['month'])){
                // Gets the selected report type and course ID.
                $report_type = $_GET['report_type'];
                $course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
                // Creates a SQL condition for the course filter.
                $course_condition = $course_id > 0 ? " AND s.course_id = $course_id" : "";

                // --- DAILY REPORT LOGIC ---
                if($report_type == 'daily'){
                    // Gets the first day of the selected month to use as the date for the daily report.
                    $date = date('Y-m-d', strtotime($_GET['month']));
                    echo "<h4>Daily Report for " . date('F d, Y', strtotime($date)) . "</h4>";
                    
                    // SQL query to fetch daily attendance records.
                    $query = "SELECT s.name, c.course_name, b.batch_name, a.status, a.time_marked
                              FROM attendance a
                              JOIN students s ON a.student_id = s.student_id
                              JOIN courses c ON s.course_id = c.course_id
                              JOIN batches b ON s.batch_id = b.batch_id
                              WHERE a.date = '$date' $course_condition";
                    
                    $result = $conn->query($query);
                    // Checks if any records were found.
                    if ($result->num_rows > 0) {
                        echo "<table class='table table-bordered table-hover'><thead><tr><th>Student</th><th>Course</th><th>Batch</th><th>Status</th><th>Time</th></tr></thead><tbody>";
                        // Loops through each record and displays it in the table.
                        while($row = $result->fetch_assoc()){
                            echo "<tr><td>{$row['name']}</td><td>{$row['course_name']}</td><td>{$row['batch_name']}</td><td>{$row['status']}</td><td>" . date('h:i A', strtotime($row['time_marked'])) . "</td></tr>";
                        }
                        echo "</tbody></table>";
                    } else {
                        // Displays a message if no records are found.
                        echo "<div class='alert alert-info'>No attendance records found for the selected criteria.</div>";
                    }

                // --- MONTHLY REPORT LOGIC ---
                } elseif ($report_type == 'monthly'){
                    // Gets the month and year from the form input.
                    $month = date('m', strtotime($_GET['month']));
                    $year = date('Y', strtotime($_GET['month']));
                    echo "<h4>Monthly Report for " . date('F Y', strtotime($_GET['month'])) . "</h4>";
                    
                    // SQL query to calculate monthly attendance summary for each student.
                    $query = "SELECT s.name, s.email, c.course_name, b.batch_name, 
                                     COUNT(CASE WHEN a.status = 'Present' THEN 1 END) as present_days,
                                     COUNT(CASE WHEN a.status = 'Absent' THEN 1 END) as absent_days,
                                     (COUNT(CASE WHEN a.status = 'Present' THEN 1 END) / COUNT(a.id)) * 100 as attendance_percentage
                              FROM students s
                              LEFT JOIN attendance a ON s.student_id = a.student_id AND MONTH(a.date) = '$month' AND YEAR(a.date) = '$year'
                              JOIN courses c ON s.course_id = c.course_id
                              JOIN batches b ON s.batch_id = b.batch_id
                              WHERE (s.course_id = $course_id OR $course_id = 0)
                              GROUP BY s.student_id, c.course_name, b.batch_name";
                    
                    $result = $conn->query($query);
                    // Checks if any records were found.
                    if ($result->num_rows > 0) {
                        echo "<table class='table table-bordered table-hover'><thead><tr><th>Student</th><th>Course</th><th>Batch</th><th>Present</th><th>Absent</th><th>Percentage</th></tr></thead><tbody>";
                        // Loops through each student's summary and displays it.
                        while($row = $result->fetch_assoc()){
                            $percentage = $row['attendance_percentage'] ? round($row['attendance_percentage'], 2) . "%" : "N/A";
                            echo "<tr><td>{$row['name']}</td><td>{$row['course_name']}</td><td>{$row['batch_name']}</td><td>{$row['present_days']}</td><td>{$row['absent_days']}</td><td>{$percentage}</td></tr>";
                        }
                        echo "</tbody></table>";
                    } else {
                        // Displays a message if no records are found.
                        echo "<div class='alert alert-info'>No attendance records found for the selected criteria.</div>";
                    }
                }
            } else {
                // Default message shown before a report is generated.
                echo "<div class='alert alert-secondary'>Please select a report type and date to generate a report.</div>";
            }
            ?>
        </div>
    </div>
<?php 
// Includes the footer file.
include 'footer.php'; 
?>
