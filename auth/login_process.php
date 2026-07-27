<?php

session_start();

require "../config/database.php";

$email = trim($_POST['email']);

$password = $_POST['password'];

// Cari User

$query = mysqli_query($conn,"SELECT * FROM users WHERE email='$email'");

if(mysqli_num_rows($query) == 1){

    $user = mysqli_fetch_assoc($query);

    if(password_verify($password,$user['password'])){

        $_SESSION['user'] = [

            "id"=>$user['id'],

            "fullname"=>$user['fullname'],

            "email"=>$user['email']

        ];

        header("Location: ../dashboard/index.php");

        exit();

    }

}

echo "<script>

alert('Email atau Password salah!');

window.location='login.php';

</script>";

?>