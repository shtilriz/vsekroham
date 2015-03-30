<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => "Парсер отзывов",
	"DESCRIPTION" => "Позволяет парсить отзывы с Яндекс Маркета и Товары.Mail.ru",
	"ICON" => "/images/icon.gif",
	"SORT" => 10,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "mycontent", // for example "my_project"
		"NAME" => "Все для крохи",
		"CHILD" => array(
			"ID" => "parser",
			"NAME" => "Парсер",
			"SORT" => 10
		),
	),
	"COMPLEX" => "N",
);

?>