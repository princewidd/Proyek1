<?php
require_once 'models/Database.php';
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
        
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'item_qty' => $itemQty,
            'total_cart' => getCartCount()
        ]);
        exit;
    }
    
    
    $scrollTo = $_POST['scroll_to'] ?? '';
    header('Location: menu.php' . ($scrollTo ? '#' . $scrollTo : ''));
    exit;
}


$db = new database();
$conn = $db->getConnection();

$query = "SELECT * FROM menu ORDER BY Kategori, Stok DESC, Nama_menu";
$stmt = $conn->prepare($query);
$stmt->execute();
$menus = $stmt->fetchAll();

$cart = getCart();
$totalCartCount = getCartCount();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Kedai Koffee Pink</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/menu.css">
</head>
<body>
    <div class="container">
        <h1>Menu Kedai Koffee Pink</h1>

        <?php
        $currentKategori = '';
        $isFirstCategory = true;

        foreach ($menus as $menu) {
            if ($currentKategori != $menu['Kategori']) {
                if (!$isFirstCategory) {
                    echo "</div>"; 
                }
                
                $currentKategori = $menu['Kategori'];
                
                echo "<div class='kategori' onclick='toggleCategory(this)'>";
                echo "<h2>$currentKategori <span class='toggle-icon'>▼</span></h2>";
                echo "</div>";
                
                echo "<div class='menu-list'>"; 
                $isFirstCategory = false;
            }

            $menuId = $menu['ID_Menu'];
            $qty = isset($cart[$menuId]) ? $cart[$menuId]['qty'] : 0;

            echo "<div class='menu-item' id='menu-$menuId'>";
            echo "<div class='menu-info'>";
            echo "<h3>" . htmlspecialchars($menu['Nama_menu']) . "</h3>";
            echo "<p>Rp " . number_format($menu['Harga'], 0, ',', '.') . "</p>";
            if ($menu['Stok'] == 0) echo "<small style='color:red;font-weight:bold;'> Stok Habis</small>";
            echo "</div>";
            
            
            echo "<div class='menu-actions'>";
            
            
            $displayStyle = $qty > 0 ? "inline-block" : "none";
            
        
            echo "<form onsubmit='submitCart(event, this)' style='display:inline;'>";
            echo "<input type='hidden' name='action' value='decrease'>";
            echo "<input type='hidden' name='menu_id' value='" . $menuId . "'>";
            echo "<button type='submit' class='btn-minus' id='btn-minus-$menuId' style='display:$displayStyle;'>-</button>";
            echo "</form>";

            
            echo "<span class='qty' id='qty-$menuId' style='display:$displayStyle;'>Qty: $qty</span>";

        
            echo "<form onsubmit='submitCart(event, this)' style='display:inline;'>";
            echo "<input type='hidden' name='action' value='add'>";
            echo "<input type='hidden' name='menu_id' value='" . $menuId . "'>";
            echo "<input type='hidden' name='nama' value='" . htmlspecialchars($menu['Nama_menu'], ENT_QUOTES) . "'>";
            echo "<input type='hidden' name='harga' value='" . $menu['Harga'] . "'>";
            if ($menu['Stok'] == 1) {
                echo "<button type='submit' class='btn-add'>+</button>"; 
            } else {
                echo "<button type='button' class='btn-add' disabled style='opacity:0.4;cursor:not-allowed;'>+</button>";
            }
            echo "</form>";
            
            echo "</div>";
            echo "</div>";
        }
        
        if (!$isFirstCategory) {
            echo "</div>";
        }
        ?>

        <div class="cart-summary" id="cart-summary" style="display: <?= $totalCartCount > 0 ? 'block' : 'none' ?>;">
            <p>Total Item: <span id="total-item-count"><?= $totalCartCount ?></span></p>
            <a href="cart.php" class="btn-order">Buat Pesanan</a>
        </div>
    </div>

    <script>
        
        function toggleCategory(element) {
            element.classList.toggle('open');
            var content = element.nextElementSibling;
            if (content.style.display === "block") {
                content.style.display = "none";
            } else {
                content.style.display = "block";
            }
        }

       
        function submitCart(event, formElement) {
            event.preventDefault(); 

            
            const formData = new FormData(formElement);
            formData.append('is_ajax', '1'); 

            
            fetch('menu.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json()) 
            .then(data => {
                if (data.status === 'success') {
                    const menuId = formData.get('menu_id');
                    const qtySpan = document.getElementById('qty-' + menuId);
                    const btnMinus = document.getElementById('btn-minus-' + menuId);
                    const cartSummary = document.getElementById('cart-summary');
                    const totalItemCount = document.getElementById('total-item-count');

                    
                    if (data.item_qty > 0) {
                        qtySpan.innerText = 'Qty: ' + data.item_qty;
                        qtySpan.style.display = 'inline-block';
                        btnMinus.style.display = 'inline-block';
                    } else {
                        
                        qtySpan.style.display = 'none';
                        btnMinus.style.display = 'none';
                    }

                    
                    if (data.total_cart > 0) {
                        cartSummary.style.display = 'block';
                        totalItemCount.innerText = data.total_cart;
                    } else {
                        cartSummary.style.display = 'none';
                    }
                }
            })
            .catch(error => console.error('Error AJAX:', error));
        }
    </script>
</body>
</html>