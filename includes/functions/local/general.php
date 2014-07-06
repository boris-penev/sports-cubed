<?php

  /**
   * Die with error
   */
  function wh_error ( $error )
  {
    echo '<div style="color:red">',
          '<h1>' . nl2br ( $error ) . '</h1>',
          '[WHALE STOP]', PHP_EOL,
          '</div>';
    die;
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

  /**
   * Benchmarks a function and outputs to console
   */
  function test ( $func, $iterations = 100, $flag = 'sum' )
  {
    $php_sum = 0.0;
    $php_max = 0.0;
    for ( $i = 0; $i < $iterations; ++$i )
    {
      $starttime = microtime(true);
      $func();
      $endtime = microtime(true);
      $t = $endtime - $starttime;
      if ( $t > $php_max )
        $php_max = $t;
      $php_sum += $t;
    }
    switch ( $flag ) {
    case 'sum':
      echo 'Sum     = ', $php_sum,         ' <br />', PHP_EOL; break;
    case 'average':
      echo 'Average = ', $php_sum / 100.0, ' <br />', PHP_EOL; break;
    case 'max':
      echo 'Max     = ', $php_max,         ' <br />', PHP_EOL; break;
    }
  }
?>
