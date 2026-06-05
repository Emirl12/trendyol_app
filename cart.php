<?php
session_start();
require_once 'db.php';

// Silme işlemi
if (isset($_GET['sil_id'])) {
    unset($_SESSION['cart'][$_GET['sil_id']]);
    header("Location: cart.php");
    exit();
}

// Toplam tutarı hesapla
$toplam = 0;
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Sepetim</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <h2 class="fw-bold mb-4">🛒 Sepetim</h2>
    
    <table class="table bg-white shadow-sm mt-4">
        <thead>
            <tr>
                <th>Ürün Adı</th>
                <th>Fiyat</th>
                <th>Adet</th>
                <th>İşlem</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if(empty($_SESSION['cart'])) {
                echo "<tr><td colspan='4' class='text-center text-muted'>Sepetiniz boş.</td></tr>";
            } else {
                foreach($_SESSION['cart'] as $id => $adet) {
                    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
                    $stmt->execute([$id]);
                    $urun = $stmt->fetch();

                    if (!$urun) { unset($_SESSION['cart'][$id]); continue; }
                    
                    $satir_toplam = $urun['price'] * $adet;
                    $toplam += $satir_toplam;
                    ?>
                    <tr>
                        <td class="align-middle"><?= htmlspecialchars($urun['title']) ?></td>
                        <td class="align-middle"><?= number_format($urun['price'], 2) ?> TL</td>
                        <td class="align-middle"><?= $adet ?></td>
                        <td class="align-middle">
                            <a href="cart.php?sil_id=<?= $id ?>" class="btn btn-danger btn-sm">Kaldır</a>
                        </td>
                    </tr>
                <?php }
            }
            ?>
        </tbody>
    </table>

    <div class="d-flex justify-content-between align-items-center mt-3">
        <h4>Toplam: <span class="text-success fw-bold"><?= number_format($toplam, 2) ?> TL</span></h4>
        <div class="gap-2">
            <a href="index.php" class="btn btn-outline-secondary">Alışverişe Devam Et</a>
            <?php if($toplam > 0): ?>
                <a href="checkout.php" class="btn btn-success">Ödeme Sayfasına Geç</a>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>