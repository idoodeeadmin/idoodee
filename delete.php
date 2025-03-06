<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include "connect.php";
$user_id = $_SESSION['user_id'];
if (isset($_GET['id'])) {
    $event_id = $_GET['id'];
    $delete_sql = "DELETE FROM events WHERE eventid = ? AND user_id = ?";
    $delete_stmt = $connect->prepare($delete_sql);
    $delete_stmt->bind_param("ii", $event_id, $user_id);
    $delete_stmt->execute();
 header("Location: Create.php?message=Event deleted successfully");      
    }
?>