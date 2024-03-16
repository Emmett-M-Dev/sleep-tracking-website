<?php
// Function to calculate the sleep score based on sleep duration and quality
function calculateSleepScore($durationInHours, $sleepQuality) {
    // Mapping of sleep quality to a multiplier
    $qualityScores = [
        'really bad' => 0.5, 
        'bad' => 0.75, 
        'fair' => 1, 
        'good' => 1.25, 
        'excellent' => 1.5
    ];

    // Base score for 8 hours of sleep
    $baseScore = 100;

    // Calculate the duration score as a percentage of the base score
    $durationScore = ($durationInHours / 8) * $baseScore;

    // Look up the quality multiplier from the mapping, defaulting to 1 if not found
    $qualityMultiplier = $qualityScores[strtolower($sleepQuality)] ?? 1;

    // Calculate the final score as the duration score adjusted by the quality multiplier
    // Ensure the final score does not exceed the base score
    return min($baseScore, $durationScore * $qualityMultiplier);
}

// Function to estimate sleep stages based on sleep duration
function estimateSleepStages($sleepStart, $sleepEnd) {
    // Convert sleep start and end times to DateTime objects
    $sleepStartTime = new DateTime($sleepStart);
    $sleepEndTime = new DateTime($sleepEnd);

    // Initialize an array to hold the sleep cycles
    $cycles = [];

    // Define standard durations for Non-REM and REM stages in minutes
    $standardCycle = ['Non-REM' => 65, 'REM' => 25];

    // Initialize the current time tracker to the sleep start time
    $currentTime = clone $sleepStartTime;

    // Loop until the current time exceeds the sleep end time
    while ($currentTime < $sleepEndTime) {
        // Loop through each stage in the standard cycle
        foreach ($standardCycle as $stage => $duration) {
            // Calculate the end time for the current stage
            $endTime = clone $currentTime;
            $endTime->add(new DateInterval('PT' . $duration . 'M'));

            // If the end time exceeds the sleep end time, adjust it to match
            if ($endTime > $sleepEndTime) {
                $endTime = clone $sleepEndTime;
            }

            // Add the current stage to the cycles array with start and end times and the duration
            $cycles[] = [
                'stage' => $stage,
                'start' => $currentTime->format('H:i'),
                'end' => $endTime->format('H:i'),
                'duration' => $currentTime->diff($endTime)->format('%H:%I')
            ];

            // Move the current time forward to the end of the current stage
            $currentTime = clone $endTime;

            // If the current time has reached or exceeded the sleep end time, break out of the loop
            if ($currentTime >= $sleepEndTime) {
                break;
            }
        }
    }

    // Return the array of sleep cycles
    return $cycles;
}
?>
