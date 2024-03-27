
<?php

 //require 'includes/sessionconnection.php';
require 'process_form.php';
include 'sleep_data_display.php';
include 'chartSleepData.php';


// if (isset($_GET['status']) && $_GET['status'] === 'success') {
//   echo "<p>Form submitted successfully!</p>";
// }
//For widget 
$sleepScore = calculateSleepScore();
$lastSleepDuration = getLastSleepDuration();
$wakeTime = getLatestWakeTime();
$sleepStreak = calculateSleepStreak();

$sleepData = getLatestSleepData();

// Check if sleep data is available and calculate sleep cycles if so
if ($sleepData) {
    $sleepCycles = calculateSleepStages($sleepData['sleep_time'], $sleepData['wake_time'],$sleepData['sleep_quality']);
    $stagePercentages = calculateStagePercentages($sleepCycles);
} else {
    // Handle the case when there's no sleep data
    $sleepData = ['sleep_time' => '00:00:00', 'wake_time' => '00:00:00']; // Default times for the purpose of demonstration
    $sleepCycles = []; // Empty array
}

//////Code for testing if the getLatestSleepData method works//////
// echo "Sleep Data Fetched:<br><pre>";
// print_r($sleepData);
// echo "</pre>";
// if ($sleepData) {
//     $sleepCycles = calculateSleepStages($sleepData['sleep_time'], $sleepData['wake_time']);
    
//     echo "Calculated Sleep Cycles:<br><pre>";
//     print_r($sleepCycles);
//     echo "</pre>";
// } else {
//     echo "No sleep data available for the user.";
// }




?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <!-- Include Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- includeing d3 libary -->
<!-- <script src="https://d3js.org/d3.v6.min.js"></script> -->





 
 <style>
 #section-4 {
  background: linear-gradient(to right, #2b1055, #7597de);
  padding: 50px 0;
  display: flex;
  justify-content: center; /* Center the content */
  align-items: flex-start;
  text-align: center;
  color: white;
  position: relative;
}

.chart-container {
  padding: 20px;
  box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.5);
  background-color: rgba(255, 255, 255, 0.1);
  border-radius: 15px;
  border: 1px solid rgba(255, 255, 255, 0.2);
  height: 400px;
  /* Subtract total width of the info box and the margin from 100% to get the chart width */
  width: calc(100% - 320px - 20px);
  flex: none; /* Disable flex grow and shrink */
}
.sleep-info-container {
  width: 600px; /* Fixed width of the info box */
  padding: 20px;
  margin-left: 300px; /* Maintain 20px distance from the chart */
  position: relative; /* Position relative to the parent */
  height: 400px;
}
.sleep-info {
    position: absolute; /* Positioned absolutely relative to its positioned parent */
  right: 20px; /* 20px from the right edge of the parent container */
  top: 0; /* Align with the top of the container */
  width: 300px; /* Fixed width of the info box */
  max-width: none; /* Override any previous max-width */
  padding: 20px; /* Padding on all sides */
  margin-left: 20px; /* Space between the chart and the info box */
  border-left: 2px solid #7597de; /* A border to visually separate from the chart */
  background-color: #202020; /* Sets a background color for visibility */
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  visibility: hidden; /* Start with info box invisible */
  opacity: 0; /* Start with info box fully transparent */
  transition: opacity 0.2s ease-in-out; /* Smooth transition for opacity */
  z-index: 10; /* Ensure it's above other elements */
}

.hidden {
  visibility: hidden;
  opacity: 0;
  transition: visibility 0.2s, opacity 0.2s linear;
}

.visible {
  visibility: visible;
  opacity: 1;
}

.chart-container:hover + .sleep-info,
.sleep-info:hover {
  visibility: visible; /* Make the info box visible on hover */
  opacity: 1;
}

.percentage-chart-container {
    width: 100%; /* Adjust the width as desired, or remove if not needed */
    max-width: 100px; 
    margin-bottom: 8px; /* Add more space at the bottom of each chart container */
    display: flex;
    flex-direction: column;
    align-items: center; /* Do not grow or shrink */
}
.progress-circles-container {
    display: flex;
    flex-direction: column;
    align-items: center; /* This will center the container itself */
    justify-content: space-around; /* This will add space between the items */
    padding: 10px 0; /* This adds padding at the top and bottom of the container */
}

.percentage-chart-container canvas {
  width: 100%;
  height: 100%;
}

