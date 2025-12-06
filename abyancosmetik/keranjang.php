<?php
session_start();
include 'koneksi.php';

// Ambil Profil Toko
$query_profil = mysqli_query($koneksi, "SELECT * FROM profil_toko WHERE id=1");
$toko = mysqli_fetch_array($query_profil);

// Jika keranjang kosong
if (empty($_SESSION['keranjang']) || !isset($_SESSION['keranjang'])) {
    echo "<script>alert('Keranjang kosong, yuk belanja dulu!'); window.location='katalog.php';</script>"; exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keranjang Belanja - <?php echo $toko['nama_toko']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap'); body { font-family: 'Outfit', sans-serif; background-color: #FFF5F7; }</style>
</head>
<body class="text-gray-800">

    <nav class="bg-white/90 backdrop-blur-md sticky top-0 z-50 border-b border-white/50 shadow-sm">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="index.php" class="font-bold text-xl tracking-tight text-gray-900 uppercase flex items-center gap-2 group">
                <i class="fas fa-arrow-left text-gray-400 group-hover:text-pink-600 text-lg mr-2 transition"></i> Lanjut Belanja
            </a>
            <h1 class="text-lg font-bold text-gray-800">Keranjang Saya</h1>
        </div>
    </nav>

    <div class="container mx-auto px-6 py-10">
        <div class="flex flex-col lg:flex-row gap-8">
            
            <!-- DAFTAR PRODUK -->
            <div class="lg:w-2/3">
                <div class="bg-white rounded-[2rem] shadow-xl shadow-pink-100/50 border border-white p-6 md:p-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <i class="fas fa-shopping-bag text-pink-500"></i> Daftar Item
                    </h2>
                    
                    <div class="space-y-6">
                        <?php
                        $total_belanja = 0;
                        foreach ($_SESSION['keranjang'] as $id_unik => $jumlah) {
                            $pecah_id = explode('-', $id_unik);
                            $id_produk = $pecah_id[0];
                            $nama_varian = isset($pecah_id[1]) ? $pecah_id[1] : '';

                            $ambil = mysqli_query($koneksi, "SELECT * FROM produk WHERE id='$id_produk'");
                            $pecah = mysqli_fetch_array($ambil);
                            $subharga = $pecah['harga'] * $jumlah;
                            $total_belanja += $subharga;
                        ?>
                        <div class="flex flex-col md:flex-row items-center gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-100 hover:shadow-md transition">
                            <!-- Gambar -->
                            <img src="img/<?php echo $pecah['gambar']; ?>" class="w-20 h-20 object-cover rounded-xl bg-white shadow-sm">
                            
                            <!-- Info Produk -->
                            <div class="flex-1 text-center md:text-left w-full">
                                <h4 class="font-bold text-gray-900 text-sm md:text-base"><?php echo $pecah['nama_produk']; ?></h4>
                                <?php if($nama_varian && $nama_varian != '-') echo "<span class='text-xs bg-pink-100 text-pink-600 px-2 py-0.5 rounded font-bold uppercase inline-block mt-1'>$nama_varian</span>"; ?>
                                <p class="text-gray-500 text-xs mt-1">Harga Satuan: Rp <?php echo number_format($pecah['harga']); ?></p>
                            </div>

                            <!-- Kontrol Jumlah (+/-) -->
                            <div class="flex items-center bg-white border border-gray-200 rounded-lg shadow-sm">
                                <a href="update_keranjang.php?id=<?php echo $id_unik; ?>&aksi=kurang" class="px-3 py-1 text-gray-500 hover:text-pink-600 hover:bg-pink-50 rounded-l-lg transition font-bold">-</a>
                                <input type="text" value="<?php echo $jumlah; ?>" class="w-10 text-center text-sm font-bold border-x border-gray-200 py-1 bg-transparent" readonly>
                                <a href="update_keranjang.php?id=<?php echo $id_unik; ?>&aksi=tambah" class="px-3 py-1 text-gray-500 hover:text-pink-600 hover:bg-pink-50 rounded-r-lg transition font-bold">+</a>
                            </div>

                            <!-- Subtotal & Hapus -->
                            <div class="text-right w-full md:w-auto flex justify-between md:block items-center">
                                <span class="md:hidden text-sm font-bold text-gray-500">Subtotal:</span>
                                <div>
                                    <p class="font-bold text-pink-600 mb-1">Rp <?php echo number_format($subharga); ?></p>
                                    <a href="hapus_keranjang.php?id=<?php echo $id_unik; ?>" class="text-xs text-gray-400 hover:text-red-500 transition flex items-center justify-end gap-1" onclick="return confirm('Hapus barang ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <!-- RINGKASAN HARGA -->
            <div class="lg:w-1/3">
                <div class="bg-white rounded-[2rem] shadow-xl shadow-pink-100/50 border border-white p-8 sticky top-24">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Ringkasan</h2>
                    
                    <div class="flex justify-between items-center mb-4 text-sm text-gray-600">
                        <span>Total Item</span>
                        <span><?php echo count($_SESSION['keranjang']); ?> Jenis Barang</span>
                    </div>
                    <div class="flex justify-between items-center mb-6 pb-6 border-b border-gray-100">
                        <span>Subtotal</span>
                        <span class="font-bold text-gray-900">Rp <?php echo number_format($total_belanja); ?></span>
                    </div>
                    
                    <div class="flex justify-between items-center mb-8">
                        <span class="font-bold text-lg text-gray-900">Total Bayar</span>
                        <span class="font-bold text-2xl text-pink-600">Rp <?php echo number_format($total_belanja); ?></span>
                    </div>

                    <a href="checkout.php" class="block w-full bg-gray-900 hover:bg-pink-600 text-white font-bold py-4 rounded-2xl text-center transition shadow-lg shadow-gray-200 hover:shadow-pink-200 transform hover:-translate-y-1">
                        Checkout Sekarang <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                    
                    <a href="katalog.php" class="block w-full text-center text-gray-400 text-sm mt-4 hover:text-gray-600">Tambah Barang Lain</a>
                </div>
            </div>

        </div>
    </div>

</body>
</html>