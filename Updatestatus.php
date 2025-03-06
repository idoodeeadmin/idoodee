<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include "connect.php";

if (isset($_GET['status_id']) && isset($_GET['action']) && isset($_GET['event_id'])) {
    $statusid = $_GET['status_id'];
    $action = $_GET['action'];
    $eventid = $_GET['event_id'];
    $status = ($action === 'approve') ? 'approved' : 'rejected';

    $sql = "UPDATE status SET status = ? WHERE id = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("si", $status, $statusid);

    if ($stmt->execute()) {
        header("Location: Check.php?id=" . $eventid);
    } else {
        echo "Error updating status: " . $stmt->error;
    }

    $stmt->close();
    exit();
} else {
    echo "Invalid request.";
    exit();
}
?>