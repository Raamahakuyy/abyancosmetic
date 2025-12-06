<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_abyan_cosmetik"; // Pastikan nama ini sama dengan di phpMyAdmin

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Gagal koneksi: " . mysqli_connect_error());
}
?>