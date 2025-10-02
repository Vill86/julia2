<?php
// ==========================================================
// <<< ВСТАВЬТЕ ВАШИ ДАННЫЕ СЮДА >>>
// ==========================================================
define('BOT_TOKEN', '8350350737:AAEzkLHZtifhH-CUiPCf47wXjWvhaQZo-ns');
define('CHAT_ID', '783773797'); // Можно и несколько через запятую: 'id1,id2'
// ==========================================================

// --- НАЧАЛО СКРИПТА ---
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON received']);
    exit;
}

$name = htmlspecialchars($data['name'] ?? 'Не указано');
$email = htmlspecialchars($data['email'] ?? 'Не указан');
$phone = htmlspecialchars($data['phone'] ?? 'Не указан');
$serviceKey = htmlspecialchars($data['service'] ?? 'Не выбрана');
$message = htmlspecialchars($data['message'] ?? 'Нет');

$services = [
    'psychology' => 'Психологическое консультирование (5 000 ₽)',
    'sexology' => 'Сексология (6 000 ₽)',
    'energy' => 'Энерготерапия (7 000 ₽)',
    'body' => 'Телесная терапия (8 000 ₽)'
];
$serviceText = $services[$serviceKey] ?? $serviceKey;

$text = "<b>🆕 Новая заявка с сайта!</b>\n\n";
$text .= "<b>👤 Имя:</b> {$name}\n";
$text .= "<b>📧 Email:</b> {$email}\n";
$text .= "<b>📱 Телефон:</b> {$phone}\n";
$text .= "<b>💼 Услуга:</b> {$serviceText}\n";
$text .= "<b>💬 Сообщение:</b> {$message}\n";
$text .= "<em>📅 " . date('d.m.Y H:i:s') . "</em>";

// --- Функция отправки ---
function sendMessage($token, $chatId, $text) {
    $url = "https://api.telegram.org/bot{$token}/sendMessage";
    $params = [
        'chat_id' => $chatId,
        'text' => $text,
        'parse_mode' => 'HTML',
    ];
    $lastError = '';

    // Способ 1: cURL (самый надежный)
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); // Таймаут соединения
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);      // Таймаут выполнения
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Отключаем проверку SSL
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Отключаем проверку SSL
        $result = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($result && !$curlError) {
            $response = json_decode($result, true);
            if ($response && $response['ok']) {
                return ['success' => true];
            }
        }
        $lastError = $curlError ?: 'cURL request failed without specific error.';
    }

    // Способ 2: file_get_contents (если cURL не сработал)
    if (ini_get('allow_url_fopen')) {
        $options = [
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query($params),
                'timeout' => 10,
            ],
            // Отключаем проверку SSL для этого метода тоже
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ];
        $context = stream_context_create($options);
        $result = @file_get_contents($url, false, $context);
        if ($result) {
            $response = json_decode($result, true);
            if ($response && $response['ok']) {
                return ['success' => true];
            }
        }
        $lastError = $lastError ?: 'file_get_contents failed.';
    }
    
    return ['success' => false, 'error' => $lastError];
}

// --- Отправляем сообщения всем получателям ---
$chatIds = explode(',', CHAT_ID);
$allSent = true;
$errors = [];

foreach ($chatIds as $id) {
    $result = sendMessage(BOT_TOKEN, trim($id), $text);
    if (!$result['success']) {
        $allSent = false;
        $errors[] = "Failed to send to {$id}: " . ($result['error'] ?? 'Unknown error');
    }
}

if ($allSent) {
    echo json_encode(['success' => true, 'message' => 'Заявка успешно отправлена!']);
} else {
    http_response_code(500);
    // Не показываем техническую ошибку пользователю, но можем ее логировать
    // error_log(implode('; ', $errors)); 
    echo json_encode(['success' => false, 'message' => 'Ошибка сервера при отправке уведомления.']);
}
