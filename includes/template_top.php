<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2012 osCommerce

  Released under the GNU General Public License
*/

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" <?php echo HTML_PARAMS; ?>>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>" />
	<title><?php echo wh_output_string_protected($whTemplate->getTitle()); ?></title>
	<!-- <base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>" /> -->
	<link rel="stylesheet" type="text/css" href="ext/jquery/ui/redmond/jquery-ui-1.8.22.css" />
	<!-- jQuery  -->
	<script type="text/javascript" src="ext/jquery/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="ext/jquery/ui/jquery-ui-1.10.3.min.js"></script>

	<meta charset="<?php echo CHARSET; ?>" />
	<!-- HTML5 Shim -->
	<!--[if lt IE 9]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
	<!-- Modernizr -->
	<script type="text/javascript" src="ext/modernizr.js"></script>
	<!-- Webforms2 -->
	<script type="text/javascript" src="ext/webforms2-0/webforms2-p.js"></script>
	<!-- Custom Scripts -->
	<script type="text/javascript" src="includes/general.js"></script>
<!--	<script type="text/javascript" src="includes/general-local.js"></script> -->

	<!-- Feuille de style -->
	<link rel="stylesheet" type="text/css" href="ext/Formulaire-HTML5-41Mag/styleFormulaire.css">
	<!-- jQuery Color Picker -->
	<link rel="stylesheet" type="text/css" href="ext/Formulaire-HTML5-41Mag/colorpicker.css">
	<script type="text/javascript" src="ext/Formulaire-HTML5-41Mag/colorpicker.js"></script>
	<!-- jQuery Numeric Spinner -->
	<script type="text/javascript" src="ext/spinner.js"></script>

	<!-- jQuery Placehol
		<script src="jquery.placehold-0.2.min.js"></script>  -->

	<!-- Demo page layout
	<link rel="stylesheet" href="html5forms.layout.css">  -->
	<script type="text/javascript" src="ext/Formulaire-HTML5-41Mag/html5forms.js"></script>
	<script type="text/javascript" src="ext/Formulaire-HTML5-41Mag/html5forms.fallback.js"></script>

<!--
	<script type="text/javascript">
	// fix jQuery 1.8.0 and jQuery UI 1.8.22 bug with dialog buttons; http://bugs.jqueryui.com/ticket/8484
	if ( $.attrFn ) { $.attrFn.text = true; }
	</script>
-->

	<script type="text/javascript" src="ext/jquery/bxGallery/jquery.bxGallery.1.1.min.js"></script>
	<link rel="stylesheet" type="text/css" href="ext/jquery/fancybox/jquery.fancybox-1.3.4.css" />
	<script type="text/javascript" src="ext/jquery/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
	<link rel="stylesheet" type="text/css" href="ext/960gs/<?php echo ((stripos(HTML_PARAMS, 'dir="rtl"') !== false) ? 'rtl_' : ''); ?>960_24_col.css" />
	<link rel="stylesheet" type="text/css" href="stylesheet.css" />
</head>
<body>

<div id="bodyWrapper" class="container_<?php echo $whTemplate->getGridContainerWidth(); ?>">

<?php require(DIR_WS_INCLUDES . 'header.php'); ?>

<div id="bodyContent" class="grid_<?php echo $whTemplate->getGridContentWidth(); ?> <?php echo ($whTemplate->hasBlocks('boxes_column_left') ? 'push_' . $whTemplate->getGridColumnWidth() : ''); ?>">
