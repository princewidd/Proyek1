<?php
require_once '../models/Database.php';

$db = new database();
$pdo = $db->getConnection();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama  = trim($_POST['nama']);
    $kategori = trim($_POST['kategori_pilih'] === '__lain__' ? $_POST['kategori_baru'] : $_POST['kategori_pilih']);
    $harga = (int) $_POST['harga'];

    if (!$nama || !$kategori || $harga <= 0) {
        $error = 'Semua field wajib diisi dan harga harus lebih dari 0.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO menu (Nama_menu, Kategori, Harga) VALUES (?, ?, ?)");
        $stmt->execute([$nama, $kategori, $harga]);
        header('Location: index.php?success=tambah');
        exit;
    }
}

$kategoriList = $pdo->query("SELECT DISTINCT Kategori FROM menu ORDER BY Kategori")->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Menu</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin/tambah.css">
</head>
<body>
<div class="container">
    <a href="index.php" class="back-link">← Kembali ke Daftar Menu</a>
    <h1>Tambah Menu</h1>

    <div class="form-card">
        <?php if ($error): ?>
            <div class="alert-error">⚠️ <?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Nama Menu</label>
                <input type="text" name="nama" placeholder="Contoh: Burger Spesial" value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Kategori</label>
                <select name="kategori_pilih" id="kategori_pilih" onchange="toggleKategoriBaru(this)">
                    <?php foreach ($kategoriList as $kat): ?>
                        <option value="<?= htmlspecialchars($kat) ?>"><?= htmlspecialchars($kat) ?></option>
                    <?php endforeach; ?>
                    <option value="__lain__">+ Kategori Baru...</option>
                </select>
                <div id="kategori-baru-wrap">
                    <input type="text" name="kategori_baru" placeholder="Tulis kategori baru">
                </div>
            </div>
            <div class="form-group">
                <label>Harga (Rp)</label>
                <input type="number" name="harga" placeholder="Contoh: 15000" min="1" value="<?= htmlspecialchars($_POST['harga'] ?? '') ?>" required>
            </div>
            <button type="submit" class="btn-submit">Simpan Menu</button>
        </form>
    </div>
</div>
<script>
function toggleKategoriBaru(sel) {
    document.getElementById('kategori-baru-wrap').style.display = sel.value === '__lain__' ? 'block' : 'none';
}
</script>
</body>
</html>
