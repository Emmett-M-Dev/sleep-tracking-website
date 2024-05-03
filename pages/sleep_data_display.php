<?php

// this calcualtes the sleep score display in tracker.php
function calculateSleepScore() {
    global $conn;

    // Initialize sleep score
    $sleepScore = 0;

    // Retrieve the last sleep entry for the logged-in user
    $query = "SELECT * FROM sleep_tracker WHERE user_id = ? ORDER BY date_of_sleep DESC LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Map sleep quality to score ranges
        $qualityScoreMap = [
            "Excellent" => [90, 100],
            "Good" => [80, 89],
            "Fair" => [60, 79],
            "Bad" => [40, 59],
            "Very Bad" => [0, 39]
        ];
        $durationMultipliers = [
            'optimal' => 1.1,
            'normal' => 1.0,
            'below_optimal' => 0.9,
            'low' => 0.8,
            'very_low' => 0.7,
            'extremely_low' => 0.6,
        ];

        // Parse the sleep duration string into hours and minutes
        list($hours, $minutes, $seconds) = explode(':', $row['sleep_duration']);
        $hoursSlept = $hours + ($minutes / 60);

        // Get base score and multiplier based on sleep quality and duration
        $sleepQuality = $row['sleep_quality'];
        $baseScoreRange = $qualityScoreMap[$sleepQuality];
        $baseScore = ($baseScoreRange[0] + $baseScoreRange[1]) / 2;

        // Determine the duration multiplier
        $durationMultiplier = 'normal';
        if ($hoursSlept >= 7 && $hoursSlept <= 9) {
            $durationMultiplier = 'optimal';
        } elseif ($hoursSlept < 7 && $hoursSlept >= 6) {
            $durationMultiplier = 'below_optimal';
        } elseif ($hoursSlept < 6 && $hoursSlept >= 5) {
            $durationMultiplier = 'low';
        } elseif ($hoursSlept < 5 && $hoursSlept >= 4) {
            $durationMultiplier = 'very_low';
        } elseif ($hoursSlept < 4) {
            $durationMultiplier = 'extremely_low';
        }

        // Apply the multiplier
        $sleepScore = $baseScore * $durationMultipliers[$durationMultiplier];

        // Apply the minimum score guarantee and cap the score at the maximum
        $sleepScore = max($baseScoreRange[0], min($sleepScore, $baseScoreRange[1]));

        // Add optimal sleep bonus if applicable
        if (($sleepQuality === 'Excellent' || $sleepQuality === 'Good') && $durationMultiplier === 'optimal') {
            $sleepScore += 1; // or some other bonus value/percentage
            $sleepScore = min($sleepScore, $baseScoreRange[1]); // Ensure it doesn't exceed the maximum
        }

        // Round up the score to the nearest integer
        $sleepScore = ceil($sleepScore);


    } else {
        // Handle the case when there's no sleep data
        $sleepScore = 0;
    }

    $stmt->close();

    return $sleepScore;
}
// 
function getLastSleepDuration() {
    global $conn;

    // Initialize sleep duration variable
    $sleepDuration = '';

    // Prepare the query to get the last sleep entry
    $query = "SELECT sleep_duration FROM sleep_tracker WHERE user_id = ? ORDER BY date_of_sleep DESC LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Get the sleep duration
        $sleepDuration = $row['sleep_duration'];
    } else {
        // Handle the case when there's no sleep data
        $sleepDuration = " --:--";
    }

    $stmt->close();

    return $sleepDuration;
}

function getLatestWakeTime() {
    global $conn;

    // Prepare the query to get the latest wake time entry
    $query = "SELECT wake_time FROM sleep_tracker WHERE user_id = ? ORDER BY date_of_sleep DESC LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Get the wake time
        $wakeTime = $row['wake_time'];
    } else {
        // Handle the case when there's no sleep data
        $wakeTime = "--:--";
    }

    $stmt->close();

    return $wakeTime;
}

function calculateSleepStreak() {
    global $conn;

    // Initialize streak count
    $streakCount = 0;
    
    // Prepare the query to get all sleep data entries for the user, ordered by date
    $query = "SELECT date_of_sleep FROM sleep_tracker WHERE user_id = ? ORDER BY date_of_sleep DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if we have at least one entry
    if ($result->num_rows > 0) {
        // Fetch all results
        $dates = $result->fetch_all(MYSQLI_ASSOC);

        // Set the previous date to the date of the first record
        $previousDate = new DateTime($dates[0]['date_of_sleep']);

        foreach ($dates as $index => $row) {
            $currentDate = new DateTime($row['date_of_sleep']);

            // Check the date difference
            $dateDiff = $currentDate->diff($previousDate)->days;

            // If the date difference is 1, it's a consecutive day
            if ($index != 0 && $dateDiff == 1) {
                $streakCount++;
            } elseif ($index != 0 && $dateDiff > 1) {
                // If the gap is larger than one day, break the loop as streak has ended
                break;
            }

            // Update previousDate to currentDate for the next iteration
            $previousDate = $currentDate;
        }
        
        // If we have data, the streak starts at least from 1
        $streakCount = $streakCount > 0 ? $streakCount + 1 : 1;
    }

    $stmt->close();

    return $streakCount;
}

