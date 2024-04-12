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

// Check if the user is not logged in, redirect to start
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Check if project ID is provided
if (isset($_GET['id'])) {
    $project_id = $_GET['id'];

    // Retrieve project details from the database
    $query = "SELECT * FROM projects WHERE pid = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $project = $result->fetch_assoc();
        if($project['uid'] !== $_SESSION['uid']){
            header('index.php');
            exit();
        }
        // Check if the form is submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Validate form input
            $title = validateInput($_POST['title']);
            $start_date = validateInput($_POST['start_date']);
            $end_date = validateInput($_POST['end_date']);
            $phase = validateInput($_POST['phase']);
            $description = validateInput($_POST['description']);

            // Update the project details in the database
            $update_query = "UPDATE projects SET title = ?, start_date = ?, end_date = ?, phase = ?, description = ? WHERE pid = ?";
            $stmt = $db->prepare($update_query);
            $stmt->bind_param("sssssi", $title, $start_date, $end_date, $phase, $description, $project_id);
            if ($stmt->execute()) {
                echo "Project details updated successfully.";
            } else {
                echo "Error updating project details.";
            }
        }
    } else {
        echo "Project not found.";
    }
} else {
    echo "Project ID not provided.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Project</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        /* CSS to adjust footer position */
        footer {
            position: relative;
            clear: both;
            margin-top: 20px; /* Add some space above the footer */
            text-align: center;
        }

        /* Container for links */
        .links-container {
            text-align: center;
            margin-top: 20px;
        }

        /* Style for links */
        .links-container a {
            margin-right: 10px;
            color: #333;
        }
    </style>
</head>
<body>
    <header>
        <h1>Update Project</h1>
    </header>
    <main>
        <section class="project-details">
            <h2>Update Project</h2>
            <form action="" method="POST">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" value="<?php echo $project['title']; ?>" required><br>
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" value="<?php echo $project['start_date']; ?>" required><br>
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" value="<?php echo $project['end_date']; ?>"><br>
                <label for="phase">Phase:</label>
                <select id="phase" name="phase">
                    <option value="Design" <?php if ($project['phase'] == 'Design') echo 'selected'; ?>>Design</option>
                    <option value="Development" <?php if ($project['phase'] == 'Development') echo 'selected'; ?>>Development</option>
                    <option value="Testing" <?php if ($project['phase'] == 'Testing') echo 'selected'; ?>>Testing</option>
                    <option value="Deployment" <?php if ($project['phase'] == 'Deployment') echo 'selected'; ?>>Deployment</option>
                    <option value="Complete" <?php if ($project['phase'] == 'Complete') echo 'selected'; ?>>Complete</option>
                </select>
                <br>
                <label for="description">Description:</label><br>
                <textarea id="description" name="description" rows="4" cols="50"><?php echo $project['description']; ?></textarea><br>
                <input type="submit" name="submit" value="Update">
            </form>
            <div class="action-links">
                <a href="projectlist.php">Back to Project List</a>
                <a href="logout.php">Logout</a>
            </div>
        </section>
    </main>
    <footer>
        <p>&copy; 2024 AProject. All rights reserved.</p>
    </footer>
</body>
</html>
