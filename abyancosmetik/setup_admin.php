<?php
include 'koneksi.php';

echo "<h3>Sedang membuat akun admin...</h3>";

// 1. Buat Tabel Admin
$sql = "CREATE TABLE IF NOT EXISTS admin (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(50) NOT NULL
)";

if (mysqli_query($koneksi, $sql)) {
    echo "✅ Tabel 'admin' siap.<br>";
} else {
    echo "❌ Gagal buat tabel: " . mysqli_error($koneksi);
    die;
}

// 2. Cek apakah admin sudah ada?
$cek = mysqli_query($koneksi, "SELECT * FROM admin");
if (mysqli_num_rows($cek) == 0) {
    // Kalau kosong, buat user default
    // Username: admin
    // Password: 123
    $pass = md5('123'); // Password di-enkripsi biar aman
    $insert = mysqli_query($koneksi, "INSERT INTO admin (username, password) VALUES ('admin', '$pass')");
    
    if ($insert) {
        echo "✅ User 'admin' berhasil dibuat!<br>";
        echo "Password default: <b>123</b><br>";
    }
} else {
    echo "ℹ️ Akun admin sudah ada.<br>";
}

echo "<hr>Selesai. Silakan buka <a href='login.php'>Halaman Login</a>";
?>