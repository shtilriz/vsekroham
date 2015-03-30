<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$arTypes = CIBlockParameters::GetIBlockTypes(array("-"=>" "));

$arIBlocks = array();
$db_iblock = CIBlock::GetList(
	array("SORT"=>"ASC"),
	array(
		"SITE_ID"=>$_REQUEST["site"],
		"TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")
	)
);
while($arRes = $db_iblock->Fetch()) {
	$arIBlocks[$arRes["ID"]] = $arRes["NAME"];
}

$arIBlocksM = array();
if ($arCurrentValues["IBLOCK_TYPE_MAKER"]) {
	$db_iblock = CIBlock::GetList(
		array("SORT"=>"ASC"),
		array(
			"SITE_ID"=>$_REQUEST["site"],
			"TYPE" => ($arCurrentValues["IBLOCK_TYPE_MAKER"]!="-"?$arCurrentValues["IBLOCK_TYPE_MAKER"]:"")
		)
	);
	while($arRes = $db_iblock->Fetch()) {
		$arIBlocksM[$arRes["ID"]] = $arRes["NAME"];
	}
}

$arComponentParameters = array(
	"GROUPS" => array(

	),
	"PARAMETERS" => array(
		"IBLOCK_TYPE" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arTypes,
			"DEFAULT" => "",
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("IBLOCK_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"REFRESH" => "Y",
		),
		"IBLOCK_TYPE_MAKER" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("IBLOCK_TYPE_MAKER"),
			"TYPE" => "LIST",
			"VALUES" => $arTypes,
			"DEFAULT" => "",
			"REFRESH" => "Y",
		),
		"IBLOCK_ID_MAKER" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("IBLOCK_ID_MAKER"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocksM,
			"REFRESH" => "Y",
		),
	),
);
?>