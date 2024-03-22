<?php $percentages = [
    'N1' => 5.43,
    'N2' => 34.78,
    'N3' => 34.78,
    'REM' => 25,
];

// Define colors for each stage.
$colors = [
    'N1' => '#3498db',
    'N2' => '#e74c3c',
    'N3' => '#f1c40f',
    'REM' => '#2ecc71',
];


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Progress Circles</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
   
    </style>
</head>
<body>


<!-- Assuming you already loaded Chart.js in the head of your tracker.php -->
<div class="progress-circles-container">
    <?php foreach ($stagePercentages as $stage => $percentage): ?>
        <div class="percentage-chart-container">
            <canvas id="progress<?= htmlspecialchars($stage) ?>"></canvas>
        </div>
    <?php endforeach; ?>
</div>

<script>
    <?php foreach ($stagePercentages as $stage => $percentage): ?>
    createProgressChart('progress<?= htmlspecialchars($stage) ?>', <?= $percentage ?>, '<?= $colors[$stage] ?>');
    <?php endforeach; ?>

    // Function to create a chart for a canvas
    function createProgressChart(canvasId, percentage, color) {
        var ctx = document.getElementById(canvasId).getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [percentage, 100 - percentage],
                    backgroundColor: [color, '#e5e5e5'], // Light grey for the unfilled part
                    borderColor: ['#343a40', '#343a40'], // Dark grey border for both parts
                    borderWidth: [0, 0],
                    hoverBorderColor: ['#343a40', '#343a40'],
                }],
                labels: [
                    'Sleep Stage',
                    'Remaining',
                ]
            },
            options: {
                rotation: -0.5 * Math.PI, // Start at the top
                circumference: 115 * Math.PI, // Full circle
                cutoutPercentage: 80, // Increase cutout for a thinner ring
                responsive: true, // Ensure chart is responsive
                maintainAspectRatio: false, // Maintain aspect ratio
                tooltips: { enabled: false },
                hover: { mode: null },
                animation: {
                    animateRotate: true,
                    animateScale: false,
                },
                plugins: {
                    legend: { display: false },
                    title: {
                        display: true,
                        text: (ctx) => `${ctx.chart.data.datasets[0].data[0]}%`,
                        color: 'white',
                        font: {
                            size: 18
                        }
                    }
                }
            }
        });
    }

    // Create charts
    createProgressChart('progressN1', percentages['N1'], '#3498db'); // N1: Blue
    createProgressChart('progressN2', percentages['N2'], '#e74c3c'); // N2: Red
    createProgressChart('progressN3', percentages['N3'], '#f1c40f'); // N3: Yellow
    createProgressChart('progressREM', percentages['REM'], '#2ecc71'); // REM: Green
</script>

</body>
</html>
