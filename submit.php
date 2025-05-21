<?php
// Устанавливаем соединение с базой данных
$host = 'localhost';
$dbname = 'u68683';
$username = 'u68683';
$password = '4171583';
$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к БД: " . $e->getMessage());
}

// Обработка POST-запроса
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];
    $oldValues = [];
    
    // Правила валидации для каждого поля
    $validationRules = [
        'name' => [
            'pattern' => '/^[a-zA-Zа-яА-ЯёЁ\s]{1,150}$/u',
            'message' => 'ФИО должно содержать только буквы и пробелы (макс. 150 символов)'
        ],
        'phone' => [
            'pattern' => '/^\+?\d{1,3}[-\s]?\(?\d{3}\)?[-\s]?\d{3}[-\s]?\d{2}[-\s]?\d{2}$/',
            'message' => 'Телефон должен быть в формате +7 (XXX) XXX-XX-XX'
        ],
        'email' => [
            'pattern' => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            'message' => 'Введите корректный email'
        ],
        'birthdate' => [
            'pattern' => '/^\d{4}-\d{2}-\d{2}$/',
            'message' => 'Дата должна быть в формате ГГГГ-ММ-ДД'
        ],
        'gender' => [
            'pattern' => '/^(male|female|other)$/',
            'message' => 'Выберите пол из предложенных вариантов'
        ],
        'languages' => [
            'pattern' => '/^(Pascal|C|C\+\+|JavaScript|PHP|Python|Java|Haskell|Clojure|Prolog|Scala)(,(Pascal|C|C\+\+|JavaScript|PHP|Python|Java|Haskell|Clojure|Prolog|Scala))*$/',
            'message' => 'Выберите хотя бы один язык программирования'
        ],
        'bio' => [
            'pattern' => '/^.{10,2000}$/s',
            'message' => 'Биография должна содержать от 10 до 2000 символов'
        ],
        'contract_accepted' => [
            'pattern' => '/^1$/',
            'message' => 'Необходимо принять условия контракта'
        ]
    ];

    // Валидация данных и сохранение старых значений
    foreach ($validationRules as $field => $rule) {
        $value = $_POST[$field] ?? '';
        
        if ($field === 'languages') {
            $value = implode(',', $_POST['languages'] ?? []);
        } elseif ($field === 'contract_accepted') {
            $value = isset($_POST['contract_accepted']) ? '1' : '';
        }
        
        $oldValues[$field] = $value;
        
        if (!preg_match($rule['pattern'], $value)) {
            $errors[$field] = $rule['message'];
        }
    }

    // Дополнительная проверка даты рождения
    if (empty($errors['birthdate'])) {
        $birthdate = DateTime::createFromFormat('Y-m-d', $_POST['birthdate']);
        $today = new DateTime();
        if ($birthdate > $today) {
            $errors['birthdate'] = 'Дата рождения не может быть в будущем';
        }
    }

    // Если есть ошибки - сохраняем в cookies и перенаправляем
    if (!empty($errors)) {
        setcookie('form_errors', json_encode($errors), 0, '/');
        setcookie('old_values', json_encode($oldValues), 0, '/');
        header('Location: index.php');
        exit;
    }

    // Если ошибок нет - сохраняем в БД
    try {
        $pdo->beginTransaction();
        
        // Сохраняем основную информацию
        $stmt = $pdo->prepare("INSERT INTO applications (name, phone, email, birthdate, gender, bio, contract_accepted) 
                              VALUES (:name, :phone, :email, :birthdate, :gender, :bio, :contract)");
        $stmt->execute([
            ':name' => $_POST['name'],
            ':phone' => $_POST['phone'],
            ':email' => $_POST['email'],
            ':birthdate' => $_POST['birthdate'],
            ':gender' => $_POST['gender'],
            ':bio' => $_POST['bio'],
            ':contract' => isset($_POST['contract_accepted']) ? 1 : 0
        ]);
        
        $applicationId = $pdo->lastInsertId();
        
        // Сохраняем языки программирования
        if (!empty($_POST['languages'])) {
            $stmt = $pdo->prepare("INSERT INTO application_languages (application_id, language_id) 
                                  SELECT ?, id FROM languages WHERE name = ?");
            foreach ($_POST['languages'] as $lang) {
                $stmt->execute([$applicationId, $lang]);
            }
        }
        
        $pdo->commit();
        
        // Сохраняем значения в cookies на год
        foreach ($oldValues as $field => $value) {
            setcookie("saved_$field", $value, time() + 60*60*24*365, '/');
        }
        
        // Удаляем cookies с ошибками
        setcookie('form_errors', '', time() - 3600, '/');
        setcookie('old_values', '', time() - 3600, '/');
        
        header('Location: index.php?success=1');
        exit;
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Ошибка при сохранении данных: " . $e->getMessage());
    }
}
?>
