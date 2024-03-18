
<!-- Rest of the wakeTimesChart.php content -->
<?php
// Include the PHP file that defines getLatestSleepData() and calculateSleepStages()
//include 'chartSleepData.php';

// Assuming session start and database connection setup is handled in sessionconnection.php
//include 'sessionconnection.php';

// Fetch the sleep data for the currently logged-in user
//$sleepData = getLatestSleepData();

if ($sleepData) {
    $sleepCycles = calculateSleepStages($sleepData['sleep_time'], $sleepData['wake_time']);
} else {
    // Handle the case when there's no sleep data for the user
    // For the sake of the D3 chart, we might set some default or empty values here
    $sleepData = ['sleep_time' => '00:00:00', 'wake_time' => '00:00:00']; // default times
    $sleepCycles = []; // empty array
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sleep Stages Chart</title>
    <script src="https://d3js.org/d3.v7.min.js"></script>
</head>
<body>
    <svg id="sleepChart" width="960" height="500"></svg>
    
    <script>
 // Prepare the data
const sleepData = <?php echo json_encode($sleepData); ?>;
console.log('sleepData', sleepData);
const sleepCycles = <?php echo json_encode($sleepCycles); ?>;
console.log('sleepCycles', sleepCycles);

// Select the SVG container
const svg = d3.select('#sleepChart');
const height = 500; // SVG height
const margin = {top: 20, right: 20, bottom: 30, left: 70};

// Parse the sleep and wake times
const parseTime = d3.timeParse('%H:%M:%S');
const sleepTime = parseTime(sleepData['sleep_time']);
const wakeTime = parseTime(sleepData['wake_time']);
if (wakeTime < sleepTime) { // Adjust for crossing midnight
    wakeTime.setDate(wakeTime.getDate() + 1);
}

// Define the x-axis scale (time scale)
const xScale = d3.scaleTime()
                 .domain([sleepTime, wakeTime])
                 .range([margin.left, 960 - margin.right]);

// Define the y-axis scale (band scale)
const yScale = d3.scaleBand()
                 .domain(['Awake', 'Light Sleep', 'Deep Sleep', 'REM Sleep'])
                 .range([margin.top, height - margin.bottom])
                 .padding(0.1);

// Append the x-axis
svg.append('g')
   .attr('transform', `translate(0,${height - margin.bottom})`)
   .call(d3.axisBottom(xScale).tickFormat(d3.timeFormat("%H:%M")).ticks(d3.timeHour.every(1)));

// Append the y-axis
svg.append('g')
   .attr('transform', `translate(${margin.left},0)`)
   .call(d3.axisLeft(yScale))
   .selectAll("text")
   .style("fill", function(d) {
       switch (d) {
           case 'Awake': return 'red';
           case 'Light Sleep': return '#ADD8E6'; // Light blue
           case 'Deep Sleep': return 'blue';
           case 'REM Sleep': return 'darkblue';
           default: return 'black';
       }
   });
  
</script>
</body>
</html>