@media screen and (max-width: 1024px) {
    .chart-container,
  .sleep-info {
    margin: 0;
    max-width: 100%;
    width: auto; /* Allow the box to adjust to the content width */
  }

  .sleep-info {
    position: static; /* On smaller screens, make the box flow with the document */
    margin: 20px 0; /* Add some space above and below */
    right: auto; /* Remove the absolute positioning offsets */
    top: auto;
    width: auto; /* Allow the box to adjust to the content width */
    visibility: visible; /* Always visible on smaller screens */
    opacity: 1;
  }
}
</style>

</head>
<body class="bg-gray-900 text-white">
<?php  include 'includes/nav.php'; ?>


  <!-- Hero Section -->
  <section id="section-1" class="relative h-screen flex flex-col justify-center items-center text-center text-white px-4">
    <img src="includes/images/evening_sky.png" alt="Night Sky" class="absolute top-0 left-0 w-full h-full object-cover" />

    <div class="z-10 flex justify-between items-center w-full max-w-4xl mx-auto">
        <!-- Left Div: Greeting and User Name -->
        <div>
            <h1 class="text-6xl font-bold mb-4 hero-title">Hello <?php echo $username; ?></h1>
            <p class="text-xl mb-8">Let's continue tracking your sleep</p>
        </div>


        <!-- Right Div: Sub Progress Bars -->
        <div class="mt-8 p-6 bg-white/30 backdrop-blur-lg rounded-xl border border-gray-200/50 flex items-center justify-between">
    
    <!-- Left Div: Main Sleep Score -->
    <div class="flex flex-col items-center mr-8">
        <div class="w-40 h-40 border-8 rounded-full border-green-500 text-5xl font-bold flex justify-center items-center" id="sleepScoreDisplay">
            <?php echo $sleepScore; ?>%
        </div>
        <p class="text-xl mt-2">Sleep Score</p>
    </div>

    <!-- Right Div: Sub Progress Bars -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <span class="text-sm">Hours Slept </span>
            
            <span class="text-sm font-semibold" id="hoursSleptDisplay"><?php echo $lastSleepDuration  ?></span>
        </div>

        <div class="flex items-center justify-between">
            <span class="text-sm">Wake Time</span>
            <span class="text-sm font-semibold" id="wakeTimeDisplay"><?php echo $wakeTime; ?></span>
            
                <!-- The width of the bar could be dynamic based on wake time, if relevant -->
               
            </div>
        
        <div class="flex items-center justify-between">
            <span class="text-sm">Sleep Streak</span>
            <span class="text-sm font-semibold" id="sleepStreakDisplay"><?php  echo $sleepStreak; ?> Days</span>
            
            </div>
        </div>
    </div>
</div>

    </div>

    <!-- arrow to scroll -->
    <div class="absolute inset-x-0 bottom-0 h-16">
        <a href="#section-2" class="scroll-arrow">
            <i class="fa fa-arrow-down"></i> Continue here to begin sleep tracking
        </a>
    </div>
</section>


  <!-- Tool Section for actually tracking sleep -->
  <section id="section-2" class="scroll-arrow relative h-screen flex flex-col justify-center items-center text-center text-white px-4">
  <div class="grid grid-cols-2 gap-10">  


  <!-- Sleep & Description Section -->
  <form id='sleepTrackerForm' action="process_form.php" method="post" class="p-6 float-left">
    <div class="bg-gray-700 p-4 rounded-lg">
        <!-- Date of Sleep Input -->
        <div class="mb-4">
            <label for="dateOfSleep" class="block mb-2">Date of Sleep</label>
            <input type="date" id="dateOfSleep" name="dateOfSleep" class="w-full p-2 bg-gray-600 rounded" required>
        </div>

        <!-- Sleep Time Input -->
        <div class="mb-4">
            <label for="sleepTime" class="block mb-2">Sleep Time</label>
            <input type="time" id="sleepTime" name="sleepTime" class="w-full p-2 bg-gray-600 rounded" required>
        </div>

        <!-- Wake Time Input -->
        <div class="mb-4">
            <label for="wakeTime" class="block mb-2">Wake Time</label>
            <input type="time" id="wakeTime" name="wakeTime" class="w-full p-2 bg-gray-600 rounded" required>
        </div>

        <!-- Sleep Quality Dropdown -->
        <div class="mb-4">
            <label for="sleepQuality" class="block mb-2">Sleep Quality</label>
            <select id="sleepQuality" name="sleepQuality" class="w-full p-2 bg-gray-600 rounded">
                <option value="Very Bad">Very Bad</option>
                <option value="Bad">Bad</option>
                <option value="Fair">Fair</option>
                <option value="Good">Good</option>
                <option value="Excellent">Excellent</option>
            </select>
        </div>

        <!-- Comments Input -->
        <div class="mb-4">
            <label for="comments" class="block mb-2">Comments</label>
            <textarea id="comments" name="comments" placeholder="Add a comment" class="w-full p-2 bg-gray-600 rounded"></textarea>
        </div>
        
        <!-- Submit Button -->
        <div class="submit-button-container">
            <button type="submit" id='submit'  class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Submit Sleep Data
            </button>
        </div>
    </div>  
