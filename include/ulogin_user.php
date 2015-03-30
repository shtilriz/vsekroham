<?php
session_start();
$arReturn = array();
if (isset($_SESSION["ULOGIN_USER"]) && !empty($_SESSION["ULOGIN_USER"])) {
	if (isset($_SESSION["REVIEW_FORM"]))
	    $arReturn = array_merge($_SESSION["ULOGIN_USER"],$_SESSION["REVIEW_FORM"]);
	else
		$arReturn = $_SESSION["ULOGIN_USER"];
}
echo json_encode($arReturn);
?>