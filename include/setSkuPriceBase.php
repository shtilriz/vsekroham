<?

/*
ТП - торговое предложение
БЦ - базовая цена основного товара
БЦТП - базовая цена торгового предложения
ТНТП - торговая наценка торгового предложения.
*/

//обновляет цену ТП
function BXIBlockAfterSave(&$arFields) {
	if (CModule::IncludeModule("iblock")) {
		$resEl = CIBlockElement::GetByID($arFields["ID"]);
		if($ar_res = $resEl->GetNext()) {
			if ($ar_res["IBLOCK_ID"] == IBLOCK_SKU_ID) {
				setSkuPrice::Update($arFields["ID"]);
			}
			elseif ($ar_res["IBLOCK_ID"] == IBLOCK_PRODUCT_ID) {
				$price_el = setSkuPrice::getBasePrice($arFields["ID"]);
				if (intval($price_el) <= 0) {
					CIBlockElement::SetPropertyValuesEx($arFields["ID"], false, array("AVAILABLE" => NULL));
				}
				$arSort = array();
				$arFilter = array("IBLOCK_ID"=>IBLOCK_SKU_ID,"PROPERTY_CML2_LINK"=>$arFields["ID"]);
				$arSelect = array("IBLOCK_ID", "ID");
				$res = CIBlockElement::GetList($arSort, $arFilter, false, false, $arSelect);
				while ($arRes = $res->GetNext()) {
					setSkuPrice::Update($arRes["ID"]);
					//обновить так же тип цен с наценкой
					setSkuPrice::UpdateMargin($arRes["ID"]);
				}
			}
		}
	}
}

class setSkuPrice
{
	//обновляем базовую цену торговго предложения
	function Update($elID) {
		if (CModule::IncludeModule("iblock") && CModule::IncludeModule("catalog") && intval($elID) > 0) {
			//узнать ID основного товара
			$BC = 0;
			$mxResult = CCatalogSku::GetProductInfo(intval($elID));
			if (is_array($mxResult)) {
				//достать цену основго товара
				$BC = setSkuPrice::getBasePrice($mxResult["ID"]);
			}
			//достать БЦТП
			$BCTP = setSkuPrice::getBasePrice($elID);
			if (!$BCTP)
				$BCTP = 0;
			//достать ТНТП - хранится в свойстве ТП
			$TNTP = 0;
			$res = CIBlockElement::GetList(
				array(),
				array(
					"IBLOCK_ID" => IBLOCK_SKU_ID,
					"ID" => $elID,
					"ACTIVE" => "Y"
				),
				false,
				false,
				array("IBLOCK_ID", "ID", "PROPERTY_MARGIN")
			);
			if ($arRes = $res->GetNext()) {
				$TNTP = $arRes["PROPERTY_MARGIN_VALUE"];
			}

			$new_BCTP = 0;
			$bUpdate = true; //флаг того, нужно ли обновлять цену ТП

			//Если заполнено БЦ и ТНТП, то БЦТП = БЦ + ТНТП.
			if ($BC && $TNTP || $BC && $TNTP == 0) {
				$new_BCTP = $BC + $TNTP;
				$bUpdate = true;
			}
			//Если заполнено БЦ и НЕ заполнены БЦТП и ТНТП, тогда БЦТП = БЦ
			elseif ($BC && !$BCTP && !$TNTP) {
				$new_BCTP = $BC;
				$bUpdate = true;
			}
			else {
				$bUpdate = false;
			}

			//если разрешено обновить цены
			if ($bUpdate) {
				CPrice::SetBasePrice(
					$elID,
					$new_BCTP,
					"RUB",
					false,
					false
				);
			}
		}
	}

	//обновить тип цены с наценкой
	function UpdateMargin($elID) {
		//узнать ID основного товара
		global $USER;
		$mxResult = CCatalogSku::GetProductInfo(intval($elID));
		if (is_array($mxResult)) {
			//достать цену с наценкой
			$arMarginPrice = setSkuPrice::getMarginPrice($mxResult["ID"]);
			if ($arMarginPrice) {
				$arFields = array(
					"PRODUCT_ID" => $elID,
					"CATALOG_GROUP_ID" => $arMarginPrice["CATALOG_GROUP_ID"],
					"CURRENCY" => $arMarginPrice["CURRENCY"],
					"EXTRA_ID" => $arMarginPrice["EXTRA_ID"]?$arMarginPrice["EXTRA_ID"]:0,
					"QUANTITY_FROM" => $arMarginPrice["QUANTITY_FROM"],
					"QUANTITY_TO" => $arMarginPrice["QUANTITY_TO"],
				);
				if (!$arFields["EXTRA_ID"])
					$arFields["PRICE"] = $arMarginPrice["PRICE"];

				$res = CPrice::GetList(
					array(),
					array(
						"PRODUCT_ID" => $arFields["PRODUCT_ID"],
						"CATALOG_GROUP_ID" => $arFields["CATALOG_GROUP_ID"]
					)
				);
				if ($arr = $res->Fetch()) {
					CPrice::Update($arr["ID"], $arFields, ($arFields["EXTRA_ID"]?true:false));
				}
				else {
					CPrice::Add($arFields, ($arFields["EXTRA_ID"]?true:false));
				}
			}
		}
	}

	//достаёт базовую цену товара по его ID
	function getBasePrice($elID) {
		$price = 0;
		if (CModule::IncludeModule("catalog") && intval($elID) > 0) {
			$arPrice = CPrice::GetBasePrice(intval($elID));
			$price = $arPrice["PRICE"];
			unset($arPrice);
		}
		return $price;
	}

	function getMarginPrice($elID) {
		$dbProductPrice = CPrice::GetListEx(
			array(),
			array("PRODUCT_ID" => $elID, "CATALOG_GROUP_ID" => 2),
			false,
			false,
			array("ID", "CATALOG_GROUP_ID", "EXTRA_ID", "PRICE", "CURRENCY", "QUANTITY_FROM", "QUANTITY_TO")
		);
		if ($arProductPrice = $dbProductPrice->Fetch())
			return $arProductPrice;
		else
			return false;
	}

}
?>
