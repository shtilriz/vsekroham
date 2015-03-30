<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?
//переносит товары со старого сайта на новый
CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");
$IBLOCK_ID = 1;
$IBLOCK_ID_SKU = 2;
$SITE_LINK = 'http://test.vsekroham.ru';
//запросить массив данных со старого сайта
$content = file_get_contents($SITE_LINK."/export/getProducts.php");
$arResult = json_decode($content, true);

//достать свойство OLD_LINK всех добавленных товаров, чтобы не создавать их по новой в случае чего
$arAdded = array();
$rsAdded = CIBlockElement::GetList(
	array(),
	array(
		"IBLOCK_ID" => 1
	),
	false,
	false,
	array("IBLOCK_ID", "PROPERTY_OLD_LINK")
);
while ($arRes = $rsAdded->GetNext()) {
	$arAdded[] = $arRes["PROPERTY_OLD_LINK_VALUE"];
}

//достать массив всех доступных свойств товара
$arProperties = array();
$rsProps = CIBlockProperty::GetList(
	array("sort"=>"asc", "name"=>"asc"),
	array(
		"IBLOCK_ID" => $IBLOCK_ID,
		"ACTIVE" => "Y",
		"!XML_ID" => false
	)
);
while ($arProp = $rsProps->GetNext()) {
	$arProperties[$arProp["XML_ID"]] = $arProp;
	if ($arProp["PROPERTY_TYPE"] == "L") {
		$rsEnums = CIBlockPropertyEnum::GetList(array("SORT"=>"ASC"), array("IBLOCK_ID"=>$IBLOCK_ID, "PROPERTY_ID"=>$arProp["ID"]));
		while ($arEnum = $rsEnums->GetNext()) {
			$arProperties[$arProp["XML_ID"]]["ENUMS"][$arEnum["XML_ID"]] = $arEnum["ID"];
		}
	}
}

//достать массив всех разделов каталога
$arSections = array();
$rsSections = CIBlockSection::GetList(
	array("SORT" => "ASC"),
	array(
		"IBLOCK_ID" => $IBLOCK_ID
	),
	false,
	array("IBLOCK_ID", "ID", "NAME", "XML_ID")
);
while ($arSect = $rsSections->GetNext()) {
	$arSections[$arSect["XML_ID"]] = array(
		"ID" => $arSect["ID"],
		"NAME" => $arSect["NAME"]
	);
}

