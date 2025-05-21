<?php
session_start();
require 'db.php';

$messages = [];
$errors = [];
$values = [];

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!empty($_COOKIE['save'])) {
        setcookie('save', '', time() - 3600);
        $messages[] = 'Спасибо, результаты сохранены.';
        
        if (!empty($_COOKIE['login']) && !empty($_COOKIE['pass'])) {
            $messages[] = sprintf(
                'Вы можете <a href="login.php">войти</a> с логином <strong>%s</strong> и паролем <strong>%s</strong> для изменения данных.',
                htmlspecialchars($_COOKIE['login']),
                htmlspecialchars($_COOKIE['pass'])
            );
        }
    }

    $field_names = ['name', 'phone', 'email', 'birthdate', 'gender', 'languages', 'bio', 'contract_accepted'];
    foreach ($field_names as $field) {
        $errors[$field] = !empty($_COOKIE[$field.'_error']) ? $_COOKIE[$field.'_error'] : '';
        if (!empty($errors[$field])) {
            setcookie($field.'_error', '', time() - 3600);
        }
        $values[$field] = empty($_COOKIE[$field.'_value']) ? '' : $_COOKIE[$field.'_value'];
    }

    if (!empty($_SESSION['login'])) {
        try {
            $stmt = $pdo->prepare("SELECT a.*, GROUP_CONCAT(l.name) as languages 
                FROM applications a
                LEFT JOIN application_languages al ON a.id = al.application_id
                LEFT JOIN languages l ON al.language_id = l.id
                WHERE a.login = ? 
                GROUP BY a.id");
            $stmt->execute([$_SESSION['login']]);
            $user_data = $stmt->fetch();
            
            if ($user_data) {
                $values = array_merge($values, $user_data);
                $values['languages'] = $user_data['languages'] ? explode(',', $user_data['languages']) : [];
            }
        } catch (PDOException $e) {
            $messages[] = '<div class="alert alert-danger">Ошибка загрузки данных: '.htmlspecialchars($e->getMessage()).'</div>';
        }
    }
}

// Вспомогательные функции для формы
function getFieldValue($field) {
    global $values;
    return $values[$field] ?? '';
}

function isSelected($field, $value) {
    global $values;
    if ($field === 'gender') {
        return (isset($values[$field]) && $values[$field] === $value) ? 'checked' : '';
    }
    return '';
}

