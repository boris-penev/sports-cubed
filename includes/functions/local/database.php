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
#   array_replace_value ( $days, 'working', 9 );
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
//Not needed - the input doesn't currently contain 9 or 10
#   if ( in_array ( 10, $days ) ) {
#     array_push ( $days, 6, 7 );
#   }
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

  function array_replace_value(&$ar, $value, $replacement)
  {
    if ( ( $key = array_search($value, $ar) ) !== false )
    {
      $ar[$key] = $replacement;
    }
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

  function getClubsBySports ( $sports )
  {
    $query = 'select clubs.id as id, clubs.name as name';
    $query .= ', clubs.latitude, clubs.longtitude';
    $query .= ', clubs.email, clubs.phone, clubs.comment';
    $query .= ' from clubs';
    $sports = array_values ( $sports );
    $counter = count ( $sports );
    for ( $i = 0; $i < $counter; ++$i )
    {
      $query .= ', clubosport as clubosport' . $i;
      $query .= ', sports as sports' . $i;
    }
    $query .= ' where 1';
    for ( $i = 0; $i < $counter; ++$i )
    {
      $query .= " and clubs.id = clubosport{$i}.club_id";
      $query .= " and sports{$i}.id = clubosport{$i}.sport_id";
      $query .= " and sports{$i}.name = '{$sports[$i]}'";
    }
    $query .= ' group by id';
#   $query .= ' order by id';
#   echoQuery ($query);
    return wh_db_query ( $query );
  }

  function getClubsBySportsId ( $sports )
  {
    $query = 'select clubs.id as id, clubs.name as name';
    $query .= ', clubs.latitude, clubs.longtitude';
    $query .= ', clubs.email, clubs.phone, clubs.comment';
    $query .= ' from clubs';
    $sports = array_values ( $sports );
    $counter = count ( $sports );
    for ( $i = 0; $i < $counter; ++$i )
    {
      $query .= ', clubosport as clubosport' . $i;
    }
    $query .= ' where 1';
    for ( $i = 0; $i < $counter; ++$i )
    {
      $query .= " and clubs.id = clubosport{$i}.club_id";
      $query .= " and clubosport{$i}.sport_id = '{$sports[$i]}'";
    }
    $query .= ' group by id';
#   $query .= ' order by id';
#   echoQuery ($query);
    return wh_db_query ( $query );
  }

  function getClubsBySportsDays ( $sports, $days )
  {
    if ( $price == null ) {
      unset ( $price );
    }
    $query = 'select clubs.id as id, clubs.name as name';
    $query .= ', clubs.latitude, clubs.longtitude';
    $query .= ', clubs.email, clubs.phone, clubs.comment';
    $query .= ' from clubs';
    $sports = array_values ( $sports );
    $counter = count ( $sports );
    for ( $i = 0; $i < $counter; ++$i )
    {
      $query .= ', clubosport as clubosport' . $i;
      $query .= ', sports as sports' . $i;
    }
    $query .= ' where 1';
    for ( $i = 0; $i < $counter; ++$i )
    {
      $query .= " and clubs.id = clubosport{$i}.club_id";
      $query .= " and sports{$i}.id = clubosport{$i}.sport_id";
      $query .= " and sports{$i}.name = '{$sports[$i]}'";
    }
    if ( is_array ( $days ) && count ( days ) ) {
      $days = filterDays ( $days );
    }
    if ( $days == false || ! is_array ( $days ) || count ( $days ) == 0 ) {
      $days = null;
    }
    if ( wh_not_null ( $days ) && isset ( $price ) )
    {
      for ( $i = 0; $i < $counter; ++$i )
      {
        $query .= " and (clubosport{$i}.day_id = 8";
        foreach ( $days as $day )
        {
          $query .= " or clubosport{$i}.day_id = {$day}";
        }
        $query .= " )";
      }
    }
    $query .= ' group by id';
#   $query .= ' order by id';
#   echoQuery ($query);
    return wh_db_query ( $query );
  }

  function getClubsBySportsDaysPrice ( $sports, $days, $price )
  {
    $query = 'select clubs.id as id, clubs.name as name';
    $query .= ', clubs.latitude, clubs.longtitude';
    $query .= ', clubs.email, clubs.phone, clubs.comment';
    $query .= ' from clubs';
    $sports = array_values ( $sports );
    $counter = count ( $sports );
    for ( $i = 0; $i < $counter; ++$i )
    {
      $query .= ', clubosport as clubosport' . $i;
      $query .= ', sports as sports' . $i;
    }
    $query .= ' where 1';
    for ( $i = 0; $i < $counter; ++$i )
    {
      $query .= " and clubs.id = clubosport{$i}.club_id";
      $query .= " and sports{$i}.id = clubosport{$i}.sport_id";
      $query .= " and sports{$i}.name = '{$sports[$i]}'";
    }
    if ( ! is_array ( $days ) || ! count ( $days ) ) {
      $days = array ( );
    }
#   if ( isset ($price) )
#   {
    for ( $i = 0; $i < $counter; ++$i )
    {
      $query .= " and (";
      $query .= " (clubosport{$i}.day_id = 8";
      $query .= " and clubosport{$i}.price_nonmember <= {$price})";
      foreach ( $days as $day )
      {
        $query .= " or (clubosport{$i}.day_id = {$day}";
        $query .= " and clubosport{$i}.price_nonmember <= {$price})";
      }
      $query .= " )";
    }
#   }
#   else
#   {
#     for ( $i = 0; $i < $counter; ++$i )
#     {
#       $query .= " and (";
#       $query .= " clubosport{$i}.day_id = 8";
#       foreach ( $days as $day ) {
#         $query .= " or clubosport{$i}.day_id = {$day}";
#       }
#       $query .= ' )';
#     }
#   }
    $query .= ' group by id';
#   $query .= ' order by id';
#   echoQuery ($query);
    return wh_db_query ( $query );
  }

  function getClubsBySportsDaysTime ( $sports, $days, $time )
  {
    $query = 'select clubs.id as id, clubs.name as name';
    $query .= ', clubs.latitude, clubs.longtitude';
    $query .= ', clubs.email, clubs.phone, clubs.comment';
    $query .= ' from clubs';
    $sports = array_values ( $sports );
    $counter = count ( $sports );
    $time_open = $time[0];
    $time_close = $time[1];
    for ( $i = 0; $i < $counter; ++$i )
    {
      $query .= ', clubosport as clubosport' . $i;
      $query .= ', sports as sports' . $i;
    }
    $query .= ' where 1';
    for ( $i = 0; $i < $counter; ++$i )
    {
      $query .= " and clubs.id = clubosport{$i}.club_id";
      $query .= " and sports{$i}.id = clubosport{$i}.sport_id";
      $query .= " and sports{$i}.name = '{$sports[$i]}'";
    }
    if ( ! is_array ( $days ) || ! count ( $days ) ) {
      $days = array ( );
    }
    for ( $i = 0; $i < $counter; ++$i )
    {
      $query .= " and (";
      $query .= " (clubosport{$i}.day_id = 8";
      $query .= " and not ( '{$time_close}' <= clubosport{$i}.opening_time";
      $query .= " or clubosport{$i}.closing_time <= '{$time_open}' ) )";
      foreach ( $days as $day )
      {
        $query .= " or (clubosport{$i}.day_id = {$day}";
        $query .= " and not ( '{$time_close}' <= clubosport{$i}.opening_time";
        $query .= " or clubosport{$i}.closing_time <= '{$time_open}' ) )";
      }
      $query .= " )";
    }
    $query .= ' group by id';
#   $query .= ' order by id';
#   echoQuery ($query);
    return wh_db_query ( $query );
  }

  function getClubsBySportsDaysPriceTime ( $sports, $days, $price, $time )
  {
    $query = 'select clubs.id as id, clubs.name as name';
    $query .= ', clubs.latitude, clubs.longtitude';
    $query .= ', clubs.email, clubs.phone, clubs.comment';
    $query .= ' from clubs';
    $sports = array_values ( $sports );
    $counter = count ( $sports );
    $time_open = $time[0];
    $time_close = $time[1];
    for ( $i = 0; $i < $counter; ++$i )
    {
      $query .= ', clubosport as clubosport' . $i;
      $query .= ', sports as sports' . $i;
    }
    $query .= ' where 1';
    for ( $i = 0; $i < $counter; ++$i )
    {
      $query .= " and clubs.id = clubosport{$i}.club_id";
      $query .= " and sports{$i}.id = clubosport{$i}.sport_id";
      $query .= " and sports{$i}.name = '{$sports[$i]}'";
    }
    if ( ! is_array ( $days ) || ! count ( $days ) ) {
      $days = array ( );
    }
    for ( $i = 0; $i < $counter; ++$i )
    {
      $query .= " and (";
      $query .= " (clubosport{$i}.day_id = 8";
      $query .= " and clubosport{$i}.price_nonmember <= {$price}";
      $query .= " and not ( '{$time_close}' <= clubosport{$i}.opening_time";
      $query .= " or clubosport{$i}.closing_time <= '{$time_open}' ) )";
      foreach ( $days as $day )
      {
        $query .= " or (clubosport{$i}.day_id = {$day}";
        $query .= " and clubosport{$i}.price_nonmember <= {$price}";
        $query .= " and not ( {$time_close} <= clubosport{$i}.opening_time";
        $query .= " or clubosport{$i}.closing_time <= {$time_open} ) )";
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

  function getSportsOrderById ( )
  {
    return wh_db_query ( 'select * from sports order by id' );
  }

  function getSportsOrderByName ( )
  {
    return wh_db_query ( 'select * from sports order by name' );
  }

  function getSportByName ( $sport )
  {
    return wh_db_query ( "select * from sports where name='{$sport}'" );
  }

  function getSportsByClub ( $club )
  {
    $query = 'select sports.name from sports, clubosport ' .
      'where clubosport.club_id = ' . $club .
      ' and sports.id = clubosport.sport_id';
    $query .= ' group by sports.name';
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

  function setSportsTimeAll ( $club, $sport, $times )
  {
    $query = 'update clubosport set opening_time=null, closing_time=null '
           . "where club_id = {$club};" . PHP_EOL;
    $query .= 'insert into clubosport ( club_id, sport_id, day_id, '
            . 'opening_time, closing_time ) ';
    $query .= "values ( {$club}, {$sport}, 8, "
            . "{$times['open']}, {$times['close']} )";
    $query .= ' on duplicate key update opening_time=values(opening_time), '
            . 'closing_time=values(closing_time);' . PHP_EOL;
#   echoQuery ( $query );
    return ( $query == '' )
      ? wh_db_query ( $query ) : null;
  }

  function setSportsPriceAll ( $club, $sport, $prices )
  {
    $query = 'update clubosport set price_member=null, price_nonmember=null '
           . "where club_id = {$club};" . PHP_EOL;
    $query .= 'insert into clubosport ( club_id, sport_id, day_id, '
            . 'price_member, price_nonmember ) ';
    $query .= "values ( {$club}, {$sport}, 8, "
            . "{$prices['member']}, {$prices['nonmember']} )";
    $query .= ' on duplicate key update price_member=values(price_member), '
            . 'price_nonmember=values(price_nonmember);' . PHP_EOL;
#   echoQuery ( $query );
    return ( $query == '' )
      ? wh_db_query ( $query ) : null;
  }

  function setSportsTimePriceAll ( $club, $sport, $times, $prices )
  {
    $query = 'update clubosport set opening_time=null, closing_time=null, '
           . 'price_member=null, price_nonmember=null '
           . "where club_id = {$club};" . PHP_EOL;
    $query .= 'insert into clubosport ( club_id, sport_id, day_id, '
            . 'opening_time, closing_time, price_member, price_nonmember ) ';
    $query .= "values ( {$club}, {$sport}, 8, "
            . "{$times['open']}, {$times['close']}, "
            . "{$prices['member']}, {$prices['nonmember']} )";
    $query .= ' on duplicate key update '
            . 'opening_time=values(opening_time), '
            . 'closing_time=values(closing_time), '
            . 'price_member=values(price_member), '
            . 'price_nonmember=values(price_nonmember);' . PHP_EOL;
#   echoQuery ( $query );
    return ( $query == '' )
      ? wh_db_query ( $query ) : null;
  }

  function setSportsTimeWorking ( $club, $sport, $times )
  {
    $query = 'update clubosport set opening_time=null, closing_time=null '
           . "where club_id = {$club};" . PHP_EOL;
    $query .= 'insert into clubosport ( club_id, sport_id, day_id, '
            . 'opening_time, closing_time ) values';
    $query .= " ( {$club}, {$sport}, 9, "
            . "{$times['working']['open']}, {$times['working']['close']} ),"
            . " ( {$club}, {$sport}, 10, "
            . "{$times['weekend']['open']}, {$times['weekend']['close']} )";
    $query .= ' on duplicate key update opening_time=values(opening_time), '
            . 'closing_time=values(closing_time);' . PHP_EOL;
#   echoQuery ( $query );
    return ( $query == '' )
      ? wh_db_query ( $query ) : null;
  }

  function setSportsPriceWorking ( $club, $sport, $prices )
  {
    $query = 'update clubosport set price_member=null, price_nonmember=null '
           . "where club_id = {$club};" . PHP_EOL;
    $query .= 'insert into clubosport ( club_id, sport_id, day_id, '
            . 'price_member, price_nonmember ) values';
    $query .= "values ( {$club}, {$sport}, 9, {$prices['working']['member']}, "
            . "{$prices['working']['nonmember']} ),"
            . " ( {$club}, {$sport}, 10, {$prices['weekend']['member']}, "
            . "{$prices['weekend']['nonmember']} )";
    $query .= ' on duplicate key update price_member=values(price_member), '
            . 'price_nonmember=values(price_nonmember);' . PHP_EOL;
#   echoQuery ( $query );
    return ( $query == '' )
      ? wh_db_query ( $query ) : null;
  }

  function setSportsTimePriceWorking ( $club, $sport, $times, $prices )
  {
    $query = 'update clubosport set opening_time=null, closing_time=null, '
           . 'price_member=null, price_nonmember=null '
           . "where club_id = {$club};" . PHP_EOL;
    $query .= 'insert into clubosport ( club_id, sport_id, day_id, '
            . 'opening_time, closing_time, price_member, price_nonmember ) ';
    $query .= " ( {$club}, {$sport}, 9, "
            . "{$times['working']['open']}, "
            . "{$times['working']['close']}, "
            . "{$prices['working']['member']}, "
            . "{$prices['working']['nonmember']} ),"
            . " ( {$club}, {$sport}, 10, "
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
    return ( $query == '' )
      ? wh_db_query ( $query ) : null;
  }

  function setSportsTimeSeparately ( $club, $sport, $times )
  {
    $query = 'update clubosport set opening_time=null, closing_time=null '
           . "where club_id = {$club};" . PHP_EOL;
    $query .= 'insert into clubosport ( club_id, sport_id, day_id, '
            . 'opening_time, closing_time ) values';
    for ($i = 1; $i < 8; ++$i)
    {
      $query .= " ( {$club}, {$sport}, {$i}, {$times[$i]['open']}, "
              . "{$times[$i]['close']} ),";
    }
    rtrim($query, ',');
    $query .= ' on duplicate key update opening_time=values(opening_time), '
            . 'closing_time=values(closing_time)' . PHP_EOL;
#   echoQuery ( $query );
    return ( $query == '' )
      ? wh_db_query ( $query ) : null;
  }

  function setSportsPriceSeparately ( $club, $sport, $prices )
  {
    $query = 'update clubosport set price_member=null, price_nonmember=null '
           . "where club_id = {$club};" . PHP_EOL;
    $query .= 'insert into clubosport ( club_id, sport_id, day_id, '
                . 'price_member, price_nonmember ) values';
    for ($i = 1; $i < 8; ++$i)
    {
      $query .= " ( {$club}, {$sport}, {$i}, {$prices[$i]['member']}, "
              . "{$prices[$i]['nonmember']} ),";
    }
    rtrim($query, ',');
    $query .= ' on duplicate key update price_member=values(price_member), '
            . 'price_nonmember=values(price_nonmember)' . PHP_EOL;
#   echoQuery ( $query );
    return ( $query == '' )
      ? wh_db_query ( $query ) : null;
  }

  function setSportsTimePriceSeparately ( $club, $sport, $times, $prices )
  {
    $query = 'update clubosport set opening_time=null, closing_time=null, '
           . 'price_member=null, price_nonmember=null '
           . "where club_id = {$club};" . PHP_EOL;

    $query .= 'insert into clubosport ( club_id, sport_id, day_id, '
            . 'opening_time, closing_time, price_member, price_nonmember ) '
            . 'values';
    for ($i = 1; $i < 8; ++$i)
    {
      $query .= " ( {$club}, {$sport}, {$i}, "
              . "{$times[$i]['open']}, {$times[$i]['close']}, "
              . "{$prices[$i]['member']}, {$prices[$i]['nonmember']} ),";
    }
    rtrim($query, ',');
    $query .= ' on duplicate key update '
            . 'opening_time=values(opening_time), '
            . 'closing_time=values(closing_time), '
            . 'price_member=values(price_member), '
            . 'price_nonmember=values(price_nonmember);' . PHP_EOL;
#   echoQuery ( $query );
    return ( $query == '' )
      ? wh_db_query ( $query ) : null;
  }

  function cleanClubosport ( $club )
  {
    $query = "delete from clubosport where club_id = {$club} "
           . 'and (opening_time is null or closing_time is null) '
           . 'and price_member is null and price_nonmember is null;' . PHP_EOL;
#   echoQuery ( $query );
    return ( $query == '' )
      ? wh_db_query ( $query ) : null;
  }

  function buildTimesArrayAll ($times)
  {
    if ( ! wh_not_null ($times['open']) || ! wh_not_null ($times['close']) )
    {
      $times['open'] = 'null';
      $times['close'] = 'null';
    }
    return $times;
  }

  function buildPricesArrayAll ($prices)
  {
    if ( ! wh_not_null ($prices['member']) )
    {
      $prices['member'] = 'null';
    }
    if ( ! wh_not_null ($prices['nonmember']) )
    {
      $prices['nonmember'] = 'null';
    }
    return $prices;
  }

  function buildTimesArrayWorking ($times)
  {
    if ( ! is_array($times['working'])
        || ! wh_not_null ($times['working']['open'])
        || ! wh_not_null ($times['working']['close']) )
    {
      $times['working']['open'] = 'null';
      $times['working']['close'] = 'null';
    }
    if ( ! is_array($times['weekend'])
        || ! wh_not_null ($times['weekend']['open'])
        || ! wh_not_null ($times['weekend']['close']) )
    {
      $times['weekend']['open'] = 'null';
      $times['weekend']['close'] = 'null';
    }
    return $times;
  }

  function buildPricesArrayWorking ($times)
  {
    if ( ! is_array($prices['working'])
        || ! wh_not_null ($prices['working']['member']) )
    {
      $prices['working']['member'] = 'null';
    }
    if ( ! is_array($prices['working'])
        || ! wh_not_null ($prices['working']['nonmember']) )
    {
      $prices['working']['nonmember'] = 'null';
    }
    if ( ! is_array($prices['weekend'])
        || ! wh_not_null ($prices['weekend']['member']) )
    {
      $prices['weekend']['member'] = 'null';
    }
    if ( ! is_array($prices['weekend'])
        || ! wh_not_null ($prices['weekend']['nonmember']) )
    {
      $prices['weekend']['nonmember'] = 'null';
    }
    return $prices;
  }

  function buildTimesArraySeparately ($times)
  {
    for ($i = 1; $i < 8; ++$i)
    {
      if ( ! is_array($times[$i])
        || ! wh_not_null ($times[$i]['open']) || ! wh_not_null ($times[$i]['close']) )
      {
        $times[$i]['open'] = 'null';
        $times[$i]['close'] = 'null';
      }
    }
    return $times;
  }

  function buildPricesArraySeparately ($times)
  {
    for ($i = 1; $i < 8; ++$i)
    {
      if ( ! is_array($prices[$i])
          || ! wh_not_null ($prices[$i]['member']) )
      {
        $prices['member'] = 'null';
      }
      if ( ! is_array($prices[$i])
          || ! wh_not_null ($prices[$i]['nonmember']) )
      {
        $prices['nonmember'] = 'null';
      }
    }
    return $prices;
  }
?>
