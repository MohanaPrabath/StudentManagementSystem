<?php 
// Includes the header, which handles authentication and the top part of the HTML page.
include 'header.php'; 
?>
<?php // Main heading for the dashboard. ?>
<h1 class="mb-4">Dashboard</h1>
<?php // Includes the Chart.js library for creating charts. ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php // --- COURSE FILTER FORM --- ?>
<?php // This form allows the user to filter the dashboard data by a specific course. ?>
<form action="dashboard.php" method="get" class="row g-3 align-items-center mb-4">
    <div class="col-auto">
        <label for="course_id" class="form-label">Filter by Course</label>
        <?php // The 'onchange' attribute submits the form automatically whenever the user selects a new course. ?>
        <select name="course_id" id="course_id" class="form-select" onchange="this.form.submit()">
            <option value="">All Courses</option>
            <?php
            // Ensures the database connection is available.
            if (empty($conn)) include 'db.php';
            // Fetches all courses to populate the dropdown.
            $course_query = "SELECT * FROM courses";
            $course_result = $conn->query($course_query);
            // Loops through each course to create an option.
            while($course_row = $course_result->fetch_assoc()){
                // Keeps the current filter selected after the page reloads.
                $selected = isset($_GET['course_id']) && $_GET['course_id'] == $course_row['course_id'] ? 'selected' : '';
                echo "<option value='{$course_row['course_id']}' $selected>{$course_row['course_name']}</option>";
            }
            ?>
        </select>
    </div>
</form>

<?php // Grid layout for the dashboard cards. ?>
    <div class="row">
        <?php // Card for the attendance pie chart. ?>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Attendance Percentage (Today)
                </div>
                <div class="card-body">
                    <?php // The canvas element where the chart will be drawn. ?>
                    <canvas id="attendanceChart"></canvas>
                </div>
            </div>
        </div>
        <?php // Card for the summary statistics. ?>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Summary
                </div>
                <div class="card-body">
                    <?php
                    // --- DATA CALCULATION FOR SUMMARY ---
                    // Checks if a course is selected and creates a SQL WHERE clause for it.
                    $course_id_filter_plain = isset($_GET['course_id']) && !empty($_GET['course_id']) ? "WHERE course_id = " . (int)$_GET['course_id'] : "";

                    // Gets today's date.
                    $today = date('Y-m-d');
                    
                    // Query to count the total number of students, applying the course filter if one is selected.
                    $total_students_query = "SELECT COUNT(*) as total FROM students $course_id_filter_plain";
                    $total_students_result = $conn->query($total_students_query);
                    $total_students = $total_students_result->fetch_assoc()['total'];

                    // Query to count the number of students marked 'Present' today, filtered by course if selected.
                    $present_today_query = "SELECT COUNT(DISTINCT a.student_id) as present 
                                            FROM attendance a 
                                            JOIN students s ON a.student_id = s.student_id 
                                            WHERE a.date = '$today' AND a.status = 'Present' " . 
                                            (isset($_GET['course_id']) && !empty($_GET['course_id']) ? "AND s.course_id = " . (int)$_GET['course_id'] : "");
                    
                    $present_today_result = $conn->query($present_today_query);
                    $present_today = $present_today_result->fetch_assoc()['present'];
                    
                    // Calculates the number of absent students.
                    $absent_today = $total_students > 0 ? $total_students - $present_today : 0;
                    ?>
                    <?php // Displays the summary data. ?>
                    <p>Total Students: <?php echo $total_students; ?></p>
                    <p>Present Today: <?php echo $present_today; ?></p>
                    <p>Absent Today: <?php echo $absent_today; ?></p>
                </div>
            </div>
        </div>
    </div>
<?php // --- CHART.JS SCRIPT --- ?>
<script>
<?php // Waits for the HTML document to be fully loaded before running the script. ?>
document.addEventListener("DOMContentLoaded", function() {
    <?php // Gets the canvas element where the chart will be drawn. ?>
    var ctx = document.getElementById('attendanceChart').getContext('2d');
    <?php // Creates a new pie chart instance. ?>
    var myChart = new Chart(ctx, {
        type: 'pie', <?php // Specifies the chart type. ?>
        data: {
            labels: ['Present', 'Absent'], <?php // Labels for the chart sections. ?>
            datasets: [{
                label: 'Attendance', <?php // Label for the dataset. ?>
                <?php // The actual data for the chart, taken from the PHP variables calculated above. ?>
                data: [<?php echo $present_today; ?>, <?php echo $absent_today; ?>],
                <?php // Colors for the 'Present' and 'Absent' sections. ?>
                backgroundColor: [
                    'rgba(75, 192, 192, 0.5)',
                    'rgba(255, 99, 132, 0.5)'
                ],
                <?php // Border colors for the sections. ?>
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 99, 132, 1)'
                ],
                borderWidth: 1 <?php // Width of the border. ?>
            }]
        }
    });
});
</script>
<?php 
// Includes the footer file.
include 'footer.php'; 
?>