</form>


 
<!-- Calendar Widget Container -->
<div id="calendar-widget" class="calendar-container p-6">
  <div class="bg-gray-700 p-4 rounded-lg">
    <!-- Calendar Header -->
    <div class="calendar-header flex justify-between items-center mb-4">
      <button id="prev-month" class="calendar-nav p-2 bg-gray-600 rounded">
        <span class="fas fa-chevron-left"></span>
      </button>
      <div id="current-month" class="current-month">Month Year</div>
      <button id="next-month" class="calendar-nav p-2 bg-gray-600 rounded">
        <span class="fas fa-chevron-right"></span>
      </button>
    </div>

    <!-- Calendar Grid -->
    <div class="calendar-grid grid grid-cols-7 gap-4">
      <!-- Weekday Headers -->
      <div class="text-center font-bold">Sun</div>
      <div class="text-center font-bold">Mon</div>
      <div class="text-center font-bold">Tue</div>
      <div class="text-center font-bold">Wed</div>
      <div class="text-center font-bold">Thu</div>
      <div class="text-center font-bold">Fri</div>
      <div class="text-center font-bold">Sat</div>
      <!-- Day Cells (to be populated by JavaScript) -->
    </div>
  </div>
</div>


</div>
<!-- arrow to next section -->
<div class="absolute inset-x-0 bottom-0 h-16">
        <a href="#section-3" class="scroll-arrow">
            <i class="fa fa-arrow-down"></i> Continue 
        </a>
    </div>
  </section>


        <!-- Section 3: Sleep Overview -->
        <section id='section-3' class="scroll-arrow relative flex flex-col text-gray justify-center py-8 px-4 items-center text-center bg-gray-600 text-white" style= "background-image: url('includes/images/sunn3.png')";>
        <!-- <img src="includes/images/sunn3.png" alt="MorningSky" class="absolute top-0 left-0 w-full h-full object-cover" /> -->
    <!-- Header -->
    <!-- <div class="text-center mb-6">
        <h2 class="text-3xl font-bold mb-2">Your Sleep Overview</h2>
        <p class="text-lg">Explore your sleep patterns and get tips for better rest.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Sleep Trend Chart Container -->
        <!-- <div class="chart-container p-4 bg-gray-700 text-white rounded-lg shadow-lg">
            
        </div> -->




        <!-- Sleep Insights Container -->
        <!-- <div class="insights-container p-4 bg-gray-700 text-white rounded-lg shadow-lg">
            <h3 class="text-xl font-semibold mb-3">Sleep Insights</h3>
            
            <ul id="sleepInsights" class="list-disc list-inside">
                 Sleep insights will be populated here -->
            <!-- </ul>
        </div>
    </div> -->

    <!-- Additional Charts -->
    <!-- <div class="additional-charts grid grid-cols-1 md:grid-cols-2 gap-4 p-4">
        <div class="sleep-quality-chart bg-gray-700 rounded-lg shadow-lg p-4">
            <canvas id="sleepQualityChart"></canvas>
        </div> -->
            <!-- Personalized Sleep Tips Container -->
        <!-- <div class="tips-container p-4 bg-gray-700 text-white rounded-lg shadow-lg">
        <h3 class="text-xl font-semibold mb-3">Personalized Sleep Tips</h3> -->
        <!-- Sleep tips will be populated here -->
    <!-- </div>
        
    </div> -->


    
</section>

<section id='section-4' class="relative flex justify-center py-8 px-4 items-start text-center bg-gray-600 text-white">
  <div class="flex justify-around items-start w-full max-w-6xl"> <!-- Use justify-around for even spacing -->
  
    <!-- Progress Bars Container -->
    <div class="progress-bars-container flex flex-col justify-start mr-12"> <!-- Vertically stacked progress bars -->
      <?php include 'percentageBars.php'; ?> <!-- Includes the progress bars -->
    </div>
    
    <!-- Chart Container -->
    <div class="chart-container flex-1 mr-12"> <!-- Flex container for the chart -->
      <?php include 'wakeTimesChart.php'; ?>
    </div>

    <!-- Sleep Stage Information -->
    <div class="sleep-info-container">
      <div id="sleepStageInfo" class="sleep-info hidden"> <!-- Hidden by default, shown on hover -->
        <h2 id="sleepStageTitle">N1: Light Sleep</h2>
        <p id="sleepStageDescription">This stage marks the transition from wakefulness into sleep and usually lasts for a short period. It's easy to be awakened from this stage, and if disrupted, one may not feel as if they've slept at all.</p>
      </div>
    </div>
