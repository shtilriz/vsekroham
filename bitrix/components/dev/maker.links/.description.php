<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => "Ссылки на каталог товаров по производителям",
	"DESCRIPTION" => "",
	"ICON" => "/images/icon.gif",
	"SORT" => 11,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "mycontent",
		"NAME" => "Все для крохи",
		"CHILD" => array(
			"ID" => "mycatalog",
			"NAME" => "Каталог",
			"SORT" => 40
		),
	),
	"COMPLEX" => "N",
);

?>