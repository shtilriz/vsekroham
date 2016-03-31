<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */

//есть ли новогодние скидки для данного товара
foreach ($arResult["ITEMS"] as $key => $arItem) {
	$arResult["ITEMS"][$key]["B_NEW_YEAR_DISCOUNT"] = false;
	$rsProductDiscounts = CCatalogDiscount::GetList(
		array (),
		array (
			"+PRODUCT_ID" => $arItem["ID"],
			"ACTIVE" => "Y",
			"COUPON" => "",
			"ID" => array(1, 2, 3)
		),
		false,
		false,
		array ("ID")
	);
	if ($rsProductDiscounts->SelectedRowsCount())
		$arResult["ITEMS"][$key]["B_NEW_YEAR_DISCOUNT"] = true;
}
?>