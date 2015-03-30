<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?
if (CModule::IncludeModule("iblock")) {
	$arPrice = array();
	$res = CIBlockElement::GetList(
		array("ID" => "ASC"),
		array(
			"IBLOCK_ID" => array(IBLOCK_PRODUCT_ID, IBLOCK_SKU_ID),
			"!CATALOG_PRICE_1" => false
		),
		false,
		false,
		array("IBLOCK_ID", "ID", "NAME", "CATALOG_GROUP_1")
	);
	while ($arRes = $res->GetNext()) {
		$arPrice[$arRes["ID"]] = $arRes["CATALOG_PRICE_1"];
	}
	echo json_encode($arPrice);
}
?>