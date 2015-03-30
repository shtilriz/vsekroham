<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Оформление заказа");
CModule::IncludeModule("iblock");
CModule::IncludeModule("sale");
global $USER;
$APPLICATION->AddHeadScript('http://api-maps.yandex.ru/2.1/?lang=ru_RU');
?>

<?
if (isset($_POST) && !empty($_POST))
{
	//заказ сформирован
	include($_SERVER["DOCUMENT_ROOT"].'/basket/order-completed.php');
}
else
{
	//форма ввода данных покупателя, выбора службы доставки и формы оплаты
	include($_SERVER["DOCUMENT_ROOT"].'/basket/order-form.php');
}
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>