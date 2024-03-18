<?php

function getLatestSleepData() {
    global $conn;

    
       
    

    // Query to get the last sleep entry for the user
    $query = "SELECT sleep_time, wake_time FROM sleep_tracker WHERE user_id = ? ORDER BY date_of_sleep DESC LIMIT 1";
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


function calculateSleepStages($sleepTime, $wakeTime) {
    // Calculate total sleep duration in minutes
    $startTime = new DateTime($sleepTime);
    $endTime = new DateTime($wakeTime);
    if ($endTime < $startTime) {
        // Handles crossing midnight
        $endTime->modify('+1 day');
    }
    $sleepDuration = ($startTime->diff($endTime)->h * 60) + ($startTime->diff($endTime)->i);

    // Basic cycle distribution assuming 4 to 6 cycles of 90 to 120 minutes each
    $cycles = floor($sleepDuration / 90); // Using 90min as a base cycle duration for simplification

    $sleepCycles = [];
    $remainingDuration = $sleepDuration;

    for ($cycle = 1; $cycle <= $cycles; $cycle++) {
        $cycleDuration = min($remainingDuration, 90); // Each cycle takes up to 90 minutes of the remaining duration
        $remainingDuration -= $cycleDuration;

        // Assuming simplified stage distribution within a cycle
        $stages = [
            'N1' => min(10, $cycleDuration * 0.1), // 10% of cycle, up to 10 minutes
            'N2' => min(50, $cycleDuration * 0.55), // 55% of cycle, up to 50 minutes
            'N3' => min(20, $cycleDuration * 0.25), // 25% of cycle, up to 20 minutes
            'REM' => max(0, $cycleDuration - 10 - 50 - 20) // Remaining time, after other stages
        ];

        $sleepCycles[] = $stages;

        if ($remainingDuration <= 0) {
            break;
        }
    }

    return $sleepCycles;
}



?>
 