<?php 
// Includes the header file, which contains authentication checks and the page layout.
include 'header.php'; 
// Checks if the current user is an administrator.
if(!isAdmin()){
    // If the user is not an admin, display an error message and stop executing the script.
    echo "<div class='alert alert-danger'>You are not authorized to view this page.</div>";
    include 'footer.php';
    exit();
}
?>
<?php // Main heading for the student management page. ?>
<h1 class="mb-4">Manage Students</h1>
    <?php // Card container for the student list. ?>
    <div class="card">
        <?php // Card header containing the 'Add New Student' button. ?>
        <div class="card-header">
            <a href="add_student.php" class="btn btn-success">Add New Student</a>
        </div>
        <?php // Card body where the table of students is displayed. ?>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Ensures the database connection is available.
                    include 'db.php';
                    // SQL query to select all students from the database.
                    $query = "SELECT * FROM students";
                    $result = $conn->query($query);
                    // Loops through each student record found.
                    while($row = $result->fetch_assoc()){
                    ?>
                    <tr>
                        <?php // Displays the student's ID. ?>
                        <td><?php echo $row['student_id']; ?></td>
                        <?php // Displays the student's name. ?>
                        <td><?php echo $row['name']; ?></td>
                        <?php // Displays the student's email. ?>
                        <td><?php echo $row['email']; ?></td>
                        <?php // Contains the action buttons for editing and deleting a student. ?>
                        <td>
                            <a href="edit_student.php?id=<?php echo $row['student_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="delete_student.php?id=<?php echo $row['student_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
<?php 
// Includes the footer file to close the HTML structure.
include 'footer.php'; 
?>
