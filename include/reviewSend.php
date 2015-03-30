<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?
CModule::IncludeModule("iblock");
//очищаем временные данные после отправки формы
unset($_SESSION["REVIEW_FORM"]);
//сохраняет отзыв в базу данных
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

$arReturn = array();
if (CModule::IncludeModule("highloadblock") && isset($_POST["UF_PRODUCT"]) && intval($_POST["UF_PRODUCT"]) > 0 && strlen($_POST["UF_COMMENT"]) > 0) {
	$hlblock = HL\HighloadBlockTable::getById(6)->fetch();
	$entity = HL\HighloadBlockTable::compileEntity($hlblock);
	$entity_data_class = $entity->getDataClass();

	$yourCity = ($_SESSION["YOUR_CITY"]?$_SESSION["YOUR_CITY"]:getYourCity());
	$arFields = array(
		"UF_ACTIVE" => false,
		"UF_PRODUCT" => $_POST["UF_PRODUCT"],
		"UF_NAME" => $_POST["UF_NAME"],
		"UF_EMAIL" => $_POST["UF_EMAIL"],
		"UF_DATE" => date("d.m.Y"),
		"UF_RATE" => $_POST["UF_RATE"],
		"UF_WORTH" => $_POST["UF_WORTH"],
		"UF_LACK" => $_POST["UF_LACK"],
		"UF_COMMENT" => $_POST["UF_COMMENT"],
		"UF_SERVICE" => "vsekroham",
		"UF_CITY" => $yourCity
	);
	//если в сессии есть данные авторизации с соцсети, то тянем аватар
	if (isset($_SESSION["ULOGIN_USER"]["photo"])) {
		$arFields["UF_AVATAR"] = CFile::MakeFileArray($_SESSION["ULOGIN_USER"]["photo"]);
	}
	if (isset($_SESSION["ULOGIN_USER"]["profile"])) {
		$arFields["UF_PROFILE_LINK"] = $_SESSION["ULOGIN_USER"]["profile"];
	}
	$result = $entity_data_class::add($arFields);

	$ID = $result->getId();

	if ($result->isSuccess()) {
		$arReturn = array(
			"SUCCESS" => "Y",
			"TITLE" => "Отзыв принят",
			"MESSAGE" => "Спасибо за отзыв! Текст отзыва будет опубликован на сайте сразу после проверки модератором."
		);
		//отправить почтовое уведомление о новом отзыве
		if ($ID > 0) {
			$productName = '';
			$rsProduct = CIBlockElement::GetList(array(),array("ID" => $_POST["UF_PRODUCT"],"ACTIVE" => "Y"),false,false,array("IBLOCK_ID", "ID", "NAME"));
			if ($arProduct = $rsProduct->GetNext()) {
				$productName = $arProduct["NAME"];
			}
			$arEventFields = array(
				"REVIEW_ID" => $ID,
				"PRODUCT_NAME" => $productName
			);
			CEvent::SendImmediate("NEW_REVIEW", SITE_ID, array_merge($arEventFields, $arFields));
		}
	}
	else {
		$arReturn = array(
			"SUCCESS" => "N",
			"TITLE" => "Ошибка",
			"MESSAGE" => "Возникла ошибка при отправке отзыва. Повторите попытку позже."
		);
	}
}
else {
	$arReturn = array(
		"SUCCESS" => "N",
		"TITLE" => "Ошибка",
		"MESSAGE" => "Не все данные указаны"
	);
}
echo json_encode($arReturn);
?>