<?php
include 'koneksi.php';

$query_profil = mysqli_query($koneksi, "SELECT * FROM profil_toko WHERE id=1");
$toko = mysqli_fetch_array($query_profil);

if (isset($_POST['daftar'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $wa = $_POST['no_wa'];
    
    $cek = mysqli_query($koneksi, "SELECT * FROM users WHERE email='$email'");
    if(mysqli_num_rows($cek) > 0){
        echo "<script>alert('Email sudah terdaftar!');</script>";
    } else {
        $simpan = mysqli_query($koneksi, "INSERT INTO users (nama_lengkap, email, password, no_wa) VALUES ('$nama', '$email', '$password', '$wa')");
        if($simpan){
            echo "<script>alert('Pendaftaran Berhasil! Silakan Login.'); window.location='masuk.php';</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - <?php echo $toko['nama_toko']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap'); body { font-family: 'Outfit', sans-serif; background-color: #FFF5F7; }</style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <div class="bg-white w-full max-w-md p-8 md:p-10 rounded-[2.5rem] shadow-2xl shadow-pink-100 border border-white relative overflow-hidden">
        
        <!-- Hiasan Blob -->
        <div class="absolute -top-10 -left-10 w-40 h-40 bg-purple-100 rounded-full blur-3xl opacity-60"></div>
        <div class="absolute bottom-0 right-0 w-32 h-32 bg-pink-100 rounded-full blur-3xl opacity-60"></div>

        <div class="relative z-10">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Buat Akun Baru</h2>
                <p class="text-gray-500 text-sm">Gabung jadi member dan nikmati promo eksklusif.</p>
            </div>

            <form action="" method="POST">
                <div class="mb-4 relative">
                    <i class="fas fa-user absolute left-4 top-4 text-gray-400"></i>
                    <input type="text" name="nama" placeholder="Nama Lengkap" required class="w-full pl-12 pr-4 py-3.5 bg-gray-50 border-transparent rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-pink-500 transition font-medium">
                </div>
                <div class="mb-4 relative">
                    <i class="fas fa-envelope absolute left-4 top-4 text-gray-400"></i>
                    <input type="email" name="email" placeholder="Alamat Email" required class="w-full pl-12 pr-4 py-3.5 bg-gray-50 border-transparent rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-pink-500 transition font-medium">
                </div>
                <div class="mb-4 relative">
                    <i class="fab fa-whatsapp absolute left-4 top-4 text-gray-400"></i>
                    <input type="number" name="no_wa" placeholder="Nomor WhatsApp" required class="w-full pl-12 pr-4 py-3.5 bg-gray-50 border-transparent rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-pink-500 transition font-medium">
                </div>
                <div class="mb-8 relative">
                    <i class="fas fa-lock absolute left-4 top-4 text-gray-400"></i>
                    <input type="password" name="password" placeholder="Buat Password" required class="w-full pl-12 pr-4 py-3.5 bg-gray-50 border-transparent rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-pink-500 transition font-medium">
                </div>
                
                <button type="submit" name="daftar" class="w-full bg-pink-600 hover:bg-pink-700 text-white font-bold py-4 rounded-2xl transition shadow-lg shadow-pink-200 transform hover:-translate-y-1">
                    Daftar Sekarang
                </button>
            </form>

            <div class="mt-8 text-center space-y-4">
                <p class="text-sm text-gray-500">Sudah punya akun? <a href="masuk.php" class="text-gray-900 font-bold hover:underline">Masuk disini</a></p>
                <a href="index.php" class="inline-block text-xs text-gray-400 hover:text-gray-600 font-medium">Kembali ke Beranda</a>
            </div>
        </div>
    </div>

</body>
</html>