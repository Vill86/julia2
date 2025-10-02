<?php
// ============= ВАШИ ДАННЫЕ =============
define('BOT_TOKEN', '8350350737:AAEzkLHZtifhH-CUiPCf47wXjWvhaQZo-ns');  // Например: 7012345678:AAHdJk3nq9wQaBcDeFgHiJkLmNoPqRsTuVw
define('CHAT_ID', '783773797'); // Например: 123456789
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

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Неверные данные']);
    exit;
}

$name = htmlspecialchars(trim($data['name'] ?? ''), ENT_QUOTES, 'UTF-8');
$email = filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL);
$phone = htmlspecialchars(trim($data['phone'] ?? ''), ENT_QUOTES, 'UTF-8');
$service = htmlspecialchars(trim($data['service'] ?? ''), ENT_QUOTES, 'UTF-8');
$message = htmlspecialchars(trim($data['message'] ?? 'Не указано'), ENT_QUOTES, 'UTF-8');

if (empty($name) || !$email || empty($phone) || empty($service)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Заполните все обязательные поля']);
    exit;
}

$services = [
    'psychology' => 'Психологическое консультирование (5 000 ₽)',
    'sexology'   => 'Сексология (6 000 ₽)',
    'energy'     => 'Энерготерапия (7 000 ₽)',
    'body'       => 'Телесная терапия (8 000 ₽)'
];

$text = "🆕 <b>Новая заявка с сайта!</b>\n\n";
$text .= "👤 <b>Имя:</b> $name\n";
$text .= "📧 <b>Email:</b> $email\n";
$text .= "📱 <b>Телефон:</b> $phone\n";
$text .= "💼 <b>Услуга:</b> " . ($services[$service] ?? htmlspecialchars($service, ENT_QUOTES, 'UTF-8')) . "\n";
$text .= "💬 <b>Сообщение:</b> $message\n";
$text .= "📅 <b>Дата:</b> " . date('d.m.Y H:i:s');

// 🔥 ВАЖНО: УБРАЛ ЛИШНИЕ ПРОБЕЛЫ В URL!
$telegramUrl = "https://api.telegram.org/bot" . BOT_TOKEN . "/sendMessage";

$postData = [
    'chat_id' => CHAT_ID,
    'text' => $text,
    'parse_mode' => 'HTML'
];

// Способ 1: cURL
if (function_exists('curl_init')) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $telegramUrl,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 15,
    ]);

    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($result && $httpCode === 200) {
        $response = json_decode($result, true);
        if (isset($response['ok']) && $response['ok']) {
            echo json_encode(['success' => true, 'message' => 'Заявка успешно отправлена!']);
            exit;
        }
    }
}

// Способ 2: file_get_contents
if (ini_get('allow_url_fopen')) {
    $options = [
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => http_build_query($postData),
            'timeout' => 15,
            'ignore_errors' => true
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false
        ]
    ];

    $context = stream_context_create($options);
    $result = @file_get_contents($telegramUrl, false, $context);

    if ($result !== false) {
        $response = json_decode($result, true);
        if (isset($response['ok']) && $response['ok']) {
            echo json_encode(['success' => true, 'message' => 'Заявка успешно отправлена!']);
            exit;
        }
    }
}

// Если всё провалилось
http_response_code(500);
echo json_encode([
    'success' => false,
    'message' => 'Ошибка отправки. Свяжитесь через Telegram/WhatsApp.',
    'debug' => [
        'curl_available' => function_exists('curl_init'),
        'fopen_enabled' => ini_get('allow_url_fopen'),
        'http_code' => $httpCode ?? null,
        'curl_error' => $curlError ?? null,
        'bot_token_set' => !empty(BOT_TOKEN),
        'chat_id_set' => !empty(CHAT_ID)
    ]
]);
?>
