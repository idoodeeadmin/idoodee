<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
}
include 'connect.php';
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Creat.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Home</title>
    <style>
        .search-bar {
            width: 100%;
            padding: 20px;
            background: #f1f1f1;
            margin-bottom: 20px;
        }
        .search-form {
            display: flex;
            gap: 15px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .search-form input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            min-width: 250px; 
        }
        .search-form input[type="text"] {
            flex: 2; 
        }
        .search-form input[type="date"] {
            min-width: 200px; 
            flex: 1;
        }
        .search-form button {
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .search-form button:hover {
            background: #45a049;
        }
    </style>
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
    <div class="search-bar">
        <form class="search-form" method="POST" action="">
            <input type="text" name="event_name" placeholder="Search by Event Name" value="<?php echo isset($_POST['event_name']) ? htmlspecialchars($_POST['event_name']) : ''; ?>">
            <input type="text" name="location" placeholder="Search by Location" value="<?php echo isset($_POST['location']) ? htmlspecialchars($_POST['location']) : ''; ?>">
            <input type="date" name="date" value="<?php echo isset($_POST['date']) ? htmlspecialchars($_POST['date']) : ''; ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <?php
    $accept = "approved";
    $sql = "SELECT * FROM events";
    $params = [];
    $types = "";
    $conditions = [];
    if (isset($_POST['event_name']) && !empty($_POST['event_name'])) {
        $conditions[] = "event_name LIKE ?";
        $params[] = "%" . $_POST['event_name'] . "%";
    
    }
    if (isset($_POST['location']) && !empty($_POST['location'])) {
        $conditions[] = "location LIKE ?";
        $params[] = "%" . $_POST['location'] . "%";
     
    }
    if (isset($_POST['date']) && !empty($_POST['date'])) {
        $conditions[] = "date = ?";
        $params[] = $_POST['date'];
    
    }
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }
    $stmt = $connect->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param("s", ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $cardIndex = 0;
        while ($row = $result->fetch_assoc()) {
            $event_id = $row['eventid'];
            $userid = $_SESSION['user_id'];
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

            $sqlcount = "SELECT COUNT(*) AS total FROM status WHERE event_id = ? AND status = ?";
            $stmt_count = $connect->prepare($sqlcount);
            $stmt_count->bind_param("is", $event_id, $accept);
            $stmt_count->execute();
            $resultcount = $stmt_count->get_result();
            $rowcount = $resultcount->fetch_assoc();

            echo "<p><strong>Limit:</strong> " . htmlspecialchars($rowcount['total']) . "/" . htmlspecialchars($row['limits']) . "</p>";
            echo "<div class='event-actions'>";
            if ($userid == $row['user_id']) {
                echo "คุณนั้นเเหละคนสร้าง";
            } else {
                $sql_status = "SELECT status FROM status WHERE user_id = ? AND event_id = ?";
                $stmt_status = $connect->prepare($sql_status);
                $stmt_status->bind_param("ii", $userid, $event_id);
                $stmt_status->execute();
                $status_result = $stmt_status->get_result();
                if ($status_result->num_rows > 0) {
                    $status_row = $status_result->fetch_assoc();
                    if ($status_row['status'] == 'pending') {
                        echo "<p style='color: orange;'>ขอเข้าร่วมแล้ว</p>";
                    } elseif ($status_row['status'] == 'approved') {
                        echo "<p style='color: green;'>ได้รับการอนุมัติแล้ว</p>";
                    } elseif ($status_row['status'] == 'rejected') {
                        echo "<p style='color: red;'>ถูกปฏิเสธ</p>";
                    }
                } else {
                    $sqlcount = "SELECT COUNT(*) AS total FROM status WHERE event_id = ? AND status = ?";
                    $stmt_count = $connect->prepare($sqlcount);
                    $stmt_count->bind_param("is", $event_id, $accept);
                    $stmt_count->execute();
                    $resultcount = $stmt_count->get_result();
                    $rowcount = $resultcount->fetch_assoc();
                    if ($rowcount['total'] == $row['limits']) {
                        echo "<p style='color: ;'>เต็มเเล้ว</p>";
                    } else {
                        echo "<a href='join.php?id=" . htmlspecialchars($event_id) . "'><button class='edit-btn'>ขอเข้าร่วม</button></a>";
                    }
                }
            }
            echo "</div>";
            echo "</div>";
            $cardIndex++;
        }
    } else {
        echo "<p style='text-align: center; grid-column: 1 / -1;'>No events found.</p>";
    }
    ?>
    
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