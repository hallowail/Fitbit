<?php
// Configuratie
$accessToken = 'eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiIyM1BUMlEiLCJzdWIiOiJCMjdQUFEiLCJpc3MiOiJGaXRiaXQiLCJ0eXAiOiJhY2Nlc3NfdG9rZW4iLCJzY29wZXMiOiJyc29jIHJlY2cgcnNldCByaXJuIHJveHkgcm51dCBycHJvIHJzbGUgcmNmIHJhY3QgcmxvYyBycmVzIHJ3ZWkgcmhyIHJ0ZW0iLCJleHAiOjE3Mzc0ODc2MjgsImlhdCI6MTczNzQ1ODgyOH0.Cc4x2FeyguGHoJ0xvUPf1IZrtKj2evtBqAszd9JjXYM';
$apiUrl = 'https://api.fitbit.com/1/user/-/activities/heart/date/today/7d.json'; // Voor hartslaggegevens van de afgelopen 7 dagen

// Functie om API-data op te halen
function fetchFitbitData($url, $token) {
    $headers = [
        "Authorization: Bearer $token"
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($httpCode === 200) {
        return json_decode($response, true);
    } else {
        return null; // Geen data of fout
    }
}

// Haal data op
$data = fetchFitbitData($apiUrl, $accessToken);

// Verwerk data
$dates = [];
$heartRates = [];

if ($data && isset($data['activities-heart'])) {
    foreach ($data['activities-heart'] as $day) {
        $dates[] = $day['dateTime'];
        $heartRates[] = $day['value']['restingHeartRate'] ?? 0; // Gebruik 0 als er geen waarde is
    }
} else {
    $dates = ['Geen data'];
    $heartRates = [0];
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitbit Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .container {
            width: 80%;
            margin: auto;
        }
        .card {
            padding: 20px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Fitbit Dashboard</h1>
        <div class="card">
            <h2>Hartslaggegevens</h2>
            <canvas id="heartRateChart"></canvas>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('heartRateChart').getContext('2d');
        const heartRateChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($dates); ?>,
                datasets: [{
                    label: 'Rusthartslag (bpm)',
                    data: <?php echo json_encode($heartRates); ?>,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 1,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Datum'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Hartslag (bpm)'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
