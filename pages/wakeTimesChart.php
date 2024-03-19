
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

// Update the yScale domain to match the data labels
const yScale = d3.scaleBand()
                 .domain(['N1', 'N2', 'N3', 'REM']) // Make sure this matches your data
                 .range([margin.top, height - margin.bottom])
                 .padding(0.1);

// Adjust the y-axis accordingly
svg.select(".y-axis").call(d3.axisLeft(yScale)); // Assuming you have a class "y-axis" for the y-axis group


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
           case 'N1': return 'red';
           case 'N2': return '#ADD8E6'; // Light blue
           case 'N3': return 'blue';
           case 'REM': return 'darkblue';
           default: return 'black';
       }
   });
  

//STEP 2
// Transform sleep cycles data to a flat array for visualization
// Transform sleep cycles data to a flat array for visualization
let currentTime = sleepTime; // Start at the beginning of the sleep time

const stagesData = sleepCycles.flatMap((cycle, index) => {
  // Reset the currentTime for each cycle
  currentTime = index === 0 ? sleepTime : d3.timeDay.offset(currentTime, 90 / 1440); // Advance by cycle length in days
  return Object.entries(cycle).map(([stage, duration]) => {
    const startTime = new Date(currentTime);
    const endTime = new Date(currentTime.getTime() + duration * 60000); // duration in ms
    currentTime = endTime; // Set currentTime to the end of the current stage
    return { stage, startTime, endTime };
  });
});

// Create rectangles for each sleep stage
svg.selectAll(".sleep-stage")
   .data(stagesData)
   .enter()
   .append("rect")
   .attr("class", "sleep-stage")
   .attr("x", d => xScale(d.startTime))
   .attr("y", d => yScale(d.stage))
   .attr("width", d => {
       const width = xScale(d.endTime) - xScale(d.startTime);
       return width > 0 ? width : 0; // Ensure width is not negative
   })
   .attr("height", yScale.bandwidth())
   .attr("fill", d => {
       // Updated to match the labels from the data
       switch (d.stage) {
           case 'N1': return 'red'; // Awake is assumed to be N1
           case 'N2': return '#ADD8E6'; // Light blue for Light Sleep
           case 'N3': return 'blue'; // Blue for Deep Sleep
           case 'REM': return 'darkblue'; // Dark blue for REM
           default: return 'black'; // Fallback color
       }
   });



// Add tooltips on hover
// svg.selectAll(".sleep-stage")
//    .append("title")
//    .text(d => `${d.stage}: ${d3.timeFormat("%H:%M")(d.startTime)} - ${d3.timeFormat("%H:%M")(d.endTime)}`);







</script>
</body>
</html>



