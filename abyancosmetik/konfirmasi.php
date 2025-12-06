<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_status']) || $_SESSION['user_status'] != "login_member") {
    header("location:masuk.php"); exit();
}

$inv = $_GET['inv'];
$id_user = $_SESSION['user_id'];
$cek = mysqli_query($koneksi, "SELECT * FROM pesanan WHERE invoice_id='$inv' AND user_id='$id_user'");
if(mysqli_num_rows($cek) == 0){ header("location:akun.php"); exit(); }

if(isset($_POST['kirim_bukti'])){
    $nama_file = $_FILES['bukti']['name'];
    $sumber = $_FILES['bukti']['tmp_name'];
    if($nama_file != ''){
        $nama_baru = "BUKTI-" . $inv . "-" . time() . ".jpg";
        move_uploaded_file($sumber, 'img/' . $nama_baru);
        mysqli_query($koneksi, "UPDATE pesanan SET bukti_bayar='$nama_baru', status='Menunggu Konfirmasi' WHERE invoice_id='$inv'");
        echo "<script>alert('Bukti terkirim!'); window.location='akun.php';</script>";
    } else { echo "<script>alert('Pilih foto dulu!');</script>"; }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Pembayaran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap'); body { font-family: 'Outfit', sans-serif; background-color: #F3F4F6; }</style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <div class="bg-white w-full max-w-md p-8 rounded-[2.5rem] shadow-2xl shadow-gray-200 border border-white relative overflow-hidden text-center">
        
        <div class="w-20 h-20 bg-pink-50 rounded-full flex items-center justify-center mx-auto mb-6 text-pink-500 text-3xl shadow-inner">
            <i class="fas fa-camera"></i>
        </div>
        
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Upload Bukti</h2>
        <p class="text-gray-500 text-sm mb-8">Invoice: <span class="font-mono font-bold text-gray-800 bg-gray-100 px-2 py-1 rounded"><?php echo $inv; ?></span></p>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-8 relative group">
                <div class="border-2 border-dashed border-gray-300 rounded-3xl p-8 transition group-hover:border-pink-400 group-hover:bg-pink-50 cursor-pointer">
                    <input type="file" name="bukti" accept="image/*" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-300 group-hover:text-pink-500 transition mb-2 block"></i>
                    <span class="text-sm text-gray-500 group-hover:text-pink-600 font-medium">Ketuk untuk pilih foto</span>
                </div>
            </div>

            <button type="submit" name="kirim_bukti" class="w-full bg-gray-900 hover:bg-pink-600 text-white font-bold py-4 rounded-2xl transition shadow-lg transform hover:-translate-y-1 flex items-center justify-center gap-2">
                <i class="fas fa-paper-plane"></i> Kirim Sekarang
            </button>
            
            <a href="akun.php" class="block mt-6 text-xs text-gray-400 hover:text-gray-600 font-bold uppercase tracking-wider">Batal</a>
        </form>
    </div>

</body>
</html>