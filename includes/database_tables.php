<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

// define the database table names used in the project
  define('TABLE_SPORTS', DB_TABLE_PREFIX . 'sports');
  define('TABLE_FACILITIES', DB_TABLE_PREFIX . 'facilities');
  define('TABLE_DAYS', DB_TABLE_PREFIX . 'days');
  define('TABLE_CLUBS_PRODUCTION', DB_TABLE_PREFIX . 'clubs');
  define('TABLE_CLUBS', TABLE_CLUBS_PRODUCTION . '_tmp');
  define('TABLE_CLUBS_OLD', TABLE_CLUBS_PRODUCTION . '_old');
  define('TABLE_CLUB_SCHEDULE_PRODUCTION', DB_TABLE_PREFIX . 'club_schedule');
  define('TABLE_CLUB_SCHEDULE', TABLE_CLUB_SCHEDULE_PRODUCTION . '_tmp');
  define('TABLE_CLUB_SCHEDULE_OLD', TABLE_CLUB_SCHEDULE_PRODUCTION . '_old');
  define('TABLE_CLUBOSPORT_PRODUCTION', DB_TABLE_PREFIX . 'clubosport');
  define('TABLE_CLUBOSPORT', TABLE_CLUBOSPORT_PRODUCTION . '_tmp');
  define('TABLE_CLUBOSPORT_OLD', TABLE_CLUBOSPORT_PRODUCTION . 'old');
  define('TABLE_CLUB_FACILITIES_PRODUCTION', DB_TABLE_PREFIX.'club_facilities');
  define('TABLE_CLUB_FACILITIES', TABLE_CLUB_FACILITIES_PRODUCTION . '_tmp');
  define('TABLE_CLUB_FACILITIES_OLD', TABLE_CLUB_FACILITIES_PRODUCTION .'_old');
?>
