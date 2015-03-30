<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$arTypesEx = CIBlockParameters::GetIBlockTypes(Array("-"=>" "));

$arIBlocks=array();
$db_iblock = CIBlock::GetList(Array("SORT"=>"ASC"), Array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")));
while($arRes = $db_iblock->Fetch()) {
	$arIBlocks[$arRes["ID"]] = $arRes["NAME"];
}

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(

		"IBLOCK_TYPE" => Array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arTypesEx,
			"DEFAULT" => "news",
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => Array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("IBLOCK_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID_CATALOG" => Array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("IBLOCK_ID_CATALOG"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"REFRESH" => "Y",
		),
		"SECTION_ID" => Array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("SECTION_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => "0",
		),
		"SECTION_CODE" => Array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("SECTION_CODE"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"FOLDER" => Array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("FOLDER"),
			"TYPE" => "STRING",
			"DEFAULT" => "products",
		),
		"CACHE_TIME"  =>  Array("DEFAULT"=>3600),
	),
);
?>