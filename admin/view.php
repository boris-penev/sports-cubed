<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require(DIR_WS_LANGUAGES . $language . '/' . 'view.php');

  require(DIR_WS_INCLUDES . 'template_top.php');

 // default page
?>

<h1><?php echo HEADING_TITLE; ?></h1>

<div class="contentContainer">

<?php
    if (wh_not_null(TEXT_MAIN)) {
?>

  <div class="contentText">
    <?php echo TEXT_MAIN; ?>
  </div>

<?php
    }
?>

  <div class="contentText">
    <table>
      <tr>
        <td>Name</td>
        <td>Address</td>
        <td>Postcode</td>
        <td>Latitude</td>
        <td>Longtitude</td>
        <td>Comment</td>
        <td>Sports</td>
      </tr>
<?php
    $club_query = getClubsOrderById ( );
    while ($row_obj = wh_db_fetch_object_custom($club_query))
    {
      $sports_query = getSportsByClub ( $row_obj->id );
      $clubosports = array ();
      while ( $sport = wh_db_fetch_row_custom($sports_query,
        MYSQLI_NUM)[0] ) {
        $clubosports [] = $sport;
      }
//      $clubosports = wh_db_fetch_all_custom($sports_query, MYSQLI_NUM);
      $row_obj->sports = implode ( ", ", $clubosports );
?>
      <tr>
        <td><?php echo $row_obj->name; ?></td>
        <td><?php echo $row_obj->address; ?></td>
        <td><?php echo $row_obj->postcode; ?></td>
        <td><?php echo $row_obj->latitude; ?></td>
        <td><?php echo $row_obj->longtitude; ?></td>
        <td><?php echo $row_obj->comment; ?></td>
        <td><?php echo $row_obj->sports; ?></td>
      </tr>
<?php
    }
?>
    </table>
  </div>

</div>

<?php

  require(DIR_WS_INCLUDES . 'template_bottom.php');
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
