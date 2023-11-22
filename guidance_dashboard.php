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
    $sql = "SELECT v.*, si.lastname, si.firstname, si.course, s.Student_Year
            FROM tb_violation v
            INNER JOIN tb_studentinfo s ON v.SR_Code = s.SR_Code
            INNER JOIN tbstudinfo si ON s.studid = si.studid
            WHERE v.SR_Code LIKE '%$search%' OR
                  si.lastname LIKE '%$search%' OR 
                  si.firstname LIKE '%$search%' OR 
                  si.course LIKE '%$search%' OR 
                  v.Violation_Description LIKE '%$search%' OR 
                  v.Violation_Status LIKE '%$search%' OR 
                  v.Violation_Date LIKE '%$search%' OR 
                  v.Violation_Offense LIKE '%$search%' OR
                  v.Violation_Penalties LIKE '%$search%'";
} else {
    $sql = "SELECT v.*, si.lastname, si.firstname, si.course, s.Student_Year
            FROM tb_violation v
            INNER JOIN tb_studentinfo s ON v.SR_Code = s.SR_Code
            INNER JOIN tbstudinfo si ON s.studid = si.studid";
}

$result = $conn->query($sql);

if (!$result) {
    die("Error executing query: " . $conn->error);
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
                        <th>SR Code</th>
                        <th>Last Name</th>
                        <th>First Name</th>
                        <th>Student Course</th>
                        <th>Student Year</th>
                        <th>Violation Description</th>
                        <th>Violation Status</th>       
                        <th>Violation Date</th>
                        <th>Violation Offense</th>
                        <th>Violation Penalties</th>
                        <th>Actions</th>
                    </tr>
                    <?php
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['Violation_ID'] . "</td>";
                        echo "<td>" . $row['SR_Code'] . "</td>";
                        echo "<td>" . $row['lastname'] . "</td>";
                        echo "<td>" . $row['firstname'] . "</td>";
                        echo "<td>" . $row['course'] . "</td>";
                        echo "<td>" . $row['Student_Year'] . "</td>";
                        echo "<td>" . $row['Violation_Description'] . "</td>";
                        echo "<td>" . $row['Violation_Status'] . "</td>";                       
                        echo "<td>" . $row['Violation_Date'] . "</td>";
                        echo "<td>" . $row['Violation_Offense'] . "</td>";
                        echo "<td>" . $row['Violation_Penalties'] . "</td>";
                        echo "<td>";
                        echo "<form method='post' action='guidance_adddashboard.php' onsubmit='return confirm(\"Are you sure you want to Edit this record?\")'>";
                        echo "<input type='hidden' name='violationID' value='" . $row['Violation_ID'] . "'>";
                        echo "<input type='submit' name='edit' value='Edit'>";
                        echo "</form>";

                        echo "<form method='post' action='guidance_adddashboard.php' onsubmit='return confirm(\"Are you sure you want to Delete this record?\")'>";
                        echo "<input type='hidden' name='violationID' value='" . $row['Violation_ID'] . "'>";
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
