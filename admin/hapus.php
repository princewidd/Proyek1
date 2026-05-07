<?php
require_once '../models/Database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) {
    header('Location: index.php');
    exit;
}

$db = new database();
$pdo = $db->getConnection();

$id = (int) $_POST['id'];

$stmt = $pdo->prepare("DELETE FROM menu WHERE ID_Menu = ?");
$stmt->execute([$id]);

header('Location: index.php?success=hapus');
exit;
