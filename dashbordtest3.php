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
            background-color: #f3f4f6;
        }

        .sidebar {
            width: 250px;
            background-color: #00b0b9;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sidebar h1 {
            text-align: center;
            padding: 20px;
            font-size: 1.8em;
            background-color: #007e8c;
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
            background-color: #008b96;
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

        .profile-details {
            display: none;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
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
                <button onclick="toggleProfileDetails()">View Profile</button>
                <button>Sync Data</button>
            </div>
        </div>

        <div class="dashboard">
            <?php
            session_start();
            $access_token = 'eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiIyM1BUMlEiLCJzdWIiOiJCMjdQUFEiLCJpc3MiOiJGaXRiaXQiLCJ0eXAiOiJhY2Nlc3NfdG9rZW4iLCJzY29wZXMiOiJyc29jIHJlY2cgcnNldCByaXJuIHJveHkgcnBybyBybnV0IHJzbGUgcmNmIHJhY3QgcnJlcyBybG9jIHJ3ZWkgcmhyIHJ0ZW0iLCJleHAiOjE3Mzc0MzQwOTMsImlhdCI6MTczNzQwNTI5M30.GDmCxeD_Pl18Wg_Nrz9tt07ieLcazNxjpmnxskB2eUk';

            // Fetch User Profile Data
            $profile_url = 'https://api.fitbit.com/1/user/-/profile.json';
            $profile_options = [
                CURLOPT_URL => $profile_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer $access_token",
                ],
            ];
            $profile_ch = curl_init();
            curl_setopt_array($profile_ch, $profile_options);
            $profile_response = curl_exec($profile_ch);
            curl_close($profile_ch);
            $profile_data = json_decode($profile_response, true);
            $user_name = $profile_data['user']['fullName'] ?? 'Unknown';
            $user_height = $profile_data['user']['height'] ?? 'Unknown';
            $user_weight = $profile_data['user']['weight'] ?? 'Unknown';
            $user_avatar = $profile_data['user']['avatar'] ?? '';

            // Fetch Steps Data
            $steps_url = 'https://api.fitbit.com/1/user/-/activities/steps/date/today/1d.json';
            $steps_data = fetchData($steps_url, 'last_steps');

            // Fetch Heart Rate Data
            $heartrate_url = 'https://api.fitbit.com/1/user/-/activities/heart/date/today/1d.json';
            $heartrate_data = fetchData($heartrate_url, 'last_heartrate');

            // Fetch Sleep Data
            $sleep_url = 'https://api.fitbit.com/1.2/user/-/sleep/date/today.json';
            $sleep_data = fetchData($sleep_url, 'last_sleep');

            function fetchData($url, $session_key) {
                global $access_token;
                $options = [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => [
                        "Authorization: Bearer $access_token",
                    ],
                ];
                $ch = curl_init();
                curl_setopt_array($ch, $options);
                $response = curl_exec($ch);
                curl_close($ch);
                $data = json_decode($response, true);

                if (empty($data)) {
                    return $_SESSION[$session_key] ?? 'No data';
                } else {
                    $_SESSION[$session_key] = $data;
                    return $data;
                }
            }
            ?>

            <div class="card">
                <h3>Steps</h3>
                <p><?php echo $steps_data['activities-steps'][0]['value'] ?? 'No data'; ?></p>
                <div class="chart-container">
                    <canvas id="stepsChart"></canvas>
                </div>
            </div>
            <div class="card">
                <h3>Heart Rate</h3>
                <p><?php echo $heartrate_data['activities-heart'][0]['value']['restingHeartRate'] ?? 'No data'; ?> bpm</p>
                <div class="chart-container">
                    <canvas id="heartRateChart"></canvas>
                </div>
            </div>
            <div class="card">
                <h3>Sleep</h3>
                <p><?php echo round($sleep_data['summary']['totalMinutesAsleep'] / 60, 1) ?? 'No data'; ?> hours</p>
                <div class="chart-container">
                    <canvas id="sleepChart"></canvas>
                </div>
            </div>
        </div>

        <div class="profile-details" id="profileDetails">
            <h3>Profile Details</h3>
            <p><strong>Name:</strong> <?php echo $user_name; ?></p>
            <p><strong>Height:</strong> <?php echo $user_height; ?> cm</p>
            <p><strong>Weight:</strong> <?php echo $user_weight; ?> kg</p>
            <img src="<?php echo $user_avatar; ?>" alt="User Avatar" style="width: 100px; height: 100px; border-radius: 50%;">
        </div>
    </div>

    <script>
        function toggleProfileDetails() {
            const profileDetails = document.getElementById('profileDetails');
            profileDetails.style.display = profileDetails.style.display === 'block' ? 'none' : 'block';
        }

        // Initialize Charts
        const stepsChartCtx = document.getElementById('stepsChart').getContext('2d');
        const heartRateChartCtx = document.getElementById('heartRateChart').getContext('2d');
        const sleepChartCtx = document.getElementById('sleepChart').getContext('2d');

        new Chart(stepsChartCtx, {
            type: 'bar',
            data: {
                labels: ['Today'],
                datasets: [{
                    label: 'Steps',
                    data: [<?php echo $steps_data['activities-steps