function getSleepQualityAverage() {
    global $conn;

    // Initialize the variable to hold the average sleep quality
    $averageSleepQuality = 0;

    // Prepare the query to calculate the average sleep quality for the logged-in user
    $query = "SELECT AVG(CASE 
                WHEN sleep_quality = 'Excellent' THEN 4 
                WHEN sleep_quality = 'Good' THEN 3 
                WHEN sleep_quality = 'Fair' THEN 2 
                WHEN sleep_quality = 'Poor' THEN 1 
                ELSE 0 END) AS avg_quality 
              FROM sleep_tracker 
              WHERE user_id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $averageSleepQuality = round($row['avg_quality'], 2);
    } else {
        // Handle the case when there's no sleep data
        $averageSleepQuality = "No sleep data available";
    }

    $stmt->close();

    // Format the average quality score to a qualitative value
    $qualityRating = '';
    switch ($averageSleepQuality) {
        case ($averageSleepQuality >= 3.5):
            $qualityRating = 'Excellent';
            break;
        case ($averageSleepQuality >= 2.5 && $averageSleepQuality < 3.5):
            $qualityRating = 'Good';
            break;
        case ($averageSleepQuality >= 1.5 && $averageSleepQuality < 2.5):
            $qualityRating = 'Fair';
            break;
        case ($averageSleepQuality < 1.5):
            $qualityRating = 'Poor';
            break;
        default:
            $qualityRating = 'No data';
            break;
    }

    return $qualityRating;
}
function getSleepTimeAverage() {
    global $conn;

    // Initialize variables
    $totalSleepMinutes = 0;
    $sleepCount = 0;

    // Prepare the query to fetch all sleep durations for the logged-in user
    $query = "SELECT sleep_duration FROM sleep_tracker WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if we have entries
    if ($result->num_rows > 0) {
        // Calculate the total sleep duration in minutes
        while ($row = $result->fetch_assoc()) {
            // Parse the sleep duration string into hours, minutes, and seconds
            list($hours, $minutes, $seconds) = explode(':', $row['sleep_duration']);
            $totalSleepMinutes += $hours * 60 + $minutes + $seconds / 60;
            $sleepCount++;
        }

        // Calculate the average duration in minutes
        $averageSleepMinutes = $sleepCount > 0 ? $totalSleepMinutes / $sleepCount : 0;

        // Convert the average duration back to time format
        $hours = floor($averageSleepMinutes / 60);
        $minutes = floor($averageSleepMinutes % 60);

        // Format the average duration as a string
        $averageSleepDuration = sprintf("%02dhrs %02dmins", $hours, $minutes);

    } else {
        // Handle the case when there's no sleep data
        $averageSleepDuration = "No sleep data available";
    }

    $stmt->close();

    return $averageSleepDuration;
}

function getAverageBedtime() {
    global $conn;

    $userId = $_SESSION['user_id'] ?? die("User is not logged in.");
    $query = "SELECT sleep_time FROM sleep_tracker WHERE user_id = ? ORDER BY date_of_sleep ASC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $adjustedSleepTimes = [];

    while ($row = $result->fetch_assoc()) {
        $timeParts = explode(':', $row['sleep_time']);
        $hours = (int)$timeParts[0];
        $minutes = (int)$timeParts[1];
        // Adjust times past midnight to the next day for averaging
        $timeAsDecimal = $hours + ($minutes / 60);
        if ($timeAsDecimal < 13) { // If time represents early morning, adjust it by adding 24 hours
            $timeAsDecimal += 24;
        }
        $adjustedSleepTimes[] = $timeAsDecimal;
    }

    if (empty($adjustedSleepTimes)) return "No sleep data available";

    // Calculate the average sleep time using adjusted times
    $meanSleepTime = array_sum($adjustedSleepTimes) / count($adjustedSleepTimes);

    // Adjust the average back if it's beyond 24 hours for display purposes
    if ($meanSleepTime > 24) {
        $meanSleepTime -= 24;
    }

    // Convert decimal time back to hours and minutes
    $avgHours = floor($meanSleepTime);
    $avgMinutes = round(($meanSleepTime - $avgHours) * 60);

    // Format the average bedtime as HH:MM
    $formattedAvgBedtime = sprintf("%02d:%02d", $avgHours, $avgMinutes);

    $stmt->close();

    return $formattedAvgBedtime;
}
?>


