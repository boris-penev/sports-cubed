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

  <form action="edit.php" method="get">
<?php
#   echo wh_draw_hidden_field ( 'action', 'select' ) . PHP_EOL;
#   var_dump ( $clubs );
    echo wh_draw_pull_down_menu_label ( 'club_search', $clubs, '', 'Club Name',
        '', 0, 'size="1"', false, true, 2, false );
    $sports_query = getSports ( );
    $sports = array ( 0 => '' );
    while ($row_obj = wh_db_fetch_object_custom($sports_query)) {
      $sports [$row_obj->id] = $row_obj->name;
    }
    unset ($row_obj);
    wh_db_free_result($sports_query);
    echo wh_draw_pull_down_menu_label ( 'sport_search', $sports, '', 'Sport',
        '', '', 'size="1"', false, true, 2, false );
    unset ( $sports [0] );
?>
    <input type="submit" value="select club" />
  </form>

<?php
    if ( ! is_null  ( $club )
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
        'text', true, 2, true );
      echo wh_draw_input_field_label ( 'address', 'address', 'Address Name', '',
        $club->address, '', 'text', true, 2, true );
      echo wh_draw_input_field_label ( 'postcode', 'postcode', 'Postcode', '',
        $club->postcode, '', 'text', true, 2, true );
      echo wh_draw_input_field_label ( 'latitude', 'latitude', 'Latitude', '',
        $club->latitude, '', 'text', true, 2, true );
      echo wh_draw_input_field_label ( 'longtitude', 'longtitude', 'Longtitude', '',
        $club->longtitude, '', 'text', true, 2, true );
      echo wh_draw_input_field_label ( 'website', 'website', 'Website', '',
      '', '', 'text', true, 2, true );
      echo wh_draw_input_field_label ( 'email', 'email', 'Email', '',
        $club->email, '', 'text', true, 2, true );
      echo wh_draw_input_field_label ( 'phone', 'phone', 'Contact phone', '',
        $club->phone, '', 'text', true, 2, true );
      echo wh_draw_textarea_field_label ( 'comment', 'comment', 'Comment', '',
        30, 3, $club->comment, 'maxlength="4000" spellcheck = "true"', true, 2, true );
      echo wh_draw_input_field_label ( 'time_open_global', 'time_open_global', 'Opening time', '',
        $club->time_open, '', 'time', true, 2, false );
?>
    <input type="button" id="time_open_global_submit" value="set global" /> <br />
<?php
      echo wh_draw_input_field_label ( 'time_close_global', 'time_close_global', 'Closing time', '',
        $club->time_close, '', 'time', true, 2, false );
?>
    <input type="button" id="time_close_global_submit" value="set global" /> <br />
<?php
      echo wh_draw_input_field_label ( 'price_member_global', 'price_member_global', 'Price Member', '',
        $club->price_member, '', 'text', true, 2, false );
?>
    <input type="button" id="price_member_global_submit" value="set global" /> <br />
