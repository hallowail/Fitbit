<?php
// Laad de vernieuwde tokens uit het bestand (bijvoorbeeld tokens.json)
$tokens = json_decode(file_get_contents('tokens.json'), true);
$access_token = $tokens['access_token']; // Haal het access token op

// Fitbit API endpoint voor hartslag
$url = 'https://api.fitbit.com/1/user/-/activities/heart/date/today/1d.json';

// cURL setup voor de API-aanroep
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $access_token"
]);

// Voer de API-aanroep uit
$response = curl_exec($ch);
curl_close($ch);

// Verwerk de API-respons
if ($response) {
    $data = json_decode($response, true);
    // Controleer of de data correct is
    if (isset($data['activities-heart'])) {
        echo "Hartslag data: " . print_r($data['activities-heart'], true);
    } else {
        echo "Geen hartslagdata gevonden of een fout opgetreden.\n";
    }
} else {
    echo "Fout bij API-aanroep.\n";
}
?>
