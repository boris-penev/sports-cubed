<?php

// start the timer for the page parse time log
  define('PAGE_PARSE_START_TIME', microtime());

// load server configuration parameters
  require('includes/configure.php');

  if (DB_SERVER === '') {
    if (is_dir ('install')) {
      header ('Location: install/index.php');
    }
    die ('Database server not specified and install directory not found');
  }

  date_default_timezone_set (CFG_TIME_ZONE);

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

?>
