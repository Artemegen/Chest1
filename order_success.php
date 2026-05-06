<?php
require_once 'config.php';
require_once 'header.php';

$order_id = (int)$_GET['order_id'];
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();
if (!$order) {
    echo "<div class='container'><p>Заказ не найден</p></div>";
    require_once 'footer.php';
    exit;
}
?>
<div class="container" style="text-align: center; padding: 60px 0;">
    <h2>✅ Спасибо за заказ!</h2>
    <p>Номер вашего заказа: <strong><?= $order['order_number'] ?></strong></p>
    <p>На указанный email отправлены детали заказа.</p>
    <p>Вы можете распечатать <a href="contract.php?order_id=<?= $order_id ?>" target="_blank">договор купли-продажи</a>.</p>
    <a href="index.php" class="btn-primary">Вернуться на главную</a>
</div>
<?php require_once 'footer.php'; ?>