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

// Create the table if it doesn't exist
$tableCreationQuery = "
CREATE TABLE IF NOT EXISTS information (
    id INT AUTO_INCREMENT PRIMARY KEY,
    Doorno VARCHAR(50),
    name VARCHAR(100),
    address TEXT,
    details JSON
)";
$conn->query($tableCreationQuery);

// ... (rest of your existing functions and code)

$searchDoorno = '';
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['searchDoorno'])) {
    $searchDoorno = $_GET['searchDoorno'];
}

// Fetch existing records for display
if ($searchDoorno) {
    $sql = "SELECT * FROM information WHERE Doorno LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%$searchDoorno%";
    $stmt->bind_param("s", $searchTerm);
} else {
    $sql = "SELECT * FROM information";
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Information Form</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* ... (your existing styles) */
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">Information Form</h1>
        <form action="index.php" method="post">
            <!-- ... (your existing form for new records) -->
        </form>

        <h2 class="mt-5">Search Existing Records</h2>
        <form action="index.php" method="get" class="mb-4">
            <div class="form-group">
                <label for="searchDoorno">Doorno:</label>
                <input type="text" id="searchDoorno" name="searchDoorno" class="form-control" value="<?= htmlspecialchars($searchDoorno) ?>">
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <h2 class="mt-5">Existing Records</h2>
        <?php if ($result->num_rows > 0): ?>
            <table class='table table-striped'>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Doorno</th>
                        <th>Name</th>
                        <th>Address</th>
                        <th>No</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Amount</th>
                        <th>Type</th>
                        <th>Year</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <?php $details = json_decode($row['details'], true); ?>
                        <?php foreach ($details as $detail): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['Doorno']) ?></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['address']) ?></td>
                                <td><?= $detail['no'] ?></td>
                                <td><?= date('d.m.Y', strtotime($detail['date'])) ?></td>
                                <td><?= $detail['time'] ?></td>
                                <td><?= $detail['amount'] ?></td>
                                <td><?= $detail['type'] ?></td>
                                <td><?= $detail['year'] ?></td>
                                <td>
                                    <a href="update.php?id=<?= $row['id'] ?>&no=<?= $detail['no'] ?>" class="btn btn-warning btn-sm">Update</a> |
                                    <a href="index.php?delete=<?= $row['id'] ?>&no=<?= $detail['no'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this record?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No records found.</p>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function addDetail() {
            const container = document.getElementById('details-container');
            const detailDiv = document.createElement('div');
            detailDiv.className = 'detail';
            detailDiv.innerHTML = `...`; // Keep your existing detail input structure here
            container.appendChild(detailDiv);
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>
