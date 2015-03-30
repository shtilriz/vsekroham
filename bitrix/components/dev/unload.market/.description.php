<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => "Выгрузка на маркет",
	"DESCRIPTION" => "Позволяет управлять товарами, которые нужно выгружать на маркет",
	"ICON" => "/images/icon.gif",
	"SORT" => 20,
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