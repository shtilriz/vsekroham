<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?
$arReturn = array();
if (strlen($_REQUEST["FIO"]) > 0 && strlen($_REQUEST["PHONE"]) > 0) {
	$arEventFields = array(
		"FIO" => $_REQUEST["FIO"],
		"PHONE" => $_REQUEST["PHONE"]
	);

	$eventID = CEvent::Send("COLLECTOR","s1",array_merge($arEventFields));
	if ($eventID > 0) {
		$arReturn = array(
			"SUCCESS" => "Y",
			"TITLE" => "Заявка принята",
			"MESSAGE" => "Спасибо! В ближайшее время с вами свяжется наш менеджер для уточнения данных."
		);
	}
	else {
		$arReturn = array(
			"SUCCESS" => "N",
			"TITLE" => "Ошибка",
			"MESSAGE" => "Возникла ошибка при отправке заявки. Повторите попытку позже."
		);
	}
}
else {
	$arReturn = array(
		"SUCCESS" => "N",
		"TITLE" => "Ошибка",
		"MESSAGE" => "Возникла ошибка. Не все поля заполнены."
	);
}
echo json_encode($arReturn);
?>