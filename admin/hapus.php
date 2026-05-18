<?php
require_once 'auth.php';
require_once '../models/Database.php';

$id = $_GET['id'] ?? null;
$nama = $_GET['nama'] ?? '';

if (!$id) { header('Location: index.php'); exit;}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new database();
    $pdo = $db->getConnection();
    $stmt = $pdo->prepare("DELETE FROM menu WHERE ID_Menu = ?");
    $stmt->execute([$id]);
    header('Location: index.php?success=hapus');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Hapus</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin/tambah.css">
</head>
<body>
    <div class="container">
        <div class="form-card" style=text-align:center;">
            <h2 style="color:#FD0053; margin-bottom:10px;">Hapus Menu</h2>
            <p style="margin-bottom:24px;">Yakin mau hapus <strong><?= htmlspecialchars($nama) ?></strong> dari daftar menu?</p>
            <form method="POST" action="hapus.php?id=<?= $id ?>&nama=<?= urlencode($nama) ?>">
                <button type="submit" class="btn-submit" style="background:#e74c3c; margin-bottom:10px;">Ya, Hapus</button>
            </form>
            <a href="index.php" class="back-link">Batal</a>
        </div>
</div>
</body>
</html>