<?php
require $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php";

class API_1C {
	const IBLOCK_PRODUCT_ID = 1;	//инфоблок товаров
	const IBLOCK_SKU_ID = 2;		//инфоблок ТП
	const AVAILABLE_ID = 1;			//ID варианта свойства "В наличии"
	private static $arActions = array(
		'getrest',					//команда выгрузки остатков по товарам на сайт
	);
	private static $arResponseMsg = array(
		"0" => "OK",
		"1" => "неправильная команда",
		"2" => "пустая выборка данных",
		"3" => "товар не найден",
		"4" => "не указан id товара",
		"5" => "отсутствует файл выгрузки",
		"6" => "ошибка работы с файлом"
	);

	function __construct () {
		CModule::IncludeModule("iblock");
		CModule::IncludeModule("catalog");
	}

	//возвращает true, если модуль поддерживает данную команду и false, если нет
	public function isActionExists ($action) {
		return in_array($action, self::$arActions);
	}

	//возвращает сообщение об ошибке по её коду
	public function getResponseMsg ($code) {
		if (array_key_exists($code, self::$arResponseMsg))
			return self::$arResponseMsg[$code];
		else
			return 'неизвестная ошибка';
	}

	//возвращает ответ сервера по коду ошибки пришедшей команде
	public function getResponse ($code) {
		foreach ($_GET as $param => $action)
			$arReturn["cmd"][$param] = $action;
		$arReturn["code"] = $code;
		$arReturn["msg"] = self::getResponseMsg($code);
		return json_encode($arReturn);
	}

	//обновляет доступное количество $quantity у товара $id
	public function updateQuantity ($id, $quantity) {
		$arFields = array('QUANTITY' => $quantity);
		return CCatalogProduct::Update($id, $arFields);
	}

	//обновляет активность у товара $id
	public function updateActive ($id, $active) {
		if ($active == "Y" || $active == "N") {
			$el = new CIBlockElement;
			$arFields = array('ACTIVE' => $active);
			$el->Update($id, $arFields);
		}
		else
			return false;
	}

	//устанавливает/снимает флажок "В наличии" у товара
	public function setAvailability ($id, $bAvailable) {
		CIBlockElement::SetPropertyValuesEx($id, IBLOCK_PRODUCT_ID, array("AVAILABLE" => ($bAvailable?self::AVAILABLE_ID:'')));
	}

	//парсит XML файл с остатками и возвращает массив или false в случае неудачи
	public function parseXmlProductRest2Array ($xmlFile) {
		$dom = new DomDocument();
		if ($dom->load($xmlFile)) {
			$arReturn = array();
			$offers = $dom->getElementsByTagName('offer');
			foreach ($offers as $key => $arOffer) {
				if ($arOffer->nodeType != 1)
					continue;
				foreach ($arOffer->childNodes as $value) {
					if ($value->nodeType != 1)
						continue;
					$arReturn[$key][$value->nodeName] = $value->textContent;
				}
			}
			return $arReturn;
		}
		else
			return false;
	}

	//возвращает массив - информацию о наличии товаров $arIDs и их доступном количестве из БД
	public function getInfoProductsFromRest ($arIDs) {
		$arReturn = array();
		foreach ($arIDs as $productID) {
			$rsProducts = CCatalogProduct::GetList(
				array(),
				array("ID" => $productID),
				false,
				false,
				array("ID", "QUANTITY")
			);
			while ($arProduct = $rsProducts->GetNext()) {
				$res = CIBlockElement::GetProperty(self::IBLOCK_PRODUCT_ID, $arProduct["ID"], array(), array("CODE" => "AVAILABLE"));
				$arProp = $res->Fetch();
				$arReturn[$arProduct["ID"]] = array(
					"AVAILABLE" => $arProp["VALUE"]?true:false,
					"QUANTITY" => $arProduct["QUANTITY"]
				);
			}
		}
		return $arReturn;
	}
}

//проверяет, существует ли допустимая команды а API
if (isset($_GET["action"])) {
	if (!API_1C::isActionExists($_GET["action"]))
		echo API_1C::getResponse('1');
}

