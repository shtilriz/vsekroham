<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?
//Переносит Страны производителей со старого сайта на новый
$content = file_get_contents("http://vsekroham.ru/export/getCountry.php");
$arCountry = json_decode($content, true);
CModule::IncludeModule('iblock');
CModule::IncludeModule('highloadblock');
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

// hlblock у нас первый, $hlblock - это массив
$hlblock = HL\HighloadBlockTable::getById(1)->fetch();
$entity = HL\HighloadBlockTable::compileEntity($hlblock);
$entity_data_class = $entity->getDataClass();

foreach ($arCountry as $key => $arItem) {
	if ($arItem["ID"] == 21412)
		continue;
	$arFile = CFile::MakeFileArray('http://www.vsekroham.ru'.$arItem["IMAGE"]);
	$result = $entity_data_class::add(array(
		"UF_NAME" => $arItem["NAME"],
		"UF_IMAGE" => $arFile,
		"UF_XML_ID" => $arItem["ID"]
	));
	if ($result->isSuccess()) {
		echo $result->getId().'<br/>';
	}
}
?>