<?php
session_start();
include 'koneksi.php';

$query_profil = mysqli_query($koneksi, "SELECT * FROM profil_toko WHERE id=1");
$toko = mysqli_fetch_array($query_profil);

if (empty($_SESSION['keranjang']) || !isset($_SESSION['keranjang'])) {
    echo "<script>alert('Keranjang kosong!'); window.location='katalog.php';</script>"; exit();
}

// Cek Login Member
$is_member = false; $u_nama = ""; $u_wa = ""; $u_alamat_db = ""; $user_id_db = "NULL";

if(isset($_SESSION['user_status']) && $_SESSION['user_status'] == 'login_member'){
    $is_member = true;
    $user_id_db = "'".$_SESSION['user_id']."'";
    
    $q_user = mysqli_query($koneksi, "SELECT * FROM users WHERE id='".$_SESSION['user_id']."'");
    $d_user = mysqli_fetch_array($q_user);
    
    $u_nama = $d_user['nama_lengkap'];
    $u_wa = $d_user['no_wa'];
    $u_alamat_db = $d_user['alamat'];
}

// Hitung Total
$total_belanja = 0;
foreach ($_SESSION['keranjang'] as $id_unik => $jumlah) {
    $pecah_id = explode('-', $id_unik);
    $id_produk = $pecah_id[0];
    $ambil = mysqli_query($koneksi, "SELECT * FROM produk WHERE id='$id_produk'");
    $pecah = mysqli_fetch_array($ambil);
    $total_belanja += $pecah['harga'] * $jumlah;
}