<?php
      echo wh_draw_input_field_label ( 'price_nonmember_global', 'price_nonmember_global', 'Price Non-Member', '',
        $club->price_nonmember, '', 'text', true, 2, false );
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
  <table>
      <tr>
        <td class="fieldKey"><?php echo ENTRY_DATE_OF_BIRTH; ?></td>
        <td class="fieldValue"><?php echo wh_draw_input_field('dob', wh_date_short($account['customers_dob']), 'id="dob"') . '&nbsp;' . (wh_not_null(ENTRY_DATE_OF_BIRTH_TEXT) ? '<span class="inputRequirement">' . ENTRY_DATE_OF_BIRTH_TEXT . '</span>': ''); ?><script type="text/javascript">$('#dob').datepicker({dateFormat: '<?php echo JQUERY_DATEPICKER_FORMAT; ?>', changeMonth: true, changeYear: true, yearRange: '-100:+0'});</script></td>
      </tr>
  </table>
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

    $sports_query = getSportsOrderByName ( );
    foreach ( $sports as $sport_id => $sport_name )
    {
?>
      <tr>
        <td style='padding:0px 6px' rowspan="10">
<?php
      $sport_selected = ( $clubosport_row &&
          $clubosport_row->sport_id == $sport_id ) ?
          true : false;

      echo wh_draw_checkbox_field_custom ( "selectSport_{$sport_id}",
          "selectSport_{$sport_id}", null, $sport_selected, 'class="selectSport"', 4, false );
?>
          <?php echo $sport_name; ?>
        </td>
        <td colspan="7" style="text-align:center">
<?php
      // TODO: The arrays for displaying times/prices in text field should
      // be filled completely, not only partially.
      // Currently there are "artifacts" showing
      // After they are filled, they will be with the right values
      $days_selected = array ();
      $prices_member = array ();
      $prices_nonmember = array ();
      $times_open = array ();
      $times_close = array ();
      $days_type = '';
      $days_type_price = '';
      $days_type_time = '';
      if ( $clubosport_row && $clubosport_row->sport_id == $sport_id )
      {
        if ( $clubosport_row->day_id == 8 )
        {
          $days_type = 'all';
          $days_type_price = 'all';
          $days_type_time = 'all';
          $days_selected = array_fill(1, 7, true);
          $price_member = $clubosport_row->price_member;
          $price_nonmember = $clubosport_row->price_nonmember;
          $time_open =
            ( wh_not_null ( $clubosport_row->opening_time )
              && $clubosport_row->opening_time != '00:00:00'
              && strtotime( $clubosport_row->opening_time ) !== false ) ?
              $clubosport_row->opening_time : null;
          $time_close =
            ( wh_not_null ( $clubosport_row->closing_time )
              && $clubosport_row->closing_time != '00:00:00'
              && strtotime( $clubosport_row->closing_time ) !== false ) ?
              $clubosport_row->closing_time : null;
          if ( ! wh_not_null ($time_open) || ! wh_not_null ($time_open) )
          {
            $time_open = $time_close = null;
          }
          $prices_member = array_fill ( 1 , 7, $price_member );
          $prices_nonmember = array_fill ( 1 , 7, $price_nonmember );
          $times_open = array_fill ( 1 , 7, $time_open );
          $times_close = array_fill ( 1 , 7, $time_close );
// Do not currently know why this is here, will be soon removed
#         $clubosport_row = wh_db_fetch_object_custom($clubosportquery);
        }
        else
        {
          $i = 0;
#         $day_id = $clubosport_row->day_id;
          do
          {
            $day_id = $clubosport_row->day_id;
#           echoQuery ( $clubosport_row->day_id );
            if ( $day_id == 9 )
            {
              $price_member = $clubosport_row->price_member;
              $price_nonmember = $clubosport_row->price_nonmember;
              $time_open =
                ( wh_not_null ( $clubosport_row->opening_time )
                  && $clubosport_row->opening_time != '00:00:00'
                  && strtotime( $clubosport_row->opening_time ) !== false ) ?
                  $clubosport_row->opening_time : null;
              $time_close =
                ( wh_not_null ( $clubosport_row->closing_time )
                  && $clubosport_row->closing_time != '00:00:00'
                  && strtotime( $clubosport_row->closing_time ) !== false ) ?
                  $clubosport_row->closing_time : null;
              if ( ! wh_not_null ($time_open) || ! wh_not_null ($time_open) )
              {
                $time_open = $time_close = null;
              }
              for ($i = 1; $i < 6; ++$i)
              {
                $days_selected [$i] = true;
                $prices_member [$i] = $price_member;
                $prices_nonmember [$i] = $price_nonmember;
                $times_open [$i] = $time_open;
                $times_close [$i] = $time_close;
              }
            }
            elseif ( $day_id == 10 )
            {
              $price_member = $clubosport_row->price_member;
              $price_nonmember = $clubosport_row->price_nonmember;
              $time_open =
                ( wh_not_null ( $clubosport_row->opening_time )
                  && $clubosport_row->opening_time != '00:00:00'
                  && strtotime( $clubosport_row->opening_time ) !== false ) ?
                  $clubosport_row->opening_time : null;
              $time_close =
                ( wh_not_null ( $clubosport_row->closing_time )
                  && $clubosport_row->closing_time != '00:00:00'
                  && strtotime( $clubosport_row->closing_time ) !== false ) ?
                  $clubosport_row->closing_time : null;
              if ( ! wh_not_null ($time_open) || ! wh_not_null ($time_open) )
              {
                $time_open = $time_close = null;
              }
              for ($i = 6; $i < 8; ++$i)
              {
                $days_selected [$i] = true;
                $prices_member [$i] = $price_member;
                $prices_nonmember [$i] = $price_nonmember;
                $times_open [$i] = $time_open;
                $times_close [$i] = $time_close;
              }
            }
            elseif ( 0 < $day_id && $day_id < 8 )
            {
              $days_selected [$day_id] = true;
              $prices_member [$day_id] = $clubosport_row->price_member;
              $prices_nonmember [$day_id] = $clubosport_row->price_nonmember;
              $times_open [$day_id] =
                ( wh_not_null ( $clubosport_row->opening_time )
                  && $clubosport_row->opening_time != '00:00:00'
                  && strtotime( $clubosport_row->opening_time ) != false ) ?
                  $clubosport_row->opening_time : null;
              $times_close [$day_id] =
                ( wh_not_null ( $clubosport_row->closing_time )
                  && $clubosport_row->closing_time != '00:00:00'
                  && strtotime( $clubosport_row->closing_time ) !== false ) ?
                  $clubosport_row->closing_time : null;
            }
          }
          while ( ($clubosport_row = wh_db_fetch_object_custom($clubosportquery))
                && $clubosport_row->sport_id == $sport_id  );

          for ( $i = 1; $i < 5; ++$i )
          {
  #         echoQuery ( $prices_member[$i] );
            if ( $prices_member [$i] != $prices_member [$i+1]
              || $prices_nonmember [$i] != $prices_nonmember [$i+1] )
            {
              $days_type_price = 'separately';
              break;
            }
          }
          for ( $i = 1; $i < 5; ++$i )
          {
  #         echoQuery ( $prices_member[$i] );
            if ( $times_open [$i] != $times_open [$i+1]
              || $times_close [$i] != $times_close [$i+1] )
            {
              $days_type_time = 'separately';
              break;
            }
          }
          if ( $days_type_price == '' )
          {
            if ( $prices_member [6] != $prices_member [7]
              || $prices_nonmember [6] != $prices_nonmember [7] )
            {
              $days_type_price = 'workingsatsun';
            }
            elseif ( $prices_member[5] == $prices_member[6]
              && $prices_nonmember [6] == $prices_nonmember [7]  )
            {
              $days_type_price = 'all';
            }
            else {
              $days_type_price = 'workingweekend';
            }
          }
          if ( $days_type_time == '' )
          {
            if ( $times_open [6] != $times_open [7]
              || $times_close [6] != $times_close [7] )
            {
              $days_type_time = 'workingsatsun';
            }
            elseif ( $times_open[5] == $times_open[6]
              && $times_close [6] == $times_close [7]  )
            {
              $days_type_time = 'all';
            }
            else {
              $days_type_time = 'workingweekend';
            }
          }
        }
      }
      else
      {
        $days_type_price = 'all';
        $days_type_time = 'all';
      }
      if ( $days_type_price == '' ) {
        $days_type_price = 'all';
      }
      if ( $days_type_time == '' ) {
        $days_type_time = 'all';
      }

      $all_select_time = ( $days_type_time == 'all' ) ? true : false;
      $working_weekend_select_time = ( $days_type_time == 'workingweekend' ) ? true : false;
      $working_sat_sun_select_time = ( $days_type_time == 'workingsatsun' ) ? true : false;
      $separately_select_time = ( $days_type_time == 'separately' ) ? true : false;

      echo wh_draw_radio_field_label ( "selectDaysViewTime{$sport_id}", "selectDaysViewTime{$sport_id}_0",
        'All days', '', 'all', $all_select_time, '', 'style="margin:0px 0px;padding:0px 0px;"', 4, false );
      echo wh_draw_radio_field_label ( "selectDaysViewTime{$sport_id}", "selectDaysViewTime{$sport_id}_1",
        'Working & Weekend', '', 'workingweekend', $working_weekend_select_time, '', 'style="margin:0px 0px;padding:0px 0px;"', 4, false );
      echo wh_draw_radio_field_label ( "selectDaysViewTime{$sport_id}", "selectDaysViewTime{$sport_id}_1",
        'Working & Sat/Sun', '', 'workingsatsun', $working_sat_sun_select_time, '', 'style="margin:0px 0px;padding:0px 0px;"', 4, false );
      echo wh_draw_radio_field_label ( "selectDaysViewTime{$sport_id}", "selectDaysViewTime{$sport_id}_2",
        'Separately', '', 'separately', $separately_select_time, '', 'style="margin:0px 0px;padding:0px 0px;"', 4, false );
?>
        </td>
      </tr>
      <tr class="trDaysSeparately<?php echo $sport_id; ?>">
<?php
      for ($i = 1; $i < 8; ++$i)
      {
  ?>
        <td style="text-align:center;padding:0px 2px">
<?php
#       $checkbox_selected = wh_not_null ( $days_selected [$i] ) ? true : false;

        echo wh_draw_checkbox_field_custom ( "selectDay{$i}_{$sport_id}",
          "selectDay{$i}_{$sport_id}", null, $days_selected [$i], 'style="display: block; margin: auto"', 4, false );

        $time_open = wh_not_null ( $times_open [$i] ) ?
            $times_open [$i] : '' ;
        $time_close = wh_not_null ( $times_close [$i] ) ?
            $times_close [$i] : '' ;

        echo wh_draw_input_field_custom ( "timeOpenDay{$i}_{$sport_id}",
          "timeOpenDay{$i}_{$sport_id}", $time_open,  ' size = "5" placeholder = ""',
          'text', true, 5, true );
        echo wh_draw_input_field_custom ( "timeCloseDay{$i}_{$sport_id}",
          "timeCloseDay{$i}_{$sport_id}", $time_close,  ' size = "5" placeholder = ""',
          'text', true, 5, true );
?>
        </td>
<?php
      }
?>
      </tr>
      <tr class="trDaysWorkingWeekend<?php echo $sport_id; ?>">
        <td style="text-align:center" colspan="5">
<?php
      $time_open = wh_not_null ( $times_open [1] ) ?
          $times_open [1] : '' ;
      $time_close = wh_not_null ( $times_close [1] ) ?
          $times_close [1] : '' ;

      echo wh_draw_input_field_label ( "timeOpenWorking{$sport_id}",
        "timeOpenWorking{$sport_id}", 'Working days', '' ,$time_open,
        ' size = "8" placeholder = ""', 'text', true, 5, false );
      echo wh_draw_input_field_custom ( "timeCloseWorking{$sport_id}",
        "timeCloseWorking{$sport_id}", $time_close, ' size = "8" placeholder = ""',
        'text', true, 5, true );
?>
        </td>
        <td style="text-align:center" colspan="2">
<?php
      $time_open = wh_not_null ( $times_open [6] ) ?
          $times_open [6] : '' ;
      $time_close = wh_not_null ( $times_close [6] ) ?
          $times_close [6] : '' ;

      echo wh_draw_label ( 'Weekend', '' , '', 5, true );
      echo wh_draw_input_field_custom ( "timeOpenWeekend{$sport_id}",
        "timeOpenWeekend{$sport_id}", $time_open, ' size = "5" placeholder = ""',
        'text', true, 5, false );
      echo wh_draw_input_field_custom ( "timeCloseWeekend{$sport_id}",
        "timeCloseWeekend{$sport_id}", $time_close, ' size = "5" placeholder = ""',
        'text', true, 5, true );
?>
        </td>
      </tr>
      <tr class="trDaysWorkingSatSun<?php echo $sport_id; ?>">
        <td style="text-align:center" colspan="5">
<?php
      $time_open = wh_not_null ( $times_open [1] ) ?
          $times_open [1] : '' ;
      $time_close = wh_not_null ( $times_close [1] ) ?
          $times_close [1] : '' ;

      echo wh_draw_input_field_label ( "timeOpenWorking{$sport_id}",
        "timeOpenWorking{$sport_id}", 'Working days', '' ,$time_open,
        ' size = "8" placeholder = ""', 'text', true, 5, false );
      echo wh_draw_input_field_custom ( "timeCloseWorking{$sport_id}",
        "timeCloseWorking{$sport_id}", $time_close, ' size = "8" placeholder = ""',
        'text', true, 5, true );
?>
        </td>
        <td style="text-align:center;padding:0px 2px">
<?php
      echo wh_draw_checkbox_field_custom ( "selectSat{$sport_id}",
        "selectSat{$sport_id}", null, $days_selected [6], 'style="display: block; margin: auto"', 4, false );

      $time_open = wh_not_null ( $times_open [6] ) ?
          $times_open [6] : '' ;
      $time_close = wh_not_null ( $times_close [6] ) ?
          $times_close [6] : '' ;

      echo wh_draw_input_field_custom ( "timeOpenSat{$sport_id}",
        "timeOpenSat{$sport_id}", $time_open, ' size = "5" placeholder = ""',
        'text', true, 5, true );
      echo wh_draw_input_field_custom ( "timeCloseSat{$sport_id}",
        "timeCloseSat{$sport_id}", $time_close, ' size = "5" placeholder = ""',
        'text', true, 5, true );
?>
        </td>
        <td style="text-align:center;padding:0px 2px">
<?php
      echo wh_draw_checkbox_field_custom ( "selectSun{$sport_id}",
        "selectSun{$sport_id}", null, $days_selected [7], 'style="display: block; margin: auto"', 4, false );

      $time_open = wh_not_null ( $times_open [7] ) ?
          $times_open [7] : '' ;
      $time_close = wh_not_null ( $times_close [7] ) ?
          $times_close [7] : '' ;

      echo wh_draw_input_field_custom ( "timeOpenSun{$sport_id}",
        "timeOpenSun{$sport_id}", $time_open, ' size = "5" placeholder = ""',
        'text', true, 5, true );
      echo wh_draw_input_field_custom ( "timeCloseSun{$sport_id}",
        "timeCloseSun{$sport_id}", $time_close, ' size = "5" placeholder = ""',
        'text', true, 5, true );
?>
        </td>
      </tr>
      <tr class="trDaysAll<?php echo $sport_id; ?>">
        <td style="text-align:center" colspan="7">
<?php
      $time_open = wh_not_null ( $times_open [1] ) ?
          $times_open [1] : '' ;
      $time_close = wh_not_null ( $times_close [1] ) ?
          $times_close [1] : '' ;

      echo wh_draw_label ( 'All days', '' , '', 7, false );
      echo wh_draw_input_field_custom ( "timeOpenAll{$sport_id}",
        "timeOpenAll{$sport_id}", $time_open, ' size = "8" placeholder = ""',
        'text', true, 5, false );
      echo wh_draw_input_field_custom ( "timeCloseAll{$sport_id}",
        "timeCloseAll{$sport_id}", $time_close, ' size = "8" placeholder = ""',
        'text', true, 5, true );
?>
        </td>
      </tr>
      <tr>
        <td colspan="7" style="text-align:center">
<?php
      $all_select_price = ( $days_type_price == 'all' ) ? true : false;
      $working_weekend_select_price = ( $days_type_price == 'workingweekend' ) ? true : false;
      $working_sat_sun_select_price = ( $days_type_price == 'workingsatsun' ) ? true : false;
      $separately_select_price = ( $days_type_price == 'separately' ) ? true : false;

      echo wh_draw_radio_field_label ( "selectDaysViewPrice{$sport_id}", "selectDaysViewPrice{$sport_id}_0",
        'All days', '', 'all', $all_select_price, '', 'style="margin:0px 0px;padding:0px 0px;"', 4, false );
      echo wh_draw_radio_field_label ( "selectDaysViewPrice{$sport_id}", "selectDaysViewPrice{$sport_id}_1",
        'Working & Weekend', '', 'workingweekend', $working_weekend_select_price, '', 'style="margin:0px 0px;padding:0px 0px;"', 4, false );
      echo wh_draw_radio_field_label ( "selectDaysViewPrice{$sport_id}", "selectDaysViewPrice{$sport_id}_1",
        'Working & Sat/Sun', '', 'workingsatsun', $working_sat_sun_select_price, '', 'style="margin:0px 0px;padding:0px 0px;"', 4, false );
      echo wh_draw_radio_field_label ( "selectDaysViewPrice{$sport_id}", "selectDaysViewPrice{$sport_id}_2",
        'Separately', '', 'separately', $separately_select_price, '', 'style="margin:0px 0px;padding:0px 0px;"', 4, false );
?>
        </td>
      </tr>
      <tr class="trDaysSeparately<?php echo $sport_id; ?>">
<?php
      for ($i = 1; $i < 8; ++$i)
      {
  ?>
        <td style="text-align:center;padding:0px 2px">
<?php
#       $checkbox_selected = wh_not_null ( $days_selected [$i] ) ? true : false;

        echo wh_draw_checkbox_field_custom ( "selectDay{$i}_{$sport_id}",
          "selectDay{$i}_{$sport_id}", null, $days_selected [$i], 'style="display: block; margin: auto"', 4, false );

        $price_member = wh_not_null ( $prices_member [$i] ) ?
            $prices_member [$i] : '' ;
        $price_nonmember = wh_not_null ( $prices_nonmember [$i] ) ?
            $prices_nonmember [$i] : '' ;

        echo wh_draw_input_field_custom ( "priceMemberDay{$i}_{$sport_id}",
          "priceMemberDay{$i}_{$sport_id}", $price_member,  ' size = "5" placeholder = ""',
          'text', true, 5, true );
        echo wh_draw_input_field_custom ( "priceNonmemberDay{$i}_{$sport_id}",
          "priceNonmemberDay{$i}_{$sport_id}", $price_nonmember,  ' size = "5" placeholder = ""',
          'text', true, 5, true );
?>
        </td>
<?php
      }
?>
      </tr>
      <tr class="trDaysWorkingWeekend<?php echo $sport_id; ?>">
        <td style="text-align:center" colspan="5">
<?php
        $price_member = wh_not_null ( $prices_member [1] ) ?
            $prices_member [1] : '' ;
        $price_nonmember = wh_not_null ( $prices_nonmember [1] ) ?
            $prices_nonmember [1] : '' ;

      echo wh_draw_input_field_label ( "priceMemberWorking{$sport_id}",
        "priceMemberWorking{$sport_id}", 'Working days', '' , $price_member,
        ' size = "8" placeholder = ""', 'text', true, 5, false );
      echo wh_draw_input_field_custom ( "priceNonmemberWorking{$sport_id}",
        "priceNonmemberWorking{$sport_id}", $price_nonmember, ' size = "8" placeholder = ""',
        'text', true, 5, true );
?>
        </td>
        <td style="text-align:center" colspan="2">
<?php
        $price_member = wh_not_null ( $prices_member [6] ) ?
            $prices_member [6] : '' ;
        $price_nonmember = wh_not_null ( $prices_nonmember [6] ) ?
            $prices_nonmember [6] : '' ;

      echo wh_draw_label ( 'Weekend', '' , '', 5, true );
      echo wh_draw_input_field_custom ( "priceMemberWeekend{$sport_id}",
        "priceMemberWeekend{$sport_id}", $price_member, ' size = "5" placeholder = ""',
        'text', true, 5, false );
      echo wh_draw_input_field_custom ( "priceNonmemberWeekend{$sport_id}",
        "priceNonmemberWeekend{$sport_id}", $price_nonmember, ' size = "5" placeholder = ""',
        'text', true, 5, true );
?>
        </td>
      </tr>
      <tr class="trDaysWorkingSatSun<?php echo $sport_id; ?>">
        <td style="text-align:center" colspan="5">
<?php
        $price_member = wh_not_null ( $prices_member [1] ) ?
            $prices_member [1] : '' ;
        $price_nonmember = wh_not_null ( $prices_nonmember [1] ) ?
            $prices_nonmember [1] : '' ;

      echo wh_draw_input_field_label ( "priceMemberWorking{$sport_id}",
        "priceMemberWorking{$sport_id}", 'Working days', '' , $price_member,
        ' size = "8" placeholder = ""', 'text', true, 5, false );
      echo wh_draw_input_field_custom ( "priceNonmemberWorking{$sport_id}",
        "priceNonmemberWorking{$sport_id}", $price_nonmember, ' size = "8" placeholder = ""',
        'text', true, 5, true );
?>
        </td>
        <td style="text-align:center;padding:0px 2px">
<?php
        echo wh_draw_checkbox_field_custom ( "selectSat{$sport_id}",
          "selectSat{$sport_id}", null, $days_selected [6], 'style="display: block; margin: auto"', 4, false );

        $price_member = wh_not_null ( $prices_member [6] ) ?
            $prices_member [6] : '' ;
        $price_nonmember = wh_not_null ( $prices_nonmember [6] ) ?
            $prices_nonmember [6] : '' ;

      echo wh_draw_input_field_custom ( "priceMemberSat{$sport_id}",
        "priceMemberSat{$sport_id}", $price_member, ' size = "5" placeholder = ""',
        'text', true, 5, true );
      echo wh_draw_input_field_custom ( "priceNonmemberSat{$sport_id}",
        "priceNonmemberSat{$sport_id}", $price_nonmember, ' size = "5" placeholder = ""',
        'text', true, 5, true );
?>
        </td>
        <td style="text-align:center;padding:0px 2px">
<?php
        echo wh_draw_checkbox_field_custom ( "selectSun{$sport_id}",
          "selectSun{$sport_id}", null, $days_selected [7], 'style="display: block; margin: auto"', 4, false );

        $price_member = wh_not_null ( $prices_member [7] ) ?
            $prices_member [7] : '' ;
        $price_nonmember = wh_not_null ( $prices_nonmember [7] ) ?
            $prices_nonmember [7] : '' ;

      echo wh_draw_input_field_custom ( "priceMemberSun{$sport_id}",
        "priceMemberSun{$sport_id}", $price_member, ' size = "5" placeholder = ""',
        'text', true, 5, true );
      echo wh_draw_input_field_custom ( "priceNonmemberSun{$sport_id}",
        "priceNonmemberSun{$sport_id}", $price_nonmember, ' size = "5" placeholder = ""',
        'text', true, 5, true );
?>
        </td>
      </tr>
      <tr class="trDaysAll<?php echo $sport_id; ?>">
        <td style="text-align:center" colspan="7">
<?php
        $price_member = wh_not_null ( $prices_member [1] ) ?
            $prices_member [1] : '' ;
        $price_nonmember = wh_not_null ( $prices_nonmember [1] ) ?
            $prices_nonmember [1] : '' ;

      echo wh_draw_label ( 'All days', '' , '', 7, false );
      echo wh_draw_input_field_custom ( "priceMemberAll{$sport_id}",
        "priceMemberAll{$sport_id}", $price_member, ' size = "8" placeholder = ""',
        'text', true, 5, false );
      echo wh_draw_input_field_custom ( "priceNonmemberAll{$sport_id}",
        "priceNonmemberAll{$sport_id}", $price_nonmember, ' size = "8" placeholder = ""',
        'text', true, 5, true );
?>
        </td>
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
