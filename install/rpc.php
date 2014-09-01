<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2008 osCommerce

  Released under the GNU General Public License
*/

  header('Cache-Control: no-cache, must-revalidate');
  header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

  require('includes/application.php');

  $dir_fs_www_root = dirname(__FILE__);
  file_put_contents ('/var/www/html/database/log.txt', date('H:i:s', time()).
      ' rpc: '.$_GET['action']."\n", FILE_APPEND);
  ini_set('display_errors', 'On');
  error_reporting (e_all);

  if (isset($_GET['action']) && !empty($_GET['action'])) {
    switch ($_GET['action']) {
      case 'dbCheck':
        file_put_contents ('/var/www/html/database/log.txt', date('H:i:s', time()).
          ' dbCheck:'."\n", FILE_APPEND);
        $db = array('DB_SERVER' => trim(rawurldecode($_GET['server'])),
                    'DB_SERVER_USERNAME' => trim(rawurldecode($_GET['username'])),
                    'DB_SERVER_PASSWORD' => trim(rawurldecode($_GET['password'])),
                    'DB_DATABASE' => trim(rawurldecode($_GET['name']))
                   );

        $db_error = false;
        wh_db_connect($db['DB_SERVER'], $db['DB_SERVER_USERNAME'], $db['DB_SERVER_PASSWORD']);

        file_put_contents ('/var/www/html/database/log.txt', date('H:i:s', time()).
          ' dbCheck error: '.$db_error."\n", FILE_APPEND);
        if ($db_error === false) {
          if (!@wh_db_select_db($db['DB_DATABASE'])) {
            $link = 'db_link';
            $db_error = mysql_error($$link);
            if ($db_error === false) {
              file_put_contents ('/var/www/html/database/log.txt', date('H:i:s', time()).
                ' dbCheck error: there is an error but we cannot identify it'."\n", FILE_APPEND);
            }
            file_put_contents ('/var/www/html/database/log.txt', date('H:i:s', time()).
              ' dbCheck error: '.$db_error."\n", FILE_APPEND);
          }
        }

        if ($db_error !== false) {
          file_put_contents ('/var/www/html/database/log.txt', date('H:i:s', time()).
            ' dbCheck error: '.$db_error."\n", FILE_APPEND);
          echo '[[0|' . $db_error . ']]';
        } else {
          file_put_contents ('/var/www/html/database/log.txt', date('H:i:s', time()).
            ' dbCheck: Success'."\n", FILE_APPEND);
          echo '[[1]]';
        }

        exit;
        break;

      case 'dbImport':
        file_put_contents ('/var/www/html/database/log.txt', date('H:i:s', time()).
          ' dbImport:'."\n", FILE_APPEND);
        $db = array('DB_SERVER' => trim(rawurldecode($_GET['server'])),
                    'DB_SERVER_USERNAME' => trim(rawurldecode($_GET['username'])),
                    'DB_SERVER_PASSWORD' => trim(rawurldecode($_GET['password'])),
                    'DB_DATABASE' => trim(rawurldecode($_GET['name'])),
                   );

        wh_db_connect($db['DB_SERVER'], $db['DB_SERVER_USERNAME'], $db['DB_SERVER_PASSWORD']);

        $db_error = false;

        wh_set_time_limit(0);
        wh_db_install($db['DB_DATABASE']);

        if ($db_error !== false) {
          file_put_contents ('/var/www/html/database/log.txt', date('H:i:s', time()).
            ' dbImport error: '.$db_error."\n", FILE_APPEND);
          echo '[[0|' . $db_error . ']]';
        } else {
          file_put_contents ('/var/www/html/database/log.txt', date('H:i:s', time()).
              ' dbImport: Success'."\n", FILE_APPEND);
          echo '[[1]]';
        }

        exit;
        break;
    }
  }

  echo '[[-100|noActionError]]';
?>
