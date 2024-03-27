<?php


$userId = $_SESSION['user_id'] ?? exit('User is not logged in.'); // Ensure user is logged in.

// Query to fetch sleep data for the specific user, ordered by date
$query = "SELECT date_of_sleep, sleep_time FROM sleep_tracker WHERE user_id = ? ORDER BY date_of_sleep ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$sleepData = [];
while ($row = $result->fetch_assoc()) {
    $timeParts = explode(':', $row['sleep_time']);
    $timeAsDecimal = $timeParts[0] + ($timeParts[1] / 60);
    
    $sleepData[] = [
        'date' => $row['date_of_sleep'],
        'sleepTime' => $timeAsDecimal
    ];
}

// Calculate the average sleep time
$averageSleepTime = array_sum(array_column($sleepData, 'sleepTime')) / count($sleepData);
$averageSleepTimeLine = round($averageSleepTime, 2);

// Prepare data for JavaScript
$sleepDataJson = json_encode($sleepData);
echo "<script>
var sleepData = $sleepDataJson;
var averageSleepTimeLine = $averageSleepTimeLine;
</script>";

// Close the statement and connection
$stmt->close();
$conn->close();

// Adjust all sleep times for the calculation of the average and standard deviation
$adjustedSleepTimes = array_map(function($entry) {
    $timeAsDecimal = $entry['sleepTime'];
    if ($timeAsDecimal < 13) {
        $timeAsDecimal += 24;
    }
    return $timeAsDecimal;
}, $sleepData);

// Calculate the average sleep time using adjusted times
$meanSleepTime = array_sum($adjustedSleepTimes) / count($adjustedSleepTimes);

// Calculate the standard deviation
$sumOfSquares = array_reduce($adjustedSleepTimes, function($carry, $time) use ($meanSleepTime) {
    return $carry + pow($time - $meanSleepTime, 2);
}, 0);
$stdDeviation = sqrt($sumOfSquares / count($adjustedSleepTimes));

// Define the maximum standard deviation that corresponds to a score of 0
// Assuming 1 hour of deviation is the maximum allowed for a zero score
$maxStdDevForZeroScore = 2.5; // 1 hour, adjust as needed

// Calculate the consistency score as an inverse of the standard deviation, scaled to a maximum of 100
$consistencyScore = max(0, 100 - ($stdDeviation / $maxStdDevForZeroScore * 100));

// Format the score to two decimal places
$consistencyScore = number_format($consistencyScore, 2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Sleep Consistency</title>
    <!-- Chart.js v4.4.2 -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2"></script>
    <!-- Tailwind CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Set maximum width for the chart container */
        .chart-container {
            max-width: 1000px; /* Adjust as needed */
            max-height: 700px;
            margin: auto;
        }
        /* Responsive canvas size */
        canvas {
            width: 100% !important;
            max-width: 1000px; /* Adjust as needed */
            max-height: 1000px !important;
        }
    </style>
</head>
<body class="bg-blue-900 text-white font-sans">
    <!-- Your navigation bar here -->

    <!-- Container for the Sleep Consistency Graph -->
    <div class="chart-container my-12 p-8">
        <h1 class="text-3xl mb-4">Sleep Consistency Graph</h1>
        <canvas id="sleepConsistencyChart"></canvas>
    </div>

    <div class="container mx-auto my-12 p-8">
        <h2 class="text-3xl mb-4">Sleep Consistency Score</h2>
        <div class="score-container">
            <div class="score-bar-container bg-gray-200 w-full rounded-full h-6">
                <div id="score-bar" class="bg-blue-600 h-6 rounded-full" style="width: 0%;"></div>
            </div>
            <div class="score-value text-2xl my-2" id="score-value">Score: 0</div>
        </div>
    </div>

    <script>
        // Function to initialize the Sleep Consistency Graph
        function initializeSleepConsistencyGraph() {
    const ctx = document.getElementById('sleepConsistencyChart').getContext('2d');
    const labels = sleepData.map(entry => entry.date);
    
    // Adjust all sleep times as we do for individual sleep times
    const adjustedSleepTimes = sleepData.map(entry => {
        let timeAsDecimal = entry.sleepTime;
        // Adjust times past midnight to the next day for averaging
        if (timeAsDecimal < 13) {
            timeAsDecimal += 24;
        }
        return timeAsDecimal;
    });
    
    // Calculate the average of the adjusted times
    const adjustedAverageSleepTime = adjustedSleepTimes.reduce((a, b) => a + b, 0) / adjustedSleepTimes.length;
    
    // Adjust the average for plotting on the graph
    const plotAdjustedAverage = adjustedAverageSleepTime < 18 ? adjustedAverageSleepTime + 6 : adjustedAverageSleepTime - 18;

    const sleepTimes = adjustedSleepTimes.map(time => time < 18 ? time + 6 : time - 18);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Sleep Time',
                data: sleepTimes,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderWidth: 2,
                pointRadius: 3,
                tension: 0.1
            }, {
                label: 'Average Sleep Time',
                // Create an array with the same length as the labels array filled with the adjusted average sleep time
                data: labels.map(() => plotAdjustedAverage),
                borderColor: 'rgba(153, 102, 255, 1)',
                borderWidth: 2,
                borderDash: [5, 5]
            }]
        },
        options: {
            scales: {
                y: {
                    min: 0,  // 6 PM previous day
                    max: 11, // 5AM current day
                    title: {
                        display: true,
                        text: 'Time (18:00 previous day to 13:00 current day)'
                    },
                    ticks: {
                        stepSize: 1,
                        callback: function(value) {
                            let hour = (value + 18) % 24;
                            let suffix = hour >= 12 ? 'PM' : 'AM';
                            hour = hour % 12;
                            hour = hour ? hour : 12; // Convert 0 to 12 for 12 AM
                            return `${hour} ${suffix}`;
                        }
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Date'
                    }
                }
            },
            maintainAspectRatio: false,
            responsive: true,
            plugins: {
                legend: {
                    display: true
                },
                tooltip: {
                    // Tooltip configuration here, as previously given
                }
            }
        }
    });
}
        // Initialize the graph on page load
        window.onload = initializeSleepConsistencyGraph;

    // Pass the PHP consistency score to JavaScript
    var consistencyScore = <?php echo $consistencyScore; ?>;

// Function to update the score display
function displayConsistencyScore() {
    const scoreBar = document.getElementById('score-bar');
    const scoreValue = document.getElementById('score-value');

    scoreBar.style.width = consistencyScore + '%'; // Update the width of the progress bar
    scoreValue.textContent = 'Score: ' + consistencyScore; // Update the text display
}

// Call this function on page load to update the score display
window.addEventListener('load', displayConsistencyScore);

    </script>
</body>
</html>