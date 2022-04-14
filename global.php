<?php

date_default_timezone_set('UTC');
class Database_manager{
  private $host = "host = 2001:67c:1220:809:20c:29ff:fee9:cbd3";
  private $port = "port = 5432";
  private $db_name = "dbname = core_db";
  private $credentials = "user = core password=motorhead";
  private $connection;
  private $sensors = array();

  function Database_manager() {
    $this->connection = pg_connect("$this->host $this->port $this->db_name $this->credentials");
//      echo "Connection";
//      echo "</br>";
  }

  function get_sensors() {
    $sensors = array();
    $result = pg_query($this->connection, "SELECT * FROM iot_objects ORDER BY OBJECT_ID ASC;");
    if($result) {
      while ($row = pg_fetch_row($result)) {
        array_push($this->sensors, $row);
      }
    }

    return $this->sensors;
  }
//UPDATE table_name SET column_name1 = new_value1, column_name2 = new_value2, column_name3 = new_value3 WHERE some_column_name = existing_value;

  function save_changes($arr) {
    $indexes = array_slice($arr, 5);
    $columns = "ARRAY[" . (($indexes[0]) ? "'TIMESTAMP', " : "") . (($indexes[1]) ? "'SECONDS', " : "") . (($indexes[2]) ? "'DAY_IN_WEEK'," : "") . "'VALUE'" . "]";
    $query = "UPDATE iot_objects SET FRIENDLY_NAME = '$arr[1]', METHOD ='$arr[2]', SENSOR_TYPE ='$arr[3]', UNIT = '$arr[4]', LEARNING_VALUES = $columns, DATE_FROM = '$arr[9]' WHERE OBJECT_ID = '$arr[0]';";
    $res = pg_query($this->connection, $query);
  }

  function get_sensor_stats($object_id) {
    $stats = array();
    $query = "SELECT COUNT(STATE), SUM(STATE) FROM iot_data WHERE OBJECT_ID='{$object_id}';";
    $result = pg_query($this->connection, $query);
    if($result) {
      $rows = pg_fetch_row($result);
      array_push($stats, $rows[0] - $rows[1]);
      array_push($stats, $rows[1]);
    }

    return $stats;
  }

  function get_sensor_status($object_id) {
    $query = "SELECT VALUE, STATE, TIMESTAMP FROM iot_data WHERE OBJECT_ID='{$object_id}' ORDER BY INDEX DESC LIMIT 1;";
    $result = pg_query($this->connection, $query);
    if($result) {
      $row = pg_fetch_row($result);
    }
    return $row;
  }

  function get_sensor_history($object_id) {
    $rows = array();
    $query = "SELECT VALUE, TIMESTAMP FROM iot_data WHERE OBJECT_ID='{$object_id}' AND STATE = 1 ORDER BY INDEX DESC LIMIT 100";
    $result = pg_query($this->connection, $query);
    if($result) {
      while ($row = pg_fetch_row($result)) {
        $row[1] = date('d/m/Y H:i:s', $row[1]);
        array_push($rows, $row);
      }
    }
    return $rows;
  }
}

class Sensor{
  public $object_id;
  public $friendly_name;
  public $warnings_counter = 1;
  public $okey_counter = 1;
  public $method;
  public $state;
  public $interval;
  public $timestamp;
  public $value;
  public $history;
  public $colums;
  public $date_from;

  function Sensor($db_row){
    $this->object_id = $db_row[0];
    $this->friendly_name = $db_row[1];
    $this->method = $db_row[2];
    $this->unit = $db_row[3];
    $this->type = $db_row[4];
    // $this->columns = array_slice($db_row[5], 1, -1);
    $this->date_from = $db_row[6];
  }

  function set_stats($db_row) {
    $this->okey_counter = $db_row[0];
    $this->warnings_counter = $db_row[1];
  }

  function set_status($db_row) {
    $this->value = $db_row[0];
    $this->state = $db_row[1];
    $this->timestamp = $db_row[2];
  }

  function get_object_id() {
    return $this->object_id;
  }

  function get_sensor_name() {
    return $this->friendly_name;
  }

  function get_warnings_cnt() {
    return $this->warnings_counter;
  }

  function get_okey_cnt(){
    return $this->okey_counter;
  }

  function get_sensor_tag() {
    return str_replace(" ", "_", $this->friendly_name);
  }

  function get_sensor_status() {
    if($this->state == 0) {
      return "OK";
    }
    return "WARNING";
  }

  function get_sensor_value() {
    $magnetic = array('Closed', 'Open');
    $motion = array('Clear', 'Occupated');
    switch ($this->type) {
      case 'magnetic':
        return $magnetic[$this->value];
      case 'motion':
        return $motion[$this->value];
      default:
        return $this->value;
    }
  }

  function get_sensor_date() {
    return date('d/m/Y H:i:s', $this->timestamp);
  }

  function get_history(){
    return $this->history;
  }

  function set_history($history){
    $this->history = $history;
  }
}

function value_convertor($value, $type, $unit="") {
  $magnetic = array('Closed', 'Open');
  $motion = array('Clear', 'Occupated');
  $return_value;
  switch ($type) {
    case 'magnetic':
      $return_value = $magnetic[$this->value];
      break;
    case 'motion':
      $return_value = $motion[$this->value];
      break;
    // default:
    //   $return_value = $value;
  }
  return "{$return_value} {$unit}";
}
