<?php
session_start(); 
include 'connect.php';
if (isset($_POST["login"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $sql = "SELECT id,password FROM users WHERE username = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id, $hashed_password);
    if ($stmt->fetch()) {
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            header("Location: index.php");
            exit;
        } else {
            $error = "Invalid password";
            
        }
    } else {
        $error = "Username not found";
       
    }
}

if (isset($_POST["regis"])) {
    header("Location: register.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <a href="#" class="logo">ProjectFinal</a>
    </nav>
    <div class="container">
        <h1>Login</h1>
        <form method="POST" action="login.php">
            <div class="input-group">
                <i class="lni lni-user"></i>
                <?php if(isset($error)){ echo "$error"; }?>
                <input type="text" name="username" placeholder="Username" >
            </div>
            <div class="input-group">
                <i class="lni lni-lock"></i>
                <input type="password" name="password" placeholder="Password" >
            </div>
            <button type="submit" name="login" class="btn btn-login">Login</button>
            <button type="submit" name="regis" class="btn btn-register">Register</button>
        </form>
    </div>
</body>
</html>