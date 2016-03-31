#!/usr/bin/php -q
<?php
$_SERVER["DOCUMENT_ROOT"] = "/home/vsekroham/public_html";
$_SERVER["HTTP_HOST"] = "www.vsekroham.ru";
//скрипт должен запускаться по крону и добавлять или удалять фото из ВК
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
//AddMessage2Log("/include/vkevent.php - Запуск скрипта");

if (!CModule::IncludeModule("iblock"))
	die();
if (!CModule::IncludeModule("kreattika.shopvk"))
	die();

$file = $_SERVER["DOCUMENT_ROOT"]."/upload/vkevent.log";
$content = file_get_contents($file);
$arProducts = unserialize($content);

foreach ($arProducts as $id => $arFields) {
	if (in_array($arFields["action"], array("add", "update"))) {
		SVK::wall_auto_post($arFields);
		//AddMessage2Log("/include/vkevent.php - Добавлена (обновлена) картинка товара ".$arFields["ID"]);
	}
	elseif ($arFields["action"] == "delete") {
		SVK::delete_auto_post($arFields);
		//AddMessage2Log("/include/vkevent.php - Удалена картинка товара ".$arFields["ID"]);
	}
}
//очищаем файл
file_put_contents($file, "");
?>