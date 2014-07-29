<?php

// start the timer for the page parse time log
  define('PAGE_PARSE_START_TIME', microtime());

// load server configuration parameters
  require('includes/configure.php');

  date_default_timezone_set (CFG_TIME_ZONE);

// set the type of request (secure or not)
  $request_type = (getenv('HTTPS') == 'on') ? 'SSL' : 'NONSSL';

// set php_self in the local scope
  $PHP_SELF = (((strlen(ini_get('cgi.fix_pathinfo')) > 0) && ((bool)ini_get('cgi.fix_pathinfo') == false)) || !isset($_SERVER['SCRIPT_NAME'])) ? basename($_SERVER['PHP_SELF']) : basename($_SERVER['SCRIPT_NAME']);

// include the list of project filenames
  require(DIR_WS_INCLUDES . 'filenames.php');

// include the list of project database tables
  require(DIR_WS_INCLUDES . 'database_tables.php');

// include the database functions
  require(DIR_WS_FUNCTIONS . 'database.php');
  require(DIR_WS_FUNCTIONS . 'local/database.php');

// make a connection to the database... now
  wh_db_connect() or die('Unable to connect to database server!');

// if gzip_compression is enabled, start to buffer the output
  if ( (GZIP_COMPRESSION == 'true') && ($ext_zlib_loaded = extension_loaded('zlib')) && !headers_sent() ) {
    if (($ini_zlib_output_compression = (int)ini_get('zlib.output_compression')) < 1) {
      ob_start('ob_gzhandler');
    } elseif (function_exists('ini_set')) {
      ini_set('zlib.output_compression_level', GZIP_LEVEL);
    }
  }

// define general functions used application-wide
  require(DIR_WS_FUNCTIONS . 'general.php');
  require(DIR_WS_FUNCTIONS . 'local/general.php');
  require(DIR_WS_FUNCTIONS . 'html_output.php');
  require(DIR_WS_FUNCTIONS . 'local/html_output.php');

// set the language
  include(DIR_WS_CLASSES . 'language.php');
  $lng = new language();

  $lng->set_language('en');

  $language = $lng->language['directory'];
  $languages_id = $lng->language['id'];

// include the language translations
  require(DIR_WS_LANGUAGES . $language . '.php');

  require(DIR_WS_CLASSES . 'wh_template.php');
  $whTemplate = new whTemplate();

?>
