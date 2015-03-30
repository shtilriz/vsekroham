<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!isset($arParams["CACHE_TIME"])) {
	$arParams["CACHE_TIME"] = 3600;
}

CPageOption::SetOptionString("main", "nav_page_in_session", "N");

if($arParams["IBLOCK_ID"] < 1) {
	ShowError("IBLOCK_ID не указан");
	return false;
}
if($arParams["IBLOCK_ID_CATALOG"] < 1) {
	ShowError("IBLOCK_ID_CATALOG не указан");
	return false;
}
if($arParams["SECTION_ID"] < 1 && strlen($arParams["SECTION_CODE"]) < 0) {
	return false;
}

if($this->StartResultCache(false, array($arParams["SECTION_ID"], $arParams["SECTION_CODE"])))
{
	if(!(CModule::IncludeModule("iblock") && CModule::IncludeModule("catalog"))) {
		$this->AbortResultCache();
		ShowError("IBLOCK_MODULE_NOT_INSTALLED");
		return false;
	}
	if ($arParams["SECTION_ID"] <= 0) {
		$arParams["SECTION_ID"] = CIBlockFindTools::GetSectionID(
			$arParams["SECTION_ID"],
			$arParams["SECTION_CODE"],
			false,
			false,
			array(
				"GLOBAL_ACTIVE" => "Y",
				"IBLOCK_ID" => $arParams["IBLOCK_ID_CATALOG"]
			)
		);
	}

	$arResult = array();
	$res = CIBlockElement::GetList(
		array("NAME" => "ASC"),
		array(
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"ACTIVE" => "Y",
			"PROPERTY_SECTION_ID" => $arParams["SECTION_ID"]
		),
		false,
		false,
		array("IBLOCK_ID", "ID", "NAME", "CODE", "PROPERTY_MAKER")
	);
	while ($arRes = $res->GetNext()) {
		//проверить, есть ли товары в данной категории по производителям
		$rsProducts = CIBlockElement::GetList(
			array(),
			array(
				"IBLOCK_ID" => IBLOCK_PRODUCT_ID,
				"ACTIVE" => "Y",
				"SECTION_ID" => $arParams["SECTION_ID"],
				"INCLUDE_SUBSECTIONS" => "Y",
				"PROPERTY_MAKER" => $arRes["PROPERTY_MAKER_VALUE"]
			),
			false,
			false,
			array("IBLOCK_ID", "ID")
		);
		if ($rsProducts->SelectedRowsCount() > 0) {
			$maker_name = '';
			if ($arRes["PROPERTY_MAKER_VALUE"]) {
				$resM = CIBlockElement::GetByID($arRes["PROPERTY_MAKER_VALUE"]);
				if($ar_resM = $resM->GetNext())
					$maker_name = $ar_resM["NAME"];
			}
			$arResult[] = array(
				"NAME" => $maker_name,
				"LINK" => $arParams["FOLDER"].$arRes["CODE"].'/'
			);
		}
	}

	$this->IncludeComponentTemplate();
}

?>