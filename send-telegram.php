<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Диагностика Telegram бота</h1>";
echo "<hr>";

// ВСТАВЬТЕ СЮДА ВАШИ ДАННЫЕ
$botToken = '8350350737:AAEzkLHZtifhH-CUiPCf47wXjWvhaQZo-ns';
$chatId = '783773797';

echo "<h2>Шаг 1: Проверка токена бота</h2>";
$url = "https://api.telegram.org/bot$botToken/getMe";
$response = @file_get_contents($url);

if ($response === false) {
    echo "❌ <span style='color: red;'>Не могу подключиться к Telegram API</span><br>";
    echo "Возможно проблема с интернетом на хостинге<br>";
} else {
    $data = json_decode($response, true);
    echo "<pre>";
    print_r($data);
    echo "</pre>";
    
    if (isset($data['ok']) && $data['ok']) {
        echo "✅ <span style='color: green;'>Токен правильный!</span><br>";
        echo "Имя бота: <b>" . $data['result']['first_name'] . "</b><br>";
        echo "Username: @" . $data['result']['username'] . "<br>";
    } else {
        echo "❌ <span style='color: red;'>Токен неправильный!</span><br>";
        exit;
    }
}

echo "<hr>";
echo "<h2>Шаг 2: Проверка chat_id</h2>";
echo "Ваш chat_id: <b>$chatId</b><br>";

if (empty($chatId) || $chatId == 'ВСТАВЬТЕ_ВАШ_CHAT_ID') {
    echo "❌ <span style='color: red;'>Chat ID не указан!</span><br>";
    exit;
}

echo "<hr>";
echo "<h2>Шаг 3: Попытка отправить тестовое сообщение</h2>";

$testMessage = "🧪 Тестовое сообщение\nВремя: " . date('H:i:s d.m.Y');
$sendUrl = "https://api.telegram.org/bot$botToken/sendMessage";

$postData = [
    'chat_id' => $chatId,
    'text' => $testMessage
];

$ch = curl_init($sendUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP код: <b>$httpCode</b><br>";
echo "Ответ от Telegram:<br>";
echo "<pre>";
print_r(json_decode($result, true));
echo "</pre>";

$resultData = json_decode($result, true);

if (isset($resultData['ok']) && $resultData['ok']) {
    echo "✅ <span style='color: green; font-size: 20px;'>СООБЩЕНИЕ ОТПРАВЛЕНО!</span><br>";
    echo "Проверьте Telegram - должно прийти сообщение от бота<br>";
} else {
    echo "❌ <span style='color: red; font-size: 20px;'>ОШИБКА!</span><br>";
    
    if (isset($resultData['description'])) {
        echo "Описание ошибки: <b>" . $resultData['description'] . "</b><br><br>";
        
        // Расшифровка популярных ошибок
        if (strpos($resultData['description'], 'chat not found') !== false) {
            echo "💡 <b>Решение:</b> Неправильный chat_id. Убедитесь что:<br>";
            echo "1. Вы нажали START у бота<br>";
            echo "2. Chat_id правильный<br>";
        }
        
        if (strpos($resultData['description'], 'bot was blocked') !== false) {
            echo "💡 <b>Решение:</b> Вы заблокировали бота. Разблокируйте его в Telegram<br>";
        }
        
        if (strpos($resultData['description'], 'Unauthorized') !== false) {
            echo "💡 <b>Решение:</b> Неправильный токен бота<br>";
        }
    }
}

echo "<hr>";
echo "<h2>Шаг 4: Проверка получения сообщений</h2>";
$updatesUrl = "https://api.telegram.org/bot$botToken/getUpdates";
$updates = @file_get_contents($updatesUrl);
$updatesData = json_decode($updates, true);

if (!empty($updatesData['result'])) {
    echo "Найдено сообщений: " . count($updatesData['result']) . "<br>";
    echo "<pre>";
    print_r($updatesData);
    echo "</pre>";
} else {
    echo "⚠️ <span style='color: orange;'>Нет сообщений от бота</span><br>";
    echo "Напишите боту что-нибудь и обновите страницу<br>";
}

echo "<hr>";
echo "<h2>📋 Чек-лист:</h2>";
echo "<ol>";
echo "<li>✓ Создали бота через @BotFather</li>";
echo "<li>✓ Получили токен</li>";
echo "<li>✓ Нашли бота в Telegram по username</li>";
echo "<li>✓ Нажали START</li>";
echo "<li>✓ Написали боту сообщение</li>";
echo "<li>✓ Получили свой chat_id</li>";
echo "<li>✓ Вставили токен и chat_id в этот файл</li>";
echo "</ol>";
?>
