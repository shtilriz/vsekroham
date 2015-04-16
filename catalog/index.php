<?
if ($_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest') {
	include($_SERVER["DOCUMENT_ROOT"].'/include/catalog-section.php');
}
else {
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Каталог товаров");
?>


<?$APPLICATION->ShowViewContent("heading");?>

<?$APPLICATION->IncludeComponent(
	"dev:maker.links",
	".default",
	array(
		"IBLOCK_TYPE" => "catalogs",
		"IBLOCK_ID" => "4",
		"IBLOCK_ID_CATALOG" => "1",
		"SECTION_ID" => "",
		"SECTION_CODE" => $_REQUEST["SECTION_CODE"],
		"FOLDER" => "/makers/",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600"
	),
	$component
);?>

<?
include($_SERVER["DOCUMENT_ROOT"].'/include/catalog-sort.php');
$GLOBALS["arrFilter"]["!PROPERTY_AVAILABLE"] = false;
$GLOBALS["arrFilter"]["!CATALOG_PRICE_1"] = false;
include($_SERVER["DOCUMENT_ROOT"].'/include/catalog-section.php');
?>

<script type="text/javascript">
$(function() {
	var _GET = window.location.search.substring(1).split("&");
	if (_GET) {
		for (var i=0; i<_GET.length; i++) {
			var getVar = _GET[i].split("=");
			if (getVar[0] == "PAGEN_1") {
				$('html, body').scrollTop($('#content .page-wrapper').offset().top);
				break;
			}
		}
	}
});
</script>
<?}?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>