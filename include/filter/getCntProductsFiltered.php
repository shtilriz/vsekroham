<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?
require_once($_SERVER["DOCUMENT_ROOT"].'/include/setFilter.php');
/**
 * Скрипт возвращает количество товаров по фильтру
 */

if (!function_exists('products_txt')) {
	function products_txt($col) {
		$str = array('товар', 'товара', 'товаров');
		$num = intval($col);
		$s='';
		if($num>19)
			$num = $num - floor($num/10)*10;
		switch ($num) {
			case 1:
				$s = $str[0]; break;
			case $num < 5:
				$s = $str[1]; break;
			default:
				$s = $str[2];
		}
		return $s;
	}
}

$arFilter = array(
	"IBLOCK_ID" => IBLOCK_PRODUCT_ID,
	"IBLOCK_LID" => SITE_ID,
	"IBLOCK_ACTIVE" => "Y",
	"ACTIVE_DATE" => "Y",
	"ACTIVE" => "Y",
	"CHECK_PERMISSIONS" => "Y",
	"MIN_PERMISSION" => "R",
	"INCLUDE_SUBSECTIONS" => "Y",
	"SECTION_ID" => (int)$_GET["SECTION_ID"],
	"!PROPERTY_AVAILABLE" => false,
	"!CATALOG_PRICE_1" => false
);

$arPriceFilter = array();
foreach($GLOBALS["arrFilter"] as $key => $value)
{
	if(preg_match('/^(>=|<=|><)CATALOG_PRICE_/', $key))
	{
		$arPriceFilter[$key] = $value;
	}
}
if(!empty($arPriceFilter)) {
	$arSubFilter = $arPriceFilter;
	$arFilter[] = array(
		"LOGIC" => "OR",
		array($arPriceFilter),
		"=ID" => CIBlockElement::SubQuery("PROPERTY_1", $arSubFilter),
	);
}

$arAllFilter = array_merge($GLOBALS["arrFilter"], $arFilter);

$cnt = IBlockHelper::getCntProductsFiltered($arAllFilter);
$arReturn = array(
	"CNT" => $cnt,
	"TXT" => products_txt($cnt)
);
echo json_encode($arReturn);
?>