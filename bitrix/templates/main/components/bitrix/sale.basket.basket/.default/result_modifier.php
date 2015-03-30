<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
foreach ($arResult["GRID"]["ROWS"] as $k => $arItem) {
    $res = CPrice::GetList(
        array(),
        array(
            "PRODUCT_ID" => $arItem["PRODUCT_ID"],
            "CATALOG_GROUP_ID" => 2
        )
    );
    if ($arr = $res->Fetch()) {
        $arResult["GRID"]["ROWS"][$k]["PRICE_MARGIN"] = $arr;
    }
}
?>
