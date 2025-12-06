<?php
session_start();
include 'koneksi.php';

// --- 1. LOGIKA KIRIM ULASAN ---
if (isset($_POST['kirim_ulasan'])) {
    // Cek Login
    if (!isset($_SESSION['user_status']) || $_SESSION['user_status'] != 'login_member') {
        echo "<script>alert('Silakan login terlebih dahulu untuk menulis ulasan.'); window.location='masuk.php';</script>";
        exit();
    }

    $id_prod = $_POST['id_produk'];
    $nama = $_SESSION['user_nama']; 
    $rating = $_POST['rating'];
    $komen = mysqli_real_escape_string($koneksi, $_POST['isi_ulasan']);
    
    $simpan = mysqli_query($koneksi, "INSERT INTO ulasan (id_produk, nama_user, bintang, isi_ulasan) VALUES ('$id_prod', '$nama', '$rating', '$komen')");
    
    if($simpan){
        echo "<script>alert('Terima kasih! Ulasan berhasil dikirim.'); window.location='detail.php?id=$id_prod';</script>";
    }
}

// --- 2. AMBIL DATA PRODUK UTAMA ---
if (!isset($_GET['id'])) { header("location:index.php"); exit(); }
$id_produk = $_GET['id'];

// Data Toko
$toko = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM profil_toko WHERE id=1"));

// Data Produk
$query = mysqli_query($koneksi, "SELECT * FROM produk WHERE id = '$id_produk'");
$data  = mysqli_fetch_array($query);

if (!$data) { echo "<script>alert('Produk tidak ditemukan!'); window.location='index.php';</script>"; exit(); }

// --- 3. AMBIL DATA PENDUKUNG ---
// Foto Galeri
$q_foto = mysqli_query($koneksi, "SELECT * FROM produk_foto WHERE id_produk='$id_produk'");
$foto_galeri = [];
while ($f = mysqli_fetch_array($q_foto)) { $foto_galeri[] = $f['nama_foto']; }

// Ulasan
$q_ulasan = mysqli_query($koneksi, "SELECT * FROM ulasan WHERE id_produk='$id_produk' ORDER BY id DESC");
$jml_ulasan = mysqli_num_rows($q_ulasan);

// Varian (Pisahkan string koma jadi array)
$varian_arr = [];
if (!empty($data['varian'])) {
    $varian_arr = explode(",", $data['varian']);
}

