<?php
require_once 'config.php';
require_once 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
        $stmt->execute([$email, $password]);
        $user_id = $pdo->lastInsertId();
        $_SESSION['user_id'] = $user_id;
        $_SESSION['role'] = 'user';
        $_SESSION['user_email'] = $email;
        header('Location: profile.php');
        exit;
    } catch (PDOException $e) {
        $error = "Email уже зарегистрирован!";
    }
}
?>
<div class="container" style="max-width:500px; margin:40px auto;">
    <h2>Регистрация</h2>
    <?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>
    <form method="post">
        <input type="email" name="email" placeholder="Email" required><br><br>
        <input type="password" name="password" placeholder="Пароль" required><br><br>
        <button type="submit" class="btn-primary">Зарегистрироваться</button>
    </form>
    <p>Уже есть аккаунт? <a href="login.php">Войти</a></p>
    <p style="font-size:0.85rem; color:#666;">После регистрации вы сможете заполнить свои данные в личном кабинете.</p>
</div>
<?php require_once 'footer.php'; ?>