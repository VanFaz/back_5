<?php
// Получаем данные из cookies
$errors = [];
$oldValues = [];
$savedValues = [];

if (isset($_COOKIE['form_errors'])) {
    $errors = json_decode($_COOKIE['form_errors'], true);
    $oldValues = json_decode($_COOKIE['old_values'], true);
}

// Получаем сохраненные значения
foreach ($_COOKIE as $name => $value) {
    if (strpos($name, 'saved_') === 0) {
        $field = substr($name, 6);
        $savedValues[$field] = $value;
    }
}

// Функция для получения значения поля
function getFieldValue($field, $default = '') {
    global $oldValues, $savedValues;
    
    if (isset($oldValues[$field])) {
        return $oldValues[$field];
    }
    
    if (isset($savedValues[$field])) {
        return $savedValues[$field];
    }
    
    return $default;
}

// Функция для проверки выбранного значения
function isSelected($field, $value) {
    global $oldValues, $savedValues;
    
    $currentValues = [];
    if (isset($oldValues[$field])) {
        if ($field === 'languages') {
            $currentValues = explode(',', $oldValues[$field]);
        } else {
            return $oldValues[$field] === $value ? 'checked' : '';
        }
    } elseif (isset($savedValues[$field])) {
        if ($field === 'languages') {
            $currentValues = explode(',', $savedValues[$field]);
        } else {
            return $savedValues[$field] === $value ? 'checked' : '';
        }
    }
    
    return in_array($value, $currentValues) ? 'selected' : '';
}

