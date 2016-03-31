<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?
//посылает данные товара для использования в электронной коммеруии яндекса
$id = abs((int)$_GET["id"]);
$arReturn = array();
if ($id) {
	CModule::IncludeModule("iblock");
	CModule::IncludeModule("catalog");
	$rsProduct = CIBlockElement::GetList(
		array(),
		array(
			"ACTIVE" => "Y",
			"ID" => $id
		),
		false,
		false,
		array("IBLOCK_ID", "ID", "NAME", "IBLOCK_SECTION_ID", "CATALOG_GROUP_1", "PROPERTY_MAKER")
	);
	if ($arProduct = $rsProduct->GetNext()) {
		$IBLOCK_SECTION_ID = $arProduct["IBLOCK_SECTION_ID"];
		$MAKER_ID = $arProduct["PROPERTY_MAKER_VALUE"];
		$mxResult = CCatalogSku::GetProductInfo($arProduct["ID"]);
		if (is_array($mxResult)) {
			$rsMainPr = CIBlockElement::GetList(array(), array("ID" => $mxResult["ID"]), false, false, array("IBLOCK_SECTION_ID", "PROPERTY_MAKER"));
			if ($arMainPr = $rsMainPr->GetNext()) {
				$IBLOCK_SECTION_ID = $arMainPr["IBLOCK_SECTION_ID"];
				$MAKER_ID = $arMainPr["PROPERTY_MAKER_VALUE"];
			}
		}

		$arSections = array();
		if ($IBLOCK_SECTION_ID) {
			$nav = CIBlockSection::GetNavChain(false, $IBLOCK_SECTION_ID);
			while ($arSectionPath = $nav->GetNext())
				$arSections[] = $arSectionPath["NAME"];
		}
		$makerName = '';
		if ($MAKER_ID) {
			$rsMaker = CIBlockElement::GetList(array(), array("ID" => $MAKER_ID), false, false, array("NAME"));
			if ($arMaker = $rsMaker->GetNext())
				$makerName = $arMaker["NAME"];
		}

		$arReturn = array(
			"ID" => $arProduct["ID"],
			"NAME" => $arProduct["NAME"],
			"PRICE" => $arProduct["CATALOG_PRICE_1"],
			"QUANTITY" => 1,
			"SECTIONS" => implode("/", $arSections),
			"MAKER" => $makerName
		);
	}
}
echo json_encode($arReturn);
?>