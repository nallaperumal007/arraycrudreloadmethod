<?php
// Database credentials
$servername = "localhost";
$username = "root"; // Your MySQL username
$password = ""; // Your MySQL password
$dbname = "information"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch existing record for editing
if (isset($_GET['id']) && isset($_GET['no'])) {
    $id = $_GET['id'];
    $no = $_GET['no'];

    // Fetch existing record
    $sql = "SELECT details FROM information WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $record = $result->fetch_assoc();
    $details = json_decode($record['details'], true);
    $stmt->close();

    // Find the specific detail to edit
    $detailToEdit = null;
    foreach ($details as $detail) {
        if ($detail['no'] == $no) {
            $detailToEdit = $detail;
            break;
        }
    }

    if (!$detailToEdit) {
        die("Detail not found.");
    }

    // Handle update form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $date = $_POST['date'];
        $time = $_POST['time'];
        $amount = $_POST['amount'];
        $type = $_POST['type'];
        $year = $_POST['year'];

        // Update the specific detail
        foreach ($details as &$detail) {
            if ($detail['no'] == $no) {
                $detail['date'] = $date;
                $detail['time'] = $time;
                $detail['amount'] = $amount;
                $detail['type'] = $type;
                $detail['year'] = $year;
                break;
            }
        }
        unset($detail);

        // Update the record in the database
        $updatedDetailsJson = json_encode($details);
        $sql = "UPDATE information SET details=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $updatedDetailsJson, $id);
        $stmt->execute();
        $stmt->close();

        header("Location: index.php");
        exit();
    }
} else {
    die("Invalid request.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Record</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1 class="mb-4">Update Record</h1>
        <form action="update.php?id=<?= $id ?>&no=<?= $no ?>" method="post">
            <div class="form-group">
                <label>Date:</label>
                <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($detailToEdit['date']) ?>" required>
            </div>
            <div class="form-group">
                <label>Time:</label>
                <input type="time" name="time" class="form-control" value="<?= htmlspecialchars($detailToEdit['time']) ?>" required>
            </div>
            <div class="form-group">
                <label>Amount:</label>
                <input type="number" step="0.01" name="amount" class="form-control" value="<?= htmlspecialchars($detailToEdit['amount']) ?>" required>
            </div>
            <div class="form-group">
                <label>Type:</label>
                <select name="type" class="form-control" required>
                    <option value="water" <?= $detailToEdit['type'] == 'water' ? 'selected' : '' ?>>Water</option>
                    <option value="current" <?= $detailToEdit['type'] == 'current' ? 'selected' : '' ?>>Current</option>
                    <option value="House" <?= $detailToEdit['type'] == 'House' ? 'selected' : '' ?>>House</option>
                </select>
            </div>
            <div class="form-group">
                <label>Year:</label>
                <input type="number" name="year" class="form-control" value="<?= htmlspecialchars($detailToEdit['year']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
