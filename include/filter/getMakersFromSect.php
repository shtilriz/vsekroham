<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?
/**
 * Скрипт возвращает массив брендов в разделе $id
 */

$arReturn = array();

if (!CModule::IncludeModule('iblock')) {
	$arReturn['SUCCESS'] = 'N';
	$arReturn['MESSAGE'] = 'Не установлен модуль информационных блоков';
}

$sectionId = abs((int)$_GET['id']);
if (!$sectionId) {
	$arReturn['SUCCESS'] = 'N';
	$arReturn['MESSAGE'] = 'Не задан Id раздела';
}
else {
	$arMakers = array();
	$arFilterMakers = array(
		"IBLOCK_ID" => 4,
		"ACTIVE" => "Y",
		"PROPERTY_SECTION_ID" => $sectionId
	);
	$rsMaker = CIBlockElement::GetList(
		array("NAME" => "ASC", "SORT" => "ASC"),
		$arFilterMakers,
		false,
		false,
		array("IBLOCK_ID", "ID", "PROPERTY_MAKER")
	);
	while ($arMaker = $rsMaker->GetNext()) {
		$arMakers[] = $arMaker["PROPERTY_MAKER_VALUE"];
	}
	if (!empty($arMakers)) {
		$arReturn["SUCCESS"] = 'Y';
		$rsMaker = CIBlockElement::GetList(
			array("NAME" => "ASC", "SORT" => "ASC"),
			array(
				"IBLOCK_ID" => 3,
				"ACTIVE" => "Y",
				"ID" => $arMakers
			),
			false,
			false,
			array("IBLOCK_ID", "ID", "NAME")
		);
		while ($arMaker = $rsMaker->GetNext()) {
			$arReturn["BRANDS"][$arMaker["ID"]] = $arMaker["NAME"];
		}
	}
	else {
		$arReturn['SUCCESS'] = 'N';
		$arReturn['MESSAGE'] = 'Пустая выборка';
	}
}
echo json_encode($arReturn);
?>