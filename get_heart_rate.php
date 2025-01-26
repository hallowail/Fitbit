<?php
$tokens = json_decode(file_get_contents('tokens.json'), true);
$access_token = $tokens['access_token']; // Haal het access token op

$url = 'https://api.fitbit.com/1/user/-/activities/heart/date/today/1d.json';

$options = [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $access_token",
    ],
];

$curl = curl_init();
curl_setopt_array($curl, $options);
$response = curl_exec($curl);
$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

if ($http_code === 200) {
    $data = json_decode($response, true);
    echo json_encode($data);
} else {
    echo json_encode(['error' => 'Unable to fetch data. HTTP Code: ' . $http_code]);
}
?>

