<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Габариты и вес товаров");
?><?$APPLICATION->IncludeComponent(
	"dev:product.params.edit",
	"",
	Array(
		"IBLOCK_TYPE" => "catalogs",
		"IBLOCK_ID" => "1",
		"IBLOCK_TYPE_MAKER" => "reference",
		"IBLOCK_ID_MAKER" => "3"
	)
);?><br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>