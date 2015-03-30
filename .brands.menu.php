<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION;

$obCache = new CPHPCache();
$cacheLifetime = 3600; $cacheID = "menu.brands"; $cachePath = "/".$cacheID;

if ($obCache->InitCache($cacheLifetime, $cacheID, $cachePath)) {
	$vars = $obCache->GetVars();
	$aMenuLinks = $vars["aMenuLinks"];
}
elseif ($obCache->StartDataCache()) {
	if (CModule::IncludeModule("iblock")) {
		$rs = CIBlockElement::GetList(
			array(
				"NAME" => "ASC",
				"SORT" => "ASC"
			),
			array(
				"IBLOCK_ID" => 3,
				"ACTIVE" => "Y"
			),
			false,
			false,
			array("IBLOCK_ID", "NAME", "DETAIL_PAGE_URL")
		);
		while($arRes = $rs->GetNext()) {
			//проверить, если у данного бренда товары
			$rsProducts = CIBlockElement::GetList(array(), array("IBLOCK_ID" => IBLOCK_PRODUCT_ID, "ACTIVE" => "Y", "PROPERTY_MAKER" => $arRes["ID"]), false, false, array("IBLOCK_ID", "ID"));
			if ($rsProducts->SelectedRowsCount()) {
				$aMenuLinks[] = array(
					$arRes["NAME"],
					$arRes["DETAIL_PAGE_URL"],
					"",
					array(
						"FROM_IBLOCK" => true,
						"IS_PARENT" => false,
						"DEPTH_LEVEL" => 1
					)
				);
			}
		}
	}
	$obCache->EndDataCache(array("aMenuLinks" => $aMenuLinks));
}
?>