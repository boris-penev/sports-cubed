<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

////
// The HTML href link wrapper function
  function wh_href_link($page = '', $parameters = '', $connection = 'NONSSL', $add_session_id = true, $search_engine_safe = true) {
    global $request_type, $session_started, $SID;

    $page = wh_output_string($page);

    if (!wh_not_null($page)) {
      die('</td></tr></table></td></tr></table><br /><br /><font color="#ff0000"><strong>Error!</strong></font><br /><br /><strong>Unable to determine the page link!<br /><br />');
    }

    if ($connection == 'NONSSL') {
      $link = HTTP_SERVER . DIR_WS_HTTP_CATALOG;
    } elseif ($connection == 'SSL') {
      if (ENABLE_SSL == true) {
        $link = HTTPS_SERVER . DIR_WS_HTTPS_CATALOG;
      } else {
        $link = HTTP_SERVER . DIR_WS_HTTP_CATALOG;
      }
    } else {
      die('</td></tr></table></td></tr></table><br /><br /><font color="#ff0000"><strong>Error!</strong></font><br /><br /><strong>Unable to determine connection method on a link!<br /><br />Known methods: NONSSL SSL</strong><br /><br />');
    }

    if (wh_not_null($parameters)) {
      $link .= $page . '?' . wh_output_string($parameters);
      $separator = '&';
    } else {
      $link .= $page;
      $separator = '?';
    }

    while ( (substr($link, -1) == '&') || (substr($link, -1) == '?') ) $link = substr($link, 0, -1);

// Add the session ID when moving from different HTTP and HTTPS servers, or when SID is defined
    if ( ($add_session_id == true) && ($session_started == true) && (SESSION_FORCE_COOKIE_USE == 'False') ) {
      if (wh_not_null($SID)) {
        $_sid = $SID;
      } elseif ( ( ($request_type == 'NONSSL') && ($connection == 'SSL') && (ENABLE_SSL == true) ) || ( ($request_type == 'SSL') && ($connection == 'NONSSL') ) ) {
        if (HTTP_COOKIE_DOMAIN != HTTPS_COOKIE_DOMAIN) {
          $_sid = wh_session_name() . '=' . wh_session_id();
        }
      }
    }

    if (isset($_sid)) {
      $link .= $separator . wh_output_string($_sid);
    }

    while (strstr($link, '&&')) $link = str_replace('&&', '&', $link);

    if ( (SEARCH_ENGINE_FRIENDLY_URLS == 'true') && ($search_engine_safe == true) ) {
      $link = str_replace('?', '/', $link);
      $link = str_replace('&', '/', $link);
      $link = str_replace('=', '/', $link);
    } else {
      $link = str_replace('&', '&amp;', $link);
    }

    return $link;
  }

////
// The HTML image wrapper function
  function wh_image($src, $alt = '', $width = '', $height = '', $parameters = '') {
    if ( (empty($src) || ($src == DIR_WS_IMAGES)) && (IMAGE_REQUIRED == 'false') ) {
      return false;
    }

// alt is added to the img tag even if it is null to prevent browsers from outputting
// the image filename as default
    $image = '<img src="' . wh_output_string($src) . '" alt="' . wh_output_string($alt) . '"';

    if (wh_not_null($alt)) {
      $image .= ' title="' . wh_output_string($alt) . '"';
    }

    if ( (CONFIG_CALCULATE_IMAGE_SIZE == 'true') && (empty($width) || empty($height)) ) {
      if ($image_size = @getimagesize($src)) {
        if (empty($width) && wh_not_null($height)) {
          $ratio = $height / $image_size[1];
          $width = intval($image_size[0] * $ratio);
        } elseif (wh_not_null($width) && empty($height)) {
          $ratio = $width / $image_size[0];
          $height = intval($image_size[1] * $ratio);
        } elseif (empty($width) && empty($height)) {
          $width = $image_size[0];
          $height = $image_size[1];
        }
      } elseif (IMAGE_REQUIRED == 'false') {
        return false;
      }
    }

    if (wh_not_null($width) && wh_not_null($height)) {
      $image .= ' width="' . wh_output_string($width) . '" height="' . wh_output_string($height) . '"';
    }

    if (wh_not_null($parameters)) $image .= ' ' . $parameters;

    $image .= ' />';

    return $image;
  }

////
// The HTML form submit button wrapper function
// Outputs a button in the selected language
  function wh_image_submit($image, $alt = '', $parameters = '') {
    global $language;

    $image_submit = '<input type="image" src="' . wh_output_string(DIR_WS_LANGUAGES . $language . '/images/buttons/' . $image) . '" alt="' . wh_output_string($alt) . '"';

    if (wh_not_null($alt)) $image_submit .= ' title=" ' . wh_output_string($alt) . ' "';

    if (wh_not_null($parameters)) $image_submit .= ' ' . $parameters;

    $image_submit .= ' />';

    return $image_submit;
  }

