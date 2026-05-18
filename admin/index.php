<?php
require_once 'auth.php';
require_once '../models/Database.php';

$db = new database();
$pdo = $db->getConnection();

$stmt = $pdo->query("SELECT * FROM menu ORDER BY Stok DESC, Kategori, Nama_menu");
$menus = $stmt->fetchAll();

$kategoriList = $pdo->query("SELECT DISTINCT Kategori FROM menu ORDER BY Kategori")->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Kelola Menu</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin/index.css">
</head>
<body>
<div class="container">
    <div class="admin-header">
        <h1 style="padding:0;">Kelola Menu</h1>
        <div style="display:flex; gap:10px;">
        <a href="tambah.php" class="btn-tambah">+ Tambah Menu</a>
        <a href="logout.php" class="btn-tambah" style="background:#c0392b;">Logout</a>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert-success">
            <?php
            $msg = [
                'tambah' => 'Menu berhasil ditambahkan!',
                'edit'   => 'Menu berhasil diperbarui!',
                'hapus'  => 'Menu berhasil dihapus!',
                'stok'   => 'Status stok berhasil diubah!',
            ];
            echo $msg[$_GET['success']] ?? '✅ Berhasil!';
            ?>
        </div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Menu</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($menus as $i => $menu): ?>
            <tr class="<?= $menu['Stok'] == 0 ? 'stok-habis' : '' ?>">
                <td><?= $i + 1 ?></td>
                <td><?= htmlspecialchars($menu['Nama_menu']) ?></td>
                <td><span class="badge"><?= htmlspecialchars($menu['Kategori']) ?></span></td>
                <td>Rp <?= number_format($menu['Harga'], 0, ',', '.') ?></td>
                <td><?= $menu['Stok'] == 1 ? 'Tersedia' : 'Habis' ?></td>
                <td class="actions">
                    <a href="edit.php?id=<?= $menu['ID_Menu'] ?>" class="btn-edit">Edit</a>
                    <form method="POST" action="toggle_stok.php">
                        <input type="hidden" name="id" value="<?= $menu['ID_Menu'] ?>">
                        <input type="hidden" name="stok" value="<?= $menu['Stok'] ?>">
                        <button type="submit" class="<?= $menu['Stok'] == 1 ? 'btn-nonaktif' : 'btn-aktif' ?>"> 
                            <?= $menu['Stok'] == 1 ? 'Habis' : 'Tersedia' ?>
                        </button>
                    </form>
                    <a href="hapus.php?id=<?= $menu['ID_Menu'] ?>&nama=<?= urlencode($menu['Nama_menu']) ?>" class="btn-hapus">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    if (window.location.search.includes('success')) {
        history.replaceState(null, '', window.location.pathname);
    }
</script>
</body>
</html>
