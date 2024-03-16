  
       //scroll feature for screen
       document.querySelectorAll('.scroll-arrow').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();

            const targetSection = document.querySelector(this.getAttribute('href'));
            if(targetSection) {
                targetSection.scrollIntoView({ 
                    behavior: 'smooth' 
                });
            }
        });
    });

  


    // Grims code for form functionality

    document.addEventListener('DOMContentLoaded', function() {


    ////////Functions///////

    function validateTime(time) {
  // Regex to validate time format HH:MM AM/PM
  var timeFormat = /^([01]?[0-9]|2[0-3]):[0-5][0-9]$/;
  return timeFormat.test(time);
}

function convertTo24HourFormat(time) {
  let [hours, minutes] = time.split(':').map(Number);

  return { hours, minutes };
}

function timeDifferenceIsValid(sleepTime, wakeTime) {
  const { hours: sleepHours, minutes: sleepMinutes } = convertTo24HourFormat(sleepTime);
  const { hours: wakeHours, minutes: wakeMinutes } = convertTo24HourFormat(wakeTime);

  const sleepDate = new Date();
  sleepDate.setHours(sleepHours, sleepMinutes, 0);

  const wakeDate = new Date();
  wakeDate.setHours(wakeHours, wakeMinutes, 0);

  // Ensure that the wake time is after the sleep time
  return wakeDate > sleepDate;
}


// Function to show a confirmation message or update the UI after successful data submission
function showConfirmation() {
  // Example: change button text and disable it temporarily
  const submitButton = document.getElementById('submit-sleep-data');
  submitButton.textContent = 'Data Saved!';
  submitButton.disabled = true;

  // Reset the button after 2 seconds
  setTimeout(() => {
    submitButton.textContent = 'Submit Sleep Data';
    submitButton.disabled = false;
  }, 2000);
}

// Function to handle errors during data submission
function handleError(error) {
  // Log the error for debugging purposes
  console.error('An error occurred:', error);

  // Inform the user
  alert('An error occurred while submitting your data. Please try again.');
}





  // Existing variables from the sleep tracking widget
  var sleepTimeInput = document.getElementById('sleep-time');
  var wakeTimeInput = document.getElementById('wake-time');
  var submitButton = document.getElementById('submit-sleep-data');

  // New variables for the Sleep & Description section
  var sleepTitle = document.getElementById('sleepTitle');
  var description = document.getElementById('description');
  var timesAwakened = document.getElementById('timesAwakened');
  var sleepDuration = document.getElementById('sleepDuration');
  

  // Event listener for the submit button
  submitButton.addEventListener('click', function() {
    
    // Validate all fields
    if (validateTime(sleepTimeInput.value) && validateTime(wakeTimeInput.value)) {
      
      if (timeDifferenceIsValid(sleepTimeInput.value, wakeTimeInput.value)) {
        
        // Collect data from both sections
        var sleepData = {
          sleepTime: sleepTimeInput.value,
          wakeTime: wakeTimeInput.value,
          sleepTitle: sleepTitle.value,
          description: description.value,
          timesAwakened: timesAwakened.value,
          sleepDuration: sleepDuration.value,
          
        };
        alert('Data input success!');
        sendDataToServer(sleepData);
      } else {
        alert('Wake time should be after sleep time.');
      }
    } else {
      alert('Please enter a valid time in HH:MM AM/PM format.');
    }
  });

  // Rest of the existing JavaScript functions go here...
  function serializeData(data) {
  return JSON.stringify(data);
}

  // AJAX request function with error handling
  function sendDataToServer(data) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '/save-sleep-data', true);
    xhr.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');
    xhr.onreadystatechange = function() {
      if (xhr.readyState === 4) {
        if (xhr.status === 200) {
          showConfirmation();
        } else {
          handleError(xhr.responseText);
        }
      }
    };
    xhr.send(serializeData(data));
  }

  // ... including validateTime, timeDifferenceIsValid, showConfirmation, handleError

});
 // Calnder code  - grim ///

 document.addEventListener('DOMContentLoaded', function() {
  const prevButton = document.getElementById('prev-month');
  const nextButton = document.getElementById('next-month');
  const currentMonthDiv = document.getElementById('current-month');
  const grid = document.querySelector('.calendar-grid');
  let currentDate = new Date();
  
  function populateCalendar(date) {
    // Clear the grid
    while (grid.children.length > 7) {
      grid.removeChild(grid.lastChild);
    }

    // Set the current month display
    currentMonthDiv.textContent = date.toLocaleDateString('default', { month: 'long', year: 'numeric' });

    // Start at the first of the month
    let tempDate = new Date(date.getFullYear(), date.getMonth(), 1);
    let dayToAdd = tempDate.getDay();
    let daysInMonth = new Date(date.getFullYear(), date.getMonth() + 1, 0).getDate();

    // Add empty cells for days of the week before the first of the month
    for (let i = 0; i < dayToAdd; i++) {
      let emptyCell = document.createElement('div');
      emptyCell.className = 'day-cell';
      grid.appendChild(emptyCell);
    }

    // Add day cells for each day of the month
    for (let day = 1; day <= daysInMonth; day++) {
let dayCell = document.createElement('div');
dayCell.className = 'day-cell py-2 bg-gray-600 rounded text-center';
dayCell.textContent = day;
 // Highlight the current day
 if (day === currentDate.getDate() && date.getMonth() === currentDate.getMonth() && date.getFullYear() === currentDate.getFullYear()) {
    dayCell.classList.add('bg-blue-600');
    dayCell.classList.remove('bg-gray-600');
  }

  dayCell.addEventListener('click', function() {
    // If there's any previously selected day, remove the highlight
    let selected = grid.querySelector('.bg-blue-600');
    if (selected) {
      selected.classList.add('bg-gray-600');
      selected.classList.remove('bg-blue-600');
    }
    // Highlight the clicked day
    dayCell.classList.add('bg-blue-600');
    dayCell.classList.remove('bg-gray-600');
  });

  grid.appendChild(dayCell);
}
}

function navigateMonths(step) {
currentDate.setMonth(currentDate.getMonth() + step);
populateCalendar(currentDate);
}

// Populate the calendar with the current month
populateCalendar(currentDate);

// Event listeners for navigation
prevButton.addEventListener('click', () => navigateMonths(-1));
nextButton.addEventListener('click', () => navigateMonths(1));
});



