<?php
require_once 'utils/session.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $menuId = $_POST['menu_id'];
    
    if ($_POST['action'] === 'add') {
        $menuData = [
            'nama' => $_POST['nama'],
            'harga' => $_POST['harga'],
        ];
        addToCart($menuId, $menuData);
    } elseif ($_POST['action'] === 'decrease') {
        $cart = getCart();
        if (isset($cart[$menuId])) {
            $newQty = $cart[$menuId]['qty'] - 1;
            updateCartItem($menuId, $newQty);
        }
    }
    
    
    if (isset($_POST['is_ajax'])) {
        $cart = getCart();
        $itemQty = isset($cart[$menuId]) ? $cart[$menuId]['qty'] : 0;
        $hargaItem = $_POST['harga'] ?? 0;
        $itemSubtotal = $itemQty * $hargaItem;
        
        
        $totalHarga = 0;
        foreach ($cart as $item) {
            $totalHarga += ($item['harga'] * $item['qty']);
        }
        
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'item_qty' => $itemQty,
            'item_subtotal' => $itemSubtotal,
            'grand_total' => $totalHarga,
            'is_empty' => empty($cart) 
        ]);
        exit;
    }
    
    
    header('Location: cart.php');
    exit;
}

$cart = getCart();
$totalHarga = 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang - Kedai Koffee Pink</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/cart.css">
    <link rel="stylesheet" href="css/menu.css"> 
</head>
<body>
    <div class="container cart-page">
        <h1>Keranjang Pesanan</h1>
        
        <?php if (empty($cart)): ?>
            <div class="empty-cart">
                <p>Keranjang kosong</p>
                <a href="menu.php" class="btn-back">Kembali ke Menu</a>
            </div>
        <?php else: ?>
            <div class="order-section">
                <h2>Pesanan Saya</h2>
            </div>
            
            <div class="cart-items">
                <?php foreach ($cart as $menuId => $item): 
                    $harga = $item['harga'] ?? 0;
                    $qty = $item['qty'] ?? 0;
                    $nama = $item['nama'] ?? '';
                    $subtotal = $harga * $qty;
                    $totalHarga += $subtotal;
                ?>
                <div class="cart-item" id="cart-item-<?= $menuId ?>">
                    <div class="item-name">
                        <h3><?= htmlspecialchars($nama) ?></h3>
                    </div>
                    
                    <div class="item-price">
                        <p>Rp <?= number_format($harga, 0, ',', '.') ?></p>
                    </div>
                    
                    <div class="item-qty" style="display: flex; align-items: center; gap: 10px;">
                        <form onsubmit="updateCartJS(event, this)" style="display:inline;">
                            <input type="hidden" name="action" value="decrease">
                            <input type="hidden" name="menu_id" value="<?= $menuId ?>">
                            <input type="hidden" name="harga" value="<?= $harga ?>">
                            <button type="submit" class="btn-minus">-</button>
                        </form>

                        <span class="qty" id="qty-<?= $menuId ?>"><?= $qty ?></span>

                        <form onsubmit="updateCartJS(event, this)" style="display:inline;">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="menu_id" value="<?= $menuId ?>">
                            <input type="hidden" name="nama" value="<?= htmlspecialchars($nama, ENT_QUOTES) ?>">
                            <input type="hidden" name="harga" value="<?= $harga ?>">
                            <button type="submit" class="btn-add">+</button>
                        </form>
                    </div>
                    
                    <div class="item-subtotal">
                        <p id="subtotal-<?= $menuId ?>">Rp <?= number_format($subtotal, 0, ',', '.') ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="cart-total">
                <h2 id="grand-total">Total: Rp <?= number_format($totalHarga, 0, ',', '.') ?></h2>
            </div>
            
        <div class="checkout-form">
            <h3>Konfirmasi Pesanan</h3>
            <form method="POST" action="konfirmasi.php" onsubmit="return validasiNama()">
                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" id="nama" name="nama" placeholder="MASUKKAN NAMA ANDA" required
                    oninput="if(/[0-9]/.test(this.value)){ alert('Nama tidak boleh mengandung angka!'); this.value = this.value.replace(/[0-9]/g, ''); }">
                </div>
                <button type="submit" class="btn-confirm">Konfirmasi Pesanan</button>
            </form>
            <a href="menu.php" class="btn-back">Kembali ke Menu</a>
        </div>
        <?php endif; ?>
    </div>

    <script>
        function validasiNama() {
            const nama = document.getElementById('nama').value;
            const errorEl = document.getElementById('error-nama');
            if (/\d/.test(nama)) {
                return false;
            }
            errorEl.style.display = 'none';
            return true;
        }
        
        function updateCartJS(event, formElement) {
            event.preventDefault(); 

            const formData = new FormData(formElement);
            
            if (formData.get('action') === 'decrease') {
                const menuId = formData.get('menu_id');
                const qty = parseInt(document.getElementById('qty-' + menuId).innerText);
                if (qty === 1) {
                    const nama = document.getElementById('cart-item-' + menuId).querySelector('.item-name h3').innerText;
                    if (!confirm('Hapus "' + nama + '" dari keranjang?')) return;
                }
            }

            formData.append('is_ajax', '1'); 

            fetch('cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    
                    if (data.is_empty) {
                        window.location.reload();
                        return;
                    }

                    const menuId = formData.get('menu_id');
                    
                    if (data.item_qty > 0) {
                        
                        document.getElementById('qty-' + menuId).innerText = data.item_qty;
                        
                        
                        const formatSubtotal = new Intl.NumberFormat('id-ID').format(data.item_subtotal);
                        document.getElementById('subtotal-' + menuId).innerText = 'Rp ' + formatSubtotal.replace(/,/g, '.');
                    } else {
                        
                        document.getElementById('cart-item-' + menuId).remove();
                    }

                    
                    const formatTotal = new Intl.NumberFormat('id-ID').format(data.grand_total);
                    document.getElementById('grand-total').innerText = 'Total: Rp ' + formatTotal.replace(/,/g, '.');
                }
            })
            .catch(error => console.error('Error AJAX:', error));
        }
    </script>
</body>
</html>