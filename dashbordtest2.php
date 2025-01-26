<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitbit Web Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            display: flex;
            height: 100vh;
            background-color: #f7f9fc;
        }

        .sidebar {
            width: 250px;
            background-color: #3a3f47;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sidebar h1 {
            text-align: center;
            padding: 20px;
            font-size: 1.5em;
            background-color: #22262b;
        }

        .sidebar ul {
            list-style: none;
        }

        .sidebar ul li {
            padding: 15px 20px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .sidebar ul li:hover {
            background-color: #50575e;
        }

        .sidebar ul li a {
            color: #fff;
            text-decoration: none;
            display: block;
        }

        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .header {
            background-color: #fff;
            padding: 15px 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h2 {
            font-size: 1.5em;
            color: #333;
        }

        .dashboard {
            padding: 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .card {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .card h3 {
            margin-bottom: 10px;
            color: #555;
        }

        .card p {
            font-size: 1.2em;
            color: #333;
        }

        .chart-container {
            position: relative;
            height: 200px;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="sidebar">
        <h1>Fitbit</h1>
        <ul>
            <li><a href="#">Dashboard</a></li>
            <li><a href="#">Activity</a></li>
            <li><a href="#">Heart Rate</a></li>
            <li><a href="#">Sleep</a></li>
            <li><a href="#">Settings</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">
            <h2>Welcome, User</h2>
            <div>
                <button>Sync Data</button>
            </div>
        </div>

        <div class="dashboard">
            <?php
            session_start();
            // Fitbit API Access Token
            $access_token = 'eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiIyM1BUMlEiLCJzdWIiOiJCMjdQUFEiLCJpc3MiOiJGaXRiaXQiLCJ0eXAiOiJhY2Nlc3NfdG9rZW4iLCJzY29wZXMiOiJyc29jIHJlY2cgcnNldCByaXJuIHJveHkgcnBybyBybnV0IHJzbGUgcmNmIHJhY3QgcnJlcyBybG9jIHJ3ZWkgcmhyIHJ0ZW0iLCJleHAiOjE3Mzc0MzQwOTMsImlhdCI6MTczNzQwNTI5M30.GDmCxeD_Pl18Wg_Nrz9tt07ieLcazNxjpmnxskB2eUk';

            // Fetch Steps Data
            $steps_url = 'https://api.fitbit.com/1/user/-/activities/steps/date/today/1d.json';
            $steps_options = [
                CURLOPT_URL => $steps_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer $access_token",
                ],
            ];
            $steps_ch = curl_init();
            curl_setopt_array($steps_ch, $steps_options);
            $steps_response = curl_exec($steps_ch);
            curl_close($steps_ch);
            $steps_data = json_decode($steps_response, true);
            $steps = $steps_data['activities-steps'][0]['value'] ?? 'No new data';

            if ($steps === 'No new data') {
                $steps = $_SESSION['last_steps'] ?? '0';
            } else {
                $_SESSION['last_steps'] = $steps;
            }

            // Fetch Heart Rate Data
            $heartrate_url = 'https://api.fitbit.com/1/user/-/activities/heart/date/today/1d.json';
            $heartrate_options = [
                CURLOPT_URL => $heartrate_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer $access_token",
                ],
            ];
            $heartrate_ch = curl_init();
            curl_setopt_array($heartrate_ch, $heartrate_options);
            $heartrate_response = curl_exec($heartrate_ch);
            curl_close($heartrate_ch);
            $heartrate_data = json_decode($heartrate_response, true);
            $heartrate = $heartrate_data['activities-heart'][0]['value']['restingHeartRate'] ?? 'No new data';

            if ($heartrate === 'No new data') {
                $heartrate = $_SESSION['last_heartrate'] ?? '0';
            } else {
                $_SESSION['last_heartrate'] = $heartrate;
            }

            // Fetch Sleep Data
            $sleep_url = 'https://api.fitbit.com/1.2/user/-/sleep/date/today.json';
            $sleep_options = [
                CURLOPT_URL => $sleep_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer $access_token",
                ],
            ];
            $sleep_ch = curl_init();
            curl_setopt_array($sleep_ch, $sleep_options);
            $sleep_response = curl_exec($sleep_ch);
            curl_close($sleep_ch);
            $sleep_data = json_decode($sleep_response, true);
            $sleep = $sleep_data['summary']['totalMinutesAsleep'] ?? 'No new data';

            if ($sleep === 'No new data') {
                $sleep = $_SESSION['last_sleep'] ?? '0';
            } else {
                $_SESSION['last_sleep'] = $sleep;
            }

            // Display Data in Cards
            ?>
            <div class="card">
                <h3>Steps</h3>
                <p><?php echo $steps; ?></p>
                <div class="chart-container">
                    <canvas id="stepsChart"></canvas>
                </div>
            </div>
            <div class="card">
                <h3>Heart Rate</h3>
                <p><?php echo $heartrate; ?> bpm</p>
                <div class="chart-container">
                    <canvas id="heartRateChart"></canvas>
                </div>
            </div>
            <div class="card">
                <h3>Sleep</h3>
                <p><?php echo round($sleep / 60, 1); ?> hours</p>
                <div class="chart-container">
                    <canvas id="sleepChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        const stepsChartCtx = document.getElementById('stepsChart').getContext('2d');
        const heartRateChartCtx = document.getElementById('heartRateChart').getContext('2d');
        const sleepChartCtx = document.getElementById('sleepChart').getContext('2d');

        new Chart(stepsChartCtx, {
            type: 'bar',
            data: {
                labels: ['Today'],
                datasets: [{
                    label: 'Steps',
                    data: [<?php echo $steps; ?>],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
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

        new Chart(heartRateChartCtx, {
            type: 'line',
            data: {
                labels: ['Today'],
                datasets: [{
                    label: 'Heart Rate',
                    data: [<?php echo $heartrate; ?>],
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
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

        new Chart(sleepChartCtx, {
            type: 'pie',
            data: {
                labels: ['Sleep'],
                datasets: [{
                    label: 'Sleep (hours)',
                    data: [<?php echo round($sleep / 60, 1); ?>],
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.2)'
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true
            }
        });
    </script>
</body>
</html>
