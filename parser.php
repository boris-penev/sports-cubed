<?php

  require('includes/application_top.php');
  require(DIR_WS_FUNCTIONS . 'local/parser.php');

  $xmls = [ 'council_edinburgh.xml' => URL_XML_COUNCIL_EDINBURGH,
          'club_sport_edinburgh.xml' => URL_XML_CLUB_SPORT_EDINBURGH ];
  if ( update_xmls ( $xmls ) === false ) {
    die;
  }
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
