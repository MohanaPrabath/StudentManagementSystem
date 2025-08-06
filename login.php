<?php
// Starts or resumes a session to store user login information.
session_start();
// Includes the database connection file, although it's not strictly needed for this simple login logic.
include 'db.php';

// --- LOGIN LOGIC ---
// Checks if the login form was submitted.
if(isset($_POST['login'])){
    // Gets the username and password from the form.
    $username = $_POST['username'];
    $password = $_POST['password'];

    // --- USER AUTHENTICATION ---
    // This is a simplified, hardcoded login system for demonstration.
    // In a real application, you should query a 'users' table in the database and use hashed passwords.
    
    // Checks if the credentials match the 'admin' user.
    if($username == 'admin' && $password == 'admin'){
        // If they match, store the 'admin' role in the session.
        $_SESSION['role'] = 'admin';
        // Redirect the user to the main page.
        header("Location: index.php");
    // Checks if the credentials match the 'faculty' user.
    } elseif ($username == 'faculty' && $password == 'faculty'){
        // If they match, store the 'faculty' role in the session.
        $_SESSION['role'] = 'faculty';
        // Redirect the user to the main page.
        header("Location: index.php");
    } else {
        // If the credentials do not match any user, set an error message.
        $error = "Invalid credentials";
    }
}
?>
<?php // Defines the document type as HTML5. ?>
<!DOCTYPE html>
<?php // Starts the HTML document and sets the language to English. ?>
<html lang="en">
<?php // Contains meta-information about the HTML document. ?>
<head>
    <?php // Sets the character encoding for the document to UTF-8. ?>
    <meta charset="UTF-8">
    <?php // Configures the viewport for responsive design. ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php // Sets the title of the page. ?>
    <title>Login - Student Attendance System</title>
    <?php // Links to Bootstrap 5 CSS for styling. ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php // Links to Bootstrap Icons for icons. ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <?php // Custom styles to center the login form on the page. ?>
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: #f8f9fa;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
        }
    </style>
</head>
<?php // Starts the body of the HTML document. ?>
<body>
<?php // --- LOGIN FORM --- ?>
<div class="card login-card">
    <div class="card-body">
        <h1 class="card-title text-center mb-4">
            <i class="bi bi-journal-check"></i> Login
        </h1>
        <?php // If an error message is set, display it in an alert box. ?>
        <?php if(isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
        <?php // The login form. ?>
        <form action="login.php" method="post">
            <?php // Username input field. ?>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <?php // Password input field. ?>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <?php // Login button. ?>
            <div class="d-grid">
                <button type="submit" name="login" class="btn btn-primary">Login</button>
            </div>
        </form>
        <?php // An info box showing the demo login credentials. ?>
        <div class="alert alert-info mt-3">
            <p class="mb-0"><strong>Admin:</strong> admin / admin</p>
            <p class="mb-0"><strong>Faculty:</strong> faculty / faculty</p>
        </div>
    </div>
</div>
<?php // Includes the Bootstrap JavaScript bundle. ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<?php // Closes the body of the HTML document. ?>
</body>
<?php // Closes the HTML document. ?>
</html>