////
// Output a function button in the selected language
  function wh_image_button($image, $alt = '', $parameters = '') {
    global $language;

    return wh_image(DIR_WS_LANGUAGES . $language . '/images/buttons/' . $image, $alt, '', '', $parameters);
  }

////
// Output a separator either through whitespace, or with an image
  function wh_draw_separator($image = 'pixel_black.gif', $width = '100%', $height = '1') {
    return wh_image(DIR_WS_IMAGES . $image, '', $width, $height);
  }

////
// Output a form
  function wh_draw_form($name, $action, $method = 'post', $parameters = '', $tokenize = false) {
    global $sessiontoken;

    $form = '<form name="' . wh_output_string($name) . '" action="' . wh_output_string($action) . '" method="' . wh_output_string($method) . '"';

    if (wh_not_null($parameters)) $form .= ' ' . $parameters;

    $form .= '>';

    if ( ($tokenize == true) && isset($sessiontoken) ) {
      $form .= '<input type="hidden" name="formid" value="' . wh_output_string($sessiontoken) . '" />';
    }

    return $form;
  }

////
// Output a form input field
  function wh_draw_input_field($name, $value = '', $parameters = '', $type = 'text', $reinsert_value = true) {
    $field = '<input type="' . wh_output_string($type) . '" name="' . wh_output_string($name) . '"';

    if ( ($reinsert_value == true) && ( (isset($_GET[$name]) && is_string($_GET[$name])) || (isset($_POST[$name]) && is_string($_POST[$name])) ) ) {
      if (isset($_GET[$name]) && is_string($_GET[$name])) {
        $value = stripslashes($_GET[$name]);
      } elseif (isset($_POST[$name]) && is_string($_POST[$name])) {
        $value = stripslashes($_POST[$name]);
      }
    }

    if (wh_not_null($value)) {
      $field .= ' value="' . wh_output_string($value) . '"';
    }

    if (wh_not_null($parameters)) $field .= ' ' . $parameters;

    $field .= ' />';

    return $field;
  }

////
// Output a form password field
  function wh_draw_password_field($name, $value = '', $parameters = 'maxlength="40"') {
    return wh_draw_input_field($name, $value, $parameters, 'password', false);
  }

////
// Output a selection field - alias function for wh_draw_checkbox_field() and wh_draw_radio_field()
  function wh_draw_selection_field($name, $type, $value = '', $checked = false, $parameters = '') {
    $selection = '<input type="' . wh_output_string($type) . '" name="' . wh_output_string($name) . '"';

    if (wh_not_null($value)) $selection .= ' value="' . wh_output_string($value) . '"';

    if ( ($checked == true) || (isset($_GET[$name]) && is_string($_GET[$name]) && (($_GET[$name] == 'on') || (stripslashes($_GET[$name]) == $value))) || (isset($_POST[$name]) && is_string($_POST[$name]) && (($_POST[$name] == 'on') || (stripslashes($_POST[$name]) == $value))) ) {
      $selection .= ' checked="checked"';
    }

    if (wh_not_null($parameters)) $selection .= ' ' . $parameters;

    $selection .= ' />';

    return $selection;
  }

////
// Output a form checkbox field
  function wh_draw_checkbox_field($name, $value = '', $checked = false, $parameters = '') {
    return wh_draw_selection_field($name, 'checkbox', $value, $checked, $parameters);
  }

////
// Output a form radio field
  function wh_draw_radio_field($name, $value = '', $checked = false, $parameters = '') {
    return wh_draw_selection_field($name, 'radio', $value, $checked, $parameters);
  }

////
// Output a form textarea field
// The $wrap parameter is no longer used in the core xhtml template
  function wh_draw_textarea_field($name, $wrap, $width, $height, $text = '', $parameters = '', $reinsert_value = true) {
    $field = '<textarea name="' . wh_output_string($name) . '" cols="' . wh_output_string($width) . '" rows="' . wh_output_string($height) . '"';

    if (wh_not_null($parameters)) $field .= ' ' . $parameters;

    $field .= '>';

    if ( ($reinsert_value == true) && ( (isset($_GET[$name]) && is_string($_GET[$name])) || (isset($_POST[$name]) && is_string($_POST[$name])) ) ) {
      if (isset($_GET[$name]) && is_string($_GET[$name])) {
        $field .= wh_output_string_protected(stripslashes($_GET[$name]));
      } elseif (isset($_POST[$name]) && is_string($_POST[$name])) {
        $field .= wh_output_string_protected(stripslashes($_POST[$name]));
      }
    } elseif (wh_not_null($text)) {
      $field .= wh_output_string_protected($text);
    }

    $field .= '</textarea>';

    return $field;
  }

