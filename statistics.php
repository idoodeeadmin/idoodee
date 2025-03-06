<?php
session_start();
include 'connect.php'; 


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT 
            e.eventid, e.event_name, 
            SUM(CASE WHEN s.status = 'approved' THEN 1 ELSE 0 END) AS total_approved,
            SUM(CASE WHEN s.status = 'approved' AND u.gender = 'male' THEN 1 ELSE 0 END) AS male_count,
            SUM(CASE WHEN s.status = 'approved' AND u.gender = 'female' THEN 1 ELSE 0 END) AS female_count,
            SUM(CASE WHEN s.status = 'approved' AND s.checked_in = 1 THEN 1 ELSE 0 END) AS checked_in_count,
            SUM(CASE WHEN s.status = 'approved' AND s.checked_in = 0 THEN 1 ELSE 0 END) AS not_checked_in_count
        FROM events e
        LEFT JOIN status s ON e.eventid = s.event_id
        LEFT JOIN users u ON s.user_id = u.id
        WHERE e.user_id = ?
        GROUP BY e.eventid, e.event_name";

$stmt = $connect->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Statistics</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="Creat.css">
</head>
<body>
<div class="sidebar">
        <h2>MENU</h2>
        <a href="index.php"><i class="fas fa-eye"></i> Preview</a>
        <a href="Create.php"><i class="fas fa-plus"></i> Create</a>
        <a href="Manage.php"><i class="fas fa-edit"></i> Checkin</a>
        <a href="joinstatement.php"><i class="fas fa-users"></i> Join</a>
        <a href="statistics.php"><i class="fas fa-chart-bar"></i> Statistics</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
    <div class="container mt-5">
        <h2 class="mb-4">Event Statistics</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Event Name</th>
                    <th>Total Approved</th>
                    <th>Male</th>
                    <th>Female</th>
                    <th>Checked In</th>
                    <th>Not Checked In</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['event_name']); ?></td>
                        <td><?php echo $row['total_approved']; ?></td>
                        <td><?php echo $row['male_count']; ?></td>
                        <td><?php echo $row['female_count']; ?></td>
                        <td><?php echo $row['checked_in_count']; ?></td>
                        <td><?php echo $row['not_checked_in_count']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>