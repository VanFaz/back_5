<?php
session_start();
require 'db.php';

if (!empty($_SESSION['login'])) {
    header('Location: index.php');
    exit();
}

$messages = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login'] ?? '';
    $pass = $_POST['pass'] ?? '';
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM applications WHERE login = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($pass, $user['pass_hash'])) {
            $_SESSION['login'] = $user['login'];
            $_SESSION['uid'] = $user['id'];
            
            header('Location: index.php');
            exit();
        } else {
            $messages[] = '<div class="alert alert-danger">Неверный логин или пароль</div>';
        }
    } catch (PDOException $e) {
        $messages[] = '<div class="alert alert-danger">Ошибка входа: '.htmlspecialchars($e->getMessage()).'</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <link rel="stylesheet"
      href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script
      src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход</title>
    <link href="style.css" rel="stylesheet" type="text/css">
</head>
<body class="d-flex flex-column align-items-center">
    <div class="login-container">
        <h2>Вход в систему</h2>
        
        <?php if (!empty($messages)): ?>
            <div class="mb-3">
                <?php foreach ($messages as $message): ?>
                    <?= $message ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="login-form">
            <div class="form-group">
                <label for="login">Логин</label>
                <input type="text" class="form-control" id="login" name="login" required>
            </div>
            <div class="form-group">
                <label for="pass">Пароль</label>
                <input type="password" class="form-control" id="pass" name="pass" required>
            </div>
            <button type="submit" class="btn btn-primary">Войти</button>
        </form>
    </div>
</body>
</html>
