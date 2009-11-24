<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2002 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors:   Carsten Lucke <luckec@tool-garage.de>                     |
// |            Mark Wiesemann <wiesemann@php.net>                        |
// +----------------------------------------------------------------------+
//
//    $Id: Bremen.php,v 1.1 2007/01/11 06:19:25 fiksdal Exp $

/**
 * Filter that only accepts official holidays in Bremen.
 *
 * @category    Date
 * @package     Date_Holidays
 * @subpackage  Filter
 * @version     $Id: Bremen.php,v 1.1 2007/01/11 06:19:25 fiksdal Exp $
 * @author      Carsten Lucke <luckec@tool-garage.de>
 * @author      Mark Wiesemann <wiesemann@php.net>
 */
class Date_Holidays_Filter_Germany_Bremen extends Date_Holidays_Filter_Whitelist 
{
    /**
     * Constructor.
     * 
     * @param   array   numerical array that contains internal names of holidays
     */
    function __construct() 
    {
        parent::__construct(
            array(
                'newYearsDay',
                'goodFriday',
                'easterMonday',
                'dayOfWork',
                'ascensionDay',
                'whitMonday',
                'germanUnificationDay',
                'xmasDay',
                'boxingDay'
            )
        );
    }
    
    /**
     * Constructor.
     * 
     * Only accepts official holidays in Bremen.
     */
    function Date_Holidays_Filter_Germany_Bremen()
    {
        $this->__construct();
    }
}
?>
