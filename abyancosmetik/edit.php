<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:login.php"); exit();
}

$id = $_GET['id'];
$query = mysqli_query($koneksi, "SELECT * FROM produk WHERE id='$id'");
$data  = mysqli_fetch_array($query);

if (isset($_POST['update'])) {
    $nama      = $_POST['nama_produk'];
    $kategori  = $_POST['kategori'];
    $label     = $_POST['label_sticker'];
    $harga     = $_POST['harga'];
    $harga_coret = $_POST['harga_coret'];
    $stok      = $_POST['stok'];
    $deskripsi = $_POST['deskripsi'];
    
    $nama_foto   = $_FILES['foto']['name'];
    $sumber_foto = $_FILES['foto']['tmp_name'];
    $folder      = './img/';

    if ($nama_foto != '') {
        if($data['gambar'] != 'no-image.jpg' && file_exists('./img/'.$data['gambar'])){
            unlink('./img/'.$data['gambar']);
        }
        $nama_file_baru = time() . '-' . $nama_foto;
        move_uploaded_file($sumber_foto, $folder . $nama_file_baru);
        
        $update = mysqli_query($koneksi, "UPDATE produk SET nama_produk='$nama', kategori='$kategori', label_sticker='$label', harga='$harga', harga_coret='$harga_coret', stok='$stok', deskripsi='$deskripsi', gambar='$nama_file_baru' WHERE id='$id'");
    } else {
        $update = mysqli_query($koneksi, "UPDATE produk SET nama_produk='$nama', kategori='$kategori', label_sticker='$label', harga='$harga', harga_coret='$harga_coret', stok='$stok', deskripsi='$deskripsi' WHERE id='$id'");
    }

    if ($update) {
        echo "<script>alert('Data berhasil diupdate!'); window.location='admin.php';</script>";
    } else {
        echo "Gagal: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans flex items-center justify-center min-h-screen py-10">

    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-lg">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center border-b pb-4">Edit Produk</h2>

        <form action="" method="POST" enctype="multipart/form-data">
            
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Nama Produk</label>
                <input type="text" name="nama_produk" value="<?php echo $data['nama_produk']; ?>" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-pink-500">
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Kategori</label>
                    <!-- KATEGORI BARU DISINI JUGA -->
                    <select name="kategori" required class="w-full px-3 py-2 border rounded-lg bg-white">
                        <option value="Kosmetik" <?php if($data['kategori']=='Kosmetik') echo 'selected'; ?>>Kosmetik</option>
                        <option value="Pakaian Dalam" <?php if($data['kategori']=='Pakaian Dalam') echo 'selected'; ?>>Pakaian Dalam</option>
                        <option value="Pakaian Anak" <?php if($data['kategori']=='Pakaian Anak') echo 'selected'; ?>>Pakaian Anak</option>
                        <option value="Perlengkapan Bayi" <?php if($data['kategori']=='Perlengkapan Bayi') echo 'selected'; ?>>Perlengkapan Bayi</option>
                        <option value="Mainan Anak" <?php if($data['kategori']=='Mainan Anak') echo 'selected'; ?>>Mainan Anak</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Label Stiker</label>
                    <select name="label_sticker" class="w-full px-3 py-2 border rounded-lg bg-white">
                        <option value="" <?php if($data['label_sticker']=='') echo 'selected'; ?>>- Polos -</option>
                        <option value="Best Seller" <?php if($data['label_sticker']=='Best Seller') echo 'selected'; ?>>🔥 Best Seller</option>
                        <option value="New Arrival" <?php if($data['label_sticker']=='New Arrival') echo 'selected'; ?>>✨ New Arrival</option>
                        <option value="Flash Sale" <?php if($data['label_sticker']=='Flash Sale') echo 'selected'; ?>>⚡ Flash Sale</option>
                        <option value="Cuci Gudang" <?php if($data['label_sticker']=='Cuci Gudang') echo 'selected'; ?>>📦 Cuci Gudang</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Harga Jual (Rp)</label>
                    <input type="number" name="harga" value="<?php echo $data['harga']; ?>" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-pink-500">
                </div>
                <div>
                    <label class="block text-gray-700 font-bold mb-2 text-red-500">Harga Coret (Rp)</label>
                    <input type="number" name="harga_coret" value="<?php echo $data['harga_coret']; ?>" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-red-500 bg-red-50">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Stok</label>
                <input type="number" name="stok" value="<?php echo $data['stok']; ?>" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-pink-500">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Foto Saat Ini</label>
                <div class="flex items-center gap-4">
                    <img src="img/<?php echo $data['gambar']; ?>" class="w-16 h-16 object-cover rounded border">
                    <div class="w-full">
                        <input type="file" name="foto" accept="image/*" class="w-full px-3 py-2 border rounded-lg bg-gray-50 text-sm">
                        <p class="text-xs text-gray-500 mt-1">Upload foto baru untuk mengganti.</p>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-2">Deskripsi</label>
                <textarea name="deskripsi" rows="4" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-pink-500"><?php echo $data['deskripsi']; ?></textarea>
            </div>

            <div class="flex justify-between">
                <a href="admin.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">Batal</a>
                <button type="submit" name="update" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-lg">
                    <i class="fas fa-save mr-1"></i> Update Perubahan
                </button>
            </div>

        </form>
    </div>

</body>
</html>