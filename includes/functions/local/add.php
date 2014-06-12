<?php
  function addClub ( )
  {
    if ( ! wh_db_post_input_check_string ( 'name' ) )
    {
      return;
    }

    $name = wh_db_post_input_string ( 'name' );

    if ( wh_not_null ( wh_db_fetch_row_custom ( getClubByName ( $name ) ) ) )
    {
      wh_define ( 'TEXT_ERROR', '<strong style="color: #FF0000">There is another club with the same name</strong>' );
      return;
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
    $time_open = wh_db_post_input_string ( 'time_open' );
    $time_open = wh_db_prepare_null ($time_open);
    //set variables to null if zeroes
    if ( $time_open == '00:00:00' ) {
      $time_open = 'null';
    }

    // previous to PHP 5.1.0 you would compare with -1, instead of false
    if ( wh_not_null ($time_open) && strtotime($time_open) === false ) {
      wh_define ( 'TEXT_ERROR',
      '<strong style="color: #FF0000">Opening time is not a valid time</strong>' );
      return;
    }

    $time_close = wh_db_post_input_string ( 'time_close' );
    $time_close = wh_db_prepare_null ($time_close);
    //set variables to null if zeroes
    if ( $time_close == '00:00:00' ) {
      $time_close = 'null';
    }

    if ( wh_not_null ($time_close) && strtotime($time_close) === false ) {
      wh_define ( 'TEXT_ERROR',
      '<strong style="color: #FF0000">Closing time is not a valid time</strong>' );
      return;
    }

    if ( $time_open == 'null' || $time_close == 'null' ) {
      $time_open = $time_close = 'null';
    }

    $price_member = wh_db_post_input_string ( 'price_member' );
    $price_member = wh_db_prepare_null ($price_member);

    if ( wh_not_null ($price_member) && ! is_numeric ( $price_member ) )
    {
      wh_define ( 'TEXT_ERROR',
      '<strong style="color: #FF0000">Members price is not numeric</strong>' );
      return;
    }

    $price_nonmember = wh_db_post_input_string ( 'price_nonmember' );
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

    wh_db_perform ( 'clubs', $data );
    $club_id = wh_db_insert_id ( );
    $data = [];

    $days = array ( );
    $everyday = wh_db_post_input_prepare ( 'everyday' );
    if ( $everyday != true )
    {
      if ( wh_db_post_input_check ( 'monday' ) )
        $days ['monday'] = 1;
      if ( wh_db_post_input_check ( 'tuesday' ) )
        $days ['tuesday'] = 2;
      if ( wh_db_post_input_check ( 'wednesday' ) )
        $days ['wednesday'] = 3;
      if ( wh_db_post_input_check ( 'thursday' ) )
        $days ['thursday'] = 4;
      if ( wh_db_post_input_check ( 'friday' ) )
        $days ['friday'] = 5;
      if ( wh_db_post_input_check ( 'saturday' ) )
        $days ['saturday'] = 6;
      if ( wh_db_post_input_check ( 'sunday' ) )
        $days ['sunday'] = 7;
    }
    $sports = wh_db_post_input_prepare_array ( 'sports' );

    //foreach do not check if sports is null

    if ( $everyday == true )
    {
      foreach ( $sports as $sport )
      {
        $data = [
          "club_id" => $club_id,
          "sport_id" => $sport,
          'price_member' => $price_member,
          'price_nonmember' => $price_nonmember,
          "opening_time" => $time_open,
          "closing_time" => $time_close
          //"day_id" => 8
        ];
        wh_db_perform ( 'clubosport', $data );
      }
    } //if ( $everyday...
    else
    {
      foreach ( $sports as $sport )
      {
        foreach ( $days as $day )
        {
          $data = [
            "club_id" => $club_id,
            "sport_id" => $sport,
            "day_id" => $day,
            "price_member" => $price_member,
            "price_nonmember" => $price_nonmember,
            "opening_time" => $time_open,
            "closing_time" => $time_close
          ];
          wh_db_perform ( 'clubosport', $data );
        } //foreach ( $days...
      } //foreach ( $sports...
    } //if ( $everyday... else...

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
    // will write the Unix timestamp to newest.txt

    wh_define ( 'TEXT_SUCCESS', '<strong style="color: #00FF00">Successfully added a club</strong>' );


  }

  addClub ( );
?>
