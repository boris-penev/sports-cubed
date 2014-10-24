<?php

  header ("Access-Control-Allow-Origin: *");

# define('DB_DATABASE', 'clubs');
  //cp query.php ..; cp -r includes ..;
  require('includes/application_top.php');

  // prevent browser from parsing the page as html
  // it is useful for debugging
  // also look at the similar comment at the bottom of the file
  // echo '<!doctype html>', PHP_EOL, '<textarea>', PHP_EOL;

  function set_schedule (&$clubs)
  {
    foreach ( $clubs as &$club ) {
      $club['schedule'] = [];
      $club['schedule']['time'] = [];
      $club['schedule']['price'] = [];
      if ($club['opening_time'] === null && $club['closing_time'] === null) {
        $club['schedule']['time'] = wh_db_fetch_all_custom(
                          getClubScheduleTime((int)$club['id']), MYSQLI_ASSOC);
        if ($club['schedule']['time'] === false) {
          $club['schedule']['time'] = [];
        }
      }
      if ($club['price_member'] === null && $club['price_nonmember'] === null) {
        $club['schedule']['price'] = wh_db_fetch_all_custom(
                          getClubSchedulePrice((int)$club['id']), MYSQLI_ASSOC);
        if ($club['schedule']['price'] === false) {
          $club['schedule']['price'] = [];
        }
      }
      foreach ($club['schedule']['time'] as $sch_time) {
        if ($sch_time['opening_time'] !== null &&
            ($club['opening_time'] === null ||
              $club['opening_time'] < $sch_time['opening_time'])) {
          $club['opening_time'] = $sch_time['opening_time'];
        }
        if ($sch_time['closing_time'] !== null &&
            ($club['closing_time'] === null ||
              $club['closing_time'] > $sch_time['closing_time'])) {
          $club['closing_time'] = $sch_time['closing_time'];
        }
      }
      foreach ($club['schedule']['price'] as $sch_price) {
        if ($sch_price['price_member'] !== null &&
            ($club['price_member'] === null ||
              $club['price_member'] > $sch_price['price_member'])) {
          $club['price_member'] = $sch_price['price_member'];
        }
        if ($sch_price['price_nonmember'] !== null &&
            ($club['price_nonmember'] === null ||
              $club['price_nonmember'] > $sch_price['price_nonmember'])) {
          $club['price_nonmember'] = $sch_price['price_nonmember'];
        }
      }
      // NOTE In future we may use schedule to display complete schedule
      unset($club['schedule']);
    }
    unset($club);
  }

# $array_input = json_decode ( wh_db_get_input_string ( 'array' ) ) or die();

  $sports = wh_db_get_input_string ( 'sports' );
  if ( wh_not_null ($sports) && ($json = json_decode ( $sports )) !== false &&
      is_array ($json) && $json != [] )
  {
    $sports = $json;
    $json = [];
  } else {
    $sports = [];
  }

  $days = wh_db_get_input_string ( 'days' );
  if ( wh_not_null ($days) && ($json = json_decode ( $days )) !== false &&
      is_array ($json) && $json != [] )
  {
    $days = filterDays ($json);
    if ( $days == range (1, 10) ) {
      $days = [];
    }
  } else {
    $days = [];
  }

  $price_member = wh_db_get_input_string ( 'price_member' );
  $price_nonmember = wh_db_get_input_string ( 'price_nonmember' );
  if ( strlen ($price_nonmember) != 0 && is_numeric ( $price_nonmember ) )
  {
    $price = ['nonmember' => (float) $price_nonmember];
  }
  else if ( strlen ($price_member) != 0 && is_numeric ( $price_member ) )
  {
    $price = ['member' => (float) $price_member];
  } else {
    $price = null;
  }

  $time_open = wh_db_get_input_string ( 'time_open' );
  $time_close = wh_db_get_input_string ( 'time_close' );
  if ( strlen ($time_open) != 0 && wh_not_null ($time_open)
      && $time_open != '00:00:00' && strtotime($time_open) != false &&
      strlen ($time_close) != 0 && wh_not_null ($time_close) &&
      $time_close != '00:00:00' && strtotime($time_close) != false )
  {
    $time = ['open' => $time_open, 'close' => $time_close];
  } else {
    unset ($time_close);
    unset ($time_open);
    $time = [];
  }

  foreach ( $sports as &$value ) {
    $value = strtolower ( $value );
  }
  unset ( $value );
  foreach ( $days as &$value ) {
    $value = strtolower ( $value );
  }
  unset ( $value );

# var_dump ( $sports );
# var_dump ( $days );
# var_dump ( $time );
# var_dump ( $price );

# $sports = array_values ( $sports );
# $days = array_values ( $days );

  // TODO Fetch the sports and replace their names with their id's, so there to
  // be no joins with the sports table
  // TODO Sports loop can be made a single SQL query

  if ( $days !== [] || $time !== [] || $price !== null )
  {
    $clubs = wh_db_fetch_all_custom ( getClubsBySportsDaysTimePrice ( 'sports',
      $sports, $days, $time, $price ), MYSQLI_ASSOC );
    if ( $clubs === false) {
      $clubs = array ();
    }
    foreach ( $clubs as $key => $value ) {
      $clubs[$key]['sports'] = wh_db_fetch_all_custom (
        getSportsByClub ( (int) $value['id'] ), MYSQLI_NUM );
    }
    set_schedule($clubs);
    echo json_encode ( $clubs );
  }
  elseif ( $sports !== [] )
  {
    $clubs = wh_db_fetch_all_custom ( getClubsBySports ( 'sports', $sports ),
                                      MYSQLI_ASSOC );
    if ( $clubs === false) {
      $clubs = array ();
    }
    foreach ( $clubs as $key => $value ) {
      $clubs[$key]['sports'] = wh_db_fetch_all_custom (
            getSportsByClub ( (int) $value['id'] ), MYSQLI_NUM );
    }
    set_schedule($clubs);
    echo json_encode ( $clubs );
  }
  else
  {
    $clubs = wh_db_fetch_all_custom ( getClubs ( ), MYSQLI_ASSOC );
    if ( $clubs === false) {
      $clubs = array ();
    }
    foreach ( $clubs as $key => $value ) {
      $clubs[$key]['sports'] = wh_db_fetch_all_custom (
        getSportsByClub ( (int) $value['id'] ), MYSQLI_NUM );
    }
    set_schedule($clubs);
    echo json_encode ( $clubs );
  }

  // prevent browser from parsing the page as html
  // it is useful for debugging
  // also look at the similar comment at the top of the file
  // echo '</textarea>', PHP_EOL;

  require(DIR_WS_INCLUDES . 'application_bottom.php');

?>
