<?php
// Configuratie
define('TOKEN_FILE', 'tokens.json'); // Bestand om tokens op te slaan
define('CLIENT_ID', '23PT2Q');
define('CLIENT_SECRET', 'fc51d59dd6faea17c7fa964d65832f36');
define('REFRESH_TOKEN', '0c022cdaded80fe6577bf344b903708be525cfe28dad441c5504d6b16585c7b6'); // Zet hier je initiële refresh token

/**
 * Functie om tokens op te halen uit een bestand.
 */
function getTokens() {
    if (file_exists(TOKEN_FILE)) {
        return json_decode(file_get_contents(TOKEN_FILE), true);
    }
    return null;
}

/**
 * Functie om tokens op te slaan in een bestand.
 */
function saveTokens($tokens) {
    file_put_contents(TOKEN_FILE, json_encode($tokens));
}

/**
 * Functie om de access token te vernieuwen.
 */
function refreshAccessToken() {
    $url = 'https://api.fitbit.com/oauth2/token';
    $data = http_build_query([
        'grant_type' => 'refresh_token',
        'refresh_token' => REFRESH_TOKEN,
        'client_id' => CLIENT_ID,
        'client_secret' => CLIENT_SECRET
    ]);

    $options = [
        'http' => [
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => $data,
        ]
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    if ($response === false) {
        die('Error: Kan geen nieuwe token ophalen.');
    }

    $tokens = json_decode($response, true);
    saveTokens($tokens); // Sla de nieuwe tokens op
    return $tokens['access_token'];
}

/**
 * Functie om een API-aanroep te doen.
 */
function makeApiRequest($endpoint) {
    $tokens = getTokens();
    $accessToken = $tokens['access_token'] ?? null;

    // Controleer of de token nog geldig is
    if (!$accessToken) {
        $accessToken = refreshAccessToken();
    }

    $url = "https://api.fitbit.com$endpoint";
    $options = [
        'http' => [
            'header' => "Authorization: Bearer $accessToken\r\n",
            'method' => 'GET',
        ]
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    // Als de token is verlopen, vernieuw en probeer opnieuw
    if ($http_response_header[0] === 'HTTP/1.1 401 Unauthorized') {
        $accessToken = refreshAccessToken();
        $options['http']['header'] = "Authorization: Bearer $accessToken\r\n";
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
    }

    return $response;
}

// Voorbeeld: Hartslag ophalen
$response = makeApiRequest('/1/user/-/activities/heart/date/today/1d.json');
echo $response;
