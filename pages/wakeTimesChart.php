



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sleep Stages Chart</title>
    <!-- <script src="https://d3js.org/d3.v7.min.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
    
</head>
<body>

    <canvas id="sleepCycleChart"></canvas>

    
    <script>
   const sleepData = <?php echo json_encode(getLatestSleepData()); ?>;
    const sleepCycles = <?php echo json_encode(calculateSleepStages($sleepData['sleep_time'], $sleepData['wake_time'], $sleepData['sleep_quality'])); ?>;

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
            pointRadius: 5,
            pointHoverRadius: 10,
            pointHitRadius: 20,
            stepped: true,
        }
    ];
    const stageInfo = {
        N1: {
        title: "N1: Light Sleep",
        description: "The N1 stage marks the transition from wakefulness into sleep. It's a light sleep stage from which you can be easily awakened. During N1, the body hasn't fully relaxed, and brain wave activity begins to slow down from its daytime wakefulness patterns.",
    },
    N2: {
        title: "N2: Intermediate Sleep",
        description: "The N2 stage accounts for the largest portion of the sleep cycle. It acts as a transitional phase into deeper sleep stages. During N2, your heart rate and breathing stabilize at a low rate, body temperature drops, and brain waves show a new pattern distinct from waking states.",
    },
    N3: {
        title: "N3: Deep Sleep",
        description: "N3, often referred to as deep or slow-wave sleep, is the most restorative sleep stage. It's more difficult to be awakened when in N3, and disorientation or grogginess can occur if sleep is disrupted. During this stage, the body repairs muscles and tissues, stimulates growth and development, boosts immune function, and builds up energy for the next day.",
    },
    REM: {
        title: "REM Sleep",
        description: "REM (Rapid Eye Movement) sleep is characterized by rapid movement of the eyes, paralysis of the body's muscles, and vivid dreaming. Brain wave activity during REM is similar to wakefulness, making it a unique sleep stage. REM sleep enhances learning, memory, and emotional health.",
    }
};

    
    // Initialize the Chart.js chart with transformed sleep data and options
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            datasets: datasets
        },
        options: {
            onHover: (event, chartElements) => {
    if (chartElements.length > 0) {
        const element = chartElements[0];
        const datasetIndex = element.datasetIndex;
        const index = element.index;
        const stage = chart.data.datasets[datasetIndex].data[index].y; // Assuming y contains the stage name

        // Check if the stage has defined info
        if (stageInfo[stage]) {
            updateAndDisplayInfo(stageInfo[stage].title, stageInfo[stage].description);
        } else {
            hideInfoBox(); // Hide the box if the stage is not recognized
        }
    } else {
        hideInfoBox();
    }
},
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
                    display: false 
                }
            }
        }
    });

    const dataPoint = chart.data.datasets[datasetIndex].data[index];

function updateAndDisplayInfo(title, description) {
    console.log("updateAndDisplayInfo called with:", title, description);
  const infoBox = document.getElementById('sleepStageInfo');
  const titleElement = document.getElementById('sleepStageTitle');
  const descriptionElement = document.getElementById('sleepStageDescription');

  titleElement.textContent = title;
  descriptionElement.textContent = description;

  // Just add 'visible' class to show the info box
  infoBox.classList.remove('hidden');
  infoBox.classList.add('visible');
}

function hideInfoBox() {
    console.log("hideInfoBox called");
  const infoBox = document.getElementById('sleepStageInfo');
  // Remove 'visible' class to hide the info box
  infoBox.classList.remove('visible');
  infoBox.classList.add('hidden');
}



    
</script>
</body>
</html>



