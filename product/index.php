<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Карточка товара");
if (!isset($_REQUEST["ELEMENT_CODE"]))
	LocalRedirect('/404.php');
?>

<?$APPLICATION->IncludeComponent("bitrix:breadcrumb", ".default", Array(
	"START_FROM" => "0",
		"PATH" => "",
		"SITE_ID" => "-",
	),
	false
);?>

<?$APPLICATION->IncludeComponent(
	"dev:catalog.element", 
	".default", 
	array(
		"IBLOCK_TYPE" => "catalogs",
		"IBLOCK_ID" => "1",
		"ELEMENT_ID" => "",
		"ELEMENT_CODE" => $_REQUEST["ELEMENT_CODE"],
		"SECTION_ID" => "",
		"SECTION_CODE" => $_REQUEST["SECTION_CODE"],
		"HIDE_NOT_AVAILABLE" => "N",
		"PROPERTY_CODE" => array(
			0 => "239",
			1 => "DEPTH_MEBEL",
			2 => "WIDTH_MEBEL",
			3 => "HEIGHT_MEBEL",
			4 => "CAR_SEAT",
			5 => "VOICE_ACTIVATION",
			6 => "NABOR",
			7 => "WEIGHT",
			8 => "VID_IGRA",
			9 => "CAMCORDER",
			10 => "TAB_MATTRESS",
			11 => "OUTSIDE_SIZE",
			12 => "INSIDE_SIZE",
			13 => "BOOSTER",
			14 => "IN_SOFA",
			15 => "AGE_ODEJDA",
			16 => "AGE_IGRA",
			17 => "OUTPUT_CHILD",
			18 => "DIMENSIONS",
			19 => "GABARITS_RAZLOJENNY",
			20 => "SIZE_COMPLECT",
			21 => "SIZE_STROLLER_SEAT",
			22 => "FRAME_UNSET",
			23 => "FRAME_SET",
			24 => "GABARITS_SLOJENNY",
			25 => "WARRANTY",
			26 => "GROUP",
			27 => "TWO_WAY",
			28 => "DEKOR_ODEJDA",
			29 => "DISPLAY",
			30 => "WEIGHT_CHILD",
			31 => "DOP_FUNCTIONS",
			32 => "WEIGTH_LOAD",
			33 => "BOARD_CHANGING",
			34 => "STIFFNESS",
			35 => "ZASTEJKA_ODEJDA",
			36 => "WINTER_SUMMER",
			37 => "GAUGE",
			38 => "WHEELS",
			39 => "OPTIONS_WHEELS",
			40 => "COUNTSOTDEL",
			41 => "COUNT_POS",
			42 => "COUNT_REGIMENT",
			43 => "NUM_PROGRAMS",
			44 => "QUANTITY_PACKAGE",
			45 => "CNT_BOXES",
			46 => "FOR_ROOM",
			47 => "LOCKER",
			48 => "FEATURES_LOCKER",
			49 => "OPTIONS",
			50 => "BASKET4BUY",
			51 => "FIXING",
			52 => "COT",
			53 => "CRADLE",
			54 => "MATERIAL_KOMOD",
			55 => "MATERIAL_ODEJDA",
			56 => "MATERIAL_IGRA",
			57 => "MATERIAL_PRODUCTION",
			58 => "MATERIAL_WHEELS",
			59 => "MAT_BASKET4BUY",
			60 => "PLATING_STROLLERS",
			61 => "MEHANIZM_VRACH",
			62 => "TILTING_MECHANIZM",
			63 => "MECHANIZM_FRAME",
			64 => "MUSIC_MODULE",
			65 => "NZ_IGRA",
			66 => "BACKREST_TILT",
			67 => "THE_CAPS",
			68 => "NIGHT_LIGHT",
			69 => "VOLUME",
			70 => "BASIS",
			71 => "FEATURES",
			72 => "REGIMENT",
			73 => "FRONT_WALL",
			74 => "SUITABLE_FOR",
			75 => "POL_ODEJDA",
			76 => "TIP_OFF",
			77 => "BEDSIDE_TABLE",
			78 => "MAKER",
			79 => "WORK_FROM",
			80 => "OP_FREQUENCY",
			81 => "ACTION_RADIUS",
			82 => "SIZE",
			83 => "SIZE_BED",
			84 => "SIZE_SLEEP",
			85 => "SIZE_CRADLE",
			86 => "LOCATION_CRADLES",
			87 => "ADJUST_SEAT",
			88 => "HEADREST",
			89 => "SEATBELTS",
			90 => "SEATBELTS2",
			91 => "SEAT_BELTS_SEAT",
			92 => "PARENT_HANDLE",
			93 => "CARRYING_HANDLE",
			94 => "HANDLE_BEFORE",
			95 => "SEASON_ODEJDA",
			96 => "CERT",
			97 => "SILICONE_PAD",
			98 => "COLIC",
			99 => "SUN_VIZOR",
			100 => "SOSTAV",
			101 => "RACK",
			102 => "STYLE_ODEJDA",
			103 => "TABLE",
			104 => "FEATURES_TABLE",
			105 => "TABLE_CHAIRS",
			106 => "FEATURES_CHAIRS",
			107 => "MATTRESS",
			108 => "TIMER",
			109 => "TIP_IGRA",
			110 => "TYPE_ARENA",
			111 => "TYPE_BOX",
			112 => "TRANSFORMERS",
			113 => "BACK_ANGLE",
			114 => "LEVEL_BED",
			115 => "INSTALL_CARSEAT",
			116 => "NIGHT_VISION",
			117 => "COLOR",
			118 => "WIDTH_FRAME",
			119 => "WARDROBE",
			120 => "FEATURES_WARDROBE",
			121 => "NECK",
			122 => "STORAGE_TRAY",
			123 => "TOY_BOX",
			124 => "BOXES",
			125 => "SIZE_IGRA",
			126 => "269",
			127 => "FEATURES_COT",
			128 => "",
		),
		"OFFERS_LIMIT" => "0",
		"TEMPLATE_THEME" => "",
		"DISPLAY_NAME" => "Y",
		"DETAIL_PICTURE_MODE" => "IMG",
		"ADD_DETAIL_TO_SLIDER" => "N",
		"DISPLAY_PREVIEW_TEXT_MODE" => "H",
		"PRODUCT_SUBSCRIPTION" => "N",
		"SHOW_DISCOUNT_PERCENT" => "N",
		"SHOW_OLD_PRICE" => "N",
		"SHOW_MAX_QUANTITY" => "N",
		"DISPLAY_COMPARE" => "N",
		"MESS_BTN_BUY" => "Купить",
		"MESS_BTN_ADD_TO_BASKET" => "В корзину",
		"MESS_BTN_SUBSCRIBE" => "Подписаться",
		"MESS_NOT_AVAILABLE" => "Нет в наличии",
		"USE_VOTE_RATING" => "N",
		"USE_COMMENTS" => "N",
		"BRAND_USE" => "N",
		"SECTION_URL" => "",
		"DETAIL_URL" => "",
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_GROUPS" => "Y",
		"SET_TITLE" => "Y",
		"SET_BROWSER_TITLE" => "Y",
		"BROWSER_TITLE" => "-",
		"SET_META_KEYWORDS" => "Y",
		"META_KEYWORDS" => "-",
		"SET_META_DESCRIPTION" => "Y",
		"META_DESCRIPTION" => "-",
		"SET_STATUS_404" => "Y",
		"ADD_SECTIONS_CHAIN" => "Y",
		"ADD_ELEMENT_CHAIN" => "Y",
		"USE_ELEMENT_COUNTER" => "Y",
		"PRICE_CODE" => array(
			0 => "BASE",
			1 => "MARGIN",
		),
		"USE_PRICE_COUNT" => "N",
		"SHOW_PRICE_COUNT" => "1",
		"PRICE_VAT_INCLUDE" => "Y",
		"PRICE_VAT_SHOW_VALUE" => "N",
		"CONVERT_CURRENCY" => "N",
		"BASKET_URL" => "/personal/basket.php",
		"ACTION_VARIABLE" => "action",
		"PRODUCT_ID_VARIABLE" => "id",
		"USE_PRODUCT_QUANTITY" => "N",
		"ADD_PROPERTIES_TO_BASKET" => "Y",
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"PARTIAL_PRODUCT_PROPERTIES" => "Y",
		"PRODUCT_PROPERTIES" => array(
		),
		"LINK_IBLOCK_TYPE" => "",
		"LINK_IBLOCK_ID" => "",
		"LINK_PROPERTY_SID" => "",
		"LINK_ELEMENTS_URL" => "link.php?PARENT_ELEMENT_ID=#ELEMENT_ID#",
		"OFFERS_FIELD_CODE" => array(
			0 => "ID",
			1 => "CODE",
			2 => "XML_ID",
			3 => "NAME",
			4 => "SORT",
			5 => "PREVIEW_TEXT",
			6 => "PREVIEW_PICTURE",
			7 => "",
		),
		"OFFERS_PROPERTY_CODE" => array(
			0 => "SIZE",
			1 => "COLOR",
			2 => "",
		),
		"OFFERS_SORT_FIELD" => "sort",
		"OFFERS_SORT_ORDER" => "asc",
		"OFFERS_SORT_FIELD2" => "id",
		"OFFERS_SORT_ORDER2" => "asc",
		"OFFERS_CART_PROPERTIES" => array(
			0 => "SIZE",
			1 => "COLOR",
		),
		"ADD_PICT_PROP" => "-",
		"LABEL_PROP" => "-",
		"OFFER_ADD_PICT_PROP" => "-",
		"OFFER_TREE_PROPS" => array(
			0 => "-",
		),
		"MESS_BTN_COMPARE" => "Сравнение",
		"PRODUCT_QUANTITY_VARIABLE" => "quantity",
		"CHECK_SECTION_ID_VARIABLE" => "N",
		"PAGE_REVIEWS_COUNT" => "10",
		"COMPONENT_TEMPLATE" => ".default"
	),
	false
);?>

<script type="text/javascript">
$(function() {
	//
		var _GET = window.location.search.substring(1).split("&");
		if (_GET) {
			for (var i=0; i<_GET.length; i++) {
				var getVar = _GET[i].split("=");
				if (getVar[0] == "PAGEN_1") {
					setTimeout(function() {
						$('a.item-tabs__link[href=#reviews]').click();
					}, 10);
					$('html, body').scrollTop($('.item-tabs').offset().top);
					//$('#moreLinkReview').click();
					break;
				}
			}
		}
		//
});
</script>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>