<?php
require_once 'config.php';
require_once 'header.php';

// Получение категорий и товаров
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
$selected_cat = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;
$sql = "SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id = c.id";
if($selected_cat > 0) {
    $sql .= " WHERE p.category_id = :cat";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['cat' => $selected_cat]);
} else {
    $stmt = $pdo->query($sql);
}
$products = $stmt->fetchAll();
?>
<section class="hero">
    <div class="container hero-grid">
        <div class="hero-content">
            <h1>Газовое оборудование <br> для дома и бизнеса</h1>
            <p>Котлы, колонки, баллоны, редукторы, счётчики. Оптом и в розницу.</p>
            <a href="#catalog" class="btn-primary">Перейти в каталог</a>
        </div>
    </div>
</section>

<section id="catalog" class="section">
    <div class="container">
        <div class="section-title">Каталог товаров</div>
        <div class="category-filter" style="display:flex; gap:12px; flex-wrap:wrap; margin-bottom:40px;">
            <a href="index.php" class="btn-outline <?= $selected_cat==0 ? 'active' : '' ?>" style="background:<?= $selected_cat==0 ? '#f97316' : 'transparent' ?>; color:<?= $selected_cat==0 ? '#fff' : '#0f3b5f' ?>">Все</a>
            <?php foreach($categories as $cat): ?>
                <a href="?cat=<?= $cat['id'] ?>" class="btn-outline <?= $selected_cat==$cat['id'] ? 'active' : '' ?>" style="background:<?= $selected_cat==$cat['id'] ? '#f97316' : 'transparent' ?>; color:<?= $selected_cat==$cat['id'] ? '#fff' : '#0f3b5f' ?>"><?= htmlspecialchars($cat['name']) ?></a>
            <?php endforeach; ?>
        </div>
        <div class="product-grid">
            <?php if(count($products) > 0): ?>
                <?php foreach($products as $prod): ?>
                    <div class="product-card">
                        <div class="product-img">
                            <?php if($prod['image'] && file_exists('uploads/'.$prod['image'])): ?>
                                <img src="uploads/<?= $prod['image'] ?>" alt="<?= htmlspecialchars($prod['name']) ?>">
                            <?php else: ?>
                                <i class="fas fa-gas-pump" style="font-size:4rem; color:#0f3b5f;"></i>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <div class="product-title"><?= htmlspecialchars($prod['name']) ?></div>
                            <div class="product-desc"><?= htmlspecialchars(mb_substr($prod['description'],0,80)) ?>...</div>
                            <div class="price"><?= number_format($prod['price'], 0, ',', ' ') ?> ₽</div>
                            <form action="cart.php" method="post">
                                <input type="hidden" name="product_id" value="<?= $prod['id'] ?>">
                                <input type="hidden" name="action" value="add">
                                <button type="submit" class="btn-small">В корзину</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Товаров не найдено</p>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php require_once 'footer.php'; ?>