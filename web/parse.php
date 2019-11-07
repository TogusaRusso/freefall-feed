<?php
// константы
$url = 'http://freefall.purrsia.com';
$token = $_ENV['TOKEN'];
$chatId = $_ENV['CHAT_ID'];
// получаем страницу и извлекаем url
$webpage = file_get_contents($url . '/');
$matches = [];
preg_match(
	'/<a href="(.+)"><img/im',
	$webpage,
	$matches
);
// Постим на канал
if (isset($matches[1])) {
	file_get_contents(
		"https://api.telegram.org/bot$token/sendPhoto?"
		. http_build_query([
			'photo' => $url . $matches[1],
			'chat_id' => $chatId,
		])
	);
}
