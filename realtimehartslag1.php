<?php
// Configuratie
$tokens = json_decode(file_get_contents('tokens.json'), true);
$accessToken = $tokens['access_token']; // Haal het access token op

// API URL voor hartslaggegevens
$url = 'https://api.fitbit.com/1/user/-/activities/heart/date/today/1d/1min.json';

// cURL-instelling
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $accessToken",
]);

// API-aanroep
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Verwerking van het antwoord
if ($httpCode === 200) {
    $data = json_decode($response, true);
    $heartRateData = $data['activities-heart-intraday']['dataset'] ?? [];
    
    if (!empty($heartRateData)) {
        $latestEntry = end($heartRateData); // Laatste meting
        $currentTime = date('H:i:s');
        echo "[$currentTime] Tijd: " . $latestEntry['time'] . "\n";
        echo "[$currentTime] Hartslag: " . $latestEntry['value'] . " bpm\n";
    } else {
        echo "Geen hartslaggegevens gevonden. Mogelijk wordt het apparaat niet gedragen of is de hartslagmeting uitgeschakeld.\n";
    }
} else {
    echo "API-aanroep mislukt. HTTP-code: $httpCode\n";
    echo "Response: $response\n";
}?>
