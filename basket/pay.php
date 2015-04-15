<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$secret_seed = "a1z34F53E2zRWz";
$id = $_POST['id'];
$sum = $_POST['sum'];
$clientid = $_POST['clientid'];
$orderid = $_POST['orderid'];
$key = $_POST['key'];

if ($key != md5 ($id . sprintf ("%.2lf", $sum).$clientid.$orderid.$secret_seed)) {
	echo "Error! Hash mismatch";
	exit;
}
else {
	if ($orderid == "") {
		//Платёж – пополнение счёта, нужно зачислить деньги на баланс $clientid
	}
	else {
		if (CModule::IncludeModule("sale")) {
			CSaleOrder::PayOrder($orderid, "Y", false, false, 0, array());
			CSaleOrder::StatusOrder($orderid, "S");
		}
	}
	echo "OK ".md5($id.$secret_seed);
}
?>