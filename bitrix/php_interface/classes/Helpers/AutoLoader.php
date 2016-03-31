<?php
/**
 * Автозагрузчик для классов в /bitrix/php_interface/classes/Helpers/
 */

function autoloadClassHelpers($class_name)
{
	include_once($class_name.'.php');
}

spl_autoload_register('autoloadClassHelpers');
