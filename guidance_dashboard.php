<?php
session_start();

$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "db_ba3101";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}
$username = $_SESSION['username'];

$search = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $search = $_POST['search'];
    $sql = "SELECT * FROM tb_violation 
            WHERE Student_Name LIKE '%$search%' OR 
                  Student_Course LIKE '%$search%' OR 
                  Student_Year LIKE '%$search%' OR 
                  Violation_Description LIKE '%$search%' OR 
                  Violation_Status LIKE '%$search%' OR 
                  SR_Code LIKE '%$search%' OR 
                  Violation_Date LIKE '%$search%' OR 
                  Violation_Offense LIKE '%$search%' OR
                  Violation_Penalties LIKE '%search%'";
    $result = $conn->query($sql);
} else {
    $sql = "SELECT * FROM tb_violation";
    $result = $conn->query($sql);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Guidance Dashboard</title>
    <link rel="stylesheet" type="text/css" href="guidance_dashboard.css">
</head>
<body>
<div class="banner">
        <div class="navbar">
            <img src="BSUlogo.png" class="logo">
            <div class="try">
                <h2>Batangas State University</h2>
            </div>
            <ul class="menu">
                <li id="home"><a href="guidance_homedashboard.php">HOME</a></li>
                <li id="list"><a href="guidance_dashboard.php">LIST</a></li>
                <li id="logout"><a href="login.html">LOGOUT</a></li>

            </ul>
        </div>
        
        <div class="gui-dashboard">
            <h2>List Dashboard</h2>
            <ul>
                <li id="createacc"><a href="registrationguidance.php">Create Account</a></li>
                <li id="add"><a href="guidance_adddashboard.php">ADD VIOLATION</a></li>
            </ul>              
                    <form method="post" action="">
                        <input type="text" name="search" placeholder="Search for records..." value="<?php echo $search; ?>">
                        <input type="submit" value="Search">
                    </form>

            <div class="table-container">
                <table>
                    <tr>
                        <th>Violation ID</th>
                        <th>Student Name</th>
                        <th>Student Course</th>
                        <th>Student Year</th>
                        <th>Violation Description</th>
                        <th>Violation Status</th>
                        <th>SR Code</th>
                        <th>Violation Date</th>
                        <th>Violation Offense</th>
                        <th>Violation Penalties</th>
                        <th>Actions</th>
                    </tr>
                    <?php
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['Violation_ID'] . "</td>";
                        echo "<td>" . $row['Student_Name'] . "</td>";
                        echo "<td>" . $row['Student_Course'] . "</td>";
                        echo "<td>" . $row['Student_Year'] . "</td>";
                        echo "<td>" . $row['Violation_Description'] . "</td>";
                        echo "<td>" . $row['Violation_Status'] . "</td>";
                        echo "<td>" . $row['SR_Code'] . "</td>";
                        echo "<td>" . $row['Violation_Date'] . "</td>";
                        echo "<td>" . $row['Violation_Offense'] . "</td>";
                        echo "<td>" . $row['Violation_Penalties'] . "</td>";
                        echo "<td>";
                        echo "<form method='post' action='guidance_adddashboard.php'>";
                        echo "<input type='hidden' name='violationID' value='" . $row['Violation_ID'] . "'>";
                        echo "<input type='submit' name='edit' value='Edit'>";
                        echo "<input type='submit' name='delete' value='Delete'>";
                        echo "</form>";
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </table>
            </div>
        </div>           
</div>        
</body>
</html>
