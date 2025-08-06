<?php
// --- DATABASE CONNECTION SETTINGS ---
// The server where the database is hosted, usually 'localhost' for a local XAMPP setup.
$servername = "localhost";
// The username to connect to the MySQL database, 'root' is the default for XAMPP.
$username = "root";
// The password for the database user, which is empty by default in XAMPP.
$password = "";
// The name of the database we will be using for this application.
$dbname = "attendance_system";

// --- ESTABLISH DATABASE CONNECTION ---
// Creates a new MySQLi object to establish a connection to the database.
$conn = new mysqli($servername, $username, $password);

// --- VERIFY CONNECTION ---
// Checks if there was an error during the connection attempt.
if ($conn->connect_error) {
  // If an error occurred, the script stops immediately and displays the error message.
  die("Connection failed: " . $conn->connect_error);
}

// --- CREATE DATABASE IF IT DOESN'T EXIST ---
// SQL query to create the database, but only if it doesn't already exist.
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
// Executes the query to create the database.
if ($conn->query($sql) === TRUE) {
  // Success message (currently commented out).
  // echo "Database created successfully";
} else {
  // If there's an error, display it.
  echo "Error creating database: " . $conn->error;
}

// --- SELECT THE DATABASE ---
// Selects the application's database to use for all subsequent queries.
$conn->select_db($dbname);

// --- TABLE CREATION QUERIES ---

// SQL query to create the 'courses' table if it doesn't exist.
$sql_courses = "CREATE TABLE IF NOT EXISTS courses (
  course_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  course_name VARCHAR(50) NOT NULL
)";
// Executes the query.
if ($conn->query($sql_courses) === TRUE) {
  // Success message (commented out).
} else {
  echo "Error creating table: " . $conn->error;
}

// SQL query to create the 'batches' table if it doesn't exist.
$sql_batches = "CREATE TABLE IF NOT EXISTS batches (
  batch_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  batch_name VARCHAR(50) NOT NULL
)";
// Executes the query.
if ($conn->query($sql_batches) === TRUE) {
  // Success message (commented out).
} else {
  echo "Error creating table: " . $conn->error;
}

// SQL query to create the 'students' table if it doesn't exist.
$sql_students = "CREATE TABLE IF NOT EXISTS students (
  student_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL,
  email VARCHAR(50),
  course_id INT(6) UNSIGNED,
  batch_id INT(6) UNSIGNED,
  reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (course_id) REFERENCES courses(course_id),
  FOREIGN KEY (batch_id) REFERENCES batches(batch_id)
)";
// Executes the query.
if ($conn->query($sql_students) === TRUE) {
  // Success message (commented out).
} else {
  echo "Error creating table: " . $conn->error;
}

// --- PATCH FOR EXISTING 'students' TABLE ---
// Checks if the 'course_id' column exists in the 'students' table.
$check_course_id_query = "SHOW COLUMNS FROM `students` LIKE 'course_id'";
$result_course_id = $conn->query($check_course_id_query);
// If the column does not exist (meaning it's an older version of the database), add the missing columns.
if ($result_course_id->num_rows == 0) {
    $conn->query("ALTER TABLE students ADD COLUMN course_id INT(6) UNSIGNED, ADD COLUMN batch_id INT(6) UNSIGNED;");
    $conn->query("ALTER TABLE students ADD FOREIGN KEY (course_id) REFERENCES courses(course_id), ADD FOREIGN KEY (batch_id) REFERENCES batches(batch_id);");
}

// SQL query to create the 'attendance' table if it doesn't exist.
$sql_attendance = "CREATE TABLE IF NOT EXISTS attendance (
  id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  student_id INT(6) UNSIGNED NOT NULL,
  date DATE NOT NULL,
  status VARCHAR(10) NOT NULL,
  time_marked DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (student_id) REFERENCES students(student_id)
)";
// Executes the query.
if ($conn->query($sql_attendance) === TRUE) {
  // Success message (commented out).
} else {
  echo "Error creating table: " . $conn->error;
}

// --- PATCH FOR EXISTING 'attendance' TABLE ---
// Checks if the 'time_marked' column exists.
$check_column_query = "SHOW COLUMNS FROM `attendance` LIKE 'time_marked'";
$result = $conn->query($check_column_query);
// If the column does not exist, add it to the table.
if ($result->num_rows == 0) {
    $conn->query("ALTER TABLE attendance ADD COLUMN time_marked DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;");
}

// --- DUMMY DATA INSERTION ---

// SQL queries to insert some sample data for testing purposes.
$sql_insert_courses = "INSERT INTO courses (course_name) VALUES ('Computer Science'), ('Information Technology')";
$sql_insert_batches = "INSERT INTO batches (batch_name) VALUES ('2023'), ('2024')";
$sql_insert_students = "INSERT INTO students (name, email, course_id, batch_id) VALUES 
('John Doe', 'john.doe@example.com', 1, 1),
('Jane Smith', 'jane.smith@example.com', 1, 2),
('Peter Jones', 'peter.jones@example.com', 2, 1)";

// Checks if the 'courses' table is empty before inserting data.
$result_courses = $conn->query("SELECT * FROM courses");
if ($result_courses->num_rows == 0) {
    $conn->query($sql_insert_courses);
}
// Checks if the 'batches' table is empty before inserting data.
$result_batches = $conn->query("SELECT * FROM batches");
if ($result_batches->num_rows == 0) {
    $conn->query($sql_insert_batches);
}

// Checks if the 'students' table is empty before inserting data.
$result_students = $conn->query("SELECT * FROM students");
if ($result_students->num_rows == 0) {
    $conn->query($sql_insert_students);
}

?>
