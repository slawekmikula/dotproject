<?php 
##
## holiday module - A dotProject module for keeping track of holidays
##
## Sensorlink AS (c) 2006
## Vegard Fiksdal (fiksdal@sensorlink.no)
##

// DotProject and PHP stuff
$AppUI->savePlace();

// Get current tab 
if (isset( $_GET['tab'] ))
{
	$AppUI->setState( 'ProjIdxTab', $_GET['tab'] );
}
$tab = $AppUI->getState( 'ProjIdxTab' ) !== NULL ? $AppUI->getState( 'ProjIdxTab' ) : 0;

// Create module header
?><table width="100%" border="0" cellpadding="3" cellspacing="1">
<tr valign="top">
<td width="32"><img src="./images/calendar2.gif" title="Holidays" border="0" height="24" width="24"></td>
<td nowrap><h1><?php echo $AppUI->_('Holidays');?></h1></td>
</table>

<?php
// tabbed information boxes
$tabBox = new CTabBox( "?m=holiday", "{$dPconfig['root_dir']}/modules/holiday/", $tab );
$tabBox->add("holiday_white","Whitelist");
$tabBox->add("holiday_black","Blacklist");
$tabBox->add("holiday_options","Settings");
$tabBox->show();

?>


