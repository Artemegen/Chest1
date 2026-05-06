<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ГазОптТорг | Газовое оборудование</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="header">
    <div class="container header-inner">
        <div class="logo">
            <a href="index.php">ГАЗОПТТОРГ</a>
            <span>газовое оборудование</span>
        </div>
        <ul class="nav-menu" id="navMenu">
            <li><a href="index.php">Главная</a></li>
            <li><a href="index.php#catalog">Каталог</a></li>
            <li><a href="cart.php">Корзина</a></li>
            <?php if(isset($_SESSION['user_id'])): ?>
                <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <li><a href="admin_dashboard.php">Личный кабинет</a></li>
                <?php else: ?>
                    <li><a href="profile.php">Личный кабинет</a></li>
                <?php endif; ?>
                <li><a href="logout.php">Выход</a></li>
            <?php else: ?>
                <li><a href="login.php">Вход</a></li>
                <li><a href="register.php">Регистрация</a></li>
            <?php endif; ?>
        </ul>
        <div class="header-contacts">
            <a href="tel:+78005553535" class="phone-link"><i class="fas fa-phone-alt"></i> 8 (800) 555-35-35</a>
            <a href="cart.php" class="cart-icon">
                <i class="fas fa-shopping-cart"></i>
                <span id="cartCount"><?= isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0 ?></span>
            </a>
            <!-- Вывод email авторизованного пользователя -->
            <?php if(isset($_SESSION['user_id'])): 
                // получим email из БД (можно сохранить в сессию при логине, но сделаем запрос)
                $user_email = '';
                $stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $user_email = $stmt->fetchColumn();
            ?>
                <div style="font-size:0.9rem; background:#f1f5f9; padding:6px 12px; border-radius:40px;">
                    <i class="fas fa-user"></i> <?= htmlspecialchars($user_email) ?>
                </div>
            <?php endif; ?>
            <div class="burger" id="burgerBtn"><i class="fas fa-bars"></i></div>
        </div>
    </div>
</header>
<main>