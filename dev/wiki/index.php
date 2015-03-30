<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("wiki");
?>

<?$APPLICATION->IncludeComponent(
	"bitrix:wiki",
	"",
	Array(
		"IBLOCK_TYPE" => "reference",
		"IBLOCK_ID" => "8",
		"ELEMENT_NAME" => $_REQUEST["title"],
		"PATH_TO_USER" => "",
		"SEF_MODE" => "N",
		"VARIABLE_ALIASES" => Array("wiki_name"=>"wiki_name","oper"=>"oper"),
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"SHOW_RATING" => "",
		"RATING_TYPE" => "",
		"NAV_ITEM" => "",
		"SET_TITLE" => "Y",
		"SET_STATUS_404" => "N",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"ADD_SECTIONS_CHAIN" => "N",
		"USE_REVIEW" => "N"
	)
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>