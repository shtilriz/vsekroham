<?
//подписка на рассылку
$arReturn = array();
if (isset($_GET["EMAIL"]) && filter_var($_GET["EMAIL"], FILTER_VALIDATE_EMAIL)) {
	//ключ доступа к API (из Личного Кабинета)
	$api_key = "5ac9165wjpajneq5qruwcyk9ot186fbk4co4qizo";

	// Данные о новом подписчике
	$email = $_GET["EMAIL"];
	$phone = $_GET["PHONE"];
	$company = $_GET["COMPANY"];
	$user_lists = "2796094";
	//$user_ip = "12.34.56.78";
	//$user_tag = urlencode("Added using API");

	// Создаём POST-запрос
	$POST = array (
		'api_key' => $api_key,
		'list_ids' => $user_lists,
		'fields[email]' => $email,
		'fields[Name]' => $company,
		'fields[phone]' => $phone,
		//'request_ip' => $user_ip,
		//'tags' => $user_tag
	);

	// Устанавливаем соединение
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $POST);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_URL, 'http://api.unisender.com/ru/api/subscribe?format=json');
	$result = curl_exec($ch);

	if ($result) {
		// Раскодируем ответ API-сервера
		$jsonObj = json_decode($result);

		if (null===$jsonObj) {
			// Ошибка в полученном ответе
			$arReturn = array(
				"SUCCESS" => "N",
				"TITLE" => "Ошибка",
				"MESSAGE" => "Возникла ошибка при подписке. Повторите попытку позже."
			);
		}
		elseif(!empty($jsonObj->error)) {
			// Ошибка добавления пользователя
			$arReturn = array(
				"SUCCESS" => "N",
				"TITLE" => "Ошибка",
				"MESSAGE" => "Возникла ошибка: ".$jsonObj->error
			);
			//echo "An error occured: " . $jsonObj->error . "(code: " . $jsonObj->code . ")";
		}
		else {
			// Новый пользователь успешно добавлен
			$arReturn = array(
				"SUCCESS" => "N",
				"TITLE" => "Подписка оформлена",
				"MESSAGE" => "Вы подписались на нашу рассылку. На ваш E-mail выслан код подтверждения. Пожалуйста, подтвердите подписку."
			);
			//echo "Added. ID is " . $jsonObj->result->person_id;
		}
	}
	else {
		// Ошибка соединения с API-сервером
		$arReturn = array(
			"SUCCESS" => "N",
			"TITLE" => "Ошибка",
			"MESSAGE" => "Возникла ошибка при подписке. Повторите попытку позже."
		);
	}
}
else {
	$arReturn = array(
		"SUCCESS" => "N",
		"TITLE" => "Ошибка",
		"MESSAGE" => "E-mail не введен или введен некорректно"
	);
}
echo json_encode($arReturn);
?>