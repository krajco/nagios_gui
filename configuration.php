<?php

require 'global.php';

$db_manager = new Database_manager();

if($_POST){
  $post_rows = array_chunk($_POST, 10);
  foreach ($post_rows as $variable) {
    $db_manager->save_changes($variable);
  }
}

$sensors_db = $db_manager->get_sensors();

function get_method($method) {
  $selected_method = array('', '', '', '', '');
  switch ($method) {
    case 'Polynomial regression':
      $selected_method[1] = 'selected';
      break;
    case 'Linear regression':
      $selected_method[2] = 'selected';
      break;
    case 'Isolation forest':
      $selected_method[3] = 'selected';
      break;
    case 'Gausian method':
      $selected_method[4] = 'selected';
      break;

    default:
      $selected_method[0] = 'selected';
      break;
  }
  return $selected_method;
}

function get_columns($cols_db){
  return explode(",", substr($cols_db, 1, - 1));
}

function get_type($method) {
  $selected_type = array('', '', '');
  switch ($method) {
    case 'magnetic':
      $selected_type[0] = 'selected';
      break;
    case 'motion':
      $selected_type[1] = 'selected';
      break;
    default:
      $selected_type[2] = 'selected';
      break;
  }
  return $selected_type;
}


function get_checked($cols) {
  $arr = get_columns($cols);
  $checked = array('', '', '', '');
  foreach ($arr as $col) {
    switch ($col) {
      case 'TIMESTAMP':
        $checked[0] = 'checked';
        break;
      case 'SECONDS':
        $checked[1] = 'checked';
        break;
      case 'DAY_IN_WEEK':
        $checked[2] = 'checked';
        break;
      case 'VALUE':
        $checked[3] = 'checked';
        break;
    }
  }
  return $checked;
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
    <script src=\"./share/select2.min.js\"></script>

    <link rel=\"stylesheet\" href=\"./share/bootstrap.min.css\">
    <link rel=\"stylesheet\" href=\"./share/style.css\">
    <link rel=\"stylesheet\" href=\"./share/font-awesome.min.css\">
    <link rel=\"stylesheet\" href=\"./share/select2.min.css\">
    <meta charset=\"utf-8\">
    <title></title>
  </head>
  <body>
    <div class=\"table-margin\">
      <div class=\"header\">
        <div class=\"tab nav nav-tabs\">
          <button class=\"tablinks nav-link\"><a href=\"index.php\">Sensors</a></button>
          <button class=\"tablinks nav-link active\"><a href=\"configuration.php\">Configuration</a></button>
        </div>
      </div>
      <div id=\"sensors\" class=\"tabcontent\">
    </div>
    <div id=\"configuration\" class=\"tabcontent active\" style=\"display: block\">
    <div class=\"row\">
      <div class=\"col-md\">
        <span class=\"input-group-text\">Object ID</span>
      </div>
      <div class=\"col-md-2\">
        <span class=\"input-group-text\">Friendly name</span>
      </div>
      <div class=\"col-md\">
        <span class=\"input-group-text\">Eval. method</span>
      </div>
      <div class=\"col-md-2\">
        <span class=\"input-group-text\">Sensor type</span>
      </div>
      <div class=\"col-md-1 \">
        <span class=\"input-group-text\">Unit</span>
      </div>
      <div class=\"col-md\">
        <span class=\"input-group-text\">Learning columns</span>
      </div>
      <div class=\"col-md\">
        <span class=\"input-group-text\">Date from</span>
      </div>
    </div>
    </br>
    <form action=\"configuration.php\" method=\"post\">";
      $cnt=0;
      foreach ($sensors_db as $sensor_db) {
        $checked = get_checked($sensor_db[5]);
        $method_arr = get_method($sensor_db[2]);
        $type_arr = get_type($sensor_db[4]);
        $cnt += $cnt +1;
        echo "
        <div class=\"row g-2 form-group\">

          <div class=\"col-md\">
            <input name=\"oid_{$cnt}\" type=\"text\" class=\"form-control\" value=\"{$sensor_db[0]}\" readonly>
          </div>
          <div class=\"col-md-2\">
            <input name=\"name_{$cnt}\" type=\"text\" class=\"form-control\" value=\"{$sensor_db[1]}\">
          </div>

          <div class=\"form-group col\">
            <select name=\"method_{$cnt}\" id=\"inputState\" class=\"form-control\">
              <option value=\"\" {$method_arr[0]}></option>
              <option value=\"Polynomial regression\" {$method_arr[1]}>Polynomial regression</option>
              <option value=\"Linear regression\" {$method_arr[2]}>Linear regression</option>
              <option value=\"Isolation forest\" {$method_arr[3]}>Isolation forest</option>
              <option value=\"Gausian method\" {$method_arr[4]}> Gausian method</option>
            </select>
          </div>

          <div class=\"col-md-2\">
            <select name=\"type_{$cnt}\" id=\"inputState\" class=\"form-control\">
              <option value=\"magnetic\" {$type_arr[0]}>Magnetic</option>
              <option value=\"motion\" {$type_arr[1]}>Motion</option>
              <option value=\"numeric\" {$type_arr[2]}>Numeric</option>
            </select>
          </div>

          <div class=\"col-md-1\">
            <input name=\"unit_{$cnt}\" type=\"text\" class=\"form-control\" value=\"{$sensor_db[3]}\">
          </div>


          <div class=\"col\">
            <div class=\"dropdown\">
              <button class=\"form-control dropdown-toggle\" type=\"button\" id=\"dropdownMenuButton\" data-toggle=\"dropdown\" aria-expanded=\"false\">
                Learning values
              </button>

              <div class=\"dropdown-menu\" aria-labelledby=\"dropdownMenuButton\">
                <div class=\"form-group form-check\">
                  <input name=\"timestamp_${cnt}\" type=\"hidden\" class=\"form-check-input\" id=\"timestamp_${cnt}\" value=\"0\">
                  <input name=\"timestamp_${cnt}\" type=\"checkbox\" class=\"form-check-input\" id=\"timestamp_${cnt}\" value=\"1\" {$checked[0]}>
                  <label name=\"timestamp_${cnt}\" class=\"form-check-label\" for=\"Timestamp_${cnt}\">Timestamp</label>
                </div>
                <div class=\"form-group form-check\">
                  <input name=\"seconds_${cnt}\" type=\"hidden\" class=\"form-check-input\" id=\"seconds_${cnt}\" value=\"0\">
                  <input name=\"seconds_${cnt}\" type=\"checkbox\" class=\"form-check-input\" id=\"seconds_${cnt}\" value=\"1\" {$checked[1]}>
                  <label class=\"form-check-label\" for=\"seconds_${cnt}\">Time</label>
                </div>
                <div class=\"form-group form-check\">
                  <input name=\"day_${cnt}\" type=\"hidden\" class=\"form-check-input\" id=\"day_${cnt}\" value=\"0\">
                  <input name=\"day_${cnt}\" type=\"checkbox\" class=\"form-check-input\" id=\"day_${cnt}\" value=\"1\" {$checked[2]}>
                  <label class=\"form-check-label\" for=\"day_${cnt}\">Day in week</label>
                </div>
                <div class=\"form-group form-check\">
                  <input name=\"value_${cnt}\" type=\"hidden\" class=\"form-check-input\" id=\"value_${cnt}\" value=\"0\">
                  <input name=\"value_${cnt}\" type=\"checkbox\" class=\"form-check-input\" id=\"value_${cnt}\" value=\"1\" checked disabled>
                  <label class=\"form-check-label\" for=\"state_${cnt}\">Value</label>
                </div>
              </div>
            </div>
          </div>

          <div class=\"col\">
            <input name=\"date_from_${cnt}\" class=\"form-control\" type=\"date\" id=\"date_from_${cnt}\" value=\"{$sensor_db[6]}\">
          </div>
          </div>

        ";
      }
echo "  <button class=\"btn btn-success\" type=\"submit\">Submit</button>
      </form>
    </div>
  </div>
  </div>
  <div class=\"footer\">
  </div>
</body>

<script type=\"text/javascript\" src=\"./share/chart.js\"></script>
<script type=\"text/javascript\" src=\"./share/data_plot.js\"></script>
</html>";
