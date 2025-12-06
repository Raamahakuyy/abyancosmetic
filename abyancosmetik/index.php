<?php
include 'koneksi.php';

// AMBIL DATA PROFIL TOKO
$query_profil = mysqli_query($koneksi, "SELECT * FROM profil_toko WHERE id=1");
$toko = mysqli_fetch_array($query_profil);

// MAPS URL
$maps_url = "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3989.274737243984!2d100.3905121!3d-0.9537736!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2fd4b94ea7d6acab%3A0xace72d542baa2cb8!2sABYAN%20COSMETIK!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid";
?>

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $toko['nama_toko']; ?> - Official Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Outfit', sans-serif; background-color: #FFF5F7; } /* Background pink sangat muda */
        
        /* Glassmorphism Navbar */
        .glass-nav {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #ec4899; border-radius: 10px; }
        
        .swiper-slide { height: auto; }
        .floating-wa { animation: float 3s ease-in-out infinite; }
        @keyframes float { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-10px); } }
        
        /* Card Hover Effect */
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 20px 25px -5px rgba(236, 72, 153, 0.15), 0 10px 10px -5px rgba(236, 72, 153, 0.1); }
    </style>
</head>
<body class="text-gray-800">

    <!-- NAVBAR MODERN -->
    <nav class="glass-nav fixed w-full top-0 z-50 transition-all duration-300">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <!-- Logo -->
            <a href="index.php" class="flex items-center gap-3 group">
                <div class="bg-gradient-to-tr from-pink-500 to-rose-400 text-white p-2.5 rounded-xl shadow-lg shadow-pink-200 group-hover:rotate-12 transition duration-300">
                    <i class="fas fa-sparkles text-xl"></i>
                </div>
                <div>
                    <h1 class="font-bold text-xl tracking-tight text-gray-900 uppercase leading-none"><?php echo $toko['nama_toko']; ?></h1>
                    <p class="text-[10px] text-pink-500 font-bold tracking-[0.2em] uppercase mt-1">Beauty Store</p>
                </div>
            </a>

            <!-- Menu Desktop -->
            <div class="hidden md:flex items-center gap-8 bg-white/50 px-8 py-2 rounded-full border border-white shadow-sm backdrop-blur-sm">
                <a href="#home" class="text-sm font-medium text-gray-600 hover:text-pink-600 transition">Home</a>
                <a href="#kategori" class="text-sm font-medium text-gray-600 hover:text-pink-600 transition">Kategori</a>
                <a href="katalog.php" class="text-sm font-medium text-gray-600 hover:text-pink-600 transition">Produk</a>
                <a href="#testimoni" class="text-sm font-medium text-gray-600 hover:text-pink-600 transition">Testimoni</a>
            </div>

            <!-- Icons Kanan -->
            <div class="flex items-center gap-4">
                <!-- Cart -->
                <a href="keranjang.php" class="relative p-2 text-gray-500 hover:text-pink-600 transition group">
                    <i class="fas fa-shopping-bag text-xl group-hover:scale-110 transition"></i>
                    <?php 
                    session_start();
                    if(isset($_SESSION['keranjang']) && count($_SESSION['keranjang']) > 0) { ?>
                        <span class="absolute top-0 right-0 bg-rose-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full border-2 border-white shadow-sm">
                            <?php echo count($_SESSION['keranjang']); ?>
                        </span>
                    <?php } ?>
                </a>

                <!-- Member/Login -->
                <?php if(isset($_SESSION['user_status']) && $_SESSION['user_status'] == 'login_member'){ ?>
                    <a href="akun.php" class="hidden md:flex items-center gap-2 bg-pink-50 text-pink-600 px-4 py-2 rounded-full font-bold text-sm hover:bg-pink-100 transition">
                        <i class="fas fa-user-circle text-lg"></i> <span><?php echo substr($_SESSION['user_nama'], 0, 8); ?>..</span>
                    </a>
                <?php } else { ?>
                    <a href="masuk.php" class="text-gray-500 hover:text-pink-600 font-bold text-sm">Masuk</a>
                <?php } ?>
                
                <!-- Admin (Hidden Style) -->
                <a href="login.php" class="text-gray-300 hover:text-gray-500 transition" title="Admin Area"><i class="fas fa-fingerprint"></i></a>
            </div>
        </div>
    </nav>

    <!-- HERO SLIDER (FULL WIDTH MODERN) -->
    <section id="home" class="relative mt-20 md:mt-0">
        <div class="swiper mySwiper w-full h-[500px] md:h-[650px]">
            <div class="swiper-wrapper">
                <?php
                $q_banner = mysqli_query($koneksi, "SELECT * FROM banner WHERE aktif='Y' ORDER BY id DESC");
                if(mysqli_num_rows($q_banner) > 0){
                    while($ban = mysqli_fetch_array($q_banner)){
                ?>
                <div class="swiper-slide relative group">
                    <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/30 to-transparent z-10"></div>
                    <img src="img/<?php echo $ban['gambar']; ?>" class="w-full h-full object-cover transition duration-1000 group-hover:scale-105">
                    
                    <div class="absolute inset-0 z-20 flex items-center container mx-auto px-6">
                        <div class="max-w-2xl text-white space-y-6" data-aos="fade-right">
                            <span class="bg-white/20 backdrop-blur-md border border-white/30 text-white px-4 py-1 rounded-full text-xs font-bold tracking-widest uppercase">New Collection</span>
                            <h2 class="text-5xl md:text-7xl font-bold leading-tight"><?php echo $ban['judul']; ?></h2>
                            <p class="text-lg md:text-xl text-gray-200 font-light max-w-lg"><?php echo $ban['deskripsi']; ?></p>
                            <div class="flex gap-4 pt-4">
                                <a href="<?php echo $ban['link']; ?>" class="bg-white text-gray-900 font-bold py-3 px-8 rounded-full shadow-[0_0_20px_rgba(255,255,255,0.3)] hover:shadow-[0_0_30px_rgba(255,255,255,0.5)] hover:scale-105 transition">
                                    Belanja Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } } else { ?>
                <!-- Fallback Banner -->
                <div class="swiper-slide relative">
                    <div class="absolute inset-0 bg-black/40 z-10"></div>
                    <img src="https://images.unsplash.com/photo-1596462502278-27bfdd403348?auto=format&fit=crop&w=1500&q=80" class="w-full h-full object-cover">
                    <div class="absolute inset-0 z-20 flex items-center justify-center text-center">
                        <div class="text-white">
                            <h2 class="text-6xl font-bold mb-4">Glow Up Today</h2>
                            <p class="text-2xl">Pusat Kosmetik Terlengkap</p>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </section>

    <!-- SERVICES / USP (Glass Effect) -->
    <div class="container mx-auto px-6 -mt-16 relative z-30">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-8">
            <!-- Item 1 -->
            <div class="bg-white/80 backdrop-blur-xl p-6 rounded-2xl shadow-lg border border-white flex flex-col items-center text-center hover:-translate-y-2 transition duration-300">
                <div class="w-12 h-12 bg-pink-100 text-pink-600 rounded-full flex items-center justify-center text-xl mb-3 shadow-inner">
                    <i class="fas fa-truck-fast"></i>
                </div>
                <h3 class="font-bold text-gray-800">Pengiriman Cepat</h3>
                <p class="text-xs text-gray-500 mt-1">Dikirim hari yang sama</p>
            </div>
            <!-- Item 2 -->
            <div class="bg-white/80 backdrop-blur-xl p-6 rounded-2xl shadow-lg border border-white flex flex-col items-center text-center hover:-translate-y-2 transition duration-300">
                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xl mb-3 shadow-inner">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3 class="font-bold text-gray-800">100% Original</h3>
                <p class="text-xs text-gray-500 mt-1">Garansi uang kembali</p>
            </div>
            <!-- Item 3 -->
            <div class="bg-white/80 backdrop-blur-xl p-6 rounded-2xl shadow-lg border border-white flex flex-col items-center text-center hover:-translate-y-2 transition duration-300">
                <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-xl mb-3 shadow-inner">
                    <i class="fas fa-tags"></i>
                </div>
                <h3 class="font-bold text-gray-800">Harga Terbaik</h3>
                <p class="text-xs text-gray-500 mt-1">Ramah di kantong</p>
            </div>
            <!-- Item 4 -->
            <div class="bg-white/80 backdrop-blur-xl p-6 rounded-2xl shadow-lg border border-white flex flex-col items-center text-center hover:-translate-y-2 transition duration-300">
                <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center text-xl mb-3 shadow-inner">
                    <i class="fas fa-headset"></i>
                </div>
                <h3 class="font-bold text-gray-800">Support 24/7</h3>
                <p class="text-xs text-gray-500 mt-1">Siap membantu Anda</p>
            </div>
        </div>
    </div>

    <!-- KATEGORI (Modern Grid) -->
    <section id="kategori" class="py-20">
        <div class="container mx-auto px-6">
            <div class="flex justify-between items-end mb-10">
                <div>
                    <span class="text-pink-500 font-bold tracking-widest text-xs uppercase">Pilihan Favorit</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-1">Kategori Belanja</h2>
                </div>
                <div class="h-1 w-20 bg-gradient-to-r from-pink-500 to-purple-500 rounded-full"></div>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                <!-- Function Cetak Kategori -->
                <?php 
                $kats = [
                    ['Kosmetik', 'kat_kosmetik.jpg'], ['Pakaian Dalam', 'kat_pakaidalam.jpg'], 
                    ['Pakaian Anak', 'kat_pakaiananak.jpg'], ['Perlengkapan Bayi', 'kat_bayi.jpg'], 
                    ['Mainan Anak', 'kat_mainan.jpg']
                ];
                foreach($kats as $k){ 
                ?>
                <a href="katalog.php?kategori=<?php echo $k[0]; ?>" class="group relative rounded-2xl overflow-hidden aspect-square shadow-md hover:shadow-xl transition">
                    <img src="img/<?php echo $k[1]; ?>" onerror="this.src='https://placehold.co/300x300/pink/white?text=<?php echo $k[0]; ?>'" class="w-full h-full object-cover transition duration-700 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end p-4">
                        <span class="text-white font-bold text-sm md:text-base group-hover:translate-x-2 transition"><?php echo $k[0]; ?></span>
                    </div>
                </a>
                <?php } ?>
                
                <!-- Tombol Lihat Semua -->
                <a href="katalog.php" class="group relative rounded-2xl overflow-hidden aspect-square shadow-md bg-gray-100 flex flex-col items-center justify-center hover:bg-pink-50 transition border-2 border-dashed border-gray-300 hover:border-pink-400">
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-gray-400 group-hover:text-pink-600 group-hover:scale-110 transition shadow-sm">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                    <span class="text-gray-500 font-bold text-sm mt-3 group-hover:text-pink-600">Lihat Semua</span>
                </a>
            </div>
        </div>
    </section>

    <!-- PRODUK TERLARIS (Card Modern) -->
    <section id="produk" class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="bg-pink-100 text-pink-600 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">Best Seller</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-3">Produk Paling Dicari</h2>
                <p class="text-gray-500 mt-4">Dapatkan produk kualitas terbaik dengan harga yang pas di kantong.</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <?php
                $query = mysqli_query($koneksi, "SELECT * FROM produk ORDER BY id DESC LIMIT 8");
                while ($data = mysqli_fetch_array($query)) {
                    $habis = ($data['stok'] == 0);
                    $ada_diskon = ($data['harga_coret'] > $data['harga']);
                    $persen = $ada_diskon ? round((($data['harga_coret']-$data['harga'])/$data['harga_coret'])*100) : 0;
                ?>
                <div class="product-card bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden relative group transition-all duration-300">
                    <!-- Link Gambar -->
                    <a href="detail.php?id=<?php echo $data['id']; ?>" class="block relative h-64 overflow-hidden bg-gray-50">
                        <?php if($data['label_sticker']) { ?>
                            <span class="absolute top-3 right-3 bg-yellow-400 text-yellow-900 text-[10px] font-bold px-2 py-1 rounded-lg shadow-sm z-20"><?php echo $data['label_sticker']; ?></span>
                        <?php } ?>
                        
                        <?php if($ada_diskon && !$habis) { ?>
                            <span class="absolute top-3 left-3 bg-rose-500 text-white text-[10px] font-bold px-2 py-1 rounded-lg shadow-sm z-20">-<?php echo $persen; ?>%</span>
                        <?php } ?>
                        
                        <img src="img/<?php echo $data['gambar']; ?>" onerror="this.src='https://via.placeholder.com/300'" class="w-full h-full object-cover transition duration-700 group-hover:scale-110">
                        
                        <?php if($habis) { ?>
                            <div class="absolute inset-0 bg-black/50 flex items-center justify-center z-20">
                                <span class="bg-black text-white px-4 py-1 font-bold rounded-full text-xs uppercase tracking-wide">Habis</span>
                            </div>
                        <?php } ?>
                    </a>
                    
                    <div class="p-5">
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1"><?php echo $data['kategori']; ?></p>
                        <h3 class="font-bold text-gray-800 mb-2 text-lg leading-tight line-clamp-2 hover:text-pink-600 transition">
                            <a href="detail.php?id=<?php echo $data['id']; ?>"><?php echo $data['nama_produk']; ?></a>
                        </h3>
                        
                        <div class="flex items-end gap-2 mb-4">
                            <p class="text-pink-600 font-bold text-xl">Rp <?php echo number_format($data['harga']); ?></p>
                            <?php if($ada_diskon) { ?>
                                <p class="text-gray-400 text-sm line-through mb-1">Rp <?php echo number_format($data['harga_coret']); ?></p>
                            <?php } ?>
                        </div>

                        <!-- Action Button -->
                        <?php if(!$habis) { ?>
                            <a href="tambah_keranjang.php?id=<?php echo $data['id']; ?>" class="w-full block bg-gray-900 text-white text-center py-3 rounded-xl font-bold text-sm hover:bg-pink-600 transition shadow-lg shadow-pink-200/0 hover:shadow-pink-500/30">
                                + Keranjang
                            </a>
                        <?php } else { ?>
                            <button disabled class="w-full block bg-gray-200 text-gray-400 py-3 rounded-xl font-bold text-sm cursor-not-allowed">Stok Habis</button>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>
            </div>

            <div class="text-center mt-12">
                <a href="katalog.php" class="inline-block border-2 border-gray-900 text-gray-900 px-8 py-3 rounded-full font-bold hover:bg-gray-900 hover:text-white transition duration-300">
                    Lihat Semua Produk
                </a>
            </div>
        </div>
    </section>

    <!-- INFO TOKO & FOOTER (Dark Elegant) -->
    <footer class="bg-gray-900 text-white pt-20 pb-10 rounded-t-[3rem] mt-12">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-16 border-b border-gray-800 pb-12">
                <!-- Brand -->
                <div class="md:col-span-2">
                    <h3 class="text-3xl font-bold mb-6 text-white flex items-center gap-2">
                        <i class="fas fa-sparkles text-pink-500"></i> <?php echo $toko['nama_toko']; ?>
                    </h3>
                    <p class="text-gray-400 leading-relaxed mb-6 max-w-md"><?php echo $toko['deskripsi']; ?></p>
                    <div class="flex gap-4">
                        <a href="<?php echo $toko['instagram']; ?>" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-pink-600 hover:text-white transition"><i class="fab fa-instagram"></i></a>
                        <a href="<?php echo $toko['facebook']; ?>" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-blue-600 hover:text-white transition"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-black hover:text-white transition"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>

                <!-- Links -->
                <div>
                    <h4 class="text-lg font-bold mb-6 text-white">Menu</h4>
                    <ul class="space-y-3 text-gray-400">
                        <li><a href="index.php" class="hover:text-pink-500 transition">Beranda</a></li>
                        <li><a href="katalog.php" class="hover:text-pink-500 transition">Katalog</a></li>
                        <li><a href="masuk.php" class="hover:text-pink-500 transition">Login Member</a></li>
                        <li><a href="login.php" class="hover:text-pink-500 transition">Login Admin</a></li>
                    </ul>
                </div>

                <!-- Kontak -->
                <div>
                    <h4 class="text-lg font-bold mb-6 text-white">Hubungi Kami</h4>
                    <ul class="space-y-4 text-gray-400">
                        <li class="flex items-start gap-3">
                            <i class="fas fa-map-marker-alt mt-1 text-pink-500"></i>
                            <span class="text-sm"><?php echo nl2br($toko['alamat']); ?></span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fab fa-whatsapp text-pink-500"></i>
                            <span>+<?php echo $toko['no_wa']; ?></span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Maps & Copyright -->
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                 <div class="w-full md:w-1/3 h-40 bg-gray-800 rounded-xl overflow-hidden shadow-lg grayscale hover:grayscale-0 transition duration-500">
                    <iframe src="<?php echo $maps_url; ?>" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
                <p class="text-sm text-gray-500 text-center md:text-right">&copy; <?php echo date('Y'); ?> <?php echo $toko['nama_toko']; ?>. All rights reserved.<br>Designed with <i class="fas fa-heart text-pink-600"></i> in Padang.</p>
            </div>
        </div>
    </footer>

    <!-- Floating WA -->
    <a href="javascript:void(0)" onclick="pesan('Tanya Produk')" class="fixed bottom-6 right-6 bg-green-500 hover:bg-green-600 text-white p-4 rounded-full shadow-2xl z-50 flex items-center justify-center w-16 h-16 floating-wa transition hover:scale-110"><i class="fab fa-whatsapp text-4xl"></i></a>

    <!-- SWIPER JS INIT -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        var swiper = new Swiper(".mySwiper", {
            spaceBetween: 0,
            effect: "fade",
            speed: 1000,
            loop: true,
            autoplay: { delay: 5000, disableOnInteraction: false },
            pagination: { el: ".swiper-pagination", clickable: true },
        });

        function pesan(namaProduk) {
            const nomorWA = "<?php echo $toko['no_wa']; ?>"; 
            window.open(`https://wa.me/${nomorWA}?text=Halo%20Admin,%20saya%20mau%20tanya%20tentang%20produk%20*${namaProduk}*...`, '_blank');
        }
    </script>
</body>
</html>