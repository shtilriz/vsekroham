<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?
//устанавливает товары, доступные к покупке
CModule::IncludeModule("iblock");
$content = file_get_contents("http://vsekroham.ru/export/getAvailable.php");
$arResult = json_decode($content, true);

$arProducts = array();
$rsProducts = CIBlockElement::GetList(
	array("ID" => "ASC"),
	array(
		"IBLOCK_ID" => array(IBLOCK_PRODUCT_ID),
	),
	false,
	false,
	array("IBLOCK_ID", "ID", "PROPERTY_OLD_LINK")
);
while ($arProduct = $rsProducts->GetNext()) {
	$arProducts[$arProduct["ID"]] = $arProduct["PROPERTY_OLD_LINK_VALUE"];
}

foreach ($arProducts as $id => $old_link) {
	CIBlockElement::SetPropertyValuesEx($id, IBLOCK_PRODUCT_ID, array("AVAILABLE" => (in_array($old_link, $arResult)?1:NULL)));
}
?>