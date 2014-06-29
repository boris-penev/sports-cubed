<?php

  require('includes/application_top.php');
  require(DIR_WS_FUNCTIONS . 'local/parser.php');

  $xml = loadXML ();
  $query = buildQuery ( 'club' );
  process_clubs ($xml, $query);

  require(DIR_WS_INCLUDES . 'application_bottom.php');

?>
