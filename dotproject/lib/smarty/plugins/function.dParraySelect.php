<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/*
 * Smarty {arraySelect array= name= extras= value= translation=} function plugin
 *
 * Type:     function<br>
 * Name:     arraySelect<br>
 * Purpose:  create a select form through dp<br>
 *
 * @param array Format: array(<br>
 * 'var' => variable name, 
 * 'value' => value to assign,
 * )
 * @param Smarty
 */
function smarty_function_dParraySelect($params, &$smarty)
{
    extract($params);
    
    if (empty($array) || empty($name) || empty($extras) || empty($value)) {
        $smarty->trigger_error("assign: missing parameter");
        return;
    }
    
    if (!isset($translation))
    	$translation = false;

    return arraySelect($array, $name, $extras, $value, $translation);
}

/* vim: set expandtab: */

?>