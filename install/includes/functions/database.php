<?php
/*
  Copyright (c) 2007 osCommerce
  Copyright (c) 2014 Boris Penev

  Released under the GNU General Public License
*/

  function wh_db_connect($server, $username, $password, $link = 'db_link') {
    global $$link, $db_error;

    $db_error = false;

    file_put_contents ('/var/www/html/database/log.txt', date('H:i:s', time()).
      ' connect: '.$server.$username.$password."\n", FILE_APPEND);
    if (!$server) {
      $db_error = 'No Server selected.';
      return false;
    }

    //$$link = @mysqli_connect($server, $username, $password) or $db_error = mysqli_connect_error();
    $$link = @mysql_connect($server, $username, $password) or $db_error = mysql_error();
    if ($db_error !== false) {
      file_put_contents ('/var/www/html/database/log.txt', date('H:i:s', time()).
        ' connectdb error: '.$db_error."\n", FILE_APPEND);
    } else {
      file_put_contents ('/var/www/html/database/log.txt', date('H:i:s', time()).
        ' connectdb link: '.var_export($$link, true)."\n", FILE_APPEND);
    }

    return $$link;
  }

  function wh_db_select_db($database) {
    file_put_contents ('/var/www/html/database/log.txt', date('H:i:s', time()).
      ' select: '.$database."\n", FILE_APPEND);
    return mysql_select_db($database);
  }

  function wh_db_query($query, $link = 'db_link') {
    global $$link;

    return mysql_query($query, $$link);
  }

  function wh_db_num_rows($db_query) {
    return mysql_num_rows($db_query);
  }

  /**
   * Creates initial database tables
   */
  function wh_db_install ($database)
  {
    $link = 'db_link';
    global $db_error, $$link;

    $db_error = false;

    file_put_contents ('/var/www/html/database/log.txt', date('H:i:s', time()).
      ' installdb: '.$database."\n", FILE_APPEND);
    if (!@wh_db_select_db($database)) {
      file_put_contents ('/var/www/html/database/log.txt', date('H:i:s', time()).
        ' installdb: cannot select '.$database.mysql_error($$link)."\n", FILE_APPEND);
      if (@wh_db_query('create database ' . $database)) {
        file_put_contents ('/var/www/html/database/log.txt', date('H:i:s', time()).
          ' installdb: create '.$database."\n", FILE_APPEND);
        wh_db_select_db($database);
      } else {
        file_put_contents ('/var/www/html/database/log.txt', date('H:i:s', time()).
          ' installdb: cannot create '.$database.mysql_error($$link)."\n", FILE_APPEND);
        $db_error = mysql_error($$link);
        return false;
      }
    }

    $query = 'drop table if exists ' .
                     TABLE_SPORTS .
              ', ' . TABLE_FACILITIES .
              ', ' . TABLE_DAYS .
              ', ' . TABLE_CLUB_SCHEDULE .
              ', ' . TABLE_CLUBOSPORT .
              ', ' . TABLE_CLUB_FACILITIES .
              ', ' . TABLE_CLUBS .
              ', ' . TABLE_CLUB_SCHEDULE_OLD .
              ', ' . TABLE_CLUBOSPORT_OLD .
              ', ' . TABLE_CLUB_FACILITIES_OLD .
              ', ' . TABLE_CLUBS_OLD .
              ', ' . TABLE_CLUB_SCHEDULE_PRODUCTION .
              ', ' . TABLE_CLUBOSPORT_PRODUCTION .
              ', ' . TABLE_CLUB_FACILITIES_PRODUCTION .
              ', ' . TABLE_CLUBS_PRODUCTION .
              ', ' . TABLE_SPORTS .
              ', ' . TABLE_FACILITIES .
              ', ' . TABLE_DAYS;
    wh_db_query ( $query );

    $query = 'CREATE TABLE IF NOT EXISTS t1 ( a INT NOT NULL AUTO_INCREMENT PRIMARY KEY, message CHAR(20)) ENGINE=MyISAM';
    wh_db_query ( $query );
  }
?>
