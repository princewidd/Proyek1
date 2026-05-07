<?php

require_once 'models/Database.php'; 
require_once 'utils/session.php'; 


$db = new database();
$conn = $db->getConnection();

if (!isset($_POST['nama'])) {
    echo "<script>alert('Akses ditolak! Silakan pesan lewat keranjang.'); window.location.href='cart.php';</script>";
    exit;
}

$cart = getCart();

if (empty($cart)) {
    echo "<script>alert('Keranjang Anda kosong!'); window.location.href='menu.php';</script>";
    exit;
}


$nama_pelanggan = $_POST['nama'];

if (preg_match('/\d/', $nama_pelanggan)) {
    echo "<script>alert('Nama tidak boleh mengandung angka!'); window.location.href='cart.php';</script>";
    exit;
}

$tanggal_sekarang = date('Y-m-d');
$total_harga_semua = 0;


$stmt_pelanggan = $conn->prepare("INSERT INTO pelanggan (nama) VALUES (?)");
$stmt_pelanggan->execute([$nama_pelanggan]);
$id_pelanggan = $conn->lastInsertId(); 


$stmt_pesanan = $conn->prepare("INSERT INTO pesanan (id_pelanggan, tanggal, total_harga) VALUES (?, ?, 0)");
$stmt_pesanan->execute([$id_pelanggan, $tanggal_sekarang]);
$id_pesanan = $conn->lastInsertId();

$nomor_wa_kasir = "6289507273413"; 
$pesan_wa = "Halo Kasir Koffee Pink!%0AAda pesanan baru!%0A%0A*Nama:* " . $nama_pelanggan . "%0A%0A*Detail Pesanan:*%0A";


$stmt_detail = $conn->prepare("INSERT INTO detail_pesanan (id_pesanan, id_menu, qty, harga_satuan) VALUES (?, ?, ?, ?)");


foreach ($cart as $id_menu => $item) {
    $jumlah = $item['qty'];
    $nama_menu = $item['nama'];
    $harga_satuan = $item['harga'];
    
    
    $subtotal = $jumlah * $harga_satuan; 
    $total_harga_semua += $subtotal;

    
    $stmt_detail->execute([$id_pesanan, $id_menu, $jumlah, $harga_satuan]);

    
    $pesan_wa .= "- " . $jumlah . "x " . $nama_menu . " (Rp " . number_format($subtotal, 0, ',', '.') . ")%0A";
}


$stmt_update = $conn->prepare("UPDATE pesanan SET total_harga = ? WHERE id_pesanan = ?");
$stmt_update->execute([$total_harga_semua, $id_pesanan]);


$pesan_wa .= "%0A*Total Pembayaran: Rp " . number_format($total_harga_semua, 0, ',', '.') . "*%0AMohon segera disiapkan, terima kasih!";
$link_wa = "https://wa.me/6283890254017" . $nomor_wa_kasir . "?text=" . $pesan_wa;


$_SESSION['cart'] = [];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pesanan Berhasil - Koffee Pink</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #ffe3ed; 
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .header-banner {
            background: #e81d62; 
            color: white;
            padding: 15px 40px;
            border-radius: 50px;
            text-align: center;
            font-weight: bold;
            font-size: 20px;
            margin-bottom: 80px;
            width: 100%;
            max-width: 600px;
            box-shadow: 0 4px 10px rgba(232, 29, 98, 0.3);
        }

        .success-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 50px;
            text-align: center;
        }

        .icon-box {
            background-color: #e81d62; 
            width: 120px;
            height: 120px;
            border-radius: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 5px 15px rgba(232, 29, 98, 0.4);
            margin-bottom: 25px;
        }

        .icon-circle {
            background-color: white;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .checkmark {
            color: #e81d62; 
            font-size: 40px;
            font-weight: bold;
            margin-top: 5px;
        }

        .success-text {
            color: #e81d62; 
            font-size: 24px;
            font-weight: 800;
            margin-bottom: 10px;
        }
        
        .sub-text {
            color: #8c6b75; 
            font-size: 14px;
            margin-bottom: 40px;
        }

        .btn-wa {
            background-color: #25D366; 
            color: white;
            padding: 15px 30px;
            border-radius: 30px;
            font-weight: bold;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 10px rgba(37, 211, 102, 0.3);
            margin-bottom: 20px;
            transition: all 0.3s;
        }

        .btn-wa:hover {
            background-color: #1ebe57;
            transform: translateY(-2px);
        }

        .btn-home {
            background: none;
            border: 2px solid #e81d62; 
            color: #e81d62;
            padding: 10px 25px;
            border-radius: 20px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s;
        }

        .btn-home:hover {
            background: #e81d62; 
            color: white;
        }
    </style>
</head>
<body>

    <div class="header-banner">Kedai Koffee Pink</div>

    <div class="success-container">
        <div class="icon-box">
            <div class="icon-circle">
                <span class="checkmark">&#10003;</span>
            </div>
        </div>

        <div class="success-text">Pesanan Berhasil Dicatat</div>
        <div class="sub-text">Silakan kirim detail pesanan ke kasir via WhatsApp agar segera diproses.</div>
        
        <a href="<?php echo $link_wa; ?>" target="_blank" class="btn-wa">
            Kirim Pesanan ke WhatsApp Kasir
        </a>
        
        <a href="menu.php" class="btn-home">Kembali ke Menu Utama</a>
    </div>

</body>
</html>