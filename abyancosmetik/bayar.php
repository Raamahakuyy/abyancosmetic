<?php
include 'koneksi.php';

if (!isset($_GET['inv'])) { header("location:index.php"); exit(); }

$inv = $_GET['inv'];
$query = mysqli_query($koneksi, "SELECT * FROM pesanan WHERE invoice_id='$inv'");
$data = mysqli_fetch_array($query);

if (!$data) { echo "Pesanan tidak ditemukan."; exit(); }

$bank_info = explode(" - ", $data['bank_tujuan']);
$nama_bank = $bank_info[0];
$no_rek = isset($bank_info[1]) ? $bank_info[1] : '';
$pengiriman = isset($data['pengiriman']) ? $data['pengiriman'] : 'Reguler';
$is_instant = ($pengiriman == 'Gojek (Instant)' || $pengiriman == 'Grab (Instant)');
$q_admin = mysqli_query($koneksi, "SELECT no_wa FROM profil_toko WHERE id=1");
$d_admin = mysqli_fetch_array($q_admin);
$wa_admin = $d_admin['no_wa'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pembayaran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap'); body { font-family: 'Outfit', sans-serif; background-color: #F3F4F6; }</style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-md bg-white rounded-[2.5rem] shadow-2xl shadow-gray-200 overflow-hidden border border-white relative">
        
        <!-- Header Gradient -->
        <div class="bg-gray-900 p-8 text-center text-white relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-gray-800 rounded-full blur-3xl opacity-50"></div>
            <div class="relative z-10">
                <div class="w-16 h-16 bg-white/10 backdrop-blur-sm rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4 border border-white/20 shadow-lg">
                    <i class="fas fa-clock text-pink-400"></i>
                </div>
                <h2 class="text-2xl font-bold tracking-wide">Menunggu Pembayaran</h2>
                <p class="text-gray-400 text-xs mt-1 uppercase tracking-widest font-bold">Order Created</p>
            </div>
        </div>

        <div class="p-8">
            <!-- Total -->
            <div class="text-center mb-8">
                <p class="text-gray-400 text-xs uppercase font-bold mb-1 tracking-wider">Total Tagihan (+Kode Unik)</p>
                <h1 class="text-4xl font-bold text-gray-900 tracking-tight">Rp <?php echo number_format($data['total_bayar']); ?></h1>
                <p class="text-xs text-red-500 mt-2 font-medium bg-red-50 inline-block px-3 py-1 rounded-full">⚠️ Transfer tepat hingga 3 digit terakhir</p>
            </div>

            <!-- Detail -->
            <div class="bg-gray-50 p-6 rounded-3xl border border-gray-100 mb-8 space-y-4">
                <div class="flex justify-between items-center text-sm border-b border-gray-200 pb-3">
                    <span class="text-gray-500">Metode Kirim</span>
                    <span class="font-bold text-gray-800 bg-white px-2 py-1 rounded border border-gray-200 text-xs uppercase"><?php echo $pengiriman; ?></span>
                </div>
                <div class="flex justify-between items-center text-sm border-b border-gray-200 pb-3">
                    <span class="text-gray-500">Bank Tujuan</span>
                    <span class="font-bold text-gray-800"><?php echo $nama_bank; ?></span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-500">No. Rekening</span>
                    <div class="text-right flex items-center gap-2">
                        <span class="font-bold text-gray-900 font-mono text-base" id="rekNum"><?php echo $no_rek; ?></span>
                        <button onclick="copyToClipboard('rekNum')" class="text-xs bg-gray-200 hover:bg-gray-300 text-gray-600 px-2 py-1 rounded transition" title="Copy"><i class="fas fa-copy"></i></button>
                    </div>
                </div>
            </div>

            <!-- Tombol -->
            <div class="space-y-3">
                <a href="konfirmasi.php?inv=<?php echo $data['invoice_id']; ?>" class="block w-full bg-gray-900 hover:bg-pink-600 text-white font-bold py-4 rounded-2xl text-center transition shadow-lg transform hover:-translate-y-1 flex items-center justify-center gap-2">
                    <i class="fas fa-upload"></i> Upload Bukti Transfer
                </a>
                
                <div class="grid grid-cols-2 gap-3">
                    <?php $pesan_wa = "Halo Admin, saya butuh bantuan untuk pesanan " . $data['invoice_id']; ?>
                    <a href="https://wa.me/<?php echo $wa_admin; ?>?text=<?php echo urlencode($pesan_wa); ?>" target="_blank" class="block w-full bg-white border-2 border-gray-100 text-gray-500 font-bold py-3 rounded-2xl text-center text-sm hover:border-green-500 hover:text-green-500 transition">
                        <i class="fab fa-whatsapp text-lg"></i> Bantuan
                    </a>
                    <a href="akun.php" class="block w-full bg-white border-2 border-gray-100 text-gray-500 font-bold py-3 rounded-2xl text-center text-sm hover:border-gray-400 hover:text-gray-800 transition">
                        Cek Status
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(elementId) {
            var text = document.getElementById(elementId).innerText;
            navigator.clipboard.writeText(text).then(function() { alert('Disalin!'); });
        }
    </script>

</body>
</html>