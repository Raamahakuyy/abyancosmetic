<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:login.php"); exit();
}

if (isset($_GET['approve'])) {
    $id = $_GET['approve'];
    mysqli_query($koneksi, "UPDATE testimoni SET status='approved' WHERE id='$id'");
    echo "<script>window.location='kelola_testimoni.php';</script>";
}

if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM testimoni WHERE id='$id'");
    echo "<script>window.location='kelola_testimoni.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Moderasi Review</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap'); body { font-family: 'Outfit', sans-serif; background-color: #F3F4F6; }</style>
</head>
<body class="text-gray-800">

    <nav class="bg-gray-900 text-white p-4 sticky top-0 z-50 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="bg-gray-800 p-2 rounded-lg"><i class="fas fa-comments text-yellow-400"></i></div>
                <h1 class="font-bold text-lg tracking-wide">Moderasi Review</h1>
            </div>
            <a href="admin.php" class="bg-gray-800 hover:bg-gray-700 px-4 py-2 rounded-lg text-xs font-bold transition"><i class="fas fa-arrow-left mr-2"></i> Dashboard</a>
        </div>
    </nav>

    <div class="container mx-auto p-6 space-y-8">
        
        <!-- PENDING REVIEWS -->
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-r-2xl shadow-sm">
            <h3 class="text-lg font-bold text-yellow-800 mb-4 flex items-center">
                <i class="fas fa-clock mr-2"></i> Menunggu Persetujuan
                <?php $pending = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM testimoni WHERE status='pending'")); 
                if($pending > 0) echo "<span class='ml-3 bg-red-500 text-white text-[10px] px-2 py-1 rounded-full animate-pulse'>$pending Baru</span>"; ?>
            </h3>
            
            <?php if($pending == 0) { echo "<p class='text-yellow-600 italic text-sm'>Tidak ada review baru.</p>"; } else { ?>
            <div class="grid gap-4">
                <?php
                $q_pending = mysqli_query($koneksi, "SELECT * FROM testimoni WHERE status='pending' ORDER BY id DESC");
                while($row = mysqli_fetch_array($q_pending)){
                ?>
                <div class="bg-white p-4 rounded-xl shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-bold text-gray-800"><?php echo $row['nama_pelanggan']; ?></span>
                            <span class="text-xs text-gray-400">• <?php echo $row['jabatan']; ?></span>
                            <div class="text-yellow-400 text-xs ml-2"><?php for($i=0;$i<$row['bintang'];$i++) echo "★"; ?></div>
                        </div>
                        <p class="text-gray-600 text-sm italic">"<?php echo $row['isi_review']; ?>"</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="kelola_testimoni.php?approve=<?php echo $row['id']; ?>" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-xs font-bold shadow transition"><i class="fas fa-check mr-1"></i> Terima</a>
                        <a href="kelola_testimoni.php?hapus=<?php echo $row['id']; ?>" onclick="return confirm('Tolak?')" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-xs font-bold shadow transition"><i class="fas fa-times mr-1"></i> Tolak</a>
                    </div>
                </div>
                <?php } ?>
            </div>
            <?php } ?>
        </div>

        <!-- APPROVED REVIEWS -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 mb-6 flex items-center gap-2"><i class="fas fa-check-circle text-green-500"></i> Review Aktif</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-bold">
                        <tr><th class="p-4">Pelanggan</th><th class="p-4">Review</th><th class="p-4 text-center">Rating</th><th class="p-4 text-center">Aksi</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-sm">
                        <?php
                        $q_ok = mysqli_query($koneksi, "SELECT * FROM testimoni WHERE status='approved' ORDER BY id DESC");
                        while($row = mysqli_fetch_array($q_ok)){
                        ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4">
                                <span class="font-bold block"><?php echo $row['nama_pelanggan']; ?></span>
                                <span class="text-xs text-gray-400"><?php echo $row['jabatan']; ?></span>
                            </td>
                            <td class="p-4 text-gray-600 italic max-w-md truncate">"<?php echo $row['isi_review']; ?>"</td>
                            <td class="p-4 text-center text-yellow-400 text-xs"><?php for($i=0;$i<$row['bintang'];$i++) echo "★"; ?></td>
                            <td class="p-4 text-center">
                                <a href="kelola_testimoni.php?hapus=<?php echo $row['id']; ?>" onclick="return confirm('Hapus permanen?')" class="text-red-400 hover:text-red-600 p-2 transition"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>