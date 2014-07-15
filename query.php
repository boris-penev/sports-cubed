<?php

  header ("Access-Control-Allow-Origin: *");

# define('DB_DATABASE', 'clubs');
  //cp query.php ..; cp -r includes ..;
  require('includes/application_top.php');

# $array_input = json_decode ( wh_db_get_input_string ( 'array' ) ) or die();

  $sports = wh_db_get_input_string ( 'sports' );
  if ( wh_not_null ($sports) && ($json = json_decode ( $sports )) !== false &&
      is_array ($json) && count ($json) > 0 )
  {
    $sports = $json;
    $json = [];
  } else {
    $sports = [];
  }

  $days = wh_db_get_input_string ( 'days' );
  if ( wh_not_null ($days) && ($json = json_decode ( $days )) !== false &&
      is_array ($json) && count ($json) > 0 )
  {
    $days = $json;
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
  // TODO getClubsBySportsDaysTimePrice should only return club id's, so there
  // to be no joins with the clubs table

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
    echo json_encode ( $clubs );
  }
  else
  {
    $clubs = wh_db_fetch_all_custom ( getClubs ( ), MYSQLI_ASSOC );
    if ( $clubs === false) {
      $clubs = array ();
    }
    foreach ( $clubs as $key => $value ) {
      $clubs[$key]['sports'] = wh_db_fetch_all_custom ( getSportsByClub ( (int) $value['id'] ), MYSQLI_NUM );
    }
    echo json_encode ( $clubs );
  }

  require(DIR_WS_INCLUDES . 'application_bottom.php');

?>
