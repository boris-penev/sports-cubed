<?php

  require('includes/application_top.php');
  require(DIR_WS_FUNCTIONS . 'local/parser.php');

  $xml = loadXML ( URL_XML_COUNCIL_EDINBURGH );
  $query = build_query_council_edinburgh ( 'club' );
//  $query = buildQuery ( 'sport' ) . buildQuery ( 'time' );
  create_temporary_tables ();
  process_clubs_council_edinburgh ($xml, $query);
  $xml = loadXML ( URL_XML_CLUB_SPORT_EDINBURGH );
  process_clubs_club_sport_edinburgh ($xml);
  change_production_tables ();

  require(DIR_WS_INCLUDES . 'application_bottom.php');

?>
