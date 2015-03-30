<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Оплата заказа");
?>

<h1><?$APPLICATION->ShowTitle();?></h1>

<?
CModule::IncludeModule("sale");
$bCorrectPayment = True;
if (!($arOrder = CSaleOrder::GetByID(IntVal(38))))
	$bCorrectPayment = False;

$dbPaySysAction = CSalePaySystemAction::GetList(
	array(),
	array(
			"PAY_SYSTEM_ID" => 3,
			"PERSON_TYPE_ID" => 1
		),
	false,
	false,
	array("NAME", "ACTION_FILE", "NEW_WINDOW", "PARAMS", "ENCODING", "LOGOTIP")
);
if ($arPaySysAction = $dbPaySysAction->Fetch())
{
	if ($bCorrectPayment)
		CSalePaySystemAction::InitParamArrays($arOrder, $arOrder["ID"], $arPaySysAction["PARAMS"]);
}
?>
<?include($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/sale/payment/yandex_3x/payment.php');?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>