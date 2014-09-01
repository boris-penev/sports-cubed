<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2012 osCommerce

  Released under the GNU General Public License
*/

////
// Sets timeout for the current script.
// Cant be used in safe mode.
  function wh_set_time_limit($limit) {
    if (!get_cfg_var('safe_mode')) {
      set_time_limit($limit);
    }
  }

  function wh_realpath($directory) {
    return str_replace('\\', '/', realpath($directory));
  }

  function wh_rand($min = null, $max = null) {
    static $seeded;

    if (!isset($seeded)) {
      mt_srand((double)microtime()*1000000);
      $seeded = true;
    }

    if (isset($min) && isset($max)) {
      if ($min >= $max) {
        return $min;
      } else {
        return mt_rand($min, $max);
      }
    } else {
      return mt_rand();
    }
  }

  function wh_encrypt_string($plain) {
    $password = '';

    for ($i=0; $i<10; $i++) {
      $password .= wh_rand();
    }

    $salt = substr(md5($password), 0, 2);

    $password = md5($salt . $plain) . ':' . $salt;

    return $password;
  }

////
// Wrapper function for is_writable() for Windows compatibility
  function wh_is_writable($file) {
    if (strtolower(substr(PHP_OS, 0, 3)) === 'win') {
      if (file_exists($file)) {
        $file = realpath($file);
        if (is_dir($file)) {
          $result = @tempnam($file, 'osc');
          if (is_string($result) && file_exists($result)) {
            unlink($result);
            return (strpos($result, $file) === 0) ? true : false;
          }
        } else {
          $handle = @fopen($file, 'r+');
          if (is_resource($handle)) {
            fclose($handle);
            return true;
          }
        }
      } else{
        $dir = dirname($file);
        if (file_exists($dir) && is_dir($dir) && wh_is_writable($dir)) {
          return true;
        }
      }
      return false;
    } else {
      return is_writable($file);
    }
  }

////
// Parse the data used in the html tags to ensure the tags will not break
  function wh_parse_input_field_data($data, $parse) {
    return strtr(trim($data), $parse);
  }

  function wh_output_string($string, $translate = false, $protected = false) {
    if ($protected == true) {
      return htmlspecialchars($string);
    } else {
      if ($translate == false) {
        return wh_parse_input_field_data($string, array('"' => '&quot;'));
      } else {
        return wh_parse_input_field_data($string, $translate);
      }
    }
  }
?>
