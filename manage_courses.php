<?php
// --- SETUP AND SECURITY ---
include 'header.php';
// Ensure only admins can access this page.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// --- HANDLE ADDING A NEW COURSE ---
// Check if the form was submitted to add a new course.
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_course'])) {
    // Get and sanitize the course name from the form.
    $course_name = trim($_POST['course_name']);
    // Validate that the course name is not empty.
    if (!empty($course_name)) {
        // Prepare an INSERT statement to prevent SQL injection.
        $stmt = $conn->prepare("INSERT INTO courses (course_name) VALUES (?)");
        $stmt->bind_param("s", $course_name);
        // Execute the statement and set a success or error message in the session.
        if ($stmt->execute()) {
            $_SESSION['message'] = "Course added successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            // The 'course_name' is a UNIQUE column, so this error usually means it already exists.
            $_SESSION['message'] = "Error adding course. It may already exist.";
            $_SESSION['message_type'] = "danger";
        }
        $stmt->close();
        // Redirect back to the same page to prevent form resubmission on refresh.
        header("Location: manage_courses.php");
        exit();
    } else {
        // If the course name was empty, set an error message.
        $_SESSION['message'] = "Course name cannot be empty.";
        $_SESSION['message_type'] = "danger";
        header("Location: manage_courses.php");
        exit();
    }
}

// --- HANDLE COURSE DELETION ---
// Check if a 'delete_id' is present in the URL.
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    // Prepare a DELETE statement.
    // Note: The database's 'ON DELETE CASCADE' will automatically handle deleting
    // related records in 'batch_courses' and 'attendance'.
    $stmt = $conn->prepare("DELETE FROM courses WHERE course_id = ?");
    $stmt->bind_param("i", $delete_id);
    // Execute the statement and set a success or error message.
    if ($stmt->execute()) {
        $_SESSION['message'] = "Course deleted successfully.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error deleting course. It might be in use.";
        $_SESSION['message_type'] = "danger";
    }
    $stmt->close();
    // Redirect back to the main page.
    header("Location: manage_courses.php");
    exit();
}

// --- DATA FETCHING FOR DISPLAY ---
// Fetch all courses from the database to display in the table.
$courses_result = $conn->query("SELECT * FROM courses ORDER BY course_name");
?>

<!-- --- HTML CONTENT --- -->
<div class="container mt-4">
    <h1 class="mb-4">Manage Courses</h1>

    <?php
    // --- DISPLAY SESSION MESSAGES ---
    // Check if a message was set in the session (e.g., after adding or deleting a course).
    if (isset($_SESSION['message'])) {
        // Display the message in a Bootstrap alert box.
        echo "<div class='alert alert-{$_SESSION['message_type']}'>{$_SESSION['message']}</div>";
        // Unset the session variables so the message doesn't show again on the next page load.
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
    ?>

    <!-- Add New Course Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h3>Add New Course</h3>
        </div>
        <div class="card-body">
            <form action="manage_courses.php" method="post">
                <div class="mb-3">
                    <label for="course_name" class="form-label">Course Name</label>
                    <input type="text" class="form-control" id="course_name" name="course_name" required>
                </div>
                <button type="submit" name="add_course" class="btn btn-primary">Add Course</button>
            </form>
        </div>
    </div>

    <!-- List of Existing Courses -->
    <div class="card">
        <div class="card-header">
            <h3>Existing Courses</h3>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Course Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($courses_result->num_rows > 0): ?>
                        <?php while($course = $courses_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                                <td>
                                    <!-- Edit button links to the edit page with the course ID. -->
                                    <a href="edit_course.php?id=<?php echo $course['course_id']; ?>" class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil-fill"></i> Edit
                                    </a>
                                    <!-- Delete button links to this page with a delete_id, with a confirmation dialog. -->
                                    <a href="manage_courses.php?delete_id=<?php echo $course['course_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this course? This will remove it from all batches and delete all related attendance records.')">
                                        <i class="bi bi-trash-fill"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <!-- Show a message if no courses are found. -->
                        <tr>
                            <td colspan="2" class="text-center">No courses found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