//обновляет остатки из XML файла
if (isset($_GET["action"]) && $_GET["action"] == "getrest") {
	file_put_contents($_SERVER["DOCUMENT_ROOT"]."/upload/files.log", date('d.m.Y H:i')."\n".print_r($_FILES, true).print_r($_POST, true)."\n", FILE_APPEND);
	if (!empty($_FILES) && isset($_FILES)) {
		$arFile = reset($_FILES);
		$uploaddir = $_SERVER["DOCUMENT_ROOT"]."/upload/1c/";
		$uploadfile = $uploaddir.basename($arFile['name']);
		if (file_exists($uploadfile))
			unlink($uploadfile);
		if (move_uploaded_file($arFile["tmp_name"], $uploadfile)) {
			if (file_exists($uploadfile)) {
				$api1c = new API_1C;
				$ar = $api1c->parseXmlProductRest2Array($uploadfile);
				$arProducts = array();		//массив с простыми товарами
				$arProductsSku = array();	//массив c торговыми предложениями
				$arIDs = array();			//массив всех выгружаемых товаров и ТП
				foreach ($ar as $key => $arOffer) {
					$arTemp = explode("-", $arOffer["site_id"]);
					if (count($arTemp) == 1) {
						$arProducts[(int)$arTemp[0]] = array(
							"rest" => (int)$arOffer["rest"],
							"price" => (int)$arOffer["price"]
						);
					}
					elseif (count($arTemp) == 2) {
						$arProductsSku[$arTemp[0]]["offers"][(int)$arTemp[1]] = array(
							"rest" => (int)$arOffer["rest"],
							"price" => (int)$arOffer["price"]
						);
					}
				}
				//деактивировать товар, если все его ТП имеют нулевые остатки
				foreach ($arProductsSku as $productID => $arItem) {
					$bAvailable = false;
					$arIDs[] = $productID;
					foreach ($arItem["offers"] as $offerID => $arOffer) {
						$arIDs[] = $offerID;
						if ($arOffer["rest"] > 0)
							$bAvailable = true;
					}
					$arProductsSku[$productID]["AVAILABLE"] = $bAvailable;
				}

				if (!empty($arIDs)) {
					//получить информацию из базы по всем выгружаемым ID товаров, чтобы потом перезаписывать только изменившиеся данные
					if ($arCurrentProducts = $api1c->getInfoProductsFromRest($arIDs)) {
						//обновить свойство "В наличии" товаров и остатки у ТП у товаров с ТП
						foreach ($arProductsSku as $productID => $arItem) {
							if (array_key_exists($productID, $arCurrentProducts) && $arItem["AVAILABLE"] !== $arCurrentProducts[$productID]["AVAILABLE"]) {
								//обновить информацию о наличии товара
								$api1c->setAvailability($productID, $arItem["AVAILABLE"]);
							}
							foreach ($arItem["offers"] as $offerID => $arOffer) {
								if (array_key_exists($offerID, $arCurrentProducts) && $arOffer["rest"] != $arCurrentProducts[$offerID]["QUANTITY"]) {
									$api1c->updateActive($offerID, ($arOffer["rest"]>0?"Y":"N"));
									$api1c->updateQuantity($offerID, $arOffer["rest"]);
								}
							}
						}
						//обновить свойство "в наличии" и остатки у простых товаров
						foreach ($arProducts as $productID => $arItem) {
							if (array_key_exists($productID, $arCurrentProducts)) {
								//обновить информацию о наличии товара
								if ($arItem["rest"] != $arCurrentProducts[$productID]["QUANTITY"])
									$api1c->updateQuantity($productID, $arItem["rest"]);

								if ($arItem["rest"] < 1 && $arCurrentProducts[$productID]["AVAILABLE"])
									$api1c->setAvailability($productID, false);
								elseif ($arItem["rest"] > 0 && !$arCurrentProducts[$productID]["AVAILABLE"])
									$api1c->setAvailability($productID, true);
							}
						}
						echo API_1C::getResponse('0');
					}
					else
						echo API_1C::getResponse('2');
				}
				else
					echo API_1C::getResponse('2');
			}
			else
				echo API_1C::getResponse('6');
		}
		else
			echo API_1C::getResponse('6');
	}
	elseif (empty($_FILES))
		echo API_1C::getResponse('5');
	else
		echo API_1C::getResponse('-1');
}

/*if (isset($_GET["action"]) && $_GET["action"] == "updateOlds") {
	$api1c = new API_1C;
	$arOld = $api1c->parseXmlProductRest2Array($_SERVER["DOCUMENT_ROOT"].'/upload/1c/ProductsRestOld.xml');
	$arNew = $api1c->parseXmlProductRest2Array($_SERVER["DOCUMENT_ROOT"].'/upload/1c/ProductsRest.xml');
	$arIDsOld = array();
	$arIDsNew = array();
	foreach ($arOld as $key => $arItem) {
		$arTemp = explode("-", $arItem["site_id"]);
		if (count($arTemp) == 2 && !in_array($arTemp[0], $arIDsOld))
			$arIDsOld[] = $arTemp[0];
	}
	foreach ($arNew as $key => $arItem) {
		$arTemp = explode("-", $arItem["site_id"]);
		if (count($arTemp) == 2 && !in_array($arTemp[0], $arIDsNew))
			$arIDsNew[] = $arTemp[0];
	}
	$arResult = array_diff($arIDsOld, $arIDsNew);

	foreach ($arResult as $productID) {
		$api1c->setAvailability($productID, true);
		$rsSku = CIBlockElement::GetList(
			array("SORT" => "ASC"),
			array(
				"IBLOCK_ID" => IBLOCK_SKU_ID,
				"ACTIVE" => "N",
				"PROPERTY_CML2_LINK" => $productID
			),
			false,
			false,
			array("ID")
		);
		while ($arSku = $rsSku->GetNext()) {
			$api1c->updateActive($arSku["ID"], "Y");
		}
	}
}*/
?>