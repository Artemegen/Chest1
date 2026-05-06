<?php
require_once 'config.php';
require_once 'header.php';

// Добавление в корзину
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $id = (int)$_POST['product_id'];
    if(!isset($_SESSION['cart'][$id])) $_SESSION['cart'][$id] = 0;
    $_SESSION['cart'][$id]++;
    header('Location: cart.php');
    exit;
}
// Удаление из корзины
if(isset($_GET['remove'])) {
    $id = (int)$_GET['remove'];
    unset($_SESSION['cart'][$id]);
    header('Location: cart.php');
    exit;
}
// Изменение количества
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    foreach($_POST['qty'] as $id => $qty) {
        if($qty <= 0) unset($_SESSION['cart'][$id]);
        else $_SESSION['cart'][$id] = (int)$qty;
    }
    header('Location: cart.php');
    exit;
}
$cart_items = [];
$total = 0;
if(!empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $in = str_repeat('?,', count($ids)-1) . '?';
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($in)");
    $stmt->execute($ids);
    $products_db = $stmt->fetchAll();
    foreach($products_db as $prod) {
        $qty = $_SESSION['cart'][$prod['id']];
        $cart_items[] = ['product' => $prod, 'qty' => $qty, 'sum' => $prod['price'] * $qty];
        $total += $prod['price'] * $qty;
    }
}
?>
<div class="container" style="padding: 40px 0;">
    <h1>Корзина</h1>
    <?php if(empty($cart_items)): ?>
        <p>Корзина пуста. <a href="index.php">Перейти в каталог</a></p>
    <?php else: ?>
        <form method="post">
            <table class="cart-table" style="width:100%; border-collapse: collapse;">
                <tr><th>Товар</th><th>Цена</th><th>Кол-во</th><th>Сумма</th><th></th></tr>
                <?php foreach($cart_items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['product']['name']) ?></td>
                    <td><?= number_format($item['product']['price'],0,' ',' ') ?> ₽</td>
                    <td><input type="number" name="qty[<?= $item['product']['id'] ?>]" value="<?= $item['qty'] ?>" min="0" style="width:70px;"></td>
                    <td><?= number_format($item['sum'],0,' ',' ') ?> ₽</td>
                    <td><a href="?remove=<?= $item['product']['id'] ?>" class="btn-danger btn">Удалить</a></td>
                </tr>
                <?php endforeach; ?>
                <tr><td colspan="3" align="right"><strong>Итого:</strong></td><td><strong><?= number_format($total,0,' ',' ') ?> ₽</strong></td><td></td></tr>
            </table>
            <button type="submit" name="update" class="btn-primary">Обновить корзину</button>
        </form>
        <form action="cart.php" method="post" style="margin-top:20px;">
            <a href="checkout.php" class="btn-primary">Оформить заказ</a>
        </form>
        <?php if(isset($_POST['order'])): ?>
            <p style="color:green;">Заказ оформлен! Спасибо. (Демо-режим)</p>
            <?php unset($_SESSION['cart']); ?>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php require_once 'footer.php'; ?>