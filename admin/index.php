<?php
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
    <style>
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0 20px;
        }
        .btn-tambah {
            background-color: #ff1493;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
        }
        .btn-tambah:hover { background-color: #cc0077; }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(255,20,147,0.1);
        }
        thead {
            background-color: #ff1493;
            color: white;
        }
        th, td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid #ffe6f0;
        }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background-color: #fff0f7; }
        .badge {
            background-color: #ffe6f0;
            color: #ff1493;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .actions { display: flex; gap: 8px; }
        .btn-edit {
            background-color: #ff69b4;
            color: white;
            padding: 6px 14px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
        }
        .btn-edit:hover { background-color: #ff1493; }
        .btn-hapus {
            background-color: #fff;
            color: #ff1493;
            border: 1px solid #ff1493;
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
        }
        .btn-hapus:hover { background-color: #ff1493; color: white; }
        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 16px;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 10px;
            color: #ff1493;
            text-decoration: none;
            font-size: 14px;
        }
        .back-link:hover { text-decoration: underline; }
        .btn-nonaktif {
            background-color: #fff;
            color: #ff1493;
            border: 1px solid #ff1493;
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
        }
        .btn-nonaktif:hover { background-color: #ff1493; color: white; }
        .btn-aktif {
            background-color: #fff;
            color: #28a745;
            border: 1px solid #28a745;
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
        }
        .btn-aktif:hover { background-color: #28a745; color: white; }
        .stok-habis td { opacity: 0.5; }
    </style>
</head>
<body>
<div class="container">
    <a href="../menu.php" class="back-link">← Kembali ke Menu</a>
    <div class="admin-header">
        <h1 style="padding:0;">Kelola Menu</h1>
        <a href="tambah.php" class="btn-tambah">+ Tambah Menu</a>
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
                <th>#</th>
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
