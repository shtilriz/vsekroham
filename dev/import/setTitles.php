<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/csv_data.php");?>

<?
//переносит тайтлы товаров с сайта все крохам
CModule::IncludeModule("iblock");
$content = file_get_contents("http://vsekroham.ru/export/getTitles.php");
$arTitles = json_decode($content, true);

$csvFile = new CCSVData('R', true);
$DATA_FILE_NAME = '/titles.csv';
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

$arCSV = array();
foreach ($arTitles as $id => $title) {
	$rsProduct = CIBlockElement::GetList(
		array(),
		array(
			"IBLOCK_ID" => 1,
			"ACTIVE" => "Y",
			"=XML_ID" => $id
		),
		false,
		false,
		array("IBLOCK_ID", "ID")
	);
	if ($arProduct = $rsProduct->GetNext()) {
		$el = new CIBlockElement;
		$arFields = array(
			"IPROPERTY_TEMPLATES"=>array(
				"ELEMENT_META_TITLE" => $title
			)
		);
		$res = $el->Update($arProduct["ID"], $arFields);
		$arCSV[] = array(
			$id,
			$arProduct["ID"],
			iconv("utf-8", 'windows-1251', $title)
		);
		echo $id.' '.$arProduct["ID"].'<br/>';
	}
}

foreach ($arCSV	as $key => $arItem) {
	$csvFile->SaveFile($_SERVER["DOCUMENT_ROOT"].$DATA_FILE_NAME, $arItem);
}
?>