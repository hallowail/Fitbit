<?php
// Configuratie
$tokens = json_decode(file_get_contents('tokens.json'), true);
$accessToken = $tokens['access_token']; // Haal het access token op
$userId = "-"; // "-" voor de ingelogde gebruiker
$date = date('Y-m-d'); // Huidige datum
$url = "https://api.fitbit.com/1/user/$userId/activities/heart/date/$date/1w.json";

// cURL verzoek
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $accessToken",
]);

$response = curl_exec($ch);
curl_close($ch);

if ($response === false) {
    die("Error tijdens het ophalen van gegevens.");
}

// JSON decoderen
$data = json_decode($response, true);

// Controleer of er gegevens zijn
if (!isset($data['activities-heart'])) {
    die("Geen hartslaggegevens gevonden.");
}

// Gegevens voorbereiden voor grafiek
$dates = [];
$restingHeartRates = [];

foreach ($data['activities-heart'] as $dayData) {
    $dates[] = $dayData['dateTime'];
    $restingHeartRates[] = $dayData['value']['restingHeartRate'] ?? 0; // Default naar 0 als geen waarde
}

// Data beschikbaar maken voor JavaScript
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hartslag Grafiek</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <canvas id="heartRateChart" width="400" height="200"></canvas>
    <script>
        const ctx = document.getElementById('heartRateChart').getContext('2d');
        const heartRateChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($dates); ?>,
                datasets: [{
                    label: 'Rusthartslag (bpm)',
                    data: <?php echo json_encode($restingHeartRates); ?>,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Datum'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Hartslag (bpm)'
                        },
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
