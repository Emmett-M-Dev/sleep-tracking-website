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
$labels = [
    'N1' => 'Light ',
    'N2' => 'Intermediate ',
    'N3' => 'Deep ',
    'REM' => 'REM ',
];


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Progress Circles</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
   .chart-label {
    color: white; /* Set the text color */
    text-align: center; /* Center the text below the donut chart */
    margin-top: 5px; /* Add some space between the chart and the label */
    font-size: 16px; /* Set the font size for the label */
    font-weight: bold; /* Optional: make the label text bold */
}
    </style>
</head>
<body>


<!-- Assuming you already loaded Chart.js in the head of your tracker.php -->
<div class="progress-circles-container">
    <?php foreach ($stagePercentages as $stage => $percentage): ?>
        <div class="percentage-chart-container">
            <canvas id="progress<?= htmlspecialchars($stage) ?>"></canvas>
            <figcaption class="chart-label"><?= $labels[$stage] ?></figcaption>
        </div>
    <?php endforeach; ?>
</div>

<script>
    <?php foreach ($stagePercentages as $stage => $percentage): ?>
    createProgressChart('progress<?= htmlspecialchars($stage) ?>', <?= $percentage ?>, '<?= $colors[$stage] ?>', '<?= $labels[$stage] ?>');
    <?php endforeach; ?>

    // Function to create a chart for a canvas
    function createProgressChart(canvasId, percentage, color, label) {
    var ctx = document.getElementById(canvasId).getContext('2d');
    var chart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [percentage, 100 - percentage],
                backgroundColor: [color, 'lightgrey'],
                borderWidth: 0
            }]
        },
        options: {
            rotation: Math.PI,
            circumference: 115 * Math.PI,
            cutout: '60%', // Adjust this for a thicker/thinner ring
            responsive: false,
            maintainAspectRatio: false,
            tooltips: {enabled: false},
            hover: {mode: null},
            animation: {
                animateRotate: false
            },
            plugins: {
                legend: { display: false },
                tooltip: { enabled: false },
                title: {
                    display: true,
                    text: `${percentage}%`,
                    color: 'white',
                    font: {
                        size: 18
                    }
                }
            }
        }
    });
}

// Create charts with labels
createProgressChart('progressN1', percentages['N1'], '#3498db', 'Light Sleep'); // N1: Blue
createProgressChart('progressN2', percentages['N2'], '#e74c3c', 'Intermediate Sleep'); // N2: Red
createProgressChart('progressN3', percentages['N3'], '#f1c40f', 'Deep Sleep'); // N3: Yellow
createProgressChart('progressREM', percentages['REM'], '#2ecc71', 'REM Sleep');
</script>

</body>
</html>
