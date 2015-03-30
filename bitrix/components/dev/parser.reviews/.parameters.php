<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$arTypesEx = CIBlockParameters::GetIBlockTypes(array("-"=>" "));

$arIBlocksCatalog = array();
$db_iblock = CIBlock::GetList(
	array("SORT"=>"ASC"),
	array(
		"SITE_ID"=>$_REQUEST["site"],
		"TYPE" => ($arCurrentValues["IBLOCK_TYPE_CATALOG"]!="-"?$arCurrentValues["IBLOCK_TYPE_CATALOG"]:"")
	)
);
while($arRes = $db_iblock->Fetch()) {
	$arIBlocksCatalog[$arRes["ID"]] = $arRes["NAME"];
}

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

/*$arProperties = array(0 => "-");
if (intval($arCurrentValues["IBLOCK_ID"]) > 0) {
	$properties = CIBlockProperty::GetList(
		array("sort"=>"asc", "name"=>"asc"),
		array("IBLOCK_ID"=>$arCurrentValues["IBLOCK_ID"], "ACTIVE"=>"Y")
	);
	while ($prop_fields = $properties->GetNext()) {
		$arProperties[$prop_fields["CODE"]] = $prop_fields["NAME"];
	}
}*/
$arProperties = array(0 => "-");
if ($arCurrentValues["BLOCK_ID"] > 0) {
	$rsData = CUserTypeEntity::GetList(
		array("ID" => "ASC"),
		array("ENTITY_ID" => "HLBLOCK_".$arCurrentValues["BLOCK_ID"])
	);
	while($arRes = $rsData->Fetch()) {
		$arProperties[$arRes["FIELD_NAME"]] = $arRes["FIELD_NAME"];
	}
}


$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		"IBLOCK_TYPE_CATALOG" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("IBLOCK_TYPE_CATALOG"),
			"TYPE" => "LIST",
			"VALUES" => $arTypesEx,
			"DEFAULT" => "",
			"REFRESH" => "Y",
		),
		"IBLOCK_ID_CATALOG" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("IBLOCK_ID_CATALOG"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocksCatalog,
			"REFRESH" => "Y",
		),
		"BLOCK_ID" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("BLOCK_ID"),
			"TYPE" => "STRING",
			"REFRESH" => "Y",
		),
		"PRODUCT" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("PRODUCT"),
			"TYPE" => "LIST",
			"DEFAULT" => "-",
			"VALUES" => $arProperties
		),
		"SERVICE" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("SERVICE"),
			"TYPE" => "LIST",
			"DEFAULT" => "-",
			"VALUES" => $arProperties
		),
		"RATING" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("RATING"),
			"TYPE" => "LIST",
			"DEFAULT" => "-",
			"VALUES" => $arProperties
		),
		"WORTH" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("WORTH"),
			"TYPE" => "LIST",
			"DEFAULT" => "-",
			"VALUES" => $arProperties
		),
		"LIMITATIONS" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("LIMITATIONS"),
			"TYPE" => "LIST",
			"DEFAULT" => "-",
			"VALUES" => $arProperties
		),
		"COMMENT" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("COMMENT"),
			"TYPE" => "LIST",
			"DEFAULT" => "-",
			"VALUES" => $arProperties
		),
		"LIKE" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("LIKE"),
			"TYPE" => "LIST",
			"DEFAULT" => "-",
			"VALUES" => $arProperties
		),
		"DIZLIKE" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("DIZLIKE"),
			"TYPE" => "LIST",
			"DEFAULT" => "-",
			"VALUES" => $arProperties
		),
		"LINK_INPUT_NAME" => array(
			"PARENT" => "DATA_SOURCE",
			"NAME" => GetMessage("LINK_INPUT_NAME"),
			"TYPE" => "STRING",
			"DEFAULT" => "link"
		)
	),
);
?>