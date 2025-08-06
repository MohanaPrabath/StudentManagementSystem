<?php
// Includes the database connection file.
include 'db.php';
// Gets the ID of the student to be deleted from the URL.
$id = $_GET['id'];

// --- DELETE STUDENT'S ATTENDANCE RECORDS ---
// It's important to delete related records first to avoid foreign key constraint errors.
$query_attendance = "DELETE FROM attendance WHERE student_id = '$id'";
// Executes the query to delete all attendance records for this student.
if($conn->query($query_attendance) === TRUE){
    // --- DELETE THE STUDENT RECORD ---
    // Once the attendance records are deleted, the student record itself can be deleted.
    $query_student = "DELETE FROM students WHERE student_id = '$id'";
    // Executes the query to delete the student.
    if($conn->query($query_student) === TRUE){
        // If successful, redirect back to the main student management page.
        header("Location: students.php");
    } else {
        // If there's an error deleting the student, display it.
        echo "Error deleting student: " . $conn->error;
    }
} else {
    // If there's an error deleting the attendance records, display it.
    echo "Error deleting attendance records: " . $conn->error;
}
?>
