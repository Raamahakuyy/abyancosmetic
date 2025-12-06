<?php
session_start();
include 'koneksi.php';

// Cek Login (Bisa Admin atau Member)
if (!isset($_SESSION['user_status']) && !isset($_SESSION['status'])) {
    header("location:masuk.php"); exit();
}

$inv = $_GET['inv'];
$query = mysqli_query($koneksi, "SELECT * FROM pesanan WHERE invoice_id='$inv'");
$data = mysqli_fetch_array($query);
$toko = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM profil_toko WHERE id=1"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice #<?php echo $inv; ?></title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; margin: 0; padding: 20px; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); }
        .header { display: flex; justify-content: space-between; margin-bottom: 40px; }
        .logo h1 { margin: 0; color: #d63384; }
        .info { text-align: right; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table th, table td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        table th { background: #f8f9fa; }
        .total { text-align: right; font-size: 18px; font-weight: bold; color: #d63384; }
        .status { padding: 5px 10px; background: #eee; border-radius: 5px; font-size: 12px; font-weight: bold; }
        .lunas { background: #d1e7dd; color: #0f5132; }
        .pending { background: #fff3cd; color: #664d03; }
        @media print { .no-print { display: none; } .invoice-box { box-shadow: none; border: none; } }
    </style>
</head>
<body>

    <div class="no-print" style="max-width: 800px; margin: 0 auto 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #333; color: #fff; border: none; cursor: pointer;">Cetak Invoice</button>
    </div>

    <div class="invoice-box">
        <div class="header">
            <div class="logo">
                <h1><?php echo $toko['nama_toko']; ?></h1>
                <p><?php echo $toko['alamat']; ?></p>
                <p>WA: <?php echo $toko['no_wa']; ?></p>
            </div>
            <div class="info">
                <h3>INVOICE</h3>
                <p>No: <b><?php echo $data['invoice_id']; ?></b></p>
                <p>Tanggal: <?php echo date('d M Y', strtotime($data['tanggal'])); ?></p>
                <p>Status: 
                    <span class="status <?php echo ($data['status']=='Selesai' || $data['status']=='Proses') ? 'lunas' : 'pending'; ?>">
                        <?php echo strtoupper($data['status']); ?>
                    </span>
                </p>
            </div>
        </div>

        <div style="margin-bottom: 30px;">
            <strong>Kepada Yth:</strong><br>
            <?php echo $data['nama_pemesan']; ?><br>
            <?php echo $data['no_wa']; ?><br>
            <?php echo $data['alamat']; ?>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Produk</th>
                    <th style="text-align:center;">Jumlah</th>
                    <th style="text-align:right;">Harga</th>
                    <th style="text-align:right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Cek Detail Pesanan
                $q_detail = mysqli_query($koneksi, "SELECT * FROM detail_pesanan WHERE invoice_id='$inv'");
                $subtotal_produk = 0;
                
                if(mysqli_num_rows($q_detail) > 0){
                    while($item = mysqli_fetch_array($q_detail)){
                        $sub = $item['harga'] * $item['jumlah'];
                        $subtotal_produk += $sub;
                ?>
                <tr>
                    <td><?php echo $item['nama_produk']; ?></td>
                    <td style="text-align:center;"><?php echo $item['jumlah']; ?></td>
                    <td style="text-align:right;">Rp <?php echo number_format($item['harga']); ?></td>
                    <td style="text-align:right;">Rp <?php echo number_format($sub); ?></td>
                </tr>
                <?php 
                    }
                } else {
                    // Fallback data lama
                    $sub = $data['harga_satuan'] * $data['jumlah'];
                    echo "<tr><td>".$data['nama_produk']."</td><td style='text-align:center;'>".$data['jumlah']."</td><td style='text-align:right;'>Rp ".number_format($data['harga_satuan'])."</td><td style='text-align:right;'>Rp ".number_format($sub)."</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <div style="text-align: right;">
            <?php if($data['diskon'] > 0) { ?>
                <p>Diskon Voucher: - Rp <?php echo number_format($data['diskon']); ?></p>
            <?php } ?>
            <!-- Hitung kode unik (selisih total bayar dengan subtotal real) -->
            <?php 
                $kode_unik = $data['total_bayar'] - ($subtotal_produk - $data['diskon']);
                if($kode_unik > 0) { echo "<p>Kode Unik: Rp $kode_unik</p>"; }
            ?>
            <p class="total">Total Bayar: Rp <?php echo number_format($data['total_bayar']); ?></p>
        </div>

        <div style="margin-top: 50px; text-align: center; color: #777; font-size: 12px;">
            <p>Terima kasih telah berbelanja di <?php echo $toko['nama_toko']; ?>!</p>
        </div>
    </div>

</body>
</html>