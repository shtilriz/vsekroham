<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?
/*set_time_limit(0);
CModule::IncludeModule("iblock");
$el = new CIBlockElement;

$arIDs = array();
$rsProducts = CIBlockElement::GetList(
	array(),
	array(
		"IBLOCK_ID" => 2,
		">ID" => 60000,
		"<=" => 65000
	),
	false,
	false,
	array("ID", "XML_ID")
);
while ($arProduct = $rsProducts->GetNext()) {
	if (strlen($arProduct["XML_ID"]) > 10)
		$arIDs[] = $arProduct["ID"];
}

foreach ($arIDs as $id) {
	if (!$id)
		continue;
	$arFields = array('XML_ID' => $id);
	if ($PRODUCT_ID = $el->Update($id, $arFields))
		echo $PRODUCT_ID.'<br/>';
	else
		echo "Error: ".$el->LAST_ERROR."<br/>";
}*/
?>