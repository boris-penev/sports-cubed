<?php

  header ("Access-Control-Allow-Origin: *");

# define('DB_DATABASE', 'clubs');
  //cp query.php ..; cp -r includes ..;
  require('includes/application_top.php');

//  $sports = wh_db_fetch_all_custom ( getSports ( ), MYSQLI_ASSOC );
  $sports = [];
  $sports_query = getSports ();

  while ( $sport = wh_db_fetch_assoc_custom ($sports_query) )
  {
    $sports [] = $sport ['name'];
  }
  echo json_encode ( $sports );

  require(DIR_WS_INCLUDES . 'application_bottom.php');

?>
