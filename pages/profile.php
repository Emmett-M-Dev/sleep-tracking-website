<?php  
include 'includes/sessionconnection.php';
include 'sleep_data_display.php'; 

$sleepStreak = calculateSleepStreak();
$sleepQualityAverage = getSleepQualityAverage();
$sleepTimeAverage = getSleepTimeAverage();





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
                            <span>Last Night's Sleep</span>
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
                    <!-- Sleep Progress Section -->
                    <div class="bg-gray-800 p-6 rounded-lg my-5">
                        <h2 class="text-lg font-semibold mb-4">Sleep Progress</h2>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-center">
                                <i class="fas fa-check-circle fa-2x mb-2"></i>
                                <p>Completed Sleep</p>
                                <p>3</p>
                            </div>
                            <div class="text-center">
                                <i class="fas fa-bed fa-2x mb-2"></i>
                                <p>Total Sleep Hours</p>
                                <p>Total Sleep</p>
                            </div>
                            <div class="text-center">
                                <i class="fas fa-trophy fa-2x mb-2"></i>
                                <p>Sleep Achievements</p>
                                <p>7</p>
                            </div>
                        </div>
                    </div>
                    <!-- Sleep Achievements Section -->
                    <div class="bg-gray-800 p-6 rounded-lg">
                        <h2 class="text-lg font-semibold mb-4">Sleep Achievements</h2>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center">
                                <i class="fas fa-stream fa-2x mb-2"></i>
                                <p>Sleep Streak</p>
                                <p>2/3</p>
                            </div>
                            <div class="text-center">
                                <i class="fas fa-gem fa-2x mb-2"></i>
                                <p>Sleep Points</p>
                                <p>1200/3000</p>
                            </div>
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

