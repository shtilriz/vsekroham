<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?
$arReturn = array();
if (strlen($_GET["PHONE"]) > 0) {
	$arEventFields = array(
		"PHONE" => $_GET["PHONE"],
		"URL" => $_GET["url"],
		"DATETIME" => date('d.m.Y H:i')
	);
	$eID = CEvent::Send("CALLBACK", SITE_ID, $arEventFields);
	if ($eID > 0) {
		$arReturn = array(
			"SUCCESS" => "Y",
			"TITLE" => "Сообщение",
			"MESSAGE" => "Заявка на обратный звонок успешно подана. Ожидайте, вскоре с вами свяжется наш менеджер."
		);
	}
	else {
		$arReturn = array(
			"SUCCESS" => "N",
			"TITLE" => "Ошибка",
			"MESSAGE" => "Возникла ошибка при отправке заявки на обратный звонок. Повторите попытку позже."
		);
	}
}
else {
	$arReturn = array(
		"SUCCESS" => "N",
		"TITLE" => "Ошибка",
		"MESSAGE" => "Не указан номер телефона"
	);
}
echo json_encode($arReturn);
?>