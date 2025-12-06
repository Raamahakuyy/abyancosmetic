<?php
session_start();
include 'koneksi.php';

// Cek Login Admin
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:login.php"); exit();
}

$admin_user_saat_ini = $_SESSION['username'];

if (isset($_POST['update_admin'])) {
    $user_baru = mysqli_real_escape_string($koneksi, $_POST['username']);
    $pass_lama = mysqli_real_escape_string($koneksi, $_POST['pass_lama']); // Input pass lama
    $pass_baru = mysqli_real_escape_string($koneksi, $_POST['password']);
    
    if(!empty($user_baru) && !empty($pass_lama) && !empty($pass_baru)){
        
        // 1. Ambil password lama dari database untuk verifikasi
        $cek_pass = mysqli_query($koneksi, "SELECT password FROM admin WHERE username='$admin_user_saat_ini'");
        $data_admin = mysqli_fetch_array($cek_pass);
        
        // Enkripsi input password lama (MD5) untuk dicocokkan
        $pass_lama_md5 = md5($pass_lama);

        // 2. Cek apakah password lama cocok?
        if($pass_lama_md5 == $data_admin['password']){
            
            // Jika cocok, baru update ke password baru
            $pass_baru_hash = md5($pass_baru);
            $query = "UPDATE admin SET username='$user_baru', password='$pass_baru_hash' WHERE username='$admin_user_saat_ini'";
            
            if(mysqli_query($koneksi, $query)){
                $_SESSION['username'] = $user_baru;
                echo "<script>alert('Berhasil! Password telah diganti. Silakan login ulang untuk keamanan.'); window.location='logout.php';</script>";
            } else {
                echo "<script>alert('Gagal update database.');</script>";
            }
            
        } else {
            // Jika password lama salah
            echo "<script>alert('Gagal! Password lama yang Anda masukkan salah.');</script>";
        }

    } else {
        echo "<script>alert('Semua kolom wajib diisi!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keamanan Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap'); body { font-family: 'Outfit', sans-serif; background-color: #F3F4F6; }</style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <div class="bg-white w-full max-w-md p-8 rounded-[2.5rem] shadow-2xl shadow-gray-200 border border-white relative overflow-hidden">
        
        <!-- Header Hiasan -->
        <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-b from-gray-900 to-transparent -z-10"></div>
        
        <div class="text-center mb-8 pt-4">
            <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4 text-gray-800 text-3xl shadow-lg border-4 border-gray-50">
                <i class="fas fa-user-shield"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Keamanan Akun</h2>
            <p class="text-sm text-gray-500">Perbarui akses login dashboard admin.</p>
        </div>

        <form action="" method="POST" class="space-y-5">
            
            <!-- Username Baru -->
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2 ml-1">Username Baru</label>
                <div class="relative">
                    <i class="fas fa-user absolute left-4 top-3.5 text-gray-400"></i>
                    <input type="text" name="username" value="<?php echo $_SESSION['username']; ?>" required class="w-full pl-12 pr-4 py-3 bg-gray-50 border-transparent rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-gray-800 transition font-medium shadow-sm">
                </div>
            </div>

            <!-- Password Lama (Verifikasi) -->
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2 ml-1">Password Lama</label>
                <div class="relative">
                    <i class="fas fa-key absolute left-4 top-3.5 text-gray-400"></i>
                    <input type="password" name="pass_lama" placeholder="Masukkan password saat ini" required class="w-full pl-12 pr-4 py-3 bg-gray-50 border-transparent rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-red-500 transition font-medium shadow-sm">
                </div>
            </div>

            <!-- Password Baru -->
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2 ml-1">Password Baru</label>
                <div class="relative">
                    <i class="fas fa-lock absolute left-4 top-3.5 text-gray-400"></i>
                    <input type="text" name="password" placeholder="Buat password baru" required class="w-full pl-12 pr-4 py-3 bg-gray-50 border-transparent rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-green-500 transition font-medium shadow-sm">
                </div>
                <p class="text-[10px] text-gray-400 mt-2 ml-1 flex items-center gap-1"><i class="fas fa-info-circle"></i> Pastikan password sulit ditebak.</p>
            </div>

            <div class="flex gap-3 pt-4">
                <a href="admin.php" class="flex-1 py-3.5 rounded-xl font-bold text-center text-gray-500 hover:bg-gray-100 transition text-sm">Batal</a>
                <button type="submit" name="update_admin" class="flex-1 bg-gray-900 text-white py-3.5 rounded-xl font-bold text-sm hover:bg-gray-800 transition shadow-lg transform hover:-translate-y-1">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</body>
</html>