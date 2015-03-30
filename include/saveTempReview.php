<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	session_start();
	$_SESSION["REVIEW_FORM"] = $_POST;
}
?>