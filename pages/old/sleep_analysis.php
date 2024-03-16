<?php
// Assuming session_start() is called at the beginning of your session management script


// var_dump($userId);

function getPreviousNightSleepDuration($userId, $conn) {
    // Adjust the SQL query to select the sleep_duration directly
    $sql = "SELECT sleep_duration FROM sleep_tracker WHERE user_id = ? ORDER BY date_of_sleep DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // Directly return the sleep_duration value
        return $row['sleep_duration'];
    } else {
        return "No data available";
    }
}

?>
