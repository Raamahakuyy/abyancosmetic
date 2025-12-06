<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:login.php"); exit();
}

// Tambah Banner
if (isset($_POST['tambah'])) {
    $judul = $_POST['judul'];
    $ket   = $_POST['deskripsi'];
    $link  = $_POST['link'];
    
    $nama_file = $_FILES['gambar']['name'];
    $tmp_file = $_FILES['gambar']['tmp_name'];
    $nama_baru = "BANNER-" . time() . ".jpg";
    
    move_uploaded_file($tmp_file, 'img/' . $nama_baru);
    mysqli_query($koneksi, "INSERT INTO banner (judul, deskripsi, link, gambar) VALUES ('$judul', '$ket', '$link', '$nama_baru')");
    echo "<script>alert('Banner berhasil ditambahkan!'); window.location='kelola_banner.php';</script>";
}

// Hapus Banner
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $q = mysqli_query($koneksi, "SELECT gambar FROM banner WHERE id='$id'");
    $img = mysqli_fetch_array($q);
    if(file_exists("img/".$img['gambar'])) unlink("img/".$img['gambar']);
    mysqli_query($koneksi, "DELETE FROM banner WHERE id='$id'");
    echo "<script>window.location='kelola_banner.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Banner</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap'); body { font-family: 'Outfit', sans-serif; background-color: #F3F4F6; }</style>
</head>
<body class="text-gray-800">

    <nav class="bg-gray-900 text-white p-4 sticky top-0 z-50 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="bg-gray-800 p-2 rounded-lg"><i class="fas fa-images text-pink-400"></i></div>
                <h1 class="font-bold text-lg tracking-wide">Banner Slider</h1>
            </div>
            <a href="admin.php" class="bg-gray-800 hover:bg-gray-700 px-4 py-2 rounded-lg text-xs font-bold transition"><i class="fas fa-arrow-left mr-2"></i> Dashboard</a>
        </div>
    </nav>

    <div class="container mx-auto p-6">
        <div class="flex flex-col lg:flex-row gap-8">
            
            <!-- FORM UPLOAD -->
            <div class="lg:w-1/3 h-fit bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-800 mb-6 flex items-center gap-2"><i class="fas fa-plus-circle text-pink-500"></i> Upload Banner Baru</h3>
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Judul Promo</label>
                        <input type="text" name="judul" placeholder="Contoh: Promo Lebaran" required class="w-full p-3 bg-gray-50 border-transparent rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-pink-500 transition">
                    </div>
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Deskripsi</label>
                        <input type="text" name="deskripsi" placeholder="Diskon up to 50%" required class="w-full p-3 bg-gray-50 border-transparent rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-pink-500 transition">
                    </div>
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Link Tujuan</label>
                        <input type="text" name="link" value="katalog.php" class="w-full p-3 bg-gray-50 border-transparent rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-pink-500 transition">
                    </div>
                    <div class="mb-6">
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Gambar (Landscape)</label>
                        <div class="relative border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:bg-gray-50 transition group">
                            <input type="file" name="gambar" accept="image/*" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            <i class="fas fa-cloud-upload-alt text-3xl text-gray-300 group-hover:text-pink-500 mb-2 transition"></i>
                            <p class="text-xs text-gray-500">Klik atau geser file ke sini</p>
                        </div>
                    </div>
                    <button type="submit" name="tambah" class="w-full bg-gray-900 text-white py-3 rounded-xl font-bold hover:bg-pink-600 transition shadow-lg">Upload</button>
                </form>
            </div>

            <!-- DAFTAR BANNER -->
            <div class="lg:w-2/3">
                <h3 class="font-bold text-gray-800 mb-6 flex items-center gap-2"><i class="fas fa-list text-blue-500"></i> Banner Aktif</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php
                    $q = mysqli_query($koneksi, "SELECT * FROM banner ORDER BY id DESC");
                    while($row = mysqli_fetch_array($q)){
                    ?>
                    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 group relative">
                        <img src="img/<?php echo $row['gambar']; ?>" class="w-full h-40 object-cover">
                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                            <a href="kelola_banner.php?hapus=<?php echo $row['id']; ?>" onclick="return confirm('Hapus banner ini?')" class="bg-red-500 text-white w-10 h-10 rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                        <div class="p-4">
                            <h4 class="font-bold text-gray-800 truncate"><?php echo $row['judul']; ?></h4>
                            <p class="text-xs text-gray-500 truncate"><?php echo $row['deskripsi']; ?></p>
                            <span class="text-[10px] bg-gray-100 px-2 py-1 rounded text-gray-500 mt-2 inline-block">Link: <?php echo $row['link']; ?></span>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>