// Hitung Diskon
$habis = ($data['stok'] == 0);
$ada_diskon = ($data['harga_coret'] > $data['harga']);
$persen = 0;
if($ada_diskon) $persen = round((($data['harga_coret']-$data['harga'])/$data['harga_coret'])*100);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['nama_produk']; ?> - <?php echo $toko['nama_toko']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap'); 
        body { font-family: 'Outfit', sans-serif; background-color: #FFF5F7; }
        
        /* Sembunyikan Scrollbar di Galeri Thumbnail */
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        
        /* Custom Scrollbar untuk Ulasan */
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #ec4899; border-radius: 10px; }
    </style>
</head>
<body class="text-gray-800">

    <!-- NAVBAR -->
    <nav class="bg-white/90 backdrop-blur-md sticky top-0 z-50 border-b border-white/50 shadow-sm">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="index.php" class="font-bold text-xl tracking-tight text-gray-900 uppercase flex items-center gap-2 group">
                <i class="fas fa-arrow-left text-gray-400 group-hover:text-pink-600 text-lg mr-2 transition"></i> <?php echo $toko['nama_toko']; ?>
            </a>
            
            <div class="flex items-center gap-4">
                <a href="katalog.php" class="text-gray-600 hover:text-pink-600 font-bold hidden md:block text-sm">Katalog</a>
                
                <!-- Icon Keranjang -->
                <a href="keranjang.php" class="relative text-gray-500 hover:text-pink-600 transition">
                    <i class="fas fa-shopping-bag text-xl"></i>
                    <?php if(isset($_SESSION['keranjang']) && count($_SESSION['keranjang']) > 0) { ?>
                        <span class="absolute -top-1 -right-1 bg-rose-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow-sm">
                            <?php echo count($_SESSION['keranjang']); ?>
                        </span>
                    <?php } ?>
                </a>

                <!-- Login/Member -->
                <?php if(isset($_SESSION['user_status']) && $_SESSION['user_status'] == 'login_member'){
                    echo '<a href="akun.php" class="text-sm font-bold text-gray-700 hover:text-pink-600 flex items-center bg-pink-50 px-3 py-1.5 rounded-full transition"><i class="fas fa-user-circle mr-2 text-lg text-pink-500"></i> '.$_SESSION['user_nama'].'</a>';
                } else {
                    echo '<a href="masuk.php" class="text-gray-500 hover:text-pink-600 text-sm font-bold">Masuk</a>';
                } ?>
            </div>
        </div>
    </nav>

    <!-- BREADCRUMB -->
    <div class="bg-white border-b border-gray-100 py-3">
        <div class="container mx-auto px-6 text-xs md:text-sm text-gray-500">
            <a href="index.php" class="hover:text-pink-600">Home</a> <span class="mx-2">/</span> 
            <a href="katalog.php" class="hover:text-pink-600">Katalog</a> <span class="mx-2">/</span>
            <span class="text-pink-500 font-bold"><?php echo substr($data['nama_produk'], 0, 40); ?>...</span>
        </div>
    </div>

    <!-- KONTEN UTAMA -->
    <div class="container mx-auto px-6 py-10">
        <div class="bg-white rounded-[2.5rem] shadow-xl shadow-pink-100/50 overflow-hidden border border-white">
            <div class="md:flex">
                
                <!-- BAGIAN KIRI: FOTO & GALERI -->
                <div class="md:w-1/2 p-8 md:p-10 bg-gray-50 relative">
                    <!-- Foto Utama (Klik untuk Zoom) -->
                    <div class="mb-6 relative rounded-3xl overflow-hidden border border-white shadow-sm group cursor-zoom-in bg-white" onclick="openFullscreen(document.getElementById('mainImage').src)">
                        <img id="mainImage" src="img/<?php echo $data['gambar']; ?>" 
                             onerror="this.src='https://via.placeholder.com/500?text=No+Image'"
                             class="w-full h-[400px] md:h-[500px] object-cover transition duration-700 group-hover:scale-105">
                        
                        <div class="absolute inset-0 bg-black/10 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                            <span class="bg-white/80 backdrop-blur text-gray-800 px-4 py-2 rounded-full text-sm font-bold shadow-lg"><i class="fas fa-search-plus mr-2"></i> Perbesar</span>
                        </div>

                        <?php if($ada_diskon) echo "<span class='absolute top-4 left-4 bg-rose-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg z-10'>Hemat $persen%</span>"; ?>
                        <?php if($data['label_sticker']) echo "<span class='absolute top-4 right-4 bg-yellow-400 text-yellow-900 text-xs font-bold px-3 py-1.5 rounded-full shadow-lg uppercase tracking-wide'>".$data['label_sticker']."</span>"; ?>
                    </div>
                    
                    <!-- Thumbnail Gallery -->
                    <div class="flex gap-3 overflow-x-auto pb-2 scrollbar-hide">
                        <!-- Foto Default -->
                        <img onclick="changeImage('img/<?php echo $data['gambar']; ?>')" src="img/<?php echo $data['gambar']; ?>" class="w-20 h-20 object-cover rounded-2xl border-2 border-white shadow-sm hover:border-pink-400 cursor-pointer transition hover:scale-105">
                        <!-- Foto Tambahan -->
                        <?php foreach($foto_galeri as $foto) { ?>
                            <img onclick="changeImage('img/<?php echo $foto; ?>')" src="img/<?php echo $foto; ?>" class="w-20 h-20 object-cover rounded-2xl border-2 border-white shadow-sm hover:border-pink-400 cursor-pointer transition hover:scale-105">
                        <?php } ?>
                    </div>
                </div>

                <!-- BAGIAN KANAN: INFO PRODUK -->
                <div class="md:w-1/2 p-8 md:p-12 flex flex-col">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-xs font-bold text-pink-500 uppercase tracking-widest bg-pink-50 px-3 py-1 rounded-lg"><?php echo $data['kategori']; ?></span>
                        <?php if($habis) { ?>
                            <span class="bg-gray-200 text-gray-500 px-3 py-1 rounded-lg text-xs font-bold uppercase">Stok Habis</span>
                        <?php } else { ?>
                            <span class="bg-green-50 text-green-600 px-3 py-1 rounded-lg text-xs font-bold uppercase">Stok Tersedia: <?php echo $data['stok']; ?></span>
                        <?php } ?>
                    </div>

                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4 leading-tight"><?php echo $data['nama_produk']; ?></h1>
                    
                    <!-- Rating & Harga -->
                    <div class="flex items-center gap-4 mb-6 border-b border-gray-100 pb-6">
                        <div class="flex items-center gap-2">
                            <div class="text-yellow-400 text-sm flex"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                            <span class="text-gray-400 text-sm">(<?php echo $jml_ulasan; ?> Ulasan)</span>
                        </div>
                        <div class="w-px h-6 bg-gray-200"></div>
                        <div class="flex items-end gap-2">
                            <?php if($ada_diskon) { ?>
                                <span class="text-gray-400 line-through text-sm mb-1">Rp <?php echo number_format($data['harga_coret']); ?></span>
                            <?php } ?>
                            <p class="text-3xl font-bold text-pink-600">Rp <?php echo number_format($data['harga']); ?></p>
                        </div>
                    </div>

                    <!-- FORM KERANJANG -->
                    <form action="tambah_keranjang.php" method="GET">
                        <input type="hidden" name="id" value="<?php echo $data['id']; ?>">
                        
                        <!-- PILIH VARIAN -->
                        <?php if(!empty($varian_arr)) { ?>
                        <div class="mb-8">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-3">Pilih Varian</label>
                            <div class="flex flex-wrap gap-3">
                                <?php foreach($varian_arr as $index => $v) { $v = trim($v); ?>
                                <label class="cursor-pointer">
                                    <input type="radio" name="varian" value="<?php echo $v; ?>" class="peer sr-only" <?php if($index==0) echo 'checked'; ?> required>
                                    <span class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-medium peer-checked:bg-gray-900 peer-checked:text-white peer-checked:border-gray-900 hover:border-gray-400 transition select-none">
                                        <?php echo $v; ?>
                                    </span>
                                </label>
                                <?php } ?>
                            </div>
                        </div>
                        <?php } ?>

                        <div class="mb-8">
                            <h3 class="block text-xs font-bold text-gray-400 uppercase mb-3">Deskripsi Produk</h3>
                            <div class="text-gray-600 text-sm leading-relaxed space-y-2">
                                <?php echo nl2br($data['deskripsi']); ?>
                            </div>
                        </div>

                        <div class="flex gap-4 mt-auto">
                            <?php if(!$habis) { ?>
                                <button type="submit" class="flex-1 bg-pink-600 hover:bg-pink-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-pink-200 transition transform hover:-translate-y-1 flex justify-center items-center gap-3 text-sm md:text-base">
                                    <i class="fas fa-shopping-bag"></i> Masukkan Keranjang
                                </button>
                            <?php } else { ?>
                                <button type="button" disabled class="flex-1 bg-gray-200 text-gray-400 font-bold py-4 rounded-2xl cursor-not-allowed text-sm md:text-base">
                                    Stok Habis
                                </button>
                            <?php } ?>
                            
                            <a href="https://wa.me/<?php echo $toko['no_wa']; ?>?text=Halo Admin, saya mau tanya detail produk *<?php echo $data['nama_produk']; ?>*" target="_blank" class="px-6 py-4 bg-white border-2 border-gray-100 text-gray-400 font-bold rounded-2xl hover:border-green-500 hover:text-green-500 transition shadow-sm" title="Chat Admin">
                                <i class="fab fa-whatsapp text-2xl"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- GRID BAWAH: ULASAN & PRODUK SERUPA -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-12">
            
            <!-- KOLOM KIRI: ULASAN -->
            <div class="lg:col-span-2 bg-white p-8 rounded-[2.5rem] shadow-sm border border-white">
                <h2 class="text-2xl font-bold text-gray-900 mb-8 flex items-center gap-2">
                    <i class="fas fa-comment-dots text-pink-500"></i> Ulasan Pembeli
                </h2>
                
                <div class="space-y-6 mb-10 max-h-[500px] overflow-y-auto pr-2 custom-scrollbar">
                    <?php if($jml_ulasan > 0) { 
                        while($rev = mysqli_fetch_array($q_ulasan)) {
                    ?>
                    <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-white shadow-sm flex items-center justify-center text-pink-600 font-bold text-sm">
                                    <?php echo substr($rev['nama_user'], 0, 1); ?>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900 text-sm"><?php echo $rev['nama_user']; ?></p>
                                    <div class="text-yellow-400 text-xs flex"><?php for($k=0; $k<$rev['bintang']; $k++) echo '<i class="fas fa-star"></i>'; ?></div>
                                </div>
                            </div>
                            <span class="text-xs text-gray-400 bg-white px-2 py-1 rounded-lg"><?php echo date('d M Y', strtotime($rev['tanggal'])); ?></span>
                        </div>
                        <p class="text-gray-600 text-sm leading-relaxed">"<?php echo $rev['isi_ulasan']; ?>"</p>
                    </div>
                    <?php } } else { echo "<div class='text-center py-12 bg-gray-50 rounded-2xl text-gray-400 italic'>Belum ada ulasan. Jadilah yang pertama!</div>"; } ?>
                </div>

                <!-- FORM TULIS ULASAN -->
                <?php if(isset($_SESSION['user_status']) && $_SESSION['user_status'] == 'login_member') { ?>
                <div class="bg-pink-50/50 p-6 rounded-2xl border border-pink-100">
                    <h3 class="font-bold text-gray-800 mb-4">Tulis Pengalamanmu</h3>
                    <form action="" method="POST">
                        <input type="hidden" name="id_produk" value="<?php echo $id_produk; ?>">
                        <div class="flex gap-4 mb-3">
                            <div class="w-1/3">
                                <select name="rating" class="w-full border-0 rounded-xl p-3 text-sm font-bold focus:ring-2 focus:ring-pink-500">
                                    <option value="5">★★★★★ (5)</option>
                                    <option value="4">★★★★ (4)</option>
                                    <option value="3">★★★ (3)</option>
                                    <option value="2">★★ (2)</option>
                                    <option value="1">★ (1)</option>
                                </select>
                            </div>
                            <div class="w-2/3">
                                <input type="text" value="<?php echo $_SESSION['user_nama']; ?>" disabled class="w-full border-0 rounded-xl p-3 text-sm bg-white text-gray-500">
                            </div>
                        </div>
                        <textarea name="isi_ulasan" rows="3" placeholder="Ceritakan kepuasanmu..." required class="w-full border-0 rounded-xl p-4 text-sm focus:ring-2 focus:ring-pink-500 mb-3"></textarea>
                        <button type="submit" name="kirim_ulasan" class="bg-gray-900 text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-gray-800 transition shadow-lg">Kirim Ulasan</button>
                    </form>
                </div>
                <?php } else { ?>
                    <div class="bg-yellow-50 p-4 rounded-xl text-center border border-yellow-100 text-yellow-800 text-sm">
                        Ingin menulis ulasan? Silakan <a href="masuk.php" class="font-bold underline hover:text-yellow-900">Login Member</a> terlebih dahulu.
                    </div>
                <?php } ?>
            </div>

            <!-- KOLOM KANAN: PRODUK SERUPA -->
            <div>
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <i class="fas fa-heart text-pink-500"></i> Produk Serupa
                </h2>
                <div class="space-y-4">
                    <?php
                    $kategori_ini = $data['kategori'];
                    $q_serupa = mysqli_query($koneksi, "SELECT * FROM produk WHERE kategori='$kategori_ini' AND id!='$id_produk' LIMIT 3");
                    
                    if(mysqli_num_rows($q_serupa) > 0){
                        while($serupa = mysqli_fetch_array($q_serupa)){
                    ?>
                    <a href="detail.php?id=<?php echo $serupa['id']; ?>" class="flex gap-4 bg-white p-4 rounded-2xl shadow-sm hover:shadow-md transition border border-white group">
                        <img src="img/<?php echo $serupa['gambar']; ?>" class="w-20 h-20 object-cover rounded-xl bg-gray-100 group-hover:scale-105 transition">
                        <div class="flex flex-col justify-center">
                            <h4 class="font-bold text-gray-800 text-sm mb-1 line-clamp-2 group-hover:text-pink-600 transition"><?php echo $serupa['nama_produk']; ?></h4>
                            <p class="text-pink-600 font-bold text-sm">Rp <?php echo number_format($serupa['harga']); ?></p>
                            <span class="text-xs text-gray-400 mt-1">Lihat Detail <i class="fas fa-arrow-right ml-1 text-[10px]"></i></span>
                        </div>
                    </a>
                    <?php } } else { echo "<p class='text-gray-400 italic text-sm'>Tidak ada produk serupa lainnya.</p>"; } ?>
                </div>
            </div>

        </div>
    </div>

    <!-- FOOTER -->
    <footer class="bg-gray-900 text-white py-8 mt-12">
        <div class="container mx-auto px-6 text-center">
            <p class="text-sm text-gray-500">&copy; <?php echo date('Y'); ?> <?php echo $toko['nama_toko']; ?>. All rights reserved.</p>
        </div>
    </footer>

    <!-- MODAL FULLSCREEN -->
    <div id="imageModal" class="fixed inset-0 z-[99] hidden bg-black/95 flex items-center justify-center p-4 backdrop-blur-md transition-all duration-300 opacity-0" onclick="closeFullscreen()">
        <button class="absolute top-6 right-6 text-white text-4xl hover:text-gray-300">&times;</button>
        <img id="fullImage" src="" class="max-h-[90vh] max-w-[90vw] rounded-xl shadow-2xl transform scale-90 transition duration-300">
    </div>

    <script>
        function changeImage(src) {
            const mainImg = document.getElementById('mainImage');
            mainImg.style.opacity = 0;
            setTimeout(() => {
                mainImg.src = src;
                mainImg.style.opacity = 1;
            }, 200);
        }
        
        function openFullscreen(src) {
            const modal = document.getElementById('imageModal');
            const img = document.getElementById('fullImage');
            img.src = src;
            modal.classList.remove('hidden');
            // Animasi Masuk
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                img.classList.remove('scale-90');
            }, 10);
        }
        
        function closeFullscreen() {
            const modal = document.getElementById('imageModal');
            const img = document.getElementById('fullImage');
            // Animasi Keluar
            modal.classList.add('opacity-0');
            img.classList.add('scale-90');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
    </script>
</body>
</html>