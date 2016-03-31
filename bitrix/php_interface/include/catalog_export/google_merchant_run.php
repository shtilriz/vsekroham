<?
//<title>Google Merchant</title>
global $USER, $APPLICATION;

$IBLOCK_ID = intval($IBLOCK_ID);
$SETUP_SERVER_NAME = trim($SETUP_SERVER_NAME);

if (!function_exists("google_text2xml")) {
	function google_text2xml($text, $bHSC = false, $bDblQuote = false) {
		global $APPLICATION;

		$bHSC = (true == $bHSC ? true : false);
		$bDblQuote = (true == $bDblQuote ? true: false);

		if ($bHSC) {
			$text = htmlspecialcharsbx($text);
			if ($bDblQuote)
				$text = str_replace('&quot;', '"', $text);
		}
		$text = preg_replace("/[\x1-\x8\xB-\xC\xE-\x1F]/", "", $text);
		$text = str_replace("'", "&apos;", $text);
		$text = $APPLICATION->ConvertCharset($text, LANG_CHARSET, 'UTF-8');
		return $text;
	}
}

function getProductType (&$arSections, $sID, $arReturn) {
	if (!$sID)
		return array_reverse($arReturn);
	else {
		foreach ($arSections as $key => $arSection) {
			if ($arSection["ID"] == $sID) {
				$arReturn[] = $arSection["NAME"];
				return getProductType($arSections, $arSection["IBLOCK_SECTION_ID"], $arReturn);
			}
		}
	}
}

function getGoogleCategory (&$arSections, $sID) {
	if (!$sID)
		return '';
	foreach ($arSections as $key => $arSection) {
		if ($arSection["ID"] == $sID)
			return $arSection["UF_GOOGLE_ID"];
	}
}

/* Разделы каталога */
$arSections = array();
$rsSections = CIBlockSection::GetList(
	array("LEFT_MARGIN" => "ASC"),
	array("IBLOCK_ID" => $IBLOCK_ID, "ACTIVE" => "Y"),
	false,
	array("IBLOCK_ID", "ID", "NAME", "IBLOCK_SECTION_ID", "DEPTH_LEVEL", "UF_GOOGLE_ID")
);
while ($arSect = $rsSections->GetNext()) {
	$arSections[] = array(
		"IBLOCK_ID" => $arSect["IBLOCK_ID"],
		"ID" => $arSect["ID"],
		"NAME" => $arSect["NAME"],
		"IBLOCK_SECTION_ID" => $arSect["IBLOCK_SECTION_ID"],
		"DEPTH_LEVEL" => $arSect["DEPTH_LEVEL"],
		"UF_GOOGLE_ID" => $arSect["UF_GOOGLE_ID"]
	);
}

/* Производители */
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use Bitrix\Main\Type as FieldType;
CModule::IncludeModule("highloadblock");
$arMakers = array();
$arMakersCountry = array();
$rsMaker = CIBlockElement::GetList(
	array(),
	array("IBLOCK_ID" => 3),
	false,
	false,
	array("ID", "NAME", "PROPERTY_COUNTRY")
);
while ($arMaker = $rsMaker->GetNext()) {

	$arMakers[$arMaker["ID"]] = $arMaker["NAME"];
	if ($arMaker["PROPERTY_COUNTRY_VALUE"]) {
		$rsCountry = CIBlockElement::GetList(array(),array("ID" => $arMaker["PROPERTY_COUNTRY_VALUE"],"ACTIVE" => "Y"),false,false,array("NAME"));
		if ($arCountry = $rsCountry->GetNext())
			$arMakersCountry[$arMaker["ID"]] = $arCountry["NAME"];
	}

	$countryID = $arMaker["PROPERTY_COUNTRY_VALUE"];

	if (intval($countryID) > 0) {
		$hlblock = HL\HighloadBlockTable::getById(1)->fetch();
		$entity = HL\HighloadBlockTable::compileEntity($hlblock);
		$entity_data_class = $entity->getDataClass();
		$rsData = $entity_data_class::getList(array(
			"select" => array("UF_NAME"),
			"order" => array(),
			"filter" => array("UF_XML_ID" => intval($countryID))
		));
		if ($arCountry = $rsData->Fetch()) {
			$arMakersCountry[$arMaker["ID"]] = $arCountry["UF_NAME"];
			//$arResult["COUNTRY"] = $arCountry;
		}
	}
}

$dom = new DomDocument("1.0", "UTF-8");
$dom->formatOutput = true;
$dom->preserveWhiteSpace = false;
$rss = $dom->createElement("rss");
/* set xmlns:g */
$rss_xmlns = $dom->createAttribute("xmlns:g");
$rss_xmlns->value = "http://base.google.com/ns/1.0";
$rss->appendChild($rss_xmlns);
/* set version */
$rss_version = $dom->createAttribute("version");
$rss_version->value = "2.0";
$rss->appendChild($rss_version);

