<?php
$token = "7695881033:AAHaJxuazvLpfzbqXP6L_M_x_X1As8fGM5c";
$apiUrl = "https://api.telegram.org/bot$token";

$update = json_decode(file_get_contents("php://input"), TRUE);

if ($update && isset($update["message"])) {
    $chatId = $update["message"]["chat"]["id"];
    $text = trim($update["message"]["text"]);

    if ($text == "/start") {
        sendMessage($chatId, "Welcome! Send me any link, and I’ll shorten it for you. Use /help to learn more.");
    } elseif ($text == "/help") {
        sendMessage($chatId, "Send me a valid link, and I'll give you a shortened version. Make sure the link is accessible!");
    } else {
        if (filter_var($text, FILTER_VALIDATE_URL)) {
            if (checkUrl($text)) {
                $shortUrl = shortenUrl($text);
                if ($shortUrl) {
                    sendMessage($chatId, "🔗 Original: $text\n\n⚡ Shortened: $shortUrl");
                } else {
                    sendMessage($chatId, "Failed to shorten the link. Try again later.");
                }
            } else {
                sendMessage($chatId, "The link seems unreachable. Please check it and try again.");
            }
        } else {
            sendMessage($chatId, "Invalid URL. Please send a valid and complete link.");
        }
    }
}

function shortenUrl($url) {
    $apiUrl = "https://is.gd/create.php?format=simple&url=" . urlencode($url);
    $shortUrl = file_get_contents($apiUrl);
    return $shortUrl ?: false;
}

function checkUrl($url) {
    $headers = @get_headers($url);
    return $headers && strpos($headers[0], '200') !== false;
}

function sendMessage($chatId, $message) {
    global $apiUrl;
    file_get_contents($apiUrl . "/sendMessage?chat_id=" . $chatId . "&text=" . urlencode($message));
}
?>Enter file contents here
