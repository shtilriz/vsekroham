<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */?>

<?

//$arResult["B_OFFERS"] = isProductAvailable($arResult);
//торговые предложения
/*foreach ($arResult["OFFERS"] as $keyOffer => $arOffer) {
	if ($arOffer["CATALOG_QUANTITY"] <= 0 || empty($arOffer["PROPERTIES"]) && empty($arOffer["PROPERTIES"]["SIZE"]["VALUE"])) {
		unset($arResult["OFFERS"][$keyOffer]);
	}
}*/

if (!empty($arResult["OFFERS"])) {
	$thisSKUProps = array();
	$arColors = array();
	$arSizes = array();
	foreach ($arResult["OFFERS"] as $keyOffer => $arOffer) {
		/*if ($arOffer["CATALOG_QUANTITY"] <= 0)
			continue;*/
		if (
			!in_array($arOffer["PROPERTIES"]["COLOR"]["VALUE"], $arColors) &&
			!empty($arOffer["PROPERTIES"]["COLOR"]["VALUE"])
		) {
			$thisSKUProps["COLOR"]["VALUES"][$arOffer["ID"]] = array(
				"VALUE" => $arOffer["PROPERTIES"]["COLOR"]["VALUE"],
				"PREVIEW_PICTURE" => $arOffer["PREVIEW_PICTURE"]
			);
			$arColors[] = $arOffer["PROPERTIES"]["COLOR"]["VALUE"];
		}
		if (
			!in_array($arOffer["PROPERTIES"]["SIZE"]["VALUE"], $arSizes) &&
			!empty($arOffer["PROPERTIES"]["SIZE"]["VALUE"])
		) {
			$thisSKUProps["SIZE"]["VALUES"][$arOffer["ID"]] = array(
				"VALUE" => $arOffer["PROPERTIES"]["SIZE"]["VALUE"],
				"PREVIEW_PICTURE" => $arOffer["PREVIEW_PICTURE"]
			);
			$arSizes[] = $arOffer["PROPERTIES"]["SIZE"]["VALUE"];
		}

		$ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($arOffer["IBLOCK_ID"], $arOffer["ID"]);
		$arResult["OFFERS"][$keyOffer]["IPROPERTY_VALUES"] = $ipropValues->getValues();
	}
	$arResult["THIS_SKU_PROPS"] = $thisSKUProps;
}

//для выгрузки в объект js
$arSkuProps = array();
if (!empty($arColors)) {
	$arSkuProps[] = "COLOR";
}
if (!empty($arSizes)) {
	$arSkuProps[] = "SIZE";
}
$arTree = array();
$arSizesInColor = array();
$arOffersPrice = array();
foreach ($arResult["OFFERS"] as $key => $arOffer) {
	/*if ($arOffer["CATALOG_QUANTITY"] <= 0)
		continue;*/
	foreach ($arOffer["PROPERTIES"] as $prop => $arProp) {
		if (in_array($prop, $arSkuProps)) {
			$arTree[$arOffer["ID"]][$prop] = $arProp["VALUE"];
		}
	}
	$arOffersPriceDiscount[$arOffer["ID"]] = $arOffer["PRICES"]["BASE"]["PRINT_DISCOUNT_VALUE"];
	$arOffersPrice[$arOffer["ID"]] = $arOffer["PRICES"]["BASE"]["PRINT_VALUE"];
	$arOffersPriceMargin[$arOffer["ID"]] = $arOffer["PRICES"]["MARGIN"]["PRINT_VALUE"];
	$arOffersDiscountDiff[$arOffer["ID"]] = $arOffer["PRICES"]["BASE"]["DISCOUNT_DIFF"];

	if (!empty($arOffer["PROPERTIES"]["COLOR"]["VALUE"]) && !empty($arOffer["PROPERTIES"]["SIZE"]["VALUE"])) {
		$arSizesInColor[$arOffer["PROPERTIES"]["COLOR"]["VALUE"]][$arOffer["ID"]] = $arOffer["PROPERTIES"]["SIZE"]["VALUE"];
	}
}

