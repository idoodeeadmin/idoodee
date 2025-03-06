<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include "connect.php";
if (isset($_GET['id'])) {
    $userid = $_SESSION['user_id'];
    $eventid = $_GET['id'];
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="Creat.css">
    <title>Check Event Requests</title>
    <style>
        .request-section {
            margin-top: 30px;
        }

        .request-list {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .request-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .request-item:last-child {
            border-bottom: none;
        }

        .request-info {
            flex-grow: 1;
        }

        .request-actions button {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 10px;
            transition: background 0.3s;
        }

        .accept-btn {
            background-color: #27ae60;
            color: white;
        }

        .accept-btn:hover {
            background-color: #219653;
        }

        .reject-btn {
            background-color: #c0392b;
            color: white;
        }

        .reject-btn:hover {
            background-color: #992d22;
        }

        .no-data {
            text-align: center;
            color: #777;
            margin: 20px 0;
        }
        .checked-in-btn {
            background-color: #27ae60;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: default;
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

    <div class="content">
        <?php
        $sql = "SELECT * FROM events WHERE eventid = ?";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("i", $eventid);
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
                echo "</div>";
                $cardIndex++;
            }
        } else {
            echo "<p class='no-data'>No events found.</p>";
        }
        $stmt->close();
        ?>
       <div class="request-section">

            <h2>Check-in</h2> 
            <div class="request-list">
                <?php
                $accept = 'approved';
                $sqlcheckin = "SELECT users.first_name, users.last_name, users.age, users.phone, status.id AS status_id, status.checked_in, events.limits
                FROM users
                JOIN status ON users.id = status.user_id
                JOIN events ON status.event_id = events.eventid
                WHERE status.event_id = ? AND status.status = ?";
                $stmtcheckin = $connect->prepare($sqlcheckin);
                $stmtcheckin->bind_param("is", $eventid, $accept);
                $stmtcheckin->execute();
                $resultcheckin = $stmtcheckin->get_result();
                
                if ($resultcheckin->num_rows > 0) {
                    while ($rowcheckin = $resultcheckin->fetch_assoc()) {
                        echo "<div class='request-item'>";
                        echo "<div class='request-info'>";
                        echo "<strong>" . htmlspecialchars($rowcheckin['first_name']) . " " . htmlspecialchars($rowcheckin['last_name']) . "</strong><br>";
                        echo "Age: " . htmlspecialchars($rowcheckin['age']) . "<br>";
                        echo "Phone: " . htmlspecialchars($rowcheckin['phone']);
                        echo "</div>";
                        echo "<div class='request-actions'>";
                        if ($rowcheckin['checked_in'] == 1) {
                            echo "<button class='checked-in-btn'>✓</button>";
                        } else {
                            echo "<button class='accept-btn' onclick=\"location.href='checkin.php?id=" . htmlspecialchars($eventid) . "&checkin_id=" . htmlspecialchars($rowcheckin['status_id']) . "'\">Check In</button>";
                        }
                        echo "</div>";
                        echo "</div>";
                    }
                } else {
                    echo "<p class='no-data'>No checked-in participants found.</p>";
                }
                $stmtcheckin->close();
                ?>
            </div>
        </div>

        <div class="request-section">
            <h2>Pending Requests</h2>
            <div class="request-list">
                <?php
                $pending = 'pending';
                $sqlcheck = "SELECT users.first_name, users.last_name, users.age, users.phone, status.id AS status_id, events.limits
                    FROM users
                    JOIN status ON users.id = status.user_id
                    JOIN events ON status.event_id = events.eventid
                    WHERE status.event_id = ? AND status.status = ?";
                $stmt = $connect->prepare($sqlcheck);
                $stmt->bind_param("is", $eventid, $pending);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<div class='request-item'>";
                        echo "<div class='request-info'>";
                        echo "<strong>" . htmlspecialchars($row['first_name']) . " " . htmlspecialchars($row['last_name']) . "</strong><br>";
                        echo "Age: " . htmlspecialchars($row['age']) . "<br>";
                        echo "Phone: " . htmlspecialchars($row['phone']);
                        echo "</div>";
                        echo "<div class='request-actions'>";
                        
                        $sqlcount = "SELECT COUNT(*) AS total FROM status WHERE event_id = ? AND status = ?";
                        $stmt_count = $connect->prepare($sqlcount);
                        $stmt_count->bind_param("is", $eventid, $accept);
                        $stmt_count->execute();
                        $resultcount = $stmt_count->get_result();
                        $rowcount = $resultcount->fetch_assoc();
                        
                        if ($rowcount['total'] >= $row['limits']) {
                            echo "<button class='accept-btn' disabled>Event Full</button>";
                        } else {
                            echo "<button class='accept-btn' onclick=\"location.href='Updatestatus.php?status_id=" . htmlspecialchars($row['status_id']) . "&action=approve&event_id=" . htmlspecialchars($eventid) . "'\">Accept</button>";
                        }
                        echo "<button class='reject-btn' onclick=\"location.href='Updatestatus.php?status_id=" . htmlspecialchars($row['status_id']) . "&action=reject&event_id=" . htmlspecialchars($eventid) . "'\">Reject</button>";
                        echo "</div>";
                        echo "</div>";
                        $stmt_count->close();
                    }
                } else {
                    echo "<p class='no-data'>No pending requests found.</p>";
                }

                ?>
            </div>
        </div>
    </div>










    <script>
        function scrollImages(cardIndex, direction) {
            const container = document.getElementById(`image-container-${cardIndex}`);
            const images = container.getElementsByTagName('img');
            const slider = container.parentElement;
            const sliderWidth = slider.offsetWidth;
            const imageWidth = images[0] ? images[0].offsetWidth + 10 : 0;
            const totalWidth = images.length * imageWidth;
            let currentPosition = parseFloat(container.style.transform?.replace('translateX(', '').replace('px)', '') || 0);
            const step = imageWidth * direction;
            currentPosition -= step;
            const maxScroll = Math.min(0, -(totalWidth - sliderWidth));
            if (currentPosition > 0) currentPosition = 0;
            if (currentPosition < maxScroll) currentPosition = maxScroll;
            container.style.transform = `translateX(${currentPosition}px)`;
        }
    </script>
</body>

</html>