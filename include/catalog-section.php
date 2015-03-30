<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
}?>

<?
if (!isset($_SESSION["CAT_ORDER"]) || empty($_SESSION["CAT_ORDER"])) {
	$_SESSION["CAT_ORDER"] = "ASC";
}
if (!isset($_SESSION["CAT_SORT"]) || empty($_SESSION["CAT_ORDER"])) {
	$_SESSION["CAT_SORT"] = "SORT";
}

if (isset($_REQUEST["order"])) {
	$_SESSION["CAT_ORDER"] = $_REQUEST["order"];
}
//если меняется поле сортировки, то принудительно ставим направление сортировки по возрастанию
if (isset($_REQUEST["sort"]) && $_REQUEST["sort"] != $_SESSION["CAT_SORT"]) {
	$_SESSION["CAT_ORDER"] = "ASC";
}
if (isset($_REQUEST["sort"])) {
	$_SESSION["CAT_SORT"] = $_REQUEST["sort"];
}
?>

<?
if (intval($_GET["SECTION_ID"]) > 0){
	$SECTION_ID = intval($_GET["SECTION_ID"]);
}
if (intval($_GET["MAKER"]) > 0){
	$GLOBALS["arrFilter"]["PROPERTY_MAKER"] = $_GET["MAKER"];
}

