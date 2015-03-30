<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Выгрузка на маркет. Удобное проставление флажков.");
?>

<?$APPLICATION->IncludeComponent(
	"dev:unload.market", 
	".default", 
	array(
		"IBLOCK_TYPE" => "catalogs",
		"IBLOCK_ID" => "1",
		"IBLOCK_TYPE_MAKER" => "reference",
		"IBLOCK_ID_MAKER" => "3"
	),
	false
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>