function isChecked($field) {
    global $values;
    return (isset($values[$field]) ? 'checked' : '';
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Иванов Иван 4 задание</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</head>

<body class="d-flex flex-column align-items-center">
    <header class="container-fluid">
        <div class="row row-cols-1 row-cols-md-2 justify-content-center justify-content-md-between">
            <div class="header-case m-0 ms-md-1 col-md-auto d-flex align-items-center justify-content-center m-3">
                <img src="image.jpg" alt="Логотип сайта" id="logo" class="img-fluid" style="max-width: 150px;">
                <h1 class="h3">WINTER ARC</h1>
            </div>
            <?php if (!empty($_SESSION['login'])): ?>
                <div class="col-md-auto d-flex align-items-center justify-content-center m-3">
                    <span class="text-white mr-3">Вы вошли как: <?= htmlspecialchars($_SESSION['login']) ?></span>
                    <a href="logout.php" class="btn btn-danger">Выйти</a>
                </div>
            <?php endif; ?>
        </div>
        <nav class="menu bg-dark py-3">
            <div class="container">
                <ul class="nav justify-content-center">
                    <li class="nav-item"><a class="nav-link text-white" href="#hiper">Ссылки</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="#tabl">Таблица</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="#forma">Форма</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <div class="content d-flex flex-column w-100">
        <!-- Сообщения системы -->
        <?php if (!empty($messages)): ?>
            <div class="alert-container m-2 m-md-3">
                <?php foreach ($messages as $message): ?>
                    <div class="alert alert-info"><?= $message ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Блок с гиперссылками -->
        <div class="hiper m-2 p-2 m-md-3" id="hiper">
            <h2 class="text-center mb-4">Примеры гиперссылок</h2>
            <ul class="list-group">
                <!-- Ваши ссылки остаются без изменений -->
                <li class="list-group-item"><a href="http://kubsu.ru">1. Ссылка на главную страницу kubsu.ru (HTTP)</a></li>
                <li class="list-group-item"><a href="https://kubsu.ru">2. Ссылка на главную страницу kubsu.ru (HTTPS)</a></li>
                <!-- ... остальные ссылки ... -->
            </ul>
        </div>

        <!-- Блок с таблицей -->
        <div class="tabl m-2 p-2 m-md-3" id="tabl">
            <h2 class="text-center mb-4">Таблица с данными</h2>
            <table class="table table-bordered table-striped">
                <!-- Ваша таблица остается без изменений -->
                <thead class="table-light">
                    <tr>
                        <th>RADDAN</th>
                        <th>Ame</th>
                        <th>Pure</th>
                        <th>dyrachyo</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- ... строки таблицы ... -->
                </tbody>
            </table>
        </div>

        <!-- Блок с формой -->
        <div class="forma m-2 p-2 m-md-3" id="forma">
            <h1 class="text-center mb-4">Форма</h1>
            
            <?php 
            $has_errors = false;
            foreach ($errors as $error) {
                if (!empty($error)) {
                    $has_errors = true;
                    break;
                }
            }
            ?>
            
            <?php if ($has_errors): ?>
                <div class="alert alert-danger mb-4">
                    <h4 class="alert-heading">Обнаружены ошибки:</h4>
                    <ul class="mb-0">
                        <?php foreach ($errors as $field => $error): ?>
                            <?php if (!empty($error)): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form action="submit.php" method="POST" class="needs-validation" novalidate>
                <!-- ФИО -->
                <div class="form-group">
                    <label for="name">ФИО:</label>
                    <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                           id="name" name="name" placeholder="Иванов Иван Иванович" required
                           value="<?= htmlspecialchars(getFieldValue('name')) ?>">
                    <?php if (isset($errors['name'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['name']) ?></div>
                    <?php endif; ?>
                </div>
                
                <!-- Телефон -->
                <div class="form-group">
                    <label for="phone">Телефон:</label>
                    <input type="tel" class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" 
                           id="phone" name="phone" placeholder="+7 (918) 123-45-67" required
                           value="<?= htmlspecialchars(getFieldValue('phone')) ?>">
                    <?php if (isset($errors['phone'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['phone']) ?></div>
                    <?php endif; ?>
                </div>
                
                <!-- Email -->
                <div class="form-group">
                    <label for="email">Электронная почта:</label>
                    <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                           id="email" name="email" placeholder="ogurec@example.com" required
                           value="<?= htmlspecialchars(getFieldValue('email')) ?>">
                    <?php if (isset($errors['email'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['email']) ?></div>
                    <?php endif; ?>
                </div>
                
                <!-- Дата рождения -->
                <div class="form-group">
                    <label for="birthdate">Дата рождения:</label>
                    <input type="date" class="form-control <?= isset($errors['birthdate']) ? 'is-invalid' : '' ?>" 
                           id="birthdate" name="birthdate" required
                           value="<?= htmlspecialchars(getFieldValue('birthdate')) ?>">
                    <?php if (isset($errors['birthdate'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['birthdate']) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Пол -->
                <div class="form-group">
                    <label>Выберите пол:</label>
                    <div class="form-check <?= isset($errors['gender']) ? 'is-invalid' : '' ?>">
                        <input class="form-check-input" type="radio" name="gender" id="male" value="male" required
                               <?= isSelected('gender', 'male') ?>>
                        <label class="form-check-label" for="male">Мужской</label>
                    </div>
                    <div class="form-check <?= isset($errors['gender']) ? 'is-invalid' : '' ?>">
                        <input class="form-check-input" type="radio" name="gender" id="female" value="female"
                               <?= isSelected('gender', 'female') ?>>
                        <label class="form-check-label" for="female">Женский</label>
                        <?php if (isset($errors['gender'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['gender']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Языки программирования -->
                <div class="form-group">
                    <label for="languages">Любимый язык программирования:</label>
                    <select class="form-control <?= isset($errors['languages']) ? 'is-invalid' : '' ?>" 
                            id="languages" name="languages[]" multiple required size="5">
                        <?php 
                        $allLanguages = ['Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python', 'Java', 'Haskell', 'Clojure', 'Prolog', 'Scala'];
                        foreach ($allLanguages as $lang): ?>
                            <option value="<?= htmlspecialchars($lang) ?>"
                                <?= in_array($lang, $values['languages'] ?? []) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($lang) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['languages'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['languages']) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Биография -->
                <div class="form-group">
                    <label for="bio">Биография:</label>
                    <textarea class="form-control <?= isset($errors['bio']) ? 'is-invalid' : '' ?>" 
                              id="bio" name="bio" rows="5" placeholder="Ваша биография" required><?= 
                              htmlspecialchars(getFieldValue('bio')) ?></textarea>
                    <?php if (isset($errors['bio'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['bio']) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Чекбокс контракта -->
                <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input <?= isset($errors['contract_accepted']) ? 'is-invalid' : '' ?>" 
                           id="contract_accepted" name="contract_accepted" value="1" required
                           <?= isChecked('contract_accepted') ?>>
                    <label class="form-check-label" for="contract_accepted">С контрактом ознакомлен(а)</label>
                    <?php if (isset($errors['contract_accepted'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['contract_accepted']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group text-center">
                    <button type="submit" name="save" class="btn btn-primary btn-lg">Сохранить</button>
                </div>
            </form>
        </div>
    </div>

    <footer class="page-footer p-3 mt-3 w-100">
        <div class="text-center">© Иванов Иван 2024</div>
    </footer>

    <script>
        // Валидация формы на стороне клиента
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>
</body>
</html>
