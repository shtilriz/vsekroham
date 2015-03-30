<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
//делает элементы товарами

CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");

$arProducts = array();
$rsProducts = CIBlockElement::GetList(
	array("ID" => "ASC"),
	array(
		"IBLOCK_ID" => array(IBLOCK_PRODUCT_ID, IBLOCK_SKU_ID),
	),
	false,
	false,
	array("IBLOCK_ID", "ID")
);
while ($arProduct = $rsProducts->GetNext()) {
	$arProducts[] = $arProduct["ID"];
}

foreach ($arProducts as $PRODUCT_ID) {
	CCatalogProduct::Add(array("ID" => $PRODUCT_ID));
}
?>