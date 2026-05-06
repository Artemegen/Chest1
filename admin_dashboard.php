<?php
require_once 'config.php';
require_once 'header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
?>
<div class="container" style="padding: 40px 0;">
    <h1>Административная панель</h1>
    <div style="display: flex; gap: 20px; flex-wrap: wrap; margin-top: 30px;">
        <a href="admin_products.php" class="btn-primary" style="background:#0f3b5f;">📦 Управление товарами</a>
        <a href="admin_orders.php" class="btn-primary" style="background:#0f3b5f;">📋 Управление заказами</a>
        <a href="index.php" class="btn-outline">На сайт</a>
        <a href="logout.php" class="btn-outline" style="border-color:#ef4444; color:#ef4444;">Выйти</a>
    </div>
</div>
<?php require_once 'footer.php'; ?>