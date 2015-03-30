<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?
//Переносит Производителей со старого сайта на новый
CModule::IncludeModule('iblock');
CModule::IncludeModule('highloadblock');
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
// hlblock у нас первый, $hlblock - это массив
$hlblock = HL\HighloadBlockTable::getById(1)->fetch();
$entity = HL\HighloadBlockTable::compileEntity($hlblock);
$entity_data_class = $entity->getDataClass();

$content = file_get_contents("http://vsekroham.ru/export/getBrands.php");
$arBrands = json_decode($content, true);
foreach ($arBrands as $key => $arItem) {
	if (in_array($arItem["ID"], array(17556)))
		continue;
	$countryID = 0;
	$rsData = $entity_data_class::getList(array(
		"select" => array("*"),
		"order" => array("ID" => "ASC"),
		"filter" => array("=UF_XML_ID" => $arItem["COUNTRY"])
	));
	if ($arData = $rsData->Fetch()) {
		$countryID = $arData["ID"];
	}
	
	$el = new CIBlockElement;
	$arProp = array(
		"COUNTRY" => $arItem["COUNTRY"],
		"RETAILROCKET" => $arItem["RETAILROCKET"]
	);
	$arFields = array(
		"IBLOCK_ID" => 3,
		"IBLOCK_SECTION_ID" => false,
		"ACTIVE" => $arItem["ACTIVE"],
		"NAME" => $arItem["NAME"],
		"CODE" => $arItem["CODE"],
		"XML_ID" => $arItem["ID"],
		"PROPERTY_VALUES" => $arProp
	);
	if($elID = $el->Add($arFields))
		echo "New ID: ".$elID.'<br/>';
	else
		echo "Error: ".$el->LAST_ERROR.'<br/>';

}
?>