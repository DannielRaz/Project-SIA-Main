<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "db_ba3101";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$student_acc_id = $_SESSION['student_acc_id'];

$sql_student_info = "SELECT tb_studentinfo.SR_Code, tb_studentinfo.Student_Name, tb_studentinfo.Student_Course, tb_studentinfo.Student_Year, tb_studentinfo.Student_Picture
                    FROM tb_studentinfo
                    WHERE tb_studentinfo.Student_Acc_ID = $student_acc_id";

$result_student_info = $conn->query($sql_student_info);

if ($result_student_info->num_rows == 1) {
    $row_student_info = $result_student_info->fetch_assoc();
    $srCode = $row_student_info['SR_Code'];
    $name = $row_student_info['Student_Name'];
    $course = $row_student_info['Student_Course'];
    $year = $row_student_info['Student_Year'];
} else {
    $srCode = "N/A";
    $name = "N/A";
    $course = "N/A";
    $year = "N/A";
}


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
            <p><strong>Name:</strong> <?php echo $name; ?></p>
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
