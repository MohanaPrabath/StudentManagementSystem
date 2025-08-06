<?php
// Includes the database connection file.
include 'db.php';
// Gets the ID of the attendance record to be deleted from the URL.
$id = $_GET['id'];

// --- FETCH DATE BEFORE DELETING ---
// This is necessary to redirect the user back to the correct date on the view page.
$query = "SELECT date FROM attendance WHERE id = '$id'";
$result = $conn->query($query);
// Fetches the result.
$row = $result->fetch_assoc();
$date = $row['date'];

// --- DELETE LOGIC ---
// SQL query to delete the specific attendance record.
$query = "DELETE FROM attendance WHERE id = '$id'";
// Executes the query.
if($conn->query($query) === TRUE){
    // If deletion is successful, redirect the user back to the view page for the correct date.
    header("Location: view.php?date=" . $date);
} else {
    // If there's an error, display it.
    echo "Error deleting record: " . $conn->error;
}
?>
