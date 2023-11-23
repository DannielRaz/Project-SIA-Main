<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $firstname = $_POST['first_name'];
    $lastname = $_POST['last_name'];
    $department = $_POST['department'];

    // Your database connection code
    $servername = "localhost";
    $db_username = "root";
    $db_password = "";
    $dbname = "db_ba3101";

    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Start a transaction
    $conn->begin_transaction();

    // Insert the employee info into tbempinfo to get the auto-incremented empid
    $empinfo_sql = "INSERT INTO tbempinfo (lastname, firstname, department) VALUES ('$lastname', '$firstname', '$department')";
    if (!$conn->query($empinfo_sql)) {
        // Rollback the transaction on failure
        $conn->rollback();
        header("Location: registrationadmin.php?status=registration_failed");
        exit;
    }

    // Get the auto-incremented empid
    $empid = $conn->insert_id;

    // Insert the new user into the admin table
    $insert_sql_admin = "INSERT INTO tb_admin (Admin_User, Admin_Password, empid) VALUES ('$username', '$password', '$empid')";
    if (!$conn->query($insert_sql_admin)) {
        // Rollback the transaction on failure
        $conn->rollback();
        header("Location: registrationadmin.php?status=registration_failed");
        exit;
    }

    // Update the Admin_ID with the same value as empid
    $update_admin_id_sql = "UPDATE tb_admin SET Admin_ID = '$empid' WHERE empid = '$empid'";
    if (!$conn->query($update_admin_id_sql)) {
        // Rollback the transaction on failure
        $conn->rollback();
        header("Location: registrationadmin.php?status=registration_failed");
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
<html>
<head>
    <title>Registration Page</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="registration.css">
</head>
<body>
    <div class="registration-container">    
        <h2>Create Admin Account</h2>
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
        <button class="go-to-main-btn" onclick="goToLoginPage()">Admin Dashboard</button>
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
            window.location.href = 'admin_homedashboard.php';
        }
    </script>
</body>
</html>
