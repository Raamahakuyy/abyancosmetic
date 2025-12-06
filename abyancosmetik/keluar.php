<?php
session_start();
// Hapus session member saja
unset($_SESSION['user_status']);
unset($_SESSION['user_id']);
unset($_SESSION['user_nama']);
unset($_SESSION['user_wa']);

echo "<script>alert('Anda telah logout.'); window.location='index.php';</script>";
?>