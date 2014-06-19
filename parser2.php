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

  if ( isset ( $_GET [ 'sport' ] ) )
    $sports_var = $_GET [ 'sport' ];
  if ( isset ( $_GET [ 'time' ] ) )
    $time_var = $_GET [ 'time' ];

  if ( isset ( $sports_var ) )
    $sports_var = explode ( ' ', $sports_var );
  if ( isset ( $time_var ) )
    $time_var = explode ( ' ', $time_var );

  $sports = curl_get_html_file_contents_custom (
    'http://www.edinburgh.gov.uk/api/directories/25/entries.xml?api_key=' .
    COUNCIL_API_KEY . '&per_page=100&page=1' );

  $sports = new SimpleXMLElement($sports);

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

  foreach ( $sports->xpath( '//entry' . $query1 . $query2 ) as $club )
  {
    $current = $club_init;
    $address = $club->xpath('fields/field[@name=\'Address\']/text()');
    $location = $club->xpath('fields/field[@name=\'Location\']/text()');
    $sports_x = $club->xpath('fields/field[@name=\'Activities\']/text()');
    $time_x = $club->xpath('fields/field[@name=\'Opening hours\']/text()');
    $sports_x = count ( $sports_x ) > 0 ? (string) $sports_x [ 0 ] : '';
    $time_x = count ( $time_x ) > 0 ? (string) $time_x [ 0 ] : '';

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
      else
      {
        $current['latitude'] = '';
        $current['longtitude'] = '';
      }
    }

    if ( count ( $address ) > 0 )
    {
      //echo $club->title, '  at  address ', $address [0];
      $current['name'] = (string) $club->title;
      $current['address'] = (string) $address[0];
    }
    else
    {
      //echo $club->title;
      $current['name'] = (string) $club->title;
      $current['address'] = '';
    }
      //echo '<br />', PHP_EOL;
      /*if ( isset ( $sports_var ) )
      {
      //echo 'provides fields for ',  implode ( ' ', $sports_var ),
      //'<br />', PHP_EOL;
      $current['sports'] = implode ( ' ', $sports_var );
    }
    else
    {
      $current['sports'] = '';
    }*/
    $sports_x = str_replace ( "\r", '', $sports_x );
    $sports_x = str_replace ( "\n", ', ', $sports_x );
    $current['sports'] = $sports_x;
    /*if ( isset ( $time_var ) )
      {
      //echo 'at time ',  implode ( ' ', $time_var ),
      //'<br />', PHP_EOL;
      $current['time'] = implode ( ' ', $time_var );
    }
    else
    {
      $current['time'] = '';
    }*/
    $current['time'] = $time_x;
    //$arr[$count++] = $current;
    $arr[] = $current;

  }

  echo json_encode ( $arr );

  require(DIR_WS_INCLUDES . 'application_bottom.php');

?>
