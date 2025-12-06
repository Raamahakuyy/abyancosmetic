<?php
session_start();
include 'koneksi.php';

$query_profil = mysqli_query($koneksi, "SELECT * FROM profil_toko WHERE id=1");
$toko = mysqli_fetch_array($query_profil);

// FILTER LOGIC
$where = "WHERE 1=1"; 
$kategori_pilih = isset($_GET['kategori']) ? $_GET['kategori'] : '';
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'terbaru';

if ($kategori_pilih != '') { $where .= " AND kategori = '$kategori_pilih'"; }
if ($keyword != '') { $where .= " AND (nama_produk LIKE '%$keyword%' OR deskripsi LIKE '%$keyword%')"; }

$order_by = "ORDER BY id DESC"; 
if ($sort == 'termurah') { $order_by = "ORDER BY harga ASC"; } 
elseif ($sort == 'termahal') { $order_by = "ORDER BY harga DESC"; } 
elseif ($sort == 'abjad') { $order_by = "ORDER BY nama_produk ASC"; }

// PAGINATION
$batas = 12; 
$halaman = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$halaman_awal = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

$previous = $halaman - 1;
$next = $halaman + 1;

$data_all = mysqli_query($koneksi, "SELECT id FROM produk $where");
$jumlah_data = mysqli_num_rows($data_all);
$total_halaman = ceil($jumlah_data / $batas);

