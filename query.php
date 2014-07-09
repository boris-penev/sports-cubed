<?php

  header ("Access-Control-Allow-Origin: *");

# define('DB_DATABASE', 'clubs');
  //cp query.php ..; cp -r includes ..;
  require('includes/application_top.php');

# $array_input = json_decode ( wh_db_get_input_string ( 'array' ) ) or die();

  $sports = wh_db_get_input_string ( 'sports' );
  if ( wh_not_null ($sports) )
  {
    $json = json_decode ( $sports );
    if ( $json !== false )
    {
      $sports = $json;
    } else {
      unset ($sports);
    }
  } else {
    unset ($sports);
  }

  $days = wh_db_get_input_string ( 'days' );
  if ( wh_not_null ($days) )
  {
    $json = json_decode ( $days );
    if ( $json !== false )
    {
      $days = $json;
    } else {
      unset ($days);
    }
  } else {
    unset ($days);
  }

  $price = wh_db_get_input_string ( 'price' );
  if ( strlen ($price) != 0 && is_numeric ( $price ) )
  {
    $price = (int) $price;
  } else {
    unset ($price);
  }

  $time_open = wh_db_get_input_string ( 'time_open' );
  if ( strlen ($time_open) != 0 && wh_not_null ($time_open)
    && $time_open != '00:00:00' && strtotime($time_open) != false )
  {
    //opening time is fine
  } else {
    unset ($time_open);
  }

  $time_close = wh_db_get_input_string ( 'time_close' );
  if ( isset ($time_open) && strlen ($time_close) != 0 && wh_not_null ($time_close)
    && $time_close != '00:00:00' && strtotime($time_close) != false )
  {
    //closing time is fine
  } else {
    unset ($time_close);
    unset ($time_open);
  }

  if ( isset ($time_open) && isset ($time_close) ) {
    $time = array ($time_open, $time_close);
  }

# var_dump ( $sports );
# var_dump ( $days );
# var_dump ( $time );

# $sports = array_values ( $sports );
# $days = array_values ( $days );


  if ( isset ($sports) && isset ($price) && isset ($time) )
  {
    $clubs = wh_db_fetch_all_custom ( getClubsBySportsDaysPriceTime ( 'sports',
      $sports, $days, $price, $time ), MYSQLI_ASSOC );
    if ( $clubs === false) {
      $clubs = array ();
    }
    foreach ( $clubs as $key => $value ) {
      $clubs[$key]['sports'] = wh_db_fetch_all_custom (
        getSportsByClub ( (int) $value['id'] ), MYSQLI_NUM );
    }
#   echo var_dump ( $clubs );
    echo json_encode ( $clubs );
  }
  elseif ( isset ($sports) && isset ($price) )
  {
    $clubs = wh_db_fetch_all_custom ( getClubsBySportsDaysPrice ( 'sports',
    $sports, $days, $price ), MYSQLI_ASSOC );
    if ( $clubs === false) {
      $clubs = array ();
    }
    foreach ( $clubs as $key => $value ) {
      $clubs[$key]['sports'] = wh_db_fetch_all_custom (
        getSportsByClub ( (int) $value['id'] ), MYSQLI_NUM );
    }
#   echo var_dump ( $clubs );
    echo json_encode ( $clubs );
  }
  elseif ( isset ($sports) && isset ($time) )
  {
    $clubs = wh_db_fetch_all_custom ( getClubsBySportsDaysTime ( 'sports',
      $sports, $days, $time ), MYSQLI_ASSOC );
    if ( $clubs === false) {
      $clubs = array ();
    }
    foreach ( $clubs as $key => $value ) {
      $clubs[$key]['sports'] = wh_db_fetch_all_custom ( getSportsByClub ( (int) $value['id'] ), MYSQLI_NUM );
    }
#   echo var_dump ( $clubs );
    echo json_encode ( $clubs );
  }
  elseif ( isset ($sports) && isset ($days) )
  {
    $clubs = wh_db_fetch_all_custom ( getClubsBySportsDays ( 'sports', $sports,
            $days ), MYSQLI_ASSOC );
    if ( $clubs === false) {
      $clubs = array ();
    }
    foreach ( $clubs as $key => $value ) {
      $clubs[$key]['sports'] = wh_db_fetch_all_custom ( getSportsByClub ( (int) $value['id'] ), MYSQLI_NUM );
    }
#   echo var_dump ( $clubs );
    echo json_encode ( $clubs );
  }
  elseif ( isset ($sports) )
  {
    $clubs = wh_db_fetch_all_custom ( getClubsBySports ( 'sports', $sports ),
                                      MYSQLI_ASSOC );
#   var_dump ( $club );
    if ( $clubs === false) {
      $clubs = array ();
    }
    foreach ( $clubs as $key => $value ) {
      $clubs[$key]['sports'] = wh_db_fetch_all_custom ( getSportsByClub ( (int) $value['id'] ), MYSQLI_NUM );
    }
#   echo var_dump ( $clubs );
    echo json_encode ( $clubs );
  }
  else
  {
    $clubs = wh_db_fetch_all_custom ( getClubs ( ), MYSQLI_ASSOC );
#   var_dump ( $club );
    if ( $clubs === false) {
      $clubs = array ();
    }
    foreach ( $clubs as $key => $value ) {
      $clubs[$key]['sports'] = wh_db_fetch_all_custom ( getSportsByClub ( (int) $value['id'] ), MYSQLI_NUM );
    }
#   echo var_dump ( $clubs );
    echo json_encode ( $clubs );
  }

  require(DIR_WS_INCLUDES . 'application_bottom.php');

?>
