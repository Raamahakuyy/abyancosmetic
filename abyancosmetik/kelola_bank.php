<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:login.php"); exit();
}

if (isset($_POST['tambah'])) {
    $bank = $_POST['nama_bank'];
    $rek  = $_POST['nomor_rekening'];
    $an   = $_POST['atas_nama'];
    mysqli_query($koneksi, "INSERT INTO bank (nama_bank, nomor_rekening, atas_nama) VALUES ('$bank', '$rek', '$an')");
    echo "<script>window.location='kelola_bank.php';</script>";
}

if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM bank WHERE id='$id'");
    echo "<script>window.location='kelola_bank.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Bank</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap'); body { font-family: 'Outfit', sans-serif; background-color: #F3F4F6; }</style>
</head>
<body class="text-gray-800">

    <nav class="bg-gray-900 text-white p-4 sticky top-0 z-50 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="bg-gray-800 p-2 rounded-lg"><i class="fas fa-wallet text-green-400"></i></div>
                <h1 class="font-bold text-lg tracking-wide">Metode Pembayaran</h1>
            </div>
            <a href="admin.php" class="bg-gray-800 hover:bg-gray-700 px-4 py-2 rounded-lg text-xs font-bold transition"><i class="fas fa-arrow-left mr-2"></i> Dashboard</a>
        </div>
    </nav>

    <div class="container mx-auto p-6">
        <div class="flex flex-col lg:flex-row gap-8">
            
            <div class="lg:w-1/3 h-fit bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-800 mb-6 flex items-center gap-2"><i class="fas fa-plus-circle text-green-500"></i> Tambah Rekening</h3>
                <form action="" method="POST">
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Nama Bank</label>
                        <input type="text" name="nama_bank" placeholder="Contoh: BCA" required class="w-full p-3 bg-gray-50 border-transparent rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-green-500 transition">
                    </div>
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Nomor Rekening</label>
                        <input type="number" name="nomor_rekening" required class="w-full p-3 bg-gray-50 border-transparent rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-green-500 transition">
                    </div>
                    <div class="mb-6">
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Atas Nama</label>
                        <input type="text" name="atas_nama" required class="w-full p-3 bg-gray-50 border-transparent rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-green-500 transition">
                    </div>
                    <button type="submit" name="tambah" class="w-full bg-gray-900 text-white py-3 rounded-xl font-bold hover:bg-green-600 transition shadow-lg">Simpan</button>
                </form>
            </div>

            <div class="lg:w-2/3 bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-800 mb-6">Daftar Rekening Aktif</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-bold">
                            <tr><th class="p-4">Bank</th><th class="p-4">No. Rekening</th><th class="p-4">Atas Nama</th><th class="p-4 text-center">Aksi</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-sm">
                            <?php
                            $q = mysqli_query($koneksi, "SELECT * FROM bank ORDER BY id ASC");
                            while($row = mysqli_fetch_array($q)){
                            ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-4 font-bold text-gray-800"><?php echo $row['nama_bank']; ?></td>
                                <td class="p-4 font-mono"><?php echo $row['nomor_rekening']; ?></td>
                                <td class="p-4 text-gray-600"><?php echo $row['atas_nama']; ?></td>
                                <td class="p-4 text-center">
                                    <a href="kelola_bank.php?hapus=<?php echo $row['id']; ?>" onclick="return confirm('Hapus rekening ini?')" class="text-red-400 hover:text-red-600 p-2 rounded transition"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>