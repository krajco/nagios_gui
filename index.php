<?php
// set the default timezone to use.
require 'global.php';

$db_manager = new Database_manager();
$sensors_db = $db_manager->get_sensors();

$sensors = array();
foreach ($sensors_db as $db_row) {
  array_push($sensors, new Sensor($db_row));
}

echo "
<!DOCTYPE html>
<html lang=\"en\" dir=\"ltr\">
  <head>
    <!-- <script type=\"text/javascript\" src=\"share/Chart.min.js\"></script> -->

    <script src=\"./share/jquery.min.js\"></script>
    <script src=\"./share/bootstrap.bundle.min.js\"></script>
    <script src=\"./share/bootstrap.min.js\"></script>
    <script src=\"./share/bootstrap.js\"></script>

    <script type=\"text/javascript\" src=\"./share/chart.js\"></script>
    <script type=\"text/javascript\" src=\"./share/data_plot.js\"></script>
    <link rel=\"stylesheet\" href=\"./share/bootstrap.min.css\">
    <link rel=\"stylesheet\" href=\"./share/style.css\">
    <link rel=\"stylesheet\" href=\"./share/font-awesome.min.css\">
    <meta charset=\"utf-8\">
    <title></title>
  </head>
  <body>
    <div class=\"table-margin\">
      <div class=\"header\">
        <div class=\"tab nav nav-tabs\">
          <button class=\"tablinks nav-link active\"><a href=\"index.php\">Sensors</a></button>
          <button class=\"tablinks nav-link\"><a href=\"configuration.php\">Configuration</a></button>
        </div>
      </div>
      <div id=\"sensors\" class=\"tabcontent active\" style=\"display: block\">";
foreach ($sensors as $sensor) {
  $sensor->set_stats($db_manager->get_sensor_stats($sensor->get_object_id()));
  $sensor->set_status($db_manager->get_sensor_status($sensor->get_object_id()));
  $sensor->set_history($db_manager->get_sensor_history($sensor->get_object_id()));
  echo "<div class=\"card\">
          <div class=\"card-header warning\">
            <div class=\"state-ok\">
            <div class=\"sensor-status\">{$sensor->get_sensor_name()}</div>
            <div class=\"sensor-status\">{$sensor->get_sensor_status()}</div>
            <div class=\"sensor-status\">{$sensor->get_sensor_value()} {$sensor->unit}</div>
            <div class=\"sensor-status\">{$sensor->get_sensor_date()}</div>
            </div>
          </div>
          <div class=\"card-body\" style=\"float:left\">
          <a class=\"btn btn-info dropdown-toggle\" data-toggle=\"collapse\" href=\"#{$sensor->get_sensor_tag()}\" aria-expanded=\"false\" aria-controls=\"{$sensor->get_sensor_tag()}\" onclick=\"load_sensor('{$sensor->get_sensor_tag()}', {$sensor->get_warnings_cnt()}, {$sensor->get_okey_cnt()})\">Sensor information</a>
          <div class=\"collapse multi-collapse\" id=\"{$sensor->get_sensor_tag()}\">
          <div class=\"about-sensor\">
                <div class=\"pie-chart\">
                  <canvas class=\"pie-chart\" id=\"{$sensor->get_sensor_tag()}_pie_chart\">
                  </canvas>
                </div>
                <div class=\"history\">
                  <div class=\"tab nav nav-tabs\">
                    <button class=\"tablinks nav-link active\" onclick=\"open_history(event, \'door_warnings\')\">Warnings</button>
                  <!--  <button class=\"tablinks nav-link\" onclick=\"open_history(event, \'door_warnings\')\">Warnings</button> -->
                  </div>

                  <div id=\"door_history\" class=\"tabcontent active\" style=\"display: block\">
                    <table class=\"table table-hover\">
                      <thead>
                        <tr>
                          <th scope=\"col\">#</th><th scope=\"col\">Value</th><th scope=\"col\">Date</th>
                        </tr>
                      </thead>
                      <tbody>";
                      $rows = $sensor->get_history();
                      $index = 1;
                      foreach ($rows as $row) {
                        echo "<tr><th scope=\"row\">{$index}</th><td>{$row[0]}</td><td>{$row[1]}</td></tr>";
                        $index = $index + 1;
                      }

                echo "</tbody>
                    </table>
                  </div>

                  <div id=\"door_warnings\" class=\"tabcontent\">
                    <table class=\"table table-hover\">
                      <thead>
                        <tr><th scope=\"col\">#</th><th scope=\"col\">State</th><th scope=\"col\">Value</th><th scope=\"col\">Date</th></tr>
                      </thead>
                      <tbody>";
                echo "</tbody>
                    </table>
                  </div>
                </div>

                <div class=\"description\">
                  <ul class=\"list-group list-group-flush\">
                    <li class=\"list-group-item\"></li>
                    <li class=\"list-group-item\">Evaluation method: {$sensor->method}</li>
                    <li class=\"list-group-item\">State: active</li>
                  </ul>
                </div>
              </div>
            </div>
           </div>
          </div>
          </br>";
    }
    echo"
        </div>
       </div>
      </div>
      <div class=\"footer\">
      </div>
    </body>

  </html>";
