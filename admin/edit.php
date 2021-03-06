<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require(DIR_WS_LANGUAGES . $language . '/' . 'edit.php');

  require(DIR_WS_FUNCTIONS . 'local/edit.php');

  require(DIR_WS_INCLUDES . 'template_top.php');

 // page for edit club
?>

<h1><?php echo HEADING_TITLE; ?></h1>

<div class="contentContainer">

<?php
    if (defined ('TEXT_SUCCESS'))
    {
?>

  <div class="contentText">
    <div class="notification">
    <?php echo TEXT_SUCCESS; ?>
    </div>
  </div>

<?php
    }
    if (defined ('TEXT_ERROR'))
    {
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

  <form action="edit.php" method="get">
<?php
#   echo wh_draw_hidden_field ( 'action', 'select' ) . PHP_EOL;
#   var_dump ( $clubs );
    echo wh_draw_pull_down_menu_label ( 'club_search', $clubs, '', 'Club Name',
        '', isset($club) ? $club->id : '', 'size="1"', false, true, 2, false );
    $sports_query = getSports ( );
    $sports = array ( 0 => PULL_DOWN_DEFAULT );
    while ($row_obj = wh_db_fetch_object_custom($sports_query)) {
      $sports [$row_obj->id] = $row_obj->name;
    }
    unset ($row_obj);
    echo wh_draw_pull_down_menu_label ( 'sport_search', $sports, '', 'Sport',
        '', '', 'size="1"', false, true, 2, false );
    unset ( $sports [0] );
?>
    <input type="submit" value="select club" />
  </form>

<?php
    if ( isset($club) && ! is_null  ( $club )
        && ! is_null ( $club->id ) && is_numeric ( $club->id )
        && wh_db_fetch_object_custom ( getClubByName ($club->name) ) != false )
    {
#     $name = wh_db_post_input_string ( 'club_search' );
#     getClubByName ( $name );
?>

  <form action="edit.php" method="post">
<?php
      echo wh_draw_hidden_field ( 'action', 'update' ) . PHP_EOL;
      echo wh_draw_hidden_field ( 'id', $club->id ) . PHP_EOL;
      echo wh_draw_input_field_label ( 'name', 'name', 'Club Name', '', $club->name,
        ' required = "required" placeholder = "enter club name"',
        'text', false, 2, true );
      echo wh_draw_input_field_label ( 'address', 'address', 'Address', '',
        $club->address, '', 'text', false, 2, true );
      echo wh_draw_input_field_label ( 'postcode', 'postcode', 'Postcode', '',
        $club->postcode, '', 'text', false, 2, true );
      echo wh_draw_input_field_label ( 'latitude', 'latitude', 'Latitude', '',
        $club->latitude, '', 'text', false, 2, true );
      echo wh_draw_input_field_label ( 'longtitude', 'longtitude', 'Longtitude', '',
        $club->longtitude, '', 'text', false, 2, true );
      echo wh_draw_input_field_label ( 'website', 'website', 'Website', '',
        $club->website, 'maxlength="200"', 'text', false, 2, true );
      echo wh_draw_input_field_label ( 'email', 'email', 'Email', '',
        $club->email, 'maxlength="100"', 'text', false, 2, true );
      echo wh_draw_input_field_label ( 'phone', 'phone', 'Contact phone', '',
        $club->phone, 'maxlength="100"', 'text', false, 2, true );
      echo wh_draw_textarea_field_label ( 'comment', 'comment', 'Comment', '',
        30, 3, $club->comment, 'maxlength="4000" spellcheck = "true"', false, 2, true );
      echo wh_draw_input_field_label ( 'time_open_global', 'time_open_global', 'Opening time', '',
        $club->time_open, '', 'time', false, 2, false );
?>
    <input type="button" id="time_open_global_submit" value="set global" /> <br />
<?php
      echo wh_draw_input_field_label ( 'time_close_global', 'time_close_global', 'Closing time', '',
        $club->time_close, '', 'time', false, 2, false );
?>
    <input type="button" id="time_close_global_submit" value="set global" /> <br />
<?php
      echo wh_draw_input_field_label ( 'price_member_global', 'price_member_global', 'Price Member', '',
        $club->price_member, '', 'text', false, 2, false );
?>
    <input type="button" id="price_member_global_submit" value="set global" /> <br />
<?php
      echo wh_draw_input_field_label ( 'price_nonmember_global', 'price_nonmember_global', 'Price Non-Member', '',
        $club->price_nonmember, '', 'text', false, 2, false );
?>
    <input type="button" id="price_nonmember_global_submit" value="set global" /> <br />
<?php
#     echo wh_draw_input_field_label ( 'everyday', 'everyday', 'All days', '',
#       '', '', 'checkbox', true, 2, false );
#     echo wh_draw_input_field_label ( 'monday', 'monday', 'Monday', '',
#       '', '', 'checkbox', true, 2, false );
#     echo wh_draw_input_field_label ( 'tuesday', 'tuesday', 'Tuesday', '',
#       '', '', 'checkbox', true, 2, false );
#     echo wh_draw_input_field_label ( 'wednesday', 'wednesday', 'Wednesday', '',
#       '', '', 'checkbox', true, 2, true );
#     echo wh_draw_input_field_label ( 'thursday', 'thursday', 'Thursday', '',
#       '', '', 'checkbox', true, 2, false );
#     echo wh_draw_input_field_label ( 'friday', 'friday', 'Friday', '',
#       '', '', 'checkbox', true, 2, false );
#     echo wh_draw_input_field_label ( 'saturday', 'saturday', 'Saturday', '',
#       '', '', 'checkbox', true, 2, false );
#     echo wh_draw_input_field_label ( 'sunday', 'sunday', 'Sunday', '',
#       '', '', 'checkbox', true, 2, true );
#     echo wh_draw_pull_down_menu_label ( 'sports[]', $sports, '', 'Sports',
#       '', '', 'multiple="multiple" size="3"', false, true, 2, true );
#     unset ($sports);
?>

  <!--
        <?php #echo wh_draw_input_field('dob', wh_date_short($account['customers_dob']), 'id="dob"') . '&nbsp;' . (wh_not_null('Date of birth') ? '<span class="inputRequirement">' . 'Date of birth' . '</span>': ''); ?><script type="text/javascript">$('#dob').datepicker({dateFormat: '<?php #echo JQUERY_DATEPICKER_FORMAT; ?>', changeMonth: true, changeYear: true, yearRange: '-100:+0'});</script>
  -->

    <table style="margin: 20px auto 0 auto" border="1">
    <thead>
      <tr>
        <th style="padding-bottom:10px;text-align:center">Sports:</th>
        <th style='padding:0px 20px;text-align:center'>Mon</th>
        <th style='padding:0px 20px;text-align:center'>Tue</th>
        <th style='padding:0px 20px;text-align:center'>Wed</th>
        <th style='padding:0px 20px;text-align:center'>Thu</th>
        <th style='padding:0px 20px;text-align:center'>Fri</th>
        <th style='padding:0px 20px;text-align:center'>Sat</th>
        <th style='padding:0px 20px;text-align:center'>Sun</th>
      </tr>
    </thead>
<?php
    $clubosportquery = getSportsByClubOrderByNameDays ( $club->id );
    $clubosport_row = wh_db_fetch_object_custom($clubosportquery);

#   var_dump ( $clubosport_row );

    foreach ( $sports as $sport_id => $sport_name )
    {
?>
      <tr>
        <td style='padding:0px 6px;' rowspan="10">
<?php
      $sport_selected = ( $clubosport_row &&
          $clubosport_row->sport_id == $sport_id );

      echo wh_draw_checkbox_field_custom ( "selectSport_{$sport_id}",
          "selectSport_{$sport_id}", null, $sport_selected, 'class="selectSport"', 4, false );
?>
          <?php echo $sport_name; ?>
        </td>
        <td colspan="7" style="text-align:center">
<?php
#     $days_selected = array ();
#     $days_selected = array_fill ( 1 , 7, '' );
      $prices = array_fill ( 1 , 7, ['member' => '', 'nonmember' => ''] );
      $times = array_fill ( 1 , 7, ['open' => '', 'close' => ''] );
      $days_type = '';
      $days_type_price = '';
      $days_type_time = '';

      wh_fill_times_prices ();

      $all_select_time = ( $days_type_time == 'all' );
      $workweek_weekend_select_time = ( $days_type_time == 'workweekweekend' );
      $workweek_sat_sun_select_time = ( $days_type_time == 'workweeksatsun' );
      $separately_select_time = ( $days_type_time == 'separately' );

      echo wh_draw_radio_field_label ( "selectDaysViewTime{$sport_id}", "selectDaysViewTime{$sport_id}_0",
        'All days', '', 'all', $all_select_time, '', 'style="margin:0px 0px;padding:0px 0px;"', 4, false );
      echo wh_draw_radio_field_label ( "selectDaysViewTime{$sport_id}", "selectDaysViewTime{$sport_id}_1",
        'Workweek & Weekend', '', 'workweekweekend', $workweek_weekend_select_time, '', 'style="margin:0px 0px;padding:0px 0px;"', 4, false );
      echo wh_draw_radio_field_label ( "selectDaysViewTime{$sport_id}", "selectDaysViewTime{$sport_id}_1",
        'Workweek & Sat/Sun', '', 'workweeksatsun', $workweek_sat_sun_select_time, '', 'style="margin:0px 0px;padding:0px 0px;"', 4, false );
      echo wh_draw_radio_field_label ( "selectDaysViewTime{$sport_id}", "selectDaysViewTime{$sport_id}_2",
        'Separately', '', 'separately', $separately_select_time, '', 'style="margin:0px 0px;padding:0px 0px;"', 4, false );
?>
        </td>
      </tr>
      <tr id="trDaysTimeAll<?php echo $sport_id; ?>" >
        <td style="text-align:center" colspan="7">
<?php

      echo wh_draw_label ( 'All days', '' , '', 7, false );
      echo wh_draw_input_field_custom ( "timeOpenAll{$sport_id}",
        "timeOpenAll{$sport_id}", $times [1] ['open'], ' size = "8" placeholder = ""',
        'text', false, 5, false );
      echo wh_draw_input_field_custom ( "timeCloseAll{$sport_id}",
        "timeCloseAll{$sport_id}", $times [1] ['close'], ' size = "8" placeholder = ""',
        'text', false, 5, true );
?>
        </td>
      </tr>
      <tr id="trDaysTimeWorkweekWeekend<?php echo $sport_id; ?>" >
        <td style="text-align:center" colspan="5">
<?php

      echo wh_draw_input_field_label ( "timeOpenWorkweek1{$sport_id}",
        "timeOpenWorkweek1{$sport_id}", 'Workweek', '' , $times [1] ['open'],
        ' size = "8" placeholder = ""', 'text', false, 5, false );
      echo wh_draw_input_field_custom ( "timeCloseWorkweek1{$sport_id}",
        "timeCloseWorkweek1{$sport_id}", $times [1] ['close'], ' size = "8" placeholder = ""',
        'text', false, 5, true );
?>
        </td>
        <td style="text-align:center" colspan="2">
<?php

      echo wh_draw_label ( 'Weekend', '' , '', 5, true );
      echo wh_draw_input_field_custom ( "timeOpenWeekend{$sport_id}",
        "timeOpenWeekend{$sport_id}", $times [6] ['open'], ' size = "5" placeholder = ""',
        'text', false, 5, false );
      echo wh_draw_input_field_custom ( "timeCloseWeekend{$sport_id}",
        "timeCloseWeekend{$sport_id}", $times [6] ['close'], ' size = "5" placeholder = ""',
        'text', false, 5, true );
?>
        </td>
      </tr>
      <tr id="trDaysTimeWorkweekSatSun<?php echo $sport_id; ?>" >
        <td style="text-align:center" colspan="5">
<?php

      echo wh_draw_input_field_label ( "timeOpenWorkweek2{$sport_id}",
        "timeOpenWorkweek2{$sport_id}", 'Workweek', '' , $times [1] ['open'],
        ' size = "8" placeholder = ""', 'text', false, 5, false );
      echo wh_draw_input_field_custom ( "timeCloseWorkweek2{$sport_id}",
        "timeCloseWorkweek2{$sport_id}", $times [1] ['close'], ' size = "8" placeholder = ""',
        'text', false, 5, true );
?>
        </td>
        <td style="text-align:center;padding:0px 2px">
<?php
#     echo wh_draw_checkbox_field_custom ( "selectSat{$sport_id}",
#       "selectSat{$sport_id}", null, $days_selected [6],
#       'style="display: block; margin: auto"', 4, false );

      echo wh_draw_input_field_custom ( "timeOpenSat{$sport_id}",
        "timeOpenSat{$sport_id}", $times [6] ['open'], ' size = "5" placeholder = ""',
        'text', false, 5, true );
      echo wh_draw_input_field_custom ( "timeCloseSat{$sport_id}",
        "timeCloseSat{$sport_id}", $times [6] ['close'], ' size = "5" placeholder = ""',
        'text', false, 5, true );
?>
        </td>
        <td style="text-align:center;padding:0px 2px">
<?php
#     echo wh_draw_checkbox_field_custom ( "selectSun{$sport_id}",
#       "selectSun{$sport_id}", null, $days_selected [7],
#       'style="display: block; margin: auto"', 4, false );

      echo wh_draw_input_field_custom ( "timeOpenSun{$sport_id}",
        "timeOpenSun{$sport_id}", $times [7] ['open'], ' size = "5" placeholder = ""',
        'text', false, 5, true );
      echo wh_draw_input_field_custom ( "timeCloseSun{$sport_id}",
        "timeCloseSun{$sport_id}", $times [7] ['close'], ' size = "5" placeholder = ""',
        'text', false, 5, true );
?>
        </td>
      </tr>
      <tr id="trDaysTimeSeparately<?php echo $sport_id; ?>" >
<?php
      for ($i = 1; $i < 8; ++$i)
      {
?>
        <td style="text-align:center;padding:0px 2px">
<?php

#       echo wh_draw_checkbox_field_custom ( "selectDay{$i}_{$sport_id}",
#         "selectDay{$i}_{$sport_id}", null, $days_selected [$i], 'style="display: block; margin: auto"', 4, false );

        echo wh_draw_input_field_custom ( "timeOpenDay{$i}_{$sport_id}",
          "timeOpenDay{$i}_{$sport_id}", $times [$i] ['open'],  ' size = "5" placeholder = ""',
          'text', false, 5, true );
        echo wh_draw_input_field_custom ( "timeCloseDay{$i}_{$sport_id}",
          "timeCloseDay{$i}_{$sport_id}", $times [$i] ['close'],  ' size = "5" placeholder = ""',
          'text', false, 5, true );
?>
        </td>
<?php
      }
?>
      </tr>
      <tr>
        <td colspan="7" style="text-align:center">
<?php
      $all_select_price = ( $days_type_price == 'all' );
      $workweek_weekend_select_price = ( $days_type_price == 'workweekweekend' );
      $workweek_sat_sun_select_price = ( $days_type_price == 'workweeksatsun' );
      $separately_select_price = ( $days_type_price == 'separately' );

      echo wh_draw_radio_field_label ( "selectDaysViewPrice{$sport_id}", "selectDaysViewPrice{$sport_id}_0",
        'All days', '', 'all', $all_select_price, '', 'style="margin:0px 0px;padding:0px 0px;"', 4, false );
      echo wh_draw_radio_field_label ( "selectDaysViewPrice{$sport_id}", "selectDaysViewPrice{$sport_id}_1",
        'Workweek & Weekend', '', 'workweekweekend', $workweek_weekend_select_price, '', 'style="margin:0px 0px;padding:0px 0px;"', 4, false );
      echo wh_draw_radio_field_label ( "selectDaysViewPrice{$sport_id}", "selectDaysViewPrice{$sport_id}_1",
        'Workweek & Sat/Sun', '', 'workweeksatsun', $workweek_sat_sun_select_price, '', 'style="margin:0px 0px;padding:0px 0px;"', 4, false );
      echo wh_draw_radio_field_label ( "selectDaysViewPrice{$sport_id}", "selectDaysViewPrice{$sport_id}_2",
        'Separately', '', 'separately', $separately_select_price, '', 'style="margin:0px 0px;padding:0px 0px;"', 4, false );
?>
        </td>
      </tr>
      <tr id="trDaysPriceAll<?php echo $sport_id; ?>" >
        <td style="text-align:center" colspan="7">
<?php

      echo wh_draw_label ( 'All days', '' , '', 7, false );
      echo wh_draw_input_field_custom ( "priceMemberAll{$sport_id}",
        "priceMemberAll{$sport_id}", $prices [1] ['member'],
        ' size = "8" placeholder = ""', 'text', false, 5, false );
      echo wh_draw_input_field_custom ( "priceNonmemberAll{$sport_id}",
        "priceNonmemberAll{$sport_id}", $prices [1] ['nonmember'],
        ' size = "8" placeholder = ""', 'text', false, 5, true );
?>
        </td>
      </tr>
      <tr id="trDaysPriceWorkweekWeekend<?php echo $sport_id; ?>" >
        <td style="text-align:center" colspan="5">
<?php

      echo wh_draw_input_field_label ( "priceMemberWorkweek1{$sport_id}",
        "priceMemberWorkweek1{$sport_id}", 'Workweek', '' , $prices [1] ['member'],
        ' size = "8" placeholder = ""', 'text', false, 5, false );
      echo wh_draw_input_field_custom ( "priceNonmemberWorkweek1{$sport_id}",
        "priceNonmemberWorkweek1{$sport_id}", $prices [1] ['nonmember'],
        ' size = "8" placeholder = ""', 'text', false, 5, true );
?>
        </td>
        <td style="text-align:center" colspan="2">
<?php

      echo wh_draw_label ( 'Weekend', '' , '', 5, true );
      echo wh_draw_input_field_custom ( "priceMemberWeekend{$sport_id}",
        "priceMemberWeekend{$sport_id}", $prices [6] ['member'],
        ' size = "5" placeholder = ""', 'text', false, 5, false );
      echo wh_draw_input_field_custom ( "priceNonmemberWeekend{$sport_id}",
        "priceNonmemberWeekend{$sport_id}", $prices [6] ['nonmember'],
        ' size = "5" placeholder = ""', 'text', false, 5, true );
?>
        </td>
      </tr>
      <tr id="trDaysPriceWorkweekSatSun<?php echo $sport_id; ?>" >
        <td style="text-align:center" colspan="5">
<?php

      echo wh_draw_input_field_label ( "priceMemberWorkweek2{$sport_id}",
        "priceMemberWorkweek2{$sport_id}", 'Workweek', '' , $prices [1] ['member'],
        ' size = "8" placeholder = ""', 'text', false, 5, false );
      echo wh_draw_input_field_custom ( "priceNonmemberWorkweek2{$sport_id}",
        "priceNonmemberWorkweek2{$sport_id}", $prices [1] ['nonmember'], ' size = "8" placeholder = ""',
        'text', false, 5, true );
?>
        </td>
        <td style="text-align:center;padding:0px 2px">
<?php
#       echo wh_draw_checkbox_field_custom ( "selectSat{$sport_id}",
#         "selectSat{$sport_id}", null, $days_selected [6], 'style="display: block; margin: auto"', 4, false );

      echo wh_draw_input_field_custom ( "priceMemberSat{$sport_id}",
        "priceMemberSat{$sport_id}", $prices [6] ['member'], ' size = "5" placeholder = ""',
        'text', false, 5, true );
      echo wh_draw_input_field_custom ( "priceNonmemberSat{$sport_id}",
        "priceNonmemberSat{$sport_id}", $prices [6] ['nonmember'], ' size = "5" placeholder = ""',
        'text', false, 5, true );
?>
        </td>
        <td style="text-align:center;padding:0px 2px">
<?php
#       echo wh_draw_checkbox_field_custom ( "selectSun{$sport_id}",
#         "selectSun{$sport_id}", null, $days_selected [7], 'style="display: block; margin: auto"', 4, false );

      echo wh_draw_input_field_custom ( "priceMemberSun{$sport_id}",
        "priceMemberSun{$sport_id}", $prices [7] ['member'], ' size = "5" placeholder = ""',
        'text', false, 5, true );
      echo wh_draw_input_field_custom ( "priceNonmemberSun{$sport_id}",
        "priceNonmemberSun{$sport_id}", $prices [7] ['nonmember'], ' size = "5" placeholder = ""',
        'text', false, 5, true );
?>
        </td>
      </tr>
      <tr id="trDaysPriceSeparately<?php echo $sport_id; ?>" >
<?php
      for ($i = 1; $i < 8; ++$i)
      {
?>
        <td style="text-align:center;padding:0px 2px">
<?php

#       echo wh_draw_checkbox_field_custom ( "selectDay{$i}_{$sport_id}",
#         "selectDay{$i}_{$sport_id}", null, $days_selected [$i], 'style="display: block; margin: auto"', 4, false );

        echo wh_draw_input_field_custom ( "priceMemberDay{$i}_{$sport_id}",
          "priceMemberDay{$i}_{$sport_id}", $prices [$i] ['member'],  ' size = "5" placeholder = ""',
          'text', false, 5, true );
        echo wh_draw_input_field_custom ( "priceNonmemberDay{$i}_{$sport_id}",
          "priceNonmemberDay{$i}_{$sport_id}", $prices [$i] ['nonmember'],  ' size = "5" placeholder = ""',
          'text', false, 5, true );
?>
        </td>
<?php
      }
?>
      </tr>

<!--      </div> -->
<?php
    }
?>
    </table>
    <input type="submit" value="edit" style="display: block; margin-left: auto; margin-right: auto"/>
  </form>

<?php
    }
?>

</div>

<?php

  require(DIR_WS_INCLUDES . 'template_bottom.php');
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
