<?php
$accessToken = 'eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiIyM1BUMlEiLCJzdWIiOiJCMjdQUFEiLCJpc3MiOiJGaXRiaXQiLCJ0eXAiOiJhY2Nlc3NfdG9rZW4iLCJzY29wZXMiOiJyc29jIHJlY2cgcnNldCByaXJuIHJveHkgcnBybyBybnV0IHJzbGUgcmNmIHJhY3QgcnJlcyBybG9jIHJ3ZWkgcmhyIHJ0ZW0iLCJleHAiOjE3Mzc0MzQwOTMsImlhdCI6MTczNzQwNTI5M30.GDmCxeD_Pl18Wg_Nrz9tt07ieLcazNxjpmnxskB2eUk'; // Zet hier je toegangstoken in
$refreshToken = '0b0107c9c4ccf29701e70770d7e319944ff7d2d45a81143f94be06a19fbfe80e'; // Zet hier je refreshtoken in

// Fitbit API endpoint voor gebruikersdata
$profileUrl = 'https://api.fitbit.com/1/user/-/profile.json';

// Haal gebruikersinformatie op
$options = [
    "http" => [
        "header" => "Authorization: Bearer $accessToken"
    ]
];
$context = stream_context_create($options);
$response = file_get_contents($profileUrl, false, $context);
$userData = json_decode($response, true);

// Fitbit API endpoint voor activiteitendata
$activitiesUrl = 'https://api.fitbit.com/1/user/-/activities/date/today.json';
$response = file_get_contents($activitiesUrl, false, $context);
$activityData = json_decode($response, true);

// Haal activiteit gegevens op
$steps = $activityData['activities-log']['steps'];
$calories = $activityData['activities-log']['caloriesOut'];
$distance = $activityData['activities-log']['distance'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitbit Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .header {
            background-color: #3f51b5;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .container {
            padding: 20px;
        }
        .card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 10px 0;
        }
        .card h3 {
            margin: 0;
        }
        .card p {
            color: #555;
        }
        canvas {
            max-width: 100%;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Fitbit Dashboard</h1>
        <p>Welcome, <?php echo $userData['user']['fullName']; ?></p>
    </div>

    <div class="container">
        <div class="card">
            <h3>Steps Today</h3>
            <p><?php echo number_format($steps); ?> steps</p>
        </div>
        <div class="card">
            <h3>Calories Burned</h3>
            <p><?php echo number_format($calories); ?> kcal</p>
        </div>
        <div class="card">
            <h3>Distance Covered</h3>
            <p><?php echo number_format($distance, 2); ?> km</p>
        </div>
        
        <div class="card">
            <h3>Activity Overview</h3>
            <canvas id="activityChart"></canvas>
        </div>
    </div>

    <script>
        var ctx = document.getElementById('activityChart').getContext('2d');
        var activityChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Steps', 'Calories', 'Distance'],
                datasets: [{
                    label: 'Today\'s Activity',
                    data: [<?php echo $steps; ?>, <?php echo $calories; ?>, <?php echo $distance; ?>],
                    backgroundColor: ['#3f51b5', '#4caf50', '#ff9800'],
                    borderColor: ['#303f9f', '#388e3c', '#f57c00'],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

</body>
</html>
