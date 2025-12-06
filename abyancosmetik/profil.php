<?php
session_start();
include 'koneksi.php';

// Cek Login Admin
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:login.php"); exit();
}

$admin_user_saat_ini = $_SESSION['username'];

// --- PROSES UPDATE TOKO ---
if (isset($_POST['update_toko'])) {
    $nama   = mysqli_real_escape_string($koneksi, $_POST['nama_toko']);
    $desc   = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $wa     = mysqli_real_escape_string($koneksi, $_POST['no_wa']);
    $maps   = mysqli_real_escape_string($koneksi, $_POST['maps_link']);
    $ig     = mysqli_real_escape_string($koneksi, $_POST['instagram']);
    $fb     = mysqli_real_escape_string($koneksi, $_POST['facebook']);

    $update = mysqli_query($koneksi, "UPDATE profil_toko SET nama_toko='$nama', deskripsi='$desc', alamat='$alamat', no_wa='$wa', maps_link='$maps', instagram='$ig', facebook='$fb' WHERE id=1");

    if ($update) echo "<script>alert('Info Toko Berhasil Diupdate!'); window.location='profil.php';</script>";
    else echo "<script>alert('Gagal update toko.');</script>";
}

// --- PROSES UPDATE AKUN ADMIN ---
if (isset($_POST['update_akun'])) {
    $user_baru = mysqli_real_escape_string($koneksi, $_POST['username']);
    $pass_lama = mysqli_real_escape_string($koneksi, $_POST['pass_lama']);
    $pass_baru = mysqli_real_escape_string($koneksi, $_POST['password']);
    
    if(!empty($user_baru) && !empty($pass_lama) && !empty($pass_baru)){
        // Ambil password lama dari DB
        $cek_pass = mysqli_query($koneksi, "SELECT password FROM admin WHERE username='$admin_user_saat_ini'");
        $data_admin = mysqli_fetch_array($cek_pass);
        
        // Verifikasi Pass Lama (MD5)
        if(md5($pass_lama) == $data_admin['password']){
            $pass_baru_hash = md5($pass_baru);
            $query = "UPDATE admin SET username='$user_baru', password='$pass_baru_hash' WHERE username='$admin_user_saat_ini'";
            
            if(mysqli_query($koneksi, $query)){
                $_SESSION['username'] = $user_baru;
                echo "<script>alert('Password Berhasil Diganti! Silakan login ulang.'); window.location='logout.php';</script>";
            }
        } else {
            echo "<script>alert('Gagal! Password lama salah.');</script>";
        }
    } else {
        echo "<script>alert('Semua kolom wajib diisi!');</script>";
    }
}

// AMBIL DATA
$data_toko = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM profil_toko WHERE id=1"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengaturan Toko & Akun</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap'); body { font-family: 'Outfit', sans-serif; background-color: #F3F4F6; }</style>
</head>
<body class="text-gray-800">

    <nav class="bg-gray-900 text-white p-4 sticky top-0 z-50 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="bg-gray-800 p-2 rounded-lg"><i class="fas fa-cogs text-yellow-500"></i></div>
                <h1 class="font-bold text-lg tracking-wide">Pengaturan</h1>
            </div>
            <a href="admin.php" class="bg-gray-800 hover:bg-gray-700 px-4 py-2 rounded-lg text-xs font-bold transition"><i class="fas fa-arrow-left mr-2"></i> Dashboard</a>
        </div>
    </nav>

    <div class="container mx-auto p-6">
        <div class="flex flex-col lg:flex-row gap-8">
            
            <!-- KOLOM KIRI: INFO TOKO -->
            <div class="lg:w-2/3">
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                    <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2"><i class="fas fa-store text-blue-500"></i> Informasi Toko</h3>
                    
                    <form action="" method="POST">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Nama Toko</label>
                                <input type="text" name="nama_toko" value="<?php echo $data_toko['nama_toko']; ?>" class="w-full p-3 bg-gray-50 border-transparent rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500 transition">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">WhatsApp Admin</label>
                                <input type="number" name="no_wa" value="<?php echo $data_toko['no_wa']; ?>" class="w-full p-3 bg-gray-50 border-transparent rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500 transition">
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Deskripsi Singkat</label>
                            <textarea name="deskripsi" rows="2" class="w-full p-3 bg-gray-50 border-transparent rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500 transition"><?php echo $data_toko['deskripsi']; ?></textarea>
                        </div>

                        <div class="mb-6">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Alamat Lengkap</label>
                            <textarea name="alamat" rows="3" class="w-full p-3 bg-gray-50 border-transparent rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500 transition"><?php echo $data_toko['alamat']; ?></textarea>
                        </div>

                        <div class="mb-6">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Link Google Maps (Embed)</label>
                            <textarea name="maps_link" rows="3" class="w-full p-3 bg-gray-50 border-transparent rounded-xl text-xs font-mono text-gray-500 focus:bg-white focus:ring-2 focus:ring-blue-500 transition"><?php echo $data_toko['maps_link']; ?></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Instagram Link</label>
                                <input type="text" name="instagram" value="<?php echo $data_toko['instagram']; ?>" class="w-full p-3 bg-gray-50 border-transparent rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500 transition">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Facebook Link</label>
                                <input type="text" name="facebook" value="<?php echo $data_toko['facebook']; ?>" class="w-full p-3 bg-gray-50 border-transparent rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500 transition">
                            </div>
                        </div>

                        <button type="submit" name="update_toko" class="bg-blue-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg text-sm">Simpan Info Toko</button>
                    </form>
                </div>
            </div>

            <!-- KOLOM KANAN: KEAMANAN ADMIN -->
            <div class="lg:w-1/3">
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 sticky top-24">
                    <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2"><i class="fas fa-user-shield text-red-500"></i> Keamanan Akun</h3>
                    
                    <form action="" method="POST">
                        <div class="mb-5">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Username Admin</label>
                            <input type="text" name="username" value="<?php echo $_SESSION['username']; ?>" required class="w-full p-3 bg-gray-50 border-transparent rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-red-500 transition">
                        </div>
                        
                        <div class="mb-5">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Password Lama</label>
                            <input type="password" name="pass_lama" placeholder="Wajib diisi..." required class="w-full p-3 bg-gray-50 border-transparent rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-red-500 transition border border-red-100">
                        </div>

                        <div class="mb-6">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Password Baru</label>
                            <input type="password" name="password" placeholder="Buat password baru..." required class="w-full p-3 bg-gray-50 border-transparent rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-red-500 transition">
                        </div>

                        <button type="submit" name="update_akun" class="w-full bg-red-600 text-white py-3 rounded-xl font-bold hover:bg-red-700 transition shadow-lg text-sm">Update Password</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</body>
</html>