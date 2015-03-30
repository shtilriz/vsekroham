<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?
//устанавливает флажок "Выгружать на маркет" в соответствии с товарами старого сайта
CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");
$content = file_get_contents("http://vsekroham.ru/export/getMarket.php");
$arIDs = json_decode($content, true);

$arProducts = array();
$rsProducts = CIBlockElement::GetList(
	array("ID" => "ASC"),
	array(
		"IBLOCK_ID" => array(IBLOCK_PRODUCT_ID, IBLOCK_SKU_ID),
	),
	false,
	false,
	array("IBLOCK_ID", "ID", "PROPERTY_OLD_LINK")
);
while ($arProduct = $rsProducts->GetNext()) {
	$arProducts[$arProduct["ID"]] = array(
		"IBLOCK_ID" => $arProduct["IBLOCK_ID"],
		"OLD_LINK" => $arProduct["PROPERTY_OLD_LINK_VALUE"]
	);
}

foreach ($arProducts as $id => $arItem) {
	if ($arItem["IBLOCK_ID"] == IBLOCK_PRODUCT_ID) {
		$rsOffers = CIBlockElement::GetList(array(),array("IBLOCK_ID" => IBLOCK_SKU_ID,"PROPERTY_CML2_LINK" => $id),false,false,array("IBLOCK_ID", "ID"));
		if ($rsOffers->SelectedRowsCount() > 0) {
			CIBlockElement::SetPropertyValuesEx($id, $arItem["IBLOCK_ID"], array("MARKET" => ""));
			continue;
		}
	}

	/*if ($arItem["IBLOCK_ID"] == IBLOCK_PRODUCT_ID) {
		$enum_id = 2;
	}
	elseif ($arItem["IBLOCK_ID"] == IBLOCK_SKU_ID) {
		$enum_id = 82;
	}
	CIBlockElement::SetPropertyValuesEx($id, $arItem["IBLOCK_ID"], array("MARKET" => (in_array($arItem["OLD_LINK"], $arIDs)?$enum_id:'')));*/
}
?>