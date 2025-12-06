<?php
include 'koneksi.php';
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:login.php"); exit();
}

if (isset($_POST['simpan'])) {
    $nama      = $_POST['nama_produk'];
    $kategori  = $_POST['kategori'];
    $label     = $_POST['label_sticker'];
    $harga     = $_POST['harga'];
    $harga_coret = $_POST['harga_coret'];
    $stok      = $_POST['stok'];
    $deskripsi = $_POST['deskripsi'];
    $varian    = $_POST['varian']; // Data Varian

    // 1. Upload Foto Utama
    $nama_foto_utama = 'no-image.jpg';
    if ($_FILES['foto_utama']['name'] != '') {
        $nama_foto_utama = time() . '_MAIN_' . $_FILES['foto_utama']['name'];
        move_uploaded_file($_FILES['foto_utama']['tmp_name'], './img/' . $nama_foto_utama);
    }

    // 2. Simpan Data Produk
    $insert = mysqli_query($koneksi, "INSERT INTO produk (nama_produk, kategori, label_sticker, harga, harga_coret, stok, deskripsi, varian, gambar) VALUES ('$nama', '$kategori', '$label', '$harga', '$harga_coret', '$stok', '$deskripsi', '$varian', '$nama_foto_utama')");

    if ($insert) {
        $id_produk_baru = mysqli_insert_id($koneksi); // Ambil ID produk yang barusan dibuat

        // 3. Upload Foto-Foto Tambahan (Gallery)
        $jumlah_foto = count($_FILES['foto_galeri']['name']);
        for ($i = 0; $i < $jumlah_foto; $i++) {
            $nama_file = $_FILES['foto_galeri']['name'][$i];
            $tmp_file = $_FILES['foto_galeri']['tmp_name'][$i];

            if ($nama_file != "") {
                $nama_baru = time() . "_GALERI_" . $i . "_" . $nama_file;
                move_uploaded_file($tmp_file, './img/' . $nama_baru);
                mysqli_query($koneksi, "INSERT INTO produk_foto (id_produk, nama_foto) VALUES ('$id_produk_baru', '$nama_baru')");
            }
        }

        echo "<script>alert('Produk berhasil ditambahkan!'); window.location='admin.php';</script>";
    } else {
        echo "Gagal: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Produk Lengkap</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 py-10 flex justify-center">

    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-2xl">
        <h2 class="text-2xl font-bold mb-6 text-center">Tambah Produk Baru</h2>

        <form action="" method="POST" enctype="multipart/form-data">
            
            <div class="mb-4">
                <label class="block font-bold mb-1">Nama Produk</label>
                <input type="text" name="nama_produk" required class="w-full border p-2 rounded">
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block font-bold mb-1">Kategori</label>
                    <select name="kategori" class="w-full border p-2 rounded">
                        <option value="Kosmetik">Kosmetik</option>
                        <option value="Pakaian Dalam">Pakaian Dalam</option>
                        <option value="Pakaian Anak">Pakaian Anak</option>
                        <option value="Perlengkapan Bayi">Perlengkapan Bayi</option>
                        <option value="Mainan Anak">Mainan Anak</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold mb-1">Label</label>
                    <select name="label_sticker" class="w-full border p-2 rounded">
                        <option value="">- Polos -</option>
                        <option value="Best Seller">Best Seller</option>
                        <option value="New Arrival">New Arrival</option>
                        <option value="Flash Sale">Flash Sale</option>
                    </select>
                </div>
            </div>

            <!-- VARIAN PRODUK -->
            <div class="mb-4 bg-yellow-50 p-4 rounded border border-yellow-200">
                <label class="block font-bold mb-1 text-yellow-800">Varian Produk (Opsional)</label>
                <input type="text" name="varian" class="w-full border p-2 rounded" placeholder="Contoh: Merah, Biru, Hijau (Pisahkan dengan koma)">
                <p class="text-xs text-gray-500 mt-1">*Biarkan kosong jika tidak ada varian.</p>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block font-bold mb-1">Harga (Rp)</label>
                    <input type="number" name="harga" required class="w-full border p-2 rounded">
                </div>
                <div>
                    <label class="block font-bold mb-1 text-red-500">Harga Coret (Rp)</label>
                    <input type="number" name="harga_coret" class="w-full border p-2 rounded">
                </div>
            </div>

            <div class="mb-4">
                <label class="block font-bold mb-1">Stok</label>
                <input type="number" name="stok" required class="w-full border p-2 rounded">
            </div>

            <!-- FOTO UTAMA -->
            <div class="mb-4">
                <label class="block font-bold mb-1">Foto Utama</label>
                <input type="file" name="foto_utama" required class="w-full border p-2 rounded bg-gray-50">
            </div>

            <!-- FOTO GALERI (BANYAK) -->
            <div class="mb-4 bg-blue-50 p-4 rounded border border-blue-200">
                <label class="block font-bold mb-1 text-blue-800">Foto Tambahan (Gallery)</label>
                <input type="file" name="foto_galeri[]" multiple class="w-full border p-2 rounded bg-white">
                <p class="text-xs text-gray-500 mt-1">*Bisa pilih lebih dari 1 file sekaligus (Ctrl + Klik).</p>
            </div>

            <div class="mb-6">
                <label class="block font-bold mb-1">Deskripsi</label>
                <textarea name="deskripsi" rows="4" class="w-full border p-2 rounded"></textarea>
            </div>

            <div class="flex justify-between">
                <a href="admin.php" class="bg-gray-500 text-white px-4 py-2 rounded">Batal</a>
                <button type="submit" name="simpan" class="bg-pink-600 text-white px-4 py-2 rounded font-bold">Simpan Produk</button>
            </div>

        </form>
    </div>
</body>
</html>