<?php
  function editClub ( )
  {
    if ( wh_db_post_input_string ( 'action' ) != 'update' )
    {
      return;
    }

    global $club;

    $id = wh_db_post_input_string ( 'id' );
    if ( is_null ( $id ) )
    {
      return;
    }
    $club_id = $id;

    $name = wh_db_post_input_string ( 'name' );

    if ( wh_null ( $name ) )
    {
      $name = $club->name;
    }
    else if ( $club_t = wh_db_fetch_object_custom ( getClubByName ( $name ) ) )
    {
      if ( ! is_null ( $club_t ) && $club_t->id != $club_id )
      {
#       var_dump (get_object_vars($club));
#       var_dump (get_object_vars($club_t));
        wh_define ( 'TEXT_ERROR', '<strong style="color: #FF0000">There is another club with the same name</strong>' );
        return;
      }
    }

    $address = wh_db_post_input_string ( 'address' );
    $address = wh_db_prepare_null ($address);
    if ( ! wh_db_limit_length ($address, 300, 'Comment') )
      return;

    $postcode = wh_db_post_input_string( 'postcode' );
    $postcode = wh_db_prepare_null ($postcode);
    if ( ! wh_db_limit_length ($postcode, 16, 'Comment') )
      return;

    $latitude = wh_db_post_input_string ( 'latitude' );
    $latitude = wh_db_prepare_null ($latitude);

    if ( wh_not_null ($latitude) && ! is_numeric ( $latitude ) )
    {
      wh_define ( 'TEXT_ERROR', '<strong style="color: #FF0000">Latitude is not numeric</strong>' );
      return;
    }

    $longtitude = wh_db_post_input_string ( 'longtitude' );
    $longtitude = wh_db_prepare_null ($longtitude);

    if ( wh_not_null ($longtitude) && ! is_numeric ( $longtitude ) )
    {
      wh_define ( 'TEXT_ERROR', '<strong style="color: #FF0000">Longtitude is not numeric</strong>' );
      return;
    }

    $comment = wh_db_post_input_string ( 'comment' );
    $comment = wh_db_prepare_null ($comment);
    if ( ! wh_db_limit_length ($comment, 4000, 'Comment') )
      return;

    $website = wh_db_post_input_string ( 'website' );
    $website = wh_db_prepare_null ($website);
    if ( ! wh_db_limit_length ($website, 100, 'Website') )
      return;

    $email = wh_db_post_input_string ( 'email' );
    $email = wh_db_prepare_null ($email);
    if ( ! wh_db_limit_length ($email, 100, 'Email address') )
      return;

    $phone = wh_db_post_input_string ( 'phone' );
    $phone = wh_db_prepare_null ($phone);
    if ( ! wh_db_limit_length ($phone, 100, 'Contact phone number') )
      return;

    // Setting global schedules and prices for club
    // It there are individual schedules/prices, they will be active instead
    $time_open = wh_db_post_input_string ( 'time_open_global' );
    $time_open = wh_db_prepare_null ($time_open);
    //set variables to null if zeroes
    if ( $time_open == '00:00:00' ) {
      $time_open = 'null';
    }

    $time_close = wh_db_post_input_string ( 'time_close_global' );
    $time_close = wh_db_prepare_null ($time_close);
    //set variables to null if zeroes
    if ( $time_close == '00:00:00' ) {
      $time_close = 'null';
    }

    // previous to PHP 5.1.0 you would compare with -1, instead of false
    if ( wh_not_null ($time_open) &&
        (strtotime($time_open) === false || ! validate_time ($time_open)) ) {
      wh_define ( 'TEXT_ERROR',
      '<strong style="color: #FF0000">Opening time is not a valid time</strong>' );
      return;
    }

    if ( wh_not_null ($time_close) &&
        (strtotime($time_close) === false || ! validate_time ($time_close)) ) {
      wh_define ( 'TEXT_ERROR',
      '<strong style="color: #FF0000">Closing time is not a valid time</strong>' );
      return;
    }

    if ( $time_open == 'null' || $time_close == 'null' ) {
      $time_open = $time_close = 'null';
    }

    $price_member = wh_db_post_input_string ( 'price_member_global' );
    $price_member = wh_db_prepare_null ($price_member);

    if ( wh_not_null ($price_member) && ! is_numeric ( $price_member ) )
    {
      wh_define ( 'TEXT_ERROR',
      '<strong style="color: #FF0000">Members price is not numeric</strong>' );
      return;
    }

    $price_nonmember = wh_db_post_input_string ( 'price_nonmember_global' );
    $price_nonmember = wh_db_prepare_null ($price_nonmember);

    if ( wh_not_null ($price_nonmember) && ! is_numeric ( $price_nonmember ) )
    {
      wh_define ( 'TEXT_ERROR',
      '<strong style="color: #FF0000">Non members price is not numeric</strong>' );
      return;
    }

    // as of PHP 5.4
    $data = [
      "name" => $name,
      "address" => $address,
      "postcode" => $postcode,
      "latitude" => $latitude,
      "longtitude" => $longtitude,
      "website" => $website,
      "email" => $email,
      "phone" => $phone,
      "comment" => $comment,
      "opening_time" => $time_open,
      "closing_time" => $time_close,
      "price_member" => $price_member,
      "price_nonmember" => $price_nonmember
    ];

    wh_db_perform ( 'clubs', $data, 'update', "id = '{$club_id}'"  );
    unset ( $data );

    $sports_query = getSports ();

    while ( $sport = wh_db_fetch_object_custom($sports_query) )
    {
      $sport_id = $sport->id;
      $select_days_view_time =
          wh_db_post_input_string ( "selectDaysViewTime{$sport_id}" );
      $select_days_view_price =
          wh_db_post_input_string ( "selectDaysViewPrice{$sport_id}" );
      $times = array();
      $prices = array();

      // Build times array
      if ( $select_days_view_time == 'all' )
      {
        $times['open'] = wh_get_time ( wh_db_post_input_string (
              "timeOpenAll{$sport_id}" ) );
        $times['close'] = wh_get_time ( wh_db_post_input_string (
              "timeCloseAll{$sport_id}" ) );

        // flag indicating whether the array is empty
        $empty = false;
        //set variables to null if zeroes
        foreach ($times as $time) {
          if ( $time === null ) {
            $times = [ 'open' => 'null', 'close' => 'null' ];
            $empty = true;
            break;
          }
        }
        $times_t = [];
        $times_t [8] = $times;
        $times = $times_t;
        unset ($times_t);
        if ($empty) {
          $select_days_view_time = '';
        }
      }
      elseif ( $select_days_view_time == 'workweekweekend' )
      {
        $times['workweek']['open'] = wh_get_time (
            wh_db_post_input_string ( "timeOpenWorkweek1{$sport_id}" ) );
        $times['workweek']['close'] = wh_get_time (
            wh_db_post_input_string ( "timeCloseWorkweek1{$sport_id}" ) );
        $times['weekend']['open'] = wh_get_time (
            wh_db_post_input_string ( "timeOpenWeekend{$sport_id}" ) );
        $times['weekend']['close'] = wh_get_time (
            wh_db_post_input_string ( "timeCloseWeekend{$sport_id}" ) );

        // flag indicating whether the array is empty
        $empty = true;
        //set variables to null if zeroes
        foreach ($times as &$times_t) {
          foreach ($times_t as $time) {
            if ( $time === null ) {
              $times_t = [ 'open' => 'null', 'close' => 'null' ];
              break;
            }
          }
          if ( $times_t ['open'] !== 'null' ) {
            $empty = false;
          }
        }
        unset($times_t);
        $times_t = [];
        $times_t [9]  = $times['workweek'];
        $times_t [10] = $times['weekend'];
        $times = $times_t;
        unset ($times_t);
        if ($empty) {
          $select_days_view_time = '';
        }
      }
      elseif ( $select_days_view_time == 'workweeksatsun' )
      {
        $times['workweek']['open'] = wh_get_time (
            wh_db_post_input_string ( "timeOpenWorkweek2{$sport_id}" ) );
        $times['workweek']['close'] = wh_get_time (
            wh_db_post_input_string ( "timeCloseWorkweek2{$sport_id}" ) );
        $times['saturday']['open'] = wh_get_time (
            wh_db_post_input_string ( "timeOpenSat{$sport_id}" ) );
        $times['saturday']['close'] = wh_get_time (
            wh_db_post_input_string ( "timeCloseSat{$sport_id}" ) );
        $times['sunday']['open'] = wh_get_time (
            wh_db_post_input_string ( "timeOpenSun{$sport_id}" ) );
        $times['sunday']['close'] = wh_get_time (
            wh_db_post_input_string ( "timeCloseSun{$sport_id}" ) );

        // flag indicating whether the array is empty
        $empty = true;
        //set variables to null if zeroes
        foreach ($times as &$times_t) {
          foreach ($times_t as $time) {
            if ( $time === null ) {
              $times_t = [ 'open' => 'null', 'close' => 'null' ];
              break;
            }
          }
          if ( $times_t ['open'] !== 'null' ) {
            $empty = false;
          }
        }
        unset($times_t);
        $times_t = [];
        $times_t [6] = $times['saturday'];
        $times_t [7] = $times['sunday'];
        $times_t [9] = $times['workweek'];
        $times = $times_t;
        unset ($times_t);
        if ($empty) {
          $select_days_view_time = '';
        }
      }
      elseif ( $select_days_view_time == 'separately' )
      {
        // flag indicating whether the array is empty
        $empty = true;
        for ($i = 1; $i < 8; ++$i)
        {
          $times[$i]['open'] = wh_get_time (
              wh_db_post_input_string ( "timeOpenDay{$i}_{$sport_id}" ) );
          $times[$i]['close'] = wh_get_time (
              wh_db_post_input_string ( "timeCloseDay{$i}_{$sport_id}" ) );

          //set variables to null if zeroes
          foreach ($times[$i] as $time) {
            if ( $time === null ) {
              $times[$i] = [ 'open' => 'null', 'close' => 'null' ];
              break;
            }
          }
          if ( $times[$i]['open'] !== 'null' ) {
            $empty = false;
          }
        }
      }
      // Build prices array
      if ( $select_days_view_price == 'all' )
      {
        $prices['member'] =
          wh_db_post_input_string ( "priceMemberAll{$sport_id}" );
        $prices['nonmember'] =
          wh_db_post_input_string ( "priceNonmemberAll{$sport_id}" );

        // flag indicating whether the array is empty
        $empty = true;
        //set variables to null if zeroes
        foreach ($prices as &$price) {
          if ( (float) $price == 0.0 ) {
            $price = 'null';
          } else {
            $empty = false;
          }
        }
        unset($price);
        $prices_t = [];
        $prices_t [8] = $prices;
        $prices = $prices_t;
        unset ($prices_t);
        if ($empty) {
          $select_days_view_price = '';
        }
      }
      elseif ( $select_days_view_price == 'workweekweekend' )
      {
        $prices['workweek']['member'] =
          wh_db_post_input_string ( "priceMemberWorkweek1{$sport_id}" );
        $prices['workweek']['nonmember'] =
          wh_db_post_input_string ( "priceNonmemberWorkweek1{$sport_id}" );
        $prices['weekend']['member'] =
          wh_db_post_input_string ( "priceMemberWeekend{$sport_id}" );
        $prices['weekend']['nonmember'] =
          wh_db_post_input_string ( "priceNonmemberWeekend{$sport_id}" );

        // flag indicating whether the array is empty
        $empty = true;
        //set variables to null if zeroes
        foreach ($prices as &$prices_t) {
          foreach ($prices_t as &$price) {
            if ( (float) $price == 0.0 ) {
              $price = 'null';
            } else {
              $empty = false;
            }
          }
          unset($price);
        }
        unset($prices_t);
        $prices_t = [];
        $prices_t [9]  = $prices['workweek'];
        $prices_t [10] = $prices['weekend'];
        $prices = $prices_t;
        unset ($prices_t);
        if ($empty) {
          $select_days_view_price = '';
        }
      }
      elseif ( $select_days_view_price == 'workweeksatsun' )
      {
        $prices['workweek']['member'] =
          wh_db_post_input_string ( "priceMemberWorkweek2{$sport_id}" );
        $prices['workweek']['nonmember'] =
          wh_db_post_input_string ( "priceNonmemberWorkweek2{$sport_id}" );
        $prices['saturday']['member'] =
          wh_db_post_input_string ( "priceMemberSat{$sport_id}" );
        $prices['saturday']['nonmember'] =
          wh_db_post_input_string ( "priceNonmemberSat{$sport_id}" );
        $prices['sunday']['member'] =
          wh_db_post_input_string ( "priceMemberSun{$sport_id}" );
        $prices['sunday']['nonmember'] =
          wh_db_post_input_string ( "priceNonmemberSun{$sport_id}" );

        // flag indicating whether the array is empty
        $empty = true;
        //set variables to null if zeroes
        foreach ($prices as &$prices_t) {
          foreach ($prices_t as &$price) {
            if ( (float) $price == 0.0 ) {
              $price = 'null';
            } else {
              $empty = false;
            }
          }
          unset($price);
        }
        unset($prices_t);
        $prices_t = [];
        $prices_t [6] = $prices['saturday'];
        $prices_t [7] = $prices['sunday'];
        $prices_t [9] = $prices['workweek'];
        $prices = $prices_t;
        unset ($prices_t);
        if ($empty) {
          $select_days_view_price = '';
        }
      }
      elseif ( $select_days_view_price == 'separately' )
      {
        // flag indicating whether the array is empty
        $empty = true;
        for ($i = 1; $i < 8; ++$i)
        {
          $prices[$i]['member'] =
            wh_db_post_input_string ( "priceMemberDay{$i}_{$sport_id}" );
          $prices[$i]['nonmember'] =
            wh_db_post_input_string ( "priceNonmemberDay{$i}_{$sport_id}" );

          //set variables to null if zeroes
          foreach ($prices[$i] as &$price) {
            if ( (float) $price == 0.0 ) {
              $price = 'null';
            } else {
              $empty = false;
            }
          }
          unset($price);
        }
        if ($empty) {
          $select_days_view_price = '';
        }
      }

//       These functions are not ready to operate
//       The format for times and prices for this function and
//       wh_determine_best_view are different
//       $prices = array_fill ( 1 , 7, ['member' => '', 'nonmember' => ''] );
//       $times = array_fill ( 1 , 7, ['open' => '', 'close' => ''] );
//       $days_type = '';
//       $days_type_price = '';
//       $days_type_time = '';
//       wh_determine_best_view_prices ();
//       wh_determine_best_view_times ();
//       $select_days_view_time = $days_type_time;
//       $select_days_view_price = $days_type_price;

      // TODO Smart selection of most compact representation of data
      // Delete times and prices
      deleteSportsTimePrice ( 'sports', $club_id, $sport_id );
      // Set times and prices when both schedules identical
      $select_days_view = ($select_days_view_time === $select_days_view_price);
      if ( $select_days_view ) {
        if ( $select_days_view_time !== '' ) {
          setSportsTimePrice ( 'sports', $club_id, $sport_id, $times, $prices );
        }
      } else {
        // Set times
        if ( $select_days_view_time !== '' ) {
          setSportsTime ( 'sports', $club_id, $sport_id, $times );
        }
        // Set prices
        if ( $select_days_view_price !== '' ) {
          setSportsPrice ( 'sports', $club_id, $sport_id, $prices );
        }
      }
    } // while $sport = wh_db_fetch...

    // Delete empty entries from clubosport
    cleanClubosport ($club_id);
    cleanClub_facilities ($club_id);


/*
    $bash_sum = 0.0;
    $bash_max = 0.0;
    for ( $i = 0; $i < 100; ++$i )
    {
      $starttime = microtime(true);
      exec ('echo `date +%s`  > /var/www/html/database/newest.txt');
      $endtime = microtime(true);
      $t = $endtime - $starttime;
      if ( $t > $bash_max )
        $bash_max = $t;
      $bash_sum += $t;
    }

    $php_sum = 0.0;
    $php_max = 0.0;
    for ( $i = 0; $i < 100; ++$i )
    {
      $starttime = microtime(true);
      file_put_contents ( '/var/www/html/database/newest.txt', time () );
      $endtime = microtime(true);
      $t = $endtime - $starttime;
      if ( $t > $php_max )
        $php_max = $t;
      $php_sum += $t;
    }

    echo 'Average bash = ', $bash_sum / 100.0, ' <br />', PHP_EOL;
    echo 'Average php = ', $php_sum / 100.0, ' <br />', PHP_EOL;
    echo 'Max bash = ', $bash_max, ' <br />', PHP_EOL;
    echo 'Max php = ', $php_max, ' <br />', PHP_EOL;
*/

    file_put_contents ( '/var/www/html/database/newest.txt', time () . PHP_EOL );
    // write the Unix timestamp to newest.txt

    wh_define ( 'TEXT_SUCCESS', '<strong style="color: #00FF00">'.
        'Successfully edited a club</strong>' );


  }

  function selectClub ( )
  {
/*    if ( wh_db_post_input_string ( 'action' ) == 'update'
      && wh_not_null ( $club ) && wh_not_null( $club->name ) )
    {
      return;
    }
*/
    $action = wh_db_post_input_string ( 'action' );
    $club_search = wh_db_get_input_string ( 'club_search' );
    $sport_search = wh_db_get_input_string ( 'sport_search' );
    $id = wh_db_post_input_string ( 'id' );

// Return the actual query, not the last query - no use for it now
#   $url_query = parse_url ( $_SERVER['REQUEST_URI'], PHP_URL_QUERY );
#   parse_str ( $url_query, $get_vars );
#   var_dump ( $club_search );
#   var_dump ( $sport_search );
#   var_dump ( $get_vars['club_search'] );
#   var_dump ( $get_vars['sport_search'] );
#   var_dump ( $url_query );
#   die ();

// Should check what club and sport were selected previously

    if ( wh_not_null ( $club_search ) && wh_not_null ( $sport_search ) &&
      $club_search != 0 && $sport_search != 0 && wh_null ( $action ) )
    {
#     if ( wh_not_null ( $get_vars['sport_search'] ) && $get_vars['sport_search'] != 0 ) {
#       header ( 'Location: edit.php?club_search=' . $club_search );
#     }
#     if ( wh_not_null ( $get_vars['club_search'] ) && $get_vars['club_search'] != 0 ) {
#       header ( 'Location: edit.php?sport_search=' . $sport_search );
#     }
      header ( 'Location: edit.php?club_search=' . $club_search );
    }

    if ( wh_not_null ( $club_search ) && wh_not_null ( $sport_search ) &&
      $club_search != 0 && $sport_search == 0 && wh_null ( $action ) )
    {
      header ( 'Location: edit.php?club_search=' . $club_search );
    }

    if ( wh_not_null ( $club_search ) && wh_not_null ( $sport_search ) &&
      $club_search == 0 && $sport_search != 0 && wh_null ( $action ) )
    {
      header ( 'Location: edit.php?sport_search=' . $sport_search );
    }

    if ( wh_not_null ( $club_search ) && wh_not_null ( $sport_search ) &&
      $club_search == 0 && $sport_search == 0 && wh_null ( $action ) )
    {
      header ( 'Location: edit.php' );
    }

    if ( $action == 'update' ||
         ( wh_not_null ($club_search) && $club_search != '0' ) )
    {
      global $club;
      $club = (object) array ();
#     var_dump ( get_object_vars ($club) );
#     var_dump ($id);
      if ( $action != 'update' && wh_not_null($club_search) ) {
        $id = $club_search;
      };
#     $id = wh_not_null($club_search) ?
#       $club_search : wh_not_null($id) ?
#         $id : null;
#     var_dump ($club_search);
#     var_dump (wh_not_null($club_search));
#     var_dump ($id);
      if ( $id == 'null' || $id == '0' ) {
        return;
      }
      if ( ! isset ($_GET['club_search'] ) ) {
        header ( 'Location: edit.php?club_search=' . $id . '&success' );
      }
      if ( $row_obj = wh_db_fetch_object_custom ( getClubById ( $id ) ) )
      {
        $club -> id = wh_db_output ( $row_obj->id );
        $club -> name = wh_db_output ( $row_obj->name );
        $club -> address = wh_db_output ( $row_obj->address );
        $club -> postcode = wh_db_output ( $row_obj->postcode );
        $club -> latitude = wh_db_output ( $row_obj->latitude );
        $club -> longtitude = wh_db_output ( $row_obj->longtitude );
        $club -> comment = wh_db_output ( $row_obj->comment );
        $club -> website = wh_db_output ( $row_obj->website );
        $club -> email = wh_db_output ( $row_obj->email );
        $club -> phone = wh_db_output ( $row_obj->phone );
        $club -> time_open = wh_db_output ( $row_obj->opening_time );
        $club -> time_close = wh_db_output ( $row_obj->closing_time );
        $club -> price_member = wh_db_output ( $row_obj->price_member );
        $club -> price_nonmember = wh_db_output ( $row_obj->price_nonmember );
#       var_dump ( get_object_vars ($club) );
#       $club -> sports = getSportsByClub ( $club->id );
      }
#     var_dump ( $club );
#     die();
    }
    if ( $action != 'update' && wh_not_null ( $sport_search ) && $sport_search != '0' )
    {
#     var_dump ( $sport_search );
      global $clubs;
      $clubs = array ( 0 => str_repeat ( '&nbsp', 45 ) );
      $club_query = getClubsBySportsId ( 'sports', array ( $sport_search ) );
      while ( $row_obj = wh_db_fetch_object_custom ( $club_query ) ) {
        $clubs [ $row_obj->id ] = $row_obj->name;
      }
#     var_dump ( $clubs );
#     die ();
    }
    else
    {
      global $clubs;
      $clubs = array ( 0 => str_repeat ( '&nbsp', 45 ) );
      $club_query = getClubsOrderByName ( );
      while ( $row_obj = wh_db_fetch_object_custom ( $club_query ) ) {
        $clubs [ $row_obj->id ] = $row_obj->name;
      }
#     var_dump ( $clubs );
#     die ();
    }

  }

  function wh_get_price ( $price )
  {
    return ( wh_not_null ($price) ? $price : null );
  }

  function wh_get_time ( $time )
  {
    return ( wh_not_null ($time) && $time !== '00:00:00' &&
        strtotime($time) !== false &&
        validate_time ($time, 'H:i:s') || validate_time ($time, 'H:i') ?
          $time : null );
  }

  function wh_fill_times_prices ( )
  {
    global $clubosportquery, $clubosport_row, $sport_id,
        $prices, $times, $days_type, $days_type_price, $days_type_time;

    if ( $clubosport_row && $clubosport_row->sport_id == $sport_id )
    {
      $prices = array_fill ( 1 , 7, ['member' => '', 'nonmember' => ''] );
      $times = array_fill ( 1 , 7, ['open' => '', 'close' => ''] );
      do
      {
        $day_id = $clubosport_row->day_id;
#         echoQuery ( $clubosport_row->day_id );
        if ( $day_id == 8 )
        {
          $price_member = wh_get_price ($clubosport_row->price_member);
          $price_nonmember = wh_get_price ($clubosport_row->price_nonmember);
          $time_open  = wh_get_time ($clubosport_row->opening_time);
          $time_close = wh_get_time ($clubosport_row->closing_time);
#           $days_selected = array_fill(1, 7, true);
          if ( $time_open === null || $time_close === null ) {
            $time_open = $time_close = null;
          }
          if ( $price_member !== null ) {
            for ( $i = 1; $i < 8; ++$i ) {
              $prices ['member'] = $price_member;
            }
          }
          if ( $price_nonmember !== null ) {
            for ( $i = 1; $i < 8; ++$i ) {
              $prices ['nonmember'] = $price_nonmember;
            }
          }
          if ( $time_open !== null ) {
            $times = array_fill ( 1 , 7,
                ['open' => $time_open, 'close' => $time_open] );
          }
        }
        elseif ( $day_id == 9 )
        {
          $price_member = wh_get_price ($clubosport_row->price_member);
          $price_nonmember = wh_get_price ($clubosport_row->price_nonmember);
          $time_open  = wh_get_time ($clubosport_row->opening_time);
          $time_close = wh_get_time ($clubosport_row->closing_time);
          if ( $time_open === null || $time_close === null ) {
            $time_open = $time_close = null;
          }
          for ($i = 1; $i < 6; ++$i)
          {
#           $days_selected [$i] = true;
            if ($price_member !== null) {
              $prices [$i] ['member'] = $price_member;
            }
            if ($price_nonmember !== null) {
              $prices [$i] ['nonmember'] = $price_nonmember;
            }
            if ($time_open !== null) {
              $times [$i] ['open']  = $time_open;
              $times [$i] ['close'] = $time_close;
            }
          }
        }
        elseif ( $day_id == 10 )
        {
          $price_member = wh_get_price ($clubosport_row->price_member);
          $price_nonmember = wh_get_price ($clubosport_row->price_nonmember);
          $time_open  = wh_get_time ($clubosport_row->opening_time);
          $time_close = wh_get_time ($clubosport_row->closing_time);
          if ( $time_open === null || $time_close === null ) {
            $time_open = $time_close = null;
          }
          for ($i = 6; $i < 8; ++$i)
          {
#             $days_selected [$i] = true;
            if ($price_member !== null) {
              $prices [$i] ['member'] = $price_member;
            }
            if ($price_nonmember !== null) {
              $prices [$i] ['nonmember'] = $price_nonmember;
            }
            if ($time_open !== null) {
              $times [$i] ['open']  = $time_open;
              $times [$i] ['close'] = $time_close;
            }
          }
        }
        elseif ( 0 < $day_id && $day_id < 8 )
        {
          $price_member = wh_get_price ($clubosport_row->price_member);
          $price_nonmember = wh_get_price ($clubosport_row->price_nonmember);
          $time_open  = wh_get_time ($clubosport_row->opening_time);
          $time_close = wh_get_time ($clubosport_row->closing_time);
          if ( $time_open === null || $time_close === null ) {
            $time_open = $time_close = null;
          }
#           $days_selected [$day_id] = true;
          if ($price_member !== null) {
            $prices [$day_id] ['member'] = $price_member;
          }
          if ($price_nonmember !== null) {
            $prices [$day_id] ['nonmember'] = $price_nonmember;
          }
          if ($time_open !== null) {
            $times [$day_id] ['open']  = $time_open;
            $times [$day_id] ['close'] = $time_close;
          }
        }
      }
      while ( ($clubosport_row = wh_db_fetch_object_custom($clubosportquery))
            && $clubosport_row->sport_id == $sport_id  );

      // checking the most optimal way to represent the data
      $days_type_price = wh_determine_best_view_prices ( $prices );
      $days_type_time  = wh_determine_best_view_times ( $times );
    }
    // If there were no clubosport entries
    if ( $days_type_price == '' ) {
      $days_type_price = 'all';
    }
    if ( $days_type_time == '' ) {
      $days_type_time = 'all';
    }
  }

  function wh_determine_best_view_prices ( $prices )
  {
    for ( $i = 1; $i < 5; ++$i )
    {
      if ( $prices [$i] ['member']    != $prices [$i+1] ['member'] ||
           $prices [$i] ['nonmember'] != $prices [$i+1] ['nonmember'] )
      {
        return 'separately';
      }
    }
    // if data type not set yet, check if at least workweek days identical
    if ( $prices [6] ['member']    != $prices [7] ['member'] ||
         $prices [6] ['nonmember'] != $prices [7] ['nonmember'] )
    {
      return 'workweeksatsun';
    }
    if ( $prices [5] ['member']    == $prices [6] ['member'] &&
         $prices [6] ['nonmember'] == $prices [7] ['nonmember']  )
    {
      return 'all';
    }
    return 'workweekweekend';
  }

  function wh_determine_best_view_times ( $times )
  {
    for ( $i = 1; $i < 5; ++$i )
    {
      if ( $times [$i] ['open']  != $times [$i+1] ['open'] ||
           $times [$i] ['close'] != $times [$i+1] ['close'] )
      {
        return 'separately';
      }
    }
    // if data type not set yet, check if at least workweek days identical
    if ( $times [6] ['open']  != $times [7] ['open'] ||
         $times [6] ['close'] != $times [7] ['close'] )
    {
      return 'workweeksatsun';
    }
    if ( $times [5] ['open']  == $times [6] ['open'] &&
         $times [6] ['close'] == $times [7] ['close']  )
    {
      return 'all';
    }
    return 'workweekweekend';
  }

  editClub ( );
  selectClub ( );
  //if ( wh_db_get_input_string ('success') == '1' ) {
  if ( isset ( $_GET ['success'] ) ) {
    wh_define ( 'TEXT_SUCCESS', '<strong style="color: #00FF00">'.
        'Successfully edited a club</strong>' );
  }
# var_dump (get_object_vars($club));
?>
