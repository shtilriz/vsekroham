<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?
//обновляет некоторые свойства со старого сайта на новом.
CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");
$IBLOCK_ID = 1;
$IBLOCK_ID_SKU = 2;
$SITE_LINK = 'http://www.vsekroham.ru';
$content = file_get_contents($SITE_LINK."/export/getSkuProps.php");
$arResult = json_decode($content, true);

$arOffers = array();
$rsSKU = CIBlockElement::GetList(
	array("SORT" => "ASC"),
	array(
		"IBLOCK_ID" => $IBLOCK_ID_SKU
	),
	false,
	false,
	array("IBLOCK_ID", "ID", "PROPERTY_OLD_LINK")
);
while ($arSKU = $rsSKU->GetNext()) {
	$arOffers[$arSKU["ID"]] = $arSKU["PROPERTY_OLD_LINK_VALUE"];
}

//обновить свойства торговых предложений
foreach ($arOffers as $id => $xml_id) {
	/*if (array_key_exists($xml_id, $arResult) && strlen($arResult[$xml_id]["PROPERTY_COLOR_VALUE"]) > 0) {
		CIBlockElement::SetPropertyValuesEx($id, $IBLOCK_ID_SKU, array("COLOR" => $arResult[$xml_id]["PROPERTY_COLOR_VALUE"]));
	}
	if (array_key_exists($xml_id, $arResult) && strlen($arResult[$xml_id]["PROPERTY_SIZE_VALUE"]) > 0) {
		CIBlockElement::SetPropertyValuesEx($id, $IBLOCK_ID_SKU, array("SIZE" => $arResult[$xml_id]["PROPERTY_SIZE_VALUE"]));
	}*/
	if (array_key_exists($xml_id, $arResult)) {
		CIBlockElement::SetPropertyValuesEx($id, $IBLOCK_ID_SKU, array("MARGIN" => $arResult[$xml_id]["PROPERTY_MARGIN_VALUE"]));
	}
}
?>