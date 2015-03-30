<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/csv_data.php");?>

<?
set_time_limit(0);
if (CModule::IncludeModule("iblock") && CModule::IncludeModule("sale") && CModule::IncludeModule("catalog"))
{
	$csvFile = new CCSVData('R', true);
	$DATA_FILE_NAME = '/upload/orders.csv';
	$fp = fopen($_SERVER["DOCUMENT_ROOT"].$DATA_FILE_NAME, "w");
	if(!is_resource($fp))
	{
		$strError .= GetMessage("IBLOCK_ADM_EXP_CANNOT_CREATE_FILE")."<br>";
		$DATA_FILE_NAME = "";
	}
	else
	{
		fclose($fp);
	}

	$arXML2ID = array();
	$rsProducts = CIBlockElement::GetList(
		array("SORT" => "ASC"),
		array(
			"IBLOCK_ID" => array(IBLOCK_PRODUCT_ID, IBLOCK_SKU_ID)
		),
		false,
		false,
		array("IBLOCK_ID", "ID", "XML_ID")
	);
	while ($arProduct = $rsProducts->GetNext()) {
		$arXML2ID[$arProduct["XML_ID"]] = $arProduct["ID"];
	}

	$content = file_get_contents("http://vsekroham.ru/export/getOrders.php");
	$arResult = json_decode($content, true);

	foreach ($arResult as $key => $arItem) {
		if (array_key_exists($arItem[2], $arXML2ID)) {
			$arResult[$key][2] = $arXML2ID[$arItem[2]];
		}
	}

	foreach ($arResult as $key => $arItem) {
		$csvFile->SaveFile($_SERVER["DOCUMENT_ROOT"].$DATA_FILE_NAME, $arItem);
	}
	echo '<a href="'.htmlspecialchars($DATA_FILE_NAME).'">'.htmlspecialcharsex($DATA_FILE_NAME).'</a>';
}
?>