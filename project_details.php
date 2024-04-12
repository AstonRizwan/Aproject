<?php
session_start();

// Include the database connection
include 'connectdb.php';

// Function to validate form input and prevent HTML injections
function validateInput($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    return $input;
}

// Check if project ID is provided
if (isset($_GET['id'])) {
    $project_id = $_GET['id'];

    // Query database for project details including user's email
    $query = "SELECT p.*, u.email 
              FROM projects p 
              INNER JOIN users u ON p.uid = u.uid 
              WHERE p.pid = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Display project details in a table
        echo "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>Project Details</title>
    <link rel=\"stylesheet\" type=\"text/css\" href=\"style.css\">
</head>
<body>
    <h2>Project Details</h2>
    <table border='1'>
        <tr><th>Title</th><td>" . validateInput($row['title']) . "</td></tr>
        <tr><th>Starting Date</th><td>" . validateInput($row['start_date']) . "</td></tr>
        <tr><th>Ending Date</th><td>" . validateInput($row['end_date']) . "</td></tr>
        <tr><th>Phase</th><td>" . validateInput($row['phase']) . "</td></tr>
        <tr><th>Description</th><td>" . validateInput($row['description']) . "</td></tr>
        <tr><th>User Email</th><td>" . validateInput($row['email']) . "</td></tr>
    </table>
    <br>
    <a href=\"projectlist.php\">Back to Project List</a>
    <a href=\"logout.php\">Logout</a>
</body>
</html>";
    } else {
        echo "Project not found.";
    }
} else {
    echo "Project ID not provided.";
}
?>
