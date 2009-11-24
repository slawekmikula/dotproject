<?php
##
## holiday module - A dotProject module for keeping track of holidays
##
## Sensorlink AS (c) 2006
## Vegard Fiksdal (fiksdal@sensorlink.no)
##


$action = @$_REQUEST["action"];
if($action=="update") {
	$holiday_manual = $_POST["holiday_manual"];
	$holiday_auto = $_POST["holiday_auto"];
	$holiday_driver = $_POST["holiday_driver"];

	db_exec("UPDATE holiday_settings SET holiday_manual=".($holiday_manual+0));
	db_exec("UPDATE holiday_settings SET holiday_auto=".($holiday_auto+0));
	db_exec("UPDATE holiday_settings SET holiday_driver=".($holiday_driver+0));

        $AppUI->setMsg( "Settings updated" );
	$AppUI->redirect();
}

require_once 'PEAR/Holidays.php';

// Filter out available driver titles (TODO: Bother checking up the proper API)
$drivers_alloc = Date_Holidays::getInstalledDrivers();
for($i=0;$drivers_alloc[$i];$i++){
        $drivers_available[$i]=$drivers_alloc[$i]['title'];
}

// Query database
$holiday_manual = db_loadResult( 'SELECT holiday_manual FROM holiday_settings' );
$holiday_auto = db_loadResult( 'SELECT holiday_auto FROM holiday_settings' );
$holiday_driver = db_loadResult( 'SELECT holiday_driver FROM holiday_settings' );

?>

<form name="AddEdit" method="post">				
<input name="action" type="hidden" value="update">

<table border="1" cellpadding="4" cellspacing="0" width="100%" class="std">
<tr>
        <td><?php
                echo $AppUI->_( 'Enable manual holidays' );
                echo "<input type='checkbox' value='1' name='holiday_manual' ";
                echo $holiday_manual ? "checked='checked' />" : "/>";
        ?></td>
        <td><?php
                echo $AppUI->_( 'Enable automatic holidays' );
                echo "<input type='checkbox' value='1' name='holiday_auto' ";
                echo $holiday_auto ? "checked='checked' />" : "/>";
        ?></td>
        <td><?php
                echo $AppUI->_( 'Holiday driver ' );
                echo arraySelect( $drivers_available,"holiday_driver",null,$holiday_driver);
        ?></td>
</tr>

<table border="0" cellspacing="0" cellpadding="3" width="100%">
<tr>
	<td height="40" width="30%">&nbsp;</td>
	<td  height="40" width="35%" align="right">
		<table>
		<tr><td>
			<input class="button" type="button" name="btnFuseAction" value="<?php echo $AppUI->_('save'); ?>" onClick="submit()">
		</td></tr>
		</table>
	</td>
</tr>
</table>	
</table>
</form>		
</body>
</html>
