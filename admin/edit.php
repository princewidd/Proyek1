<?php
require_once 'auth.php';
require_once '../models/Database.php';

$db = new database();
$pdo = $db->getConnection();

$id = $_GET['id'] ?? null;
if (!$id) { header('Location: index.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM menu WHERE ID_Menu = ?");
$stmt->execute([$id]);
$menu = $stmt->fetch();
if (!$menu) { header('Location: index.php'); exit; }

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama']);
    $kategori = trim($_POST['kategori_pilih'] === '__lain__' ? $_POST['kategori_baru'] : $_POST['kategori_pilih']);
    $harga    = (int) $_POST['harga'];
    $gambar = $menu['Gambar'];
    if (!empty($_FILES['gambar']['name'])) {
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $namaFile = uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['gambar']['tmp_name'], '../uploads/menu/' . $namaFile);
        $gambar = $namaFile;
    }

    if (!$nama || !$kategori || $harga <= 0) {
        $error = 'Semua field wajib diisi dan harga harus lebih dari 0.';
    } else {
        $stmt = $pdo->prepare("UPDATE menu SET Nama_menu=?, Kategori=?, Harga=?, Gambar=? WHERE ID_Menu=?");
        $stmt->execute([$nama, $kategori, $harga, $gambar, $id]);
        header('Location: index.php?success=edit');
        exit;
    }
}

$kategoriList = $pdo->query("SELECT DISTINCT Kategori FROM menu ORDER BY Kategori")->fetchAll(PDO::FETCH_COLUMN);
$kategoriMenu = $menu['Kategori'];
$isKatBaru = !in_array($kategoriMenu, $kategoriList);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Menu</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin/edit.css">
</head>
<body>
<div class="container">
    <a href="index.php" class="back-link">← Kembali ke Daftar Menu</a>
    <h1>Edit Menu</h1>

    <div class="form-card">
        <?php if ($error): ?>
            <div class="alert-error">⚠️ <?= $error ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Nama Menu</label>
                <input type="text" name="nama" value="<?= htmlspecialchars($_POST['nama'] ?? $menu['Nama_menu']) ?>" required>
            </div>
            <div class="form-group">
                <label>Kategori</label>
                <select name="kategori_pilih" id="kategori_pilih" onchange="toggleKategoriBaru(this)">
                    <?php foreach ($kategoriList as $kat): ?>
                        <option value="<?= htmlspecialchars($kat) ?>" <?= $kat === $kategoriMenu ? 'selected' : '' ?>>
                            <?= htmlspecialchars($kat) ?>
                        </option>
                    <?php endforeach; ?>
                    <option value="__lain__" <?= $isKatBaru ? 'selected' : '' ?>>+ Kategori Baru...</option>
                </select>
                <div id="kategori-baru-wrap" style="<?= $isKatBaru ? 'display:block' : 'display:none' ?>; margin-top:10px;">
                    <input type="text" name="kategori_baru" value="<?= $isKatBaru ? htmlspecialchars($kategoriMenu) : '' ?>" placeholder="Tulis kategori baru">
                </div>
            </div>
            <div class="form-group">
                <label>Harga (Rp)</label>
                <input type="number" name="harga" value="<?= htmlspecialchars($_POST['harga'] ?? (int)$menu['Harga']) ?>" min="1" required>
            </div>
            <div class="form-group">
                <label>Gambar Menu</label>
                <?php if ($menu['Gambar']): ?>
                    <img src="../uploads/menu/<?= $menu['Gambar'] ?>" style="width:80px; border-radius:8px; margin-bottom:8px; display:block;">
                <?php endif; ?>
                <input type="file" name="gambar" accept="image/*">
                <small style="color:#999;">Kosongkan jika tidak ingin mengganti gambar</small>
            </div>
            <button type="submit" class="btn-submit">Simpan Perubahan</button>
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
