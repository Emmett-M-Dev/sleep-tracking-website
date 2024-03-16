<?php

//  include 'includes/sessionconnection.php';
// Function to calculate sleep score - Placeholder for your logic
function calculateSleepScore($sleepQuality) {
    // Replace with your actual calculation logic
    $qualityScores = ['Excellent' => 100, 'Good' => 80, 'Fair' => 60, 'Bad' => 40, 'Very Bad' => 20];
    return $qualityScores[$sleepQuality] ?? 0;
}

// Function to calculate sleep streak
function calculateSleepStreak($userId, $conn) {
    $streak = 0;
    $today = new DateTime('today');
    
    // Prepare statement to get all sleep data for user ordered by date
    $stmt = $conn->prepare("SELECT date_of_sleep FROM sleep_tracker WHERE user_id = ? ORDER BY date_of_sleep DESC");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $sleepDate = new DateTime($row['date_of_sleep']);
            $diff = $today->diff($sleepDate)->days;

            // If there's no gap in dates, increase streak
            if ($diff == $streak) {
                $streak++;
                // Update 'today' to check for the previous day in the next iteration
                $today->modify('-1 day');
            } else {
                // If there's a gap, streak ends
                break;
            }
        }
    }

    $stmt->close();
    return $streak;
}

// Assuming you have the user's ID from the session
$userId = $_SESSION['user_id'] ?? 1; // Fallback to user ID 1

// Fetch the latest sleep data entry
$stmt = $conn->prepare("SELECT sleep_quality, sleep_time, wake_time, date_of_sleep FROM sleep_tracker WHERE user_id = ? ORDER BY date_of_sleep DESC LIMIT 1");
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
$sleepData = $result->fetch_assoc();

// Initialize variables
$sleepScore = 0;
$hoursSlept = '0';
$wakeTime = '00:00';
$sleepStreak = 0;

if ($sleepData) {
    // Calculate sleep score
    $sleepScore = calculateSleepScore($sleepData['sleep_quality']);

    // Parse times
    $sleepDateTime = new DateTime($sleepData['sleep_time']);
    $wakeDateTime = new DateTime($sleepData['wake_time']);

    // Calculate hours slept (assuming 'sleep_time' and 'wake_time' are in 'HH:MM:SS' format)
    $interval = $sleepDateTime->diff($wakeDateTime);
    $hoursSlept = $interval->format('%h');
    $minutesSlept = $interval->format('%i');
    $hoursSleptText = $hoursSlept . 'h ' . $minutesSlept . 'm';

    // Format wake time
    $wakeTime = $wakeDateTime->format('g:i A');
}

// Calculate sleep streak
$sleepStreak = calculateSleepStreak($userId, $conn);

$conn->close();


// Now construct the data array to be returned as JSON
$outputData = [
    'sleepScore' => $sleepScore, // Assuming $sleepScore is already calculated
    'hoursSleptText' => $hoursSleptText, // The formatted string, e.g., "7h 30m"
    'wakeTime' => $wakeTime, // The formatted wake time, e.g., "6:00 AM"
    'sleepStreak' => $sleepStreak, // The calculated sleep streak
    // Include any other data you want to send back for the progress bar
];

// Set the header to inform the client that the content type of the response is JSON
header('Content-Type: application/json');


// Include the estimated sleep stages in the output data
$estimatedStages = estimateSleepStages($sleepData['sleep_time'], $sleepData['wake_time']);
$outputData['estimatedStages'] = $estimatedStages;


// Output the JSON representation of the outputData array
echo json_encode($outputData);
?>