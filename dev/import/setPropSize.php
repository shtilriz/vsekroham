<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?
//устанавливает размеры со старого сайта
CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");
$content = file_get_contents("http://test.vsekroham.ru/export/getPropSize.php");
$arItems = json_decode($content, true);

$arProducts = array();
$rsProducts = CIBlockElement::GetList(
	array("ID" => "ASC"),
	array(
		"IBLOCK_ID" => 1,
		"PROPERTY_SIZE" => false
	),
	false,
	false,
	array("IBLOCK_ID", "ID", "PROPERTY_OLD_LINK")
);
while ($arRes = $rsProducts->GetNext()) {
	$arProducts[$arRes["PROPERTY_OLD_LINK_VALUE"]] = $arRes["ID"];
}

foreach ($arProducts as $key => $ID) {
	if (array_key_exists($key, $arItems)) {
		CIBlockElement::SetPropertyValuesEx($ID, 1, array("SIZE" => $arItems[$key]));
		echo $ID.' - '.$arItems[$key].'<br/>';
	}
}
?>