if (isset($_GET["q"]) && !empty($_GET["q"])) {
	if ($_GET['q'] != urldecode($_GET["q"])) {
		header('Location: /search/?q='.urldecode($_GET["q"]));
	}
	//если запрос являет числом, то ищем по ID товара
	if (is_numeric($_GET["q"])) {
		$GLOBALS["arrFilter"]["ID"] = (int)$_GET["q"];
	}
	else {
		$arQuery = explode(" ", $_GET["q"]);
		$arNewQuery = array();
		foreach ($arQuery as $key => $q) {
			if ($q)
				$arNewQuery[] = trim(strip_tags($q));
		}

		$strQuery = '';
		if (!empty($arNewQuery)) {
			$strQuery = implode(" ", $arNewQuery);
		}
		//$GLOBALS["arrFilter"]["NAME"] = "%".$_GET["q"]."%";
		$GLOBALS["arrFilter"]["?NAME"] = '('.$strQuery.')';
	}
}
if (!empty($_GET["category"]) && $APPLICATION->GetCurDir() == "/search/") {
	$GLOBALS["arrFilter"]["SECTION_ID"] = $_GET["category"];
	$GLOBALS["arrFilter"]["INCLUDE_SUBSECTIONS"] = "Y";
}
if (!empty($_GET["brand"])) {
	$GLOBALS["arrFilter"]["PROPERTY_MAKER"] = $_GET["brand"];
}
if (intval($_GET["price_from"]) > 0) {
	$GLOBALS["arrFilter"][">=CATALOG_PRICE_1"] = intval($_GET["price_from"]);
}
if (intval($_GET["price_to"]) > 0) {
	$GLOBALS["arrFilter"]["<=CATALOG_PRICE_1"] = intval($_GET["price_to"]);
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
				false
			);
			while ($arSection = $db_list->GetNext()){
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
					array("IBLOCK_ID", "ID")
				);
				while ($arRes = $res->GetNext()) {
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
$TEMPLATE_THEME = '.default';
$PAGER_TEMPLATE = 'fixed';
if (strrpos($_SERVER["REQUEST_URI"],'/brands/')===0) {
	$TEMPLATE_THEME = 'ajax';
	$PAGER_TEMPLATE = '.default';
}
?>

<?$SECT_ID = $APPLICATION->IncludeComponent(
	"dev:catalog.section",
	".default",
	array(
		"IBLOCK_TYPE" => "catalogs",
		"IBLOCK_ID" => "1",
		"SECTION_ID" => ($SECTION_ID>0?$SECTION_ID:""),
		"SECTION_CODE" => $_REQUEST["SECTION_CODE"],
		"SECTION_USER_FIELDS" => array(
			0 => "",
			1 => "",
		),
		"ELEMENT_SORT_FIELD" => "PROPERTY_AVAILABLE",
		"ELEMENT_SORT_ORDER" => "ASC,nulls",
		"ELEMENT_SORT_FIELD2" => $_SESSION["CAT_SORT"],
		"ELEMENT_SORT_ORDER2" => $_SESSION["CAT_ORDER"],
		"FILTER_NAME" => "arrFilter",
		"INCLUDE_SUBSECTIONS" => "Y",
		"SHOW_ALL_WO_SECTION" => "Y",
		"HIDE_NOT_AVAILABLE" => "N",
		"PAGE_ELEMENT_COUNT" => "24",
		"LINE_ELEMENT_COUNT" => "3",
		"PROPERTY_CODE" => array(
			0 => "AVAILABLE",
			1 => "",
		),
		"OFFERS_LIMIT" => "5",
		"TEMPLATE_THEME" => $TEMPLATE_THEME,
		"PRODUCT_SUBSCRIPTION" => "N",
		"SHOW_DISCOUNT_PERCENT" => "N",
		"SHOW_OLD_PRICE" => "N",
		"MESS_BTN_BUY" => "Купить",
		"MESS_BTN_ADD_TO_BASKET" => "В корзину",
		"MESS_BTN_SUBSCRIBE" => "Подписаться",
		"MESS_BTN_DETAIL" => "Подробнее",
		"MESS_NOT_AVAILABLE" => "Нет в наличии",
		"SECTION_URL" => "",
		"DETAIL_URL" => "",
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_GROUPS" => "Y",
		"SET_TITLE" => "Y",
		"SET_BROWSER_TITLE" => "Y",
		"BROWSER_TITLE" => "-",
		"SET_META_KEYWORDS" => "Y",
		"META_KEYWORDS" => "",
		"SET_META_DESCRIPTION" => "Y",
		"META_DESCRIPTION" => "",
		"ADD_SECTIONS_CHAIN" => "Y",
		"DISPLAY_COMPARE" => "Y",
		"SET_STATUS_404" => "Y",
		"CACHE_FILTER" => "N",
		"PRICE_CODE" => array(
			0 => "BASE",
			1 => "MARGIN",
		),
		"USE_PRICE_COUNT" => "N",
		"SHOW_PRICE_COUNT" => "1",
		"PRICE_VAT_INCLUDE" => "Y",
		"CONVERT_CURRENCY" => "N",
		"BASKET_URL" => "/bsket/",
		"ACTION_VARIABLE" => "action",
		"PRODUCT_ID_VARIABLE" => "id",
		"USE_PRODUCT_QUANTITY" => "N",
		"ADD_PROPERTIES_TO_BASKET" => "Y",
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"PARTIAL_PRODUCT_PROPERTIES" => "N",
		"PRODUCT_PROPERTIES" => array(
		),
		"PAGER_TEMPLATE" => $PAGER_TEMPLATE,
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "Товары",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"OFFERS_FIELD_CODE" => array(
			0 => "ID",
			1 => "NAME",
			2 => "PREVIEW_TEXT",
			3 => "PREVIEW_PICTURE",
			4 => "",
		),
		"OFFERS_PROPERTY_CODE" => array(
			0 => "SIZE",
			1 => "COLOR",
			2 => "",
		),
		"OFFERS_SORT_FIELD" => "sort",
		"OFFERS_SORT_ORDER" => "asc",
		"OFFERS_SORT_FIELD2" => "id",
		"OFFERS_SORT_ORDER2" => "asc",
		"PRODUCT_DISPLAY_MODE" => "N",
		"ADD_PICT_PROP" => "-",
		"LABEL_PROP" => "-",
		"OFFERS_CART_PROPERTIES" => array(
			0 => "SIZE",
			1 => "COLOR",
		),
		"AJAX_OPTION_ADDITIONAL" => "",
		"PRODUCT_QUANTITY_VARIABLE" => "quantity",
		"COMPARE_PATH" => ""
	),
	false
);
if (!$SECT_ID && strrpos($_SERVER["REQUEST_URI"],'/catalog/')===0) {
	LocalRedirect("/404.php");
}
?>
