<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:login.php"); exit();
}

// UPDATE STATUS & RESI
if (isset($_POST['update_order'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];
    $resi = $_POST['no_resi']; 
    $query = "UPDATE pesanan SET status='$status', no_resi='$resi' WHERE id='$id'";
    mysqli_query($koneksi, $query);
    echo "<script>alert('Update berhasil!'); window.location='pesanan.php';</script>";
}

// --- FITUR HAPUS PESANAN (BARU) ---
if (isset($_GET['hapus'])) {
    $id_pesanan = $_GET['hapus'];
    
    // Hapus juga data di tabel detail_pesanan biar bersih (Relasi)
    // Ambil invoice_id dulu
    $q_cek = mysqli_query($koneksi, "SELECT invoice_id, bukti_bayar FROM pesanan WHERE id='$id_pesanan'");
    $d_cek = mysqli_fetch_array($q_cek);
    $invoice = $d_cek['invoice_id'];
    
    // Hapus bukti bayar jika ada
    if(!empty($d_cek['bukti_bayar']) && file_exists("img/".$d_cek['bukti_bayar'])){
        unlink("img/".$d_cek['bukti_bayar']);
    }

    // Hapus detail pesanan
    mysqli_query($koneksi, "DELETE FROM detail_pesanan WHERE invoice_id='$invoice'");
    
    // Hapus pesanan utama
    $hapus = mysqli_query($koneksi, "DELETE FROM pesanan WHERE id='$id_pesanan'");
    
    if($hapus){
        echo "<script>alert('Riwayat pesanan berhasil dihapus permanen!'); window.location='pesanan.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Pesanan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap'); 
        body { font-family: 'Outfit', sans-serif; background-color: #F3F4F6; }
        .modal { display: none; position: fixed; z-index: 999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.9); }
        .modal-content { margin: auto; display: block; max-width: 90%; max-height: 90%; }
    </style>
</head>
<body class="text-gray-800">

    <nav class="bg-gray-900 text-white p-4 sticky top-0 z-50 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="bg-gray-800 p-2 rounded-lg"><i class="fas fa-shipping-fast text-blue-400"></i></div>
                <h1 class="font-bold text-lg tracking-wide">Pengiriman</h1>
            </div>
            <a href="admin.php" class="bg-gray-800 hover:bg-gray-700 px-4 py-2 rounded-lg text-xs font-bold transition"><i class="fas fa-arrow-left mr-2"></i> Dashboard</a>
        </div>
    </nav>

    <div class="container mx-auto p-6">
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-bold border-b border-gray-100">
                        <tr><th class="p-5">Invoice</th><th class="p-5">Pelanggan & Pengiriman</th><th class="p-5">Total</th><th class="p-5">Status & Resi</th><th class="p-5 text-center">Aksi</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-sm">
                        <?php
                        $query = mysqli_query($koneksi, "SELECT * FROM pesanan ORDER BY id DESC");
                        while($row = mysqli_fetch_array($query)){
                            $statusColor = "bg-gray-50 text-gray-600";
                            if($row['status']=='Baru') $statusColor = "bg-blue-50 text-blue-600 border border-blue-200";
                            if($row['status']=='Menunggu Konfirmasi') $statusColor = "bg-orange-50 text-orange-600 border border-orange-200 animate-pulse";
                            if($row['status']=='Proses') $statusColor = "bg-yellow-50 text-yellow-600 border border-yellow-200";
                            if($row['status']=='Selesai') $statusColor = "bg-green-50 text-green-600 border border-green-200";
                            if($row['status']=='Batal') $statusColor = "bg-red-50 text-red-600 border border-red-200";
                            
                            $is_instant = ($row['pengiriman'] == 'Instant (Gojek/Grab)');
                        ?>
                        <tr class="hover:bg-gray-50 transition align-top">
                            <td class="p-5">
                                <span class="font-bold text-gray-900 font-mono bg-gray-100 px-2 py-1 rounded text-xs"><?php echo $row['invoice_id']; ?></span><br>
                                <span class="text-xs text-gray-400 mt-1 block"><?php echo date('d/m/Y H:i', strtotime($row['tanggal'])); ?></span>
                                <?php if(!empty($row['bukti_bayar'])) { ?>
                                    <button onclick="showImage('img/<?php echo $row['bukti_bayar']; ?>')" class="mt-3 bg-purple-50 text-purple-600 px-3 py-1.5 rounded-lg text-xs font-bold border border-purple-100 hover:bg-purple-100 transition w-full flex justify-center gap-1 items-center">
                                        <i class="fas fa-receipt"></i> Bukti
                                    </button>
                                <?php } ?>
                            </td>
                            <td class="p-5">
                                <div class="font-bold text-gray-800"><?php echo $row['nama_pemesan']; ?></div>
                                <div class="text-xs text-gray-500 mb-2 flex items-center gap-1"><i class="fab fa-whatsapp text-green-500"></i> <?php echo $row['no_wa']; ?></div>
                                <div class="text-xs bg-blue-50 text-blue-600 px-2 py-1 rounded inline-block font-bold mb-1">
                                    <i class="<?php echo $is_instant ? 'fas fa-motorcycle' : 'fas fa-truck'; ?>"></i> <?php echo $row['pengiriman']; ?>
                                </div>
                                <div class="text-xs text-gray-400 mt-1 max-w-xs line-clamp-2"><?php echo $row['alamat']; ?></div>
                            </td>
                            <td class="p-5 font-bold text-gray-800">
                                Rp <?php echo number_format($row['total_bayar']); ?>
                            </td>
                            
                            <form action="" method="POST">
                            <td class="p-5">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <select name="status" class="w-full text-xs p-2 border-transparent rounded-lg mb-2 font-bold <?php echo $statusColor; ?> focus:ring-2 focus:ring-blue-500">
                                    <option value="Baru" <?php if($row['status']=='Baru') echo 'selected'; ?>>Baru</option>
                                    <option value="Menunggu Konfirmasi" <?php if($row['status']=='Menunggu Konfirmasi') echo 'selected'; ?>>Menunggu Konfirmasi</option>
                                    <option value="Proses" <?php if($row['status']=='Proses') echo 'selected'; ?>>Proses</option>
                                    <option value="Selesai" <?php if($row['status']=='Selesai') echo 'selected'; ?>>Selesai</option>
                                    <option value="Batal" <?php if($row['status']=='Batal') echo 'selected'; ?>>Batal</option>
                                </select>

                                <input type="text" name="no_resi" value="<?php echo $row['no_resi']; ?>" 
                                       class="w-full text-xs p-2 bg-gray-100 border-transparent rounded-lg text-gray-600 focus:bg-white focus:ring-2 focus:ring-blue-500 transition" 
                                       placeholder="<?php echo $is_instant ? 'Info Driver...' : 'No. Resi...'; ?>">
                            </td>
                            
                            <td class="p-5 text-center space-y-2">
                                <button type="submit" name="update_order" class="bg-gray-900 text-white p-2 rounded-lg shadow hover:bg-gray-700 transition w-full" title="Simpan Perubahan">
                                    <i class="fas fa-save"></i>
                                </button>
                                <div class="flex gap-2">
                                    <a href="cetak_label.php?id=<?php echo $row['id']; ?>" target="_blank" class="bg-white border border-gray-200 text-gray-500 p-2 rounded-lg hover:text-gray-800 hover:border-gray-400 transition flex-1" title="Cetak Label">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <?php 
                                    $pesan_wa = "Halo kak " . $row['nama_pemesan'] . ", pesanan " . $row['invoice_id'] . " statusnya: " . $row['status'] . ".";
                                    ?>
                                    <a href="https://wa.me/<?php echo $row['no_wa']; ?>?text=<?php echo urlencode($pesan_wa); ?>" target="_blank" class="bg-green-100 text-green-600 p-2 rounded-lg hover:bg-green-200 transition flex-1" title="Chat WA">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                </div>
                                
                                <!-- TOMBOL HAPUS (BARU) -->
                                <a href="pesanan.php?hapus=<?php echo $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus riwayat pesanan ini secara permanen? Data tidak bisa dikembalikan.')" class="block bg-red-50 text-red-500 border border-red-100 p-2 rounded-lg hover:bg-red-100 hover:text-red-700 transition text-xs font-bold">
                                    <i class="fas fa-trash-alt mr-1"></i> Hapus
                                </a>
                            </td>
                            </form>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="modalBukti" class="modal flex items-center justify-center" onclick="this.style.display='none'">
        <img class="modal-content rounded-xl shadow-2xl" id="imgBukti">
    </div>

    <script>
        function showImage(src) {
            document.getElementById('modalBukti').style.display = "flex";
            document.getElementById('imgBukti').src = src;
        }
    </script>
</body>
</html>