<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arSort = array(
	"UF_NAME" => GetMessage('SORT_FIELD_NAME'),
	"UF_SORT" => GetMessage('SORT_FIELD_SORT'),
);
$arOrder = array(
	"ASC" => GetMessage("SORT_ASC"),
	"DESC" => GetMessage("SORT_DESC"),
);

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		"BLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage('HLLIST_COMPONENT_BLOCK_ID_PARAM'),
			"TYPE" => "TEXT"
		),
		"SORT_BY" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage('SORT_BY'),
			"TYPE" => "LIST",
			"VALUES" => $arSort
		),
		"SORT_ORDER" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage('SORT_ORDER'),
			"TYPE" => "LIST",
			"VALUES" => $arOrder
		),
	),
);