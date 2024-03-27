<?php  
include 'includes/sessionconnection.php';
include 'sleep_data_display.php'; 

$sleepStreak = calculateSleepStreak();
$sleepQualityAverage = getSleepQualityAverage();
$sleepTimeAverage = getSleepTimeAverage();

$averageBedtime = getAverageBedtime(); // Now correctly using the new function
// Assuming the function returns "No sleep data available" if no data exists
$recommendedWakeupTime = "";

// Check if we have a valid average bedtime before trying to calculate the recommended wakeup time
if ($averageBedtime !== "No sleep data available") {
    $bedtimeDateTime = new DateTime($averageBedtime);
    $recommendedWakeupTime = $bedtimeDateTime->modify('+8 hours')->format('H:i');
    $wakeupMessage = "To help reinforce your body's natural circadian rhythm, we recommend waking up at $recommendedWakeupTime. This complements your bedtime and completes an ideal 8 hours of rest.";
} else {
    $wakeupMessage = "We can't calculate your optimal wake-up time yet. Try logging your sleep for a few days.";
}

// Prepare the educational snippet on the benefits of consistent sleep time
$whyConsistencyMatters = "Maintaining a regular sleep schedule helps synchronize your body's internal clock, leading to better sleep quality. It can improve your mood, brain function, and overall health.";


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sleep Tracker Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-image: url('includes/images/StarsBG.png');
            background-attachment: fixed; /* This makes the background image fixed */
            background-position: center; /* Centers the image in the viewport */
            background-repeat: no-repeat; /* Prevents the image from repeating */
            background-size: cover; /* Covers the entire viewport */
            
        }
        
     
        .section {
            
            position: relative; /* Ensures content is positioned over the background */
            z-index: 1; /* Higher than the default z-index of 0 */
            /* Other styles such as padding, margin, etc. */
        }
    </style>
</head>
<body class="font-inter text-white">
<?php  include 'includes/nav.php'; ?>

    <div class="min-h-screen flex">
      
     

        <!-- Main Content -->
        <div class="flex-1 p-10">
            <br><br>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <!-- Left Column - Takes full width on small screens and half on medium screens -->
                <div class="md:col-span-1">
            
                    <div class="flex items-center justify-between">
                        <h1 class="text-xl font-semibold"><?php echo $username; ?>'s Sleep History</h1>
                        <div class="text-sm">
                            <span>Your Sleep Information</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4 my-5">
                        <div class="bg-gray-800 p-4 rounded-lg text-center">
                            <p class="text-sm text-gray-400">Sleep Streak</p>
                            <p class="text-2xl"><?php echo $sleepStreak?></p>
                        </div>
                        <div class="bg-gray-800 p-4 rounded-lg text-center">
                            <p class="text-sm text-gray-400">Sleep Quality Average</p>
                            <p class="text-2xl"><?php echo $sleepQualityAverage ?></p>
                        </div>
                        <div class="bg-gray-800 p-4 rounded-lg text-center">
                            <p class="text-sm text-gray-400">Average Sleep</p>
                            <p class="text-2xl"><?php echo $sleepTimeAverage?></p>
                        </div>
                    </div>
                    
                    
                    <!-- Optimal Sleep Schedule Section -->
                    <div class="container mx-auto p-8 bg-gray-800 rounded-lg shadow-xl mt-5">
    <h2 class="text-2xl font-semibold mb-6 text-white">Your Targeted Sleep Schedule</h2>
    
    <!-- Consistent Bedtime Section -->
    <div class="mb-6">
        <h3 class="text-xl font-semibold mb-2 text-white">Consistent Bedtime: Your Key to Better Sleep</h3>
        <p class="text-md text-white">Based on your recent sleep history, you tend to fall asleep around <span class="font-semibold"><?php echo $averageBedtime; ?></span>. Let's aim to maintain this routine for consistency.</p>
    </div>
    
    <!-- Why Consistency Matters -->
    <div class="mb-6">
        <h3 class="text-xl font-semibold mb-2 text-white">Why Consistency Matters</h3>
        <p class="text-md text-white"><?php echo $whyConsistencyMatters; ?></p>
    </div>
    
    <!-- Tonight's Bedtime Goal -->
    <div class="mb-6">
        <h3 class="text-xl font-semibold mb-2 text-white">Tonight's Bedtime Goal</h3>
        <p class="text-md text-white"><?php echo $averageBedtime !== "No sleep data available" ? "To help reinforce your body's natural circadian rhythm, we recommend continuing to fall asleep at <span class='font-semibold'>$averageBedtime</span>." : "Log your sleep to receive personalized recommendations."; ?></p>
    </div>
    
    <!-- Importance of Routine -->
    <div class="mb-6">
        <h3 class="text-xl font-semibold mb-2 text-white">Importance of Routine</h3>
        <p class="text-md text-white mb-2">Having a consistent nightly routine is crucial for several reasons:</p>
        <ul class="list-disc pl-5 text-white">
            <li>Sleep Efficiency: Consistency leads to faster sleep onset and fewer nighttime awakenings.</li>
            <li>Hormonal Balance: It supports natural hormonal cycles, including the release of melatonin.</li>
            <li>Overall Health: Regular sleep patterns are linked with a lower risk of chronic diseases.</li>
        </ul>
    </div>
    
    <!-- Ideal Wake Time -->
    <div class="mb-6">
        <h3 class="text-xl font-semibold mb-2 text-white">Ideal Wake Time</h3>
        <p class="text-md text-white"><?php echo $wakeupMessage; ?></p>
    </div>
</div>


                </div>
                <!-- Right Column -->
                <div class="md:col-span-1">
                    <!-- Sleep Consistency Section - Removed fixed width to allow responsiveness -->
                    <div class="bg-gray-800 p-6 rounded-lg">
                        <?php include 'consistencyChart.php'?>
                    </div>
                    
                    
                </div>
            </div>
        </div>
    </div>

</body>
</html>

