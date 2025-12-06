<?php
session_start();
include 'koneksi.php';

// Cek Login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:login.php?pesan=belum_login"); exit();
}

// Hapus Produk
if (isset($_GET['hapus'])) {
    $id_produk = $_GET['hapus'];
    $gambar_lama = mysqli_query($koneksi, "SELECT gambar FROM produk WHERE id='$id_produk'");
    $data_gbr = mysqli_fetch_array($gambar_lama);
    if($data_gbr['gambar'] != 'no-image.jpg' && file_exists('./img/'.$data_gbr['gambar'])){ unlink('./img/'.$data_gbr['gambar']); }
    mysqli_query($koneksi, "DELETE FROM produk WHERE id = '$id_produk'");
    echo "<script>alert('Dihapus!'); window.location='admin.php';</script>";
}

// Pencarian
$keyword = "";
$query_str = "SELECT * FROM produk ORDER BY id DESC";
if(isset($_POST['cari'])){
    $keyword = $_POST['keyword'];
    $query_str = "SELECT * FROM produk WHERE nama_produk LIKE '%$keyword%' OR kategori LIKE '%$keyword%' ORDER BY id DESC";
}

// Data Grafik
$labels_laris = []; $data_laris = [];
$q_laris = mysqli_query($koneksi, "SELECT nama_produk, SUM(jumlah) as total_jual FROM detail_pesanan GROUP BY nama_produk ORDER BY total_jual DESC LIMIT 5");
while($row = mysqli_fetch_array($q_laris)){ $labels_laris[] = substr($row['nama_produk'], 0, 10).'...'; $data_laris[] = $row['total_jual']; }

