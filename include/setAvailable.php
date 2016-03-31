<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?php
//снимает флажок "В наличии" у товаров с отсутствующей ценой
if (CModule::IncludeModule("iblock")) {
	$arProducts = array();
	$rsProducts = CIBlockElement::GetList(
		array("ID" => "ASC"),
		array(
			"IBLOCK_ID" => IBLOCK_PRODUCT_ID
		),
		false,
		false,
		array("IBLOCK_ID", "ID", "CATALOG_GROUP_1")
	);
	while ($arProduct = $rsProducts->GetNext()) {
		$arProducts[$arProduct["ID"]] = $arProduct["CATALOG_PRICE_1"];
	}

	foreach ($arProducts as $id => $price) {
		if (intval($price) <= 0) {
			CIBlockElement::SetPropertyValuesEx($id, IBLOCK_PRODUCT_ID, array("AVAILABLE" => NULL));
		}
	}
}
?>