// PROSES CHECKOUT (Logic Tetap Sama)
if (isset($_POST['buat_pesanan'])) {
    $nama_pemesan = $_POST['nama_pemesan'];
    $no_wa = $_POST['no_wa'];
    if(substr($no_wa, 0, 1) == '0') $no_wa = '62' . substr($no_wa, 1);
    
    if(!empty($_POST['jalan']) && !empty($_POST['nama_provinsi'])) {
        $alamat_lengkap = $_POST['jalan'] . ", " . $_POST['nama_kecamatan'] . ", " . $_POST['nama_kota'] . ", " . $_POST['nama_provinsi'];
    } else {
        $alamat_lengkap = $_POST['alamat_manual'];
    }

    $bank_pilihan = $_POST['bank_tujuan'];
    $kurir = $_POST['pengiriman']; 
    
    $potongan = 0;
    $kode_voucher = $_POST['kode_voucher_input'];
    if(!empty($kode_voucher)){
        $cek_v = mysqli_query($koneksi, "SELECT * FROM voucher WHERE kode_voucher='$kode_voucher' AND kuota > 0");
        if(mysqli_num_rows($cek_v) > 0){
            $dv = mysqli_fetch_assoc($cek_v);
            $potongan = $dv['potongan'];
            mysqli_query($koneksi, "UPDATE voucher SET kuota = kuota - 1 WHERE id='".$dv['id']."'");
        }
    }

    $total_setelah_diskon = $total_belanja - $potongan;
    if($total_setelah_diskon < 0) $total_setelah_diskon = 0;
    
    $kode_unik = rand(1, 99); 
    $total_final = $total_setelah_diskon + $kode_unik;
    $invoice = "INV-" . date("Ymd") . rand(100,999);
    
    $q_bank = mysqli_query($koneksi, "SELECT * FROM bank WHERE id='$bank_pilihan'");
    $d_bank = mysqli_fetch_array($q_bank);
    $nama_bank_fix = $d_bank['nama_bank'] . " - " . $d_bank['nomor_rekening'];

    $ringkasan = count($_SESSION['keranjang']) . " Item Produk";
    
    $simpan = mysqli_query($koneksi, "INSERT INTO pesanan (user_id, invoice_id, nama_pemesan, no_wa, alamat, pengiriman, nama_produk, harga_satuan, jumlah, total_bayar, diskon, bank_tujuan, status) VALUES ($user_id_db, '$invoice', '$nama_pemesan', '$no_wa', '$alamat_lengkap', '$kurir', '$ringkasan', '0', '".count($_SESSION['keranjang'])."', '$total_final', '$potongan', '$nama_bank_fix', 'Baru')");

    if ($simpan) {
        foreach ($_SESSION['keranjang'] as $id_unik => $jumlah) {
            $pecah_id = explode('-', $id_unik);
            $id_produk = $pecah_id[0];
            $nm_varian = isset($pecah_id[1]) ? $pecah_id[1] : '';
            $ambil = mysqli_query($koneksi, "SELECT * FROM produk WHERE id='$id_produk'");
            $perproduk = mysqli_fetch_array($ambil);
            $nama_final = $perproduk['nama_produk'];
            if($nm_varian != '') $nama_final .= " (" . $nm_varian . ")";
            $harga = $perproduk['harga'];
            $subharga = $perproduk['harga'] * $jumlah;
            mysqli_query($koneksi, "INSERT INTO detail_pesanan (invoice_id, produk_id, nama_produk, harga, jumlah, subtotal) VALUES ('$invoice', '$id_produk', '$nama_final', '$harga', '$jumlah', '$subharga')");
            mysqli_query($koneksi, "UPDATE produk SET stok = stok - $jumlah WHERE id='$id_produk'");
        }
        unset($_SESSION['keranjang']);
        header("location:bayar.php?inv=$invoice");
    } else {
        echo "<script>alert('Gagal membuat pesanan.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Checkout - <?php echo $toko['nama_toko']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap'); body { font-family: 'Outfit', sans-serif; background-color: #FFF5F7; }</style>
</head>
<body class="text-gray-800">

    <nav class="bg-white/90 backdrop-blur-md sticky top-0 z-50 border-b border-white/50 shadow-sm">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="keranjang.php" class="font-bold text-xl tracking-tight text-gray-900 uppercase flex items-center gap-2 group">
                <i class="fas fa-arrow-left text-gray-400 group-hover:text-pink-600 text-lg mr-2 transition"></i> Kembali ke Keranjang
            </a>
            <div class="hidden md:block text-sm font-bold text-pink-600 bg-pink-50 px-4 py-2 rounded-full">
                <i class="fas fa-lock mr-2"></i> Checkout Aman
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-10">
        <form action="" method="POST" class="flex flex-col lg:flex-row gap-8 max-w-6xl mx-auto">
            
            <!-- KOLOM KIRI: FORMULIR -->
            <div class="lg:w-2/3 space-y-8">
                
                <!-- 1. Identitas -->
                <div class="bg-white p-8 rounded-[2rem] shadow-lg shadow-pink-100/50 border border-white relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-2 h-full bg-gray-900"></div>
                    <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2"><div class="bg-gray-100 p-2 rounded-lg"><i class="fas fa-user text-gray-600"></i></div> Data Penerima</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase mb-2 block ml-1">Nama Lengkap</label>
                            <input type="text" name="nama_pemesan" value="<?php echo $u_nama; ?>" required class="w-full p-3.5 bg-gray-50 border-transparent rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-gray-900 transition placeholder-gray-400" placeholder="Nama Penerima">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase mb-2 block ml-1">Nomor WhatsApp</label>
                            <input type="number" name="no_wa" value="<?php echo $u_wa; ?>" required class="w-full p-3.5 bg-gray-50 border-transparent rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-gray-900 transition placeholder-gray-400" placeholder="08xxx">
                        </div>
                    </div>
                    <?php if(!$is_member) { ?>
                        <div class="mt-4 bg-blue-50 p-3 rounded-xl flex items-center justify-between">
                            <p class="text-xs text-blue-600 font-bold ml-2">Punya akun? Login biar lebih cepat.</p>
                            <a href="masuk.php" class="text-xs bg-blue-600 text-white px-3 py-1.5 rounded-lg font-bold hover:bg-blue-700 transition">Login</a>
                        </div>
                    <?php } ?>
                </div>

                <!-- 2. Alamat -->
                <div class="bg-white p-8 rounded-[2rem] shadow-lg shadow-pink-100/50 border border-white relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-2 h-full bg-pink-500"></div>
                    <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2"><div class="bg-pink-50 p-2 rounded-lg"><i class="fas fa-map-marker-alt text-pink-600"></i></div> Alamat Pengiriman</h3>
                    
                    <?php if($is_member && !empty($u_alamat_db)) { ?>
                        <label class="cursor-pointer bg-gray-50 border-2 border-pink-100 p-4 rounded-xl flex items-start gap-3 mb-6 hover:border-pink-400 transition group relative">
                            <input type="radio" name="pilih_alamat" value="db" checked class="mt-1 accent-pink-600" onclick="toggleAlamat('db')">
                            <div>
                                <span class="text-xs font-bold text-pink-500 bg-pink-100 px-2 py-0.5 rounded uppercase tracking-wide mb-1 inline-block">Alamat Utama</span>
                                <p class="text-sm text-gray-700 font-medium leading-relaxed mt-1"><?php echo $u_alamat_db; ?></p>
                            </div>
                            <textarea name="alamat_manual" class="hidden"><?php echo $u_alamat_db; ?></textarea>
                        </label>
                        <div class="text-center text-xs text-gray-400 font-bold uppercase tracking-widest mb-4">- Atau Isi Alamat Baru -</div>
                    <?php } ?>

                    <div id="form_alamat_baru" class="<?php echo ($is_member && !empty($u_alamat_db)) ? 'hidden' : ''; ?>">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <select id="selectProvinsi" class="w-full p-3 bg-gray-50 border-transparent rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-pink-500 transition"><option value="">Provinsi...</option></select>
                            <select id="selectKota" class="w-full p-3 bg-gray-50 border-transparent rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-pink-500 transition" disabled><option value="">Kota/Kab...</option></select>
                            <select id="selectKecamatan" class="w-full p-3 bg-gray-50 border-transparent rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-pink-500 transition" disabled><option value="">Kecamatan...</option></select>
                        </div>
                        <input type="hidden" name="nama_provinsi" id="nama_provinsi">
                        <input type="hidden" name="nama_kota" id="nama_kota">
                        <input type="hidden" name="nama_kecamatan" id="nama_kecamatan">
                        <textarea name="jalan" rows="3" class="w-full p-3.5 bg-gray-50 border-transparent rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-pink-500 transition placeholder-gray-400" placeholder="Nama Jalan, Gang, Nomor Rumah, RT/RW..."></textarea>
                    </div>
                    
                    <?php if($is_member && !empty($u_alamat_db)) { ?>
                        <div class="mt-4"><input type="radio" name="pilih_alamat" id="rad_baru" value="baru" onclick="toggleAlamat('baru')" class="accent-pink-600"> <label for="rad_baru" class="text-sm text-gray-500 ml-2 cursor-pointer">Gunakan alamat baru di atas</label></div>
                    <?php } ?>
                </div>

                <!-- 3. Pengiriman -->
                <div class="bg-white p-8 rounded-[2rem] shadow-lg shadow-pink-100/50 border border-white relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-2 h-full bg-yellow-400"></div>
                    <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2"><div class="bg-yellow-50 p-2 rounded-lg"><i class="fas fa-shipping-fast text-yellow-600"></i></div> Metode Pengiriman</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- JNE -->
                        <label class="cursor-pointer relative group">
                            <input type="radio" name="pengiriman" value="Reguler (JNE/J&T)" class="peer sr-only" checked required>
                            <div class="p-4 border-2 border-gray-100 rounded-2xl peer-checked:border-blue-500 peer-checked:bg-blue-50 transition hover:border-blue-200 h-full flex flex-col items-center text-center gap-2">
                                <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-lg"><i class="fas fa-truck"></i></div>
                                <div><div class="font-bold text-sm text-gray-800">Reguler</div><div class="text-[10px] text-gray-500">JNE / J&T / SiCepat</div></div>
                            </div>
                            <div class="absolute top-2 right-2 text-blue-600 opacity-0 peer-checked:opacity-100 transition"><i class="fas fa-check-circle"></i></div>
                        </label>
                        <!-- Gojek -->
                        <label class="cursor-pointer relative group">
                            <input type="radio" name="pengiriman" value="Gojek (Instant)" class="peer sr-only" required>
                            <div class="p-4 border-2 border-gray-100 rounded-2xl peer-checked:border-green-500 peer-checked:bg-green-50 transition hover:border-green-200 h-full flex flex-col items-center text-center gap-2">
                                <div class="w-10 h-10 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-lg"><i class="fas fa-motorcycle"></i></div>
                                <div><div class="font-bold text-sm text-gray-800">Gojek</div><div class="text-[10px] text-gray-500">Instant / Sameday</div></div>
                            </div>
                            <div class="absolute top-2 right-2 text-green-600 opacity-0 peer-checked:opacity-100 transition"><i class="fas fa-check-circle"></i></div>
                        </label>
                        <!-- Grab -->
                        <label class="cursor-pointer relative group">
                            <input type="radio" name="pengiriman" value="Grab (Instant)" class="peer sr-only" required>
                            <div class="p-4 border-2 border-gray-100 rounded-2xl peer-checked:border-green-500 peer-checked:bg-green-50 transition hover:border-green-200 h-full flex flex-col items-center text-center gap-2">
                                <div class="w-10 h-10 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-lg"><i class="fas fa-motorcycle"></i></div>
                                <div><div class="font-bold text-sm text-gray-800">Grab</div><div class="text-[10px] text-gray-500">Instant / Sameday</div></div>
                            </div>
                            <div class="absolute top-2 right-2 text-green-600 opacity-0 peer-checked:opacity-100 transition"><i class="fas fa-check-circle"></i></div>
                        </label>
                    </div>
                    <div class="mt-4 bg-orange-50 border border-orange-100 p-3 rounded-xl flex gap-3 items-start">
                        <i class="fas fa-info-circle text-orange-500 mt-0.5"></i>
                        <div class="text-xs text-orange-800">
                            <strong>Catatan Ongkir:</strong><br>
                            • <b>Reguler:</b> Admin akan menghubungi via WhatsApp untuk info total ongkir.<br>
                            • <b>Instant:</b> Ongkir dibayar tunai langsung ke driver saat barang sampai.
                        </div>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: RINGKASAN & TOTAL -->
            <div class="lg:w-1/3 space-y-8">
                
                <!-- LIST BELANJAAN -->
                <div class="bg-white p-6 rounded-[2rem] shadow-lg shadow-pink-100/50 border border-white max-h-96 overflow-y-auto custom-scrollbar">
                    <h3 class="font-bold text-gray-800 mb-4 pb-2 border-b border-gray-50 text-sm uppercase tracking-wider">Item Belanjaan</h3>
                    <div class="space-y-4">
                        <?php 
                        foreach ($_SESSION['keranjang'] as $id_unik => $jumlah) { 
                            $pecah_id = explode('-', $id_unik); $id_produk = $pecah_id[0]; $varian = isset($pecah_id[1]) ? $pecah_id[1] : '';
                            $ambil = mysqli_query($koneksi, "SELECT * FROM produk WHERE id='$id_produk'"); $pecah = mysqli_fetch_array($ambil);
                        ?>
                        <div class="flex gap-3 items-start">
                            <div class="relative">
                                <img src="img/<?php echo $pecah['gambar']; ?>" class="w-14 h-14 object-cover rounded-lg bg-gray-100 border border-gray-50">
                                <span class="absolute -top-2 -right-2 bg-gray-800 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow-sm"><?php echo $jumlah; ?></span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-bold text-gray-800 truncate"><?php echo $pecah['nama_produk']; ?></h4>
                                <?php if($varian) echo "<p class='text-[10px] text-gray-400 font-medium uppercase'>Var: $varian</p>"; ?>
                                <p class="text-xs text-pink-600 font-bold mt-0.5">Rp <?php echo number_format($pecah['harga']); ?></p>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>

                <!-- VOUCHER & TOTAL -->
                <div class="bg-gray-900 p-8 rounded-[2rem] shadow-2xl text-white relative overflow-hidden">
                    <div class="absolute -top-10 -right-10 w-32 h-32 bg-gray-800 rounded-full blur-3xl opacity-50"></div>
                    
                    <!-- Voucher -->
                    <div class="relative z-10 mb-6">
                        <label class="text-xs font-bold text-gray-400 uppercase mb-2 block">Kode Voucher</label>
                        <div class="flex gap-2">
                            <input type="text" id="kodeVoucher" class="flex-1 bg-gray-800 border-transparent rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-pink-500 uppercase font-bold tracking-wider placeholder-gray-600" placeholder="KODE..">
                            <button type="button" onclick="cekVoucher()" class="bg-pink-600 px-4 rounded-xl text-xs font-bold hover:bg-pink-500 transition">Gunakan</button>
                        </div>
                        <p id="msgVoucher" class="text-[10px] mt-2 min-h-[15px]"></p>
                        <input type="hidden" name="kode_voucher_input" id="kodeVoucherInput">
                    </div>

                    <!-- Rincian -->
                    <div class="relative z-10 space-y-2 border-t border-gray-800 pt-4 mb-6 text-sm">
                        <div class="flex justify-between text-gray-400"><span>Subtotal</span><span>Rp <?php echo number_format($total_belanja); ?></span></div>
                        <div id="rowDiskon" class="flex justify-between text-green-400 hidden"><span>Voucher</span><span id="textDiskon">- Rp 0</span></div>
                        <div class="flex justify-between text-white font-bold text-lg pt-2 border-t border-gray-800 mt-2">
                            <span>Total Bayar</span><span id="textTotalAkhir">Rp <?php echo number_format($total_belanja); ?></span>
                        </div>
                    </div>
                    
                    <!-- Pembayaran -->
                    <div class="mb-6">
                        <label class="text-xs font-bold text-gray-400 uppercase mb-3 block">Pilih Bank Transfer</label>
                        <div class="grid grid-cols-2 gap-2">
                            <?php $banks = mysqli_query($koneksi, "SELECT * FROM bank"); while($bank = mysqli_fetch_array($banks)){ ?>
                            <label class="cursor-pointer">
                                <input type="radio" name="bank_tujuan" value="<?php echo $bank['id']; ?>" class="peer sr-only" required>
                                <div class="bg-gray-800 border border-gray-700 rounded-xl p-3 text-center peer-checked:bg-white peer-checked:text-gray-900 transition hover:bg-gray-700">
                                    <span class="text-xs font-bold block"><?php echo $bank['nama_bank']; ?></span>
                                </div>
                            </label>
                            <?php } ?>
                        </div>
                    </div>

                    <button type="submit" name="buat_pesanan" class="relative z-10 w-full bg-white text-gray-900 py-3.5 rounded-xl font-bold text-sm hover:bg-gray-200 transition shadow-lg transform hover:-translate-y-1">
                        Buat Pesanan Sekarang
                    </button>
                </div>

            </div>
        </form>
    </div>

    <script>
        function toggleAlamat(tipe) {
            const formBaru = document.getElementById('form_alamat_baru');
            if(tipe == 'baru') {
                formBaru.classList.remove('hidden');
                document.getElementById('selectProvinsi').required = true;
            } else {
                formBaru.classList.add('hidden');
                document.getElementById('selectProvinsi').required = false;
            }
        }

        let totalBelanja = <?php echo $total_belanja; ?>;
        let diskon = 0;

        function hitungTotal(){
            let totalAkhir = totalBelanja - diskon;
            if(totalAkhir < 0) totalAkhir = 0;
            document.getElementById('textTotalAkhir').innerText = 'Rp ' + totalAkhir.toLocaleString('id-ID');
            if(diskon > 0){
                document.getElementById('rowDiskon').classList.remove('hidden');
                document.getElementById('textDiskon').innerText = '- Rp ' + diskon.toLocaleString('id-ID');
            }
        }

        function cekVoucher(){
            const kode = document.getElementById('kodeVoucher').value;
            const msg = document.getElementById('msgVoucher');
            if(kode == "") return;
            const formData = new FormData(); formData.append('kode', kode);
            fetch('cek_voucher.php', { method: 'POST', body: formData }).then(response => response.json()).then(data => {
                if(data.status == 'success'){
                    diskon = parseInt(data.potongan);
                    document.getElementById('kodeVoucherInput').value = kode;
                    msg.innerHTML = "<span class='text-green-400 font-bold'>Hemat Rp "+diskon.toLocaleString()+"</span>";
                    hitungTotal();
                } else {
                    diskon = 0; document.getElementById('kodeVoucherInput').value = "";
                    msg.innerHTML = "<span class='text-red-400'>"+data.message+"</span>"; hitungTotal();
                }
            });
        }

        // API WILAYAH STANDARD
        const selectProvinsi = document.getElementById('selectProvinsi');
        const selectKota = document.getElementById('selectKota');
        const selectKecamatan = document.getElementById('selectKecamatan');
        const inputNamaProvinsi = document.getElementById('nama_provinsi');
        const inputNamaKota = document.getElementById('nama_kota');
        const inputNamaKecamatan = document.getElementById('nama_kecamatan');

        fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json`).then(response => response.json()).then(provinces => {
            provinces.forEach(province => { const option = document.createElement('option'); option.value = province.id; option.text = province.name; selectProvinsi.add(option); });
        });

        selectProvinsi.addEventListener('change', (e) => {
            const provinceId = e.target.value;
            inputNamaProvinsi.value = e.target.options[e.target.selectedIndex].text;
            selectKota.innerHTML = '<option value="">Pilih Kota/Kab...</option>'; selectKecamatan.innerHTML = '<option value="">Pilih Kecamatan...</option>'; selectKecamatan.disabled = true;
            if(provinceId) {
                selectKota.disabled = false; selectKota.classList.replace('bg-gray-100', 'bg-white');
                fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${provinceId}.json`).then(response => response.json()).then(regencies => {
                    regencies.forEach(regency => { const option = document.createElement('option'); option.value = regency.id; option.text = regency.name; selectKota.add(option); });
                });
            } else { selectKota.disabled = true; }
        });

        selectKota.addEventListener('change', (e) => {
            const regencyId = e.target.value;
            inputNamaKota.value = e.target.options[e.target.selectedIndex].text;
            selectKecamatan.innerHTML = '<option value="">Pilih Kecamatan...</option>';
            if(regencyId) {
                selectKecamatan.disabled = false; selectKecamatan.classList.replace('bg-gray-100', 'bg-white');
                fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${regencyId}.json`).then(response => response.json()).then(districts => {
                    districts.forEach(district => { const option = document.createElement('option'); option.value = district.id; option.text = district.name; selectKecamatan.add(option); });
                });
            } else { selectKecamatan.disabled = true; }
        });

        selectKecamatan.addEventListener('change', (e) => { inputNamaKecamatan.value = e.target.options[e.target.selectedIndex].text; });
    </script>
</body>
</html>