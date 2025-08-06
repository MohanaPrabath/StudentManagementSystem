<?php 
// Includes the header file, which handles authentication and page layout.
include 'header.php'; 
?>
<?php // Main heading for the page. ?>
<h1 class="mb-4">Add New Student</h1>
    <?php
    // --- FORM SUBMISSION LOGIC ---
    // Includes the database connection file.
    include 'db.php';
    // Checks if the 'add' button on the form was clicked.
    if(isset($_POST['add'])){
        // Retrieves the student's name and email from the submitted form data.
        $name = $_POST['name'];
        $email = $_POST['email'];
        // Retrieves the selected course and batch IDs.
        $course_id = $_POST['course_id'];
        $batch_id = $_POST['batch_id'];
        // SQL query to insert the new student's details into the 'students' table.
        $query = "INSERT INTO students (name, email, course_id, batch_id) VALUES ('$name', '$email', '$course_id', '$batch_id')";
        // Executes the query.
        if($conn->query($query) === TRUE){
            // If the query is successful, redirect the user back to the main student management page.
            header("Location: students.php");
        } else {
            // If there's an error, display it.
            echo "<div class='alert alert-danger'>Error adding student: " . $conn->error . "</div>";
        }
    }
    ?>
    <?php // --- HTML FORM --- ?>
    <?php // This form is used to collect the new student's information. ?>
    <form action="add_student.php" method="post">
        <?php // Input field for the student's name. ?>
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <?php // Input field for the student's email. ?>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <?php // Dropdown to select the student's course. ?>
        <div class="form-group">
            <label for="course">Course:</label>
            <select name="course_id" class="form-control" required>
                <option value="">Select Course</option>
                <?php
                // Fetches all courses from the database to populate the dropdown.
                $course_query = "SELECT * FROM courses";
                $course_result = $conn->query($course_query);
                while($course_row = $course_result->fetch_assoc()){
                    echo "<option value='{$course_row['course_id']}'>{$course_row['course_name']}</option>";
                }
                ?>
            </select>
        </div>
        <?php // Dropdown to select the student's batch. ?>
        <div class="form-group">
            <label for="batch">Batch:</label>
            <select name="batch_id" class="form-control" required>
                <option value="">Select Batch</option>
                <?php
                // Fetches all batches from the database to populate the dropdown.
                $batch_query = "SELECT * FROM batches";
                $batch_result = $conn->query($batch_query);
                while($batch_row = $batch_result->fetch_assoc()){
                    echo "<option value='{$batch_row['batch_id']}'>{$batch_row['batch_name']}</option>";
                }
                ?>
            </select>
        </div>
        <?php // Submit button to add the student. ?>
        <button type="submit" name="add" class="btn btn-primary">Add Student</button>
        <?php // Cancel button to return to the student management page. ?>
        <a href="students.php" class="btn btn-secondary">Cancel</a>
    </form>
<?php 
// Includes the footer file.
include 'footer.php'; 
?>
