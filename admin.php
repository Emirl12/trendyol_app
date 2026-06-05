<?php
// admin.php
require_once 'db.php';

// 1. Ürün Silme İşlemi
if (isset($_GET['sil_id'])) {
    $sil_id = intval($_GET['sil_id']);
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$sil_id]);
    header("Location: admin.php"); // Sayfayı yenile
    exit();
}

// 2. Yeni Ürün Ekleme İşlemi
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ekle'])) {
    $title = trim($_POST['title']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $image_url = trim($_POST['image_url']);
    $description = trim($_POST['description']);

    if (!empty($title) && $price > 0) {
        $stmt = $pdo->prepare("INSERT INTO products (title, price, stock, image_url, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $price, $stock, $image_url, $description]);
        $message = "<div class='alert alert-success'>Ürün başarıyla eklendi!</div>";
    }
}

// 3. Mevcut Ürünleri Çekme
$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Admin Paneli - Ürün Yönetimi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <h2 class="mb-4">📦 Ürün Yönetim Paneli</h2>
    <?= $message ?>

    <div class="row">
        <div class="col-md-4">
            <div class="card p-4 shadow-sm">
                <h5 class="mb-3">Yeni Ürün Ekle</h5>
                <form action="admin.php" method="POST">
                    <div class="mb-2"><input type="text" name="title" class="form-control" placeholder="Ürün Adı" required></div>
                    <div class="mb-2"><input type="number" step="0.01" name="price" class="form-control" placeholder="Fiyat" required></div>
                    <div class="mb-2"><input type="number" name="stock" class="form-control" placeholder="Stok" required></div>
                    <div class="mb-2"><input type="text" name="image_url" class="form-control" placeholder="Görsel URL"></div>
                    <div class="mb-3"><textarea name="description" class="form-control" placeholder="Açıklama"></textarea></div>
                    <button type="submit" name="ekle" class="btn btn-success w-100">Ürünü Kaydet</button>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card p-4 shadow-sm">
                <h5 class="mb-3">Mağazadaki Ürünler</h5>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Ürün</th>
                            <th>Fiyat</th>
                            <th>Stok</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($products as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['title']) ?></td>
                            <td><?= number_format($p['price'], 2) ?> TL</td>
                            <td><?= $p['stock'] ?></td>
                            <td>
                                <a href="admin.php?sil_id=<?= $p['id'] ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Silmek istediğine emin misin?')">Sil</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-4"><a href="index.php" class="btn btn-outline-secondary">← Vitrine Dön</a></div>
</div>

</body>
</html>