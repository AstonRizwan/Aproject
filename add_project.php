<?php
session_start();

// Check if the user is not logged in, redirect to start
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Include the database connection
include 'connectdb.php';

// Generate CSRF token and store it in the session
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Generate a random token
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }

    // Retrieve logged-in user's ID
    $username = $_SESSION['username'];
    $query = "SELECT uid FROM users WHERE username = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $uid = $user['uid'];

    // Retrieve form data and validate inputs
    $title = validateInput($_POST['title']);
    $start_date = validateInput($_POST['start_date']);
    $end_date = validateInput($_POST['end_date']);
    $phase = validateInput($_POST['phase']);
    $description = validateInput($_POST['description']);

    // Insert the new project into the database
    $query = "INSERT INTO projects (uid, title, start_date, end_date, phase, description) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->bind_param("isssss", $uid, $title, $start_date, $end_date, $phase, $description);
    if ($stmt->execute()) {
        $success_message = "New project added successfully!";
    } else {
        $error_message = "Error: " . $db->error;
    }
}

// Function to validate form input and prevent HTML injections
function validateInput($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    return $input;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Project</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        h2 {
            margin-bottom: 20px;
        }

        form {
            width: 300px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        label {
            margin-bottom: 10px;
        }

        input[type="text"],
        input[type="date"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        input[type="submit"] {
            width: 100%;
            background-color: #333;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #555;
        }

        a {
            margin-top: 20px;
            text-decoration: none;
            color: #333;
        }

        a:hover {
            text-decoration: underline;
        }

        .message {
            margin-top: 20px;
            text-align: center;
            font-weight: bold;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }
    </style>
</head>

<body>
    <h2>Add New Project</h2>
    <?php if (isset($success_message)) : ?>
        <p class="message success"><?php echo $success_message; ?></p>
    <?php endif; ?>
    <?php if (isset($error_message)) : ?>
        <p class="message error"><?php echo $error_message; ?></p>
    <?php endif; ?>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required><br>
        <label for="start_date">Starting Date:</label>
        <input type="date" id="start_date" name="start_date" required><br>
        <label for="end_date">Ending Date:</label>
        <input type="date" id="end_date" name="end_date" required><br>
        <!-- Change the input for "Phase" to a dropdown menu -->
        <label for="phase">Phase:</label>
        <select id="phase" name="phase" required>
            <option value="design">Design</option>
            <option value="development">Development</option>
            <option value="testing">Testing</option>
            <option value="deployment">Deployment</option>
            <option value="complete">Complete</option>
        </select><br>
        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea><br>
        <input type="submit" value="Add Project">
    </form>
    <a href="projectlist.php">Back to Project List</a>
    <a href="logout.php">Logout</a>
</body>

</html>
