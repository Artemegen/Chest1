<?php
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

// Обработка добавления
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $category_id = (int)$_POST['category_id'];
    $description = $_POST['description'];
    $price = (float)$_POST['price'];
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $filename);
        $image = $filename;
    }
    $stmt = $pdo->prepare("INSERT INTO products (category_id, name, description, price, image) VALUES (?,?,?,?,?)");
    $stmt->execute([$category_id, $name, $description, $price, $image]);
    header('Location: admin_products.php');
    exit;
}

// Обработка удаления
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $img = $pdo->prepare("SELECT image FROM products WHERE id=?");
    $img->execute([$id]);
    $row = $img->fetch();
    if ($row && $row['image'] && file_exists('uploads/'.$row['image'])) unlink('uploads/'.$row['image']);
    $pdo->prepare("DELETE FROM products WHERE id=?")->execute([$id]);
    header('Location: admin_products.php');
    exit;
}

// Обработка редактирования
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_product'])) {
    $id = (int)$_POST['id'];
    $name = $_POST['name'];
    $category_id = (int)$_POST['category_id'];
    $description = $_POST['description'];
    $price = (float)$_POST['price'];
    $stmt = $pdo->prepare("UPDATE products SET name=?, category_id=?, description=?, price=? WHERE id=?");
    $stmt->execute([$name, $category_id, $description, $price, $id]);
    if (isset($_FILES['new_image']) && $_FILES['new_image']['error'] === UPLOAD_ERR_OK) {
        $old = $pdo->prepare("SELECT image FROM products WHERE id=?");
        $old->execute([$id]);
        $old_img = $old->fetchColumn();
        if ($old_img && file_exists('uploads/'.$old_img)) unlink('uploads/'.$old_img);
        $ext = pathinfo($_FILES['new_image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['new_image']['tmp_name'], 'uploads/' . $filename);
        $pdo->prepare("UPDATE products SET image=? WHERE id=?")->execute([$filename, $id]);
    }
    header('Location: admin_products.php');
    exit;
}

// Получение списка товаров
$products = $pdo->query("SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC")->fetchAll();

// Если передан параметр edit – показываем форму редактирования
$edit_product = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id=?");
    $stmt->execute([$id]);
    $edit_product = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Управление товарами</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-container { max-width:1200px; margin:40px auto; padding:0 20px; }
        .admin-form { background:#f8fafc; padding:20px; border-radius:20px; margin-bottom:40px; }
        table { width:100%; border-collapse:collapse; background:white; border-radius:16px; overflow:hidden; }
        th, td { padding:12px; border-bottom:1px solid #e2e8f0; text-align:left; }
        th { background:#0f3b5f; color:white; }
        .btn { display:inline-block; padding:6px 12px; border-radius:30px; text-decoration:none; margin:0 4px; }
        .btn-danger { background:#ef4444; color:white; }
        .btn-edit { background:#f97316; color:white; }
    </style>
</head>
<body>
<div class="admin-container">
    <h1>Управление товарами</h1>
    <a href="admin_dashboard.php" class="btn-outline">← Назад в админку</a>
    <a href="logout.php" class="btn-danger btn" style="float:right;">Выйти</a>
    <div style="clear:both;"></div>

    <!-- Вкладки: добавление / редактирование -->
    <div style="margin: 30px 0;">
        <a href="?action=add" class="btn-primary" style="background:#0f3b5f; margin-right:10px;">➕ Добавить товар</a>
        <?php if ($edit_product): ?>
            <a href="admin_products.php" class="btn-outline">✖ Отменить редактирование</a>
        <?php endif; ?>
    </div>

    <?php if (isset($_GET['action']) && $_GET['action'] === 'add' && !$edit_product): ?>
        <!-- Форма добавления товара -->
        <div class="admin-form">
            <h3>Добавление нового товара</h3>
            <form method="post" enctype="multipart/form-data">
                <input type="text" name="name" placeholder="Название" required><br><br>
                <select name="category_id" required>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select><br><br>
                <textarea name="description" rows="3" placeholder="Описание"></textarea><br><br>
                <input type="number" step="0.01" name="price" placeholder="Цена" required><br><br>
                <input type="file" name="image" accept="image/*"><br><br>
                <button type="submit" name="add_product" class="btn-primary">Добавить</button>
            </form>
        </div>
    <?php endif; ?>

    <?php if ($edit_product): ?>
        <!-- Форма редактирования товара -->
        <div class="admin-form">
            <h3>Редактирование товара</h3>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $edit_product['id'] ?>">
                <input type="text" name="name" value="<?= htmlspecialchars($edit_product['name']) ?>" required><br><br>
                <select name="category_id">
                    <?php foreach($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $cat['id']==$edit_product['category_id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select><br><br>
                <textarea name="description" rows="3"><?= htmlspecialchars($edit_product['description']) ?></textarea><br><br>
                <input type="number" step="0.01" name="price" value="<?= $edit_product['price'] ?>" required><br><br>
                <?php if($edit_product['image'] && file_exists('uploads/'.$edit_product['image'])): ?>
                    <p>Текущее фото: <img src="uploads/<?= $edit_product['image'] ?>" width="80"></p>
                <?php endif; ?>
                <input type="file" name="new_image" accept="image/*"><br><br>
                <button type="submit" name="edit_product" class="btn-primary">Сохранить изменения</button>
            </form>
        </div>
    <?php endif; ?>

    <h3>Список товаров</h3>
    <table>
        <thead>
            <tr><th>ID</th><th>Фото</th><th>Название</th><th>Категория</th><th>Цена</th><th>Действия</th></tr>
        </thead>
        <tbody>
        <?php foreach($products as $item): ?>
            <tr>
                <td><?= $item['id'] ?></td>
                <td><?php if($item['image'] && file_exists('uploads/'.$item['image'])): ?><img src="uploads/<?= $item['image'] ?>" width="50"><?php else: ?>—<?php endif; ?></td>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= htmlspecialchars($item['cat_name']) ?></td>
                <td><?= number_format($item['price'],0,' ',' ') ?> ₽</td>
                <td>
                    <a href="?edit=<?= $item['id'] ?>" class="btn-edit btn">Редакт.</a>
                    <a href="?delete=<?= $item['id'] ?>" onclick="return confirm('Удалить?')" class="btn-danger btn">Удалить</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>