$arResult["JS"]["PRODUCT_ID"] = $arResult["ID"];
$arResult["JS"]["B_OFFERS"] = (!empty($arResult["OFFERS"])?true:false);
$arResult["JS"]["SKU_PROPS"] = $arSkuProps;
$arResult["JS"]["TREE"] = $arTree;
$arResult["JS"]["SIZE_IN_COLOR"] = $arSizesInColor;
$arResult["JS"]["PRICE_DISCOUNT"] = $arOffersPriceDiscount;
$arResult["JS"]["PRICE"] = $arOffersPrice;
$arResult["JS"]["PRICE_MARGIN"] = $arOffersPriceMargin;
$arResult["JS"]["DISCOUNT_DIFF"] = $arOffersDiscountDiff;
$arResult["JS"]["CNT_COLOR"] = count($arResult["THIS_SKU_PROPS"]["COLOR"]["VALUES"]);
$arResult["JS"]["CNT_SIZE"] = count($arResult["THIS_SKU_PROPS"]["SIZE"]["VALUES"]);

use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use Bitrix\Main\Type as FieldType;
//страна бренда
if (CModule::IncludeModule("iblock") && CModule::IncludeModule("highloadblock") && intval($arResult["PROPERTIES"]["MAKER"]["VALUE"]) > 0) {
	$countryID = 0;
	$rsMaker = CIBlockElement::GetList(array(),array("IBLOCK_ID" => $arResult["PROPERTIES"]["MAKER"]["LINK_IBLOCK_ID"],"ACTIVE" => "Y","ID" => $arResult["PROPERTIES"]["MAKER"]["VALUE"]),false,false,array("IBLOCK_ID", "ID", "PROPERTY_COUNTRY"));
	if ($arMaker = $rsMaker->GetNext()) {
		$countryID = $arMaker["PROPERTY_COUNTRY_VALUE"];
	}

	if (intval($countryID) > 0) {
		$hlblock = HL\HighloadBlockTable::getById(1)->fetch();
		$entity = HL\HighloadBlockTable::compileEntity($hlblock);
		$entity_data_class = $entity->getDataClass();
		$rsData = $entity_data_class::getList(array(
			"select" => array("*"),
			"order" => array(),
			"filter" => array("UF_XML_ID" => intval($countryID))
		));
		if ($arCountry = $rsData->Fetch()) {
			$arResult["COUNTRY"] = $arCountry;
		}
	}
}

//подарок
if ($arResult["PROPERTIES"]["GIFT"]["VALUE"]) {
	$rsGift = CIBlockElement::GetList(
		array(),
		array(
			"IBLOCK_ID" => $arResult["PROPERTIES"]["GIFT"]["LINK_IBLOCK_ID"],
			"ACTIVE" => "Y",
			"ID" => $arResult["PROPERTIES"]["GIFT"]["VALUE"]
		),
		false,
		false,
		array("IBLOCK_ID", "ID", "NAME", "DETAIL_PAGE_URL")
	);
	if ($arGift = $rsGift->GetNext()) {
		$arResult["GIFT"] = array(
			"NAME" => $arGift["NAME"],
			"DETAIL_PAGE_URL" => $arGift["DETAIL_PAGE_URL"]
		);
	}
}

//список комплектации
foreach ($arResult["PROPERTIES"]["OPTIONS"]["VALUE"] as $key => $xml_id) {
	$hlblock = HL\HighloadBlockTable::getById(4)->fetch();
	$entity = HL\HighloadBlockTable::compileEntity($hlblock);
	$entity_data_class = $entity->getDataClass();
	$rsData = $entity_data_class::getList(array(
		"select" => array("UF_NAME"),
		"order" => array(),
		"filter" => array("UF_XML_ID" => $xml_id)
	));
	if ($arOption = $rsData->Fetch()) {
		$arResult["PROPERTIES"]["OPTIONS"]["VALUES"][] = $arOption["UF_NAME"];
	}
}

