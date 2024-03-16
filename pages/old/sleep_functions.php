<?php 

function estimateSleepStages($sleepStart, $sleepEnd) {
    $sleepStages = [];

    // Convert sleep start and end times to DateTime objects
    $sleepStartTime = new DateTime($sleepStart);
    $sleepEndTime = new DateTime($sleepEnd);

    // Calculate the total duration of sleep in minutes
    $totalDuration = ($sleepEndTime->getTimestamp() - $sleepStartTime->getTimestamp()) / 60;

    // Standard durations for each sleep cycle stage in minutes
    $deepSleepDuration = 75;  // Non-REM
    $lightSleepDuration = 60; // Light sleep
    $remSleepDuration = 20;   // REM sleep

    // While there is still time left in the total duration, estimate the stages
    while ($totalDuration > 0) {
        // Estimate Deep Sleep
        $deepSleepEnd = clone $sleepStartTime;
        $deepSleepEnd->modify("+{$deepSleepDuration} minutes");
        array_push($sleepStages, [
            'stage' => 'Deep',
            'start' => $sleepStartTime->format('H:i'),
            'end' => $deepSleepEnd->format('H:i')
        ]);
        $sleepStartTime = clone $deepSleepEnd;
        $totalDuration -= $deepSleepDuration;

        // Estimate Light Sleep
        $lightSleepEnd = clone $sleepStartTime;
        $lightSleepEnd->modify("+{$lightSleepDuration} minutes");
        array_push($sleepStages, [
            'stage' => 'Light',
            'start' => $sleepStartTime->format('H:i'),
            'end' => $lightSleepEnd->format('H:i')
        ]);
        $sleepStartTime = clone $lightSleepEnd;
        $totalDuration -= $lightSleepDuration;

        // Estimate REM Sleep
        if ($totalDuration >= $remSleepDuration) {
            $remSleepEnd = clone $sleepStartTime;
            $remSleepEnd->modify("+{$remSleepDuration} minutes");
            array_push($sleepStages, [
                'stage' => 'REM',
                'start' => $sleepStartTime->format('H:i'),
                'end' => $remSleepEnd->format('H:i')
            ]);
            $sleepStartTime = clone $remSleepEnd;
            $totalDuration -= $remSleepDuration;
        }

        // If there's not enough time for a full cycle, allocate remaining time to light sleep
        if ($totalDuration > 0 && $totalDuration < ($deepSleepDuration + $lightSleepDuration + $remSleepDuration)) {
            $lightSleepEnd = clone $sleepStartTime;
            $lightSleepEnd->modify("+{$totalDuration} minutes");
            array_push($sleepStages, [
                'stage' => 'Light',
                'start' => $sleepStartTime->format('H:i'),
                'end' => $lightSleepEnd->format('H:i')
            ]);
            break;
        }
    }

    return $sleepStages;
}


?>

