<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/include/arCity.php");

$arResult = array();
if (isset($arCity) && is_array($arCity)) {
	$q = (strlen($_REQUEST["q"])>1?$_REQUEST["q"]:'');
	foreach ($arCity as $key => $city) {
		$pos = stripos($city, $q);
		if (!($pos === false) && $pos == 0) {
			$arResult[] = $city;
		}
	}
}
echo json_encode($arResult);
//вывод городов по запросу
/*use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use Bitrix\Main\Type as FieldType;
$arResult = array();

$q = (strlen($_REQUEST["q"])>1?$_REQUEST["q"]:'');

if (CModule::IncludeModule("highloadblock") && $q) {
	$hlblock = HL\HighloadBlockTable::getById(7)->fetch();
	$entity = HL\HighloadBlockTable::compileEntity($hlblock);
	$main_query = new Entity\Query($entity);
	$main_query->setSelect(array('*'));
	$main_query->setOrder(array("UF_NAME" => "ASC"));
	$main_query->setFilter(array("UF_NAME" => $q."%"));
	$main_query->setLimit(8);
	$result = $main_query->exec();
	$result = new CDBResult($result);
	while ($arCity = $result->Fetch()) {
		$arResult[] = $arCity["UF_NAME"];
	}
}
echo json_encode($arResult);*/
?>