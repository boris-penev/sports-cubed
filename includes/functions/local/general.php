<?php

  /**
   * Die with error
   */
  function wh_error ( $error )
  {
    die('<font color="#ff0000"><strong>' . $error . '<br /><br /><small><font color="#ff0000">[WHALE STOP]</font></small><br /><br /></strong></font>');
  }

  /**
   * Check if variable is null, alias to wh_not_null with the opposite value
   */
  function wh_null ( $arg )
  {
    return ( ! wh_not_null ( $arg ) );
  }

  /**
   * Define a constant
   */
  function wh_define ( $name, $value )
  {
    if ( defined ( $name ) )
      return;
    define ( $name, $value );
  }

  /**
   * Check if constant is defined and not null
   */
  function wh_defined ( $name )
  {
    return defined ( $name ) && wh_not_null ( constant ( $name ) );
  }

  /**
   * Check if a value is null and return it if not
   * @return the input value or nothing
   */
  function wh_value ( $var )
  {
    if ( wh_not_null ($var) ) {
      return $var;
    }
    return;
  }

  /**
   * @return the name and value of variable for associative array or nothing
   */
  function all_or_nothing ( $arg )
  {
    if ( $arg )
    {
      return "'{$arg}' => {$$arg}";
    }
    return;
  }

  /**
   * Check if the time is valid
   */
  function validate_time ($time, $format = 'H:i:s')
  {
    $timezone = new DateTimeZone('UTC');
    $datetime = DateTime::createFromFormat($format, $time, $timezone);
    return $datetime && $datetime->format($format) == $time;
  }
?>
