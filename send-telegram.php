<?php
// Настройки (замените на свои данные)
$botToken = '8350350737:AAEzkLHZtifhH-CUiPCf47wXjWvhaQZo-ns';
$chatId = '783773797';

// Разрешаем запросы с вашего сайта
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

// Получаем данные из формы
$data = json_decode(file_get_contents('php://input'), true);

$name = htmlspecialchars($data['name']);
$email = htmlspecialchars($data['email']);
$phone = htmlspecialchars($data['phone']);
$service = htmlspecialchars($data['service']);
$message = htmlspecialchars($data['message'] ?? 'Не указано');

// Названия услуг
$services = [
    'psychology' => 'Психологическое консультирование (5 000 ₽)',
    'sexology' => 'Сексология (6 000 ₽)',
    'energy' => 'Энерготерапия (7 000 ₽)',
    'body' => 'Телесная терапия (8 000 ₽)'
];

// Формируем сообщение
$text = "🆕 Новая заявка!\n\n";
$text .= "👤 Имя: $name\n";
$text .= "📧 Email: $email\n";
$text .= "📱 Телефон: $phone\n";
$text .= "💼 Услуга: " . $services[$service] . "\n";
$text .= "💬 Сообщение: $message";

// Отправляем в Telegram
$url = "https://api.telegram.org/bot$botToken/sendMessage";
$postData = [
    'chat_id' => $chatId,
    'text' => $text
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);

// Ответ клиенту
echo json_encode(['success' => true, 'message' => 'Заявка отправлена!']);
?>