//рекомендуемые товары
$arResult["RECOMMEND"]["TYPE"] = '';
$query = 'http://api.retailrocket.ru/api/1.0/Recomendation/UpSellItemToItems/53a000601e994424286fc7d9/'.$arResult["ID"];
$xml_string = file_get_contents($query);
$arData = json_decode($xml_string,true);
$arResult["RECOMMEND"]["TYPE"] = "UpSellItemToItems";

//если выборка пустая, смотрим Рекомендации для карточки товара
if (empty($arData)) {
	$query = 'http://api.retailrocket.ru/api/1.0/Recomendation/ItemToItems/53a000601e994424286fc7d9/'.$arResult["ID"];
	$xml_string = file_get_contents($query);
	$arData = json_decode($xml_string,true);
	$arResult["RECOMMEND"]["TYPE"] = "ItemToItems";
}
//если выборка пустая, смотрим сопутствующие товары
if (empty($arData)) {
	$query = 'http://api.retailrocket.ru/api/1.0/Recomendation/CrossSellItemToItems/53a000601e994424286fc7d9/'.$arResult["ID"];
	$xml_string = file_get_contents($query);
	$arData = json_decode($xml_string,true);
	$arResult["RECOMMEND"]["TYPE"] = "CrossSellItemToItems";
}
//если и эта выборка пустая, смотрим хиты продаж из категории
if (empty($arData)) {
	$query = 'http://api.retailrocket.ru/api/1.0/Recomendation/CategoryToItems/53a000601e994424286fc7d9/'.$IBLOCK_SECTION_ID["ID"];
	$xml_string = file_get_contents($query);
	$arData = json_decode($xml_string,true);
	$arResult["RECOMMEND"]["TYPE"] = "CategoryToItems";
}

$arResult["RECOMMEND"]["IDS"] = $arData;

if (!empty($arData) && is_array($arData)) {
	foreach ($arData as $key => $id) {
		$rsRecommends = CIBlockElement::GetList(
			array(),
			array(
				"IBLOCK_ID" => $arParams["IBLOCK_ID"],
				"ACTIVE" => "Y",
				"ID" => $id
			),
			false,
			false,
			array("IBLOCK_ID", "ID", "NAME", "PREVIEW_PICTURE", "DETAIL_PAGE_URL", "CATALOG_GROUP_1")
		);
		if ($obRecommend = $rsRecommends->GetNextElement()) {
			$arRecommend = $obRecommend->GetFields();
			$arRecommend["PROPERTIES"] = $obRecommend->GetProperties();
			$arCatalogPrices = CIBlockPriceTools::GetCatalogPrices(false, array('BASE'));
			$arRecommend["PRICES"] = CIBlockPriceTools::GetItemPrices(false, $arCatalogPrices, $arRecommend, false, array());
			$arResult["RECOMMEND"]["ITEMS"][] = $arRecommend;
		}
	}
}

//если параметры товара (вес, длина, ширина и высота) равны 0, то достаем эти параметры из свойств родительского раздела
if (($arResult["CATALOG_WEIGHT"] == 0 || $arResult["CATALOG_WIDTH"] == 0 || $arResult["CATALOG_LENGTH"] == 0 || $arResult["CATALOG_HEIGHT"] == 0) && (int)$arResult["IBLOCK_SECTION_ID"] > 0) {
	$rsSection = CIBlockSection::GetList(
		array(),
		array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "ACTIVE" => "Y", "ID" => $arResult["IBLOCK_SECTION_ID"]),
		false,
		array("IBLOCK_ID", "ID", "UF_WEIGHT", "UF_WIDTH", "UF_LENGTH", "UF_HEIGHT")
	);
	if ($arSection = $rsSection->GetNext()) {
		$arResult["CATALOG_WEIGHT"] = (int)$arSection["UF_WEIGHT"];
		$arResult["CATALOG_WIDTH"] = (int)$arSection["UF_WIDTH"];
		$arResult["CATALOG_LENGTH"] = (int)$arSection["UF_LENGTH"];
		$arResult["CATALOG_HEIGHT"] = (int)$arSection["UF_HEIGHT"];
	}
}
?>