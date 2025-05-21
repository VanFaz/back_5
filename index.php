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
      <style>
        .error-field {
            border: 1px solid #dc3545 !important;
        }
        .error-message {
            color: #dc3545;
            font-size: 0.875em;
            margin-top: 0.25rem;
        }
        .error-list {
            color: #dc3545;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            border-radius: 0.25rem;
        }
        .success-message {
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            border-radius: 0.25rem;
        }
    </style>
  </head>

  <body class="d-flex flex-column align-items-center">
    <header class="container-fluid">
        <div class="row row-cols-1 row-cols-md-2 justify-content-center justify-content-md-between">
            <div class="header-case m-0 ms-md-1 col-md-auto d-flex align-items-center justify-content-center m-3">
            <img src="image.jpg" alt="Логотип сайта" id="logo" class="img-fluid" style="max-width: 150px;">
            <h1 class="h3">WINTER ARC</h1>
        </div>
        <nav class="menu mt-1 mt-md-0 p-2 col-auto d-flex flex-column flex-md-row align-items-center">
            <div class="move mx-2"><a href="#hiper">Список гиперссылок</a></div>
            <div class="move mx-2"><a href="#tabl">Таблица</a></div>
            <div class="move mx-2"><a href="#forma">Форма</a></div>
            <?php if (!empty($_SESSION['login'])): ?>
                <div class="move mx-2"><a href="logout.php">Выйти</a></div>
            <?php else: ?>
                <div class="move mx-2"><a href="login.php">Войти</a></div>
            <?php endif; ?>
        </nav>
        </div>
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
              <?php if (!empty($messages)): ?>
            <div class="mb-3">
                <?php foreach ($messages as $message): ?>
                    <div class="alert alert-info"><?= $message ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
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
            <div class="alert alert-danger mb-3">
                <h4>Обнаружены ошибки:</h4>
                <ul class="mb-0">
                    <?php foreach ($errors as $field => $error): ?>
                        <?php if (!empty($error)): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
            
            <form action="submit.php" method="POST">
                <!-- ФИО -->
                <div class="form-group">
                    <label for="name">ФИО:</label>
                    <input type="text" class="form-control <?php echo !empty($errors['name']) ? 'is-invalid' : ''; ?>" 
                           id="name" name="name" placeholder="Иванов Иван Иванович" required
                           value="<?php echo htmlspecialchars($values['name'] ?? ''); ?>">
                    <?php if (!empty($errors['name'])): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['name']); ?></div>
                    <?php endif; ?>
                </div>
                
                <!-- Телефон -->
                <div class="form-group">
                    <label for="phone">Телефон:</label>
                    <input type="tel" class="form-control <?php echo !empty($errors['phone']) ? 'is-invalid' : ''; ?>" 
                           id="phone" name="phone" placeholder="+7 (918) 123-45-67" required
                           value="<?php echo htmlspecialchars($values['phone'] ?? ''); ?>">
                    <?php if (!empty($errors['phone'])): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['phone']); ?></div>
                    <?php endif; ?>
                </div>
                
                <!-- Email -->
                <div class="form-group">
                    <label for="email">Электронная почта:</label>
                    <input type="email" class="form-control <?php echo !empty($errors['email']) ? 'is-invalid' : ''; ?>" 
                           id="email" name="email" placeholder="ogurec@example.com" required
                           value="<?php echo htmlspecialchars($values['email'] ?? ''); ?>">
                    <?php if (!empty($errors['email'])): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['email']); ?></div>
                    <?php endif; ?>
                </div>
                
                <!-- Дата рождения -->
                <div class="form-group">
                    <label for="birthdate">Дата рождения:</label>
                    <input type="date" class="form-control <?php echo !empty($errors['birthdate']) ? 'is-invalid' : ''; ?>" 
                           id="birthdate" name="birthdate" required
                           value="<?php echo htmlspecialchars($values['birthdate'] ?? ''); ?>">
                    <?php if (!empty($errors['birthdate'])): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['birthdate']); ?></div>
                    <?php endif; ?>
                </div>

                <!-- Пол -->
                <div class="form-group">
                    <label>Пол:</label>
                    <div class="form-check">
                        <input class="form-check-input <?php echo !empty($errors['gender']) ? 'is-invalid' : ''; ?>" 
                               type="radio" name="gender" id="male" value="male" required
                               <?php echo ($values['gender'] ?? '') === 'male' ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="male">Мужской</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input <?php echo !empty($errors['gender']) ? 'is-invalid' : ''; ?>" 
                               type="radio" name="gender" id="female" value="female"
                               <?php echo ($values['gender'] ?? '') === 'female' ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="female">Женский</label>
                    </div>
                    <?php if (!empty($errors['gender'])): ?>
                        <div class="invalid-feedback d-block"><?php echo htmlspecialchars($errors['gender']); ?></div>
                    <?php endif; ?>
                </div>

                <!-- Языки программирования -->
                <div class="form-group">
                    <label for="languages">Любимый язык программирования:</label>
                    <select class="form-control <?php echo !empty($errors['languages']) ? 'is-invalid' : ''; ?>" 
                            id="languages" name="languages[]" multiple="multiple" required size="5">
                        <?php 
                        $allLanguages = ['Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python', 'Java', 'Haskell', 'Clojure', 'Prolog', 'Scala'];
                        $selectedLanguages = isset($values['languages']) ? (is_array($values['languages']) ? $values['languages'] : explode(',', $values['languages'])) : [];
                        
                        foreach ($allLanguages as $lang): ?>
                            <option value="<?php echo htmlspecialchars($lang); ?>" 
                                <?php echo in_array($lang, $selectedLanguages) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($lang); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($errors['languages'])): ?>
                        <div class="invalid-feedback d-block"><?php echo htmlspecialchars($errors['languages']); ?></div>
                    <?php endif; ?>
                </div>

                <!-- Биография -->
                <div class="form-group">
                    <label for="bio">Биография:</label>
                    <textarea class="form-control <?php echo !empty($errors['bio']) ? 'is-invalid' : ''; ?>" 
                              id="bio" name="bio" required rows="5"><?php 
                              echo htmlspecialchars($values['bio'] ?? ''); ?></textarea>
                    <?php if (!empty($errors['bio'])): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['bio']); ?></div>
                    <?php endif; ?>
                </div>

                <!-- Чекбокс контракта -->
                <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input <?php echo !empty($errors['contract_accepted']) ? 'is-invalid' : ''; ?>" 
                           id="contract_accepted" name="contract_accepted" value="1" required
                           <?php echo ($values['contract_accepted'] ?? '') ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="contract_accepted">С контрактом ознакомлен(а)</label>
                    <?php if (!empty($errors['contract_accepted'])): ?>
                        <div class="invalid-feedback d-block"><?php echo htmlspecialchars($errors['contract_accepted']); ?></div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary">Сохранить</button>
                
                <?php if (!empty($_SESSION['login'])): ?>
                    <a href="logout.php" class="btn btn-danger ml-2">Выйти</a>
                <?php endif; ?>
            </form>
        </div>

        <h1 id="important"></h1>
    </div>
    <footer class="page-footer p-3 mt-3">
        <span>© Иванов Иван 2024</span>
    </footer>
</body>
</html>
