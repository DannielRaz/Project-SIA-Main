
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

// Fetch admin information from tb_admin and tbempinfo tables
$sql = "SELECT g.Guidance_User, e.lastname, e.firstname, e.department
        FROM tb_guidance g
        INNER JOIN tbempinfo e ON g.empid = e.empid
        WHERE g.Guidance_User = '$username'";

$result = $conn->query($sql);

if (!$result) {
    die("Error executing query: " . $conn->error);
}

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
   
    $guidanceUser = $row['Guidance_User'];
    $lastname = $row['lastname'];
    $firstname = $row['firstname'];
    $department = $row['department'];

} else {
    // Handle the case where no records are found
   
    $guidanceUser = "N/A";
    $lastname = "N/A";
    $firstname = "N/A";
    $department = "N/A";
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Guidance Home Dashboard</title>
    <link rel="stylesheet" type="text/css" href="admin_homedashboard.css">
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
            <li id="violation"><a href="guidance_dashboard.php">VIOLATION LIST</a></li>
            <li id="logout"><a href="login.html">LOGOUT</a></li>
        </ul>
    </div>
    <div class="admin-info">
        <div class="admin-info-details">
            <h2>Guidance Information</h2>
            <p><strong>Guidance User:</strong> <?php echo $guidanceUser; ?></p>
            <p><strong>Lastname:</strong> <?php echo $lastname; ?></p>
            <p><strong>Firstname:</strong> <?php echo $firstname; ?></p>
            <p><strong>Department:</strong> <?php echo $department; ?></p>
        </div>
    </div>

    
</div>
</body>
</html>
