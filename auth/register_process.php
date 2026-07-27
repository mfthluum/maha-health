<?php

session_start();

require "../config/database.php";

// Ambil Data
$fullname = trim($_POST['fullname']);
$email = trim($_POST['email']);
$password = $_POST['password'];
$confirm = $_POST['confirm_password'];

$height = $_POST['height'];
$weight = $_POST['weight'];
$gender = $_POST['gender'];
$birth_date = $_POST['birth_date'];

// Validasi Password
if ($password != $confirm) {

    echo "<script>
        alert('Konfirmasi password tidak sesuai!');
        window.location='register.php';
    </script>";

    exit();
}

// Cek Email
$cek = mysqli_query($conn,"SELECT * FROM users WHERE email='$email'");

if(mysqli_num_rows($cek) > 0){

    echo "<script>
        alert('Email sudah digunakan!');
        window.location='register.php';
    </script>";

    exit();
}

// Enkripsi Password
$password = password_hash($password, PASSWORD_DEFAULT);

// Simpan
$query = mysqli_query($conn,"INSERT INTO users(

fullname,
email,
password,
gender,
birth_date,
height,
weight

)

VALUES(

'$fullname',
'$email',
'$password',
'$gender',
'$birth_date',
'$height',
'$weight'

)");

if($query){

    echo "<script>

    alert('Registrasi berhasil!');

    window.location='login.php';

    </script>";

}else{

    echo "<script>

    alert('Registrasi gagal!');

    window.location='register.php';

    </script>";

}

?>