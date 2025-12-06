<?php
session_start();

// Ambil ID produk dan aksi (tambah/kurang) dari URL
$id_produk = $_GET['id'];
$aksi = $_GET['aksi'];

// Pastikan produk ada di keranjang
if (isset($_SESSION['keranjang'][$id_produk])) {
    if ($aksi == 'tambah') {
        $_SESSION['keranjang'][$id_produk] += 1;
    } elseif ($aksi == 'kurang') {
        $_SESSION['keranjang'][$id_produk] -= 1;
        
        // Jika jumlah jadi 0, hapus barang dari keranjang
        if ($_SESSION['keranjang'][$id_produk] <= 0) {
            unset($_SESSION['keranjang'][$id_produk]);
        }
    }
}

// Redirect kembali ke halaman keranjang
header("location:keranjang.php");
?>