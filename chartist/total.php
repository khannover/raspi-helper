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
  $slice = 1;
  $date = date("d.m.Y");

  if(isset($_GET['slice']))
    $slice = $_GET['slice'];
  if(isset($_GET['date']))
    $date = date("d.m.Y", strtotime($_GET['date']));
  else
    $date2 = date("Y-m-d");

echo '<a href="/chartist">Daily</a>';
/*
echo '
    <form method=GET action=index.php>
      <input type="date" name="date" value="' . (isset($_GET['date'])? $_GET['date'] : $date2) . '"><input id="slice" name="slice" type="range" min="1" max="60" value="' . $slice . '" onchange=javascript:showSlice(this.value)" oninput="javascript:showSlice(this.value)"><span id="sliceLabel">' . $slice . '</span>
      <input type="submit" value="Auswählen">
    </form>
';
*/
  $content = file("$datadir/total.dat");
  $count = 0;
    $labels = array();
    $series = array();
    echo '<div class="ct-chart" id="chart' . $count . '"></div>';
    foreach($content as $key=>$line){
      $values = explode(";", $line);
      $labels[] = "'" . substr($values[DATE],0,5) . "'";
      $series["down"][] = $values[RECEIVED];
      $series["up"][]= $values[SENT];
      $series["total"][]= $values[TOTAL];
      $currentValueTime = $labels[sizeof($labels)-1];
      $currentValueDown = $series["down"][sizeof($series["down"])-1];
      $currentValueUp = $series["up"][sizeof($series["up"])-1];
      $currentValueTotal = $series["total"][sizeof($series["total"])-1];
    }
      echo '<script>';
      $totalUp = 0;
      $totalDown = 0;
      $totalTotal = 0;
      echo 'new Chartist.Line("#chart' . $count . '", { 
             labels: [';
      for($i=0; $i < sizeof($labels); $i+=$slice){
        echo $labels[$i] . ", ";
      }
      echo ' ],  
           series: [ 
              [ ';
      for($i=0; $i < sizeof($series["down"]); $i += $slice){
        $x = $labels[$i];
        echo $series['down'][$i] . ", ";
        $totalDown += $series['down'][$i];
      }
      echo ' ],
             [';
  
      for($i=0; $i < sizeof($series["up"]); $i += $slice){
        $x = $labels[$i];
        echo $series['up'][$i] . ", ";
        $totalUp += $series['up'][$i];
      }
      echo ' ],
             [';
      for($i=0; $i < sizeof($series["total"]); $i += $slice){
        $x = $labels[$i];
        echo $series['total'][$i] . ", ";
        $totalTotal += $series['total'][$i];
      }
      echo ' ]
          ] 
      }, {
      fullWidth: true,
      chartPadding: {
        right: 40
      },
      plugins: [
        Chartist.plugins.ctPointLabels({
          textAnchor: "middle"
        }),
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
    echo "<p>( " . number_format($totalDown) . " MB &darr; ) | ( " . number_format($totalUp) . " MB &uarr; ) | ( " . number_format($totalTotal) . " MB &darr;&uarr; )</p>";
?>
  </body>
</html>
