<?php
// ============= ВАШИ ДАННЫЕ =============
define('BOT_TOKEN', '8350350737:AAEzkLHZtifhH-CUiPCf47wXjWvhaQZo-ns');
define('CHAT_ID', '783773797');
// ========================================

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Только POST запросы']);
    exit;
}

// Получаем данные
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Неверные данные']);
    exit;
}

$name = htmlspecialchars($data['name'] ?? '');
$email = htmlspecialchars($data['email'] ?? '');
$phone = htmlspecialchars($data['phone'] ?? '');
$service = htmlspecialchars($data['service'] ?? '');
$message = htmlspecialchars($data['message'] ?? 'Не указано');

// Валидация
if (empty($name) || empty($email) || empty($phone) || empty($service)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Заполните все обязательные поля']);
    exit;
}

$services = [
    'psychology' => 'Психологическое консультирование (5 000 ₽)',
    'sexology' => 'Сексология (6 000 ₽)',
    'energy' => 'Энерготерапия (7 000 ₽)',
    'body' => 'Телесная терапия (8 000 ₽)'
];

// Формируем сообщение
$text = "🆕 Новая заявка с сайта!\n\n";
$text .= "👤 Имя: $name\n";
$text .= "📧 Email: $email\n";
$text .= "📱 Телефон: $phone\n";
$text .= "💼 Услуга: " . ($services[$service] ?? $service) . "\n";
$text .= "💬 Сообщение: $message\n";
$text .= "📅 Дата: " . date('d.m.Y H:i:s');

// URL для отправки
$telegramUrl = "https://api.telegram.org/bot" . BOT_TOKEN . "/sendMessage";

// Данные для отправки
$postData = [
    'chat_id' => CHAT_ID,
    'text' => $text,
    'parse_mode' => 'HTML'
];

// Пробуем 3 способа отправки

// СПОСОБ 1: CURL (самый надёжный)
if (function_exists('curl_init')) {
    $ch = curl_init($telegramUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if ($result && $httpCode == 200) {
        $response = json_decode($result, true);
        
        if (isset($response['ok']) && $response['ok']) {
            echo json_encode(['success' => true, 'message' => 'Заявка успешно отправлена!']);
            exit;
        }
    }
}

// СПОСОБ 2: file_get_contents
if (ini_get('allow_url_fopen')) {
    $options = [
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'content' => http_build_query($postData),
            'timeout' => 10,
            'ignore_errors' => true
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false
        ]
    ];
    
    $context = stream_context_create($options);
    $result = @file_get_contents($telegramUrl, false, $context);
    
    if ($result) {
        $response = json_decode($result, true);
        
        if (isset($response['ok']) && $response['ok']) {
            echo json_encode(['success' => true, 'message' => 'Заявка успешно отправлена!']);
            exit;
        }
    }
}

// СПОСОБ 3: Через GET запрос (последний шанс)
$getText = $text;
$getText = urlencode($getText);
$getUrl = "https://api.telegram.org/bot" . BOT_TOKEN . "/sendMessage?chat_id=" . CHAT_ID . "&text=" . $getText;

$result = @file_get_contents($getUrl);

if ($result) {
    $response = json_decode($result, true);
    
    if (isset($response['ok']) && $response['ok']) {
        echo json_encode(['success' => true, 'message' => 'Заявка успешно отправлена!']);
        exit;
    }
}

// Если ничего не сработало
http_response_code(500);
echo json_encode([
    'success' => false, 
    'message' => 'Ошибка отправки. Свяжитесь через Telegram/WhatsApp',
    'debug' => [
        'curl' => function_exists('curl_init') ? 'available' : 'not available',
        'fopen' => ini_get('allow_url_fopen') ? 'enabled' : 'disabled',
        'curl_error' => $curlError ?? 'no error',
        'http_code' => $httpCode ?? 'unknown'
    ]
]);
?>
