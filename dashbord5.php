<?php
session_start();

// Include your Fitbit API credentials
$fitbitAccessToken = "eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiIyM1BUMlEiLCJzdWIiOiJCMjdQUFEiLCJpc3MiOiJGaXRiaXQiLCJ0eXAiOiJhY2Nlc3NfdG9rZW4iLCJzY29wZXMiOiJyc29jIHJlY2cgcnNldCByaXJuIHJveHkgcm51dCBycHJvIHJzbGUgcmNmIHJhY3QgcnJlcyBybG9jIHJ3ZWkgcmhyIHJ0ZW0iLCJleHAiOjE3Mzc1NjM1MjIsImlhdCI6MTczNzUzNDcyMn0.88vaFb0W-MSP1KPGIorWi-JgD-Z1jZ4lP9eYeQjJPZcYOUR_ACCESS_TOKEN";
$fitbitApiUrl = "https://api.fitbit.com/1/user/-/";

// Function to fetch data from Fitbit API
function fetchFitbitData($endpoint, $accessToken) {
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $endpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer $accessToken"
        ]
    ]);

    $response = curl_exec($curl);
    curl_close($curl);

    return json_decode($response, true);
}

// Fetch user profile data
$profileData = fetchFitbitData($fitbitApiUrl . "profile.json", $fitbitAccessToken);

// Fetch weekly activity data
$activityData = fetchFitbitData($fitbitApiUrl . "activities/heart/date/today/1w.json", $fitbitAccessToken);

// Fetch other data (e.g., sleep, temperature)
$sleepData = fetchFitbitData($fitbitApiUrl . "sleep/date/today.json", $fitbitAccessToken);
$temperatureData = fetchFitbitData($fitbitApiUrl . "body/temperature/date/today/1w.json", $fitbitAccessToken);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitbit Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="logo">My Dashboard</div>
        <div class="week-navigation">
            <button onclick="navigateWeek(-1)">Previous Week</button>
            <button onclick="navigateWeek(1)">Next Week</button>
        </div>
        <div class="profile">
            <img src="<?= $profileData['user']['avatar'] ?>" alt="Profile Picture" onclick="showProfile()">
        </div>
    </header>

    <main>
        <div class="stats">
            <div class="stat">
                <img src="activity.png" alt="Activity">
                <p><?= $activityData['activities-heart'][0]['value']['restingHeartRate'] ?> bpm</p>
            </div>
            <div class="stat">
                <img src="sleep.png" alt="Sleep">
                <p><?= $sleepData['summary']['totalMinutesAsleep'] ?> mins</p>
            </div>
            <div class="stat">
                <img src="heart.png" alt="Heart Rate">
                <p><?= $activityData['activities-heart'][0]['value']['heartRateZones'][1]['minutes'] ?> mins</p>
            </div>
            <div class="stat">
                <img src="temperature.png" alt="Temperature">
                <p><?= $temperatureData['temperature'][0]['value'] ?> °C</p>
            </div>
        </div>

        <div class="graph">
            <canvas id="chart"></canvas>
            <div class="graph-navigation">
                <button onclick="showPreviousGraph()">Previous</button>
                <button onclick="showNextGraph()">Next</button>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 My Fitbit Dashboard. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="scripts.js"></script>
</body>
</html>
