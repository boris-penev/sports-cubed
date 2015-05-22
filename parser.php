<?php

  require('includes/application_top.php');
  require(DIR_WS_FUNCTIONS . 'local/parser.php');

  $xmls = ['council_edinburgh.xml' => URL_XML_COUNCIL_EDINBURGH,
            'club_sport_edinburgh.xml' => URL_XML_CLUB_SPORT_EDINBURGH];

  if ( update_xmls ( $xmls ) === false ) {
    $message = 'XMLs are the same, database not updated';
    if (php_sapi_name() === 'cli') {
      echo $message;
    } else {
      echo '<div style="color:green">',
            '<h1>', $message, '</h1>', PHP_EOL,
            '</div>';
    }
    error_log($message . "\n", 0);
    return 0;
  }

  $message = 'Updating databases';
  if (php_sapi_name() === 'cli') {
    echo $message;
  } else {
    echo '<div style="color:green">',
          '<h1>', $message, '</h1>', PHP_EOL,
          '</div>';
  }
  error_log($message . "\n", 0);

  $xml = loadXML ( 'council_edinburgh.xml' );
  $query = build_query_council_edinburgh ( 'club' );
//  $query = buildQuery ( 'sport' ) . buildQuery ( 'time' );
  create_temporary_tables ();
  process_clubs_council_edinburgh ($xml, $query);
  $xml = loadXML ( 'club_sport_edinburgh.xml' );
  process_clubs_club_sport_edinburgh ($xml);
  change_production_tables ();

  require(DIR_WS_INCLUDES . 'application_bottom.php');

?>
