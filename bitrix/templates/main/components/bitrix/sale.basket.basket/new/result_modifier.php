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

//подсчет габаритов посылки
$p_weight = 0;
$p_volume = 0;
foreach ($arResult["GRID"]["ROWS"] as $key => $arItem) {
    $PRODUCT_ID = $arItem["PRODUCT_ID"];
    $mxResult = CCatalogSku::GetProductInfo($PRODUCT_ID);
    if (is_array($mxResult))
        $PRODUCT_ID = $mxResult["ID"];

    $rsProduct = CCatalogProduct::GetList(
        array(),
        array("ID" => $PRODUCT_ID),
        false,
        false,
        array("WEIGHT", "WIDTH", "LENGTH", "HEIGHT")
    );
    if ($arProduct = $rsProduct->GetNext()) {
        $arResult["GRID"]["ROWS"][$key]["WEIGHT"] = (int)$arProduct["WEIGHT"] * $arItem["QUANTITY"];
        $arResult["GRID"]["ROWS"][$key]["WIDTH"] = (int)$arProduct["WIDTH"] * $arItem["QUANTITY"];
        $arResult["GRID"]["ROWS"][$key]["LENGTH"] = (int)$arProduct["LENGTH"] * $arItem["QUANTITY"];
        $arResult["GRID"]["ROWS"][$key]["HEIGHT"] = (int)$arProduct["HEIGHT"] * $arItem["QUANTITY"];

        $p_weight += (int)$arProduct["WEIGHT"] * $arItem["QUANTITY"];
        $p_volume += (int)$arProduct["WIDTH"]*(int)$arProduct["LENGTH"]*(int)$arProduct["HEIGHT"] * $arItem["QUANTITY"];
        //если параметры товара (вес, длина, ширина и высота) равны 0, то достаем эти параметры из свойств родительского раздела
        $rs = CIBlockElement::GetList(array(),array("ACTIVE"=>"Y","ID"=>$PRODUCT_ID),false,false,array("IBLOCK_ID", "IBLOCK_SECTION_ID"));
        if ($ar = $rs->GetNext()) {
            $sectID = $ar["IBLOCK_SECTION_ID"];
        }

        if (((int)$arProduct["WEIGHT"] == 0 || (int)$arProduct["WIDTH"] == 0 || (int)$arProduct["LENGTH"] == 0 || (int)$arProduct["HEIGHT"] == 0) && (int)$sectID > 0) {
            $rsSection = CIBlockSection::GetList(
                array(),
                array("IBLOCK_ID" => IBLOCK_PRODUCT_ID, "ACTIVE" => "Y", "ID" => $sectID),
                false,
                array("IBLOCK_ID", "ID", "UF_WEIGHT", "UF_WIDTH", "UF_LENGTH", "UF_HEIGHT")
            );
            if ($arSection = $rsSection->GetNext()) {
                $arResult["GRID"]["ROWS"][$key]["WEIGHT"] = (int)$arSection["UF_WEIGHT"];
                $arResult["GRID"]["ROWS"][$key]["WIDTH"] = (int)$arSection["UF_WIDTH"];
                $arResult["GRID"]["ROWS"][$key]["LENGTH"] = (int)$arSection["UF_LENGTH"];
                $arResult["GRID"]["ROWS"][$key]["HEIGHT"] = (int)$arSection["UF_HEIGHT"] * $arItem["QUANTITY"];

                $p_weight += (int)$arSection["UF_WEIGHT"] * $arItem["QUANTITY"];
                $p_volume += (int)$arSection["UF_WIDTH"]*(int)$arSection["UF_LENGTH"]*(int)$arSection["UF_HEIGHT"] * $arItem["QUANTITY"];
            }
        }
    }
}
$arResult["TOTAL_WEIGHT"] = $p_weight;
$arResult["VOLUME"] = $p_volume;
$arResult["TOTAL_WIDTH"] = $arResult["TOTAL_LENGTH"] = $arResult["TOTAL_HEIGHT"] = round(pow($p_volume, 1/3));
?>