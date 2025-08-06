<?php 
// Includes the header file for authentication and page layout.
include 'header.php'; 
?>
<?php // Main heading for the page. ?>
<h1 class="mb-4">Edit Student</h1>
    <?php
    // --- DATA FETCHING AND FORM SUBMISSION LOGIC ---
    // Includes the database connection.
    include 'db.php';
    // Gets the student's ID from the URL query parameter.
    $id = $_GET['id'];
    // SQL query to select the details of the specific student to be edited.
    $query = "SELECT * FROM students WHERE student_id = '$id'";
    $result = $conn->query($query);
    // Fetches the student's data into an associative array.
    $row = $result->fetch_assoc();

    // Checks if the 'update' button on the form was clicked.
    if(isset($_POST['update'])){
        // Retrieves the updated information from the form.
        $name = $_POST['name'];
        $email = $_POST['email'];
        $course_id = $_POST['course_id'];
        $batch_id = $_POST['batch_id'];
        // SQL query to update the student's record in the database.
        $query = "UPDATE students SET name = '$name', email = '$email', course_id = '$course_id', batch_id = '$batch_id' WHERE student_id = '$id'";
        // Executes the query.
        if($conn->query($query) === TRUE){
            // If successful, redirect back to the main student management page.
            header("Location: students.php");
        } else {
            // If there's an error, display it.
            echo "<div class='alert alert-danger'>Error updating record: " . $conn->error . "</div>";
        }
    }
    ?>
    <?php // --- HTML FORM --- ?>
    <?php // This form displays the student's current information and allows editing. ?>
    <form action="edit_student.php?id=<?php echo $id; ?>" method="post">
        <?php // Input field for the student's name, pre-filled with current data. ?>
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" name="name" class="form-control" value="<?php echo $row['name']; ?>" required>
        </div>
        <?php // Input field for the student's email, pre-filled with current data. ?>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" class="form-control" value="<?php echo $row['email']; ?>" required>
        </div>
        <?php // Dropdown to select the student's course. ?>
        <div class="form-group">
            <label for="course">Course:</label>
            <select name="course_id" class="form-control" required>
                <option value="">Select Course</option>
                <?php
                // Fetches all courses to populate the dropdown.
                $course_query = "SELECT * FROM courses";
                $course_result = $conn->query($course_query);
                while($course_row = $course_result->fetch_assoc()){
                    // Pre-selects the student's current course.
                    $selected = $row['course_id'] == $course_row['course_id'] ? 'selected' : '';
                    echo "<option value='{$course_row['course_id']}' $selected>{$course_row['course_name']}</option>";
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
                // Fetches all batches to populate the dropdown.
                $batch_query = "SELECT * FROM batches";
                $batch_result = $conn->query($batch_query);
                while($batch_row = $batch_result->fetch_assoc()){
                    // Pre-selects the student's current batch.
                    $selected = $row['batch_id'] == $batch_row['batch_id'] ? 'selected' : '';
                    echo "<option value='{$batch_row['batch_id']}' $selected>{$batch_row['batch_name']}</option>";
                }
                ?>
            </select>
        </div>
        <?php // Submit button to save the changes. ?>
        <button type="submit" name="update" class="btn btn-primary">Update Student</button>
        <?php // Cancel button to return to the student management page. ?>
        <a href="students.php" class="btn btn-secondary">Cancel</a>
    </form>
<?php 
// Includes the footer file.
include 'footer.php'; 
?>
