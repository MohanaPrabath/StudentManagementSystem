<?php 
// Includes the authentication check file. This ensures that any page using this header is protected.
include 'check_auth.php'; 
?>
<?php // Defines the document type as HTML5. ?>
<!DOCTYPE html>
<?php // Starts the HTML document and sets the language to English. ?>
<html lang="en">
<?php // Contains meta-information about the HTML document. ?>
<head>
    <?php // Sets the character encoding for the document to UTF-8. ?>
    <meta charset="UTF-8">
    <?php // Configures the viewport for responsive design, making the site adaptable to different device widths. ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php // Sets the title of the page, which appears in the browser tab. ?>
    <title>Student Attendance System</title>
    <?php // Links to the Bootstrap 5 CSS framework for styling. ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php // Links to the Bootstrap Icons library for using icons throughout the site. ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <?php // Starts a block of internal CSS for custom styling. ?>
    <style>
        <?php // Styles the body of the page. ?>
        body {
            <?php // Sets a light gray background color. ?>
            background-color: #f8f9fa;
        }
        <?php // Styles the navigation bar. ?>
        .navbar {
            <?php // Sets a white background for the navbar. ?>
            background-color: #ffffff;
            <?php // Adds a subtle shadow for a floating effect. ?>
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        <?php // Styles card elements. ?>
        .card {
            <?php // Removes the default border from cards. ?>
            border: none;
            <?php // Adds a shadow to cards to make them stand out. ?>
            box-shadow: 0 4px 8px rgba(0,0,0,.1);
        }
    </style>
</head>
<?php // Starts the body of the HTML document, containing the visible content. ?>
<body>

<?php // --- NAVIGATION BAR --- ?>
<nav class="navbar navbar-expand-lg navbar-light mb-4">
    <?php // A full-width container for the navbar contents. ?>
    <div class="container-fluid">
        <?php // The brand link, typically for the site name or logo. ?>
        <a class="navbar-brand" href="index.php">
            <?php // An icon and the name of the system. ?>
            <i class="bi bi-journal-check"></i> Attendance System
        </a>
        <?php // The hamburger menu button for mobile views. ?>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <?php // The icon inside the toggler button. ?>
            <span class="navbar-toggler-icon"></span>
        </button>
        <?php // This div contains the navigation links and is collapsible on smaller screens. ?>
        <div class="collapse navbar-collapse" id="navbarNav">
            <?php // An unordered list for the navigation items, aligned to the end (right side). ?>
            <ul class="navbar-nav ms-auto">
                <?php // A list item for a navigation link. ?>
                <li class="nav-item">
                    <?php // Link to the Dashboard page. ?>
                    <a class="nav-link" href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
                </li>
                <?php // A list item for a navigation link. ?>
                <li class="nav-item">
                    <?php // Link to the Mark Attendance page. ?>
                    <a class="nav-link" href="index.php"><i class="bi bi-pencil-square"></i> Mark Attendance</a>
                </li>
                <?php // A list item for a navigation link. ?>
                <li class="nav-item">
                    <?php // Link to the View Records page. ?>
                    <a class="nav-link" href="view.php"><i class="bi bi-calendar-week"></i> View Records</a>
                </li>
                <?php 
                // Conditionally displays the 'Manage Students' link only if the user is an admin.
                if(isAdmin()): 
                ?>
                <?php // A list item for a navigation link, shown only to admins. ?>
                <li class="nav-item">
                    <?php // Link to the Manage Students page. ?>
                    <a class="nav-link" href="students.php"><i class="bi bi-people"></i> Manage Students</a>
                </li>
                <?php 
                // Ends the 'if' statement.
                endif; 
                ?>
                <?php // A list item for a navigation link. ?>
                <li class="nav-item">
                    <?php // Link to the Reports page. ?>
                    <a class="nav-link" href="reports.php"><i class="bi bi-file-earmark-bar-graph"></i> Reports</a>
                </li>
                <?php // A list item for a navigation link. ?>
                <li class="nav-item">
                    <?php // Link to log out of the system. ?>
                    <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<?php // A container for the main content of the page, providing padding and alignment. ?>
<div class="container">
