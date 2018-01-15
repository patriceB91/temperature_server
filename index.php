<html>
   <head>
      <title>Pool Temp Report</title>
      <!-- Load c3.css -->
      <link href="c3/c3.css" rel="stylesheet">
   </head>

   <body>    
<div class="container">
    <div class="row filter">
        <div class="col-sm-12">
            <select id="period">
                <option value="today" selected>Last 24h</option> 
                <option value="thisweek" >This week</option>
                <option value="thismonth">This month</option>
            </select>
            <button id="refresh" type="button">Click Me!</button>
        </div>
    </div>
    <div class="row Top-Row">
        <div class="temp1 col-sm-4">
            <div id="airtemp"  style="width: 310px; height: 250px; margin: 0; float:left;"></div>
        </div>
        <div class="temp2 col-sm-4">
            <div id="pooltemp"  style="width: 310px; height: 250px; margin: 0; float:left;"></div>
        </div>
        <div class="temp3 col-sm-4">
            <div id="c3temp"  style="width: 310px; height: 250px; margin: 0; float:left;"></div>
        </div>
    </div>
    <div class="row Graph">
        <div class="thegraph col-sm-12">
        <div id="container" style="min-width: 310px; height: 450px; margin: 0 auto"></div>
        </div>
    </div>
    <div class="row"></div>
</div>
        
   </body>
<script src="Highcharts/js/highcharts.js"></script>
<script src="Highcharts/js/highcharts-more.js"></script>
<script src="Highcharts/js/modules/exporting.js"></script>
<script src="Highcharts/js/modules/solid-gauge.js"></script>
<script src="jquery-3.2.1.js"></script>
<script src="charts.js"></script>
<!-- Load d3.js and c3.js -->
<script src="d3/d3.min.js" charset="utf-8"></script>
<script src="c3/c3.min.js"></script>
</html>