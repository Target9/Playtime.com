<?php
include '../db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $person = $_POST['person'];
    $fromDate = $_POST['fromDate'];
    $toDate = $_POST['toDate'];

    $conn = getDbConnection(USER_ROBLOX);
    $stmt = $conn->prepare("SELECT * FROM activity WHERE person = ? AND timestamp BETWEEN ? AND ?");
    $stmt->bind_param("sss", $person, $fromDate, $toDate);

    $stmt->execute();
    $result = $stmt->get_result();

    echo "<table>";
    echo "<thead><tr><th>Timestamp</th><th>Status</th><th>Game</th><th>IP Address</th></tr></thead>";
    echo "<tbody>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['timestamp'] . "</td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "<td>" . $row['game'] . "</td>";
        echo "<td>" . $row['ip_address'] . "</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";

    $stmt->close();
    $conn->close();
}
?>

<!-- Include JavaScript to further enhance the UI/UX -->