$query_produk = mysqli_query($koneksi, "SELECT * FROM produk $where $order_by LIMIT $halaman_awal, $batas");
?>

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog - <?php echo $toko['nama_toko']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Outfit', sans-serif; background-color: #FFF5F7; }
        .glass-nav { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(255,255,255,0.5); }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body class="text-gray-800 flex flex-col min-h-screen">

    <!-- NAVBAR SIMPLE -->
    <nav class="glass-nav sticky top-0 z-50">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="index.php" class="flex items-center gap-2 font-bold text-xl tracking-tight text-gray-900 uppercase">
                <div class="bg-pink-600 text-white p-1.5 rounded-lg"><i class="fas fa-sparkles"></i></div>
                <?php echo $toko['nama_toko']; ?>
            </a>
            <div class="flex items-center gap-4">
                <a href="index.php" class="text-sm font-medium text-gray-500 hover:text-pink-600">Home</a>
                <a href="keranjang.php" class="relative text-gray-500 hover:text-pink-600 transition">
                    <i class="fas fa-shopping-bag text-xl"></i>
                    <?php if(isset($_SESSION['keranjang']) && count($_SESSION['keranjang']) > 0) { ?>
                        <span class="absolute -top-1 -right-1 bg-rose-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full"><?php echo count($_SESSION['keranjang']); ?></span>
                    <?php } ?>
                </a>
            </div>
        </div>
    </nav>

    <!-- CONTENT -->
    <div class="container mx-auto px-6 py-10 flex-grow">
        <div class="flex flex-col lg:flex-row gap-10">
            
            <!-- SIDEBAR FILTER -->
            <aside class="lg:w-1/4">
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 sticky top-24">
                    <div class="flex items-center gap-2 mb-6">
                        <div class="p-2 bg-pink-50 rounded-lg text-pink-600"><i class="fas fa-sliders-h"></i></div>
                        <h3 class="font-bold text-lg">Filter</h3>
                    </div>
                    
                    <form action="katalog.php" method="GET">
                        <div class="mb-5">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Pencarian</label>
                            <div class="relative">
                                <input type="text" name="keyword" value="<?php echo $keyword; ?>" class="w-full pl-10 pr-4 py-3 bg-gray-50 border-transparent rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-pink-500 transition" placeholder="Cari produk...">
                                <i class="fas fa-search absolute left-4 top-3.5 text-gray-400"></i>
                            </div>
                        </div>

                        <div class="mb-5">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Kategori</label>
                            <select name="kategori" class="w-full p-3 bg-gray-50 border-transparent rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-pink-500 cursor-pointer">
                                <option value="">Semua Kategori</option>
                                <?php
                                $q_kat = mysqli_query($koneksi, "SELECT DISTINCT kategori FROM produk");
                                while($kat = mysqli_fetch_array($q_kat)){
                                    $selected = ($kategori_pilih == $kat['kategori']) ? 'selected' : '';
                                    echo "<option value='".$kat['kategori']."' $selected>".$kat['kategori']."</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-8">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Urutkan</label>
                            <select name="sort" class="w-full p-3 bg-gray-50 border-transparent rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-pink-500 cursor-pointer">
                                <option value="terbaru" <?php if($sort=='terbaru') echo 'selected'; ?>>Paling Baru</option>
                                <option value="termurah" <?php if($sort=='termurah') echo 'selected'; ?>>Harga Terendah</option>
                                <option value="termahal" <?php if($sort=='termahal') echo 'selected'; ?>>Harga Tertinggi</option>
                                <option value="abjad" <?php if($sort=='abjad') echo 'selected'; ?>>Nama A-Z</option>
                            </select>
                        </div>

                        <button type="submit" class="w-full bg-gray-900 hover:bg-pink-600 text-white font-bold py-3 rounded-xl transition shadow-lg shadow-pink-200/0 hover:shadow-pink-500/30">
                            Terapkan Filter
                        </button>
                        
                        <?php if($keyword || $kategori_pilih) { ?>
                            <a href="katalog.php" class="block text-center mt-4 text-xs text-gray-400 hover:text-red-500 transition">Hapus semua filter</a>
                        <?php } ?>
                    </form>
                </div>
            </aside>

            <!-- PRODUCT GRID -->
            <div class="lg:w-3/4">
                <?php if(mysqli_num_rows($query_produk) > 0) { ?>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                    <?php
                    while ($data = mysqli_fetch_array($query_produk)) {
                        $habis = ($data['stok'] == 0);
                        $ada_diskon = ($data['harga_coret'] > $data['harga']);
                        $persen = $ada_diskon ? round((($data['harga_coret']-$data['harga'])/$data['harga_coret'])*100) : 0;
                    ?>
                    <!-- Modern Card -->
                    <div class="bg-white rounded-3xl border border-gray-100 overflow-hidden card-hover transition-all duration-300 group relative">
                        <!-- Link Foto -->
                        <a href="detail.php?id=<?php echo $data['id']; ?>" class="block relative h-56 bg-gray-50 overflow-hidden">
                            <?php if($data['label_sticker']) { ?>
                                <span class="absolute top-3 right-3 bg-white/90 backdrop-blur text-gray-800 text-[10px] font-bold px-2 py-1 rounded-lg shadow-sm z-20"><?php echo $data['label_sticker']; ?></span>
                            <?php } ?>
                            <?php if($ada_diskon && !$habis) { ?>
                                <span class="absolute top-3 left-3 bg-rose-500 text-white text-[10px] font-bold px-2 py-1 rounded-lg shadow-sm z-20">-<?php echo $persen; ?>%</span>
                            <?php } ?>
                            
                            <img src="img/<?php echo $data['gambar']; ?>" onerror="this.src='https://via.placeholder.com/300'" class="w-full h-full object-cover transition duration-700 group-hover:scale-110">
                            
                            <?php if($habis) { ?>
                                <div class="absolute inset-0 bg-black/50 flex items-center justify-center z-20"><span class="bg-white text-black px-3 py-1 font-bold rounded-full text-xs uppercase">Habis</span></div>
                            <?php } ?>
                        </a>
                        
                        <div class="p-5">
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider font-bold mb-1"><?php echo $data['kategori']; ?></p>
                            <h3 class="font-bold text-gray-900 mb-2 leading-snug line-clamp-2 group-hover:text-pink-600 transition">
                                <a href="detail.php?id=<?php echo $data['id']; ?>"><?php echo $data['nama_produk']; ?></a>
                            </h3>
                            
                            <div class="flex items-end gap-2 mb-4">
                                <p class="text-pink-600 font-bold text-lg">Rp <?php echo number_format($data['harga']); ?></p>
                                <?php if($ada_diskon) { ?>
                                    <p class="text-gray-300 text-xs line-through mb-1">Rp <?php echo number_format($data['harga_coret']); ?></p>
                                <?php } ?>
                            </div>

                            <div class="flex gap-2">
                                <a href="detail.php?id=<?php echo $data['id']; ?>" class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-50 text-gray-400 hover:bg-pink-50 hover:text-pink-600 transition"><i class="fas fa-eye"></i></a>
                                <?php if(!$habis) { ?>
                                    <a href="tambah_keranjang.php?id=<?php echo $data['id']; ?>" class="flex-1 bg-gray-900 text-white text-center py-2.5 rounded-xl font-bold text-sm hover:bg-pink-600 transition shadow-lg shadow-gray-200 hover:shadow-pink-200">
                                        + Keranjang
                                    </a>
                                <?php } else { ?>
                                    <button disabled class="flex-1 bg-gray-100 text-gray-400 py-2.5 rounded-xl font-bold text-sm cursor-not-allowed">Stok Habis</button>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>

                <!-- PAGINATION -->
                <div class="mt-12 flex justify-center">
                    <nav class="flex items-center bg-white p-2 rounded-full shadow-sm border border-gray-100 gap-2">
                        <?php if($halaman > 1) { ?>
                            <a href="?page=<?php echo $previous; ?>&keyword=<?php echo $keyword; ?>&kategori=<?php echo $kategori_pilih; ?>&sort=<?php echo $sort; ?>" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-100 transition text-gray-500"><i class="fas fa-chevron-left"></i></a>
                        <?php } ?>

                        <?php for($x = 1; $x <= $total_halaman; $x++) { ?>
                            <a href="?page=<?php echo $x; ?>&keyword=<?php echo $keyword; ?>&kategori=<?php echo $kategori_pilih; ?>&sort=<?php echo $sort; ?>" class="w-10 h-10 flex items-center justify-center rounded-full font-bold text-sm transition <?php echo ($x == $halaman) ? 'bg-pink-600 text-white shadow-md' : 'text-gray-500 hover:bg-gray-100'; ?>">
                                <?php echo $x; ?>
                            </a>
                        <?php } ?>

                        <?php if($halaman < $total_halaman) { ?>
                            <a href="?page=<?php echo $next; ?>&keyword=<?php echo $keyword; ?>&kategori=<?php echo $kategori_pilih; ?>&sort=<?php echo $sort; ?>" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-100 transition text-gray-500"><i class="fas fa-chevron-right"></i></a>
                        <?php } ?>
                    </nav>
                </div>

                <?php } else { ?>
                    <div class="text-center py-24 bg-white rounded-3xl border border-dashed border-gray-200">
                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300 text-3xl"><i class="fas fa-search"></i></div>
                        <h3 class="text-xl font-bold text-gray-800">Oops, tidak ketemu!</h3>
                        <p class="text-gray-500 mb-6 mt-2">Coba kata kunci lain atau ubah filter kategori.</p>
                        <a href="katalog.php" class="inline-block bg-white border border-gray-300 text-gray-700 px-6 py-2 rounded-full font-bold hover:border-pink-500 hover:text-pink-600 transition text-sm">Reset Filter</a>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <footer class="bg-white border-t border-gray-100 py-8 mt-auto">
        <div class="container mx-auto px-6 text-center text-gray-500 text-sm">
            &copy; <?php echo date('Y'); ?> <?php echo $toko['nama_toko']; ?>. Crafted for Beauty.
        </div>
    </footer>

</body>
</html>