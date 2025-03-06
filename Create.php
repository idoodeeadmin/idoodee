<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include "connect.php";
$user_id = $_SESSION['user_id'];

if (isset($_POST['addEvent'])) {
    $nameEvent = $_POST["nameEvent"];
    $dateEvent = $_POST["DateEvent"];
    $locationEvent = $_POST["locationEvent"];
    $limitEvent = $_POST["limitEvent"];
    $imagePaths = [];
    if (isset($_FILES['images'])) {
        $targetDir = "uploads/";
        foreach ($_FILES['images']['name'] as $key => $fileName) {
            $targetFile = $targetDir . uniqid() . "_" . basename($fileName);
            move_uploaded_file($_FILES['images']['tmp_name'][$key], $targetFile);
            $imagePaths[] = $targetFile;
        }
    }
    
    if (!empty($imagePaths)) {
        $imageEvent = implode(",", $imagePaths);
    }
    $sqlevent = "INSERT INTO events(user_id, event_name, date, location, limits, image) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $connect->prepare($sqlevent);
    $stmt->bind_param("isssis", $user_id, $nameEvent, $dateEvent, $locationEvent, $limitEvent, $imageEvent);
    $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="Creat.css">
    <title>Event Manager</title>
</head>

<body>
<div class="sidebar">
            <h2>Event Manager</h2>
            <a href="index.php"><i class="fas fa-eye"></i> Preview</a>
            <a href="create.php"><i class="fas fa-plus"></i> Create</a>
            <a href="manage.php"><i class="fas fa-edit"></i> Checkin</a>
            <a href="joinstatement.php"><i class="fas fa-users"></i> Join</a>
            <a href="statistics.php"><i class="fas fa-chart-bar"></i> Statistics</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

    <div class="profile-container">
    <?php 
    $sqlimg = "SELECT imageprofile,username FROM users WHERE id = ?";
    $stmt = $connect->prepare($sqlimg);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $profileImage = htmlspecialchars($row['imageprofile']);
        $user = htmlspecialchars($row['username']);
        echo "<p>$user<p>";
        echo "<img src='$profileImage' alt='Profile Picture' class='profile-pic'>";
    } 
    ?>
</div>
    </div>
    <div class="container">
        <h1>Create New Event</h1>
        <div class="form-container">
            <form method="POST" action="Create.php" enctype="multipart/form-data">
                <input type="text" name="nameEvent" placeholder="Event Name" required>
                <input type="date" name="DateEvent" required>
                <input type="text" name="locationEvent" placeholder="Location">
                <input type="number" name="limitEvent" placeholder="Participant Limit" min="1">
                <input type="file" name="images[]" multiple accept="image/*">
                <button type="submit" name="addEvent">Add Event</button>
            </form>
        </div>

        <h2>Your Events</h2>
        <div class="events-grid">
            <?php
            $sql = "SELECT * FROM events WHERE user_id = ?";
            $stmt = $connect->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $cardIndex = 0;
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='event-card'>";
                    $images = explode(",", $row['image']);

                    echo "<div class='image-slider'>";
                    echo "<div class='image-container' id='image-container-{$cardIndex}'>";
                    foreach ($images as $image) {
                        if (!empty($image)) {
                            echo "<img src='" . htmlspecialchars($image) . "' alt='Event Image'>";
                        }
                    }
                    echo "</div>";
                    if (count(array_filter($images)) > 1) { 
                        echo "<button class='nav-btn prev-btn' onclick='scrollImages({$cardIndex}, -1)'><</button>";
                        echo "<button class='nav-btn next-btn' onclick='scrollImages({$cardIndex}, 1)'>></button>";
                    }

                    echo "</div>";
                    echo "<h3>" . htmlspecialchars($row['event_name']) . "</h3>";
                    echo "<p><strong>Date:</strong> " . htmlspecialchars($row['date']) . "</p>";
                    echo "<p><strong>Location:</strong> " . htmlspecialchars($row['location']) . "</p>";
                    echo "<p><strong>Limit:</strong> " . htmlspecialchars($row['limits']) . "</p>";
                    echo "<div class='event-actions'>";
                    echo "<a href='edit.php?id=" . $row['eventid'] .$row['image']. "'><button class='edit-btn'>Edit</button></a>";
                    echo "<a href='delete.php?id=" . $row['eventid'] . "' onclick='return confirm(\"Are you sure you want to delete this event?\");'><button class='delete-btn'>Delete</button></a>";
                    echo "</div>";
                    echo "</div>";
                    $cardIndex++;
                }
            } else {
                echo "<p style='text-align: center; grid-column: 1 / -1;'>No events found.</p>";
            }
            ?>
        </div>
    </div>




    <script>
        function scrollImages(cardIndex, direction) {
            const container = document.getElementById(`image-container-${cardIndex}`);
            const images = container.getElementsByTagName('img');
            const slider = container.parentElement; 
            const sliderWidth = slider.offsetWidth;
            const imageWidth = images[0].offsetWidth + 10; 
            const totalWidth = images.length * imageWidth; 
            let currentPosition = parseFloat(container.style.transform?.replace('translateX(', '').replace('px)', '') || 0);
            const step = imageWidth * direction; 
            currentPosition -= step; 
            const maxScroll = Math.min(0, -(totalWidth - sliderWidth)); 
            if (currentPosition > 0) {
                currentPosition = 0; 
            }
            if (currentPosition < maxScroll) {
                currentPosition = maxScroll; 
            }
            container.style.transform = `translateX(${currentPosition}px)`;
        }
    </script>
</body>
</html>