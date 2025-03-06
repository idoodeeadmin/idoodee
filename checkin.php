<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include "connect.php";
if (!isset($_GET['id']) || !isset($_GET['checkin_id'])) {
    header("Location: Manage.php"); 
    exit();
}
$eventid = $_GET['id'];
$status_id = $_GET['checkin_id'];
$sql_update = "UPDATE status SET checked_in = 1 WHERE id = ? AND checked_in = 0";
$stmt_update = $connect->prepare($sql_update);
$stmt_update->bind_param("i", $status_id);
$stmt_update->execute();
header("Location: Check.php?id=" . urlencode($eventid));
?>