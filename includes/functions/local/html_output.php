<?php
////
// Output a form input field
  function wh_draw_input_field_custom($name, $id = '', $value = '', $parameters = '', $type = 'text', $reinsert_value = true, $indent = 0, $newline = false) {
    global $_GET, $_POST;

    $field = str_repeat ("\t", $indent);
    $field .= '<input type="' . wh_output_string($type) . '" name="' . wh_output_string($name) . '"';
    if ( wh_not_null ( $id ) ) {
			$field .= ' id="' . wh_output_string($id) . '"';
		}

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

    if (wh_not_null($parameters)) {
			$field .= ' ' . $parameters;
		}

    $field .= ' />';

    if ( $newline ) {
			$field .= ' <br />';
		}
		$field .= PHP_EOL;

    return $field;
  }

////
// Output a form input field with label
  function wh_draw_input_field_label ($name, $id = '', $label = '', $label_id = '', $value = '', $parameters = '', $type = 'text', $reinsert_value = true, $indent = 0, $newline = false)
  {
		$field = str_repeat ("\t", $indent);
		$field .= '<label';
		if ( wh_not_null($label_id) ) {
			$field .= ' id ="' . wh_output_string($label_id) . '"';
		}
		$field .= '>';
		$field .= PHP_EOL . str_repeat ("\t", $indent + 1);
		$field .= $label;
		$field .= PHP_EOL;
		$field .= wh_draw_input_field_custom ( $name, $id, $value, $parameters,
			$type, $reinsert_value, $indent + 1, false );
		$field .= str_repeat ("\t", $indent);
		$field .= '</label>';

		if ( $newline ) {
			$field .= ' <br />';
		}
		$field .= PHP_EOL;

		return $field;
	}

// Output a form textarea field
// The $wrap parameter is no longer used in the core xhtml template
  function wh_draw_textarea_field_custom($name, $id, $width, $height, $text = '', $parameters = '', $reinsert_value = true, $indent = 0, $newline = false) {
    global $_GET, $_POST;

    $field = str_repeat ("\t", $indent);
    $field .= '<textarea name="' . wh_output_string($name) . '" cols="' . wh_output_string($width) . '" rows="' . wh_output_string($height) . '"';
    if ( wh_not_null ( $id ) ) {
			$field .= ' id="' . wh_output_string($id) . '"';
		}

    if (wh_not_null($parameters)) {
			$field .= ' ' . $parameters;
		}

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

    if ( $newline ) {
			$field .= ' <br />';
		}
		$field .= PHP_EOL;

    return $field;
  }

////
// Output a form textarea field with label
	function wh_draw_textarea_field_label ($name, $id = '', $label = '', $label_id = '', $width = 20, $height = 3, $text = '', $parameters = '', $reinsert_value = true, $indent = 0, $newline = false)
  {
		$field = str_repeat ("\t", $indent);
		$field .= '<label';
		if ( wh_not_null($label_id) ) {
			$field .= ' id ="' . wh_output_string($label_id) . '"';
		}
		$field .= '>';
		$field .= PHP_EOL . str_repeat ("\t", $indent + 1);
		$field .= $label;
		$field .= PHP_EOL;
		$field .= wh_draw_textarea_field_custom ( $name, $id, $width, $height,
			$text, $parameters, $reinsert_value, $indent + 1, false );
		$field .= str_repeat ("\t", $indent);
		$field .= '</label>';

		if ( $newline ) {
			$field .= ' <br />';
		}
		$field .= PHP_EOL;

		return $field;
	}

////
// Output a form pull down menu
//($name, $id = '', $label = '', $label_id = '', $width = 20, $height = 3, $text = '', $parameters = '', )
  function wh_draw_pull_down_menu_custom($name, $values, $id = '', $default = '', $parameters = '', $required = false, $reinsert_value = true, $indent = 0, $newline = false)
  {
    global $_GET, $_POST;

		$field = str_repeat ("\t", $indent);
    $field .= '<select name="' . wh_output_string($name) . '"';

    if ( wh_not_null ( $id ) ) {
			$field .= ' id="' . wh_output_string($id) . '"';
		}

    if (wh_not_null($parameters)) {
			$field .= ' ' . $parameters;
		}

    if (empty($default) && ( (isset($_GET[$name]) && is_string($_GET[$name])) || (isset($_POST[$name]) && is_string($_POST[$name])) ) ) {
      if (isset($_GET[$name]) && is_string($_GET[$name])) {
        $default = stripslashes($_GET[$name]);
        $field .= ' selectedIndex="' . $default . '"';
      } elseif (isset($_POST[$name]) && is_string($_POST[$name])) {
        $default = stripslashes($_POST[$name]);
        $field .= ' selectedIndex="' . $default . '"';
      }
    }

    $field .= '>';
    $field .= PHP_EOL;

    foreach ($values as $key => $value)
    {
      $field .= str_repeat ("\t", $indent+1);
      $field .= '<option value="' . wh_output_string($key) . '"';
      if ($default == $key) {
        $field .= ' selected="selected"';
      }

      $field .= '>' . wh_output_string($value, array('"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;')) . '</option>';
      $field .= PHP_EOL;
    }
    $field .= str_repeat ("\t", $indent);
    $field .= '</select>';

    if ($required == true) {
			$field .= TEXT_FIELD_REQUIRED;
		}

		if ( $newline ) {
			$field .= ' <br />';
		}
		$field .= PHP_EOL;

    return $field;
  }

