<?php
session_start();

// 1. Cek apakah ID produk dikirim?
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Produk tidak valid!'); window.location='index.php';</script>";
    exit();
}

$id_produk = $_GET['id'];

// 2. Ambil Varian
// Jika user memilih varian di detail.php, ambil nilainya.
// Jika user klik beli dari index.php (tidak ada pilihan varian), set jadi 'Default'.
$varian = isset($_GET['varian']) ? $_GET['varian'] : 'Default';

// 3. Buat ID Unik untuk Keranjang (Gabungan ID + Varian)
// Contoh: Produk ID 5 warna Merah jadi "5-Merah"
// Kita hapus spasi agar lebih aman
$id_unik = $id_produk . '-' . str_replace(' ', '', $varian);

// 4. Siapkan Session Keranjang jika belum ada
if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

// 5. Masukkan ke Keranjang
if (isset($_SESSION['keranjang'][$id_unik])) {
    // Jika barang yang sama (ID & Varian sama) sudah ada, tambah jumlahnya
    $_SESSION['keranjang'][$id_unik] += 1;
} else {
    // Jika belum ada, masukkan sebagai item baru dengan jumlah 1
    $_SESSION['keranjang'][$id_unik] = 1;
}

// 6. Redirect ke Halaman Keranjang
echo "<script>window.location='keranjang.php';</script>";
?>