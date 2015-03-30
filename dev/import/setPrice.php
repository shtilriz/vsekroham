<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?
//обновляет цены товаров со старого сайта
/*CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");
$content = file_get_contents("http://test.vsekroham.ru/export/getPrice.php");
$arPrice = json_decode($content, true);

$arProducts = array();
$rsProducts = CIBlockElement::GetList(
	array("ID" => "ASC"),
	array(
		"IBLOCK_ID" => array(IBLOCK_PRODUCT_ID, IBLOCK_SKU_ID),
	),
	false,
	false,
	array("IBLOCK_ID", "ID", "PROPERTY_OLD_LINK")
);
while ($arProduct = $rsProducts->GetNext()) {
	$arProducts[$arProduct["ID"]] = $arProduct["PROPERTY_OLD_LINK_VALUE"];
}

foreach ($arProducts as $id => $old_link) {
	CPrice::SetBasePrice(
		$id,
		($arPrice[$old_link]?$arPrice[$old_link]:0),
		"RUB",
		false,
		false
	);
}*/
?>