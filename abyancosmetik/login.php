<?php
session_start();
include 'koneksi.php';

// Jika sudah login, lempar langsung ke admin
if (isset($_SESSION['status']) && $_SESSION['status'] == "login") {
    header("location:admin.php");
    exit();
}

$pesan_error = "";

if (isset($_POST['login'])) {
    // 1. SECURITY: Gunakan mysqli_real_escape_string untuk mencegah SQL Injection
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = md5($_POST['password']); // Tetap pakai MD5 (bisa diupgrade ke password_hash nanti)

    // Cek User
    $cek = mysqli_query($koneksi, "SELECT * FROM admin WHERE username='$username' AND password='$password'");

    if (mysqli_num_rows($cek) > 0) {
        $_SESSION['username'] = $username;
        $_SESSION['status'] = "login";
        header("location:admin.php");
    } else {
        $pesan_error = "Username atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Owner - Abyan Cosmetik</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

    <!-- Card Container (Split Design) -->
    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden flex max-w-4xl w-full">
        
        <!-- BAGIAN KIRI: Gambar Estetik (Disembunyikan di HP) -->
        <div class="hidden md:block w-1/2 bg-cover bg-center relative" style="background-image: url('https://images.unsplash.com/photo-1616683693504-3ea7e9ad6fec?ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80');">
            <div class="absolute inset-0 bg-pink-900 bg-opacity-40 flex items-center justify-center">
                <div class="text-white text-center p-8">
                    <h2 class="text-4xl font-bold mb-2">Abyan Cosmetik</h2>
                    <p class="text-pink-100 tracking-wider">Manage your beauty empire.</p>
                </div>
            </div>
        </div>

        <!-- BAGIAN KANAN: Form Login -->
        <div class="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-center">
            
            <div class="text-center mb-8">
                <div class="inline-block p-3 rounded-full bg-pink-100 text-pink-600 mb-4">
                    <i class="fas fa-user-shield text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800">Selamat Datang Kembali!</h3>
                <p class="text-gray-500 text-sm">Silakan login untuk mengelola toko.</p>
            </div>

            <!-- Alert Error -->
            <?php if($pesan_error != "") { ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded text-sm relative" role="alert">
                    <strong class="font-bold">Oops!</strong>
                    <span class="block sm:inline"><?php echo $pesan_error; ?></span>
                </div>
            <?php } ?>

            <form action="" method="POST">
                
                <div class="mb-5">
                    <label class="block text-gray-700 font-bold mb-2 text-sm">Username</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <input type="text" name="username" class="w-full pl-10 pr-3 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 transition bg-gray-50" placeholder="Masukkan username" required>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-2 text-sm">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" name="password" id="passInput" class="w-full pl-10 pr-10 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 transition bg-gray-50" placeholder="••••••••" required>
                        <!-- Tombol Mata (Show Password) -->
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer" onclick="togglePassword()">
                            <i class="fas fa-eye text-gray-400 hover:text-pink-600" id="eyeIcon"></i>
                        </div>
                    </div>
                </div>

                <button type="submit" name="login" class="w-full bg-pink-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-pink-700 transform hover:-translate-y-1 transition duration-200 shadow-lg">
                    Masuk Dashboard
                </button>

            </form>

            <div class="mt-8 text-center">
                <a href="index.php" class="text-sm text-gray-400 hover:text-pink-600 flex items-center justify-center gap-2 transition">
                    <i class="fas fa-arrow-left"></i> Kembali ke Website Utama
                </a>
            </div>

        </div>
    </div>

    <!-- Script Javascript untuk Show/Hide Password -->
    <script>
        function togglePassword() {
            const passInput = document.getElementById('passInput');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passInput.type === 'password') {
                passInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    </script>

</body>
</html>