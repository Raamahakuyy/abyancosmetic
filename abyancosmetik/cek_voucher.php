<?php
include 'koneksi.php';

header('Content-Type: application/json');

if(isset($_POST['kode'])){
    $kode = strtoupper(mysqli_real_escape_string($koneksi, $_POST['kode']));
    
    $query = mysqli_query($koneksi, "SELECT * FROM voucher WHERE kode_voucher='$kode' AND status='aktif' AND kuota > 0");
    $data = mysqli_fetch_assoc($query);
    
    if($data){
        echo json_encode(['status' => 'success', 'potongan' => $data['potongan']]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Kode tidak valid atau habis!']);
    }
}
?>