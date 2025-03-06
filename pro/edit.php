<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include "connect.php";
$user_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    header("Location: Create.php?error=Invalid event ID");
    exit();
}

$eventid = $_GET['id'];
$sql = "SELECT * FROM events WHERE eventid = ? AND user_id = ?";
$stmt = $connect->prepare($sql);
$stmt->bind_param("ii", $eventid, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

if (!$event) {
    header("Location: Create.php?error=Event not found");
    exit();
}

if (isset($_POST['updateEvent'])) {
    $nameEvent = $_POST["nameEvent"];
    $dateEvent = $_POST["DateEvent"];
    $locationEvent = $_POST["locationEvent"];
    $limitEvent = $_POST["limitEvent"];
    $imageEvent = $event['image']; 

    if (isset($_FILES['images'])) {
        $targetDir = "uploads/";
        $imagePaths = [];
        foreach ($_FILES['images']['name'] as $key => $fileName) {
            if ($_FILES['images']['size'][$key] > 0) { 
                $targetFile = $targetDir . uniqid() . "_" . basename($fileName);
                if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $targetFile)) {
                    $imagePaths[] = $targetFile;
                }
            }
        }
        if (!empty($imagePaths)) {
            $imageEvent = implode(",", $imagePaths); 
        }
    }

    $sqlevent = "UPDATE events SET user_id = ?, event_name = ?, date = ?, location = ?, limits = ?, image = ? WHERE eventid = ?";
    $update_stmt = $connect->prepare($sqlevent);
    $update_stmt->bind_param("isssisi", $user_id, $nameEvent, $dateEvent, $locationEvent, $limitEvent, $imageEvent, $eventid);
    $update_stmt->execute();
    header("Location: Create.php?message=Event updated successfully");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="styleedit.css">
    <title>Edit Event</title>
</head>
<body>
<div class="sidebar">
    <h2>Event Manager</h2>
    <a href="preview.php"><i class="fas fa-eye"></i> Preview</a>
    <a href="create.php"><i class="fas fa-plus"></i> Create</a>
    <a href="manage.php"><i class="fas fa-edit"></i> Checkin</a>
    <a href="joinstatement.php"><i class="fas fa-users"></i> Join</a>
    <a href="statistics.php"><i class="fas fa-chart-bar"></i> Statistics</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>
<div class="container">
    <h1>Edit Event</h1>
    <div class="form-container">
        <form method="POST" action="edit.php?id=<?= $eventid ?>" enctype="multipart/form-data">
            <input type="text" name="nameEvent" placeholder="Event Name" value="<?= htmlspecialchars($event['event_name']) ?>" required>
            <input type="date" name="DateEvent" value="<?= htmlspecialchars($event['date']) ?>" required>
            <input type="text" name="locationEvent" placeholder="Location" value="<?= htmlspecialchars($event['location']) ?>">
            <input type="number" name="limitEvent" placeholder="Participant Limit" value="<?= htmlspecialchars($event['limits']) ?>" min="1">
            <input type="file" name="images[]" multiple accept="image/*">
            <button type="submit" name="updateEvent">Update Event</button>
        </form>
    </div>
</div>
</body>
</html>