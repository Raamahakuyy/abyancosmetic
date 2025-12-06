<?php
session_start();
include 'koneksi.php';

// AMBIL DATA TOKO
$query_profil = mysqli_query($koneksi, "SELECT * FROM profil_toko WHERE id=1");
$toko = mysqli_fetch_array($query_profil);

// Jika sudah login, lempar ke akun
if (isset($_SESSION['user_status']) && $_SESSION['user_status'] == "login_member") {
    header("location:akun.php"); exit();
}

if (isset($_POST['masuk'])) {
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];

    $cek = mysqli_query($koneksi, "SELECT * FROM users WHERE email='$email'");
    
    if (mysqli_num_rows($cek) > 0) {
        $data = mysqli_fetch_assoc($cek);
        if (password_verify($password, $data['password'])) {
            $_SESSION['user_status'] = "login_member";
            $_SESSION['user_id'] = $data['id'];
            $_SESSION['user_nama'] = $data['nama_lengkap'];
            $_SESSION['user_wa'] = $data['no_wa'];
            echo "<script>alert('Selamat Datang, ".$data['nama_lengkap']."!'); window.location='index.php';</script>";
        } else {
            echo "<script>alert('Password Salah!');</script>";
        }
    } else {
        echo "<script>alert('Email tidak ditemukan!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - <?php echo $toko['nama_toko']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap'); body { font-family: 'Outfit', sans-serif; background-color: #FFF5F7; }</style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <div class="bg-white w-full max-w-md p-8 md:p-10 rounded-[2.5rem] shadow-2xl shadow-pink-100 border border-white relative overflow-hidden">
        <!-- Hiasan Blob -->
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-pink-100 rounded-full blur-3xl opacity-60"></div>
        <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-purple-100 rounded-full blur-3xl opacity-60"></div>

        <div class="relative z-10">
            <div class="text-center mb-8">
                <a href="index.php" class="inline-block mb-4 bg-pink-50 text-pink-600 p-3 rounded-2xl">
                    <i class="fas fa-sparkles text-2xl"></i>
                </a>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Selamat Datang!</h2>
                <p class="text-gray-500 text-sm">Silakan masuk untuk mulai belanja.</p>
            </div>

            <form action="" method="POST">
                <div class="mb-5 relative">
                    <i class="fas fa-envelope absolute left-4 top-4 text-gray-400"></i>
                    <input type="email" name="email" placeholder="Alamat Email" required class="w-full pl-12 pr-4 py-3.5 bg-gray-50 border-transparent rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-pink-500 transition font-medium">
                </div>
                <div class="mb-8 relative">
                    <i class="fas fa-lock absolute left-4 top-4 text-gray-400"></i>
                    <input type="password" name="password" placeholder="Password" required class="w-full pl-12 pr-4 py-3.5 bg-gray-50 border-transparent rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-pink-500 transition font-medium">
                </div>
                
                <button type="submit" name="masuk" class="w-full bg-gray-900 hover:bg-pink-600 text-white font-bold py-4 rounded-2xl transition shadow-lg shadow-gray-200 hover:shadow-pink-200 transform hover:-translate-y-1">
                    Masuk Sekarang
                </button>
            </form>

            <div class="mt-8 text-center space-y-4">
                <p class="text-sm text-gray-500">Belum punya akun? <a href="daftar.php" class="text-pink-600 font-bold hover:underline">Daftar disini</a></p>
                <a href="index.php" class="inline-block text-xs text-gray-400 hover:text-gray-600 font-medium">Kembali ke Beranda</a>
            </div>
        </div>
    </div>

</body>
</html>