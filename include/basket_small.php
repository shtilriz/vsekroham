<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?$APPLICATION->IncludeComponent("bitrix:sale.basket.basket.small", ".default", Array(
	"PATH_TO_BASKET" => "/basket/",	// Страница корзины
	"PATH_TO_ORDER" => "/personal/order.php",	// Страница оформления заказа
	),
	false
);?>