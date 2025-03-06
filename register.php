<?php
include 'connect.php';
if (isset($_POST["register"])) {
    $username = $_POST["username"];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $Fname = $_POST["Fname"];
    $Lname = $_POST["Lname"];
    $age = $_POST["age"];
    $gender = $_POST["gender"];
    $phone = $_POST["phone"];
    $role = 'user' ;
    $img = ''; 
    $target_dir = "uploads/";
if (isset($_FILES["image"])) {
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
      move_uploaded_file($_FILES["image"]["tmp_name"], $target_file); 
    } 
    $sql = "INSERT INTO users (username, password, first_name, last_name, age, gender, phone, role, imageprofile) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("sssssssss", $username, $password, $Fname, $Lname, $age, $gender, $phone, $role, $target_file);
    $stmt->execute();
    header('Location: login.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet"> <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <a href="#" class="logo">ProjectFinal</a>
    </nav>
    <div class="container">
        <h1>Register</h1>
        <form method="POST" action="register.php" enctype="multipart/form-data">
            <div class="input-group">
                <i class="lni lni-user"></i>
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="input-group">
                <i class="lni lni-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <div class="input-group">
                <i class="lni lni-pencil"></i>
                <input type="text" name="Fname" placeholder="First Name" >
            </div>
            <div class="input-group">
                <i class="lni lni-pencil"></i>
                <input type="text" name="Lname" placeholder="Last Name" >
            </div>
            <div class="input-group">
                <i class="lni lni-calendar"></i>
                <input type="number" name="age" placeholder="Age" min="1" >
            </div>
            <div class="input-group">
                <i class="lni lni-users"></i>
                <select name="gender" >
                    <option value="" disabled selected>Select Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="input-group">
                <i class="lni lni-phone"></i>
                <input type="text" name="phone" placeholder="Phone">
            </div>
            <div class="input-group">
                <i class="lni lni-upload"></i>
                <input type="file" name="image">
            </div>
            <button type="submit" name="register" class="btn btn-register">Register</button>
        </form>
    </div>
</body>
</html>