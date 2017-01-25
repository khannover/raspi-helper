<doctype html>
<html>
  <head>
    <link rel="stylesheet" href="chartist.css" />
    <script src="chartist.js"></script>
    <script src="chartist-plugin-pointlabels.min.js"></script>
    <script src="chartist-plugin-zoom.min.js"></script>
    <!-- <script src="chartist-plugin-zoom.min.js.map"></script> -->
    <script>
      function showSlice(value){
        document.getElementById('sliceLabel').innerHTML = value;
      }
    </script>
    <style>
        /* style the svg rect */
        .ct-zoom-rect {
          fill: rgba(200, 100, 100, 0.3);
          stroke: red;
        }
    </style>
  </head>
  <body>
<?php
  $datadir = "/home/pi/inet-usage-data/";
  define("DATE", 0);
  define("TIME", 1);
  define("RECEIVED", 4);
  define("SENT", 3);
  define("TOTAL", 2);
  $slice = 60;
  $date = date("d.m.Y");

  if(isset($_GET['slice']))
    $slice = $_GET['slice'];
  if(isset($_GET['date']))
    $date = date("d.m.Y", strtotime($_GET['date']));
  else
    $date2 = date("Y-m-d");

echo '
    <a href="/chartist/total.php">Total</a>
    <form method=GET action=index.php>
      <input type="date" name="date" value="' . (isset($_GET['date'])? $_GET['date'] : $date2) . '"><input id="slice" name="slice" type="range" min="1" max="60" value="' . $slice . '" onchange=javascript:showSlice(this.value)" oninput="javascript:showSlice(this.value)"><span id="sliceLabel">' . $slice . '</span>
      <input type="submit" value="Auswählen">
    </form>
';

  $files = scandir("$datadir", SCANDIR_SORT_DESCENDING);
  $count = 0;
  foreach($files as $file){
    $labels = array();
    $series = array();
    if( $file != "." && $file != ".." && strpos($file, $date) !== false ){
      echo '<div class="ct-chart" id="chart' . $count . '"></div>';
      $content = file("$datadir/$file");
      foreach($content as $key=>$line){
        $values = explode(";", $line);
        $labels[] = "'" . $values[TIME] . "'";
        $series["down"][] = $values[RECEIVED];
        $series["up"][]= $values[SENT];
        $series["total"][]= $values[TOTAL];
      }
      $currentValueTime = $labels[sizeof($labels)-1];
      $currentValueDown = $series["down"][sizeof($series["down"])-1];
      $currentValueUp = $series["up"][sizeof($series["up"])-1];
      $currentValueTotal = $series["total"][sizeof($series["total"])-1];

      echo "<p>" . str_replace("'","", $currentValueTime) . " : ( $currentValueDown MB &darr; ) | ( $currentValueUp MB &uarr; ) | ( $currentValueTotal MB &darr;&uarr; )</p>";
      echo '<script>';
      echo 'new Chartist.Line("#chart' . $count . '", { 
               labels: [';
      for($i=0; $i < sizeof($labels); $i+=$slice){
        echo $labels[$i] . ", ";
      }
      echo ' ],  
           series: [ 
              [ ';
      for($i=0; $i < sizeof($series["down"]); $i += $slice){
        $x = explode(":",str_replace("'","",$labels[$i]));
        $x = round(($x[0] * 60 + $x[1]) / 60, 2);
        echo '{ x: "' . $x . '", y: ' . $series['down'][$i] . "}, ";
      }
      echo ' ],
             [';

      for($i=0; $i < sizeof($series["up"]); $i += $slice){
        $x = explode(":",str_replace("'","",$labels[$i]));
        $x = round(($x[0] * 60 + $x[1]) / 60, 2);
        echo '{ x: "' . $x . '", y: ' . $series['up'][$i] . "}, ";
      }
      echo ' ],
             [';
      for($i=0; $i < sizeof($series["total"]); $i += $slice){
        $x = explode(":",str_replace("'","",$labels[$i]));
        $x = round(($x[0] * 60 + $x[1]) / 60, 2);
        echo '{ x: "' . $x . '", y: ' . $series['total'][$i] . "}, ";
      }
      echo ' ]
            ] 
      }, {
      fullWidth: true,
      chartPadding: {
        right: 40
      },
      axisX: {
        type: Chartist.AutoScaleAxis
      },  
      axisY: {
        type: Chartist.AutoScaleAxis
      },
      plugins: [
        Chartist.plugins.ctPointLabels({
          textAnchor: "middle"
        }),
        Chartist.plugins.zoom({
          onZoom: onZoom 
        })
      ],
    });
    var resetFnc;
    function onZoom(chart, reset) {
      resetFnc = reset;
    }
    var btn = document.createElement("button");
    btn.id = "reset-zoom-btn";
    btn.innerHTML = "Reset Zoom";
    btn.style.float = "right";
    btn.addEventListener("click", function() {
      console.log(resetFnc);
      resetFnc && resetFnc();
    });
    var parent = document.querySelector(".ct-chart");
    !parent.querySelector("#reset-zoom-btn") && parent.appendChild(btn);    
    ';
    echo '  </script>';
    $count++;
  }       
}
?>
  </body>
</html>
