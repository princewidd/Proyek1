<?php
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

    if (!$nama || !$kategori || $harga <= 0) {
        $error = 'Semua field wajib diisi dan harga harus lebih dari 0.';
    } else {
        $stmt = $pdo->prepare("UPDATE menu SET Nama_menu=?, Kategori=?, Harga=? WHERE ID_Menu=?");
        $stmt->execute([$nama, $kategori, $harga, $id]);
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
    <style>
        .form-card {
            background: white;
            border-radius: 12px;
            padding: 28px;
            box-shadow: 0 2px 10px rgba(255,20,147,0.1);
            max-width: 480px;
            margin: 20px auto;
        }
        .form-group { margin-bottom: 18px; }
        label { display: block; margin-bottom: 6px; font-weight: bold; color: #555; }
        input[type=text], input[type=number], select {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #ffb6d9;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
        }
        input:focus, select:focus { border-color: #ff1493; }
        .btn-submit {
            width: 100%;
            background-color: #ff1493;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
        }
        .btn-submit:hover { background-color: #cc0077; }
        .back-link { display: inline-block; margin-bottom: 10px; color: #ff1493; text-decoration: none; }
        .back-link:hover { text-decoration: underline; }
        .alert-error { background: #f8d7da; color: #721c24; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; }
        #kategori-baru-wrap { display: none; margin-top: 10px; }
    </style>
</head>
<body>
<div class="container">
    <a href="index.php" class="back-link">← Kembali ke Daftar Menu</a>
    <h1>Edit Menu</h1>

    <div class="form-card">
        <?php if ($error): ?>
            <div class="alert-error">⚠️ <?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
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
            <button type="submit" class="btn-submit">💾 Simpan Perubahan</button>
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
