<?
define("IBLOCK_PRODUCT_ID", 1); //ID инфоблока каталога товаров
define("IBLOCK_SKU_ID", 2); //ID инфоблока торговых предложений

include($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/geobaza/geobaza.php");

if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/funcs.php")) {
	require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/php_interface/include/funcs.php");
}

if (is_file($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/logs/LogsDB.class.php")) {
	require $_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/logs/LogsDB.class.php";
	//$ib_logs = new LogsDB;
}

//запрос данных о пользователя, авторизованном через соцсети
if (isset($_POST["token"]) && !empty($_POST["token"])) {
	$s = file_get_contents('http://ulogin.ru/token.php?token='.$_POST['token'].'&host='.$_SERVER['HTTP_HOST']);
	session_start();
	$_SESSION["ULOGIN_USER"] = json_decode($s, true);
}

//обновляет параметры товара (габариты и вес) из свойств раздела
//AddEventHandler("iblock", "OnAfterIBlockSectionUpdate", array("MyEvents", "SizesUpdate"));
//обновляет параметры торговых предложений товара (габариты и вес) при изменении основного товара
//AddEventHandler("catalog", "OnBeforeProductUpdate", array("MyEvents", "SizesUpdateSKU"));
//Убирает флажок у товара "В наличии", если отсутствует цена или цена равна 0
AddEventHandler("catalog", "OnBeforePriceUpdate", array("MyEvents", "setAvailable"));
AddEventHandler("catalog", "OnPriceDelete", array("MyEvents", "setAvailableDel"));
AddEventHandler("catalog", "OnBeforePriceDelete", array("MyEvents", "setAvailableDel"));
//удаляет наценку у ТП, если удалена наценка у основного товара
AddEventHandler("catalog", "OnProductPriceDelete", array("MyEvents", "setMarginFromSKUdel"));

/***************logs**************/
AddEventHandler("iblock", "OnAfterIBlockElementUpdate", Array("MyEvents", "updateElement2Log")); //записывает в лог изменение элемента
AddEventHandler("iblock", "OnAfterIBlockElementAdd", Array("MyEvents", "addElement2Log")); //записывает в лог добавление элемента
AddEventHandler("iblock", "OnBeforeIBlockElementDelete", Array("MyEvents", "delElement2Log")); //записывает в лог удаление элемента
/***************end logs**************/

//прописывает в свойство заказа "Менеджер" логин пользователя, который изменил статус заказа
AddEventHandler("sale", "OnSaleStatusOrder", Array("MyEvents", "setManager2OrderStatus"));
AddEventHandler("sale", "OnSalePayOrder", Array("MyEvents", "setManager2OrderPay"));
AddEventHandler("sale", "OnSaleDeliveryOrder", Array("MyEvents", "setManager2OrderDelivery"));
AddEventHandler("sale", "OnSaleCancelOrder", Array("MyEvents", "setManager2OrderCancel"));
//добавляет екобходимые поля в почтовый шаблон перед отправкой письма
AddEventHandler("main", "OnBeforeEventAdd", Array("MyEvents", "setParamsEvent"));

class MyEvents {
	function SizesUpdate(&$arFields) {
		//если текущий раздел не содержит подразделов и разрешено обновить параметры товаров раздела
		if (CModule::IncludeModule("iblock") && CModule::IncludeModule("catalog") && $arFields["IBLOCK_ID"] == IBLOCK_PRODUCT_ID && $arFields["RESULT"] && !isSubsection($arFields) && $arFields["UF_SETPARAMS"]) {

			//если у раздела установлены габариты и вес
			$length = (intval($arFields["UF_LENGTH"]) > 0?intval($arFields["UF_LENGTH"]):0);
			$width = (intval($arFields["UF_WIDTH"]) > 0?intval($arFields["UF_WIDTH"]):0);
			$height = (intval($arFields["UF_HEIGHT"]) > 0?intval($arFields["UF_HEIGHT"]):0);
			$weight = (intval($arFields["UF_WEIGHT"]) > 0?intval($arFields["UF_WEIGHT"]):0);

			$arProductParams = array(
				"LENGTH" => $length,
				"WIDTH" => $width,
				"HEIGHT" => $height,
				"WEIGHT" => $weight
			);
			$rsProducts = CIBlockElement::GetList(
				array("ID" => "ASC"),
				array(
					"IBLOCK_ID" => $arFields["IBLOCK_ID"],
					"SECTION_ID" => $arFields["ID"]
				),
				false,
				false,
				array("IBLOCK_ID", "ID")
			);
			while ($arProduct = $rsProducts->GetNext()) {
				//установить параметры у основного товара
				CCatalogProduct::Update($arProduct["ID"], $arProductParams);
				$rsSKU = CIBlockElement::GetList(
					array("ID" => "ASC"),
					array(
						"IBLOCK_ID" => IBLOCK_SKU_ID,
						"PROPERTY_CML2_LINK" => $arProduct["ID"]
					),
					false,
					false,
					array("IBLOCK_ID", "ID")
				);
				while ($arSKU = $rsSKU->GetNext()) {
					//установить параметры у торговых предложений
					CCatalogProduct::Update($arSKU["ID"], $arProductParams);
				}
			}
		}
	}

	function SizesUpdateSKU ($ID, &$arFields) {
		if (CModule::IncludeModule("iblock") && CModule::IncludeModule("catalog")) {
			$rsProduct = CIBlockElement::GetByID($ID);
			if($arProduct = $rsProduct->GetNext()) {
				if ($arProduct["IBLOCK_ID"] == IBLOCK_PRODUCT_ID) {
					$arProductParams = array(
						"LENGTH" => $arFields["LENGTH"],
						"WIDTH" => $arFields["WIDTH"],
						"HEIGHT" => $arFields["HEIGHT"],
						"WEIGHT" => $arFields["WEIGHT"],
					);
					$rsSKU = CIBlockElement::GetList(
						array("ID" => "ASC"),
						array(
							"IBLOCK_ID" => IBLOCK_SKU_ID,
							"PROPERTY_CML2_LINK" => $ID
						),
						false,
						false,
						array("IBLOCK_ID", "ID")
					);
					while ($arSKU = $rsSKU->GetNext()) {
						//установить параметры у торговых предложений
						CCatalogProduct::Update($arSKU["ID"], $arProductParams);
					}
				}
			}
		}
	}

	function setAvailable ($ID, &$arFields) {
		if ((intval($arFields["PRICE"]) <= 0 || $arFields["PRICE"] == "") && CModule::IncludeModule("iblock")) {
			CIBlockElement::SetPropertyValuesEx($arFields["PRODUCT_ID"], false, array("AVAILABLE" => NULL));
		}
	}

	function setAvailableDel ($ID) {

		$arPrice = CPrice::GetByID($ID);
		if (0 < intval($arPrice["PRODUCT_ID"]) && CModule::IncludeModule("iblock")) {
			CIBlockElement::SetPropertyValuesEx($arPrice["PRODUCT_ID"], false, array("AVAILABLE" => NULL));
		}
	}

	function setMarginFromSKUdel ($ID, $arExceptionIDs) {
		if (!(CModule::IncludeModule("catalog") && CModule::IncludeModule("catalog")))
			return false;

		//если товар не содержит ТП (либо это простой товар, либо это само ТП), то вываливаемся из функции )
		$offersExist = CCatalogSKU::getExistOffers(array($ID));
		if (!$offersExist[$ID])
			return false;

		$bDelMarginPriceSKU = true; //флаг удалять или нет наценочную цену ТП
		foreach ($arExceptionIDs as $id) {
			$arPrice = CPrice::GetByID($id);
			//если среди цен основного товара есть наценочная цена, то удалять у ТП её не нужно
			if ($arPrice["CATALOG_GROUP_ID"] == 2) {
				$bDelMarginPriceSKU = false;
				break;
			}
		}
		if (!$bDelMarginPriceSKU)
			return false;

		//удалить наценочную цену ТП
		$arSelect = array("IBLOCK_ID", "ID");
		$rsSKU = CIBlockElement::GetList(
			array(),
			array(
				"IBLOCK_ID" => IBLOCK_SKU_ID,
				"=PROPERTY_CML2_LINK" => $ID
			),
			false,
			false,
			array("IBLOCK_ID", "ID")
		);
		while ($arSKU = $rsSKU->GetNext()) {
			$arExceptionIDs = array(); //массив цен, которые будут оставлены
			$db_res = CPrice::GetList(array(), array("PRODUCT_ID" => $arSKU["ID"]));
			while ($ar_res = $db_res->Fetch()) {
				if ($ar_res["CATALOG_GROUP_ID"] != 2)
					$arExceptionIDs[] = $ar_res["ID"];
			}
			CPrice::DeleteByProduct($arSKU["ID"], $arExceptionIDs);
		}
	}

	function updateElement2Log(&$arFields) {
		global $USER;
		if (in_array($arFields["IBLOCK_ID"], array(IBLOCK_PRODUCT_ID, IBLOCK_SKU_ID)) && $USER->IsAuthorized()) {
			$ib_logs = new LogsDB;
			$event = 'Изменение элемента инфоблока';
			$ip = MyEvents::getUserIP();
			$ib_logs->add($event, $ip, $USER->GetID(), $arFields);
			unset($ib_logs);
		}
	}
	function addElement2Log(&$arFields) {
		global $USER;
		if (in_array($arFields["IBLOCK_ID"], array(IBLOCK_PRODUCT_ID, IBLOCK_SKU_ID)) && $USER->IsAuthorized()) {
			$ib_logs = new LogsDB;
			$event = 'Добавление элемента инфоблока';
			$ip = MyEvents::getUserIP();
			$ib_logs->add($event, $ip, $USER->GetID(), $arFields);
			unset($ib_logs);
		}
	}
	function delElement2Log($ID) {
		global $USER;
		if (in_array($arFields["IBLOCK_ID"], array(IBLOCK_PRODUCT_ID, IBLOCK_SKU_ID)) && $USER->IsAuthorized()) {
			$ib_logs = new LogsDB;
			$event = 'Удаление элемента инфоблока';
			$ip = MyEvents::getUserIP();
			$ib_logs->add($event, $ip, $USER->GetID(), array("ID" => $ID));
			unset($ib_logs);
		}
	}
	private function getUserIP() {
		$ip = '';
		if ($_SERVER['HTTP_X_FORWARDED_FOR'])
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else
			$ip = $_SERVER["REMOTE_ADDR"];
		return $ip;
	}

	function setManager2OrderStatus($ORDER_ID, $val) {
		if (!CModule::IncludeModule("sale"))
			return false;
		if ($arOrder = CSaleOrder::GetByID($ORDER_ID))
			MyEvents::setManager2Order($arOrder["ID"], $arOrder["EMP_STATUS_ID"]);
	}
	function setManager2OrderPay($ORDER_ID, $val) {
		if (!CModule::IncludeModule("sale"))
			return false;
		if ($arOrder = CSaleOrder::GetByID($ORDER_ID))
			MyEvents::setManager2Order($arOrder["ID"], $arOrder["EMP_PAYED_ID"]);
	}
	function setManager2OrderDelivery($ORDER_ID, $val) {
		if (!CModule::IncludeModule("sale"))
			return false;
		if ($arOrder = CSaleOrder::GetByID($ORDER_ID))
			MyEvents::setManager2Order($arOrder["ID"], $arOrder["EMP_ALLOW_DELIVERY_ID"]);
	}
	function setManager2OrderCancel($ORDER_ID, $val) {
		if (!CModule::IncludeModule("sale"))
			return false;
		if ($arOrder = CSaleOrder::GetByID($ORDER_ID))
			MyEvents::setManager2Order($arOrder["ID"], $arOrder["EMP_CANCELED_ID"]);
	}
	private function setManager2Order($ORDER_ID, $USER_ID) {
		if (!CModule::IncludeModule("sale"))
			return false;
		$arUser = CUser::GetByID($USER_ID)->Fetch();
		$db_props = CSaleOrderProps::GetList(array(),array("PERSON_TYPE_ID"=>1,"PROPS_GROUP_ID"=>2,"CODE"=>"MANAGER"),false,false,array());
		if ($props = $db_props->Fetch()) {
			$arFields = array(
				"ORDER_ID" => $ORDER_ID,
				"ORDER_PROPS_ID" => $props["ID"],
				"NAME" => $props["NAME"],
				"CODE" => $props["CODE"],
				"VALUE" => $arUser["LOGIN"]
			);
			CSaleOrderPropsValue::Add($arFields);
		}
	}

	function setParamsEvent($event, $lid, &$arFields, $message_id) {
		if ((int)$arFields["ORDER_ID"] && CModule::IncludeModule("sale")) {
			switch ($event) {
				case "SALE_STATUS_CHANGED_D":
					$phoneID = 3; //ID свойства заказа "Телефон"
					$db_vals = CSaleOrderPropsValue::GetList(array(),array("ORDER_ID"=>$arFields["ORDER_ID"],"ORDER_PROPS_ID"=>$phoneID));
					if ($arVals = $db_vals->Fetch())
						$arFields["PHONE"] = $arVals["VALUE"];
					break;
				case "SALE_STATUS_CHANGED_P":
				case "SMS4B_SALE_STATUS_CHANGED_P":
					$arOrder = CSaleOrder::GetByID($arFields["ORDER_ID"]);
					$arFields["TRACKING_NUMBER"] = $arOrder["TRACKING_NUMBER"]?$arOrder["TRACKING_NUMBER"]:'';
					break;
			}

		}
	}
}

//пересчет цен ТП при обновлении цены товара с списке товаров.
AddEventHandler("iblock", "OnAfterIBlockElementUpdate", Array("PriceCalculate", "OnAfterIBlockElementUpdateHandler"));
class PriceCalculate
{
	function OnAfterIBlockElementUpdateHandler(&$arFields)
	{
		if (!function_exists('BXIBlockAfterSave') && CModule::IncludeModule("iblock")) {
			if ($arFields["IBLOCK_ID"] == IBLOCK_SKU_ID) {
				PriceCalculate::setSkuPrice($arFields["ID"], $arFields);
			}
			elseif ($arFields["IBLOCK_ID"] == IBLOCK_PRODUCT_ID) {
				$arSort = array();
				$arFilter = array("IBLOCK_ID"=>IBLOCK_SKU_ID,"ACTIVE"=>"Y","PROPERTY_CML2_LINK"=>$arFields["ID"]);
				$arSelect = array("IBLOCK_ID", "ID");
				$res = CIBlockElement::GetList($arSort, $arFilter, false, false, $arSelect);
				while ($arRes = $res->GetNext()) {
					PriceCalculate::setSkuPrice($arRes["ID"], $arFields);
				}
			}
		}
		//прописать названия ТП, как у основного товара
		if ($arFields["IBLOCK_ID"] == IBLOCK_PRODUCT_ID) {
			$rsSKU = CIBlockElement::GetList(
				array(),
				array(
					"IBLOCK_ID" => IBLOCK_SKU_ID,
					"PROPERTY_CML2_LINK" => $arFields["ID"]
				),
				false,
				false,
				array("IBLOCK_ID", "ID", "NAME")
			);
			while ($arSKU = $rsSKU->GetNext()) {
				if ($arSKU["NAME"] != $arFields["NAME"]) {
					$el = new CIBlockElement;
					$el->Update($arSKU["ID"], array("NAME"=>$arFields["NAME"]));
				}
			}
		}
	}

	function getBasePrice($elID) {
		$price = 0;
		if (CModule::IncludeModule("catalog") && intval($elID) > 0) {
			$arPrice = CPrice::GetBasePrice(intval($elID));
			$price = $arPrice["PRICE"];
			unset($arPrice);
		}
		return $price;
	}

	function setSkuPrice($elID, &$arFields) {
		if (CModule::IncludeModule("iblock") && CModule::IncludeModule("catalog") && intval($elID) > 0)
		{
			//узнать ID основного товара
			$BC = 0;

			if ($arFields["IBLOCK_ID"] == IBLOCK_PRODUCT_ID) {
				$BC = $_POST["CATALOG_PRICE"][$arFields["ID"]][1];
			}
			elseif ($arFields["IBLOCK_ID"] == IBLOCK_SKU_ID) {
				$mxResult = CCatalogSku::GetProductInfo(intval($elID));
				if (is_array($mxResult))
				{
					//достать цену основго товара
					$BC = PriceCalculate::getBasePrice($mxResult["ID"]);
				}
			}

			//достать БЦТП
			$BCTP = PriceCalculate::getBasePrice($elID);
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
			if ($arRes = $res->GetNext())
			{
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
}
?>