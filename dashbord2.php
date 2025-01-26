<?php
// Configuratie
$accessToken = 'eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiIyM1BUMlEiLCJzdWIiOiJCMjdQUFEiLCJpc3MiOiJGaXRiaXQiLCJ0eXAiOiJhY2Nlc3NfdG9rZW4iLCJzY29wZXMiOiJyc29jIHJlY2cgcnNldCByaXJuIHJveHkgcm51dCBycHJvIHJzbGUgcmNmIHJhY3QgcmxvYyBycmVzIHJ3ZWkgcmhyIHJ0ZW0iLCJleHAiOjE3Mzc0ODc2MjgsImlhdCI6MTczNzQ1ODgyOH0.Cc4x2FeyguGHoJ0xvUPf1IZrtKj2evtBqAszd9JjXYM';

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

// Haal gebruikergegevens op
$userProfileUrl = 'https://api.fitbit.com/1/user/-/profile.json';
$userProfile = fetchFitbitData($userProfileUrl, $accessToken);

// Haal activiteitgegevens op
$activityUrl = 'https://api.fitbit.com/1/user/-/activities/date/today.json';
$activityData = fetchFitbitData($activityUrl, $accessToken);

// Haal slaapgegevens op
$sleepUrl = 'https://api.fitbit.com/1.2/user/-/sleep/date/today.json';
$sleepData = fetchFitbitData($sleepUrl, $accessToken);

// Haal temperatuurgegevens op (simulatie, omdat Fitbit API dit niet standaard biedt)
$temperature = 37; // Vervang met een ander endpoint als je temperatuurdata hebt

// Verwerk data
$name = $userProfile['user']['fullName'] ?? 'Onbekend';
$age = $userProfile['user']['age'] ?? 'Onbekend';
$height = $userProfile['user']['height'] ?? 'Onbekend';
$weight = $userProfile['user']['weight'] ?? 'Onbekend';

$steps = $activityData['summary']['steps'] ?? 0;
$calories = $activityData['summary']['caloriesOut'] ?? 0;
$heartRate = $activityData['summary']['restingHeartRate'] ?? 'Onbekend';

$sleepScore = $sleepData['summary']['stages']['deep'] ?? 0;
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
        .data-item {
            margin: 10px 0;
        }
        .data-item span {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Fitbit Dashboard</h1>

        <!-- Gebruikersgegevens -->
        <div class="card">
            <h2>Gebruikersgegevens</h2>
            <p class="data-item"><span>Naam:</span> <?php echo htmlspecialchars($name); ?></p>
            <p class="data-item"><span>Leeftijd:</span> <?php echo htmlspecialchars($age); ?></p>
            <p class="data-item"><span>Lengte:</span> <?php echo htmlspecialchars($height); ?> cm</p>
            <p class="data-item"><span>Gewicht:</span> <?php echo htmlspecialchars($weight); ?> kg</p>
        </div>

        <!-- Activiteitgegevens -->
        <div class="card">
            <h2>Activiteit</h2>
            <p class="data-item"><span>Stappen:</span> <?php echo htmlspecialchars($steps); ?></p>
            <p class="data-item"><span>Verbrande calorieën:</span> <?php echo htmlspecialchars($calories); ?> kcal</p>
        </div>

        <!-- Hartslag -->
        <div class="card">
            <h2>Hartslag</h2>
            <p class="data-item"><span>Rusthartslag:</span> <?php echo htmlspecialchars($heartRate); ?> bpm</p>
        </div>

        <!-- Slaapkwaliteit -->
        <div class="card">
            <h2>Slaapkwaliteit</h2>
            <p class="data-item"><span>Diepe slaap:</span> <?php echo htmlspecialchars($sleepScore); ?> minuten</p>
        </div>

        <!-- Temperatuur -->
        <div class="card">
            <h2>Temperatuur</h2>
            <p class="data-item"><span>Temperatuur:</span> <?php echo htmlspecialchars($temperature); ?> °C</p>
        </div>

        <!-- Grafiek -->
        <div class="card">
            <h2>Hartslaggrafiek</h2>
            <canvas id="heartRateChart"></canvas>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('heartRateChart').getContext('2d');
        const heartRateChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($dates ?? ['Geen data']); ?>,
                datasets: [{
                    label: 'Rusthartslag (bpm)',
                    data: <?php echo json_encode($heartRates ?? [0]); ?>,
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
