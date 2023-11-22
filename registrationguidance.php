<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $firstname = $_POST['first_name'];
    $lastname = $_POST['last_name'];

    // Your database connection code
    $servername = "localhost";
    $db_username = "root";
    $db_password = "";
    $dbname = "db_ba3101";

    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Set the role to 'admin'
    $role = 'guidance';

    // Determine the tables and columns based on the selected role
    if ($role === 'guidance') {
        $guidance_table = 'tb_guidance';
        $guidance_username_column = 'Guidance_User';
        $guidance_password_column = 'Guidance_Password';
        $empid_column = 'empid_gui';
    } else {
        // Handle invalid role
        header("Location: registrationguidance.php?status=invalid_role");
        exit;
    }

    // Start a transaction
    $conn->begin_transaction();

    // Insert the new user into the admin table
    $insert_sql_guidance = "INSERT INTO $guidance_table ($guidance_username_column, $guidance_password_column) VALUES ('$username', '$password')";
    if (!$conn->query($insert_sql_guidance)) {
        // Rollback the transaction on failure
        $conn->rollback();
        header("Location: registrationguidance.php?status=registration_failed");
        exit;
    }

    // Get the auto-incremented Admin_ID
    $guidance_id = $conn->insert_id;

    // Update the empid in tb_admin with the same value as Admin_ID
    $update_empid_sql = "UPDATE $guidance_table SET $empid_column = '$guidance_id' WHERE Guidance_ID = '$guidance_id'";
    if (!$conn->query($update_empid_sql)) {
        // Rollback the transaction on failure
        $conn->rollback();
        header("Location: registrationguidance.php?status=registration_failed");
        exit;
    }

    // Insert the employee info into tbempinfo with the same empid
    $empinfo_table = 'tbempinfoguidance';
    $empinfo_sql = "INSERT INTO $empinfo_table (empid_gui, lastname_gui, firstname_gui) VALUES ('$guidance_id', '$lastname', '$firstname')";
    if (!$conn->query($empinfo_sql)) {
        // Rollback the transaction on failure
        $conn->rollback();
        header("Location: registrationguidance.php?status=registration_failed");
        exit;
    }

    // Commit the transaction
    $conn->commit();

    // Registration successful, redirect to login page
    header("Location: login.html");
    exit;

    $conn->close();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Page</title>
    <link rel="stylesheet" type="text/css" href="registration.css">
</head>
<body>
    <div class="registration-container">    
        <h2>Create Guidance Account</h2>
        <form action="" method="post">
            <div class="input-box">
                <label for="last_name"></label>
                <input type="text" id="last_name" name="last_name" placeholder="Last Name" required>
            </div>

            <div class="input-box">
                <label for="first_name"></label>
                <input type="text" id="first_name" name="first_name" placeholder="First Name" required>
            </div>

            <div class="input-box">
                <label for="department"></label>
                <input type="text" id="department" name="department" placeholder="Department" required>
            </div>
            <div class="input-box">
                <label for="username"></label>
                <input type="text" id="username" name="username" placeholder="Username" required>
            </div>

            <div class="input-box">
                <label for="password"></label>
                <input type="password" id="password" name="password" placeholder="Password" required>
            </div>

            <input class="btn" type="submit" value="Create Account">
        </form>
        <button class="go-to-main-btn" onclick="goToLoginPage()">Guidance Dashboard</button>
    </div>

    <script>
        // Get the status from the URL
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');

        // Display a pop-up message if the status is present
        if (status) {
            let message = '';

            switch (status) {
                case 'invalid_role':
                    message = 'Invalid role selected.';
                    break;
                case 'username_exists':
                    message = 'Username already exists. Please choose a different username.';
                    break;
                case 'registration_failed':
                    message = 'Registration failed. Please try again.';
                    break;
                default:
                    // Handle other status values as needed
                    break;
            }

            if (message !== '') {
                alert(message);
            }
        }

        function goToLoginPage() {
            window.location.href = 'guidance_homedashboard.php';
        }
    </script>
</body>
</html>


