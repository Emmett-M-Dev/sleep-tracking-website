// Assume the sleep data is in the following format:
// const sleepData = [
//     { stage: 'WAKE', startTime: '23:00', endTime: '23:15' },
//     { stage: 'REM', startTime: '23:15', endTime: '00:45' },
//     // ... other stages
// ];

// This function converts time strings to date objects.
function parseTime(timeString) {
    const time = timeString.match(/(\d+):(\d+)/);
    return new Date(1970, 0, 1, +time[1], +time[2]);
}

// This function calculates the Y position for each stage.
function getYPosition(stage) {
    const stagesOrder = ['WAKE', 'REM', 'LIGHT', 'DEEP'];
    return stagesOrder.indexOf(stage) * (height / stagesOrder.length);
}

// Set the dimensions and margins of the graph
const margin = { top: 20, right: 20, bottom: 30, left: 50 },
      width = 900 - margin.left - margin.right,
      height = 500 - margin.top - margin.bottom;

// Append the SVG object to the body of the page
const svg = d3.select("#chart")
  .append("svg")
    .attr("width", width + margin.left + margin.right)
    .attr("height", height + margin.top + margin.bottom)
  .append("g")
    .attr("transform",
          "translate(" + margin.left + "," + margin.top + ")");

// Add X axis --> it is a date format
const x = d3.scaleTime()
  .domain([parseTime("22:00"), parseTime("07:00")]) // Assuming sleep data is between 10PM and 7AM
  .range([0, width]);

svg.append("g")
  .attr("transform", "translate(0," + height + ")")
  .call(d3.axisBottom(x));

// Add Y axis
const y = d3.scaleLinear()
  .domain([0, 3]) // Four sleep stages
  .range([height, 0]);

svg.append("g")
  .call(d3.axisLeft(y));

// Draw the line
svg.selectAll(".stage-line")
  .data(sleepData)
  .enter()
  .append("line")
    .attr("class", "stage-line")
    .attr("x1", d => x(parseTime(d.startTime)))
    .attr("y1", d => getYPosition(d.stage))
    .attr("x2", d => x(parseTime(d.endTime)))
    .attr("y2", d => getYPosition(d.stage))
    .attr("stroke", "white")
    .attr("stroke-width", 2);
