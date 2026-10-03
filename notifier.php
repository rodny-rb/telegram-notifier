<?php
/**
 * BLACK ROSE Telegram Notification Relay
 * Hosted on Render.com (bypasses InfinityFree blocking)
 */

header('Content-Type: application/json');

// Get credentials from environment variables
$BOT_TOKEN = getenv('TG_BOT_TOKEN');
$CHAT_ID = getenv('TG_CHAT_ID');

// Validate
if (!$BOT_TOKEN || !$CHAT_ID) {
    http_response_code(500);
    echo json_encode(['error' => 'Missing environment variables']);
    exit;
}

// Handle incoming requests
$action = $_GET['action'] ?? $_POST['action'] ?? 'test';

switch ($action) {
    case 'user':
        $name = $_POST['name'] ?? 'Unknown';
        $email = $_POST['email'] ?? 'Unknown';
        $ip = $_POST['ip'] ?? 'Unknown';
        
        $text = "🔥 <b>NEW BLACK ROSE CLIENT</b>\n\n" .
                "👤 Name: " . htmlspecialchars($name) . "\n" .
                "📧 Email: " . htmlspecialchars($email) . "\n" .
                "🌐 IP: " . htmlspecialchars($ip) . "\n" .
                "⏰ " . date('Y-m-d H:i:s');
        break;
        
    case 'login':
        $username = $_POST['username'] ?? 'Unknown';
        $ip = $_POST['ip'] ?? 'Unknown';
        
        $text = "🔐 <b>CLIENT LOGIN</b>\n\n" .
                "👤 User: " . htmlspecialchars($username) . "\n" .
                "🌐 IP: " . htmlspecialchars($ip) . "\n" .
                "⏰ " . date('Y-m-d H:i:s');
        break;
        
    case 'test':
    default:
        $text = "🧪 <b>Test from BLACK ROSE Notifier</b>\n\n" .
                "Hosted on Render.com\n" .
                "⏰ " . date('Y-m-d H:i:s');
        break;
}

// Send to Telegram
$url = "https://api.telegram.org/bot{$BOT_TOKEN}/sendMessage";
$data = [
    'chat_id' => $CHAT_ID,
    'text' => $text,
    'parse_mode' => 'HTML'
];

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query($data),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_TIMEOUT => 15
]);

$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

$result = json_decode($response, true);

if ($result && $result['ok']) {
    echo json_encode(['status' => 'sent', 'action' => $action, 'time' => date('Y-m-d H:i:s')]);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'failed', 'error' => $error ?? $result]);
}
?>