//////section 3 - grims///////////

// Part 1: Sleep Trend Chart Initialization
document.addEventListener('DOMContentLoaded', function() {
    initializeSleepTrendChart();
});

function initializeSleepTrendChart() {
    var ctx = document.getElementById('sleepTrendChart').getContext('2d');
    if (window.sleepTrendChart instanceof Chart) {
        window.sleepTrendChart.destroy();
    }

    window.sleepTrendChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
            datasets: [{
                label: 'Hours Slept',
                data: [7, 6.5, 8, 5, 9, 7.5, 8], // Replace with actual data
                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

// Part 2: JavaScript for Sleep Quality Pie Chart:
  function initializeSleepQualityChart() {
    var ctx = document.getElementById('sleepQualityChart').getContext('2d');

    if (window.sleepQualityChart instanceof Chart) {
        window.sleepQualityChart.destroy();
    }

    window.sleepQualityChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Deep Sleep', 'Light Sleep', 'REM Sleep'],
            datasets: [{
                label: 'Sleep Quality',
                data: [25, 50, 25], // Replace these values with actual data
                backgroundColor: [
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(255, 206, 86, 0.6)',
                    'rgba(255, 99, 132, 0.6)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(255, 99, 132, 1)'
                ],
                borderWidth: 1
            }]
        }
    });
}

// Add this function call in the DOMContentLoaded listener
document.addEventListener('DOMContentLoaded', function() {
    initializeSleepTrendChart();
    initializeSleepQualityChart();
});

