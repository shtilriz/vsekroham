<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?
include($_SERVER["DOCUMENT_ROOT"]."/include/arCity.php");
if (!Cmodule::IncludeModule("catalog"))
	die();
if (!Cmodule::IncludeModule("sale"))
	die();

$arReturn = array();
$arEmails = array();
$rsOrders = CSaleOrder::GetList(array("DATE_INSERT" => "ASC"), array(), false, false, array("ID"));
while ($arOrder = $rsOrders->Fetch()) {
	$arProps = array();
	$db_vals = CSaleOrderPropsValue::GetList(
		array("SORT" => "ASC"),
		array(
			"ORDER_ID" => $arOrder["ID"],
			"ORDER_PROPS_ID" => array(1, 2, 4, 9)
		)
	);
	while ($arVals = $db_vals->Fetch()) {
		$arProps[$arVals["CODE"]] = $arVals;
	}

	if (in_array($arProps["CITY"]["VALUE"], $arCityMoskowRegion) && !in_array($arProps["EMAIL"]["VALUE"], $arEmails)) {
		$arEmails[] = $arProps["EMAIL"]["VALUE"];
		$arReturn[] = array(
			"FIO" => iconv("UTF-8", "windows-1251", $arProps["FIO"]["VALUE"].($arProps["FAMILY"]["VALUE"] ? " ".$arProps["FAMILY"]["VALUE"] : "")),
			"EMAIL" => $arProps["EMAIL"]["VALUE"]
		);
	}
}

$fp = fopen($_SERVER["DOCUMENT_ROOT"]."/upload/email-msk.csv", "w");
foreach ($arReturn as $fields) {
    fputcsv($fp, $fields, ";");
}
fclose($fp);
?>
<a href="/upload/email-msk.csv">Скачать файл</a>