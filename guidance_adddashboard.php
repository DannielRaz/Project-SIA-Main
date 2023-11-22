<?php
session_start();

$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "db_ba31011";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}
$username = $_SESSION['username'];

$srCode = $violationDescription = $violationStatus = $violationOffense = $violationPenalties = "";
$editMode = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $srCode = $_POST['srCode'];
        $violationDescription = $_POST['violationDescription'];
        $violationStatus = $_POST['violationStatus'];
        $violationOffense = $_POST['violationOffense'];
        $violationPenalties = $_POST['violationPenalties'];

        $adminId = 1;
        $guidanceId = 1;

        $violationDate = date('Y-m-d H:i:s');

        $checkSRCodeQuery = "SELECT COUNT(*) AS count FROM tb_studentinfo WHERE SR_Code = '$srCode'";
        $result = $conn->query($checkSRCodeQuery);

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $count = $row['count'];

            if ($count > 0) {
                $insertViolationQuery = "INSERT INTO tb_violation (Violation_Date, SR_Code, Violation_Description, Violation_Status, Admin_ID, Guidance_ID, Violation_Offense, Violation_Penalties)
                    VALUES ('$violationDate', '$srCode', '$violationDescription', '$violationStatus', '$adminId', '$guidanceId', '$violationOffense', '$violationPenalties')";

                if ($conn->query($insertViolationQuery) === TRUE) {
                    // Handle success, if needed
                } else {
                    echo "Error: " . $insertViolationQuery . "<br>" . $conn->error;
                }
            } else {
                echo "Error: The SR_Code does not exist in the tb_studentinfo table.";
            }
        } else {
            echo "Error: Unable to verify the SR_Code.";
        }
    } elseif (isset($_POST['edit'])) {
        $editMode = true;
        $violationID = $_POST['violationID'];

        $getViolationQuery = "SELECT * FROM tb_violation WHERE Violation_ID = '$violationID'";
        $result = $conn->query($getViolationQuery);

        if ($result) {
            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $srCode = $row['SR_Code'];
                $violationDescription = $row['Violation_Description'];
                $violationStatus = $row['Violation_Status'];
                $violationOffense = $row['Violation_Offense'];
                $violationPenalties = $row['Violation_Penalties'];
            } else {
                echo "Error: Violation record not found.";
            }
        } else {
            echo "Error: " . $conn->error;
        }
    } elseif (isset($_POST['delete'])) {
        $violationID = $_POST['violationID'];

        echo "Deleting record...";

        $deleteViolationQuery = "DELETE FROM tb_violation WHERE Violation_ID = '$violationID'";

        if ($conn->query($deleteViolationQuery) === TRUE) {
            echo "Record deleted successfully";
            // Redirect back to guidance_dashboard.php
            header("Location: guidance_dashboard.php");
            exit();
        } else {
            echo "Error deleting record: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Dashboard</title>
    <link rel="stylesheet" type="text/css" href="guidance_adddashboard.css">
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

        <div class="form-container">
            <h2>Add Violation</h2>
            <form method="post" action="guidance_adddashboard.php">
                <input type="hidden" name="violationID" value="<?php echo $editMode ? $violationID : '' ?>">
                <input type="text" id="srCode" name="srCode" placeholder="SR Code" value="<?php echo $srCode ?>" required>
                <input type="text" id="violationDescription" name="violationDescription" placeholder="Violation Description" value="<?php echo $violationDescription ?>" required>
                <label for="violationStatus">Select Violation Status</label>
                <select id="violationStatus" name="violationStatus" required>
                    <option value="Pending" <?php echo ($violationStatus == 'pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="Done" <?php echo ($violationStatus == 'done') ? 'selected' : ''; ?>>Done</option>
                    <option value="Ongoing" <?php echo ($violationStatus == 'ongoing') ? 'selected' : ''; ?>>Ongoing</option>
                </select>
                <label for="violationOffense">Select Violation Offense</label>
                <select id="violationOffense" name="violationOffense" required>
                    <option value="Minor" <?php echo ($violationOffense == 'minor') ? 'selected' : ''; ?>>Minor</option>
                    <option value="Major" <?php echo ($violationOffense == 'major') ? 'selected' : ''; ?>>Major</option>
                </select>
                <input type="text" id="violationPenalties" name="violationPenalties" placeholder="Violation Penalties" value="<?php echo $violationPenalties ?>" required>

                <?php if ($editMode) { ?>
                    <input type="submit" name="update" value="Update Violation" class="form-button">
                    <button type="button" id="clearButton" class="clear-button">Clear</button>
                <?php } else { ?>
                    <input type="submit" name="add" value="Add Violation" class="form-button" >
                    <button type="button" id="clearButton" class="clear-button">Clear</button>
                <?php } ?>
            </form>
        </div>

        <script>
            document.getElementById('clearButton').addEventListener('click', function() {
                clearFormFields();
            });

            function clearFormFields() {
                document.getElementById('srCode').value = '';
                document.getElementById('violationDescription').value = '';
                document.getElementById('violationStatus').value = '';
                document.getElementById('violationOffense').value = '';
                document.getElementById('violationPenalties').value = '';
            }            
        </script>
    </div>
</body>
</html>
