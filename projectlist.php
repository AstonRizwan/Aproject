<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Details</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <?php
    session_start();

    // Check if the user is not logged in, redirect to start
    if (!isset($_SESSION['username'])) {
        $user = "Guest";
        $uid = "N/A";
    }
    else{
        $user = $_SESSION["username"];
        $uid = $_SESSION ["uid"];
    }

    // Include the database connection
    include 'connectdb.php';
    

    // Check if search form submitted
    if(isset($_POST['search'])) {
        $search_title = $_POST['search_title'];
        $search_date = $_POST['search_date'];

        // Query to search projects by title or start date
        $query = "SELECT * FROM projects WHERE title LIKE '%$search_title%'";
        if (!empty($search_date)) {
            $query .= " AND start_date = '$search_date'";
        }

        $result = $db->query($query);
    } else {
        // Query to get all project information from the projects table
        $result = $db->query("SELECT * FROM projects");
    }

    // Check if any result is returned
    if ($result->num_rows == 0) {
        echo "<p>No projects available. <a href='projectlist.php'>Go back to project list</a></p>";
    } else {
        // Display the project list in a table
        echo "<h2>Welcome, {$user}!</h2>";
        echo "<h3>Project Information</h3>";
        echo "<form class='search-form' method='POST' action='projectlist.php'>";
        echo "<input type='text' name='search_title' placeholder='Search by title' class='search-input'>";
        echo "<input type='date' name='search_date' placeholder='Search by date' class='search-input'>";
        echo "<input type='submit' name='search' value='Search' class='search-button'>";
        echo "</form>";
        echo "<table border='1'>
                <tr>
                    <th>Title</th>
                    <th>Starting Date</th>
                    <th>Description</th>
                    <th>Action</th> <!-- Add a new column for the action -->
                </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            // Hyperlink the project title to project_details.php with project ID as query parameter
            echo "<td><a href='project_details.php?id=" . $row['pid'] . "'>" . $row['title'] . "</a></td>";
            echo "<td>" . $row['start_date'] . "</td>";
            echo "<td>" . $row['description'] . "</td>";
            // Add the "Update" link
            if($row['uid'] == $uid){
                echo "<td><a href='update_project.php?id=" . $row['pid'] . "'>Update</a></td>";
            }
            echo "</tr>";
        }
        echo "</table>";

    }
    echo"<br>";
    if(isset($_SESSION["username"])){
        echo "<a href='add_project.php'>Add New Project</a>";
        echo "<br>";
        echo "<a href='logout.php'>Logout</a>";
    }
    else{
        echo "<a href='index.php'>Login</a>";
        echo "<br>";
        echo "<a href='register.php'>Register</a>";
    }
    ?>
    <br>
    <style>
        /* CSS for search form */
        .search-form {
            margin-top: 20px;
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .search-input {
            padding: 8px;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .search-button {
            padding: 8px 15px;
            background-color: #333;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .search-button:hover {
            background-color: #555;
        }
    </style>
</body>

</html>
