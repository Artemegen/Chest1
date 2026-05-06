<?php
require_once 'config.php';
require_once 'header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Обновление профиля
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $update = $pdo->prepare("UPDATE users SET full_name=?, phone=?, address=? WHERE id=?");
    $update->execute([$full_name, $phone, $address, $user_id]);
    header('Location: profile.php');
    exit;
}

// История заказов
$orders = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$orders->execute([$user_id]);
$orders_list = $orders->fetchAll();
?>
<div class="container" style="padding: 40px 0;">
    <h2>Личный кабинет</h2>
    <div style="display: flex; gap: 40px; flex-wrap: wrap;">
        <div style="flex:1;">
            <h3>Мои данные</h3>
            <form method="post">
                <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required><br><br>
                <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>"><br><br>
                <textarea name="address"><?= htmlspecialchars($user['address']) ?></textarea><br><br>
                <button type="submit" name="update_profile" class="btn-primary">Сохранить</button>
            </form>
        </div>
        <div style="flex:2;">
            <h3>История заказов</h3>
            <?php if(count($orders_list) > 0): ?>
                <table style="width:100%; border-collapse: collapse;">
                    <tr><th>№ заказа</th><th>Сумма</th><th>Статус</th><th>Дата</th><th>Договор</th></tr>
                    <?php foreach($orders_list as $order): ?>
                    <tr>
                        <td><?= $order['order_number'] ?></td>
                        <td><?= number_format($order['total_amount'],2,' ',' ') ?> ₽</td>
                        <td><?= $order['status'] ?></td>
                        <td><?= $order['created_at'] ?></td>
                        <td><a href="contract.php?order_id=<?= $order['id'] ?>" target="_blank">Распечатать договор</a></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>У вас ещё нет заказов.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require_once 'footer.php'; ?>