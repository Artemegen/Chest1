<?php
require_once 'config.php';

$order_id = (int)$_GET['order_id'];
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();
if (!$order) die("Заказ не найден");

$items = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$items->execute([$order_id]);
$order_items = $items->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Договор купли-продажи №<?= $order['order_number'] ?></title>
    <style>
        body { font-family: 'Inter', sans-serif; margin: 40px; line-height: 1.4; }
        .contract { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border: 1px solid #ddd; }
        h1, h3 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        .sign { margin-top: 40px; display: flex; justify-content: space-between; }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
            .contract { border: none; }
        }
        .no-print { margin-top: 20px; text-align: center; }
        button { padding: 10px 20px; font-size: 16px; cursor: pointer; }
    </style>
</head>
<body>
<div class="contract">
    <h1>ДОГОВОР КУПЛИ-ПРОДАЖИ №<?= $order['order_number'] ?></h1>
    <p>г. Москва &nbsp;&nbsp;&nbsp;&nbsp; «<?= date('d.m.Y', strtotime($order['created_at'])) ?>»</p>
    <p><strong>Продавец:</strong> ГазОптТорг (ИНН 1234567890), адрес: г. Москва, ул. Газопроводная, 18.</p>
    <p><strong>Покупатель:</strong> <?= htmlspecialchars($order['full_name']) ?>, тел. <?= htmlspecialchars($order['phone']) ?>, email: <?= htmlspecialchars($order['email']) ?>, адрес доставки: <?= htmlspecialchars($order['delivery_address']) ?></p>
    <p>Продавец передаёт, а Покупатель принимает следующий товар:</p>
    <table>
        <tr><th>№</th><th>Наименование</th><th>Кол-во</th><th>Цена</th><th>Сумма</th></tr>
        <?php $i=1; $total=0; foreach($order_items as $item): $sum = $item['price'] * $item['quantity']; $total += $sum; ?>
        <tr>
            <td><?= $i++ ?></td>
            <td><?= htmlspecialchars($item['product_name']) ?></td>
            <td><?= $item['quantity'] ?></td>
            <td><?= number_format($item['price'],2,' ',' ') ?> ₽</td>
            <td><?= number_format($sum,2,' ',' ') ?> ₽</td>
        </tr>
        <?php endforeach; ?>
        <tr><td colspan="4" align="right"><strong>Итого:</strong></td><td><strong><?= number_format($total,2,' ',' ') ?> ₽</strong></td></tr>
    </table>
    <p>Способ доставки: 
        <?php if($order['delivery_method']=='courier') echo 'Курьером'; 
              elseif($order['delivery_method']=='pickup') echo 'Самовывоз'; 
              else echo 'Почта России'; ?>
    </p>
    <p>Обязательства по передаче товара считаются выполненными после получения товара Покупателем.</p>
    <div class="sign">
        <div>Продавец: _____________ /ООО "ГазОптТорг"/</div>
        <div>Покупатель: _____________ /<?= htmlspecialchars($order['full_name']) ?>/</div>
    </div>
</div>
<div class="no-print">
    <button onclick="window.print()">🖨 Распечатать договор</button>
    <br><br>
    <a href="profile.php">Вернуться в личный кабинет</a>
</div>
</body>
</html>