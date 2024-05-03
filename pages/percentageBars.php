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
    color: white; 
    text-align: center; 
    margin-top: 5px; 
    font-size: 16px; 
    font-weight: bold; 
}
    </style>
</head>
<body>



<div class="progress-circles-container">
<?php 
// Check if there's sleep data available
if (!empty($stagePercentages)) {
    // If sleep data is available, proceed with displaying the progress bars
    foreach ($stagePercentages as $stage => $percentage) {
        echo "<div class=\"percentage-chart-container\">";
        echo "<canvas id=\"progress" . htmlspecialchars($stage) . "\"></canvas>";
        echo "<figcaption class=\"chart-label\">" . $labels[$stage] . "</figcaption>";
        echo "</div>";
    }
} else {
    // If no sleep data is available, display a message instead
    echo "<p>No sleep data available. Please begin your sleep journey by entering your sleep data.</p>";
}

?>
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
            cutout: '60%', 
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
