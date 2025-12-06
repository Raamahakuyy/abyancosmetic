<?php
session_start();
include 'koneksi.php';

// Cek Login Admin
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:login.php"); exit();
}

$id = $_GET['id'];
$query = mysqli_query($koneksi, "SELECT * FROM pesanan WHERE id='$id'");
$data = mysqli_fetch_array($query);

// Ambil Data Toko (Pengirim)
$toko = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM profil_toko WHERE id=1"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Label Pengiriman - <?php echo $data['invoice_id']; ?></title>
    <style>
        body { font-family: sans-serif; max-width: 400px; margin: 0 auto; padding: 20px; border: 2px dashed #000; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .section { margin-bottom: 20px; }
        h1 { font-size: 18px; margin: 0; }
        h2 { font-size: 14px; margin: 0 0 5px 0; text-transform: uppercase; text-decoration: underline; }
        p { margin: 2px 0; font-size: 12px; }
        .kurir { font-size: 20px; font-weight: bold; border: 2px solid #000; padding: 5px; display: inline-block; margin-bottom: 10px; }
        .footer { font-size: 10px; text-align: center; margin-top: 30px; border-top: 1px solid #000; padding-top: 5px; }
        @media print {
            body { border: none; }
            #btn-print { display: none; }
        }
    </style>
</head>
<body>

    <button id="btn-print" onclick="window.print()" style="width:100%; padding:10px; background:black; color:white; border:none; cursor:pointer; margin-bottom:20px;">CETAK LABEL INI</button>

    <div class="header">
        <div class="kurir"><?php echo $data['pengiriman']; ?></div>
        <h1><?php echo $toko['nama_toko']; ?></h1>
    </div>

    <div class="section">
        <h2>KEPADA (PENERIMA):</h2>
        <p style="font-size: 14px; font-weight: bold;"><?php echo $data['nama_pemesan']; ?></p>
        <p><?php echo $data['no_wa']; ?></p>
        <p style="margin-top: 5px;"><?php echo nl2br($data['alamat']); ?></p>
    </div>

    <div class="section">
        <h2>DARI (PENGIRIM):</h2>
        <p style="font-weight: bold;"><?php echo $toko['nama_toko']; ?></p>
        <p><?php echo $toko['no_wa']; ?></p>
    </div>

    <div class="section">
        <h2>ISI PAKET:</h2>
        <p>No. Invoice: <b><?php echo $data['invoice_id']; ?></b></p>
        <br>
        <!-- Tampilkan Detail Barang -->
        <?php
        $q_detail = mysqli_query($koneksi, "SELECT * FROM detail_pesanan WHERE invoice_id='".$data['invoice_id']."'");
        if(mysqli_num_rows($q_detail) > 0){
            while($item = mysqli_fetch_array($q_detail)){
                echo "<p>- " . $item['nama_produk'] . " (" . $item['jumlah'] . " pcs)</p>";
            }
        } else {
            // Fallback untuk data lama (sebelum fitur keranjang)
            echo "<p>- " . $data['nama_produk'] . " (" . $data['jumlah'] . " pcs)</p>";
        }
        ?>
    </div>

    <div class="footer">
        Jangan dibanting! Barang Mudah Pecah.<br>
        Terima kasih telah berbelanja.
    </div>

</body>
</html>