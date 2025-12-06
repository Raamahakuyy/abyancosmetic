<?php
session_start();
include 'koneksi.php';

if ($_SESSION['status'] != "login") {
    header("location:login.php"); exit();
}

// Default: Tampilkan data bulan ini
$tgl_mulai = date('Y-m-01');
$tgl_selesai = date('Y-m-d');

if (isset($_POST['filter'])) {
    $tgl_mulai = $_POST['tgl_mulai'];
    $tgl_selesai = $_POST['tgl_selesai'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none; }
            body { background: white; }
            .shadow-lg { box-shadow: none; }
            .border { border: 1px solid #000; }
        }
    </style>
</head>
<body class="bg-gray-100 font-sans p-6">

    <div class="max-w-5xl mx-auto bg-white p-8 rounded-lg shadow-lg">
        
        <!-- Header Laporan -->
        <div class="flex justify-between items-center border-b-2 border-gray-800 pb-4 mb-6">
            <div class="flex items-center gap-4">
                <i class="fas fa-chart-line text-4xl text-gray-800"></i>
                <div>
                    <h1 class="text-2xl font-bold uppercase tracking-wide">Laporan Penjualan</h1>
                    <p class="text-gray-600">Abyan Cosmetik Official Store</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Periode Laporan</p>
                <p class="font-bold text-lg"><?php echo date('d M Y', strtotime($tgl_mulai)); ?> - <?php echo date('d M Y', strtotime($tgl_selesai)); ?></p>
            </div>
        </div>

        <!-- Filter & Tombol (Disembunyikan saat Print) -->
        <div class="no-print mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200 flex flex-col md:flex-row justify-between items-center gap-4">
            <form action="" method="POST" class="flex items-center gap-2">
                <span class="text-sm font-bold text-gray-600">Filter:</span>
                <input type="date" name="tgl_mulai" value="<?php echo $tgl_mulai; ?>" class="border p-2 rounded text-sm">
                <span class="text-gray-500">-</span>
                <input type="date" name="tgl_selesai" value="<?php echo $tgl_selesai; ?>" class="border p-2 rounded text-sm">
                <button type="submit" name="filter" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm font-bold"><i class="fas fa-filter"></i> Tampilkan</button>
            </form>
            
            <div class="flex gap-2">
                <a href="admin.php" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 text-sm font-bold"><i class="fas fa-arrow-left"></i> Kembali</a>
                <button onclick="window.print()" class="bg-pink-600 text-white px-4 py-2 rounded hover:bg-pink-700 text-sm font-bold shadow-lg"><i class="fas fa-print"></i> Cetak / PDF</button>
            </div>
        </div>

        <!-- Tabel Data -->
        <table class="w-full text-left border-collapse border border-gray-800">
            <thead>
                <tr class="bg-gray-200 text-gray-800 text-sm uppercase">
                    <th class="border border-gray-400 p-3 text-center" width="5%">No</th>
                    <th class="border border-gray-400 p-3">Tanggal & Invoice</th>
                    <th class="border border-gray-400 p-3">Pelanggan</th>
                    <th class="border border-gray-400 p-3">Produk</th>
                    <th class="border border-gray-400 p-3 text-right">Total Bayar</th>
                    <th class="border border-gray-400 p-3 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                <?php
                $no = 1;
                $total_omzet = 0;
                // Query Filter Tanggal
                $query = mysqli_query($koneksi, "SELECT * FROM pesanan WHERE (tanggal BETWEEN '$tgl_mulai 00:00:00' AND '$tgl_selesai 23:59:59') AND status!='Batal' ORDER BY tanggal DESC");
                
                if(mysqli_num_rows($query) > 0){
                    while ($data = mysqli_fetch_array($query)) {
                        $total_omzet += $data['total_bayar'];
                ?>
                <tr>
                    <td class="border border-gray-300 p-3 text-center"><?php echo $no++; ?></td>
                    <td class="border border-gray-300 p-3">
                        <span class="font-bold"><?php echo $data['invoice_id']; ?></span><br>
                        <span class="text-xs text-gray-500"><?php echo date('d/m/Y H:i', strtotime($data['tanggal'])); ?></span>
                    </td>
                    <td class="border border-gray-300 p-3">
                        <?php echo $data['nama_pemesan']; ?><br>
                        <span class="text-xs text-gray-500 italic"><?php echo $data['no_wa']; ?></span>
                    </td>
                    <td class="border border-gray-300 p-3">
                        <?php echo $data['nama_produk']; ?> <span class="text-xs bg-gray-200 px-1 rounded">x<?php echo $data['jumlah']; ?></span>
                    </td>
                    <td class="border border-gray-300 p-3 text-right font-mono">
                        Rp <?php echo number_format($data['total_bayar']); ?>
                    </td>
                    <td class="border border-gray-300 p-3 text-center">
                        <span class="text-xs font-bold px-2 py-1 rounded <?php echo ($data['status']=='Selesai') ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                            <?php echo $data['status']; ?>
                        </span>
                    </td>
                </tr>
                <?php 
                    } 
                } else {
                    echo "<tr><td colspan='6' class='p-4 text-center text-gray-500 italic border border-gray-300'>Tidak ada data penjualan pada periode ini.</td></tr>";
                }
                ?>
            </tbody>
            <tfoot>
                <tr class="bg-gray-100 font-bold text-gray-800">
                    <td colspan="4" class="border border-gray-400 p-3 text-right text-lg">TOTAL OMZET</td>
                    <td class="border border-gray-400 p-3 text-right text-lg">Rp <?php echo number_format($total_omzet); ?></td>
                    <td class="border border-gray-400 p-3 bg-gray-200"></td>
                </tr>
            </tfoot>
        </table>

        <div class="mt-8 flex justify-end no-print">
            <div class="text-center w-48">
                <p class="mb-16">Padang, <?php echo date('d F Y'); ?></p>
                <p class="font-bold underline"><?php echo $_SESSION['username']; ?></p>
                <p class="text-xs text-gray-500">Owner / Admin</p>
            </div>
        </div>

    </div>

</body>
</html>