</section>


        <!-- footer -->
        <?php include 'includes/footer.php'?>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
       //scroll feature for screen
    //     document.querySelectorAll('.scroll-arrow').forEach(anchor => {
    //     anchor.addEventListener('click', function (e) {
    //         e.preventDefault();

    //         const targetSection = document.querySelector(this.getAttribute('href'));
    //         if(targetSection) {
    //             targetSection.scrollIntoView({ 
    //                 behavior: 'smooth' 
    //             });
    //         }
    //     });
    // });
    let sleepCyclesData = <?php echo json_encode($sleepCycles); ?>;
  


    // Grims code for form functionality
    document.getElementById('sleepTrackerForm').addEventListener('submit', function(e) {
      console.log('Form attempted to submit');
        // Example client-side validation
        let dateOfSleep = document.getElementById('dateOfSleep').value;
        let sleepTime = document.getElementById('sleepTime').value;
        let wakeTime = document.getElementById('wakeTime').value;
        let sleepQuality = document.getElementById('sleepQuality').value;

        if (!dateOfSleep || !sleepTime || !wakeTime || sleepQuality === "") {
            e.preventDefault(); // Prevent form submission
            alert('Please fill in all required fields.');
        }
    // Parse dates
    let sleepDateTime = new Date(dateOfSleep + ' ' + sleepTime);
    let wakeDateTime = new Date(dateOfSleep + ' ' + wakeTime);

    // Account for crossing over midnight
    if (wakeDateTime < sleepDateTime) {
        wakeDateTime.setDate(wakeDateTime.getDate() + 1);
    }

    // Calculate duration in milliseconds
    let durationMs = wakeDateTime - sleepDateTime;

    // Convert milliseconds into hours and minutes
    let durationHours = Math.floor(durationMs / (3600 * 1000));
    let durationMinutes = Math.floor((durationMs % (3600 * 1000)) / 60000);

    // Construct duration string in "HH:MM" format
    let sleepDuration = `${durationHours.toString().padStart(2, '0')}:${durationMinutes.toString().padStart(2, '0')}`;

    // Debugging: Log the calculated sleep duration
    console.log(`Calculated Sleep Duration: ${sleepDuration}`);


    let formData = new FormData();
    formData.append('dateOfSleep', dateOfSleep);
    formData.append('sleepTime', sleepTime);
    formData.append('wakeTime', wakeTime);
    formData.append('sleepDuration', sleepDuration);
    formData.append('sleepQuality', sleepQuality);

     // AJAX call to send the form data to process_form.php
     fetch('process_form.php', {
        method: 'POST',
        body: new URLSearchParams(new FormData(document.getElementById('sleepTrackerForm')))
    })
    .then(response => response.json())
    .then(data => {
        // Handle successful response here, e.g., show a message or redirect
    })
    .catch(error => {
        // Handle network errors here
        console.error('Error:', error);
    });
});

// Function to update the widget display
function updateSleepWidget(sleepData) {
        // Update the sleep score display
        const sleepScoreDisplay = document.getElementById('sleepScoreDisplay');
        if (sleepScoreDisplay) {
            sleepScoreDisplay.textContent = sleepData.sleepScore + '%';
        }

        // Update the hours slept display
        const hoursSleptDisplay = document.getElementById('hoursSleptDisplay');
        if (hoursSleptDisplay) {
            hoursSleptDisplay.textContent = sleepData.hoursSleptText;
        }

        // Update the wake time display
        const wakeTimeDisplay = document.getElementById('wakeTimeDisplay');
        if (wakeTimeDisplay) {
            wakeTimeDisplay.textContent = sleepData.wakeTime;
        }

        // Update the sleep streak display
        const sleepStreakDisplay = document.getElementById('sleepStreakDisplay');
        if (sleepStreakDisplay) {
            sleepStreakDisplay.textContent = sleepData.sleepStreak + ' Days';
        }

        // Update the progress bars' widths
        // Adjust the width calculations as needed based on your design
        document.querySelector('.bg-blue-500').style.width = sleepData.sleepScore + '%';
        document.querySelector('.bg-red-500').style.width = sleepData.sleepStreak * 10 + '%';
    }

    // Make an AJAX call to the PHP script that returns the sleep data as JSON
    


       




        
});






    </script>
 
   
    </body>
</html>