$dom->appendChild($rss);

$channel = $dom->createElement("channel");
$rss->appendChild($channel);

$title = $dom->createElement("title", COption::GetOptionString("main", "site_name", ""));
$channel->appendChild($title);

$link = $dom->createElement("link", "http://".$SETUP_SERVER_NAME);
$channel->appendChild($link);

$ar_iblock = CIBlock::GetByID($IBLOCK_ID)->Fetch();

$arOffers = CCatalogSKU::GetInfoByProductIBlock($IBLOCK_ID);
$intOfferIBlockID = $arOffers["IBLOCK_ID"];
$arOfferIBlock = CIBlock::GetByID($intOfferIBlockID)->Fetch();

$arFilter = array(
	"IBLOCK_ID" => $IBLOCK_ID,
	"ACTIVE" => "Y",
	//"!PROPERTY_MARKET" => false,
	"!PROPERTY_AVAILABLE" => false,
	"!PROPERTY_MAKER" => false
);
$rsProducts = CIBlockElement::GetList(
	array("ID" => "ASC"),
	$arFilter,
	false,
	false,
	array("IBLOCK_ID", "ID", "NAME", "CODE", "PREVIEW_TEXT", "DETAIL_TEXT", "DETAIL_PAGE_URL", "PREVIEW_PICTURE", "IBLOCK_SECTION_ID")
);
while ($obProduct = $rsProducts->GetNextElement()) {
	$arProduct = $obProduct->GetFields();
	$arProduct["PROPERTIES"] = $obProduct->GetProperties();

	$boolItemOffers = false;

	$arOfferFilter = array("IBLOCK_ID" => IBLOCK_SKU_ID, "PROPERTY_".$arOffers["SKU_PROPERTY_ID"] => $arProduct["ID"], "ACTIVE" => "Y", "ACTIVE_DATE" => "Y");
	$arOfferSelect = array("IBLOCK_ID", "ID", "NAME", "DETAIL_PAGE_URL", "PREVIEW_PICTURE");
	$rsOfferItems = CIBlockElement::GetList(array("SORT"=>"ASC", "ID" => "ASC"),$arOfferFilter,false,false,$arOfferSelect);
	while ($obOfferItem = $rsOfferItems->GetNextElement()) {
		$arOfferItem = $obOfferItem->GetFields();
		$arOfferItem["PROPERTIES"] = $obOfferItem->GetProperties();

		$minPrice = -1;
		$minPriceCurrency = "RUB";

		if ($arPrice = CCatalogProduct::GetOptimalPrice(
			$arOfferItem["ID"],
			1,
			array(2), // anonymous
			'N',
			array(),
			$arOfferIBlock['LID'],
			array()
		))
		{
			$minPrice = $arPrice['DISCOUNT_PRICE'];
			$minPriceCurrency = $BASE_CURRENCY;
			$minPriceRUR = CCurrencyRates::ConvertCurrency($minPrice, $BASE_CURRENCY, $RUR);
			$minPriceGroup = $arPrice['PRICE']['CATALOG_GROUP_ID'];
		}

		if ($minPrice <= 0)
			continue;

		$item = $dom->createElement("item");

		$g_id = $dom->createElement("g:id", $arOfferItem["ID"]);
		$item->appendChild($g_id);

		$g_title = $dom->createElement("g:title", google_text2xml($arOfferItem["NAME"], true));
		$item->appendChild($g_title);

		$g_description = $dom->createElement("g:description", google_text2xml($arProduct["PREVIEW_TEXT"]?$arProduct["PREVIEW_TEXT"]:$arProduct["DETAIL_TEXT"], true));
		$item->appendChild($g_description);

		$str_utm = ($SHOW_UTM?"utm_campaign=".google_text2xml($arMakers[$arProduct["PROPERTIES"]["MAKER"]["VALUE"]], true)."&amp;utm_term=".google_text2xml($arProduct["CODE"], true):"");
		$g_link = $dom->createElement("g:link", "http://".$SETUP_SERVER_NAME.$arOfferItem["DETAIL_PAGE_URL"].($GET_PARAMS?'?'.$GET_PARAMS:'').($str_utm?($GET_PARAMS?'&amp;':'?').$str_utm:''));
		$item->appendChild($g_link);

		$g_image_link = $dom->createElement("g:image_link", "http://".$SETUP_SERVER_NAME.CFile::GetPath($arOfferItem["PREVIEW_PICTURE"]));
		$item->appendChild($g_image_link);

		$g_condition = $dom->createElement("g:condition", "new");
		$item->appendChild($g_condition);

		$g_availability = $dom->createElement("g:availability", "in stock");
		$item->appendChild($g_availability);

		$g_price = $dom->createElement("g:price", $minPrice." ".$minPriceCurrency);
		$item->appendChild($g_price);

		if ($arProduct["PROPERTIES"]["MAKER"]["VALUE"] && array_key_exists($arProduct["PROPERTIES"]["MAKER"]["VALUE"], $arMakers)) {
			$g_brand = $dom->createElement("g:brand", google_text2xml($arMakers[$arProduct["PROPERTIES"]["MAKER"]["VALUE"]], true));
			$item->appendChild($g_brand);
		}

		if ($arProduct["IBLOCK_SECTION_ID"]) {
			$g_google_product_category = $dom->createElement("g:google_product_category", getGoogleCategory($arSections, $arProduct["IBLOCK_SECTION_ID"]));
			$item->appendChild($g_google_product_category);

			$arNav = getProductType($arSections, $arProduct["IBLOCK_SECTION_ID"], array());
			$g_product_type = $dom->createElement("g:product_type", google_text2xml(implode(" > ", $arNav), true));
			$item->appendChild($g_product_type);
		}

		$channel->appendChild($item);

		$boolItemOffers = true;
	}

	if (!$boolItemOffers) {
		$minPrice = -1;
		$minPriceCurrency = "RUB";

		if ($arPrice = CCatalogProduct::GetOptimalPrice(
			$arProduct["ID"],
			1,
			array(2), // anonymous
			'N',
			array(),
			$ar_iblock['LID'],
			array()
		))
		{
			$minPrice = $arPrice['DISCOUNT_PRICE'];
			$minPriceCurrency = $BASE_CURRENCY;
			$minPriceRUR = CCurrencyRates::ConvertCurrency($minPrice, $BASE_CURRENCY, $RUR);
			$minPriceGroup = $arPrice['PRICE']['CATALOG_GROUP_ID'];
		}

		if ($minPrice <= 0)
			continue;

		$item = $dom->createElement("item");

		$g_id = $dom->createElement("g:id", $arProduct["ID"]);
		$item->appendChild($g_id);

		$g_title = $dom->createElement("g:title", google_text2xml($arProduct["NAME"], true));
		$item->appendChild($g_title);

		$g_description = $dom->createElement("g:description", google_text2xml($arProduct["PREVIEW_TEXT"]?$arProduct["PREVIEW_TEXT"]:$arProduct["DETAIL_TEXT"], true));
		$item->appendChild($g_description);

		$str_utm = ($SHOW_UTM?"utm_campaign=".google_text2xml($arMakers[$arProduct["PROPERTIES"]["MAKER"]["VALUE"]], true)."&amp;utm_term=".google_text2xml($arProduct["CODE"], true):"");
		$g_link = $dom->createElement("g:link", "http://".$SETUP_SERVER_NAME.$arProduct["DETAIL_PAGE_URL"].($GET_PARAMS?'?'.$GET_PARAMS:'').($str_utm?($GET_PARAMS?'&amp;':'?').$str_utm:''));
		$item->appendChild($g_link);

		$g_image_link = $dom->createElement("g:image_link", "http://".$SETUP_SERVER_NAME.CFile::GetPath($arProduct["PREVIEW_PICTURE"]));
		$item->appendChild($g_image_link);

		$g_condition = $dom->createElement("g:condition", "new");
		$item->appendChild($g_condition);

		$g_availability = $dom->createElement("g:availability", "in stock");
		$item->appendChild($g_availability);

		$g_price = $dom->createElement("g:price", $minPrice." ".$minPriceCurrency);
		$item->appendChild($g_price);

		if ($arProduct["PROPERTIES"]["MAKER"]["VALUE"] && array_key_exists($arProduct["PROPERTIES"]["MAKER"]["VALUE"], $arMakers)) {
			$g_brand = $dom->createElement("g:brand", google_text2xml($arMakers[$arProduct["PROPERTIES"]["MAKER"]["VALUE"]], true));
			$item->appendChild($g_brand);
		}

		if ($arProduct["IBLOCK_SECTION_ID"]) {
			$g_google_product_category = $dom->createElement("g:google_product_category", getGoogleCategory($arSections, $arProduct["IBLOCK_SECTION_ID"]));
			$item->appendChild($g_google_product_category);

			$arNav = getProductType($arSections, $arProduct["IBLOCK_SECTION_ID"], array());
			$g_product_type = $dom->createElement("g:product_type", google_text2xml(implode(" > ", $arNav), true));
			$item->appendChild($g_product_type);
		}

		$channel->appendChild($item);
	}
}

$dom->save($_SERVER["DOCUMENT_ROOT"].$SETUP_FILE_NAME);

?>