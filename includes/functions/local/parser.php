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
  $hour_regex = '(?:\d\d?(?:(?::|\.)\d\d)?\s*(?:am|pm)?)|(?:12(?:(?::|\.)\d\d)?\s*(?:noon)?)';
  $price_regex = '(?:Free|(?:\d+(?:\.\d\d)*))';

  function curl_get_file_contents_custom($URL)
  {
    $c = curl_init();
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_URL, $URL);
    $contents = curl_exec($c);
    curl_close($c);

    if ($contents)
      return $contents;
    else
      return FALSE;
  }

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

  function get_first_element ( $entity )
  {
    return count ( $entity ) > 0 ?
              (string) $entity[0] : '';
  }

  function buildQuery ( $entity )
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
      if ( isset ( $sports ) ) {
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
      if ( isset ( $times ) ) {
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

  function loadXML ( )
  {
    $xml = curl_get_html_file_contents_custom (
      'http://www.edinburgh.gov.uk/api/directories/25/entries.xml?api_key=' .
      COUNCIL_API_KEY . '&per_page=100&page=1' );
#     'http://localhost/database/entries.edit.xml' );

    try {
      return new SimpleXMLElement($xml);
    } catch (Exception $e) {
      $error = 'The XML could not be loaded.<br />' .
               'Possibly the contacted server is down.';
      wh_error ( $error );
    }
  }

  function process_current_club ( $club )
  {
    global $club_init;
    $current = $club_init;
    $address = $club->xpath('fields/field[@name=\'Address\']/text()');
    $postcode = $club->xpath('fields/field[@name=\'Postcode\']/text()');
    $location = $club->xpath('fields/field[@name=\'Location\']/text()');
    $email = $club->xpath('fields/field[@name=\'Email\']/text()');
    $phone = $club->xpath('fields/field[@name=\'Telephone\']/text()');
    $sports = $club->xpath('fields/field[@name=\'Activities\']/text()');
    $facilities = $club->xpath('fields/field[@name=\'Facilities\']/text()');
    $times = $club->xpath('fields/field[@name=\'Opening hours\']/text()');
    $prices = $club->xpath('fields/field[@name=\'Prices\']/text()');
    $website = $club->xpath('fields/field[@name=\'Timetables\']/text()');
    $comment = $club->xpath('fields/field[@name=\'More information\']/text()');
    $current['name'] = (string) $club->title;

    if ( count ( $location ) > 0 && strlen ( $location[0] ) > 1 )
    {
      $location = explode ( ',', $location[0] );
      if ( count ( $location ) == 2 )
      {
        $latitude = $location [ 0 ];
        $longtitude = $location [ 1 ];
        $current['latitude'] = (double) $latitude;
        $current['longtitude'] = (double) $longtitude;
      }
    }
#   echo $club->title, '  at  address ', $address [0];
    $current['address'] = get_first_element ($address);
    $current['postcode'] = get_first_element ($postcode);
    $current['email'] = get_first_element ($email);
    $current['phone'] = get_first_element ($phone);
    $current['website'] = get_first_element ($website);
    $sports = get_first_element ($sports);
    $facilities = get_first_element ($facilities);
    $times = get_first_element ($times);
    $prices = get_first_element ($prices);
    $comment = get_first_element ($comment);
    $sports = str_replace ( "\r", '', $sports );
    $sports = str_replace ( "\n", ', ', $sports );
    $facilities = str_replace ( "\r", '', $facilities );
    $facilities = str_replace ( "\n", ', ', $facilities );
    $times = str_replace ( "\r", '', $times );
#   $times = str_replace ( "\n", ', ', $times );
    $prices = str_replace ( "\r", '', $prices );
#   $prices = str_replace ( "\n", ', ', $prices );
#   $comment = str_replace ( "\r", '', $comment );
#   $comment = str_replace ( "\n", ', ', $comment );
    $current['sports'] = $sports;
    $current['facilities'] = $facilities;
    $current['time'] = $times;
    $current['price'] = $prices;
    $current['comment'] = $comment;
    /*if ( isset ( $time_var ) )
      {
      //echo 'at times ',  implode ( ' ', $times ),
      //'<br />', PHP_EOL;
      $current['time'] = implode ( ' ', $times );
    }
    else
    {
      $current['time'] = '';
    }*/
    //$arr[$count++] = $current;
    return $current;
  }

  function process_clubs ( $xml, $query )
  {
    $arr = [];
    $sports = wh_db_fetch_all_custom ( getSports ( ), MYSQLI_ASSOC );
    delete_clubs ();
    foreach ( $xml->xpath('/entries/entry' . $query) as $club )
    {
      $current_club = process_current_club ($club);

      $data = $current_club;

      // These are raw unparsed fields and should not be submitted
      unset ($data['time']);
      unset ($data['price']);
      unset ($data['sports']);
      unset ($data['facilities']);

      echo '<p><strong>' . wh_output_string_protected ($current_club ['name']) .
           '</strong></p>' . PHP_EOL;

      foreach ( $arr as $club_t ) {
        if ( $club_t ['name'] == $current_club['name'] ) {
#         wh_error ('There is another club with the same name');
          $error = 'There is another club with the same name';
          echo '<div style="color:red">',
                '<h1>' . nl2br ( $error ) . '</h1>', PHP_EOL,
                '</div>';
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

      wh_db_perform ( 'clubs', $data, 'insert' );
      $id = wh_db_insert_id ();
      $data = [];

      // TODO The queries can be made in one query

      if ( (count ($times) > 0) && (! isset ($times [8])) )
      {
        $data ['club_id'] = $id;
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
          wh_db_perform ( 'club_schedule', $data, 'insert' );
        }
      }

      unset ($data ['opening_time']);
      unset ($data ['closing_time']);

      if ( (count ($prices) > 0) && (! isset ($prices [8])) )
      {
        $data ['club_id'] = $id;
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
          wh_db_perform ( 'club_schedule', $data,
                          'insert on duplicate key update' );
        }
      }

      $sports_club = parse_sports ($sports, $current_club ['sports']);

      $arr[] = $current_club;
#     var_dump ($current_club);
    }
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
          preg_replace ( "/(?:\\n)+/", "\n",
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
#   return (int) date('N', strtotime($matches['left']));
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
      if ( $price === 'Free' ) {
        $price = '0';
      }
    }
    unset ($price);
    return $prices;
  }

  /**
   * @return false if empty or true otherwise
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
          if ( strpos ( $time, ':' ) === false ) {
            $format = 'ga';
          } else {
            $format = 'g:ia';
          }
        } else {
          if ( strpos ( $time, ':' ) === false ) {
            $format = 'h';
          } else {
            $format = 'h:i';
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
    echo '<p><strong>times:</strong></p>';
    for ( $i = 1; $i < 8; ++$i )
    {
      echo '<span style="color:initial">',
            '[', $i, '] ', '</span>';
      foreach ( $times [$i] as $key => $time )
      {
        if ( $time !== '' && $time !== true ) {
          echo $key, ' - ',
              '<span style="color:#E80000;margin:0.5%">', $time, ' </span>';
        }
      }
      echo '<br />';
    }
    return ! $empty;
  }

  /**
   * @return false if empty or true otherwise
   */
  function price_check ( &$prices )
  {
    // flag indicating whether there are valid prices
    $empty = true;
    for ( $i = 1; $i < 8; ++$i )
    {
      foreach ( $prices [$i] as $price )
      {
        if ( $price === '' ) {
          continue;
        }
        $empty = false;
        break 2;
#       return true;
      }
    }
    return false;
  }

  function parse_time ( $time )
  {
    global $day_regex;
    global $hour_regex;

    if ( $time === '' ) {
      return;
    }

    echo '<p>' . nl2br (wh_output_string_protected ($time)) .
         '</p>' . PHP_EOL;

    $subject = $time;
    $subject = strtolower ( $subject );
    // Replacing m dashes and other characters
    // Otherwise the parsing did not parse em dash
    $subject = unicode_fix ( $subject );
    $subject = str_replace ('weekday', 'workweek', $subject);

    $pattern = '/(?:(?:(?P<start_day>'.$day_regex.')(?:\s*-\s*(?P<end_day>'.$day_regex.'))'.
        ')|' .
        '(?:(?:(?P<day1>(?:'.$day_regex.'|workweek|weekend|everyday)))';
    for ( $i = 2; $i < 8; ++$i ) {
      $pattern .= "(?:\s*.\s*(?P<day$i>(?:".$day_regex."|workweek|weekend|everyday)))?";
    }
    $pattern .= '))' .
        '(?:\s*(?::|,)\s*(?P<open_time>'.$hour_regex.')\s*-\s*(?P<close_time>'.
        $hour_regex.'))?/';
#   var_dump (wordwrap($pattern, 80, PHP_EOL, TRUE));
#   echo nl2br ( wordwrap ( wh_output_string_protected
#         ($pattern), 80, PHP_EOL, TRUE));
    if (preg_match_all ($pattern, $subject, $matches))
    {
#     var_dump($matches[0]);
      $count = count ($matches [0]);
      echo '<p style="color:#E80000">';
      foreach ($matches[0] as $key => $match) {
        echo '<span style="color:initial">', ' [', $key, '] ', '</span>';
        echo wh_output_string ($match), '<br />', PHP_EOL;
      }
      foreach ($matches as $key => $match) {
        if ( is_numeric ($key) ) {
          continue;
        }
        foreach ( $match as $key2 => $value ) {
          if ( $value === '' ) continue;
          echo '<span style="color:initial">', $key,
              ' [', $key2, ']', ' - ', '</span>';
          echo wh_output_string ($value), '<br />' . PHP_EOL;
        }
      }
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
      echo '</p>' . PHP_EOL;

      $days_type_time = '';
      $times_empty = ! time_check ( $times );
      if ( $times_empty === false )
      {
        $days_type_time  = wh_determine_best_view_times ($times);
        if ($days_type_time !== 'separately') {
          $times = wh_times_prices_num_to_assoc ($times, $days_type_time);
        }
      }

      echo '<p><strong>times:</strong></p>';
      foreach ( $times as $time_key => $time_day )
      {
        echo '<span style="color:initial">',
              '[', $time_key, '] ', '</span>';
        foreach ( $time_day as $key => $time )
        {
          if ( $time !== '' && $time !== true ) {
            echo $key, ' - ',
                '<span style="color:#E80000;margin:0.5%">', $time, ' </span>';
          }
        }
        echo '<br />';
      }

      return $times;
    }
    return [];
  }

  function parse_price ( $price )
  {
    global $day_regex;
    global $price_regex;

    if ( $price === '' ) {
      return;
    }

    echo '<p>' . nl2br (wh_output_string_protected ($price)) .
         '</p>' . PHP_EOL;

    $subject = $price;
    $subject = strtolower ( $subject );
    // Replacing m dashes and other characters
    // Otherwise the parsing did not parse em dash
    $subject = unicode_fix ( $subject );
    $subject = str_replace ('weekday', 'workweek', $subject);
    $subject = str_replace ('non member', 'nonmember', $subject);

    $pattern = '/(?:(?:(?P<start_day>'.$day_regex.')(?:\s*-\s*(?P<end_day>'.$day_regex.'))'.
        ')|' .
        '(?:(?:(?P<day1>(?:'.$day_regex.'|workweek|weekend|everyday)))';
    for ( $i = 2; $i < 8; ++$i ) {
      $pattern .= "(?:\s*.\s*(?P<day$i>(?:".$day_regex."|workweek|weekend|everyday)))?";
    }
    $pattern .= '))' .
        '\s*(?::|-|,)?\s*member\s*(?::|-)?\s*£?(?P<price_member>'.$price_regex.')'.
        '\s*,?\s*nonmember\s*(?::|-)?\s*£?(?P<price_nonmember>'.$price_regex.')/';
#   var_dump (wordwrap($pattern, 80, PHP_EOL, TRUE));
#   echo nl2br ( wordwrap ( wh_output_string_protected
#         ($pattern), 80, PHP_EOL, TRUE));
    if (preg_match_all ($pattern, $subject, $matches))
    {
#     var_dump($matches[0]);
      $count = count ($matches [0]);
      echo '<p style="color:#E80000">';
      foreach ($matches[0] as $key => $match) {
        echo '<span style="color:initial">', ' [', $key, '] ', '</span>';
        echo wh_output_string ($match), '<br />', PHP_EOL;
      }
      foreach ($matches as $key => $match) {
        if ( is_numeric ($key) ) {
          continue;
        }
        foreach ( $match as $key2 => $value ) {
          if ( $value === '' ) continue;
          echo '<span style="color:initial">', $key,
              ' [', $key2, ']', ' - ', '</span>';
          echo wh_output_string ($value), '<br />' . PHP_EOL;
        }
      }
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
      echo '</p>' . PHP_EOL;

      $days_type_price = '';
      $prices_empty = ! price_check ( $prices );
      if ( $prices_empty === false )
      {
        $days_type_price  = wh_determine_best_view_prices ($prices);
        if ($days_type_price !== 'separately') {
          $prices = wh_times_prices_num_to_assoc ($prices, $days_type_price);
        }
      }

      echo '<p><strong>prices:</strong></p>';
      foreach ( $prices as $price_key => $price_day )
      {
        echo '<span style="color:initial">',
              '[', $price_key, '] ', '</span>';
        foreach ( $price_day as $key => $price )
        {
          if ( $price !== '' && $price !== true ) {
            echo $key, ' - ',
                '<span style="color:#E80000;margin:0.5%">', $price, ' </span>';
          }
        }
        echo '<br />';
      }

      return $prices;
    }
    return [];
  }

  function parse_sports ( $sports, $sports_club )
  {
    if ( $sports_club === '' ) {
      return;
    }
    echo '<p>' . nl2br (wh_output_string_protected ($sports)) .
         '</p>' . PHP_EOL;

    $subject = $sports_club;
    $subject = strtolower ( $subject );
    // Replacing m dashes and other characters
    // Otherwise the parsing did not parse em dash
    $subject = unicode_fix ( $subject );
    $subject = str_replace ('weekday', 'workweek', $subject);
    $subject = str_replace ('non member', 'nonmember', $subject);

    $sports_club = [];

    for ( $sport in $sports )
    {
      $entry = $parse_sport ( $sport, $subject );
      if ( $entry != [] ) {
        $sports_club [] = $entry;
      }
    }
    return $sports_club;
  }

  function parse_sport ( $sport, $sports_club )
  {
    global $day_regex;
    global $time_regex;
    global $price_regex;

    $pattern = '/' . $sport .
        '(?:(?:(?P<start_day>'.$day_regex.')(?:\s*-\s*(?P<end_day>'.$day_regex.'))'.
        ')|' .
        '(?:(?:(?P<day1>(?:'.$day_regex.'|workweek|weekend|everyday)))';
    for ( $i = 2; $i < 8; ++$i ) {
      $pattern .= "(?:\s*.\s*(?P<day$i>(?:".$day_regex."|workweek|weekend|everyday)))?";
    }
    $pattern .= '))' .
        '(?:\s*(?::|,)\s*(?P<open_time>'.$hour_regex.')\s*-\s*(?P<close_time>'.
        $hour_regex.'))?' .
        '(?:\s*(?::|-|,)?\s*member\s*(?::|-)?\s*£?(?P<price_member>'.$price_regex.')'.
        '\s*,?\s*nonmember\s*(?::|-)?\s*£?(?P<price_nonmember>'.$price_regex.'))?/';
#   var_dump (wordwrap($pattern, 80, PHP_EOL, TRUE));
#   echo nl2br ( wordwrap ( wh_output_string_protected
#         ($pattern), 80, PHP_EOL, TRUE));
    if (preg_match_all ($pattern, $subject, $matches))
    {
      // TODO Write code here
      return $sports;
    }
    return [];
  }

?>
