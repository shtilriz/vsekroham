<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Информация об оплате");
?>

<h1><?$APPLICATION->ShowTitle();?></h1>

<?
$status = trim(strip_tags($_GET['status']));
if ($status == 'ok') {
	echo '<div class="pay-message">Заказ успешно оплачен и вскоре будет отправлен. Ожидайте, с вами обязательно свяжется наш менеджер.</div>';
}
elseif ($status == 'error') {
	echo '<div class="pay-message">При оплате заказа произошла ошибка. Повторите попытку позже. Если проблема сохранилась, позвоните по телефону 8 (800) 775-35-48.</div>';
}
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>