<?php

  $club_init = [
    'name' => '',
    'address' => '',
    'postcode' => '',
    'latitude' => '',
    'longtitude' => '',
    'website' => '',
    'email' => '',
    'phone' => '',
    'comment' => '',
    'time' => '',
    'price' => '',
    'sports' => '',
    'facilities' => ''
  ];

  $day_regex = '(?:monday|tuesday|wednesday|thursday|friday|saturday|sunday'
       . '|mon|tue|wed|thu|fri|sat|sun)';
  $hour_regex = '(?:\d\d?(?:(?::|\.)\d\d)?(?:\s*(?:am|pm))?)|(?:12(?:(?::|\.)\d\d)?(?:\s*(?:noon))?)';
  $price_regex = '(?:free|(?:\d+(?:\.\d\d)*))';

  /**
   * @param url fetch url
   * @return remote file, string
   * Fetch remote file
   */
  function curl_get_file_contents_custom ($url)
  {
    $c = curl_init();
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_URL, $url);
    $data = curl_exec($c);
    curl_close($c);
    return $data;
  }

  /**
   * @param url fetch url
   * @return remote file, string
   * Fetch remote page
   */
  function curl_get_html_file_contents_custom($url)
  {
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_USERAGENT, 'curl');
    curl_setopt($curl, CURLOPT_ENCODING, "gzip");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    // enable FOLLOWLOCATION if the location of the xml can be changed
    // and the old one URL link to the new
    // curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    $data = curl_exec($curl);
    curl_close($curl);
    return $data;
  }

  /**
   * @param entity input array
   * @return first element
   * Get first element from array and cast to string
   */
  function get_first_element ( $entity )
  {
    return count ( $entity ) > 0 ?
              (string) $entity[0] : '';
  }

  /**
   * @param entity club, sport or time
   * @return xpath query
   * Build xpath query, uses GET to fill the values
   */
  function build_query_council_edinburgh ( $entity )
  {
    if ( $entity == 'club' )
    {
      $club = wh_db_get_input_string ( 'club' );
      if ( ! isset ( $club ) ) {
        return '';
      }
      return "[title[text()='$club']]";
    }
    else if ( $entity == 'sport' )
    {
      $sports = wh_db_get_input_string ( 'sport' );
      if ( ! isset ( $sports ) ) {
        return '';
      }
      $sports = explode ( ' ', $sports );
      $query = "[fields/field[@name='Activities']";
      foreach ( $sports as $sport ) {
        $query .= "[contains(text(),'$sport')]";
      }
      $query .= ']';
      return $query;
    }
    else if ( $entity == 'time' )
    {
      $times = wh_db_get_input_string ( 'time' );
      if ( ! isset ( $times ) ) {
        return '';
      }
      $times = explode ( ' ', $times );
      $query = "[fields/field[@name='Opening hours']";
      foreach ( $times as $time ) {
        $query .= "[contains(text(),'$time')]";
      }
      $query .= ']';
      return $query;
    }
    return '';
  }

  /**
   * Fetch remote xmls and update local xmls if changed and if valid xml
   * @param xmls array with origin file and remote url as key and value
   * @return boolean, whether updated or not
   */
  function update_xmls ( $xmls )
  {
    $updated = false;
    foreach ( $xmls as $file => $url )
    {
      $remote = curl_get_html_file_contents_custom ( $url );
      if ( ! file_exists (DIR_WS_DATABASE.$file) ||
            $remote !== file_get_contents (DIR_WS_DATABASE.$file) )
      {
        // parse remote xml
        set_error_handler (function ($errno, $errstr, $errfile, $errline) {
          throw new Exception ($errstr, $errno);
        });
        try {
          new SimpleXMLElement ($remote);
        } catch (Exception $e) {
          restore_error_handler ();
          continue;
        }
        restore_error_handler ();

        // write remote to origin
        try {
          file_put_contents (DIR_WS_DATABASE.$file, $remote);
        } catch (Exception $e) {
          $error = 'The XML could not be written.<br />' .
                  'Possibly there is a permission problem.<br />' .
                  'Delete the XML files and try again.';
          wh_error ( $error );
        }
        $updated = true;
      }
    }
    return $updated;
  }

  /**
   * Parse xml and create SimpleXMLElement
   * @param file name of input file
   * @return SimpleXMLElement
   */
  function loadXML ( $file )
  {
    set_error_handler (function ($errno, $errstr, $errfile, $errline) {
      throw new Exception ($errstr, $errno);
    });

    try {
      $xml = new SimpleXMLElement (DIR_WS_DATABASE.$file, null, true);
    } catch (Exception $e) {
      $error = 'The XML could not be loaded.<br />' .
               'Possibly the contacted server is down.';
      wh_error ( $error );
    }

    restore_error_handler ();
    return $xml;
  }

  function process_current_club_council_edinburgh ( $club )
  {
    global $club_init;
    $current = $club_init;
    $address = $club->xpath('fields/field[@name=\'Address\']/text()');
    $postcode = $club->xpath('fields/field[@name=\'Postcode\']/text()');
    $location = $club->xpath('fields/field[@name=\'Location\']/text()');
    $email = $club->xpath('fields/field[@name=\'Email\']/text()');
    $phone = $club->xpath('fields/field[@name=\'Telephone\']/text()');
    $website = $club->xpath('fields/field[@name=\'Timetables\']/text()');
    $comment = $club->xpath('fields/field[@name=\'More information\']/text()');
    $times = $club->xpath('fields/field[@name=\'Opening hours\']/text()');
    $prices = $club->xpath('fields/field[@name=\'Prices\']/text()');
    $sports = $club->xpath('fields/field[@name=\'Activities\']/text()');
    $facilities = $club->xpath('fields/field[@name=\'Facilities\']/text()');

    $name = trim ((string) $club->title);
    $address = trim (get_first_element ($address));
    $postcode = trim (get_first_element ($postcode));
    $email= strtolower (trim (get_first_element ($email)));
    $phone = trim (get_first_element ($phone));
    $website = strtolower (trim (get_first_element ($website)));
    $comment = trim (get_first_element ($comment));
    $times = strtolower (trim (get_first_element ($times)));
    $prices = strtolower (trim (get_first_element ($prices)));
    $sports = strtolower (trim (get_first_element ($sports)));
    $facilities = strtolower (trim (get_first_element ($facilities)));
    $name = str_replace ( ["\r", "\n", "\t"], ' ', $name );
    $name = preg_replace ( '/\s+/', ' ', $name );
    $address = str_replace ( ["\r", "\n", "\t"], ' ', $address );
    $address = preg_replace ( '/\s+/', ' ', $address );
    $email = str_replace ( ["\r", "\t"], ' ', $email );
    $email = preg_replace ( '/\s+/', ' ', $email );
    $email = str_replace ( "\n", ', ', $email );
    $email = rtrim (str_replace ( '.uk', '.uk ', $email ));
    $phone = str_replace ( ["\r", "\n", "\t"], ' ', $phone );
    $phone = preg_replace ( '/\s+/', ' ', $phone );
    $website = str_replace ( ["\r", "\n", "\t"], ' ', $website );
    $website = preg_replace ( '/\s+/', ' ', $website );
    $comment = str_replace ( ["\r", "\t"], '', $comment );
    $comment = preg_replace ( '/ +\n/', "\n", $comment );
    $comment = preg_replace ( '/\n +/', "\n", $comment );
    $comment = preg_replace ( '/ +/', ' ', $comment );
    $comment = preg_replace ( '/\n+/', "\n", $comment );
    $times = str_replace ( ["\r", "\t"], '', $times );
#   $times = str_replace ( "\n", ', ', $times );
    $times = preg_replace ( '/\s+/', ' ', $times );
    $prices = str_replace ( ["\r", "\t"], '', $prices );
#   $prices = str_replace ( "\n", ', ', $prices );
    $prices = preg_replace ( '/\s+/', ' ', $prices );
    $sports = str_replace ( ["\r", "\t"], '', $sports );
    $sports = preg_replace ( '/ +\n/', "\n", $sports );
    $sports = preg_replace ( '/\n +/', "\n", $sports );
    $sports = preg_replace ( '/ +/', ' ', $sports );
    $sports = preg_replace ( '/\n+/', "\n", $sports );
    $sports = str_replace ( "\n", ', ', $sports );
    $facilities = str_replace ( ["\r", "\t"], '', $facilities );
    $facilities = preg_replace ( '/ +\n/', "\n", $facilities );
    $facilities = preg_replace ( '/\n +/', "\n", $facilities );
    $facilities = preg_replace ( '/ +/', ' ', $facilities );
    $facilities = preg_replace ( '/\n+/', "\n", $facilities );
    $facilities = str_replace ( "\n", ', ', $facilities );
    if ( count ($location) > 0 && $location[0] != '' &&
          count ($location = explode ( ',', $location[0])) == 2 )
    {
      $current ['latitude'] = (double) trim ($location [0]);
      $current ['longtitude'] = (double) trim ($location [1]);
    }
    $matches = null;
    if ($website == '' &&
        preg_match_all ('/(?:http\:\/\/|www\.)\S+\.\S+/', $comment, $matches) )
    {
      $website = '';
      foreach ( $matches [0] as $match ) {
        $website .= $match . ', ';
      }
      $website = substr ( $website, 0, -2 );
      $comment = '';
      $matches = null;
    }
    $current['name'] = $name;
    $current['address'] = $address;
    $current['postcode'] = $postcode;
    $current['email'] = $email;
    $current['phone'] = $phone;
    $current['website'] = $website;
    $current['comment'] = $comment;
    $current['time'] = $times;
    $current['price'] = $prices;
    $current['sports'] = $sports;
    $current['facilities'] = $facilities;
    return $current;
  }

  function process_current_club_club_sport_edinburgh ( $club )
  {
    global $club_init;
    $current = $club_init;
    $name = $club->xpath('title/text()');
    $email = $club->xpath('email/text()');
    $phone = $club->xpath('telephone/text()');
    $website_local = $club->xpath('path/text()');
    $website_remote = $club->xpath('website/text()');
    $comment_short = $club->xpath('short/text()');
    $comment_long  = $club->xpath('long/text()');
    $sport = $club->xpath('sport/text()');
    $name = trim (get_first_element ($name));
    $email= strtolower (trim (get_first_element ($email)));
    $phone = trim (get_first_element ($phone));
    $website_local = strtolower (trim (get_first_element ($website_local)));
    $website_remote = strtolower (trim (get_first_element ($website_remote)));
#   $website = implode ( ', ', [$website_local, $website_remote] );
    $website = $website_local . ', ' . $website_remote;
    $website = trim ( $website, ', ' );
    $comment_short = trim (get_first_element ($comment_short));
    $comment_long  = trim (get_first_element ($comment_long));
    $comment = '<p>' . $comment_short . '</p>' . $comment_long;
    $sport = strtolower (trim (get_first_element ($sport)));
    $name = str_replace ( ["\r", "\n", "\t"], ' ', $name );
    $name = preg_replace ( '/\s+/', ' ', $name );
    $email = str_replace ( ["\r", "\t"], ' ', $email );
    $email = preg_replace ( '/\s+/', ' ', $email );
    $email = str_replace ( "\n", ', ', $email );
    $email = rtrim (str_replace ( '.uk', '.uk ', $email ));
    $phone = str_replace ( ["\r", "\n", "\t"], ' ', $phone );
    $phone = preg_replace ( '/\s+/', ' ', $phone );
    $website = str_replace ( ["\r", "\n", "\t"], ' ', $website );
    $website = preg_replace ( '/\s+/', ' ', $website );
    $comment = str_replace ( ["\r", "\t"], '', $comment );
    $comment = preg_replace ( '/ +\n/', "\n", $comment );
    $comment = preg_replace ( '/\n +/', "\n", $comment );
    $comment = preg_replace ( '/ +/', ' ', $comment );
    $comment = preg_replace ( '/\n+/', "\n", $comment );
    $sport = str_replace ( ["\r", "\n", "\t"], ' ', $sport );
    $matches = null;
    $current ['name'] = $name;
    $current ['email'] = $email;
    $current ['phone'] = $phone;
    $current ['website'] = $website;
    $current ['comment'] = $comment;
    $current ['sports'] = $sport;
    $current ['venue'] = [];
    foreach ( $club->xpath('venue') as $venue )
    {
      $venue_name = $venue->xpath('name/text()');
      $address = $venue->xpath('address/text()');
      $latitude = $venue->xpath('latitude/text()');
      // this is wrong but it is fix for an error in the input xml
      $longtitude = $venue->xpath('longitude/text()');
      $venue_name = trim (get_first_element ($venue_name));
      $address = trim (get_first_element ($address));
      $venue_name = str_replace ( ["\r", "\n", "\t"], ' ', $venue_name );
      $venue_name = preg_replace ( '/\s+/', ' ', $venue_name );
      $address = str_replace ( ["\r", "\n", "\t"], ' ', $address );
      $address = preg_replace ( '/\s+/', ' ', $address );
      $latitude = trim (get_first_element ($latitude));
      $longtitude = trim (get_first_element ($longtitude));
      $postcode = null;
      if ($address != '' &&
          preg_match ('/EH\d\d? [A-Z0-9]{3}/', $address, $matches) )
      {
        $postcode = $matches [0];
        $matches = null;
      }
      $time_mon = $venue->xpath('montime/text()');
      $time_tue = $venue->xpath('tuetime/text()');
      $time_wed = $venue->xpath('wedtime/text()');
      $time_thu = $venue->xpath('thutime/text()');
      $time_fri = $venue->xpath('fritime/text()');
      $time_sat = $venue->xpath('sattime/text()');
      $time_sun = $venue->xpath('suntime/text()');
      $times = [1 => $time_mon, 2 => $time_tue, 3 => $time_wed, 4 => $time_thu,
                5 => $time_fri, 6 => $time_sat, 7 => $time_sun];
      foreach ( $times as &$time )
      {
        $time = strtolower (trim (get_first_element ($time)));
      }
      unset ($time);
      $current ['venue'] [] = [
          'name'=> $venue_name,
          'address' => $address,
          'postcode' => $postcode,
          'latitude' => (double) $latitude,
          'longtitude' => (double) $longtitude,
          'time' => $times
      ];
    }
    return $current;
  }

  function process_clubs_council_edinburgh ( $xml, $query )
  {
    $arr = [];
    $sports = wh_db_fetch_all_custom ( getSports ( ), MYSQLI_ASSOC );
    foreach ( $xml->xpath('/entries/entry' . $query) as $club )
    {
      $current_club = process_current_club_council_edinburgh ($club);

      $data = $current_club;

      // These are raw unparsed fields and should not be submitted
      unset ($data ['time']);
      unset ($data ['price']);
      unset ($data ['sports']);
      unset ($data ['facilities']);

      foreach ( $arr as $club_t ) {
        if ( $club_t ['name'] == $current_club['name'] ) {
#         wh_error ('There is another club with the same name');
          $error = 'There is another club with the same name - ' .
                   $current_club ['name'];
          if (php_sapi_name() === 'cli') {
            echo $error, PHP_EOL;
          } else {
            echo '<div style="color:red">',
                  '<h1>', nl2br ($error) , '</h1>', PHP_EOL,
                  '</div>';
          }
          error_log($error . "\n", 0);
          break;
        }
      }
      if ( isset ($error) && $error !== '' ) {
        $error = '';
        continue;
      }

      $times = parse_time ($current_club ['time']);
      if ( isset ($times [8]) ) {
        $data ['opening_time'] = $times [8] ['open'];
        $data ['closing_time'] = $times [8] ['close'];
      }

      $prices = parse_price ($current_club ['price']);
      if ( isset ($prices [8]) ) {
        $data ['price_member']    = $prices [8] ['member'];
        $data ['price_nonmember'] = $prices [8] ['nonmember'];
      }

      wh_db_perform ( TABLE_CLUBS, $data, 'insert' );
      $id = wh_db_insert_id ();
      $data = [ 'club_id' => $id ];

      // TODO The queries can be made in one query

      if ( (count ($times) > 0) && (! isset ($times [8])) )
      {
        foreach ( $times as $day => $time )
        {
          $data ['day_id'] = $day;
          if ( $time ['open'] === true && $time ['close'] === true ) {
            $data ['opening_time'] = 'null';
            $data ['closing_time'] = 'null';
          } else if ( $time ['open'] !== '' && $time ['close'] !== '' ) {
            $data ['opening_time'] = $time ['open'];
            $data ['closing_time'] = $time ['close'];
          } else continue;
          wh_db_perform ( TABLE_CLUB_SCHEDULE, $data, 'insert' );
        }
      }

      $data = [ 'club_id' => $id ];

      if ( (count ($prices) > 0) && (! isset ($prices [8])) )
      {
        foreach ( $prices as $day => $price )
        {
          if ( $price ['nonmember'] === '' && $price ['member'] === '' ) {
            continue;
          }
          $data ['day_id'] = $day;
          if ( $price ['member'] !== '' ) {
            $data ['price_member'] = $price ['member'];
          }
          if ( $price ['nonmember'] !== '' ) {
            $data ['price_nonmember'] = $price ['nonmember'];
          }
          wh_db_perform ( TABLE_CLUB_SCHEDULE, $data,
                          'insert on duplicate key update' );
        }
      }

      $data = [ 'club_id' => $id ];
      $sports_club = parse_sports ($sports, $current_club ['sports']);
      foreach ( $sports_club  as $entry )
      {
        $data ['sport_id'] = $entry ['sport'];
        foreach ( $entry ['times'] as $day => $time )
        {
          $data ['day_id'] = $day;
          if ( $time ['open'] === true && $time ['close'] === true ) {
            $data ['opening_time'] = 'null';
            $data ['closing_time'] = 'null';
          } else if ( $time ['open'] !== '' && $time ['close'] !== '' ) {
            $data ['opening_time'] = $time ['open'];
            $data ['closing_time'] = $time ['close'];
          } else {
            $data ['opening_time'] = 'null';
            $data ['closing_time'] = 'null';
          }
          wh_db_perform ( TABLE_CLUBOSPORT, $data, 'insert' );
        }
        unset ($data ['opening_time']);
        unset ($data ['closing_time']);
        foreach ( $entry ['prices'] as $day => $price )
        {
          $data ['day_id'] = $day;
          if ( $price ['member'] !== '' ) {
            $data ['price_member'] = $price ['member'];
          } else {
            $data ['price_member'] = 'null';
          }
          if ( $price ['nonmember'] !== '' ) {
            $data ['price_nonmember'] = $price ['nonmember'];
          } else {
            $data ['price_nonmember'] = 'null';
          }
          wh_db_perform ( TABLE_CLUBOSPORT, $data,
                          'insert on duplicate key update' );
        }
        unset ($data ['price_member']);
        unset ($data ['price_nonmember']);
      }
      $data = [ 'club_id' => $id ];

      $arr[] = $current_club;
    }
    file_put_contents ( DIR_WS_DATABASE . 'newest.txt', time () . PHP_EOL );
    // write the Unix timestamp to newest.txt
  }

  function process_clubs_club_sport_edinburgh ( $xml )
  {
    $arr = [];
    $sports = wh_db_fetch_all_custom ( getSports ( ), MYSQLI_ASSOC );
    foreach ( $xml->xpath('/xml/club') as $club )
    {
      $current_club = process_current_club_club_sport_edinburgh ($club);
      $current_club_main = $current_club;
      unset ($current_club_main ['venue']);
      foreach ( $current_club ['venue'] as &$venue )
      {
        $data = $current_club_main;

        // These are raw unparsed fields and should not be submitted
        unset ($data ['time']);
        unset ($data ['price']);  // does not exist
        unset ($data ['sports']);
        unset ($data ['facilities']);  // does not exist

        if ( $current_club_main ['name'] === '' ) {
          wh_error ('No name for the club');
        }

        if ( $venue ['name'] === '' ) {
          wh_error ('No name for the venue');
        }

        if ($venue ['name'] === '') {
          $venue ['name'] = $current_club_main ['name'];
        } else if ($current_club_main ['name'] !== $venue ['name']) {
          $venue ['name'] = $current_club_main ['name'] . ', ' . $venue['name'];
        }
        $data ['name'] = $venue ['name'];
        $data ['address'] = $venue ['address'];
        $data ['postcode'] = $venue ['postcode'];
        $data ['latitude'] = $venue ['latitude'];
        $data ['longtitude'] = $venue ['longtitude'];

        foreach ( $arr as $club_t ) {
          if ( $club_t ['name'] == $venue ['name'] ) {
  #         wh_error ('There is another club with the same name');
            $error = 'There is another club with the same name - ' .
                    $venue ['name'];
            if (php_sapi_name() === 'cli') {
              echo $error, PHP_EOL;
            } else {
              echo '<div style="color:red">',
                    '<h1>', nl2br ($error) , '</h1>', PHP_EOL,
                    '</div>';
            }
            error_log($error . "\n", 0);
            break;
          }
        }
        if ( isset ($error) && $error !== '' ) {
          $error = '';
          continue;
        }

        $times = $venue ['time'];
        $times = process_time_club_sport_edinburgh ($times);
        $times_empty = ! time_check ( $times );
        if ( $times_empty === false )
        {
          $times = wh_times_prices_num_to_assoc (
              $times, wh_determine_best_view_times ($times));
          if ( isset ($times [8]) &&
                isset ($times [8]['open']) &&  isset ($times [8]['close']) &&
                $times [8]['open'] !== '' && $times [8]['close'] !== '' &&
                $times [8]['open'] !== true && $times [8]['close'] !== true ) {
            $data ['opening_time'] = $times [8] ['open'];
            $data ['closing_time'] = $times [8] ['close'];
          }
        } else {
          $times = [];
        }

        wh_db_perform ( TABLE_CLUBS, $data, 'insert' );
        $id = wh_db_insert_id ();
        $data = [ 'club_id' => $id ];

        // TODO The queries can be made in one query

        if ( $times !== [] && (! isset ($times [8])) )
        {
          foreach ( $times as $day => $time )
          {
            $data ['day_id'] = $day;
            if ( $time ['open'] === '' || $time ['close'] === '' ||
                  $time ['open'] === true || $time ['close'] === true ) {
              continue;
            }
            $data ['opening_time'] = $time ['open'];
            $data ['closing_time'] = $time ['close'];
            wh_db_perform ( TABLE_CLUB_SCHEDULE, $data, 'insert' );
          }
        }
        $data = [ 'club_id' => $id ];

        $sports_club = process_sports_club_sport_edinburgh ($sports, $current_club_main ['sports']);
        foreach ( $sports_club  as $entry )
        {
          $data ['sport_id'] = $entry ['id'];
          wh_db_perform ( TABLE_CLUBOSPORT, $data, 'insert' );
        }
        $data = [ 'club_id' => $id ];

        $arr [] = array_merge ($current_club_main, $venue);
      }
      unset ($venue);
    }
    file_put_contents ( DIR_WS_DATABASE . 'newest.txt', time () . PHP_EOL );
    // write the Unix timestamp to newest.txt
  }

  function output_array ( $arr )
  {
#   echo json_encode ( $arr );
    var_dump ($arr);
#   var_export($arr);
  }

  function output_xml ( $xml )
  {
    echo nl2br ( wh_output_string_protected (
          preg_replace ( '/(?:\n)+/', "\n",
              str_replace ( '&#13;', '', $xml ) ) ) );
  }

  /**
   * Replacing em dashes and other characters
   * Fix for the parser
   * source: http://www.toao.net/48-replacing-smart-quotes-and-em-dashes-in-mysql
   */
  function unicode_fix ( $string )
  {
//     // First, replace UTF-8 characters.
//     $string = str_replace(
//         array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d",
//               "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"),
//         array("'", "'", '"', '"', '-', '--', '...'),
//         $string);
//     // Next, replace their Windows-1252 equivalents.
//     $string = str_replace(
//         array(chr(145), chr(146), chr(147), chr(148),
//               chr(150), chr(151), chr(133)),
//         array("'", "'", '"', '"', '-', '--', '...'),
//         $string);
    return str_replace(
        array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d",
              "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6",
              chr(145), chr(146), chr(147), chr(148),
              chr(150), chr(151), chr(133)),
        array("'", "'", '"', '"', '-', '--', '...',
              "'", "'", '"', '"', '-', '--', '...'),
        $string);
  }

  /**
   * Convert a day to its number in the week
   */
  function day_to_number ( $day )
  {
#   return (int) date('N', strtotime($day));
    switch ( $day )
    {
    case 'mon': case 'monday':
      return 1; break;
    case 'tue': case 'tuesday':
      return 2; break;
    case 'wed': case 'wednesday':
      return 3; break;
    case 'thu': case 'thursday':
      return 4; break;
    case 'fri': case 'friday':
      return 5; break;
    case 'sat': case 'saturday':
      return 6; break;
    case 'sun': case 'sunday':
      return 7; break;
    }
  }

  /**
   * Reads i-th entry from the matches array (returned by preg_match_all).
   * If the time open_time and close_time variables are not nulls,
   * it sets the i-th entry of the times array to them.
   * If they are nulls, but the times array does not contain a time,
   * it sets it to true.
   * If they are nulls, but the times array already contains a time,
   * it does nothing.
   * This function must be called as the following:
   * $times [$j] = set_time ( $matches, $times[$j], $i );
   */
  function set_time ( $matches, $times, $i )
  {
    // If the current entry in matches array contains a valid time,
    // overwrite the times array
    if ( $matches['open_time'][$i] !== '' &&
         $matches['close_time'][$i] !== '') {
        return [ 'open'  => $matches['open_time'] [$i],
                 'close' => $matches['close_time'][$i] ];
    }
    // If the day is not selected, select it without specifying time
    if ( $times['open'] === '' || $times['close'] === '' ) {
      return [ 'open'  => true, 'close' => true ];
    }
    // Otherwise, just return the current times
    return $times;
  }

  /**
   * Reads i-th entry from the matches array (returned by preg_match_all).
   * If the price price_member or price_nonmember variables are not nulls,
   * it sets the i-th entry of the prices array to them.
   * If they are nulls, but the prices array already contains a price,
   * it does nothing.
   * This function must be called as the following:
   * $prices [$j] = set_price ( $matches, $prices[$j], $i );
   */
  function set_price ( $matches, $prices, $i )
  {
    // If the current entry in matches array contains a valid price,
    // overwrite the prices array
    if ( $matches['price_member'][$i] !== '' ) {
      $prices ['member'] = $matches['price_member'][$i];
    }
    if ( $matches['price_nonmember'][$i] !== '' ) {
      $prices ['nonmember'] = $matches['price_nonmember'][$i];
    }
    foreach ( $prices as &$price ) {
      if ( $price === 'free' ) {
        $price = '0';
      }
    }
    unset ($price);
    return $prices;
  }

  /**
   * The function filters the times and modifies the input array,
   * parsing the am/pm/noon times and nullifying invalid times.
   * @param times array with 7 associative time arrays (open, close)
   * @return false if containing non-empty time or true otherwise;
   *           boolean true is considered a valid time
   *           because it shows the day is selected
   */
  function time_check ( &$times )
  {
    // flag indicating whether there are valid times
    $empty = true;
    for ( $i = 1; $i < 8; ++$i )
    {
      foreach ( $times [$i] as &$time )
      {
        if ( $time === '' ) {
          continue;
        }
        $empty = false;
        if ( $time === true ) {
          continue;
        }
        $time = strtolower ( $time );
        if ( strpos ($time, 'noon') !== false ) {
          $time = str_replace ( 'noon' ,  'pm', $time );
        }
        if ( strpos ($time, 'am') !== false || strpos ($time, 'pm') !== false )
        {
          if ( strpos ( $time, ':' ) !== false ) {
            $format = 'h:ia';
          } else if ( strpos ( $time, '.' ) !== false ) {
            $format = 'h.ia';
          } else {
            $format = 'ha';
          }
        } else {
          if ( strpos ( $time, ':' ) !== false ) {
            $format = 'H:i';
          } else if ( strpos ( $time, '.' ) !== false ) {
            $format = 'H.i';
          } else {
            $format = 'H';
          }
        }
        $timezone = new DateTimeZone('UTC');
        $datetime = DateTime::createFromFormat($format, $time, $timezone);
        if ( $datetime ) {
          $time = $datetime->format ( 'H:i' );
        } else {
          $time = true;
        }
      }
      unset ($time);
    }
    return ! $empty;
  }

  /**
   * @return false if empty or true otherwise
   */
  function price_check ( &$prices )
  {
    for ( $i = 1; $i < 8; ++$i )
    {
      foreach ( $prices [$i] as $price )
      {
        if ( $price === '' ) {
          continue;
        }
        return true;
      }
    }
    return false;
  }

  /**
   * Parse the time field and extracts timetable information from it
   * @param time the input field
   * @return associative array with times or empty array
   */
  function parse_time ( $time )
  {
    global $day_regex;
    global $hour_regex;

    if ( $time === '' ) {
      return;
    }

    $subject = $time;
    $subject = strtolower ( $subject );
    // Replacing m dashes and other characters
    // Otherwise the parsing did not parse em dash
    $subject = unicode_fix ( $subject );
    $subject = str_replace ('weekday', 'workweek', $subject);

    $pattern = "/(?:(?:(?P<start_day>{$day_regex})" .
        "(?:\s*-\s*(?P<end_day>{$day_regex})))|" .
        "(?:(?:(?P<day1>(?:{$day_regex}|workweek|weekend|everyday)))";
    for ( $i = 2; $i < 8; ++$i ) {
      $pattern .= "(?:\s*.\s*" .
          "(?P<day$i>(?:{$day_regex}|workweek|weekend|everyday)))?";
    }
    $pattern .= '))' .
        "(?:\s*(?::|,)\s*(?P<open_time>{$hour_regex})\s*-\s*" .
        "(?P<close_time>{$hour_regex}))?/";
    if (preg_match_all ($pattern, $subject, $matches))
    {
      $count = count ($matches [0]);
      // Fills the times array
      $times = array_fill_keys ( range(1 , 7), ['open' => '', 'close' => ''] );
      for ( $i = 0; $i < $count; ++$i )
      {
        // The current matched entry represents a day interval
        if ( $matches['start_day'][$i] !== '' && $matches['end_day'][$i] !== '' )
        {
          $begin = day_to_number ( $matches['start_day'][$i] );
          $end   = day_to_number ( $matches['end_day']  [$i] );
          for ( $j = $begin; $j <= $end; ++$j ) {
            $times [$j] = set_time ( $matches, $times[$j], $i );
          }
          continue;
        }
        // The current matched entry represents a sequence of days
        for ( $k = 1; ($k < 8) && ($matches['day'.$k][$i] !== ''); ++$k ) {
          switch ( $matches['day'.$k][$i] ) {
          case 'workweek':
            for ( $l = 1; $l < 6; ++$l ) {
              $times [$l] = set_time ( $matches, $times[$l], $i );
            }
            break;
          case 'weekend':
            for ( $l = 6; $l < 8; ++$l ) {
              $times [$l] = set_time ( $matches, $times[$l], $i );
            }
            break;
          default:
            $l = day_to_number ( $matches['day'.$k][$i] );
            if ( ! isset ( $l ) ) {
              break;
            }
            $times [$l] = set_time ( $matches, $times[$l], $i );
          }
        }
      }

      // TODO For example, if times are best viewed as 8 and prices as 9 and 10,
      // times should fallback to 9 and 10 using current rows instead of
      // inserting a new row in the table.

      $days_type_time = '';
      $times_empty = ! time_check ( $times );
      if ( $times_empty === false )
      {
        $days_type_time  = wh_determine_best_view_times ($times);
        if ($days_type_time !== 'separately') {
          $times = wh_times_prices_num_to_assoc ($times, $days_type_time);
        }
      } else {
        $times = [ 8 => ['open' => true, 'close' => true] ];
      }

      return $times;
    }
    return [];
  }

  /**
   * Parse the price field and extracts price information from it
   * @param price the input field
   * @return associative array with prices or empty array
   */
  function parse_price ( $price )
  {
    global $day_regex;
    global $price_regex;

    if ( $price === '' ) {
      return;
    }

    $subject = $price;
    $subject = strtolower ( $subject );
    // Replacing m dashes and other characters
    // Otherwise the parsing did not parse em dash
    $subject = unicode_fix ( $subject );
    $subject = str_replace ('weekday', 'workweek', $subject);
    $subject = str_replace ('non member', 'nonmember', $subject);

    $pattern = "/(?:(?:(?P<start_day>{$day_regex})".
        "(?:\s*-\s*(?P<end_day>{$day_regex})))|" .
        "(?:(?:(?P<day1>(?:{$day_regex}|workweek|weekend|everyday)))";
    for ( $i = 2; $i < 8; ++$i ) {
      $pattern .= "(?:\s*.\s*" .
          "(?P<day$i>(?:{$day_regex}|workweek|weekend|everyday)))?";
    }
    $pattern .= '))' .
        "\s*(?::|-|,)?\s*member\s*(?::|-)?\s*£?(?P<price_member>{$price_regex})".
        "\s*,?\s*nonmember\s*(?::|-)?\s*£?(?P<price_nonmember>{$price_regex})/";
    if (preg_match_all ($pattern, $subject, $matches))
    {
      $count = count ($matches [0]);
      // Fills the prices array
      $prices = array_fill_keys ( range(1 , 7), ['member' => '', 'nonmember' => ''] );
      for ( $i = 0; $i < $count; ++$i )
      {
        // The current matched entry represents a day interval
        if ( $matches['start_day'][$i] !== '' && $matches['end_day'][$i] !== '' )
        {
          $begin = day_to_number ( $matches['start_day'][$i] );
          $end   = day_to_number ( $matches['end_day']  [$i] );
          for ( $j = $begin; $j <= $end; ++$j ) {
            $prices [$j] = set_price ( $matches, $prices[$j], $i );
          }
          continue;
        }
        // The current matched entry represents a sequence of days
        for ( $k = 1; ($k < 8) && ($matches['day'.$k][$i] !== ''); ++$k ) {
          switch ( $matches['day'.$k][$i] ) {
          case 'workweek':
            for ( $l = 1; $l < 6; ++$l ) {
              $prices [$l] = set_price ( $matches, $prices[$l], $i );
            }
            break;
          case 'weekend':
            for ( $l = 6; $l < 8; ++$l ) {
              $prices [$l] = set_price ( $matches, $prices[$l], $i );
            }
            break;
          default:
            $l = day_to_number ( $matches['day'.$k][$i] );
            if ( ! isset ( $l ) ) {
              break;
            }
            $prices [$l] = set_price ( $matches, $prices[$l], $i );
          }
        }
      }

      $days_type_price = '';
      $prices_empty = ! price_check ( $prices );
      if ( $prices_empty === false )
      {
        $days_type_price  = wh_determine_best_view_prices ($prices);
        if ($days_type_price !== 'separately') {
          $prices = wh_times_prices_num_to_assoc ($prices, $days_type_price);
        }
      }

      return $prices;
    }
    return [];
  }

  /**
   * Parse the sports field and extracts sports timetable information from it
   * for all the sports
   * @param sports array with all the sports possible
   * @param sports_club the input field
   * @return associative array with sports and their time and price schedule
   *           or empty array
   */
  function parse_sports ( $sports, $sports_club )
  {
    if ( $sports_club === '' ) {
      return [];
    }

    $subject = $sports_club;
    $subject = strtolower ( $subject );
    // Replacing m dashes and other characters
    // Otherwise the parsing did not parse em dash
    $subject = unicode_fix ( $subject );
    $subject = str_replace ('weekday', 'workweek', $subject);
    $subject = str_replace ('non member', 'nonmember', $subject);

    $sports_club = [];

    foreach ( $sports as $sport )
    {
      if ($sport === 'canoe-kayak') {
        $sport = 'canoeing / kayaking';
      } elseif ($sport === 'synchro-swim') {
        $sport = 'swimming - synchro';
      } elseif ($sport === 'am. football') {
        $sport = 'american football';
      }
      $entry = parse_sport ( $sport, $subject );
      if ( $entry !== [] ) {
        $sports_club [] = $entry;
      }
    }
    return $sports_club;
  }

  /**
   * Parse the sports field and extracts sports timetable information from it
   * for a single sport
   * @param sport the sport that we are searching for
   * @param sports_club the input field
   * @return associative array with the sport and its time and price schedule
   *           or empty array
   */
  function parse_sport ( $sport, $sports_club )
  {
    global $day_regex;
    global $hour_regex;
    global $price_regex;

    $subject = strtolower ($sports_club);

    $interval = '\s*(?::|-|,)?\s*';
    $interval_list = '\s*(?::|,)?\s*';

    $sport['name'] = str_replace( '/', '\/', $sport['name']);
    $sport_pattern = "(?P<sport>{$sport['name']})";

    if ( ! preg_match ("/{$sport_pattern}/", $subject) ) {
      return [];
    }

    $global_pattern = '(?:' .
      "(?:{$interval}(?P<open_time_global>{$hour_regex})\s*-\s*" .
      "(?P<close_time_global>{$hour_regex}))?" .
      "(?:{$interval}member\s*(?::|-)?\s*£?(?P<price_member_global>{$price_regex})".
      "\s*,?\s*nonmember\s*(?::|-)?\s*£?(?P<price_nonmember_global>{$price_regex}))?".
      ')';

    $days_period = "(?:{$interval}" .
      "(?P<start_day>{$day_regex})\s*-\s*(?P<end_day>{$day_regex}))";

    $days_list = '(?:';
    for ( $i = 1; $i < 8; ++$i ) {
      $days_list .= '(?:' . $interval_list .
        "(?P<day$i>(?:{$day_regex}|workweek|weekend|everyday)))";
      if ( $i !== 1 ) {
        $days_list .= '?';
      }
    }
    $days_list .= ')';

    $days_time =
      "(?:{$interval}(?P<open_time>{$hour_regex})\s*-\s*" .
      "(?P<close_time>{$hour_regex}))?";
    $days_prices =
      "(?:{$interval}member\s*(?::|-)?\s*£?(?P<price_member>{$price_regex})" .
      "\s*,?\s*nonmember\s*(?::|-)?\s*£?(?P<price_nonmember>{$price_regex}))?";

    $days_pattern = '(?:(?:' . $days_period . '|' . $days_list . ')' .
                  $days_time . $days_prices . ')';

    $pattern = '/' . $sport_pattern .
      '(?:' . $days_pattern . '+|' . $global_pattern .
      ')/';

    if ( ! preg_match ($pattern, $subject, $matches) ) {
      return [];
    }
    $subject = $matches [0];
    $matches = [];

    if ( preg_match_all ("/{$days_pattern}/", $subject, $matches) )
    {
      $count = count ($matches [0]);
      $times = array_fill_keys ( range(1 , 7), ['open' => '', 'close' => ''] );
      $prices = array_fill_keys ( range(1 , 7), ['member' => '', 'nonmember' => ''] );
      // Loop through the lines
      for ( $i = 0; $i < $count; ++$i )
      {
        // The current matched entry represents a day interval
        if ( $matches['start_day'][$i] !== '' && $matches['end_day'][$i] !== '' )
        {
          $begin = day_to_number ( $matches['start_day'][$i] );
          $end   = day_to_number ( $matches['end_day']  [$i] );
          for ( $j = $begin; $j <= $end; ++$j ) {
            $times [$j] = set_time ( $matches, $times[$j], $i );
            $prices [$j] = set_price ( $matches, $prices[$j], $i );
          }
          continue;
        }
        // The current matched entry represents a sequence of days
        for ( $k = 1; ($k < 8) && ($matches['day'.$k][$i] !== ''); ++$k )
        {
          switch ( $matches['day'.$k][$i] )
          {
          case 'workweek':
            for ( $j = 1; $j < 6; ++$j ) {
              $times [$j] = set_time ( $matches, $times[$j], $i );
              $prices [$j] = set_price ( $matches, $prices[$j], $i );
            }
            break;
          case 'weekend':
            for ( $j = 6; $j < 8; ++$j ) {
              $times [$j] = set_time ( $matches, $times[$j], $i );
              $prices [$j] = set_price ( $matches, $prices[$j], $i );
            }
            break;
          default:
            $j = day_to_number ( $matches['day'.$k][$i] );
            if ( ! isset ( $j ) ) {
              break;
            }
            $times [$j] = set_time ( $matches, $times[$j], $i );
            $prices [$j] = set_price ( $matches, $prices[$j], $i );
          }
        }
      }

      $days_type_time = '';
      $times_empty = ! time_check ( $times );
      if ( $times_empty === false )
      {
        $days_type_time  = wh_determine_best_view_times ($times);
        if ($days_type_time !== 'separately') {
          $times = wh_times_prices_num_to_assoc ($times, $days_type_time);
        }
      } else {
        $times = [ 8 => ['open' => true, 'close' => true] ];
      }

      $days_type_price = '';
      $prices_empty = ! price_check ( $prices );
      if ( $prices_empty === false )
      {
        $days_type_price  = wh_determine_best_view_prices ($prices);
        if ($days_type_price !== 'separately') {
          $prices = wh_times_prices_num_to_assoc ($prices, $days_type_price);
        }
      }

      return [ 'sport' => $sport ['id'],
                'times' => $times,
                'prices' => $prices ];
    }

    if ( preg_match ("/{$sport_pattern}{$global_pattern}/", $subject, $matches) )
    {
      // Fills the times and prices arrays
      // If the global entry in matches array contains a valid time
      if ( isset ($matches['open_time_global']) &&
            isset ($matches['close_time_global']) &&
            $matches['open_time_global'] !== '' &&
            $matches['close_time_global'] !== '' )
      {
          $times =  [ 8 =>
              [ 'open'  => $matches['open_time_global'],
                'close' => $matches['close_time_global'] ] ];
      }
      else
      {  // Select all days without specifying time
        $times =  [ 8 => [ 'open'  => true, 'close' => true ] ];
      }
      // If the global entry in matches array contains a valid price
      $prices = ['member' => '', 'nonmember' => ''];
      if ( isset ($matches['price_member_global']) &&
            $matches['price_member_global'] !== '' ) {
        $prices ['member'] = $matches['price_member_global'];
      }
      if ( isset ($matches['price_nonmember_global']) &&
            $matches['price_nonmember_global'] !== '' ) {
        $prices ['nonmember'] = $matches['price_nonmember_global'];
      }
      foreach ( $prices as &$price ) {
        if ( $price === 'free' ) {
          $price = '0';
        }
      }
      unset ($price);
      $prices = [ 8 => $prices ];

      return [ 'sport' => $sport ['id'],
                'times' => $times,
                'prices' => $prices ];
    }

    return [];
  }

  /**
   * Process the time field for Club Sport Edinburgh and extracts timetable
   * information from it, filling the times array.
   * Unlike parse_time, no regular expression matching is done.
   * @param times array with the input fields
   * @return associative array with times or empty array
   */
  function process_time_club_sport_edinburgh ( $times )
  {
    global $hour_regex;
    foreach ( $times as &$time )
    {
      if ( $time === '' ) {
        $time = [ 'open'  => null, 'close' => null];
        continue;
      }
      $subject = $time;
      $subject = strtolower ( $subject );
      // Replacing m dashes and other characters
      // Otherwise the parsing did not parse em dash
      $subject = unicode_fix ( $subject );

      $pattern =
        "/(?P<open_time>{$hour_regex})\s*-\s*" .
        "(?P<close_time>{$hour_regex})/";
      if ( preg_match ($pattern, $subject, $matches) &&
            isset ($matches['open_time']) &&
            isset ($matches['close_time']) &&
            $matches['open_time'] !== '' &&
            $matches['close_time'] !== '' )
      {
        $time = [ 'open'  => $matches['open_time'],
                  'close' => $matches['close_time'] ];
      } else {
        $time = [ 'open'  => null, 'close' => null];
      }
    }
    unset ($time);
    return $times;
  }

  /**
   * Process the sports field for Club Sport Edinburgh.
   * If a sport is not in the database in the sports table,
   * it inserts it to it and to the sports array.
   * Unlike parse_sports, no regular expression matching is done.
   * It is assumed that there is a single sport because of the xml structure.
   * @param sports array with all the sports in the database
   * @param sports_club the input field
   * @return associative array with sports IDs and names
   */
  function process_sports_club_sport_edinburgh ( &$sports, $sports_club )
  {
    if ( $sports_club === '' ) {
      return [];
    }

    $subject = $sports_club;
    $subject = strtolower ( $subject );
    // Replacing m dashes and other characters
    // Otherwise the parsing did not parse em dash
    $subject = unicode_fix ( $subject );
    $entry = $subject;
    $sports_club = [];

    if ($entry === 'canoeing / kayaking') {
      $entry = 'canoe-kayak';
    } elseif ($entry === 'swimming - synchro') {
      $entry = 'synchro-swim';
    } elseif ($entry === 'american football') {
      $entry = 'am. football';
    } elseif (strlen($entry) > 16) {
      error_log('Sport too long and omitted - ' . $entry . "\n", 0);
    }

    foreach ( $sports as $sport )
    {
      if ( $sport ['name'] === $entry ) {
        $sports_club [] = $sport;
        return $sports_club;  // because there is a single sport
      }
    }

    $data = [ 'name' => $entry ];
    wh_db_perform ( TABLE_SPORTS, $data, 'insert' );
    $id = wh_db_insert_id ();
    $sports [] = [ 'id' => $id, 'name' => $entry ];
    $sports_club [] = [ 'id' => $id, 'name' => $entry ];
    return $sports_club;
  }

?>
