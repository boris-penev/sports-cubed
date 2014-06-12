<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require(DIR_WS_LANGUAGES . $language . '/' . 'add_sport.php');

  require(DIR_WS_FUNCTIONS . 'local/add_sport.php');
# header("Location: " . DIR_WS_FUNCTIONS . 'local/add_sport.php');

  require(DIR_WS_INCLUDES . 'template_top.php');

 // page for add club
?>

<h1><?php echo HEADING_TITLE; ?></h1>

<div class="contentContainer">

<?php
    if (defined ('TEXT_SUCCESS')) {
?>

  <div class="contentText">
    <div class="notification">
    <?php echo TEXT_SUCCESS; ?>
    </div>
  </div>

<?php
    }
    if (defined ('TEXT_ERROR')) {
?>

  <div class="contentText">
    <div class="notification">
    <?php echo TEXT_ERROR; ?>
    </div>
  </div>

<?php
    }
    if ( wh_defined ('TEXT_MAIN') ) {
?>

  <div class="contentText">
    <?php echo TEXT_MAIN; ?>
  </div>

<?php
    }
?>
  <form action="add_sport.php" method="post">
<?php
    echo wh_draw_hidden_field ( 'action', 'add' );
    echo wh_draw_input_field_label ( 'sport', 'sport', 'Sport', '', '',
      'autofocus = "autofocus" required = "required" placeholder = "enter sport"',
      'text', true, 2, true );
?>
    <input type="submit" />
  </form>

  <form action="add_sport.php" method="post">
<?php
    echo wh_draw_hidden_field ( 'action', 'delete' );
?>
    <table style="margin-top:20px">
      <tr>
        <td style="padding-bottom:10px">Sports:</td>
      </tr>
<?php
    $sports_query = getSportsOrderByName ( );
    while ($row_obj = wh_db_fetch_object_custom($sports_query))
    {
?>
      <tr>
          <td><?php echo $row_obj->name; ?></td>
          <td style='padding-left:25px'>
            <input type="submit" value="delete" name="<?php echo $row_obj->id; ?>"/>
          </td>
      </tr>
<?php
    }
?>
    </table>
  </form>

</div> <!-- <div class="contentContainer"> -->

<?php

  require(DIR_WS_INCLUDES . 'template_bottom.php');
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
