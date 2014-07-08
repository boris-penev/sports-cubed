<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  function wh_db_connect($server = DB_SERVER, $username = DB_SERVER_USERNAME, $password = DB_SERVER_PASSWORD, $database = DB_DATABASE, $link = 'db_link') {
    global $$link;

    if (USE_PCONNECT == 'true') {
      $$link = mysqli_pconnect($server, $username, $password, $database);
    } else {
      $$link = mysqli_connect($server, $username, $password, $database);
    }

    //if ($$link) mysqli_select_db($database);

    return $$link;
  }

  function wh_db_close($link = 'db_link') {
    global $link;

    return mysqli_close($$link);
  }

  function wh_db_error($query, $errno, $error) {
    die('<font color="#ff0000"><strong>' . $errno . ' - ' . $error . '<br /><br />' . $query . '<br /><br /><small><font color="#ff0000">[WHALE STOP]</font></small><br /><br /></strong></font>');
  }

  function wh_db_query($query, $link = 'db_link') {
    global $$link;

    if (defined('STORE_DB_TRANSACTIONS') && (STORE_DB_TRANSACTIONS == 'true')) {
      error_log('QUERY ' . $query . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);
    }

    $result = mysqli_query($$link, $query) or wh_db_error($query, mysqli_errno($$link), mysqli_error($$link));

    if (defined('STORE_DB_TRANSACTIONS') && (STORE_DB_TRANSACTIONS == 'true')) {
       $result_error = mysqli_error();
       error_log('RESULT ' . $result . ' ' . $result_error . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);
    }

    return $result;
  }

  function wh_db_perform($table, $data, $action = 'insert', $parameters = '', $link = 'db_link') {
    if ($action == 'insert') {
      $query = 'insert into ' . $table . ' (';
      foreach ($data as $columns => $value) {
        $query .= $columns . ', ';
      }
      $query = substr($query, 0, -2) . ') values (';
      foreach ($data as $value) {
        switch ((string)$value) {
          case 'now()':
            $query .= 'now(), ';
            break;
          case 'null':
            $query .= 'null, ';
            break;
          default:
            $query .= '\'' . wh_db_input($value) . '\', ';
            break;
        }
      }
      $query = substr($query, 0, -2) . ')';
    } elseif ($action == 'update') {
      $query = 'update ' . $table . ' set ';
      foreach ( $data as $columns => $value ) {
        switch ((string)$value) {
          case 'now()':
            $query .= $columns . ' = now(), ';
            break;
          case 'null':
            $query .= $columns .= ' = null, ';
            break;
          default:
            $query .= $columns . ' = \'' . wh_db_input($value) . '\', ';
            break;
        }
      }
      $query = substr($query, 0, -2) . ' where ' . $parameters;
    } elseif ($action == 'insert on duplicate key update') {
      $query = 'insert into ' . $table . ' (';
      foreach ($data as $columns => $value) {
        $query .= $columns . ', ';
      }
      $query = substr($query, 0, -2) . ') values (';
      foreach ($data as $value) {
        switch ((string)$value) {
          case 'now()':
            $query .= 'now(), ';
            break;
          case 'null':
            $query .= 'null, ';
            break;
          default:
            $query .= '\'' . wh_db_input($value) . '\', ';
            break;
        }
      }
      $query = substr($query, 0, -2) . ')';
      $query .= ' on duplicate key update ';
      foreach ($data as $columns => $value) {
        $query .= $columns . ' = values (' . $columns . '), ';
      }
      $query = substr($query, 0, -2);
    }

#    echoQuery ( $query );
    return wh_db_query($query, $link);
  }

  function wh_db_fetch_array($db_query) {
    return mysqli_fetch_array($db_query, MYSQLI_ASSOC);
  }

  function wh_db_fetch_assoc($db_query) {
    return mysqli_fetch_assoc($db_query);
  }

  function wh_db_fetch_row($db_query) {
    return mysqli_fetch_row($db_query);
  }

  function wh_db_fetch_object($db_query) {
    return mysqli_fetch_object($db_query);
  }

  function wh_db_fetch_all($db_query, $resulttype = MYSQLI_ASSOC) {
    return mysqli_fetch_all($db_query, $resulttype);
  }

  function wh_db_num_rows($db_query) {
    return mysqli_num_rows($db_query);
  }

  function wh_db_data_seek($db_query, $row_number) {
    return mysqli_data_seek($db_query, $row_number);
  }

  function wh_db_insert_id($link = 'db_link') {
    global $$link;

    return mysqli_insert_id($$link);
  }

  function wh_db_free_result($db_query) {
    return mysqli_free_result($db_query);
  }

  function wh_db_fetch_fields($db_query) {
    return mysqli_fetch_field($db_query);
  }

  function wh_db_output($string) {
    return htmlspecialchars($string);
  }

  function wh_db_input($string, $link = 'db_link') {
    global $$link;

    if (function_exists('mysqli_real_escape_string')) {
      return mysqli_real_escape_string($$link, $string);
    } elseif (function_exists('mysqli_escape_string')) {
      return mysqli_escape_string($string);
    }

    return addslashes($string);
  }

  function wh_db_prepare_input($string) {
    if (is_string($string)) {
      return trim(wh_sanitize_string(stripslashes($string)));
    } elseif (is_array($string)) {
      reset($string);
      foreach ( $string as $key => $value ) {
        $string[$key] = wh_db_prepare_input($value);
      }
      return $string;
    } else {
      return $string;
    }
  }
?>
