<?php
// Starts or resumes a user session, which is necessary to store and access user data like their role across multiple pages.
session_start();

// Checks if the 'role' variable is not set in the current session.
// This is the primary check to see if a user is logged in.
if(!isset($_SESSION['role'])){
    // If the 'role' is not set (meaning the user is not logged in), this sends an HTTP header to redirect the browser to the login page.
    header("Location: login.php");
    // Immediately stops the script from executing any further. This is a crucial security step to prevent any part of the protected page from being loaded.
    exit();
}

// Defines a reusable function named 'isAdmin'.
function isAdmin(){
    // This function checks if the 'role' stored in the session is exactly 'admin' and returns true or false.
    return $_SESSION['role'] == 'admin';
}

// Defines a reusable function named 'isFaculty'.
function isFaculty(){
    // This function checks if the 'role' stored in the session is exactly 'faculty' and returns true or false.
    return $_SESSION['role'] == 'faculty';
}
?>
