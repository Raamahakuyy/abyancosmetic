<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_status']) || $_SESSION['user_status'] != "login_member") {
    header("location:masuk.php"); exit();
}

$id_user = $_SESSION['user_id'];

// Update Profil
if(isset($_POST['update_profil'])){
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $wa = mysqli_real_escape_string($koneksi, $_POST['no_wa']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $update = mysqli_query($koneksi, "UPDATE users SET nama_lengkap='$nama', no_wa='$wa', alamat='$alamat' WHERE id='$id_user'");
    if($update){ $_SESSION['user_nama'] = $nama; $_SESSION['user_wa'] = $wa; echo "<script>alert('Profil berhasil diperbarui!'); window.location='akun.php';</script>"; }
}

// Ganti Password
if(isset($_POST['ganti_pass'])){
    $pass_lama = $_POST['pass_lama'];
    $pass_baru = $_POST['pass_baru'];
    $konfirmasi = $_POST['konfirmasi_pass'];
    $cek = mysqli_query($koneksi, "SELECT password FROM users WHERE id='$id_user'");
    $data = mysqli_fetch_assoc($cek);

    if(password_verify($pass_lama, $data['password'])){
        if($pass_baru == $konfirmasi){
            $hash_baru = password_hash($pass_baru, PASSWORD_DEFAULT);
            mysqli_query($koneksi, "UPDATE users SET password='$hash_baru' WHERE id='$id_user'");
            echo "<script>alert('Password diganti! Login ulang.'); window.location='keluar.php';</script>";
        } else { echo "<script>alert('Konfirmasi password salah!');</script>"; }
    } else { echo "<script>alert('Password lama salah!');</script>"; }
}

$query_user = mysqli_query($koneksi, "SELECT * FROM users WHERE id='$id_user'");
$user = mysqli_fetch_array($query_user);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Akun Saya</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap'); body { font-family: 'Outfit', sans-serif; background-color: #FFF5F7; }</style>
</head>
<body class="text-gray-800">

    <nav class="bg-white/90 backdrop-blur-md sticky top-0 z-50 border-b border-white/50 shadow-sm">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="index.php" class="font-bold text-xl tracking-tight text-gray-900 uppercase flex items-center gap-2">
                <div class="bg-pink-50 p-1.5 rounded-lg text-pink-600"><i class="fas fa-store"></i></div> Toko
            </a>
            <div class="flex items-center gap-4">
                <span class="text-sm font-bold text-gray-600 hidden md:block">Halo, <?php echo $user['nama_lengkap']; ?></span>
                <a href="keluar.php" class="bg-rose-50 text-rose-600 px-4 py-2 rounded-full text-sm font-bold hover:bg-rose-100 transition"><i class="fas fa-sign-out-alt mr-1"></i> Keluar</a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-6 py-10">
        <div class="flex flex-col lg:flex-row gap-10">
            
            <!-- SIDEBAR KIRI -->
            <div class="lg:w-1/3 space-y-8">
                <!-- Profil Card -->
                <div class="bg-white p-8 rounded-[2rem] shadow-lg shadow-pink-100/50 border border-white text-center relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-24 bg-gradient-to-r from-pink-400 to-rose-400"></div>
                    <div class="relative z-10 -mt-12 mb-4">
                        <div class="w-24 h-24 bg-white rounded-full mx-auto flex items-center justify-center text-4xl text-pink-500 font-bold border-4 border-white shadow-md">
                            <?php echo substr($user['nama_lengkap'], 0, 1); ?>
                        </div>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900"><?php echo $user['nama_lengkap']; ?></h2>
                    <p class="text-sm text-gray-500 mb-6"><?php echo $user['email']; ?></p>
                    
                    <form action="" method="POST" class="text-left space-y-4">
                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase block mb-1">Nama</label>
                            <input type="text" name="nama" value="<?php echo $user['nama_lengkap']; ?>" class="w-full p-3 bg-gray-50 rounded-xl text-sm border-transparent focus:bg-white focus:ring-2 focus:ring-pink-500">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase block mb-1">WhatsApp</label>
                            <input type="number" name="no_wa" value="<?php echo $user['no_wa']; ?>" class="w-full p-3 bg-gray-50 rounded-xl text-sm border-transparent focus:bg-white focus:ring-2 focus:ring-pink-500">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase block mb-1">Alamat Utama</label>
                            <textarea name="alamat" rows="3" class="w-full p-3 bg-gray-50 rounded-xl text-sm border-transparent focus:bg-white focus:ring-2 focus:ring-pink-500"><?php echo $user['alamat']; ?></textarea>
                        </div>
                        <button type="submit" name="update_profil" class="w-full bg-gray-900 text-white py-3 rounded-xl font-bold text-sm hover:bg-gray-800 transition shadow-lg">Simpan Profil</button>
                    </form>
                </div>

                <!-- Password Card -->
                <div class="bg-white p-8 rounded-[2rem] shadow-lg shadow-pink-100/50 border border-white">
                    <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-lock text-pink-500"></i> Ganti Password</h3>
                    <form action="" method="POST" class="space-y-4">
                        <input type="password" name="pass_lama" placeholder="Password Lama" class="w-full p-3 bg-gray-50 rounded-xl text-sm border-transparent focus:bg-white focus:ring-2 focus:ring-pink-500">
                        <input type="password" name="pass_baru" placeholder="Password Baru" class="w-full p-3 bg-gray-50 rounded-xl text-sm border-transparent focus:bg-white focus:ring-2 focus:ring-pink-500">
                        <input type="password" name="konfirmasi_pass" placeholder="Ulangi Password" class="w-full p-3 bg-gray-50 rounded-xl text-sm border-transparent focus:bg-white focus:ring-2 focus:ring-pink-500">
                        <button type="submit" name="ganti_pass" class="w-full bg-pink-50 text-pink-600 py-3 rounded-xl font-bold text-sm hover:bg-pink-100 transition">Update Password</button>
                    </form>
                </div>
            </div>

            <!-- KANAN: RIWAYAT PESANAN -->
            <div class="lg:w-2/3">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Riwayat Pesanan</h2>
                    <span class="bg-pink-100 text-pink-600 text-xs font-bold px-3 py-1 rounded-full">Terbaru</span>
                </div>

                <div class="space-y-6">
                    <?php
                    $q_order = mysqli_query($koneksi, "SELECT * FROM pesanan WHERE user_id='$id_user' ORDER BY id DESC");
                    if(mysqli_num_rows($q_order) > 0){
                        while($row = mysqli_fetch_array($q_order)){
                            $statusClass = "bg-gray-100 text-gray-600";
                            if($row['status'] == 'Baru') $statusClass = "bg-blue-50 text-blue-600 border border-blue-100";
                            if($row['status'] == 'Menunggu Konfirmasi') $statusClass = "bg-orange-50 text-orange-600 border border-orange-100";
                            if($row['status'] == 'Proses') $statusClass = "bg-yellow-50 text-yellow-600 border border-yellow-100";
                            if($row['status'] == 'Selesai') $statusClass = "bg-green-50 text-green-600 border border-green-100";
                            if($row['status'] == 'Batal') $statusClass = "bg-red-50 text-red-600 border border-red-100";
                            $is_instant = ($row['pengiriman'] == 'Instant (Gojek/Grab)');
                    ?>
                    <div class="bg-white rounded-3xl shadow-sm border border-white hover:shadow-lg transition p-6 group">
                        <div class="flex justify-between items-center mb-4 pb-4 border-b border-gray-50">
                            <div class="text-sm">
                                <span class="font-bold text-gray-900 block"><?php echo $row['invoice_id']; ?></span>
                                <span class="text-xs text-gray-400"><?php echo date('d M Y', strtotime($row['tanggal'])); ?></span>
                            </div>
                            <span class="px-4 py-1.5 rounded-full text-xs font-bold <?php echo $statusClass; ?>"><?php echo $row['status']; ?></span>
                        </div>
                        
                        <div class="flex flex-col md:flex-row justify-between gap-6">
                            <div>
                                <h4 class="font-bold text-gray-800 mb-1"><?php echo $row['nama_produk']; ?></h4>
                                <p class="text-sm text-gray-500 mb-3">Total: <span class="font-bold text-pink-600">Rp <?php echo number_format($row['total_bayar']); ?></span></p>
                                <div class="flex gap-2">
                                    <span class="text-[10px] bg-gray-100 px-2 py-1 rounded font-bold text-gray-500 uppercase tracking-wide"><?php echo $row['pengiriman']; ?></span>
                                    <?php if(!empty($row['no_resi'])) { ?>
                                        <span class="text-[10px] bg-green-50 text-green-700 px-2 py-1 rounded font-bold border border-green-100 select-all cursor-copy" title="Copy Resi"><?php echo $row['no_resi']; ?></span>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="flex gap-2 items-end">
                                <?php if($row['status'] == 'Baru') { ?>
                                    <a href="bayar.php?inv=<?php echo $row['invoice_id']; ?>" class="bg-pink-600 text-white px-5 py-2.5 rounded-xl text-xs font-bold hover:bg-pink-700 transition shadow-lg shadow-pink-200">Bayar</a>
                                <?php } ?>
                                <?php if(!empty($row['no_resi']) && !$is_instant) { ?>
                                    <a href="https://cekresi.com/?noresi=<?php echo $row['no_resi']; ?>" target="_blank" class="bg-gray-900 text-white px-4 py-2.5 rounded-xl text-xs font-bold hover:bg-black transition">Lacak</a>
                                <?php } ?>
                                <a href="cetak_invoice.php?inv=<?php echo $row['invoice_id']; ?>" target="_blank" class="bg-white border border-gray-200 text-gray-500 px-4 py-2.5 rounded-xl text-xs font-bold hover:border-pink-400 hover:text-pink-600 transition">Invoice</a>
                            </div>
                        </div>
                    </div>
                    <?php } } else { echo "<div class='text-center py-16 bg-white rounded-[2rem] border border-dashed border-gray-200'><i class='fas fa-receipt text-4xl text-gray-200 mb-3'></i><p class='text-gray-400'>Belum ada pesanan.</p></div>"; } ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>