//перебрать все товары
foreach ($arResult as $key => $arItem) {
	if (in_array($arItem["ID"], $arAdded))
		continue;
	//сохранить все поля и свойства товара в основной товар
	$el = new CIblockElement;
	$arProps = array();
	foreach ($arItem["PROPERTIES"] as $pKey => $arProp) {
		$pID = $arProperties[$arProp["ID"]]["ID"];
		if (array_key_exists($arProp["ID"], $arProperties) && !empty($arProp["VALUE"]) && $pID > 0) {
			if ($arProp["PROPERTY_TYPE"] == "L" && $arProp["PROPERTY_TYPE"] == $arProperties[$arProp["ID"]]["PROPERTY_TYPE"]) {
				//$arProps[$pID] = $arProp["VALUE_ENUM_ID"];
				$arProps[$pID] = $arProperties[$arProp["ID"]]["ENUMS"][$arProp["VALUE_ENUM_ID"]];
			}
			elseif (in_array($arProp["PROPERTY_TYPE"], array("S","N")) && !is_array($arProp["VALUE"])) {
				$arProps[$pID] = htmlspecialcharsBack($arProp["VALUE"]);
			}
			elseif ($arProp["PROPERTY_TYPE"] == "E" && $arProp["CODE"] == "MAKER") {
				$rsMaker = CIBlockElement::GetList(
					array(),
					array("IBLOCK_ID" => 3,	"XML_ID" => $arProp["VALUE"]),
					false,
					false,
					array("IBLOCK_ID", "ID")
				);
				if ($arMaker = $rsMaker->GetNext()) {
					$arProps[$pID] = $arMaker["ID"];
				}
			}
			elseif ($arProp["PROPERTY_TYPE"] == "F" && is_array($arProp["VALUE"])) {
				foreach ($arProp["VALUE"] as $fKey => $src) {
					$arProps[$pID][$fKey] = CFile::MakeFileArray($SITE_LINK.$src);
					$arProps[$pID][$fKey]["description"] = $arProp["DESCRIPTION"][$fKey];
				}
			}
			elseif ($arProp["PROPERTY_TYPE"] == "F" && !is_array($arProp["VALUE"])) {
				$arProps[$pID] = CFile::MakeFileArray($SITE_LINK.$arProp["VALUE"]);
				$arProps[$pID]["description"] = $arProp["DESCRIPTION"];
			}
		}
		if ($arProp["ID"] == 67) { //нет в наличии
			$arProps[4] = ($arProp["VALUE_ENUM_ID"]?'':'1');
		}
	}
	$arProps["OLD_LINK"] = $arItem["ID"];
	$arLoad = array(
		"IBLOCK_ID" => $IBLOCK_ID,
		"IBLOCK_SECTION_ID" => $arSections[$arItem["IBLOCK_SECTION_ID"]]["ID"],
		"NAME" => $arItem["NAME"],
		"CODE" => $arItem["CODE"],
		//"XML_ID" => $arItem["ID"],
		"SORT" => $arItem["SORT"],
		"MODIFIED_BY" => $USER->GetID(),
		"PROPERTY_VALUES"=> $arProps,
		"ACTIVE" => $arItem["ACTIVE"],
		"PREVIEW_TEXT" => $arItem["PREVIEW_TEXT"],
		"PREVIEW_TEXT_TYPE" => $arItem["PREVIEW_TEXT_TYPE"],
		"DETAIL_TEXT" => $arItem["DETAIL_TEXT"],
		"DETAIL_TEXT_TYPE" => $arItem["DETAIL_TEXT_TYPE"],
		"SHOW_COUNTER" => $arItem["SHOW_COUNTER"],
		"SHOW_COUNTER_START" => $arItem["SHOW_COUNTER_START"]
	);
	if ($arItem["DETAIL_PICTURE"] || $arItem["PREVIEW_PICTURE"]) {
		$arLoad["PREVIEW_PICTURE"] = CFile::MakeFileArray($SITE_LINK.($arItem["DETAIL_PICTURE"]?$arItem["DETAIL_PICTURE"]:$arItem["PREVIEW_PICTURE"]));
	}
	if ($PRODUCT_ID = $el->Add($arLoad)) {
		CCatalogProduct::Add(array("ID" => $PRODUCT_ID));
		//установить цену товара
		CPrice::SetBasePrice(
			$PRODUCT_ID,
			$arItem["CATALOG_PRICE_1"],
			"RUB",
			false,
			false
		);
		//если текущий товар содержит в себе ТП, то добавляем основной товар как ТП и все остальные ТП
		if (!empty($arItem["OFFERS"])) {
			echo "Добавлен новый товар ID: ".$PRODUCT_ID.'<br/>';
			$elMain = new CIblockElement;
			$arPropsMainTP = array(
				"CML2_LINK" => $PRODUCT_ID,
				"MARKET" => ($arItem["PROPERTIES"]["MARKET"]["VALUE"]=="Y"?82:false),
				"COLOR" => $arItem["PROPERTIES"]["COLOR"]["VALUE"],
				"SIZE" => $arItem["PROPERTIES"]["COLOR"]["SIZE"],
				"OLD_LINK" => $arItem["ID"]
			);
			$arMainTP = array(
				"IBLOCK_ID" => $IBLOCK_ID_SKU,
				"IBLOCK_SECTION_ID" => false,
				"NAME" => $arItem["NAME"],
				//"XML_ID" => $arItem["ID"],
				"MODIFIED_BY" => $USER->GetID(),
				"PROPERTY_VALUES"=> $arPropsMainTP,
				"ACTIVE" => $arItem["ACTIVE"]
			);
			if ($arItem["DETAIL_PICTURE"] || $arItem["PREVIEW_PICTURE"]) {
				$arMainTP["PREVIEW_PICTURE"] = CFile::MakeFileArray($SITE_LINK.($arItem["DETAIL_PICTURE"]?$arItem["DETAIL_PICTURE"]:$arItem["PREVIEW_PICTURE"]));
			}
			//сохранить основной товар как отдельное ТП
			if ($MAIN_TP_ID = $elMain->Add($arMainTP)) {
				CCatalogProduct::Add(array("ID" => $MAIN_TP_ID));
				echo "Добавлено новое торговое предложение ID: ".$MAIN_TP_ID.'<br/>';
				//установить цену
				CPrice::SetBasePrice(
					$MAIN_TP_ID,
					$arItem["CATALOG_PRICE_1"],
					"RUB",
					false,
					false
				);
			}
			else {
				echo "Ошибка: ".$elMain->LAST_ERROR.'<br/>';
			}
			//сохранить все ТП товара
			foreach ($arItem["OFFERS"] as $oKey => $arOffer) {
				$elOffer = new CIBlockElement;
				$arPropsTP = array(
					"CML2_LINK" => $PRODUCT_ID,
					"MARKET" => ($arOffer["PROPERTIES"]["MARKET"]["VALUE"]=="Y"?82:false),
					"COLOR" => $arOffer["PROPERTIES"]["COLOR"]["VALUE"],
					"SIZE" => $arOffer["PROPERTIES"]["SIZE"]["VALUE"],
					"MARGIN" => $arOffer["PROPERTIES"]["MARGIN"]["VALUE"],
					"OLD_LINK" => $arOffer["ID"]
				);
				$arTP = array(
					"IBLOCK_ID" => $IBLOCK_ID_SKU,
					"IBLOCK_SECTION_ID" => false,
					"NAME" => $arOffer["NAME"],
					//"XML_ID" => $arOffer["ID"],
					"MODIFIED_BY" => $USER->GetID(),
					"PROPERTY_VALUES"=> $arPropsTP,
					"ACTIVE" => $arOffer["ACTIVE"]
				);
				if ($arOffer["DETAIL_PICTURE"] || $arOffer["PREVIEW_PICTURE"]) {
					$arTP["PREVIEW_PICTURE"] = CFile::MakeFileArray($SITE_LINK.($arOffer["DETAIL_PICTURE"]?$arOffer["DETAIL_PICTURE"]:$arOffer["PREVIEW_PICTURE"]));
				}
				if ($TP_ID = $elOffer->Add($arTP)) {
					CCatalogProduct::Add(array("ID" => $TP_ID));
					echo "Добавлено новое торговое предложение ID: ".$TP_ID.'<br/>';
					//установить цену
					CPrice::SetBasePrice(
						$TP_ID,
						$arOffer["CATALOG_PRICE_1"],
						"RUB",
						false,
						false
					);
				}
				else {
					echo "Ошибка: ".$elOffer->LAST_ERROR.'<br/>';
				}
			}
		}
	}
	else {
		echo "Ошибка: ".$el->LAST_ERROR.'<br/>';
	}
	echo '<br/>';
}
?>