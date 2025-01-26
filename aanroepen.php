<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Fitbit API Aanroepen</title>
</head>
<body>
<?php
$tokens = json_decode(file_get_contents('tokens.json'), true);
$access_token = $tokens['access_token']; // Haal het access token op

$url = 'https://api.fitbit.com/1/user/-/activities/heart/date/today/1d.json';

// Vervang [] door array() voor compatibiliteit met oudere PHP-versies
$options = array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer $access_token",
    ),
    CURLOPT_TIMEOUT => 30,  // Timeout instellen voor de cURL-aanroep
    CURLOPT_FOLLOWLOCATION => true, // Volg eventuele redirects
);

$curl = curl_init();
curl_setopt_array($curl, $options);
$response = curl_exec($curl);

// Controleer op fouten in de cURL-aanroep
if (curl_errno($curl)) {
    echo 'cURL Error: ' . curl_error($curl);
} else {
    // Controleer of de respons geldig is
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    if ($http_code == 200) {
        $data = json_decode($response, true);
        echo '<pre>';
        print_r($data); // Gegevens van Fitbit API weergeven
        echo '</pre>';
    } else {
        echo "Error: HTTP status code $http_code";
        echo '<pre>';
        print_r($response); // API respons weergeven bij fout
        echo '</pre>';
    }
}

curl_close($curl);
?>

</body>
</html>

