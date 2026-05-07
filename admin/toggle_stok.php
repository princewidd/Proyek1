<?php
require_once '../models/Database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) {
    header('Location: index.php');
    exit;
}

$db = new database();
$pdo = $db->getConnection();

$id = (int) $_POST['id'];
$stokBaru = $_POST['stok'] == 1 ? 0 : 1;

$stmt = $pdo->prepare("UPDATE menu SET Stok = ? WHERE ID_Menu = ?");
$stmt->execute([$stokBaru, $id]);

header('Location: index.php?success=stok');
exit;