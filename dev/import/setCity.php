<?/*require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?
//переносит города с сайта все крохам
CModule::IncludeModule('iblock');
CModule::IncludeModule('highloadblock');
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

$content = file_get_contents("http://vsekroham.ru/export/getCity.php");
$arCity = json_decode($content, true);

$hlblock = HL\HighloadBlockTable::getById(7)->fetch();
$entity = HL\HighloadBlockTable::compileEntity($hlblock);
$entity_data_class = $entity->getDataClass();

foreach ($arCity as $key => $name) {
	$arFields = array(
		"UF_NAME" => $name
	);
	$result = $entity_data_class::add($arFields);
	if ($result->isSuccess()) {
		echo 'SUCCESS<br/>';
	}
	else {
		echo 'ERROR<br/>';
	}
}*/
?>