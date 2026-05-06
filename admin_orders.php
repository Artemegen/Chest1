<?php
require_once 'config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$statuses = ['pending' => 'Новый', 'paid' => 'Оплачен', 'shipped' => 'Отправлен', 'delivered' => 'Доставлен', 'cancelled' => 'Отменён'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $order_id]);
    header('Location: admin_orders.php');
    exit;
}

$orders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head><title>Управление заказами</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="container" style="padding: 40px 0;">
    <h2>Заказы</h2>
    <a href="admin_dashboard.php">← Назад в админку</a>
    <table style="width:100%; border-collapse: collapse; margin-top:20px;">
        <tr><th>ID</th><th>Номер</th><th>Покупатель</th><th>Сумма</th><th>Статус</th><th>Действие</th><th>Договор</th></tr>
        <?php foreach($orders as $order): ?>
        <tr>
            <td><?= $order['id'] ?></td>
            <td><?= $order['order_number'] ?></td>
            <td><?= htmlspecialchars($order['full_name']) ?></td>
            <td><?= number_format($order['total_amount'],2,' ',' ') ?> ₽</td>
            <td>
                <form method="post" style="margin:0">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <select name="status" onchange="this.form.submit()">
                        <?php foreach($statuses as $val => $label): ?>
                        <option value="<?= $val ?>" <?= $order['status']==$val ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="update_status" value="1">
                </form>
            </td>
            <td><a href="contract.php?order_id=<?= $order['id'] ?>" target="_blank">Печать</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>