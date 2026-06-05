<?php
// index.php
session_start();
require_once 'db.php';

// Sepet mekanizmasını dizili (array) olarak başlatıyoruz
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// 🛒 SEPETE EKLEME İŞLEMİ
if (isset($_GET['action']) && $_GET['action'] === 'add_to_cart') {
    $p_id = intval($_GET['product_id']);
    
    // Eğer ürün sepetimizde zaten varsa adetini 1 artır, yoksa sepet dizisine ekle
    if (isset($_SESSION['cart'][$p_id])) {
        $_SESSION['cart'][$p_id]++;
    } else {
        $_SESSION['cart'][$p_id] = 1;
    }
    header("Location: index.php");
    exit();
}

// Veritabanındaki tüm ürünleri çekiyoruz (Vitrinde göstermek için)
$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TrendyolApp - Güvenli Alışveriş</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f5f7; font-family: system-ui, sans-serif; }
        .navbar { background-color: #ff6000 !important; } /* Trendyol Turuncusu */
        .product-card { border: none; transition: transform 0.2s; background: #fff; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.08); }
        .price-tag { color: #ff6000; font-size: 1.25rem; font-weight: bold; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold fs-3" href="index.php">🧡 trendyol<span class="fw-light">app</span></a>
        <div class="d-flex">
            <a href="cart.php" class="btn btn-light position-relative fw-bold text-dark">
                🛒 Sepetim 
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    <?= array_sum($_SESSION['cart']) ?>
                </span>
            </a>
        </div>
    </div>
</nav>

<div class="container">
    <h3 class="fw-bold mb-4 text-secondary">Sizin İçin Önerilen Ürünler</h3>
    
    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
        <?php if(count($products) > 0): ?>
            <?php foreach($products as $product): ?>
                <div class="col">
                    <div class="card h-100 product-card p-2 rounded-3 shadow-sm">
                        <img src="<?= htmlspecialchars($product['image_url'] ?? 'https://via.placeholder.com/300x300?text=Urun+Gorseli') ?>" class="card-img-top rounded" alt="Ürün" style="height: 200px; object-fit: cover;">
                        
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div>
                                <h5 class="card-title fw-bold text-dark mb-1"><?= htmlspecialchars($product['title']) ?></h5>
                                <p class="card-text text-muted small"><?= htmlspecialchars(mb_strimwidth($product['description'], 0, 80, "...")) ?></p>
                            </div>
                            <div class="mt-3">
                                <div class="price-tag mb-2"><?= number_format($product['price'], 2, ',', '.') ?> TL</div>
                                <div class="small text-muted mb-2">Stokta Kalan: <b><?= $product['stock'] ?></b> adet</div>
                                <a href="index.php?action=add_to_cart&product_id=<?= $product['id'] ?>" class="btn btn-warning w-100 text-white fw-bold bg-gradient" style="background-color: #ff6000; border: none;">Sepete Ekle</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 w-100 text-center py-5">
                <p class="text-muted fs-5">Henüz mağazada ürün yok. Lütfen <b>admin.php</b> sayfasından ürün ekleyin!</p>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>