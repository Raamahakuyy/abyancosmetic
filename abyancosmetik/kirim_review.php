<?php
include 'koneksi.php';

if (isset($_POST['kirim'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $jabatan = mysqli_real_escape_string($koneksi, $_POST['jabatan']);
    $isi = mysqli_real_escape_string($koneksi, $_POST['isi']);
    $bintang = $_POST['bintang'];
<?php
include 'koneksi.php';

if (isset($_POST['kirim'])) {
    // Gunakan real_escape_string untuk keamanan (mencegah karakter aneh/hack)
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $jabatan = mysqli_real_escape_string($koneksi, $_POST['jabatan']);
    $isi = mysqli_real_escape_string($koneksi, $_POST['isi']);
    $bintang = $_POST['bintang'];

    // Simpan dengan status 'pending' agar tidak langsung muncul
    $query = mysqli_query($koneksi, "INSERT INTO testimoni (nama_pelanggan, isi_review, bintang, jabatan, status) VALUES ('$nama', '$isi', '$bintang', '$jabatan', 'pending')");

    if ($query) {
        // Tampilkan pesan sukses dan kembalikan ke halaman depan
        echo "<script>
            alert('Terima kasih! Review kamu berhasil dikirim. Review akan dicek oleh admin terlebih dahulu sebelum ditampilkan.');
            window.location='index.php#testimoni';
        </script>";
    } else {
        echo "<script>alert('Maaf, gagal mengirim review.'); window.location='index.php';</script>";
    }
} else {
    // Kalau coba akses file ini langsung tanpa isi form, tendang balik
    header("location:index.php");
}
?>
    // Simpan dengan status 'pending'
    $query = mysqli_query($koneksi, "INSERT INTO testimoni (nama_pelanggan, isi_review, bintang, jabatan, status) VALUES ('$nama', '$isi', '$bintang', '$jabatan', 'pending')");

    if ($query) {
        echo "<script>
            alert('Terima kasih! Review kamu berhasil dikirim dan menunggu persetujuan admin.');
            window.location='index.php#testimoni';
        </script>";
    } else {
        echo "<script>alert('Gagal mengirim review.'); window.location='index.php';</script>";
    }
} else {
    header("location:index.php");
}
?>