////
// Output a form hidden field
  function wh_draw_hidden_field($name, $value = '', $parameters = '') {
    $field = '<input type="hidden" name="' . wh_output_string($name) . '"';

    if (wh_not_null($value)) {
      $field .= ' value="' . wh_output_string($value) . '"';
    } elseif ( (isset($_GET[$name]) && is_string($_GET[$name])) || (isset($_POST[$name]) && is_string($_POST[$name])) ) {
      if ( (isset($_GET[$name]) && is_string($_GET[$name])) ) {
        $field .= ' value="' . wh_output_string(stripslashes($_GET[$name])) . '"';
      } elseif ( (isset($_POST[$name]) && is_string($_POST[$name])) ) {
        $field .= ' value="' . wh_output_string(stripslashes($_POST[$name])) . '"';
      }
    }

    if (wh_not_null($parameters)) $field .= ' ' . $parameters;

    $field .= ' />';

    return $field;
  }

////
// Hide form elements
  function wh_hide_session_id() {
    global $session_started, $SID;

    if (($session_started == true) && wh_not_null($SID)) {
      return wh_draw_hidden_field(wh_session_name(), wh_session_id());
    }
  }

////
// Output a form pull down menu
  function wh_draw_pull_down_menu($name, $values, $default = '', $parameters = '', $required = false) {
    $field = '<select name="' . wh_output_string($name) . '"';

    if (wh_not_null($parameters)) $field .= ' ' . $parameters;

    $field .= '>';

    if (empty($default) && ( (isset($_GET[$name]) && is_string($_GET[$name])) || (isset($_POST[$name]) && is_string($_POST[$name])) ) ) {
      if (isset($_GET[$name]) && is_string($_GET[$name])) {
        $default = stripslashes($_GET[$name]);
      } elseif (isset($_POST[$name]) && is_string($_POST[$name])) {
        $default = stripslashes($_POST[$name]);
      }
    }

    for ($i=0, $n=sizeof($values); $i<$n; $i++) {
      $field .= '<option value="' . wh_output_string($values[$i]['id']) . '"';
      if ($default == $values[$i]['id']) {
        $field .= ' selected="selected"';
      }

      $field .= '>' . wh_output_string($values[$i]['text'], array('"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;')) . '</option>';
    }
    $field .= '</select>';

    if ($required == true) $field .= TEXT_FIELD_REQUIRED;

    return $field;
  }

////
// Creates a pull-down list of countries
  function wh_get_country_list($name, $selected = '', $parameters = '') {
    $countries_array = array(array('id' => '', 'text' => PULL_DOWN_DEFAULT));
    $countries = wh_get_countries();

    for ($i=0, $n=sizeof($countries); $i<$n; $i++) {
      $countries_array[] = array('id' => $countries[$i]['countries_id'], 'text' => $countries[$i]['countries_name']);
    }

    return wh_draw_pull_down_menu($name, $countries_array, $selected, $parameters);
  }

////
// Output a jQuery UI Button
  function wh_draw_button($title = null, $icon = null, $link = null, $priority = null, $params = null) {
    static $button_counter = 1;

    $types = array('submit', 'button', 'reset');

    if ( !isset($params['type']) ) {
      $params['type'] = 'submit';
    }

    if ( !in_array($params['type'], $types) ) {
      $params['type'] = 'submit';
    }

    if ( ($params['type'] == 'submit') && isset($link) ) {
      $params['type'] = 'button';
    }

    if (!isset($priority)) {
      $priority = 'secondary';
    }

    $button = '<span class="tdbLink">';

    if ( ($params['type'] == 'button') && isset($link) ) {
      $button .= '<a id="tdb' . $button_counter . '" href="' . $link . '"';

      if ( isset($params['newwindow']) ) {
        $button .= ' target="_blank"';
      }
    } else {
      $button .= '<button id="tdb' . $button_counter . '" type="' . wh_output_string($params['type']) . '"';
    }

    if ( isset($params['params']) ) {
      $button .= ' ' . $params['params'];
    }

    $button .= '>' . $title;

    if ( ($params['type'] == 'button') && isset($link) ) {
      $button .= '</a>';
    } else {
      $button .= '</button>';
    }

    $button .= '</span><script type="text/javascript">$("#tdb' . $button_counter . '").button(';

    $args = array();

    if ( isset($icon) ) {
      if ( !isset($params['iconpos']) ) {
        $params['iconpos'] = 'left';
      }

      if ( $params['iconpos'] == 'left' ) {
        $args[] = 'icons:{primary:"ui-icon-' . $icon . '"}';
      } else {
        $args[] = 'icons:{secondary:"ui-icon-' . $icon . '"}';
      }
    }

    if (empty($title)) {
      $args[] = 'text:false';
    }

    if (!empty($args)) {
      $button .= '{' . implode(',', $args) . '}';
    }

    $button .= ').addClass("ui-priority-' . $priority . '").parent().removeClass("tdbLink");</script>';

    $button_counter++;

    return $button;
  }
?>
