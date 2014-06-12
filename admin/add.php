<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require(DIR_WS_LANGUAGES . $language . '/' . 'add.php');

  require(DIR_WS_FUNCTIONS . 'local/add.php');

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

  <form action="add.php" method="post">
<?php
    echo wh_draw_input_field_label ( 'name', 'name', 'Club Name', '', '',
      'autofocus = "autofocus" required = "required" placeholder = "enter club name"',
      'text', true, 2, true );
    echo wh_draw_input_field_label ( 'address', 'address', 'Address', '',
      '', '', 'text', true, 2, true );
    echo wh_draw_input_field_label ( 'postcode', 'postcode', 'Postcode', '',
      '', '', 'text', true, 2, true );
    echo wh_draw_input_field_label ( 'latitude', 'latitude', 'Latitude', '',
      '', '', 'text', true, 2, true );
    echo wh_draw_input_field_label ( 'longtitude', 'longtitude', 'Longtitude', '',
      '', '', 'text', true, 2, true );
    echo wh_draw_input_field_label ( 'website', 'website', 'Website', '',
      '', 'maxlength="200"', 'text', true, 2, true );
    echo wh_draw_input_field_label ( 'email', 'email', 'Email', '',
      '', 'maxlength="100"', 'text', true, 2, true );
    echo wh_draw_input_field_label ( 'phone', 'phone', 'Contact phone', '',
      '', 'maxlength="100"', 'text', true, 2, true );
    echo wh_draw_textarea_field_label ( 'comment', 'comment', 'Comment', '',
      30, 3, '', 'maxlength="4000" spellcheck = "true"', true, 2, true );
    echo wh_draw_input_field_label ( 'time_open', 'time_open', 'Opening time', '',
      '', '', 'time', true, 2, true );
    echo wh_draw_input_field_label ( 'time_close', 'time_close', 'Closing time', '',
      '', '', 'time', true, 2, true );
    echo wh_draw_input_field_label ( 'price_member', 'price_member', 'Price Member', '',
      '', '', 'text', true, 2, true );
    echo wh_draw_input_field_label ( 'price_nonmember', 'price_nonmember', 'Price Non-Member', '',
      '', '', 'text', true, 2, true );
    echo wh_draw_input_field_label ( 'everyday', 'everyday', 'All days', '',
      '', '', 'checkbox', true, 2, false );
    echo wh_draw_input_field_label ( 'monday', 'monday', 'Monday', '',
      '', '', 'checkbox', true, 2, false );
    echo wh_draw_input_field_label ( 'tuesday', 'tuesday', 'Tuesday', '',
      '', '', 'checkbox', true, 2, false );
    echo wh_draw_input_field_label ( 'wednesday', 'wednesday', 'Wednesday', '',
      '', '', 'checkbox', true, 2, true );
    echo wh_draw_input_field_label ( 'thursday', 'thursday', 'Thursday', '',
      '', '', 'checkbox', true, 2, false );
    echo wh_draw_input_field_label ( 'friday', 'friday', 'Friday', '',
      '', '', 'checkbox', true, 2, false );
    echo wh_draw_input_field_label ( 'saturday', 'saturday', 'Saturday', '',
      '', '', 'checkbox', true, 2, false );
    echo wh_draw_input_field_label ( 'sunday', 'sunday', 'Sunday', '',
      '', '', 'checkbox', true, 2, true );
    $sports_query = getSports ( );
    while ($row_obj = wh_db_fetch_object_custom($sports_query)) {
      $sports [$row_obj->id] = $row_obj->name;
    }
    wh_db_free_result ($sports_query);
    unset ($row_obj);
    echo wh_draw_pull_down_menu_label ( 'sports[]', $sports, '', 'Sports',
      '', '', 'multiple="multiple" size="3"', false, true, 2, true );
    unset ($sports);
?>
    <input type="submit" />
  </form>

</div>

<?php

  require(DIR_WS_INCLUDES . 'template_bottom.php');
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
