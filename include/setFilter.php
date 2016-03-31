<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?php
/**
 * Формирует фильтр для каталога товаров
 */
if (intval($_GET['SECTION_ID']) > 0){
	$SECTION_ID = intval($_GET['SECTION_ID']);
}
if (intval($_GET['MAKER']) > 0){
	$GLOBALS['arrFilter']['PROPERTY_MAKER'] = $_GET['MAKER'];
}

if (isset($_GET['q']) && !empty($_GET['q'])) {
	if ($_GET['q'] != urldecode($_GET['q'])) {
		header('Location: /search/?q='.urldecode($_GET['q']));
	}
	//если запрос являет числом, то ищем по ID товара
	if (is_numeric($_GET['q'])) {
		$GLOBALS['arrFilter']['ID'] = (int)$_GET['q'];
	}
	else {
		$arQuery = explode(' ', $_GET['q']);
		$arNewQuery = array();
		foreach ($arQuery as $key => $q) {
			if ($q)
				$arNewQuery[] = trim(strip_tags($q));
		}

		$strQuery = '';
		if (!empty($arNewQuery)) {
			$strQuery = implode(' ', $arNewQuery);
		}
		$GLOBALS['arrFilter']['?NAME'] = '('.$strQuery.')';
	}
}
if (!empty($_GET['category']) && $APPLICATION->GetCurDir() == '/search/') {
	$GLOBALS['arrFilter']['SECTION_ID'] = $_GET['category'];
	$GLOBALS['arrFilter']['INCLUDE_SUBSECTIONS'] = 'Y';
}
if (intval($_GET['price_from']) > 0) {
	$GLOBALS['arrFilter']['>=CATALOG_PRICE_1'] = (int)$_GET['price_from'];
}
if (intval($_GET['price_to']) > 0) {
	$GLOBALS['arrFilter']['<=CATALOG_PRICE_1'] = (int)$_GET['price_to'];
}
if (!empty($_GET['complects'])) {
	$GLOBALS['arrFilter']['PROPERTY_OPTIONS'] = $_GET['complects'];
}
if (isset($_GET['weight_from']) && isset($_GET['weight_to'])) {
	$GLOBALS['arrFilter']['>=PROPERTY_WEIGHT'] = (int)$_GET['weight_from'];
	$GLOBALS['arrFilter']['<=PROPERTY_WEIGHT'] = (int)$_GET['weight_to'];
}
if ((int)$_GET['WIDTH_FRAME_from'] > 0) {
	$GLOBALS['arrFilter']['>=PROPERTY_WIDTH_FRAME'] = (int)$_GET['WIDTH_FRAME_from'];
}
if ((int)$_GET['WIDTH_FRAME_to'] > 0) {
	$GLOBALS['arrFilter']['<=PROPERTY_WIDTH_FRAME'] = (int)$_GET['WIDTH_FRAME_to'];
}

foreach ($_GET['prop'] as $propCode => $value) {
	$GLOBALS['arrFilter']['PROPERTY_'.$propCode] = $value;
}

if (strrpos($_SERVER["REQUEST_URI"],'/catalog/')===0 || strrpos($_SERVER["REQUEST_URI"],'/makers/')===0 || $_SESSION["SHOW_ALL"] == "N") {
	$GLOBALS["arrFilter"]["!PROPERTY_AVAILABLE"] = false;
	$GLOBALS["arrFilter"]["!CATALOG_PRICE_1"] = false;
}

if (strlen($_REQUEST["SECTION_CODE"]) > 0) {
	//Не выводить элементы подраздела в родительском разделе
	$elementsHide = array();
	$obCache = new CPHPCache();
	$cacheLifetime = 3600; $cacheID = "catalog.filter.hide.elements/".$_REQUEST["SECTION_CODE"].'/'; $cachePath = "/".$cacheID;
	CModule::IncludeModule("iblock");
	if ($obCache->InitCache($cacheLifetime, $cacheID, $cachePath)) {
		$vars = $obCache->GetVars();
		$elementsHide = $vars["elementsHide"];
	}
	elseif ($obCache->StartDataCache()) {
		$SECTION_ID = CIBlockFindTools::GetSectionID(
			"",
			$_REQUEST["SECTION_CODE"],
			false,
			false,
			array(
				"GLOBAL_ACTIVE" => "Y",
				"IBLOCK_ID" => IBLOCK_PRODUCT_ID
			)
		);
		if (0 < $SECTION_ID) {
			$db_list = CIBlockSection::GetList(
				array("ID" => "ASC"),
				array(
					"IBLOCK_ID" => IBLOCK_PRODUCT_ID,
					"SECTION_ID" => $SECTION_ID,
					">DEPTH_LEVEL" => 1,
					"GLOBAL_ACTIVE" => "Y",
					"UF_SHOW" => true
				),
				false,
				array("ID")
			);
			while ($arSection = $db_list->Fetch()){
				$res = CIBlockElement::GetList(
					array(),
					array(
						"IBLOCK_ID" => IBLOCK_PRODUCT_ID,
						"SECTION_ID" => $arSection["ID"],
						"ACTIVE" => "Y",
						"INCLUDE_SUBSECTIONS" => "Y"
					),
					false,
					false,
					array("ID")
				);
				while ($arRes = $res->Fetch()) {
					$elementsHide[] = $arRes["ID"];
				}
			}
		}
		$obCache->EndDataCache(array("elementsHide" => $elementsHide));
	}
	if (!empty($elementsHide)) {
		$GLOBALS["arrFilter"]["!ID"] = $elementsHide;
	}
}