////
// Output a form textarea field with label
	function wh_draw_pull_down_menu_label($name, $values, $id = '', $label = '', $label_id = '', $default = '', $parameters = '', $required = false, $reinsert_value = true, $indent = 0, $newline = false)
	{
		$field = str_repeat ("\t", $indent);
		$field .= '<label';
		if ( wh_not_null($label_id) ) {
			$field .= ' id ="' . wh_output_string($label_id) . '"';
		}
		$field .= '>';
		$field .= PHP_EOL . str_repeat ("\t", $indent + 1);
		$field .= $label;
		$field .= PHP_EOL;
		$field .= wh_draw_pull_down_menu_custom ( $name, $values, $id, $default,
			$parameters, $required, $reinsert_value, $indent + 1, false );
		$field .= str_repeat ("\t", $indent);
		$field .= '</label>';

		if ( $newline ) {
			$field .= ' <br />';
		}
		$field .= PHP_EOL;

		return $field;
	}

////
// Output a selection field - alias function for wh_draw_checkbox_field() and wh_draw_radio_field()
  function wh_draw_selection_field_custom($name, $type, $id, $value = '', $checked = false,
		$parameters = '', $indent = 0, $newline = false) {
    global $_GET, $_POST;

    $selection = str_repeat ("\t", $indent);
    $selection .= '<input type="' . wh_output_string($type) . '" name="' . wh_output_string($name) . '"';

    if ( wh_not_null ( $id ) ) {
			$selection .= ' id="' . wh_output_string($id) . '"';
		}

    if ( wh_not_null($value) ) {
			$selection .= ' value="' . wh_output_string($value) . '"';
		}

    if ( ($checked == true) || (isset($_GET[$name]) && is_string($_GET[$name]) && (($_GET[$name] == 'on') || (stripslashes($_GET[$name]) == $value))) || (isset($_POST[$name]) && is_string($_POST[$name]) && (($_POST[$name] == 'on') || (stripslashes($_POST[$name]) == $value))) ) {
      $selection .= ' checked="checked"';
    }

		if (wh_not_null($parameters)) {
			$selection .= ' ' . $parameters;
		}

    $selection .= ' />';

     if ( $newline ) {
			$selection .= ' <br />';
		}
		$selection .= PHP_EOL;

    return $selection;
  }

////
// Output a form checkbox field
  function wh_draw_checkbox_field_custom($name, $id, $value = '', $checked = false,
		$parameters = '', $indent = 0, $newline = false) {
    return wh_draw_selection_field_custom($name, 'checkbox', $id, $value, $checked,
			$parameters, $indent, $newline);
  }

////
// Output a form radio field
  function wh_draw_radio_field_custom($name, $id, $value = '', $checked = false,
		$parameters = '', $indent = 0, $newline = false) {
    return wh_draw_selection_field_custom($name, 'radio', $id, $value, $checked,
			$parameters, $indent, $newline);
  }

////
// Output a form selection field with label
	function wh_draw_selection_field_label ($name, $type, $id = '', $label = '', $label_id = '',
		$value = '', $checked = false, $parameters = '', $parameters_label = '', $indent = 0, $newline = false)
  {
		$field = str_repeat ("\t", $indent);
		$field .= '<label';
		if ( wh_not_null($label_id) ) {
			$field .= ' id ="' . wh_output_string($label_id) . '"';
		}
		if (wh_not_null($parameters_label)) {
			$field .= ' ' . $parameters_label;
		}
		$field .= '>';
		$field .= PHP_EOL . str_repeat ("\t", $indent + 1);
		$field .= $label;
		$field .= PHP_EOL;
		$field .= wh_draw_selection_field_custom ( $name, $type, $id, $value,
			$checked,	$parameters, $indent + 1, false );
		$field .= str_repeat ("\t", $indent);
		$field .= '</label>';

		if ( $newline ) {
			$field .= ' <br />';
		}
		$field .= PHP_EOL;

		return $field;
	}

////
// Output a form checkbox field with label
  function wh_draw_checkbox_field_label($name, $id, $label, $label_id = '', $value = '',
		$checked = false, $parameters = '', $parameters_label = '', $indent = 0, $newline = false) {
    return wh_draw_selection_field_label($name, 'checkbox', $id, $label, $label_id = '',
			$value, $checked, $parameters, $parameters_label, $indent, $newline);
  }

////
// Output a form radio field with label
  function wh_draw_radio_field_label($name, $id, $label, $label_id = '', $value = '',
		$checked = false, $parameters = '', $parameters_label = '', $indent = 0, $newline = false) {
    return wh_draw_selection_field_label($name, 'radio', $id, $label, $label_id = '',
			$value, $checked, $parameters, $parameters_label, $indent, $newline);
  }

////
// Output a label
  function wh_draw_label ($label, $id = '', $parameters = '',
		$indent = 0, $newline = false)
  {
		$field = str_repeat ("\t", $indent);
		$field .= '<label';
		if ( wh_not_null($id) ) {
			$field .= ' id ="' . wh_output_string($id) . '"';
		}
		if (wh_not_null($parameters)) {
			$field .= ' ' . $parameters;
		}
		$field .= '>';
		$field .= PHP_EOL . str_repeat ("\t", $indent + 1);
		$field .= $label;
		$field .= PHP_EOL;
		$field .= str_repeat ("\t", $indent);
		$field .= '</label>';

		if ( $newline ) {
			$field .= ' <br />';
		}
		$field .= PHP_EOL;

		return $field;
	}

?>
