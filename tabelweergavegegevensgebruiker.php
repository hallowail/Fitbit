<?php
// Configuratie
$tokens = json_decode(file_get_contents('tokens.json'), true);
$accessToken = $tokens['access_token']; // Haal het access token op

// API URL voor gebruikersgegevens (als voorbeeld)
$url = 'https://api.fitbit.com/1/user/-/profile.json';

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
    $user = $data['user'];

    // HTML-weergave
    echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Fitbit Gebruikersgegevens</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 20px;
        }
        table {
            width: 50%;
            margin: 0 auto;
            border-collapse: collapse;
            background-color: #ffffff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px 15px;
            border: 1px solid #dddddd;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <h2 style='text-align: center;'>Fitbit Gebruikersgegevens</h2>
    <table>
        <tr>
            <th>Eigenschap</th>
            <th>Waarde</th>
        </tr>
        <tr>
            <td>Naam</td>
            <td>{$user['fullName']}</td>
        </tr>
        <tr>
            <td>Geslacht</td>
            <td>{$user['gender']}</td>
        </tr>
        <tr>
            <td>Leeftijd</td>
            <td>{$user['age']}</td>
        </tr>
        <tr>
            <td>Lengte</td>
            <td>{$user['height']} cm</td>
        </tr>
        <tr>
            <td>Gewicht</td>
            <td>{$user['weight']} kg</td>
        </tr>
        <tr>
            <td>Land</td>
            <td>{$user['country']}</td>
        </tr>
    </table>
</body>
</html>";
} else {
    echo "API-aanroep mislukt. HTTP-code: $httpCode";
}
?>