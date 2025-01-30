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

// Haal huidige gegevens op
$activityUrl = 'https://api.fitbit.com/1/user/-/activities/date/today.json';
$activityData = fetchFitbitData($activityUrl, $accessToken);

$sleepUrl = 'https://api.fitbit.com/1.2/user/-/sleep/date/today.json';
$sleepData = fetchFitbitData($sleepUrl, $accessToken);

$heartRateUrl = 'https://api.fitbit.com/1/user/-/activities/heart/date/today/1d.json';
$heartRateData = fetchFitbitData($heartRateUrl, $accessToken);

// Dummy temperatuurdata (vervang met API-call als beschikbaar)
$temperature = 37;

// Verwerk data
$steps = $activityData['summary']['steps'] ?? 0;
$calories = $activityData['summary']['caloriesOut'] ?? 0;
$heartRate = $heartRateData['activities-heart'][0]['value']['restingHeartRate'] ?? 'Onbekend';
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
        .hidden {
            display: none;
        }
        .button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Fitbit Dashboard</h1>

        <!-- Overzicht huidige gegevens -->
        <div class="card">
            <h2>Huidige gegevens</h2>
            <p><strong>Stappen:</strong> <?php echo htmlspecialchars($steps); ?></p>
            <p><strong>Verbrande calorieën:</strong> <?php echo htmlspecialchars($calories); ?> kcal</p>
            <p><strong>Rusthartslag:</strong> <?php echo htmlspecialchars($heartRate); ?> bpm</p>
            <p><strong>Diepe slaap:</strong> <?php echo htmlspecialchars($sleepScore); ?> minuten</p>
            <p><strong>Temperatuur:</strong> <?php echo htmlspecialchars($temperature); ?> °C</p>

            <!-- Knoppen voor meer details -->
            <button class="button" onclick="toggleDetails('activityDetails')">Meer over Activiteit</button>
            <button class="button" onclick="toggleDetails('heartRateDetails')">Meer over Hartslag</button>
            <button class="button" onclick="toggleDetails('sleepDetails')">Meer over Slaapkwaliteit</button>
            <button class="button" onclick="toggleDetails('temperatureDetails')">Meer over Temperatuur</button>
        </div>

        <!-- Details per categorie -->
        <div id="activityDetails" class="card hidden">
            <h2>Activiteit Details</h2>
            <canvas id="activityChart"></canvas>
        </div>

        <div id="heartRateDetails" class="card hidden">
            <h2>Hartslag Details</h2>
            <canvas id="heartRateChart"></canvas>
        </div>

        <div id="sleepDetails" class="card hidden">
            <h2>Slaapkwaliteit Details</h2>
            <canvas id="sleepChart"></canvas>
        </div>

        <div id="temperatureDetails" class="card hidden">
            <h2>Temperatuur Details</h2>
            <p>Geen grafiek beschikbaar voor temperatuur.</p>
        </div>
    </div>

    <script>
        // Functie om details te tonen/verbergen
        function toggleDetails(id) {
            const details = document.getElementById(id);
            details.classList.toggle('hidden');
        }

        // Simulatie van grafiekdata (vervang met echte data)
        const activityCtx = document.getElementById('activityChart').getContext('2d');
        const activityChart = new Chart(activityCtx, {
            type: 'line',
            data: {
                labels: ['ma', 'di', 'wo', 'do', 'vr', 'za', 'zo'],
                datasets: [{
                    label: 'Stappen',
                    data: [3000, 5000, 7000, 8000, 6000, 9000, 10000],
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
                            text: 'Dag'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Aantal stappen'
                        }
                    }
                }
            }
        });

        const heartRateCtx = document.getElementById('heartRateChart').getContext('2d');
        const heartRateChart = new Chart(heartRateCtx, {
            type: 'line',
            data: {
                labels: ['ma', 'di', 'wo', 'do', 'vr', 'za', 'zo'],
                datasets: [{
                    label: 'Rusthartslag',
                    data: [60, 62, 64, 63, 65, 61, 60],
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
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
                            text: 'Dag'
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

        const sleepCtx = document.getElementById('sleepChart').getContext('2d');
        const sleepChart = new Chart(sleepCtx, {
            type: 'line',
            data: {
                labels: ['ma', 'di', 'wo', 'do', 'vr', 'za', 'zo'],
                datasets: [{
                    label: 'Diepe Slaap (min)',
                    data: [40, 50, 60, 55, 45, 70, 65],
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
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
                            text: 'Dag'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Minuten'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
