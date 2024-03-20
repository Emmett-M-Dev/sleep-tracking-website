<?php

function getLatestSleepData() {
    global $conn;

    
       
    

    // Query to get the last sleep entry for the user
    $query = "SELECT sleep_time, wake_time, sleep_quality FROM sleep_tracker WHERE user_id = ? ORDER BY date_of_sleep DESC LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $sleepData = $result->fetch_assoc();
        $stmt->close();
        return $sleepData;
    } else {
        $stmt->close();
        
        return null; // Handle the case when there's no sleep data
    }
}


function calculateSleepStages($sleepTime, $wakeTime, $sleepQuality) {
    // Calculate total sleep duration in minutes
    $startTime = new DateTime($sleepTime);
    $endTime = new DateTime($wakeTime);
    if ($endTime < $startTime) {
        $endTime->modify('+1 day');
    }
    $sleepDuration = ($startTime->diff($endTime)->h * 60) + ($startTime->diff($endTime)->i);

    // Adjustments based on sleep quality input
    $qualityAdjustments = [
        'very bad' => ['N1' => 0.3, 'N2' => 0.4, 'N3' => 0.15, 'REM' => 0.15],
        'bad' => ['N1' => 0.25, 'N2' => 0.45, 'N3' => 0.2, 'REM' => 0.1],
        'fair' => ['N1' => 0.15, 'N2' => 0.5, 'N3' => 0.25, 'REM' => 0.1],
        'good' => ['N1' => 0.1, 'N2' => 0.4, 'N3' => 0.3, 'REM' => 0.2],
        'excellent' => ['N1' => 0.05, 'N2' => 0.35, 'N3' => 0.35, 'REM' => 0.25]
    ];
    
    // Basic cycle distribution assuming 4 to 6 cycles of 90 to 120 minutes each
    $cycles = floor($sleepDuration / 90); // Using 90min as a base cycle duration for simplification
    $adjustments = $qualityAdjustments[strtolower($sleepQuality)];
    
    $sleepCycles = [];
    $remainingDuration = $sleepDuration;

    for ($cycle = 1; $cycle <= $cycles; $cycle++) {
        $cycleDuration = min($remainingDuration, 90); // Each cycle takes up to 90 minutes of the remaining duration
        $remainingDuration -= $cycleDuration;

        // Adjusting stage distribution within a cycle based on sleep quality
        $stages = [
            'N1' => round($cycleDuration * $adjustments['N1']),
            'N2' => round($cycleDuration * $adjustments['N2']),
            'N3' => round($cycleDuration * $adjustments['N3']),
            'REM' => round($cycleDuration * $adjustments['REM'])
        ];

        $sleepCycles[] = $stages;

        if ($remainingDuration <= 0) {
            break;
        }
    }

    return $sleepCycles;
}



?>
 