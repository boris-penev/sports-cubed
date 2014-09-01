<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

?>

<div class="mainBlock">
  <h1>Welcome to Sports Cubed!</h1>
</div>

<div class="contentBlock">
  <div class="infoPane">
    <h3>Server Capabilities</h3>

    <div class="infoPaneContents">
      <table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr>
          <td><strong>PHP Version</strong></td>
          <td align="right"><?php echo PHP_VERSION; ?></td>
          <td align="right" width="25"><img src="images/<?php echo ((PHP_VERSION >= 5.4) ? 'tick.gif' : 'cross.gif'); ?>" border="0" width="16" height="16"></td>
        </tr>
      </table>

      <br />

      <table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr>
          <td><strong>Required PHP Extensions</strong></td>
          <td align="right" width="25"></td>
        </tr>
        <tr>
          <td>MySQLi</td>
          <td align="right"><img src="images/<?php echo (extension_loaded('mysqli') ? 'tick.gif' : 'cross.gif'); ?>" border="0" width="16" height="16"></td>
        </tr>
        <tr>
          <td>cURL</td>
          <td align="right"><img src="images/<?php echo (extension_loaded('curl') ? 'tick.gif' : 'cross.gif'); ?>" border="0" width="16" height="16"></td>
        </tr>
      </table>

    </div>
  </div>

  <div class="contentPane">
    <h2>New Installation</h2>

<?php
  $configfile_array = array();

  if (file_exists(wh_realpath(dirname(__FILE__) . '/../../../includes') . '/configure.php') && !wh_is_writable(wh_realpath(dirname(__FILE__) . '/../../../includes') . '/configure.php')) {
    @chmod(wh_realpath(dirname(__FILE__) . '/../../../includes') . '/configure.php', 0660);
  }

  if (file_exists(wh_realpath(dirname(__FILE__) . '/../../../includes') . '/configure.php') && !wh_is_writable(wh_realpath(dirname(__FILE__) . '/../../../includes') . '/configure.php')) {
    $configfile_array[] = wh_realpath(dirname(__FILE__) . '/../../../includes') . '/configure.php';
  }

  $warning_array = array();

  if (!extension_loaded('mysqli')) {
    $warning_array['mysqli'] = 'The MySQLi extension is required but is not installed. Please enable it to continue installation.';
  }

  if (!extension_loaded('curl')) {
    $warning_array['curl'] = 'The cURL extension is required but is not installed. Please enable it to continue installation.';
  }

  if ((sizeof($configfile_array) > 0) || (sizeof($warning_array) > 0)) {
?>

    <div class="noticeBox">

<?php
    if (sizeof($warning_array) > 0) {
?>

      <table border="0" width="100%" cellspacing="0" cellpadding="2" style="background: #fffbdf; border: 1px solid #ffc20b; padding: 2px;">

<?php
      reset($warning_array);
      while (list($key, $value) = each($warning_array)) {
        echo '        <tr>' . "\n" .
             '          <td valign="top"><strong>' . $key . '</strong></td>' . "\n" .
             '          <td valign="top">' . $value . '</td>' . "\n" .
             '        </tr>' . "\n";
      }
?>

      </table>
<?php
    }

    if (sizeof($configfile_array) > 0) {
?>

      <p>The webserver is not able to save the installation parameters to its configuration files.</p>
      <p>The following files need to have their file permissions set to server-writeable (chmod 660):</p>
      <p>

<?php
      for ($i=0, $n=sizeof($configfile_array); $i<$n; $i++) {
        echo $configfile_array[$i];

        if (isset($configfile_array[$i+1])) {
          echo '<br />';
        }
      }
?>

      </p>

<?php
    }
?>

    </div>

<?php
  }

  if ((sizeof($configfile_array) > 0) || (sizeof($warning_array) > 0)) {
?>

    <p>Please correct the above errors and retry the installation procedure with the changes in place.</p>

<?php
    if (sizeof($warning_array) > 0) {
      echo '    <p><i>Changing webserver configuration parameters may require the webserver service to be restarted before the changes take affect.</i></p>' . "\n";
    }
?>

    <p align="right"><a href="index.php"><img src="images/button_retry.gif" border="0" alt="Retry" /></a></p>

<?php
  } else {
?>

    <p>The webserver environment has been verified to proceed with a successful installation and configuration of Sports Cubed.</p>
    <p>Please continue to start the installation procedure.</p>
    <p align="right"><a href="install.php"><img src="images/button_continue.gif" border="0" alt="Continue" /></a></p>

<?php
  }
?>

  </div>
</div>
