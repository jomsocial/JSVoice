<?php header("Content-type: text/css");
/**
 * ------------------------------------------------------------------------
 * JA Voice Package for Joomla 2.5 & 3.x
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' ); 

$component_path = dirname( dirname( $_SERVER['REQUEST_URI'] ) );
global $color;
function ieversion() {
  ereg('MSIE ([0-9]\.[0-9])',$_SERVER['HTTP_USER_AGENT'],$reg);
  if(!isset($reg[1])) {
    return -1;
  } else {
    return floatval($reg[1]);
  }
}
$iev = ieversion();

?>
<?php /*All IE*/ ?>

<?php
/*IE 6*/
if ($iev == 6) {
?>

#jav-btfeedback{
	position: absolute !important;
	top: expression( ( 200 + ( ignoreMe = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop ) ) + 'px' ) !important;
}

#jav-feedback_overlay {
	position: absolute !important;
	height: expression(document.documentElement.clientHeight + 'px') !important;
	top: expression( ( 0 + ( ignoreMe = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop ) ) + 'px' ) !important;
}

#jav-feedback_dialog {
	position: absolute !important;
	top: expression( ( 20 + ( ignoreMe = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop ) ) + 'px' ) !important;
}

#jav-feedback_dialog iframe{
	background: none !important;
}

a.jav-copyright {
	filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php echo $component_path;?>/images/powered-by.png', sizingMethod='crop');
 	background-image: none !important;
}

a#jav-feedback_close {
	filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php echo $component_path;?>/images/feedback-close.png', sizingMethod='crop');
 	background-image: none !important;
}

a#jav-feedback_close:hover {
	filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?php echo $component_path;?>/images/feedback-close-hover.png', sizingMethod='crop');
 	background-image: none !important;
}

.button {
	overflow: visible;
}

<?php
}
?>


<?php
/*IE 7*/
if ($iev == 7) {
?>


<?php
}
?>


<?php
/*IE 8*/
if ($iev == 8) {
?>

<?php
}
?>
