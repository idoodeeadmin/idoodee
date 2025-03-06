<?php
session_start();
include 'connect.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
if (isset($_GET['id'])) {
    $event_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];
    $status = 'pending';
    $sql = "INSERT INTO status (user_id, event_id, status) VALUES (?, ?, ?)";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("iis", $user_id, $event_id, $status);
    $stmt->execute();
    echo "<p>Request sent successfully!</p>";
    header("Location: index.php");
}
?>