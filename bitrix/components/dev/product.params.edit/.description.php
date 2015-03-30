<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => "Габариты и вес товаров",
	"DESCRIPTION" => "Позволяет редактировать параметры товаров, такие как длина, ширина, высота и вес",
	"ICON" => "/images/icon.gif",
	"SORT" => 30,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "mycontent", // for example "my_project"
		"NAME" => "Все для крохи",
		"CHILD" => array(
			"ID" => "services",
			"NAME" => "Сервисы",
			"SORT" => 20
		),
	),
	"COMPLEX" => "N",
);

?>