<?php
/**
 * скрипт позволяет редиректить мобильные устройства на мобильную версию
 */

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/classes/Helpers/Mobile_Detect.php');

//функция определяет, открыт ли сайт в мобильной версии на поддомене m.
function isMobileVersion()
{
	if ($_SERVER['SERVER_NAME'] == 'm.vsekroham.ru')
		return true;
	else
		return false;
}

//если пришёл GET запрос ?mode=full и пользователь находится в мобильной версии сайта
if (!isset($_GET['mode']))
	$_GET['mode'] = '';

if (isMobileVersion() && $_GET['mode'] == 'full') {
	setcookie('site_version', 'full', 0, '/', 'vsekroham.ru');
	$uri = str_replace('http://m.vsekroham.ru', '', $_SERVER['HTTP_REFERER']);
	header('Location: http://www.vsekroham.ru'.$uri);
	exit;
}

$detect = new Mobile_Detect;
//если пользователь зашел с мобильного устройства и не определена cookie site_version и пользователь находится в полной версии
if (!isset($_COOKIE['site_version']) && $detect->isMobile() && !$detect->isTablet() && !isMobileVersion()) {
	setcookie('site_version', 'mobile', 0, '/', 'vsekroham.ru');
	header('Location: http://m.vsekroham.ru'.$_SERVER['REQUEST_URI']);
	exit;
}
//если пользователь зашел с мобильного устройства и определена cookie site_version как mobile и пользователь находится в полной версии сайта
elseif (isset($_COOKIE['site_version']) && $_COOKIE['site_version'] == 'mobile' && $detect->isMobile() && !$detect->isTablet() && !isMobileVersion()) {
	header('Location: http://m.vsekroham.ru'.$_SERVER['REQUEST_URI']);
	exit;
}
//если пользователь зашел с ПК и пользователь находится в мобильной версии
elseif (!$detect->isMobile() && isMobileVersion()) {
	setcookie('site_version', 'full', 0, '/', 'vsekroham.ru');
	header('Location: http://www.vsekroham.ru'.$_SERVER['REQUEST_URI']);
	exit;
}
