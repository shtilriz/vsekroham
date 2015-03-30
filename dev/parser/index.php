<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Парсер отзывов с Яндекс.Маркета и Товары@Мейл.ru");
?><?$APPLICATION->IncludeComponent(
	"dev:parser.reviews", 
	".default", 
	array(
		"IBLOCK_TYPE_CATALOG" => "catalogs",
		"IBLOCK_ID_CATALOG" => "1",
		"BLOCK_ID" => "6",
		"PRODUCT" => "UF_PRODUCT",
		"SERVICE" => "UF_SERVICE",
		"RATING" => "UF_RATE",
		"WORTH" => "UF_WORTH",
		"LIMITATIONS" => "UF_LACK",
		"COMMENT" => "UF_COMMENT",
		"LINK_INPUT_NAME" => "link",
		"LIKE" => "UF_LIKE",
		"DIZLIKE" => "UF_DIZLIKE"
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>