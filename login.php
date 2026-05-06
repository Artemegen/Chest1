<?php
require_once 'config.php';
require_once 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];           // единое имя роли
            $_SESSION['user_email'] = $user['email'];
        if ($user['role'] == 'admin') {
            header('Location: admin_dashboard.php');
        } else {
            header('Location: profile.php');
        }
        exit;
    } else {
        $error = "Неверный email или пароль";
    }
}
?>
<div class="container" style="max-width:400px; margin:40px auto;">
    <h2>Вход</h2>
    <?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>
    <form method="post">
        <input type="email" name="email" placeholder="Email" required><br><br>
        <input type="password" name="password" placeholder="Пароль" required><br><br>
        <button type="submit" class="btn-primary">Войти</button>
    </form>
    <p>Нет аккаунта? <a href="register.php">Зарегистрироваться</a></p>
</div>
<?php require_once 'footer.php'; ?>