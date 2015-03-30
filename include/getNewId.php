<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?php /*
//выгружает зависимость старых ID с новыми
$arReturn = array();
if (CModule::IncludeModule("iblock")) {
	$rsProducts = CIBlockElement::GetList(
		array("SORT" => "ASC"),
		array(
			"IBLOCK_ID" => array(1, 2),
			"!PROPERTY_OLD_LINK" => false
		),
		false,
		false,
		array("IBLOCK_ID", "ID", "PROPERTY_OLD_LINK")
	);
	while ($arProduct = $rsProducts->GetNext()) {
		$arReturn[intval($arProduct["PROPERTY_OLD_LINK_VALUE"])] = intval($arProduct["ID"]);
	}
}
echo json_encode($arReturn);
*/
?>