$labels_kat = []; $data_kat = [];
$q_kat = mysqli_query($koneksi, "SELECT kategori, COUNT(*) as jumlah FROM produk GROUP BY kategori");
while($row = mysqli_fetch_array($q_kat)){ $labels_kat[] = $row['kategori']; $data_kat[] = $row['jumlah']; }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap'); body { font-family: 'Outfit', sans-serif; background-color: #F3F4F6; }</style>
</head>
<body class="text-gray-800">

    <nav class="bg-gray-900 text-white p-4 sticky top-0 z-50 shadow-lg">
        <div class="container mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="bg-gray-800 p-2 rounded-lg"><i class="fas fa-chart-pie text-pink-500"></i></div>
                <div><h1 class="font-bold text-lg tracking-wide">Admin Panel</h1><span class="text-xs text-gray-400">Hi, <?php echo $_SESSION['username']; ?></span></div>
            </div>
            
            <!-- MENU NAVIGASI -->
            <div class="flex flex-wrap gap-2 justify-center">
                <a href="pesanan.php" class="bg-gray-800 hover:bg-gray-700 px-3 py-2 rounded-lg text-xs font-bold transition flex items-center gap-2"><i class="fas fa-shopping-bag text-blue-400"></i> Pesanan</a>
                <a href="kelola_testimoni.php" class="bg-gray-800 hover:bg-gray-700 px-3 py-2 rounded-lg text-xs font-bold transition flex items-center gap-2"><i class="fas fa-star text-yellow-400"></i> Review</a>
                <a href="kelola_bank.php" class="bg-gray-800 hover:bg-gray-700 px-3 py-2 rounded-lg text-xs font-bold transition flex items-center gap-2"><i class="fas fa-wallet text-green-400"></i> Bank</a>
                <a href="kelola_voucher.php" class="bg-gray-800 hover:bg-gray-700 px-3 py-2 rounded-lg text-xs font-bold transition flex items-center gap-2"><i class="fas fa-ticket text-purple-400"></i> Voucher</a>
                <a href="kelola_banner.php" class="bg-gray-800 hover:bg-gray-700 px-3 py-2 rounded-lg text-xs font-bold transition flex items-center gap-2"><i class="fas fa-images text-pink-400"></i> Banner</a>
                <a href="laporan.php" class="bg-gray-800 hover:bg-gray-700 px-3 py-2 rounded-lg text-xs font-bold transition flex items-center gap-2"><i class="fas fa-print text-orange-400"></i> Laporan</a>
                
                <!-- TOMBOL PENGATURAN TOKO & AKUN (DIGABUNG) -->
                <a href="profil.php" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow flex items-center">
                    <i class="fas fa-cogs mr-2"></i> Pengaturan
                </a>

                <a href="index.php" target="_blank" class="bg-white text-gray-800 hover:bg-gray-200 px-4 py-2 rounded-lg text-sm font-bold shadow"><i class="fas fa-external-link-alt"></i></a>
                <a href="logout.php" onclick="return confirm('Keluar?')" class="bg-red-600 hover:bg-red-700 px-3 py-2 rounded-lg text-xs font-bold transition"><i class="fas fa-power-off"></i></a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto p-6 space-y-8">
        <!-- STATS -->
        <?php
            $total_produk = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM produk"));
            $sql_stok = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(stok) as total FROM produk"));
            $sql_aset = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(harga * stok) as total FROM produk"));
            $sql_pesanan = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM pesanan WHERE status='Baru'"));
        ?>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl"><i class="fas fa-cube"></i></div>
                <div><p class="text-xs text-gray-400 font-bold uppercase">Produk</p><p class="text-2xl font-bold text-gray-800"><?php echo $total_produk; ?></p></div>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center text-xl"><i class="fas fa-boxes"></i></div>
                <div><p class="text-xs text-gray-400 font-bold uppercase">Stok</p><p class="text-2xl font-bold text-gray-800"><?php echo $sql_stok['total'] ?? 0; ?></p></div>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-yellow-50 text-yellow-600 flex items-center justify-center text-xl"><i class="fas fa-coins"></i></div>
                <div><p class="text-xs text-gray-400 font-bold uppercase">Aset</p><p class="text-xl font-bold text-gray-800">Rp <?php echo number_format($sql_aset['total']); ?></p></div>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-xl"><i class="fas fa-bell"></i></div>
                <div><p class="text-xs text-gray-400 font-bold uppercase">Order Baru</p><p class="text-2xl font-bold text-red-600"><?php echo $sql_pesanan; ?></p></div>
            </div>
        </div>

        <!-- GRAFIK -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 md:col-span-2">
                <h3 class="font-bold text-gray-800 mb-4">Penjualan Terlaris</h3>
                <div class="h-64"><canvas id="chartLaris"></canvas></div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-800 mb-4">Kategori</h3>
                <div class="h-64 flex justify-center"><canvas id="chartKategori"></canvas></div>
            </div>
        </div>

        <!-- TABEL PRODUK -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-50 flex flex-col md:flex-row justify-between gap-4 bg-white">
                <h2 class="font-bold text-lg">Stok Produk</h2>
                <div class="flex gap-2">
                    <form action="" method="POST" class="flex">
                        <input type="text" name="keyword" value="<?php echo $keyword; ?>" placeholder="Cari..." class="px-4 py-2 bg-gray-50 border-transparent rounded-l-lg text-sm focus:bg-white focus:ring-2 focus:ring-gray-200 transition">
                        <button name="cari" class="bg-gray-100 px-4 rounded-r-lg text-gray-500 hover:bg-gray-200"><i class="fas fa-search"></i></button>
                    </form>
                    <a href="tambah.php" class="bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-gray-800 flex items-center gap-2"><i class="fas fa-plus"></i> Tambah</a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-bold">
                        <tr><th class="p-4">Produk</th><th class="p-4">Kategori</th><th class="p-4">Harga</th><th class="p-4 text-center">Stok</th><th class="p-4 text-center">Aksi</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-sm">
                        <?php
                        $query = mysqli_query($koneksi, $query_str);
                        while ($data = mysqli_fetch_array($query)) {
                            $stok_alert = ($data['stok'] <= 5) ? "text-red-600 font-bold" : "text-gray-600";
                        ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 flex items-center gap-3">
                                <img src="img/<?php echo $data['gambar']; ?>" class="w-10 h-10 rounded-lg object-cover border border-gray-200">
                                <div>
                                    <p class="font-bold text-gray-800"><?php echo $data['nama_produk']; ?></p>
                                    <?php if($data['stok'] <= 5) echo "<span class='text-[10px] text-red-500 bg-red-50 px-1.5 py-0.5 rounded font-bold'>LOW!</span>"; ?>
                                </div>
                            </td>
                            <td class="p-4"><span class="bg-gray-100 text-gray-500 px-2 py-1 rounded text-xs font-bold"><?php echo $data['kategori']; ?></span></td>
                            <td class="p-4 font-medium">Rp <?php echo number_format($data['harga']); ?></td>
                            <td class="p-4 text-center <?php echo $stok_alert; ?>"><?php echo $data['stok']; ?></td>
                            <td class="p-4 text-center">
                                <a href="edit.php?id=<?php echo $data['id']; ?>" class="text-blue-500 hover:bg-blue-50 p-2 rounded transition"><i class="fas fa-edit"></i></a>
                                <a href="admin.php?hapus=<?php echo $data['id']; ?>" onclick="return confirm('Hapus?')" class="text-red-500 hover:bg-red-50 p-2 rounded transition"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        const labelsLaris = <?php echo json_encode($labels_laris); ?>;
        const dataLaris = <?php echo json_encode($data_laris); ?>;
        const labelsKat = <?php echo json_encode($labels_kat); ?>;
        const dataKat = <?php echo json_encode($data_kat); ?>;

        new Chart(document.getElementById('chartLaris'), {
            type: 'bar',
            data: { labels: labelsLaris, datasets: [{ label: 'Terjual', data: dataLaris, backgroundColor: '#1F2937', borderRadius: 6 }] },
            options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, grid: { display: false } }, x: { grid: { display: false } } }, plugins: { legend: { display: false } } }
        });

        new Chart(document.getElementById('chartKategori'), {
            type: 'doughnut',
            data: { labels: labelsKat, datasets: [{ data: dataKat, backgroundColor: ['#EC4899', '#8B5CF6', '#3B82F6', '#10B981', '#F59E0B'], borderWidth: 0 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } } } }
        });
    </script>
</body>
</html>