//////Part 3: Dynamic Sleep Insights

function updateSleepInsights() {
    var sleepData = fetchSleepData(); // Replace with actual data fetching logic
    var insights = generateInsightsBasedOnData(sleepData);
    var insightsList = document.getElementById('sleepInsights'); // Targeting the new ID
    insightsList.innerHTML = ''; // Clear existing insights

    insights.forEach(insight => {
        let listItem = document.createElement('li');
        listItem.textContent = insight;
        insightsList.appendChild(listItem);
    });
}

function fetchSleepData() {
    // Placeholder function for sleep data
    // Replace with actual data fetching logic
    return {
        sleepDuration: 8, // example data
        sleepQuality: 'bad', // example data
        
    };
}

function generateInsightsBasedOnData(sleepData) {
    var insights = [];

    // Logic to generate insights based on sleep data
    if (sleepData.sleepDuration < 7) {
        insights.push("Increasing your sleep time could improve your restfulness.");
    } else if (sleepData.sleepDuration > 8) {
        insights.push("You're getting a great amount of sleep! Keep it up.");
    }

    if (sleepData.sleepQuality === 'Good') {
        insights.push("Your sleep quality is good. Maintaining a consistent sleep schedule might help it stay that way.");
    } else {
        insights.push("Improving sleep environment and reducing screen time before bed could enhance your sleep quality.");
    }

    // Add more insights based on other data points
    return insights;
}

// Call this function within the DOMContentLoaded listener
document.addEventListener('DOMContentLoaded', function() {
    initializeSleepTrendChart();
    initializeSleepQualityChart();
    updateSleepInsights();
});

//////Part 4: Dynamic Sleep Tips

function updateSleepTips() {
    var userPreferences = getUserPreferences(); // Replace with actual user data fetching logic
    var tips = generateSleepTipsBasedOnPreferences(userPreferences);

    var tipsContainer = document.querySelector('.tips-container'); // Targeting the correct container
    tipsContainer.innerHTML = ''; // Clear existing tips

    tips.forEach(tip => {
        let tipSection = document.createElement('div');
        tipSection.className = 'tip-section mb-2';

        let tipTitle = document.createElement('button');
        tipTitle.className = 'tip-title font-bold';
        tipTitle.textContent = tip.title;
        tipSection.appendChild(tipTitle);

        let tipContent = document.createElement('div');
        tipContent.className = 'tip-content hidden p-2';
        tipContent.innerHTML = `<p>${tip.content}</p>`;
        tipSection.appendChild(tipContent);

        tipsContainer.appendChild(tipSection);
    });

    attachTipToggleListeners(); // Attach event listeners for tip toggling
}


function getUserPreferences() {
    // Placeholder for user preference data
    // Replace with actual data fetching logic
    return {
        prefersNaturalRemedies: true,
        // More preferences...
    };
}

function generateSleepTipsBasedOnPreferences(preferences) {
    var tips = [];

    // Logic to generate tips based on user preferences
    if (preferences.prefersNaturalRemedies) {
        tips.push({ title: "Natural Sleep Aids", content: "Consider natural sleep aids like chamomile tea or lavender oil." });
    }
    // Add more tips based on other preferences
    tips.push({ title: "Regular Sleep Schedule", content: "Maintain a regular sleep schedule, even on weekends." });
    tips.push({ title: "Optimize Your Sleep Environment", content: "Create a restful environment: dim lights and reduce noise before bedtime." });

    return tips;
}

function attachTipToggleListeners() {
    document.querySelectorAll('.tip-title').forEach(button => {
        button.addEventListener('click', function() {
            this.nextElementSibling.classList.toggle('hidden');
        });
    });
}

// Add this function call within the DOMContentLoaded listener
document.addEventListener('DOMContentLoaded', function() {
    initializeSleepTrendChart();
    initializeSleepQualityChart();
    updateSleepInsights();
    updateSleepTips();
});
