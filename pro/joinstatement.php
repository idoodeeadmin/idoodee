<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'connect.php';
$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Creat.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Join Statement</title>
    <style>
        .container {
            margin-left: 250px; 
            padding: 20px;
        }

        .section {
            margin-bottom: 40px;
        }

        .section h2 {
            color: #333;
            border-bottom: 2px solidrgb(191, 143, 31);
            padding-bottom: 5px;
        }

        .event-list {
            list-style: none;
            padding: 0;
        }

        .event-list li {
            background:rgb(249, 249, 249);
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }

        .no-data {
            color: #777;
            font-style: italic;
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

    <div class="container">
        <div class="section">
            <h2>Approved Events</h2>
            <ul class="event-list">
                <?php
                $sql = "SELECT events.event_name, events.location, events.date
                        FROM events
                        JOIN status ON events.eventid = status.event_id
                        WHERE status.user_id = ? AND status.status = 'approved'";
                $stmt = $connect->prepare($sql);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<li>";
                        echo "<strong>Eventname:</strong> " . htmlspecialchars($row['event_name']) . "<br>";
                        echo "<strong>Location:</strong> " . htmlspecialchars($row['location']) . "<br>";
                        echo "<strong>Date:</strong> " . htmlspecialchars($row['date']);
                        echo "</li>";
                    }
                } else {
                    echo "<li class='no-data'>No approved events found.</li>";
                }
                $stmt->close();
                ?>
            </ul>
        </div>


        <div class="section">
            <h2>Rejected Events</h2>
            <ul class="event-list">
                <?php
                $sql = "SELECT events.event_name, events.location, events.date
                        FROM events
                        JOIN status ON events.eventid = status.event_id
                        WHERE status.user_id = ? AND status.status = 'rejected'";
                $stmt = $connect->prepare($sql);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<li>";
                        echo "<strong>Eventname:</strong> " . htmlspecialchars($row['event_name']) . "<br>";
                        echo "<strong>Location:</strong> " . htmlspecialchars($row['location']) . "<br>";
                        echo "<strong>Date:</strong> " . htmlspecialchars($row['date']);
                        echo "</li>";
                    }
                } else {
                    echo "<li class='no-data'>No rejected events found.</li>";
                }
                $stmt->close();
                ?>
            </ul>
        </div>

        <div class="section">
            <h2>Pending Events</h2>
            <ul class="event-list">
                <?php
                $sql = "SELECT events.event_name, events.location, events.date
                        FROM events
                        JOIN status ON events.eventid = status.event_id
                        WHERE status.user_id = ? AND status.status = 'pending'";
                $stmt = $connect->prepare($sql);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<li>";
                        echo "<strong>Eventname:</strong> " . htmlspecialchars($row['event_name']) . "<br>";
                        echo "<strong>Location:</strong> " . htmlspecialchars($row['location']) . "<br>";
                        echo "<strong>Date:</strong> " . htmlspecialchars($row['date']);
                        echo "</li>";
                    }
                } else {
                    echo "<li class='no-data'>No pending events found.</li>";
                }
                $stmt->close();
                ?>
            </ul>
        </div>
    </div>
</body>

</html>