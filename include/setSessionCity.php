<?
mb_internal_encoding("UTF-8");
function mb_ucfirst($text) {
    return mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1);
}
if (isset($_REQUEST["city"])) {
	session_start();
	$_SESSION["YOUR_CITY"] = mb_ucfirst(trim(strip_tags($_REQUEST["city"])));
}
?>
