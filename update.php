<?php 
// Includes the header file for authentication and page layout.
include 'header.php'; 
?>
<?php // Main heading for the page. ?>
<h1 class="mb-4">Update Attendance</h1>
    <?php
    // --- DATA FETCHING AND FORM SUBMISSION LOGIC ---
    // Includes the database connection.
    include 'db.php';
    // Gets the attendance record ID from the URL.
    $id = $_GET['id'];
    // SQL query to get the specific attendance record along with the student's name.
    $query = "SELECT attendance.*, students.name FROM attendance 
              JOIN students ON attendance.student_id = students.student_id 
              WHERE attendance.id = '$id'";
    $result = $conn->query($query);
    // Fetches the record's data.
    $row = $result->fetch_assoc();

    // Checks if the 'update' button was clicked.
    if(isset($_POST['update'])){
        // Gets the new status from the form.
        $status = $_POST['status'];
        // SQL query to update the status of the attendance record.
        $query = "UPDATE attendance SET status = '$status' WHERE id = '$id'";
        // Executes the query.
        if($conn->query($query) === TRUE){
            // If successful, redirect back to the view page for the date of the record that was just updated.
            header("Location: view.php?date=" . $row['date']);
        } else {
            // If there's an error, display it.
            echo "<div class='alert alert-danger'>Error updating record: " . $conn->error . "</div>";
        }
    }
    ?>
    <?php // --- HTML FORM --- ?>
    <?php // This form displays the attendance record and allows updating the status. ?>
    <form action="update.php?id=<?php echo $id; ?>" method="post">
        <?php // Displays the student's name (read-only). ?>
        <div class="form-group">
            <label>Student Name:</label>
            <p><?php echo $row['name']; ?></p>
        </div>
        <?php // Displays the date of the attendance record (read-only). ?>
        <div class="form-group">
            <label>Date:</label>
            <p><?php echo $row['date']; ?></p>
        </div>
        <?php // Dropdown to change the attendance status. ?>
        <div class="form-group">
            <label for="status">Status:</label>
            <select name="status" class="form-control">
                <?php // Pre-selects the current status of the record. ?>
                <option value="Present" <?php if($row['status'] == 'Present') echo 'selected'; ?>>Present</option>
                <option value="Absent" <?php if($row['status'] == 'Absent') echo 'selected'; ?>>Absent</option>
            </select>
        </div>
        <?php // Submit button to save the changes. ?>
        <button type="submit" name="update" class="btn btn-primary">Update</button>
        <?php // Cancel button to return to the view page without saving. ?>
        <a href="view.php?date=<?php echo $row['date']; ?>" class="btn btn-secondary">Cancel</a>
    </form>
<?php 
// Includes the footer file.
include 'footer.php'; 
?>
