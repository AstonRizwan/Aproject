<?php
session_start();

// Function to validate form input and prevent HTML injections
function validateInput($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    return $input;
}

// If the form has been submitted
if (isset($_POST['submitted'])) {
    // Connect to the database
    require_once('connectdb.php');

    $username = isset($_POST['username']) ? validateInput($_POST['username']) : false;
    $password = isset($_POST['password']) ? validateInput($_POST['password']) : false;
    $confirm_password = isset($_POST['confirm_password']) ? validateInput($_POST['confirm_password']) : false;
    $email = isset($_POST['email']) ? validateInput($_POST['email']) : false;

    // Validate password complexity
    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number    = preg_match('@[0-9]@', $password);
    $specialChars = preg_match('@[^\w]@', $password);

    if (!$username || !$email || !$password || !$confirm_password) {
        exit("All fields are required!");
    } elseif (strlen($password) < 8 || !$uppercase || !$lowercase || !$number || !$specialChars) {
        exit("Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character!");
    } elseif ($password !== $confirm_password) {
        exit("Passwords do not match!");
    }

    // Hash the password
    $password = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Register user by inserting the user info
        $stat = $db->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
        $stat->bind_param("sss", $username, $password, $email);
        $stat->execute();
        echo "Congratulations! You are now registered";
    } catch (PDOException $ex) {
        echo "Sorry, a database error occurred! <br>";
        echo "Error details: <em>" . $ex->getMessage() . "</em>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AProject - Register</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <script>
        function validateForm() {
            var password = document.getElementById("password").value;
            var confirmPassword = document.getElementById("confirm_password").value;

            // Validate password complexity
            var pattern = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^\w\d\s:])([^\s]){8,}$/;
            if (!pattern.test(password)) {
                alert("Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character!");
                return false;
            }

            // Confirm password match
            if (password !== confirmPassword) {
                alert("Passwords do not match!");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
<header>
    <h1>Register for AProject</h1>
</header>
<main>
    <section>
        <h2>Registration Form</h2>
        <form action="register.php" method="POST" onsubmit="return validateForm()">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required><br>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br>
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required><br>
            <input type="hidden" name="submitted" value="1">
            <input type="submit" value="Register">
        </form>
        <p>Already have an account? <a href="index.php">Login here</a></p>
    </section>
</main>
<footer>
    <p>&copy; 2024 AProject. All rights reserved.</p>
</footer>
</body>
</html>
