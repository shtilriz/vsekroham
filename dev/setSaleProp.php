<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?
/*CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");
$rsProductDiscounts = CCatalogDiscount::GetList(
	array (),
	array (
		"ACTIVE" => "Y",
		"COUPON" => "",
		"ID" => array(1, 2, 3)
	),
	false,
	false,
	array ("ID", "PRODUCT_ID")
);
while ($arProductDiscount = $rsProductDiscounts->GetNext()) {
	$arProducts[] = $arProductDiscount["PRODUCT_ID"];
}

foreach ($arProducts as $key => $id) {
	if ($id)
		CIBlockElement::SetPropertyValuesEx($id, false, array("SALE_EXIST" => 124));
}*/
?>