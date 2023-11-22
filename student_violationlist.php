<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

// Database connection parameters
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "db_ba31011";

// Create a connection to the database
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$student_acc_id = $_SESSION['student_acc_id'];

// Fetch student information including the image from both tables based on Student_Acc_ID from the session
$sql = "SELECT tbstudinfo.lastname, tbstudinfo.firstname, tbstudinfo.course,
               tb_studentinfo.SR_Code, tb_studentinfo.Student_Year, tb_studentinfo.Student_Picture
        FROM tb_studentinfo
        INNER JOIN tbstudinfo ON tb_studentinfo.studid = tbstudinfo.studid
        INNER JOIN tb_studentacc ON tb_studentinfo.Student_Acc_ID = tb_studentacc.Student_Acc_ID
        WHERE tb_studentinfo.Student_Acc_ID = $student_acc_id";


$result = $conn->query($sql);

if (!$result) {
    die("Error executing query: " . $conn->error);
}

if ($result->num_rows == 1) {
    // Fetch and store the student's information in PHP variables
    $row = $result->fetch_assoc();
    $lastname = $row['lastname'];
    $firstname = $row['firstname'];
    $course = $row['course'];
    $srCode = $row['SR_Code'];
    $year = $row['Student_Year'];
    $imageData = $row['Student_Picture'];

    // Create a base64-encoded image string for display
    $imageData = base64_encode($imageData);
    $imageSrc = "data:image/jpeg;base64," . $imageData;
} else {
    // Handle the case where no records are found
    $lastname = "N/A";
    $firstname = "N/A";
    $course = "N/A";
    $srCode = "N/A";
    $year = "N/A";
    $imageSrc = "path_to_default_image.jpg"; // Replace with the path to a default image
}

// Fetch violation information from tb_violation table
$sql_violations = "SELECT * FROM tb_violation WHERE SR_Code = '$srCode'";
$result_violations = $conn->query($sql_violations);

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Violation List</title>
    <link rel="stylesheet" type="text/css" href="student_violationlist.css">
</head>
<body>
<div class="banner">
        <div class="navbar">
            <img src="BSUlogo.png" class="logo">
            <div class="try">
                <h2>Batangas State University</h2>
            </div>
            <ul class="menu">
                <li id="profile"><a href="student_dashboard.php">PROFILE</a></li>
                <li id="violation"><a href="student_violationlist.php">VIOLATION LIST</a></li>
                <li id="logout"><a href="login.html">LOGOUT</a></li>
            </ul>
        </div>
    
        <div class="student-info">
            <h2>Student Information</h2>
            <p><strong>SR-Code:</strong> <?php echo $srCode; ?></p>
            <p><strong>Lastname:</strong> <?php echo $lastname; ?></p>
            <p><strong>Firstname:</strong> <?php echo $firstname; ?></p>
            <p><strong>Course:</strong> <?php echo $course; ?></p>
            <p><strong>Year:</strong> <?php echo $year; ?></p>
        </div>

        <div class="violations">
            <h2>Violation List</h2>
            <table>
                <tr>
                    <th>Violation Date</th>
                    <th>Violation Description</th>
                    <th>Violation Status</th>
                    <th>Violation Offense</th>
                    <th>Violation Penalties</th>
                </tr>
                <?php
                while ($row_violation = $result_violations->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row_violation['Violation_Date'] . "</td>";
                    echo "<td>" . $row_violation['Violation_Description'] . "</td>";
                    echo "<td>" . $row_violation['Violation_Status'] . "</td>";
                    echo "<td>" . $row_violation['Violation_Offense'] . "</td>";
                    echo "<td>" . $row_violation['Violation_Penalties'] . "</td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </div>
</div>        
</body>
</html>
