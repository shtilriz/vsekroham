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

/*if ($_POST["formProducts"] == "Y") {
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
}*/

if (!empty($_POST) && $_POST["formProducts"] == "Y") {
	$arPostParams = array();
	foreach ($_POST["WEIGHT"] as $PRODUCT_ID => $weight) {
		$arPostParams[$PRODUCT_ID] = array(
			"WEIGHT" => ((int)$weight>0?(int)$weight:0),
			"LENGTH" => ((int)$_POST["LENGTH"][$PRODUCT_ID]>0?(int)$_POST["LENGTH"][$PRODUCT_ID]:0),
			"WIDTH" => ((int)$_POST["WIDTH"][$PRODUCT_ID]>0?(int)$_POST["WIDTH"][$PRODUCT_ID]:0),
			"HEIGHT" => ((int)$_POST["HEIGHT"][$PRODUCT_ID]>0?(int)$_POST["HEIGHT"][$PRODUCT_ID]:0),
		);
	}
	foreach ($arPostParams as $PRODUCT_ID => $arFields) {
		CCatalogProduct::Update($PRODUCT_ID, $arFields);
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
	if (isset($_POST["q"]) && strlen($_POST["q"]) > 0) {
		$arQuery = explode(" ", $_POST["q"]);
		$arNewQuery = array();
		foreach ($arQuery as $key => $q) {
			if ($q)
				$arNewQuery[] = trim($q);
		}

		$strQuery = '';
		if (!empty($arNewQuery)) {
			$strQuery = implode(" ", $arNewQuery);
		}
		$arFilter["?NAME"] = '('.$strQuery.')';
	}
	$rsElements = CIBlockElement::GetList(
		array("DATE_CREATE" => "DESC"),
		$arFilter,
		false,
		false,
		array("IBLOCK_ID", "ID", "NAME", "PREVIEW_PICTURE", "DETAIL_PAGE_URL", "DATE_CREATE", "TIMESTAMP_X")
	);
	while ($arElement = $rsElements->GetNext()) {		
		$rsProduct = CCatalogProduct::GetList(
			array(),
			array("ID" => $arElement["ID"]),
			false,
			false,
			array("ID", "WEIGHT", "LENGTH", "WIDTH", "HEIGHT")
		);
		if ($arProduct = $rsProduct->GetNext()) {
			$arResult["PRODUCTS"][] = array_merge($arElement, $arProduct);
		}
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