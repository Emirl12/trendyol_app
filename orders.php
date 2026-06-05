<?php
require_once 'db.php';
$orders = $pdo->query("SELECT * FROM orders ORDER BY id DESC")->fetchAll();
?>
<h2>Sipariş Geçmişi</h2>
<table class="table">
    <?php foreach($orders as $o): ?>
    <tr>
        <td><?= $o['user_name'] ?></td>
        <td><?= $o['total_price'] ?> TL</td>
        <td><?= $o['order_date'] ?></td>
    </tr>
    <?php endforeach; ?>
</table>