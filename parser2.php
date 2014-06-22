<?php

  require('includes/application_top.php');
  require(DIR_WS_FUNCTIONS . 'local/parser.php');

  // as of PHP 5.4
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

  $sports_var = wh_db_get_input_string ( 'sport' );
  $time_var = wh_db_get_input_string ( 'time' );

  if ( isset ( $sports_var ) )
    $sports_var = explode ( ' ', $sports_var );
  if ( isset ( $time_var ) )
    $time_var = explode ( ' ', $time_var );

  $xml = curl_get_html_file_contents_custom (
    'http://www.edinburgh.gov.uk/api/directories/25/entries.xml?api_key=' .
    COUNCIL_API_KEY . '&per_page=100&page=1' );

  $xml = new SimpleXMLElement($xml);

  if ( ! isset ( $sports_var ) )
  {
    $query1 = '';
  }
  else
  {
    $query1 = "[fields/field[@name='Activities']";

    foreach ( $sports_var as $sport )
    {
      $query1 .= "[contains(text(),'$sport')]";
    }

    $query1 .= ']';
  }

  if ( ! isset ( $time_var ) )
  {
    $query2 = '';
  }
  else
  {
    $query2 = "[fields/field[@name='Opening hours']";

    foreach ( $time_var as $time_t )
    {
      $query2 .= "[contains(text(),'$time_t')]";
    }

    $query2 .= ']';
  }

  //echo '//entry' . $query1 . $query2;

  //$count = 0;
  $arr = array ( );

  foreach ( $xml->xpath( '/entries/entry' . $query1 . $query2 ) as $club )
  {
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
    $arr[] = $current;

  }

# echo json_encode ( $arr );
  var_dump ($arr);
# var_export($arr);

  require(DIR_WS_INCLUDES . 'application_bottom.php');

?>
