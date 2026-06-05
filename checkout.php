<?php
session_start();
require_once 'db.php';

// Sepet toplamını hesapla
$toplam = 0;
foreach($_SESSION['cart'] as $id => $adet) {
    $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $urun = $stmt->fetch();
    $toplam += ($urun['price'] * $adet);
}

// Ödeme İşlemi (Form gönderildiğinde)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Siparişi Veritabanına Kaydet
    $stmt = $pdo->prepare("INSERT INTO orders (user_name, total_price, items_detail) VALUES (?, ?, ?)");
    $stmt->execute([$_POST['fullname'], $toplam, "Ürünler: " . count($_SESSION['cart']) . " adet"]);

    // 2. Stoklardan Düş
    foreach ($_SESSION['cart'] as $id => $adet) {
        $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?")->execute([$adet, $id]);
    }

    $_SESSION['cart'] = []; // Sepeti boşalt
    die("<h3>Ödeme Başarılı!</h3><p>Siparişiniz alındı. <a href='index.php'>Ana sayfaya dön</a></p>");
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
    <div class="card p-4 shadow" style="max-width: 500px; margin: auto;">
        <h3>Ödeme Bilgileri</h3>
        <p>Toplam Ödenecek: <b><?= number_format($toplam, 2) ?> TL</b></p>
        <form method="POST">
            <input type="text" name="fullname" class="form-control mb-2" placeholder="Ad Soyad" required>
            <input type="text" class="form-control mb-2" placeholder="Kart Numarası (Rastgele)" required>
            <div class="row">
                <div class="col"><input type="text" class="form-control" placeholder="AA/YY" required></div>
                <div class="col"><input type="text" class="form-control" placeholder="CVV" required></div>
            </div>
            <button type="submit" class="btn btn-success w-100 mt-3">Ödemeyi Tamamla</button>
        </form>
    </div>
</body>
</html>