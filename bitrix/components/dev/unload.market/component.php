<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
	
if(!(CModule::IncludeModule("iblock"))) {
	ShowError("IBLOCK_MODULE_NOT_INSTALLED");
	return false;
}
$arParams["IBLOCK_ID"] = (int)$arParams["IBLOCK_ID"];

//список разделов каталога
$obCache = new CPHPCache();
$cacheLifetime = 3600; $cacheID = "unload.market"; $cachePath = "/".$cacheID;
$arResult = array();
if ($obCache->InitCache($cacheLifetime, $cacheID, $cachePath)) {
	$vars = $obCache->GetVars();
	$arResult = $vars["arResult"];
}
else {
	$resS = CIBlockSection::GetList(
		array("left_margin"=>"asc"),
		array(
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"ACTIVE" => "Y"
		),
		false,
		array("IBLOCK_ID", "ID", "NAME", "DEPTH_LEVEL")
	);
	while ($arSection = $resS->GetNext()) {
		$arResult["SECTIONS"][] = array(
			"ID" => $arSection["ID"],
			"NAME" => $arSection["NAME"],
			"DEPTH_LEVEL" => $arSection["DEPTH_LEVEL"]
		);
	}

	//список производителей
	$rsMaker = CIBlockElement::GetList(
		array("NAME" => "ASC", "SORT" => "ASC"),
		array(
			"IBLOCK_ID" => $arParams["IBLOCK_ID_MAKER"],
			"ACTIVE" => "Y"
		),
		false,
		false,
		array("IBLOCK_ID", "ID", "NAME")
	);
	while ($arMaker = $rsMaker->GetNext()) {
		$arResult["MAKERS"][] = array(
			"ID" => $arMaker["ID"],
			"NAME" => $arMaker["NAME"]
		);
	}
	$obCache->EndDataCache(array("arResult" => $arResult));
}

if ($_POST["formProducts"] == "Y") {
	if (is_array($_POST["MARKET_PRODUCT_Y"]) && !empty($_POST["MARKET_PRODUCT_Y"])) {
		foreach ($_POST["MARKET_PRODUCT_Y"] as $key => $id) {
			CIBlockElement::SetPropertyValuesEx($id, IBLOCK_PRODUCT_ID, array("MARKET" => 2));
		}
	}
	if (is_array($_POST["MARKET_PRODUCT_N"]) && !empty($_POST["MARKET_PRODUCT_N"])) {
		foreach ($_POST["MARKET_PRODUCT_N"] as $key => $id) {
			CIBlockElement::SetPropertyValuesEx($id, IBLOCK_PRODUCT_ID, array("MARKET" => NULL));
		}
	}
	
	if (is_array($_POST["MARKET_Y"]) && !empty($_POST["MARKET_Y"])) {
		foreach ($_POST["MARKET_Y"] as $key => $id) {
			CIBlockElement::SetPropertyValuesEx($id, IBLOCK_SKU_ID, array("MARKET" => 82));
		}
	}
	if (is_array($_POST["MARKET_N"]) && !empty($_POST["MARKET_N"])) {
		foreach ($_POST["MARKET_N"] as $key => $id) {
			CIBlockElement::SetPropertyValuesEx($id, IBLOCK_SKU_ID, array("MARKET" => NULL));
		}
	}

	$arResult["MESSAGE"] = array(
		"SUCCESS" => "Y",
		"REPLY" => "Изменения успешно сохранены!"
	);
}

if ($_POST["formParams"] == "Y" || $_POST["formProducts"] == "Y") {
	$arFilter = array(
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"ACTIVE" => "Y"
	);
	if (intval($_POST["section"]) > 0) {
		$arFilter["SECTION_ID"] = $_POST["section"];
		$arFilter["INCLUDE_SUBSECTIONS"] = "Y";
	}
	if (intval($_POST["maker"]) > 0) {
		$arFilter["PROPERTY_MAKER"] = intval($_POST["maker"]);
	}
	$rsProducts = CIBlockElement::GetList(
		array("SORT" => "ASC"),
		$arFilter,
		false,
		false,
		array("IBLOCK_ID", "ID", "NAME", "PREVIEW_PICTURE", "DETAIL_PAGE_URL", "PROPERTY_MARKET")
	);
	while ($arProduct = $rsProducts->GetNext()) {		
		$arSKU_items = array();
		$rsSKU = CIBlockElement::GetList(
			array("ID" => "ASC"),
			array(
				"IBLOCK_ID" => IBLOCK_SKU_ID,
				"ACTIVE" => "Y",
				"PROPERTY_CML2_LINK" => $arProduct["ID"]
			),
			false,
			false,
			array("IBLOCK_ID", "ID", "NAME", "PREVIEW_PICTURE", "PROPERTY_MARKET", "PROPERTY_COLOR", "PROPERTY_SIZE")
		);
		while ($arSKU = $rsSKU->GetNext()) {
			$arSKU_items[] = array(
				"IBLOCK_ID" => $arSKU["IBLOCK_ID"],
				"ID" => $arSKU["ID"],
				"NAME" => $arSKU["NAME"],
				"PREVIEW_PICTURE" => $arSKU["PREVIEW_PICTURE"],
				"MARKET" => $arSKU["PROPERTY_MARKET_VALUE"],
				"COLOR" => $arSKU["PROPERTY_COLOR_VALUE"],
				"SIZE" => $arSKU["PROPERTY_SIZE_VALUE"]
			);
		}
		$arResult["PRODUCTS"][] = array(
			"IBLOCK_ID" => $arProduct["IBLOCK_ID"],
			"ID" => $arProduct["ID"],
			"NAME" => $arProduct["NAME"],
			"PREVIEW_PICTURE" => $arProduct["PREVIEW_PICTURE"],
			"DETAIL_PAGE_URL" => $arProduct["DETAIL_PAGE_URL"],
			"MARKET" => $arProduct["PROPERTY_MARKET_VALUE"],
			"OFFERS" => $arSKU_items
		);
	}
	if (count($arResult["PRODUCTS"]) <= 0) {
		$arResult["MESSAGE"] = array(
			"SUCCESS" => "N",
			"REPLY" => "Нет товаров по данному фильтру. Измените параметры фильтра."
		);
	}
}

$this->IncludeComponentTemplate();
?>