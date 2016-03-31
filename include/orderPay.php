<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("sale");
$arReturn = array();
$orderID = abs((int)$_GET["order"]);

if ($orderID) {
	$arFilter = array("ID" => $orderID);
	$db_sales = CSaleOrder::GetList(array(), array("ID" => $orderID));
	if ($ar_sales = $db_sales->Fetch()) {
		if ($ar_sales["PAYED"] == "Y") {
			$arReturn = array(
				"STATUS" => "ERROR",
				"MESSAGE" => "Ваш заказ №{$ar_sales["ID"]} уже оплачен"
			);
		}
		elseif ($ar_sales["STATUS_ID"] != "M") {
			$arReturn = array(
				"STATUS" => "ERROR",
				"MESSAGE" => "Ваш заказ №{$ar_sales["ID"]} ещё не подтвержден менеджером. Пожалуйста ожидайте подтверждения"
			);
		}
		elseif ($ar_sales["STATUS_ID"] == "M") {
			$arReturn = array(
				"STATUS" => "OK",
				"MESSAGE" => ""
			);
		}
	}
	else {
		$arReturn = array(
			"STATUS" => "ERROR",
			"MESSAGE" => "Заказ №$orderID не найден. Проверьте правильность ввода номера заказа"
		);
	}
}
else {
	$arReturn = array(
		"STATUS" => "ERROR",
		"MESSAGE" => "Номер заказа не введен или введен неверно"
	);
}
echo json_encode($arReturn);