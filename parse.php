<?php
// константы
$page = 'http://freefall.purrsia.com';
$token = $_ENV['TOKEN'];
$chatId = $_ENV['CHAT_ID'];
$user = $_ENV['DB_USER'];
$pass = $_ENV['DB_PASS'];
$dbname = $_ENV['DB_NAME'];
$host = $_ENV['DB_HOST'];
// получаем страницу и извлекаем url
$webpage = file_get_contents($page . '/');
$matches = [];
preg_match(
	'/<a href="(.+)"><img/im',
	$webpage,
	$matches
);
if (isset($matches[1])) {
    $url = $matches[1];
    // Проверяем уникальность url
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";
    $opt = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, $user, $pass, $opt);
    $stmt = $pdo->prepare("SELECT url FROM urls WHERE url=:url");
    $stmt->execute([':url' => $url]);
    if (!($stmt->fetch()))
    {
        // Сохраняем url
        $stmt = $pdo->prepare("INSERT INTO urls (url) VALUES(:url)");
        $stmt->execute([':url' => $url]);
        // Постим на канал
        file_get_contents(
            "https://api.telegram.org/bot$token/sendPhoto?"
            . http_build_query([
                'photo' => $page . $url,
                'chat_id' => $chatId,
            ])
        );
    }
}