// Функция для проверки чекбокса
function isChecked($field) {
    global $oldValues, $savedValues;
    
    if (isset($oldValues[$field])) {
        return $oldValues[$field] ? 'checked' : '';
    }
    
    if (isset($savedValues[$field])) {
        return $savedValues[$field] ? 'checked' : '';
    }
    
    return '';
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
      <title>Иванов Иван 4 задание</title>
      <link href="style.css" rel="stylesheet" type="text/css">
  </head>

  <body class="d-flex flex-column align-items-center">
    <header class="container-fluid">
        <div class="row row-cols-1 row-cols-md-2 justify-content-center justify-content-md-between">
            <div class="header-case m-0 ms-md-1 col-md-auto d-flex align-items-center justify-content-center m-3">
            <img src="image.jpg" alt="Логотип сайта" id="logo" class="img-fluid" style="max-width: 150px;">
            <h1 class="h3">WINTER ARC</h1>
        </div>
        </div>
        <nav class="menu bg-dark py-3">
            <div class="container">
                <ul class="nav justify-content-center">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="#section2">Ссылочка 1</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="#section3">Ссылочка 2</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="#section4">Ссылочка 3</a>
                    </li>
                </ul>
            </div>
        </nav>
    
</header>

    <div class="content d-flex flex-column">
        <div class="hiper m-2 p-2 m-md-3" id="hiper">
            <ul>
                <li class="list-group-item"><a href="http://kubsu.ru">1. Ссылка на главную страницу kubsu.ru (HTTP)</a></li>
                <li class="list-group-item"><a href="https://kubsu.ru">2. Ссылка на главную страницу kubsu.ru (HTTPS)</a></li>
                <li class="list-group-item"><a href="https://www.example.com"><img src="pic.jpg" alt="3. Ссылка-изображение" width = "200"></a></li>
                <li class="list-group-item"><a href="/about">4. Сокращенная ссылка на внутреннюю страницу</a></li>
                <li class="list-group-item"><a href="/">5. Сокращенная ссылка на главную страницу</a></li>
                <li class="list-group-item"><a href="#section1">6. Ссылка на фрагмент текущей страницы</a></li>
                <li class="list-group-item"><a href="https://youtu.be/d5IMLOR-bRo?si=-VU-9fB2x6fkUmpT">7. Ссылка с тремя параметрами</a></li>
                <li class="list-group-item"><a href="https://vk.com/id145294955">8. Ссылка с параметром id</a></li>
                <li class="list-group-item"><a href="./page.html">9. Относительная ссылка на страницу в текущем каталоге</a></li>
                <li class="list-group-item"><a href="about/page.html">10. Относительная ссылка на страницу в каталоге about</a></li>
                <li class="list-group-item"><a href="../page1.html">11. Относительная ссылка на страницу уровнем выше</a></li>
                <li class="list-group-item"><a href="../../page2.html">12. Относительная ссылка на страницу двумя уровнями выше</a></li>
                <li class="list-group-item"><p>13. Это <a href="https://www.wikipedia.org"> контекстная ссылка</a> в тексте абзаца.</p></li>
                <li class="list-group-item"><a href="https://ru.wikipedia.org/wiki/%D0%9A%D1%83%D0%B1%D0%B0%D0%BD%D1%81%D0%BA%D0%B8%D0%B9_%D0%B3%D0%BE%D1%81%D1%83%D0%B4%D0%B0%D1%80%D1%81%D1%82%D0%B2%D0%B5%D0%BD%D0%BD%D1%8B%D0%B9_%D1%83%D0%BD%D0%B8%D0%B2%D0%B5%D1%80%D1%81%D0%B8%D1%82%D0%B5%D1%82#%D0%A0%D0%B5%D0%BA%D1%82%D0%BE%D1%80%D1%8B">14. Ссылка на фрагмент страницы стороннего сайта</a></li>
                <li class="list-group-item">
                    <img src="pic.jpg" width = "300" usemap="#imagemap" alt="чета">
                    <map name="imagemap">
                        <area shape="rect" coords="0,0,50,50" href="https://www.youtube.com" alt="Прямоугольная область">
                        <area shape="circle" coords="150,150,50" href="https://www.twitch.tv" alt="Круглая область">
                    </map>
                </li>
        
                <li class="list-group-item"><a href="">16. Ссылка с пустым href</a></li>
        
                <li class="list-group-item"><a>17. Ссылка без href</a></li>
        
                <li class="list-group-item"><a href="https://www.apple.com" rel="nofollow">18. Ссылка, по которой запрещен переход поисковикам</a></li>
        
                <li class="list-group-item"><a href="https://translate.google.com/" rel="noindex">19. Не индексируемая ссылка</a></li>
                
                <li class="list-group-item">
                    <ol>
                        <li><a href="https://github.com/" title="=)">Первая ссылка</a></li>
                        <li><a href="https://mail.ru/" title="Mail почта">Вторая ссылка</a></li>
                    </ol>
                </li>
        
                <li class="list-group-item"><a href="ftp://username:password@ftp.example.com/file.txt">21. Ссылка на файл на сервере FTP</a></li>
            </ul>
        </div>
        <div class="tabl m-2 p-2 m-md-3" id="tabl">
            <h2>Таблица с данными</h2>
            <table class="table table-bordered table-striped">
                <thead class="table-light">
            <tr>
                <th>RADDAN</th>
                <th>Ame</th>
                <th>Pure</th>
                <th>dyrachyo</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Morphling</td>
                <td>Luna</td>
                <td>Alchemist</td>
                <td>Phantom Assassin</td>
            </tr>
            <tr>
                <td colspan="2">Главные конкуренты Tier 1</td>
                <td>Tier 2</td>
                <td>Tier 3</td>
            </tr>
            <tr>
                <td>twice TI winner</td>
                <td>several times 2nd</td>
                <td>once 2nd</td>
                <td>twice 2nd</td>
            </tr>
            <tr>
                <td>Team Spirit</td>
                <td>Xtreme Gaming</td>
                <td>Tundra Esport</td>
                <td>Gaimin Gladiators</td>
            </tr>
            <tr>
                <td>Ukraine</td>
                <td>China</td>
                <td>Russia</td>
                <td>Russia</td>
            </tr>
            <tr>
                <td>age 21</td>
                <td>age 27</td>
                <td>age 20</td>
                <td>age 23</td>
            </tr>
        </tbody>
    </table>
</div>

        <div class="forma m-2 p-2 m-md-3" id="forma">
         <h1>Форма</h1>
            <?php if (isset($_GET['success'])): ?>
                <div class="success-message">Данные успешно сохранены!</div>
            <?php endif; ?>
            
            <?php if (!empty($errors)): ?>
                <div class="error-list">
                    <h3>Обнаружены ошибки:</h3>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form action="submit.php" method="POST">
                <!-- ФИО -->
                <label for="name">
                    ФИО:<br>
                    <input id="name" name="name" placeholder="Иванов Иван Иванович" required
                           value="<?php echo htmlspecialchars(getFieldValue('name')); ?>"
                           class="<?php echo isset($errors['name']) ? 'error-field' : ''; ?>">
                    <?php if (isset($errors['name'])): ?>
                        <div class="error-message"><?php echo htmlspecialchars($errors['name']); ?></div>
                    <?php endif; ?>
                </label><br>
                
                <!-- Телефон -->
                <label for="phone">
                    Телефон:<br>
                    <input id="phone" type="tel" name="phone" placeholder="+7 (918) 123-45-67" required
                           value="<?php echo htmlspecialchars(getFieldValue('phone')); ?>"
                           class="<?php echo isset($errors['phone']) ? 'error-field' : ''; ?>">
                    <?php if (isset($errors['phone'])): ?>
                        <div class="error-message"><?php echo htmlspecialchars($errors['phone']); ?></div>
                    <?php endif; ?>
                </label><br>
                
                <!-- Email -->
                <label for="email">
                    Электронная почта:<br>
                    <input id="email" name="email" type="email" placeholder="ogurec@example.com" required
                           value="<?php echo htmlspecialchars(getFieldValue('email')); ?>"
                           class="<?php echo isset($errors['email']) ? 'error-field' : ''; ?>">
                    <?php if (isset($errors['email'])): ?>
                        <div class="error-message"><?php echo htmlspecialchars($errors['email']); ?></div>
                    <?php endif; ?>
                </label><br>
                
                <!-- Дата рождения -->
                <label for="birthdate">
                    Дата рождения:<br>
                    <input id="birthdate" name="birthdate" type="date" required
                           value="<?php echo htmlspecialchars(getFieldValue('birthdate')); ?>"
                           class="<?php echo isset($errors['birthdate']) ? 'error-field' : ''; ?>">
                    <?php if (isset($errors['birthdate'])): ?>
                        <div class="error-message"><?php echo htmlspecialchars($errors['birthdate']); ?></div>
                    <?php endif; ?>
                </label><br>

                <!-- Пол -->
                <div>
                    Выберите пол:<br>
                    <label for="male">
                        <input id="male" type="radio" name="gender" value="male" required
                               <?php echo isSelected('gender', 'male'); ?>
                               class="<?php echo isset($errors['gender']) ? 'error-field' : ''; ?>"> Мужской
                    </label><br>
                    <label for="female">
                        <input id="female" type="radio" name="gender" value="female"
                               <?php echo isSelected('gender', 'female'); ?>
                               class="<?php echo isset($errors['gender']) ? 'error-field' : ''; ?>"> Женский
                    </label><br>
                    <?php if (isset($errors['gender'])): ?>
                        <div class="error-message"><?php echo htmlspecialchars($errors['gender']); ?></div>
                    <?php endif; ?>
                </div><br>

                <!-- Языки программирования -->
                <label for="languages">
                    Любимый язык программирования:<br>
                    <select id="languages" name="languages[]" multiple="multiple" required
                            class="<?php echo isset($errors['languages']) ? 'error-field' : ''; ?>" size="5">
                        <?php 
                        $allLanguages = ['Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python', 'Java', 'Haskell', 'Clojure', 'Prolog', 'Scala'];
                        foreach ($allLanguages as $lang): ?>
                            <option value="<?php echo htmlspecialchars($lang); ?>"
                                <?php echo isSelected('languages', $lang); ?>>
                                <?php echo htmlspecialchars($lang); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['languages'])): ?>
                        <div class="error-message"><?php echo htmlspecialchars($errors['languages']); ?></div>
                    <?php endif; ?>
                </label><br>

                <!-- Биография -->
                <label for="bio">
                    Биография:<br>
                    <textarea id="bio" name="bio" placeholder="Ваша биография" required
                              class="<?php echo isset($errors['bio']) ? 'error-field' : ''; ?>"><?php 
                              echo htmlspecialchars(getFieldValue('bio')); ?></textarea>
                    <?php if (isset($errors['bio'])): ?>
                        <div class="error-message"><?php echo htmlspecialchars($errors['bio']); ?></div>
                    <?php endif; ?>
                </label><br>

                <!-- Чекбокс контракта -->
                <label for="contract_accepted">
                    <input id="contract_accepted" type="checkbox" name="contract_accepted" value="1" required
                           <?php echo isChecked('contract_accepted'); ?>
                           class="<?php echo isset($errors['contract_accepted']) ? 'error-field' : ''; ?>">
                    С контрактом ознакомлен(а)
                    <?php if (isset($errors['contract_accepted'])): ?>
                        <div class="error-message"><?php echo htmlspecialchars($errors['contract_accepted']); ?></div>
                    <?php endif; ?>
                </label><br>

                <input type="submit" name="save" value="Сохранить" class="btn btn-primary">
            </form>
        </div>

        <h1 id="important"></h1>
    </div>
    <footer class="page-footer p-3 mt-3">
        <span>© Иванов Иван 2024</span>
    </footer>
</body>

</html>
