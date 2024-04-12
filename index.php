<?php
// If the form has been submitted
if (isset($_POST['submitted'])) {
    if (!isset($_POST['username'], $_POST['email'], $_POST['password'])) {
        // Could not get the data that should have been sent.
        exit('Please fill username, email and password fields!');
    }

    // Connect to DB
    require_once("connectdb.php");

    try {
        // Query DB to find the matching username/email and password
        // Using prepare/bindparameter to prevent SQL injection.
        $stat = $db->prepare('SELECT uid,username, password FROM users WHERE username = ? OR email = ?');
        $stat->bind_param('ss', $_POST['username'], $_POST['email']);
        $stat->execute();
        // Bind the result
        $stat->bind_result($uid,$username, $password);

        // Fetch the result row and check 
        if ($stat->fetch()) {  // Matching username/email found
            if (password_verify($_POST['password'], $password)) { // Matching password
                // Start session and redirect to logged-in page
                session_start();
                $_SESSION["username"] = $username;
                $_SESSION["uid"] = $uid;
                header("Location: projectlist.php");
                exit();
            } else {
                echo "<p style='color:red'>Error logging in, password does not match </p>";
            }
        } else {
            // Else display an error
            echo "<p style='color:red'>Error logging in, Username or Email not found </p>";
        }
    } catch (PDOException $ex) {
        echo("Failed to connect to the database.<br>");
        echo($ex->getMessage());
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AProject - Login</title>
    <link rel="stylesheet" type="text/css" href="style.css">

</head>
<body>
    <header>
        <h1>Login to AProject</h1>
    </header>
    <main>
        <section>
            <h2>Login Form</h2>
            <form action="index.php" method="POST">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required><br>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required><br>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required><br>
                <input type="hidden" name="submitted" value="1">
                <input type="submit" value="Login">
            </form>
            <p>Don't have an account? <a href="register.php">Register here</a></p>
            <p> Search without loging in? <a href="projectlist.php">Search projects</a></p>
        </section>
    </main>
    <footer>
        <p>&copy; 2024 AProject. All rights reserved.</p>
    </footer>
</body>
</html>
