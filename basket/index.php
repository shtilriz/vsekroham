<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("ecomm_pagetype", "cart");
$APPLICATION->SetTitle("Корзина");
?>

<?$APPLICATION->IncludeComponent(
	"bitrix:sale.basket.basket",
	"new",
	array(
		"COLUMNS_LIST" => array(
			0 => "NAME",
			1 => "PROPS",
			2 => "DELETE",
			3 => "PRICE",
			4 => "QUANTITY",
			5 => "SUM",
		),
		"PATH_TO_ORDER" => "/basket/",
		"HIDE_COUPON" => "N",
		"PRICE_VAT_SHOW_VALUE" => "N",
		"COUNT_DISCOUNT_4_ALL_QUANTITY" => "N",
		"USE_PREPAYMENT" => "N",
		"QUANTITY_FLOAT" => "N",
		"SET_TITLE" => "Y",
		"ACTION_VARIABLE" => "action",
		"OFFERS_PROPS" => array(
			0 => "SIZE",
			1 => "COLOR",
		),
		"COMPONENT_TEMPLATE" => "new"
	),
	false
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>