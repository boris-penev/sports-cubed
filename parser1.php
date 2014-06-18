<?php

  require('includes/application_top.php');
  require(DIR_WS_FUNCTIONS . 'local/parser.php');

  $sports = curl_get_html_file_contents_custom (
    'http://www.edinburgh.gov.uk/api/directories/25/entries.xml?api_key=' .
    COUNCIL_API_KEY . '&per_page=100&page=1' );

  $sports = new SimpleXMLElement($sports);

  foreach ( $sports->xpath( '//entry[fields/field[@name=\'Activities\'][contains(text(),\'Football\')]][fields/field[@name=\'Opening hours\'][contains(text(),\'Monday\')]]' ) as $club )
  {
    $address = $club->xpath('fields/field[@name=\'Address\']/text()');
    if ( count ( $address ) )
      echo $club->title, '  at  ', $address [0], '<br />', PHP_EOL;
    else
      echo $club->title, '<br />', PHP_EOL;
  }

  require(DIR_WS_INCLUDES . 'application_bottom.php');

?>
