<?php
  if ( ! function_exists ( 'mysqli_fetch_all' ) )
  {
    function mysqli_fetch_all ( $db_query, $resulttype = MYSQLI_ASSOC )
    {
      $res = array();
      while ( $tmp = mysqli_fetch_array ($db_query, $resulttype) ) {
        $res[] = $tmp;
      };
      return $res;
    }
  }

  function wh_db_multi_query($query, $link = 'db_link') {
    global $$link;

    if (defined('STORE_DB_TRANSACTIONS') && (STORE_DB_TRANSACTIONS == 'true')) {
      error_log('QUERY ' . $query . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);
    }

    $result = mysqli_multi_query($$link, $query) or wh_db_error($query, mysqli_errno($$link), mysqli_error($$link));

    if (defined('STORE_DB_TRANSACTIONS') && (STORE_DB_TRANSACTIONS == 'true')) {
      $result_error = mysqli_error();
      error_log('RESULT ' . $result . ' ' . $result_error . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);
    }

    return $result;
  }

  function wh_db_fetch_array_custom($db_query) {
    if ( ! wh_db_num_rows($db_query) )
      return false;
    return mysqli_fetch_array($db_query, MYSQLI_ASSOC);
  }

  function wh_db_fetch_assoc_custom($db_query) {
    if ( ! wh_db_num_rows($db_query) )
      return false;
    return mysqli_fetch_assoc($db_query);
  }

  function wh_db_fetch_row_custom($db_query) {
    if ( ! wh_db_num_rows($db_query) )
      return false;
    return mysqli_fetch_row($db_query);
  }

  function wh_db_fetch_object_custom($db_query) {
    if ( ! wh_db_num_rows($db_query) )
      return false;
    return mysqli_fetch_object($db_query);
  }

  function wh_db_fetch_all_custom($db_query, $resulttype = MYSQLI_ASSOC) {
    if ( ! wh_db_num_rows($db_query) )
      return false;
    return mysqli_fetch_all($db_query, $resulttype);
  }

  function echoQuery ( $query )
  {
    echo $query . '<br />' . PHP_EOL;
  }

  function wh_db_post_input_check ( $arg )
  {
    return isset ( $_POST [$arg] )
      && wh_not_null ( $_POST [$arg] );
  }

  function wh_db_post_input_check_string ( $arg )
  {
    return isset ( $_POST [$arg] )
      && $_POST [$arg] != null
      && is_string  ( $_POST [$arg] )
      && strlen ( $_POST [$arg] );
  }

  function wh_db_post_input_prepare ( $arg )
  {
    return ( isset ( $_POST [$arg] ) && wh_not_null ( $_POST [$arg] ) )
      ? wh_db_prepare_input ( $_POST [$arg] ) : null;
  }

  function wh_db_post_input_string ( $arg )
  {
    return isset ( $_POST [$arg] )
      && wh_not_null ( $_POST [$arg] )
      && is_string  ( $_POST [$arg] )
      ? wh_db_prepare_input ( $_POST [$arg] ) : null;
  }

  function wh_db_post_input_prepare_array ( $arg )
  {
    return isset ( $_POST [$arg] )
      && wh_not_null ( $_POST [$arg] )
      && is_array ( $_POST [$arg] )
      ? wh_db_prepare_input ( $_POST [$arg] ) : array ();
  }

  function wh_db_get_input_check ( $arg )
  {
    return isset ( $_GET [$arg] )
      && wh_not_null ( $_GET [$arg] );
  }

  function wh_db_get_input_check_string ( $arg )
  {
    return isset ( $_GET [$arg] )
      && $_GET [$arg] != null
      && strlen ( $_GET [$arg] );
  }

  function wh_db_get_input_prepare ( $arg )
  {
    return isset ( $_GET [$arg] )
      && wh_not_null ( $_GET [$arg] )
      ? wh_db_prepare_input ( $_GET [$arg] ) : null;
  }

  function wh_db_get_input_string ( $arg )
  {
    return isset ( $_GET [$arg] )
      && wh_not_null ( $_GET [$arg] )
      && is_string  ( $_GET [$arg] )
      ? wh_db_prepare_input ( $_GET [$arg] ) : null;
  }

  function wh_db_get_input_array ( $arg )
  {
    return isset ( $_GET [$arg] )
      && wh_not_null ( $_GET [$arg] )
      && is_array ( $_GET [$arg] )
      ? wh_db_prepare_input ( $_GET [$arg] ) : array ();
  }

  /**
   * If value is null, replaces it with 'null'
   * @param arg string to test
   * @return input argument or 'null'
   */
  function wh_db_prepare_null ( $arg )
  {
    if ( wh_null ($arg) )
      return 'null';
    return $arg;
  }

  /**
   * Check the length of input text for the database limit
   * @param arg string to test
   * @param length_limit maximum length of the string
   * @param arg_name name of the input that will be shown in the error message
   * @return boolean true or false
   */
  function wh_db_limit_length ( $arg, $length_limit, $arg_name )
  {
    if ( wh_not_null ( $arg ) && strlen ( $arg ) > $length_limit )
    {
      wh_define ( 'TEXT_ERROR', '<strong style="color: #FF0000">'
                . $arg_name . ' is too long &mdash; over '
                . $length_limit . ' symbols</strong>' );
      return false;
    }
    return true;
  }

  /**
   * Replaces a value in array, using a reference to the array
   */
  function array_replace_value(&$ar, $value, $replacement)
  {
    if ( ( $key = array_search($value, $ar) ) !== false )
    {
      $ar[$key] = $replacement;
    }
  }

  function filterDays ( $days )
  {
#   var_dump ( $days );
#   die ();
    array_replace_value ( $days, 'monday', 1 );
    array_replace_value ( $days, 'tuesday', 2 );
    array_replace_value ( $days, 'wednesday', 3 );
    array_replace_value ( $days, 'thursday', 4 );
    array_replace_value ( $days, 'friday', 5 );
    array_replace_value ( $days, 'saturday', 6 );
    array_replace_value ( $days, 'sunday', 7 );
    array_replace_value ( $days, 'everyday', 8 );
    array_replace_value ( $days, 'whole week', 8 );
#   array_replace_value ( $days, 'workweek', 9 );
#   array_replace_value ( $days, 'weekend', 10 );
#   var_dump ( $days );
    $days = array_intersect ( $days, range(1, 8) );
    if ( in_array ( 8, $days ) ) {
      return false;
    }
    if ( in_array ( 1, $days ) || in_array ( 2, $days ) || in_array ( 3, $days ) ||
         in_array ( 4, $days ) || in_array ( 5, $days ) )
    {
      array_push ( $days, 9 );
    }
    if ( in_array ( 6, $days ) || in_array ( 7, $days ) )
    {
      array_push ( $days, 10 );
    }
    // Not needed - the input doesn't currently contain 9 or 10
#   if ( in_array ( 10, $days ) ) {
#     array_push ( $days, 6, 7 );
#   }
    // If all days are selected, simply return false
    // This means there is no need for days check in queries
    $real_days = array_intersect ( $days, range(1, 7) );
    if ( $real_days == range (1, 7) ) {
      return false;
    }
#   var_dump ( $days );
#   array_push ( $days, 8 );
    $days = array_unique ( $days, SORT_NUMERIC );
#   foreach ( $days as $key=>$value )
#   {
#     $days[$key] = strtolower ($value);
#   }
#   var_dump ( $days );
#   die ();
    return $days;
  }

  function getClubs ( )
  {
    return getClubsOrderByName ( );
  }

  function getClubsOrderById ( )
  {
    return wh_db_query ( 'select * from clubs order by id' );
  }

  function getClubsOrderByName ( )
  {
    return wh_db_query ( 'select * from clubs order by name' );
  }

  function getClubById ( $id )
  {
    return wh_db_query ( "select * from clubs where id = '{$id}'" );
  }

  function getClubByName ( $name )
  {
    return wh_db_query ( "select * from clubs where name = '{$name}'" );
  }

  /**
   * @param $table entity table - sports or facilities
   * @param $data array with entities - sports or facilities
   * @return query result
   */
  function getClubsBySports ( $table, $data )
  {
    if ( $table == 'sports' )
    {
      $entity_table = 'sports';
      $junction_table = 'clubosport';
      $entity_id = 'sport_id';
    }
    else if ( $table == 'facilities' )
    {
      $entity_table = 'facilities';
      $junction_table = 'club_facilities';
      $entity_id = 'facility_id';
    } else {
      wh_error ('Check your SQL queries');
    }
    $query = 'select clubs.id as id, clubs.name as name';
    $query .= ', clubs.latitude, clubs.longtitude';
    $query .= ', clubs.website, clubs.email, clubs.phone, clubs.comment';
    $query .= ' from clubs';
    $data = array_values ( $data );
    $counter = count ( $data );
    for ( $i = 0; $i < $counter; ++$i )
    {
      $query .= ", {$junction_table} as " . $junction_table . $i;
      $query .= ", {$entity_table} as " . $entity_table . $i;
    }
    $query .= ' where 1';
    for ( $i = 0; $i < $counter; ++$i )
    {
      $query .= " and clubs.id = {$junction_table}{$i}.club_id";
      $query .= " and {$entity_table}{$i}.id = {$junction_table}{$i}.{$entity_id}";
      $query .= " and {$entity_table}{$i}.name = '{$data[$i]}'";
    }
    $query .= ' group by id';
#   $query .= ' order by id';
#   echoQuery ($query);
    return wh_db_query ( $query );
  }

  /**
   * @param $table entity table - sports or facilities
   * @param $data array with entities - sports or facilities
   * @return query result
   */
  function getClubsBySportsId ( $table, $data )
  {
    if ( $table == 'sports' )
    {
      $entity_table = 'sports';
      $junction_table = 'clubosport';
      $entity_id = 'sport_id';
    }
    else if ( $table == 'facilities' )
    {
      $entity_table = 'facilities';
      $junction_table = 'club_facilities';
      $entity_id = 'facility_id';
    } else {
      wh_error ('Check your SQL queries');
    }
    $query = 'select clubs.id as id, clubs.name as name';
    $query .= ', clubs.latitude, clubs.longtitude';
    $query .= ', clubs.website, clubs.email, clubs.phone, clubs.comment';
    $query .= ' from clubs';
    $data = array_values ( $data );
    $counter = count ( $data );
    for ( $i = 0; $i < $counter; ++$i )
    {
      $query .= ", {$junction_table} as " . $junction_table . $i;
    }
    $query .= ' where 1';
    for ( $i = 0; $i < $counter; ++$i )
    {
      $query .= " and clubs.id = {$junction_table}{$i}.club_id";
      $query .= " and {$junction_table}{$i}.{$entity_id} = '{$data[$i]}'";
    }
    $query .= ' group by id';
#   $query .= ' order by id';
#   echoQuery ($query);
    return wh_db_query ( $query );
  }

  /**
   * @param $table entity table - sports or facilities
   * @param $data array with entities - sports or facilities
   * @param $days array with selected days
   * @return query result
   */
  function getClubsBySportsDays ( $table, $data, $days )
  {
    if ( $table == 'sports' )
    {
      $entity_table = 'sports';
      $junction_table = 'clubosport';
      $entity_id = 'sport_id';
    }
    else if ( $table == 'facilities' )
    {
      $entity_table = 'facilities';
      $junction_table = 'club_facilities';
      $entity_id = 'facility_id';
    } else {
      wh_error ('Check your SQL queries');
    }
    $query = 'select clubs.id as id, clubs.name as name';
    $query .= ', clubs.latitude, clubs.longtitude';
    $query .= ', clubs.website, clubs.email, clubs.phone, clubs.comment';
    $query .= ' from clubs';
    $data = array_values ( $data );
    $counter = count ( $data );
    for ( $i = 0; $i < $counter; ++$i )
    {
      $query .= ", {$junction_table} as " . $junction_table . $i;
      $query .= ", {$entity_table} as " . $entity_table . $i;
    }
    $query .= ' where 1';
    for ( $i = 0; $i < $counter; ++$i )
    {
      $query .= " and clubs.id = {$junction_table}{$i}.club_id";
      $query .= " and {$entity_table}{$i}.id = {$junction_table}{$i}.{$entity_id}";
      $query .= " and {$entity_table}{$i}.name = '{$data[$i]}'";
    }
    if ( is_array ( $days ) && count ( days ) ) {
      $days = filterDays ( $days );
    }
    if ( $days == false || ! is_array ( $days ) || count ( $days ) == 0 ) {
      $days = null;
    }
    if ( wh_not_null ( $days ) )
    {
      for ( $i = 0; $i < $counter; ++$i )
      {
        $query .= " and ({$junction_table}{$i}.day_id = 8";
        foreach ( $days as $day )
        {
          $query .= " or {$junction_table}{$i}.day_id = {$day}";
        }
        $query .= " )";
      }
    }
    $query .= ' group by id';
#   $query .= ' order by id';
#   echoQuery ($query);
    return wh_db_query ( $query );
  }

  /**
   * @param $table entity table - sports or facilities
   * @param $data array with entities - sports or facilities
   * @param $days array with selected days
   * @param $price array with prices for selected days
   * @return query result
   */
  function getClubsBySportsDaysPrice ( $table, $data, $days, $price )
  {
    if ( $table == 'sports' )
    {
      $entity_table = 'sports';
      $junction_table = 'clubosport';
      $entity_id = 'sport_id';
    }
    else if ( $table == 'facilities' )
    {
      $entity_table = 'facilities';
      $junction_table = 'club_facilities';
      $entity_id = 'facility_id';
    } else {
      wh_error ('Check your SQL queries');
    }
    $query = 'select clubs.id as id, clubs.name as name';
    $query .= ', clubs.latitude, clubs.longtitude';
    $query .= ', clubs.website, clubs.email, clubs.phone, clubs.comment';
    $query .= ' from clubs';
    $data = array_values ( $data );
    $counter = count ( $data );
    for ( $i = 0; $i < $counter; ++$i )
    {
      $query .= ", {$junction_table} as" . $junction_table . $i;
      $query .= ", {$entity_table} as" . $entity_table . $i;
    }
    $query .= ' where 1';
    for ( $i = 0; $i < $counter; ++$i )
    {
      $query .= " and clubs.id = {$junction_table}{$i}.club_id";
      $query .= " and {$entity_table}{$i}.id = {$junction_table}{$i}.{$entity_id}";
      $query .= " and {$entity_table}{$i}.name = '{$data[$i]}'";
    }
    if ( ! is_array ( $days ) || ! count ( $days ) ) {
      $days = array ( );
    }
#   if ( isset ($price) )
#   {
    for ( $i = 0; $i < $counter; ++$i )
    {
      $query .= " and (";
      $query .= " ({$junction_table}{$i}.day_id = 8";
      $query .= " and {$junction_table}{$i}.price_nonmember <= {$price})";
      foreach ( $days as $day )
      {
        $query .= " or ({$junction_table}{$i}.day_id = {$day}";
        $query .= " and {$junction_table}{$i}.price_nonmember <= {$price})";
      }
      $query .= " )";
    }
#   }
#   else
#   {
#     for ( $i = 0; $i < $counter; ++$i )
#     {
#       $query .= " and (";
#       $query .= " {$junction_table}{$i}.day_id = 8";
#       foreach ( $days as $day ) {
#         $query .= " or {$junction_table}{$i}.day_id = {$day}";
#       }
#       $query .= ' )';
#     }
#   }
    $query .= ' group by id';
#   $query .= ' order by id';
#   echoQuery ($query);
    return wh_db_query ( $query );
  }

  /**
   * @param $table entity table - sports or facilities
   * @param $data array with entities - sports or facilities
   * @param $days array with selected days
   * @param $time array with time schedules for selected days
   * @return query result
   */
  function getClubsBySportsDaysTime ( $table, $data, $days, $time )
  {
    if ( $table == 'sports' )
    {
      $entity_table = 'sports';
      $junction_table = 'clubosport';
      $entity_id = 'sport_id';
    }
    else if ( $table == 'facilities' )
    {
      $entity_table = 'facilities';
      $junction_table = 'club_facilities';
      $entity_id = 'facility_id';
    } else {
      wh_error ('Check your SQL queries');
    }
    $query = 'select clubs.id as id, clubs.name as name';
    $query .= ', clubs.latitude, clubs.longtitude';
    $query .= ', clubs.website, clubs.email, clubs.phone, clubs.comment';
    $query .= ' from clubs';
    $data = array_values ( $data );
    $counter = count ( $data );
    $time_open = $time[0];
    $time_close = $time[1];
    for ( $i = 0; $i < $counter; ++$i )
    {
      $query .= ", {$junction_table} as" . $junction_table . $i;
      $query .= ", {$entity_table} as" . $entity_table . $i;
    }
    $query .= ' where 1';
    for ( $i = 0; $i < $counter; ++$i )
    {
      $query .= " and clubs.id = {$junction_table}{$i}.club_id";
      $query .= " and {$entity_table}{$i}.id = {$junction_table}{$i}.{$entity_id}";
      $query .= " and {$entity_table}{$i}.name = '{$data[$i]}'";
    }
    if ( ! is_array ( $days ) || ! count ( $days ) ) {
      $days = array ( );
    }
    for ( $i = 0; $i < $counter; ++$i )
    {
      $query .= " and (";
      $query .= " ({$junction_table}{$i}.day_id = 8";
      $query .= " and not ( '{$time_close}' <= {$junction_table}{$i}.opening_time";
      $query .= " or {$junction_table}{$i}.closing_time <= '{$time_open}' ) )";
      foreach ( $days as $day )
      {
        $query .= " or ({$junction_table}{$i}.day_id = {$day}";
        $query .= " and not ( '{$time_close}' <= {$junction_table}{$i}.opening_time";
        $query .= " or {$junction_table}{$i}.closing_time <= '{$time_open}' ) )";
      }
      $query .= " )";
    }
    $query .= ' group by id';
#   $query .= ' order by id';
#   echoQuery ($query);
    return wh_db_query ( $query );
  }

  /**
   * @param $table entity table - sports or facilities
   * @param $data array with entities - sports or facilities
   * @param $days array with selected days
   * @param $price array with prices for selected days
   * @param $time array with time schedules for selected days
   * @return query result
   */
  function getClubsBySportsDaysPriceTime ( $table, $data, $days, $price, $time )
  {
    if ( $table == 'sports' )
    {
      $entity_table = 'sports';
      $junction_table = 'clubosport';
      $entity_id = 'sport_id';
    }
    else if ( $table == 'facilities' )
    {
      $entity_table = 'facilities';
      $junction_table = 'club_facilities';
      $entity_id = 'facility_id';
    } else {
      wh_error ('Check your SQL queries');
    }
    $query = 'select clubs.id as id, clubs.name as name';
    $query .= ', clubs.latitude, clubs.longtitude';
    $query .= ', clubs.website, clubs.email, clubs.phone, clubs.comment';
    $query .= ' from clubs';
    $data = array_values ( $data );
    $counter = count ( $data );
    $time_open = $time[0];
    $time_close = $time[1];
    for ( $i = 0; $i < $counter; ++$i )
    {
      $query .= ", {$junction_table} as" . $junction_table . $i;
      $query .= ", {$entity_table} as" . $entity_table . $i;
    }
    $query .= ' where 1';
    for ( $i = 0; $i < $counter; ++$i )
    {
      $query .= " and clubs.id = {$junction_table}{$i}.club_id";
      $query .= " and {$entity_table}{$i}.id = {$junction_table}{$i}.{$entity_id}";
      $query .= " and {$entity_table}{$i}.name = '{$data[$i]}'";
    }
    if ( ! is_array ( $days ) || ! count ( $days ) ) {
      $days = array ( );
    }
    for ( $i = 0; $i < $counter; ++$i )
    {
      $query .= " and (";
      $query .= " ({$junction_table}{$i}.day_id = 8";
      $query .= " and {$junction_table}{$i}.price_nonmember <= {$price}";
      $query .= " and not ( '{$time_close}' <= {$junction_table}{$i}.opening_time";
      $query .= " or {$junction_table}{$i}.closing_time <= '{$time_open}' ) )";
      foreach ( $days as $day )
      {
        $query .= " or ({$junction_table}{$i}.day_id = {$day}";
        $query .= " and {$junction_table}{$i}.price_nonmember <= {$price}";
        $query .= " and not ( {$time_close} <= {$junction_table}{$i}.opening_time";
        $query .= " or {$junction_table}{$i}.closing_time <= {$time_open} ) )";
      }
      $query .= " )";
    }

    $query .= ' group by id';
#   $query .= ' order by id';
#   echoQuery ($query);
    return wh_db_query ( $query );
  }

  function getSports ( )
  {
    return wh_db_query ( 'select * from sports' );
  }
  
  function getFacilities ( )
  {
    return wh_db_query ( 'select * from facilities' );
  }

  function getSportsOrderById ( )
  {
    return wh_db_query ( 'select * from sports order by id' );
  }
  
  function getFacilitiesOrderById ( )
  {
    return wh_db_query ( 'select * from facilities order by id' );
  }

  function getSportsOrderByName ( )
  {
    return wh_db_query ( 'select * from sports order by name' );
  }
  
  function getFacilitiesOrderByName ( )
  {
    return wh_db_query ( 'select * from facilities order by name' );
  }

  function getSportByName ( $sport )
  {
    return wh_db_query ( "select * from sports where name='{$sport}'" );
  }
  
  function getFacilityByName ( $facility )
  {
    return wh_db_query ( "select * from facilities where name='{$facility}'" );
  }

  function getSportsByClub ( $club )
  {
    $query = 'select sports.name from sports, clubosport ' .
      'where clubosport.club_id = ' . $club .
      ' and sports.id = clubosport.sport_id';
    $query .= ' group by sports.name';
    return wh_db_query ( $query );
  }
  
  function getFacilitiesByClub ( $club )
  {
    $query = 'select facilities.name from sports, club_facilities ' .
      'where club_facilities.club_id = ' . $club .
      ' and facilities.id = club_facilities.facility_id';
    $query .= ' group by facilities.name';
    return wh_db_query ( $query );
  }

  function getSportsByClubOrderByNameDays ( $club )
  {
    $query = 'select distinct sport_id, day_id, ' .
      'price_member, price_nonmember, opening_time, closing_time' .
      ' from sports, clubosport' .
      ' where clubosport.club_id = ' . $club .
      ' and sports.id = clubosport.sport_id';
#   $query .= ' group by sports.name, clubosport.day_id';
    $query .= ' order by sports.name, day_id';
#   echoQuery ( $query );
    return wh_db_query ( $query );
  }
  
  function getFacilitiesByClubOrderByNameDays ( $club )
  {
    $query = 'select distinct sport_id, day_id, ' .
      'price_member, price_nonmember, opening_time, closing_time' .
      ' from facilities, club_facilities' .
      ' where club_facilities.club_id = ' . $club .
      ' and facilities.id = club_facilities.sport_id';
#   $query .= ' group by facilities.name, club_facilities.day_id';
    $query .= ' order by facilities.name, day_id';
#   echoQuery ( $query );
    return wh_db_query ( $query );
  }

  /**
   * @param $table entity table - sports or facilities
   * @param $club id of the club
   * @param $entity id of entity - sport or facility
   * @param $times array with time schedules
   * @return query result
   */
  function setSportsTimeAll ( $table, $club, $entity, $times )
  {
    if ( $table == 'sports' )
    {
      $junction_table = 'clubosport';
      $entity_id = 'sport_id';
    }
    else if ( $table == 'facilities' )
    {
      $junction_table = 'club_facilities';
      $entity_id = 'facility_id';
    } else {
      wh_error ('Check your SQL queries');
    }
    $query = "update {$junction_table} set opening_time=null, closing_time=null "
           . "where club_id = {$club} and {$entity_id} = {$entity};" . PHP_EOL;
    wh_db_multi_query ( $query );
    $query = "insert into {$junction_table} ( club_id, {$entity_id}, day_id, "
           . 'opening_time, closing_time ) ';
    $query .= "values ( {$club}, {$entity}, 8, "
            . "{$times['open']}, {$times['close']} )";
    $query .= ' on duplicate key update opening_time=values(opening_time), '
            . 'closing_time=values(closing_time);' . PHP_EOL;
#   echoQuery ( $query );
    return wh_db_multi_query ( $query );
  }

  /**
   * @param $table entity table - sports or facilities
   * @param $club id of the club
   * @param $entity id of entity - sport or facility
   * @param $prices array with prices
   * @return query result
   */
  function setSportsPriceAll ( $table, $club, $entity, $prices )
  {
    if ( $table == 'sports' )
    {
      $junction_table = 'clubosport';
      $entity_id = 'sport_id';
    }
    else if ( $table == 'facilities' )
    {
      $junction_table = 'club_facilities';
      $entity_id = 'facility_id';
    } else {
      wh_error ('Check your SQL queries');
    }
    $query = "update {$junction_table} set price_member=null, price_nonmember=null "
           . "where club_id = {$club} and {$entity_id} = {$entity};" . PHP_EOL;
    wh_db_multi_query ( $query );
    $query = "insert into {$junction_table} ( club_id, {$entity_id}, day_id, "
           . 'price_member, price_nonmember ) ';
    $query .= "values ( {$club}, {$entity}, 8, "
            . "{$prices['member']}, {$prices['nonmember']} )";
    $query .= ' on duplicate key update price_member=values(price_member), '
            . 'price_nonmember=values(price_nonmember);' . PHP_EOL;
#   echoQuery ( $query );
    return wh_db_multi_query ( $query );
  }

  /**
   * @param $table entity table - sports or facilities
   * @param $club id of the club
   * @param $entity id of entity - sport or facility
   * @param $times array with time schedules
   * @param $prices array with prices
   * @return query result
   */
  function setSportsTimePriceAll ( $table, $club, $entity, $times, $prices )
  {
    if ( $table == 'sports' )
    {
      $junction_table = 'clubosport';
      $entity_id = 'sport_id';
    }
    else if ( $table == 'facilities' )
    {
      $junction_table = 'club_facilities';
      $entity_id = 'facility_id';
    } else {
      wh_error ('Check your SQL queries');
    }
    $query = "update {$junction_table} set opening_time=null, closing_time=null, "
           . 'price_member=null, price_nonmember=null '
           . "where club_id = {$club} and {$entity_id} = {$entity};" . PHP_EOL;
    wh_db_multi_query ( $query );
    $query = "insert into {$junction_table} ( club_id, {$entity_id}, day_id, "
           . 'opening_time, closing_time, price_member, price_nonmember ) ';
    $query .= "values ( {$club}, {$entity}, 8, "
            . "{$times['open']}, {$times['close']}, "
            . "{$prices['member']}, {$prices['nonmember']} )";
    $query .= ' on duplicate key update '
            . 'opening_time=values(opening_time), '
            . 'closing_time=values(closing_time), '
            . 'price_member=values(price_member), '
            . 'price_nonmember=values(price_nonmember);' . PHP_EOL;
#   echoQuery ( $query );
    return wh_db_multi_query ( $query );
  }

  /**
   * @param $table entity table - sports or facilities
   * @param $club id of the club
   * @param $entity id of entity - sport or facility
   * @param $times array with time schedules
   * @return query result
   */
  function setSportsTimeWorkweekWeekend ( $table, $club, $entity, $times )
  {
    if ( $table == 'sports' )
    {
      $junction_table = 'clubosport';
      $entity_id = 'sport_id';
    }
    else if ( $table == 'facilities' )
    {
      $junction_table = 'club_facilities';
      $entity_id = 'facility_id';
    } else {
      wh_error ('Check your SQL queries');
    }
    $query = "update {$junction_table} set opening_time=null, closing_time=null "
           . "where club_id = {$club} and {$entity_id} = {$entity};" . PHP_EOL;
    wh_db_multi_query ( $query );
    $query = "insert into {$junction_table} ( club_id, {$entity_id}, day_id, "
           . 'opening_time, closing_time ) values';
    $query .= " ( {$club}, {$entity}, 9, "
            . "{$times['workweek']['open']}, {$times['workweek']['close']} ),"
            . " ( {$club}, {$entity}, 10, "
            . "{$times['weekend']['open']}, {$times['weekend']['close']} )";
    $query .= ' on duplicate key update opening_time=values(opening_time), '
            . 'closing_time=values(closing_time);' . PHP_EOL;
#   echoQuery ( $query );
    return wh_db_multi_query ( $query );
  }

  /**
   * @param $table entity table - sports or facilities
   * @param $club id of the club
   * @param $entity id of entity - sport or facility
   * @param $prices array with prices
   * @return query result
   */
  function setSportsPriceWorkweekWeekend ( $table, $club, $entity, $prices )
  {
    if ( $table == 'sports' )
    {
      $junction_table = 'clubosport';
      $entity_id = 'sport_id';
    }
    else if ( $table == 'facilities' )
    {
      $junction_table = 'club_facilities';
      $entity_id = 'facility_id';
    } else {
      wh_error ('Check your SQL queries');
    }
    $query = "update {$junction_table} set price_member=null, price_nonmember=null "
           . "where club_id = {$club} and {$entity_id} = {$entity};" . PHP_EOL;
    wh_db_multi_query ( $query );
    $query = "insert into {$junction_table} ( club_id, {$entity_id}, day_id, "
           . 'price_member, price_nonmember ) values';
    $query .= "values ( {$club}, {$entity}, 9, {$prices['workweek']['member']}, "
            . "{$prices['workweek']['nonmember']} ),"
            . " ( {$club}, {$entity}, 10, {$prices['weekend']['member']}, "
            . "{$prices['weekend']['nonmember']} )";
    $query .= ' on duplicate key update price_member=values(price_member), '
            . 'price_nonmember=values(price_nonmember);' . PHP_EOL;
#   echoQuery ( $query );
    return wh_db_multi_query ( $query );
  }

  /**
   * @param $table entity table - sports or facilities
   * @param $club id of the club
   * @param $entity id of entity - sport or facility
   * @param $times array with time schedules
   * @param $prices array with prices
   * @return query result
   */
  function setSportsTimePriceWorkweekWeekend ( $table, $club, $entity, $times, $prices )
  {
    if ( $table == 'sports' )
    {
      $junction_table = 'clubosport';
      $entity_id = 'sport_id';
    }
    else if ( $table == 'facilities' )
    {
      $junction_table = 'club_facilities';
      $entity_id = 'facility_id';
    } else {
      wh_error ('Check your SQL queries');
    }
    $query = "update {$junction_table} set opening_time=null, closing_time=null, "
           . 'price_member=null, price_nonmember=null '
           . "where club_id = {$club} and {$entity_id} = {$entity};" . PHP_EOL;
    wh_db_multi_query ( $query );
    $query = "insert into {$junction_table} ( club_id, {$entity_id}, day_id, "
           . 'opening_time, closing_time, price_member, price_nonmember ) ';
    $query .= " ( {$club}, {$entity}, 9, "
            . "{$times['workweek']['open']}, "
            . "{$times['workweek']['close']}, "
            . "{$prices['workweek']['member']}, "
            . "{$prices['workweek']['nonmember']} ),"
            . " ( {$club}, {$entity}, 10, "
            . "{$times['weekend']['open']}, "
            . "{$times['weekend']['close']}, "
            . "{$prices['weekend']['member']}, "
            . "{$prices['weekend']['nonmember']} )";
    $query .= ' on duplicate key update '
            . 'opening_time=values(opening_time), '
            . 'closing_time=values(closing_time), '
            . 'price_member=values(price_member), '
            . 'price_nonmember=values(price_nonmember);' . PHP_EOL;
#   echoQuery ( $query );
    return wh_db_multi_query ( $query );
  }

  /**
   * @param $table entity table - sports or facilities
   * @param $club id of the club
   * @param $entity id of entity - sport or facility
   * @param $times array with time schedules
   * @return query result
   */
  function setSportsTimeSeparately ( $table, $club, $entity, $times )
  {
    if ( $table == 'sports' )
    {
      $junction_table = 'clubosport';
      $entity_id = 'sport_id';
    }
    else if ( $table == 'facilities' )
    {
      $junction_table = 'club_facilities';
      $entity_id = 'facility_id';
    } else {
      wh_error ('Check your SQL queries');
    }
    $query = "update {$junction_table} set opening_time=null, closing_time=null "
           . "where club_id = {$club} and {$entity_id} = {$entity};" . PHP_EOL;
    wh_db_multi_query ( $query );
    $query = "insert into {$junction_table} ( club_id, {$entity_id}, day_id, "
           . 'opening_time, closing_time ) values';
    for ($i = 1; $i < 8; ++$i)
    {
      $query .= " ( {$club}, {$entity}, {$i}, {$times[$i]['open']}, "
              . "{$times[$i]['close']} ),";
    }
    $query = rtrim($query, ',');
    $query .= ' on duplicate key update opening_time=values(opening_time), '
            . 'closing_time=values(closing_time)' . PHP_EOL;
#   echoQuery ( $query );
    return wh_db_multi_query ( $query );
  }

  /**
   * @param $table entity table - sports or facilities
   * @param $club id of the club
   * @param $entity id of entity - sport or facility
   * @param $prices array with prices
   * @return query result
   */
  function setSportsPriceSeparately ( $table, $club, $entity, $prices )
  {
    if ( $table == 'sports' )
    {
      $junction_table = 'clubosport';
      $entity_id = 'sport_id';
    }
    else if ( $table == 'facilities' )
    {
      $junction_table = 'club_facilities';
      $entity_id = 'facility_id';
    } else {
      wh_error ('Check your SQL queries');
    }
    $query = "update {$junction_table} set price_member=null, price_nonmember=null "
           . "where club_id = {$club} and {$entity_id} = {$entity};" . PHP_EOL;
    wh_db_multi_query ( $query );
    $query = "insert into {$junction_table} ( club_id, {$entity_id}, day_id, "
           . 'price_member, price_nonmember ) values';
    for ($i = 1; $i < 8; ++$i)
    {
      $query .= " ( {$club}, {$entity}, {$i}, {$prices[$i]['member']}, "
              . "{$prices[$i]['nonmember']} ),";
    }
    $query = rtrim($query, ',');
    $query .= ' on duplicate key update price_member=values(price_member), '
            . 'price_nonmember=values(price_nonmember)' . PHP_EOL;
#   echoQuery ( $query );
    return wh_db_multi_query ( $query );
  }

  /**
   * @param $table entity table - sports or facilities
   * @param $club id of the club
   * @param $entity id of entity - sport or facility
   * @param $times array with time schedules
   * @param $prices array with prices
   * @return query result
   */
  function setSportsTimePriceSeparately ( $table, $club, $entity, $times, $prices )
  {
    if ( $table == 'sports' )
    {
      $junction_table = 'clubosport';
      $entity_id = 'sport_id';
    }
    else if ( $table == 'facilities' )
    {
      $junction_table = 'club_facilities';
      $entity_id = 'facility_id';
    } else {
      wh_error ('Check your SQL queries');
    }
    $query = "update {$junction_table} set opening_time=null, closing_time=null, "
           . 'price_member=null, price_nonmember=null '
           . "where club_id = {$club} and {$entity_id} = {$entity};" . PHP_EOL;
    wh_db_multi_query ( $query );
    $query = "insert into {$junction_table} ( club_id, {$entity_id}, day_id, "
           . 'opening_time, closing_time, price_member, price_nonmember ) '
           . 'values';
    for ($i = 1; $i < 8; ++$i)
    {
      $query .= " ( {$club}, {$entity}, {$i}, "
              . "{$times[$i]['open']}, {$times[$i]['close']}, "
              . "{$prices[$i]['member']}, {$prices[$i]['nonmember']} ),";
    }
    $query = rtrim($query, ',');
    $query .= ' on duplicate key update '
            . 'opening_time=values(opening_time), '
            . 'closing_time=values(closing_time), '
            . 'price_member=values(price_member), '
            . 'price_nonmember=values(price_nonmember);' . PHP_EOL;
#   echoQuery ( $query );
    return wh_db_multi_query ( $query );
  }

  /**
   * Deletes empty entries from clubosport
   */
  function cleanClubosport ( $club )
  {
    $query = "delete from clubosport where club_id = {$club} "
           . 'and (opening_time is null or closing_time is null) '
           . 'and price_member is null and price_nonmember is null;' . PHP_EOL;
#   echoQuery ( $query );
    return wh_db_query ( $query );
  }
  
  /**
   * Deletes empty entries from club_facilities
   */
  function cleanClub_facilities ( $club )
  {
    $query = "delete from club_facilities where club_id = {$club} "
           . 'and (opening_time is null or closing_time is null) '
           . 'and price_member is null and price_nonmember is null;' . PHP_EOL;
#   echoQuery ( $query );
    return wh_db_query ( $query );
  }

?>
