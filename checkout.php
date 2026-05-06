<?php
require_once 'config.php';
require_once 'header.php';

// Если корзина пуста
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

// Если пользователь авторизован – подтягиваем его данные
$user_data = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user_data = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из формы
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $delivery_method = $_POST['delivery_method'];

    // Получаем товары из корзины
    $cart = $_SESSION['cart'];
    $ids = array_keys($cart);
    $in = str_repeat('?,', count($ids)-1) . '?';
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($in)");
    $stmt->execute($ids);
    $products = $stmt->fetchAll();

    $total = 0;
    foreach ($products as $prod) {
        $total += $prod['price'] * $cart[$prod['id']];
    }

    // Генерируем уникальный номер заказа
    $order_number = 'ORD-' . time() . rand(100,999);

    // Вставляем заказ
    $user_id = $_SESSION['user_id'] ?? null;
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, order_number, full_name, phone, email, delivery_address, delivery_method, total_amount, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->execute([$user_id, $order_number, $full_name, $phone, $email, $address, $delivery_method, $total]);
    $order_id = $pdo->lastInsertId();

    // Вставляем товары в order_items
    foreach ($products as $prod) {
        $qty = $cart[$prod['id']];
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, quantity, price) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$order_id, $prod['id'], $prod['name'], $qty, $prod['price']]);
    }

    // Очищаем корзину
    unset($_SESSION['cart']);

    // Перенаправляем на страницу успеха
    header("Location: order_success.php?order_id=$order_id");
    exit;
}

// Получаем товары для отображения в форме
$cart_items = [];
$total = 0;
$ids = array_keys($_SESSION['cart']);
if (!empty($ids)) {
    $in = str_repeat('?,', count($ids)-1) . '?';
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($in)");
    $stmt->execute($ids);
    $products = $stmt->fetchAll();
    foreach ($products as $prod) {
        $qty = $_SESSION['cart'][$prod['id']];
        $cart_items[] = ['product' => $prod, 'qty' => $qty, 'sum' => $prod['price'] * $qty];
        $total += $prod['price'] * $qty;
    }
}
?>
<div class="container" style="padding: 40px 0;">
    <h2>Оформление заказа</h2>
    <div style="display: flex; gap: 30px; flex-wrap: wrap;">
        <div style="flex: 1.5;">
            <h3>Ваши данные для доставки</h3>
            <form method="post">
                <input type="text" name="full_name" placeholder="ФИО" value="<?= htmlspecialchars($user_data['full_name'] ?? '') ?>" required><br><br>
                <input type="text" name="phone" placeholder="Телефон" value="<?= htmlspecialchars($user_data['phone'] ?? '') ?>" required><br><br>
                <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($user_data['email'] ?? '') ?>" required><br><br>
                <textarea name="address" placeholder="Адрес доставки" required><?= htmlspecialchars($user_data['address'] ?? '') ?></textarea><br><br>
                <select name="delivery_method" required>
                    <option value="courier">Курьером</option>
                    <option value="pickup">Самовывоз</option>
                    <option value="post">Почта России</option>
                </select><br><br>
                <button type="submit" class="btn-primary">Подтвердить заказ</button>
            </form>
        </div>
        <div style="flex: 1;">
            <h3>Ваш заказ</h3>
            <?php foreach($cart_items as $item): ?>
                <p><?= htmlspecialchars($item['product']['name']) ?> x <?= $item['qty'] ?> = <?= number_format($item['sum'],2,' ',' ') ?> ₽</p>
            <?php endforeach; ?>
            <hr>
            <p><strong>Итого: <?= number_format($total,2,' ',' ') ?> ₽</strong></p>
        </div>
    </div>
</div>
<?php require_once 'footer.php'; ?>