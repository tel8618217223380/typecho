<?php
/**
 * Typecho Blog Platform
 *
 * @author     qining
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */


function typechoStripslashesDeep($value)
{
    return is_array($value) ? array_map('typechoStripslashesDeep', $value) : stripslashes($value);
}
