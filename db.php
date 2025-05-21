<?php
$host = 'localhost';
$dbname = 'u68683';
$username = 'u68683';
$password = '4171583';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Удаляем существующие таблицы в правильном порядке (если они есть)
    $pdo->exec("DROP TABLE IF EXISTS application_languages");
    $pdo->exec("DROP TABLE IF EXISTS applications");
    $pdo->exec("DROP TABLE IF EXISTS languages");
    
    // Создаем таблицы заново с правильной структурой
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS applications (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            login VARCHAR(50) NOT NULL UNIQUE,  // Добавлено NOT NULL
            pass_hash VARCHAR(255) NOT NULL,     // Добавлено NOT NULL
            name VARCHAR(150) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            email VARCHAR(100) NOT NULL,
            birthdate DATE NOT NULL,
            gender ENUM('male','female','other') NOT NULL, // Добавлено 'other'
            bio TEXT NOT NULL,                   // Добавлено NOT NULL
            contract_accepted BOOLEAN NOT NULL DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS languages (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) NOT NULL UNIQUE
        ) ENGINE=InnoDB
    ");
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS application_languages (
            application_id INT UNSIGNED NOT NULL,
            language_id INT UNSIGNED NOT NULL,
            PRIMARY KEY (application_id, language_id),
            FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
            FOREIGN KEY (language_id) REFERENCES languages(id) ON DELETE CASCADE
        ) ENGINE=InnoDB
    ");
    
    // Заполнение языков программирования
    $stmt = $pdo->query("SELECT COUNT(*) FROM languages");
    if ($stmt->fetchColumn() == 0) {
        $languages = ['Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python', 'Java', 'Haskell', 'Clojure', 'Prolog', 'Scala'];
        $insert = $pdo->prepare("INSERT INTO languages (name) VALUES (?)");
        foreach ($languages as $lang) {
            $insert->execute([$lang]);
        }
    }
} catch (PDOException $e) {
    die("Ошибка подключения к БД: " . $e->getMessage());
}
?>
