
<!-- Rest of the wakeTimesChart.php content -->
<?php
// Include the PHP file that defines getLatestSleepData() and calculateSleepStages()
//include 'chartSleepData.php';

// Assuming session start and database connection setup is handled in sessionconnection.php
//include 'sessionconnection.php';

// Fetch the sleep data for the currently logged-in user
//$sleepData = getLatestSleepData();

// if ($sleepData) {
//     $sleepCycles = calculateSleepStages($sleepData['sleep_time'], $sleepData['wake_time']);
// } else {
//     // Handle the case when there's no sleep data for the user
//     // For the sake of the D3 chart, we might set some default or empty values here
//     $sleepData = ['sleep_time' => '00:00:00', 'wake_time' => '00:00:00']; // default times
//     $sleepCycles = []; // empty array
// }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sleep Stages Chart</title>
    <!-- <script src="https://d3js.org/d3.v7.min.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
    <style>
     /* .chart-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 500px; 
        background-color: #131862; 
    }
    
    #sleepCycleChart {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2); 
        border-radius: 8px; 
        max-width: 600px; 
        width: 100%; 
    } */
</style>
</head>
<body>

    <canvas id="sleepCycleChart"></canvas>

    
    <script>
   const sleepData = <?php echo json_encode(getLatestSleepData()); ?>;
    const sleepCycles = <?php echo json_encode(calculateSleepStages($sleepData['sleep_time'], $sleepData['wake_time'])); ?>;

    // Parsing PHP date into JavaScript Date object
    const sleepStartTime = new Date('1970-01-01T' + sleepData['sleep_time'] + 'Z');
    const wakeEndTime = new Date('1970-01-01T' + sleepData['wake_time'] + 'Z');
    if (wakeEndTime <= sleepStartTime) {
        wakeEndTime.setDate(wakeEndTime.getDate() + 1); // Adjust for crossing midnight
    }

    function transformSleepData(sleepCycles) {
        let currentTime = sleepStartTime;
        return sleepCycles.flatMap((cycle, index) => {
            return Object.entries(cycle).map(([stage, duration]) => {
                const endTime = new Date(currentTime.getTime() + duration * 60000); // duration in ms
                const segment = {
                    x: currentTime,
                    x2: endTime,
                    y: stage
                };
                currentTime = endTime; // Update currentTime to the end of the current stage
                return segment;
            });
        });
    }

    // Get canvas context
    const ctx = document.getElementById('sleepCycleChart').getContext('2d');

    // Gradient background
    const gradient = ctx.createLinearGradient(0, 0, 0, ctx.canvas.height);
    gradient.addColorStop(0, '#002d72'); // Top gradient color
    gradient.addColorStop(1, '#81a4f8'); // Bottom gradient color

    // Transformed sleep data for Chart.js
    const transformedData = transformSleepData(sleepCycles);
    const datasets = [
        {
            label: 'Sleep Stage',
            data: transformedData,
            borderColor: gradient,
            borderWidth: 2,
            pointRadius: 0,
            pointHoverRadius: 0,
            stepped: true,
        }
    ];

    // Initialize the Chart.js chart with transformed sleep data and options
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            datasets: datasets
        },
        options: {
            scales: {
                x: {
                    type: 'time',
                    time: {
                        parser: 'HH:mm:ss', // The format your time data is in
                        tooltipFormat: 'HH:mm'
                    },
                    title: {
                        display: true,
                        text: 'Time of Night',
                        color: 'white' // Set x-axis title color to white
                },
                ticks: {
                    color: 'white' // Set x-axis ticks color to white
                }
                    
                },
                y: {
                    reversed: true, // Reverse the y-axis to show deeper sleep stages at the bottom
                    type: 'category',
                    labels: ['N1', 'REM', 'N2', 'N3'], // Ordered as they will appear top to bottom
                    ticks: {
                    color: 'white' // Set y-axis ticks color to white
                }
                }
            },
            elements: {
                line: {
                    fill: false,
                    tension: 0 // No curve, create stepped lines
                },
                point: {
                    radius: 0 // Hide points on the line
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        title: function(context) {
                            // Display start and end time for each sleep stage segment
                            const segment = context[0].element.$context.raw;
                            return segment.y + ': ' + segment.x.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }) + ' - ' + segment.x2.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                        },
                        label: function(context) {
                            // Calculate duration for tooltip
                            const segment = context.element.$context.raw;
                            const duration = (segment.x2 - segment.x) / 60000; // Convert ms to minutes
                            return `Duration: ${duration.toFixed(0)} minutes`;
                        }
                    },
                    mode: 'nearest',
                    intersect: false
                },
                legend: {
                    display: false // No legend needed
                }
            }
        }
    });
    
</script>
</body>
</html>



