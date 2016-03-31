<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Акции");
?>

<?$APPLICATION->IncludeComponent(
	"dev:banners.main.list", 
	"sales", 
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"BLOCK_ID" => "5",
		"DETAIL_URL" => "",
		"SORT_BY" => "UF_SORT",
		"SORT_ORDER" => "ASC"
	),
	false
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>