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
    'opening_time' => '',
    'closing_time' => '',
    'price_member' => '',
    'price_nonmember' => ''
  ];

  $day = '(?:Monday|Tuesday|Wednesday|Thursday|Friday|Saturday|Sunday'
       . '|Mon|Tue|Wed|Thu|Fri|Sat|Sun)';
  $hour = '(?:\d\d?(?:(?::|\.)\d\d)?\s*(?:am|pm)?)|(?:12(?:(?::|\.)\d\d)?\s*(?:noon)?)';

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
//    CLI equivalents
//    This will be removed but can be found with `$ hg grep`
//    wget --quiet --output-document=- \'.$url.\' > /dev/null
//    curl --silent \'.$url.\' > /dev/null
//     $out = array();
//     $status = -1;
//     exec( '/path/to/sync.sh', $out, $status );
//     if ( $status != 0 ) {
//         // shell script indicated an error return
//     }
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
    $prices = str_replace ( "\n", ', ', $prices );
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
    foreach ( $xml->xpath('/entries/entry' . $query) as $club )
    {
      $current_club = process_current_club ($club);
      parse_time ($current_club);
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

  function parse_time ( $club )
  {
    global $day;
    global $hour;

    echo '<p><strong>' . wh_output_string_protected ($club ['name']) .
         '</strong></p>' . PHP_EOL;

    if ( $club ['time'] === '' ) {
      return;
    }

#   var_dump($club ['time']);

    echo '<p>' . nl2br (wh_output_string_protected ($club ['time'])) .
         '</p>' . PHP_EOL;

    $subject = $club['time'];
    $pattern = '/(?:(?P<start_day>'.$day.')(?:\s*-\s*(?P<end_day>'.$day.'))?'.
        '|Workweek|Weekend)' .
        '(?:\s*(?::|,)\s*(?P<open_time>'.$hour.')\s*-\s*(?P<close_time>'.
        $hour.'))?/';
#   var_dump (wordwrap($pattern, 80, PHP_EOL, TRUE));
    if (preg_match_all ($pattern, $subject, $matches)) {
#     var_dump($matches[0]);
      echo '<p style="color:red">';
      foreach ($matches[0] as $match) {
        echo '' . wh_output_string_protected ($match) .
         '<br />' . PHP_EOL;
      }
      echo '</p>' . PHP_EOL;
    }

  }

?>
