<?php
require $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php";
set_time_limit(0);

$file = $_SERVER["DOCUMENT_ROOT"]."/upload/log1c.txt";
$logStr = 'api1c|';
if ($_SERVER['HTTP_X_FORWARDED_FOR']) {
	$logStr .= $_SERVER['HTTP_X_FORWARDED_FOR'];
}
else {
	$logStr .= $_SERVER["REMOTE_ADDR"];
}
$logStr .= "|".implode("|", $_GET)."|".date('d.m.Y H:i')."\n";
file_put_contents($file, $logStr, FILE_APPEND);

class API_1C {
	const IBLOCK_PRODUCT_ID = 1;		//инфоблок товаров
	const IBLOCK_SKU_ID = 2;			//инфоблок ТП
	const AVAILABLE_ID = 1;				//ID варианта свойства "В наличии"
	const UPLOAD_DIR = "/upload/1c/";	//Директория для загрузки файлов синхронизации
	const SECTION_1C_ID = 232;			//ID раздела для загрузки новых товаров

	private static $arActions = array(
		//'getrest',					//команда выгрузки остатков по товарам на сайт и обновления флага активности
		'updrest',						//команда выгрузки остатков по товарам на сайт
		'updprice',						//команда обновления цен
		'setactive',					//команда, по которой активируются все товары, находящиеся в файле выгрузки
		'setunactive',					//команда, по которой деактивируются все товары, находящиеся в файле выгрузки
		'newoffer',						//добавить новые товары на сайт
		'setcode',						//обновить коды из 1С у товаров на сайте
		'delete',						//удалить товары
		'getmodifylist',				//выгружает товары, которые были изменены за последние 2 часа
		'getimage',						//возвращает url картинки по внешнему коду товара
		'getdopimages'					//возвращает url дополнительных картинок товара по внешнему коду
	);
	private static $arResponseMsg = array(
		"0" => "OK",
		"1" => "неправильная команда",
		"2" => "пустая выборка данных",
		"3" => "товар не найден",
		"4" => "не указан id товара",
		"5" => "отсутствует файл выгрузки",
		"6" => "ошибка работы с файлом",
		"7" => "все товары в данной выгрузке уже добавлены на сайт"
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

	//возвращает ответ сервера в формате XML по коду ошибки пришедшей команде
	public function getResponseXml ($code) {
		$dom = new DomDocument("1.0", "utf-8");
		$dom->formatOutput = true;
		$dom->preserveWhiteSpace = false;
		$response = $dom->createElement('response');
		$dom->appendChild($response);
		$cmd = $dom->createElement('cmd');
		$response->appendChild($cmd);
		foreach ($_GET as $param => $action) {
			$temp = $dom->createElement($param, $action);
			$cmd->appendChild($temp);
		}
		$response->appendChild($dom->createElement('code', $code));
		$response->appendChild($dom->createElement('msg', self::getResponseMsg($code)));
		/*header('Content-type: text/xml');
		header('Content-Disposition: attachment; filename="response.xml"');
		echo $dom->saveXML();*/
		return $dom->saveXML();
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
	public function parseXml2Array ($xmlFile) {
		/*$dom = new DomDocument();
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
			return false;*/
		if ($xml = simplexml_load_file($xmlFile)) {
			$arReturn = array();
			foreach ($xml->shop->offers->offer as $product) {
				$arReturn[] = array(
					"id" => (string)$product->id,
					"site_id" => (string)$product->site_id,
					"price" => (string)$product->price,
					"rest" => (string)$product->rest
				);
			}
			return $arReturn;
		}
		else
			return false;
	}

	//парсит XML файл с новыми товарами
	public function parseXml2ArrayNewOffers ($xmlFile) {
		if ($xml = simplexml_load_file($xmlFile)) {
			$arReturn = array();
			foreach ($xml->shop->offers->offer as $product) {
				$arOffers = array();
				if ($product->Characteristics->Characteristic) {
					foreach ($product->Characteristics->Characteristic as $offer) {
						$arTempOffer = array();
						foreach ($offer->attributes() as $a => $b) {
							$arTempOffer[$a] = (string)$b;
						}
						$arTempOffer["name"] = (string)$product->name;
						$arTempOffer["color"] = (string)$offer;
						$arOffers[] = $arTempOffer;
					}
				}
				$arTempProduct = array();
				foreach ($product->attributes() as $a => $b) {
					$arTempProduct[$a] = (string)$b;
				}
				$arTempProduct["name"] = (string)$product->name;
				$arTempProduct["suite_name"] = (string)$product->suite_name;
				$arTempProduct["comment"] = (string)$product->comment;
				$arTempProduct["suite_price"] = (int)$product->suite_price;
				$arTempProduct["offers"] = $arOffers;
				$arReturn[] = $arTempProduct;
			}
			return $arReturn;
		}
		else
			return false;
	}

	/*сохраняет новые товары из 1С. принимает массив, возвращенный методом parseXml2ArrayNewOffers
	возвращает массив ID добавленных товаров и торговых предложений*/
	function saveNewOffers ($arProducts) {
		if (empty($arProducts))
			return false;

		$arReturn = array();
		$arEvent = array(); //данные для отправки по почте
		foreach ($arProducts as $arProduct) {
			/*if (isset($arProduct["tp"]) && $arProduct["tp"] && isset($arProduct["general_suite_id"]) && (int)$arProduct["general_suite_id"] > 0) {
				//в данной ситуации добавить товар как ТП к товару $arProduct["general_suite_id"]
				if ($offerID = self::isAddedProduct($arProduct["id"])) {
					$arReturn[$arProduct["id"]] = $arProduct["general_suite_id"].'-'.$offerID;
				}
				else {
					if ($offerID = self::__saveOffer($arProduct["general_suite_id"], $arProduct)) {
						$arReturn[$arOffer["id"]] = $productID.'-'.$offerID;
						$arEvent[] = array(
							"type" => "Торговое предложение",
							"name" => $arProduct["name"],
							"productID" => $productID,
							"offerID" => $offerID,
							"color" => $arOffer["color"],
							"comment" => ""
						);
					}
				}
			}
			else*/
			//если главный товар уже добавлен в каталог
			if ($productID = self::isAddedProduct($arProduct["id"])) {
				$arReturn[$arProduct["id"]] = $productID;
				foreach ($arProduct["offers"] as $arOffer) {
					//если данное ТП уже добавлено в Bitrix
					if ($offerID = self::isAddedProduct($arOffer["id"])) {
						$arReturn[$arOffer["id"]] = $productID.'-'.$offerID;
					}
					//иначе
					else {
						//добавить данное ТП в каталог
						if ($offerID = self::__saveOffer($productID, $arOffer)) {
							$arReturn[$arOffer["id"]] = $productID.'-'.$offerID;
							$arEvent[] = array(
								"type" => "Торговое предложение",
								"name" => $arProduct["name"],
								"productID" => $productID,
								"offerID" => $offerID,
								"color" => $arOffer["color"],
								"comment" => ""
							);
						}
					}
				}
			}
			//иначе
			else {
				//добавить главный товар в каталог
				$el = new CIblockElement;
				$arFields = array(
					"IBLOCK_ID" => self::IBLOCK_PRODUCT_ID,
					"IBLOCK_SECTION_ID" => self::SECTION_1C_ID,
					"NAME" => $arProduct["name"],
					"CODE" => self::translitCode($arProduct["name"]),
					"XML_ID" => $arProduct["id"],
					"ACTIVE" => "N",
					"PROPERTY_VALUES" => array("COMMENT_1C" => $arProduct["comment"])
				);
				if ($productID = $el->Add($arFields)) {
					CCatalogProduct::Add(array("ID" => $productID));
					//установить цену товара
					CPrice::SetBasePrice(
						$productID,
						$arProduct["suite_price"],
						"RUB",
						false,
						false
					);
					CPrice::ReCalculate('', $productID, $arProduct["suite_price"]);
					$arEvent[] = array(
						"type" => "Основной товар",
						"name" => $arProduct["name"],
						"productID" => $productID,
						"offerID" => "",
						"color" => "",
						"comment" => $arProduct["comment"]
					);
					foreach ($arProduct["offers"] as $arOffer) {
						//если данное ТП уже добавлено в Bitrix
						if ($offerID = self::isAddedProduct($arOffer["id"])) {
							//$arReturn[$arOffer["id"]] = $productID.'-'.$offerID;
						}
						//иначе
						else {
							if ($offerID = self::__saveOffer($productID, $arOffer)) {
								$arReturn[$arOffer["id"]] = $productID.'-'.$offerID;
								$arEvent[] = array(
									"type" => "Торговое предложение",
									"name" => $arProduct["name"],
									"productID" => $productID,
									"offerID" => $offerID,
									"color" => $arOffer["color"],
									"comment" => ""
								);
							}
						}
					}
					$arReturn[$arProduct["id"]] = $productID;
				}
			}
		}
		//отправить письмо с добавленным товарами
		if (!empty($arEvent))
			self::sendAddedProductsEvent($arEvent);

		return $arReturn;
	}

	//сохраняет ТП товара $productID с полями $arOffer, возвращает ID добавленного ТП
	private function __saveOffer($productID, $arOffer) {
		if (!$productID || empty($arOffer))
			return false;
		$elOffer = new CIBlockElement;
		$arPropsTP = array("CML2_LINK" => $productID);
		if ($arOffer["color"])
			$arPropsTP["COLOR"] = $arOffer["color"];
		if ($arOffer["size"])
			$arPropsTP["SIZE"] = $arOffer["size"];
		$arFieldsSku = array(
			"IBLOCK_ID" => self::IBLOCK_SKU_ID,
			"NAME" => $arOffer["name"],
			"XML_ID" => $arOffer["id"],
			"ACTIVE" => "N",
			"PROPERTY_VALUES"=> $arPropsTP
		);
		if ($offerID = $elOffer->Add($arFieldsSku)) {
			CCatalogProduct::Add(array("ID" => $offerID));
			CPrice::SetBasePrice(
				$offerID,
				(int)$arOffer["suite_price"],
				"RUB",
				false,
				false
			);
			CPrice::ReCalculate('', $offerID, (int)$arOffer["suite_price"]);
			//$arReturn[$arOffer["id"]] = $productID.'-'.$offerID;
			return $offerID;
		}
		else
			return false;
	}

	/*возвращает строку xml с добавленной информацией о связке ID товаров на сайте и 1С
	принимет исходный xml файл и массив новых добавленных ID товаров, возвращенных методом saveNewOffers*/
	public function getAddedID2XML ($xmlFile, $arAddedIDs) {
		if (empty($arAddedIDs))
			return false;
		if ($xml = simplexml_load_file($xmlFile)) {
			foreach ($xml->shop->offers->offer as $product) {
				if ($product->Characteristics->Characteristic) {
					foreach ($product->Characteristics->Characteristic as $offer) {
						foreach ($offer->attributes() as $a => $b) {
							if ($a == "id" && array_key_exists((string)$b, $arAddedIDs))
								$offer->addAttribute('suite_id', $arAddedIDs[(string)$b]);
						}
					}
				}
				foreach ($product->attributes() as $a => $b) {
					if ($a == "id" && array_key_exists((string)$b, $arAddedIDs))
						$product->addAttribute('suite_id', $arAddedIDs[(string)$b]);
				}
			}
			return $xml->asXML();
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

	//принимает XML файл и загружает его в директорию. возвращает путь до файла или false в случае ошибки
	public function uploadFile () {
		if (!empty($_FILES) && isset($_FILES)) {
			$arFile = reset($_FILES);
			$uploaddir = $_SERVER["DOCUMENT_ROOT"].self::UPLOAD_DIR;
			$uploadfile = $uploaddir.basename($arFile['name']);
			if (file_exists($uploadfile))
				unlink($uploadfile);
			if (move_uploaded_file($arFile["tmp_name"], $uploadfile)) {
				if (file_exists($uploadfile))
					return $uploadfile;
			}
			else
				return false;
		}
		else
			return false;
	}

	//возвращает id товара или ТП из строки вида "10399-10402"
	public function getIDFromStr ($str) {
		if (!$str)
			return false;
		$arTemp = explode("-", $str);
		if (count($arTemp) == 1)
			return $arTemp[0];
		elseif (count($arTemp) == 2)
			return $arTemp[1];
		else
			return false;
	}

	//возвращает массив ID главных товаров
	public function getProductIDs ($arOffers) {
		if (empty($arOffers))
			return false;
		$arReturn = array();
		foreach ($arOffers as $key => $arOffer) {
			$arTemp = explode("-", $arOffer["site_id"]);
			if (in_array(count($arTemp), array(1, 2)) && !in_array($arTemp[0], $arReturn))
				$arReturn[] = $arTemp[0];

		}
		return $arReturn;
	}

	//возвращает "Y" если у товара ID нужно установить флажок "В наличии", и "N" если нужно его снять
	public function getAvailable ($productID) {
		if (!$productID)
			return false;

		$offersExist = CCatalogSKU::getExistOffers(array($productID));
		if ($offersExist[$productID]) {
			$arSKU = array();
			$rsSKU = CIBlockElement::GetList(
				array(),
				array("IBLOCK_ID" => self::IBLOCK_SKU_ID, "ACTIVE" => "Y", "PROPERTY_CML2_LINK" => $productID),
				false,
				false,
				array("ID")
			);
			while ($arRes = $rsSKU->GetNext())
				$arSKU[] = $arRes["ID"];

			if (!empty($arSKU)) {
				$rsProducts = CCatalogProduct::GetList(
					array(),
					array("ID" => $arSKU, ">QUANTITY" => 0),
					false,
					false,
					array("ID")
				);
				if ($rsProducts->SelectedRowsCount())
					return "Y";
				else
					return "N";
			}
			else
				return "N";
		}
		else {
			$rsProduct = CCatalogProduct::GetList(
				array(),
				array("ID" => $productID, ">QUANTITY" => 0),
				false,
				false,
				array("ID")
			);
			if ($rsProduct->SelectedRowsCount())
				return "Y";
			else
				return "N";
		}
	}

	//транслитерирует строку в символьный код
	private function translitCode ($str) {
		if (!strlen($str))
			return false;
		$str = trim(strip_tags($str));
		$arParamsT = array("replace_space"=>"-","replace_other"=>"-", 'change_case' => 'L', 'max_len' => 100);
		$code = Cutil::translit($str, "ru", $arParamsT);
		return $code;
	}

	/*проверяет по коду id из 1С, имеется ли данный товар на сайте
	Возвращает ID товара, если товар уже добавлен или false, если ещё не добавлен */
	public static function isAddedProduct ($xmlID) {
		if (!strlen($xmlID))
			return false;
		$xmlID = trim(strip_tags($xmlID));

		$rsProducts = CIBlockElement::GetList(
			array(),
			array("IBLOCK_ID" => array(IBLOCK_PRODUCT_ID, IBLOCK_SKU_ID), "XML_ID" => $xmlID),
			false,
			false,
			array("ID")
		);
		if ($arProduct = $rsProducts->GetNext())
			return $arProduct["ID"];
		else
			return false;
	}

	//отправка почтового уведомления о добавленных товарах
	private function sendAddedProductsEvent($arEvent) {
		if (empty($arEvent))
			return false;

		$table = '';
		foreach ($arEvent as $key => $arMess) {
			$table .= "<tr><td>{$arMess["type"]}</td><td>{$arMess["name"]}</td><td>{$arMess["productID"]}</td><td>{$arMess["offerID"]}</td><td>{$arMess["color"]}</td><td>{$arMess["comment"]}</td></tr>";
		}
		if ($table) {
			$table = "<table border='1' cellspacing='0' cellpadding='5'><tr><th>Тип товара</th><th>Название</th><th>ID товара</th><th>ID ТП</th><th>Цвет</th><th>Комментарий</th></tr>$table</table>";
			CEvent::Send("NEW_PROUCT_FROM_1C", SITE_ID, array("PRODUCTS"=>$table));
		}
	}

	//парсит XML файл с таблицей соответствия
	public function parseXml2ArrayCode ($xmlFile) {
		if ($xml = simplexml_load_file($xmlFile)) {
			$arReturn = array();
			foreach ($xml->shop->offers->offer as $product) {
				foreach ($product->attributes() as $a => $b) {
					$arTempProduct[$a] = (string)$b;
				}
				$arReturn[] = $arTempProduct;
			}
			return $arReturn;
		}
		else
			return false;
	}

	//обновляет внешний код $xmlID у товара $id
	public function setCode1C ($id, $xmlID) {
		if (!$id || !$xmlID)
			return false;

		$el = new CIBlockElement;
		$arFields = array('XML_ID' => $xmlID);
		$el->Update($id, $arFields);
	}

	//обновляет цену товара $id
	public function SetBasePrice ($id, $price) {
		if (!$id)
			return false;
		if (!CCatalogProduct::IsExistProduct($id)) {
			CCatalogProduct::Add(array("ID" => $id));
		}
		CPrice::SetBasePrice(
			$id,
			$price,
			"RUB",
			false,
			false
		);
		CPrice::ReCalculate('', $id, $price);
	}
}

//проверяет, существует ли допустимая команды а API
if (isset($_GET["action"])) {
	if (!API_1C::isActionExists($_GET["action"]))
		echo API_1C::getResponse('1');
}

//обновляет остатки из XML файла
if (isset($_GET["action"]) && $_GET["action"] == "updrest") {
	$api1c = new API_1C;
	if ($uploadfile = $api1c->uploadFile()) {
		$arOffers = $api1c->parseXml2Array($uploadfile);
		foreach ($arOffers as $arOffer) {
			if ($id = $api1c->getIDFromStr($arOffer["site_id"])) {
				$api1c->updateQuantity($id, $arOffer["rest"]);
				/*if (substr_count($arOffer["site_id"], "-") > 0)
					$api1c->updateActive($id, ($arOffer["rest"]>0?"Y":"N"));*/
			}
		}
		//установка/снятие флажка "В наличии"
		$arAvailable = array();
		if ($arProductIDs = $api1c->getProductIDs($arOffers)) {
			foreach ($arProductIDs as $id) {
				if ($api1c->getAvailable($id) === "Y")
					$api1c->setAvailability($id, true);
				elseif ($api1c->getAvailable($id) === "N")
					$api1c->setAvailability($id, false);
			}
		}
		CCatalogExport::PreGenerateExport(5);
		CCatalogExport::PreGenerateExport(14);

		echo API_1C::getResponseXml('0');
	}
	else
		echo API_1C::getResponseXml('6');
}
//активирует все товары из XML файла
if (isset($_GET["action"]) && $_GET["action"] == "setactive") {
	$api1c = new API_1C;
	if ($uploadfile = $api1c->uploadFile()) {
		$arOffers = $api1c->parseXml2Array($uploadfile);
		foreach ($arOffers as $arOffer) {
			if ($id = $api1c->getIDFromStr($arOffer["site_id"]))
				$api1c->updateActive($id, "Y");
		}
		echo API_1C::getResponseXml('0');
	}
	else
		echo API_1C::getResponseXml('6');
}
//деактивирует все товары из XML файла
if (isset($_GET["action"]) && $_GET["action"] == "setunactive") {
	$api1c = new API_1C;
	if ($uploadfile = $api1c->uploadFile()) {
		$arOffers = $api1c->parseXml2Array($uploadfile);
		foreach ($arOffers as $arOffer) {
			if ($id = $api1c->getIDFromStr($arOffer["site_id"]))
				$api1c->updateActive($id, "N");
		}
		echo API_1C::getResponseXml('0');
	}
	else
		echo API_1C::getResponseXml('6');
}
//добавить новые товары на сайт
if (isset($_GET["action"]) && $_GET["action"] == "newoffer") {
	$api1c = new API_1C;
	header('Content-Type: application/xml');
	header('Content-Disposition: attachment; filename="response.xml"');
	if ($uploadfile = $api1c->uploadFile()) {
		$arProducts = $api1c->parseXml2ArrayNewOffers($uploadfile);
		if (!empty($arProducts)) {
			$arAddedIDs = $api1c->saveNewOffers($arProducts);
			if (!empty($arAddedIDs)) {
				echo $api1c->getAddedID2XML($uploadfile, $arAddedIDs);
			}
			else {
				echo API_1C::getResponseXml('7');
			}
		}
		else {
			echo API_1C::getResponseXml('2');
		}
	}
	else
		echo API_1C::getResponseXml('6');
}
//обновляет коды из 1С у товаров на сайте
if (isset($_GET["action"]) && $_GET["action"] == "setcode") {
	$api1c = new API_1C;
	if ($uploadfile = $api1c->uploadFile()) {
		$arProducts = $api1c->parseXml2ArrayCode($uploadfile);
		foreach ($arProducts as $arItem) {
			if ($id = $api1c->getIDFromStr($arItem["suite_id"])) {
				$api1c->setCode1C($id, $arItem["id"]);
			}
		}
		echo API_1C::getResponseXml('0');

	}
	else
		echo API_1C::getResponseXml('6');
}
//обновляет цены из 1С на сайте
if (isset($_GET["action"]) && $_GET["action"] == "updprice") {
	header('Content-Type: application/xml');
	header('Content-Disposition: attachment; filename="response.xml"');
	$api1c = new API_1C;
	if ($uploadfile = $api1c->uploadFile()) {
		$arProducts = $api1c->parseXml2ArrayCode($uploadfile);
		foreach ($arProducts as $arItem) {
			if ($arItem["id"] && $id = $api1c->isAddedProduct($arItem["id"])) {
				$api1c->SetBasePrice($id, $arItem["price"]);
			}
			elseif ($arItem["suite_id"] && $id = $api1c->getIDFromStr($arItem["suite_id"])) {
				$api1c->SetBasePrice($id, $arItem["price"]);
			}
		}
		echo API_1C::getResponseXml('0');

	}
	else
		echo API_1C::getResponseXml('6');
}
//выгружает товары, которые были изменены за последние 2 часа
if (isset($_GET["action"]) && $_GET["action"] == "getmodifylist") {
	if (CModule::IncludeModule("catalog")) {
		if (CCatalogExport::PreGenerateExport(18)) {
			sleep(10);
			$xmlFile = file_get_contents($_SERVER["DOCUMENT_ROOT"]."/bitrix/catalog_export/1C_modify.xml");
			header('Content-Disposition: attachment; filename="1C_modify.xml"');
			//header('Content-Length: ' . strlen($xmlFile));
			echo $xmlFile;
		}
	}
}
//возвращает url картинки по внешнему коду товара
if (isset($_GET["action"]) && $_GET["action"] == "getimage" && strlen($_GET["id"]) > 0) {
	$id = trim($_GET["id"]);
	if (CModule::IncludeModule("iblock")) {
		$rsProduct = CIBlockElement::GetList(
			array(),
			array(
				"IBLOCK_ID" => array(IBLOCK_PRODUCT_ID, IBLOCK_SKU_ID),
				"XML_ID" => $id
			),
			false,
			false,
			array("PREVIEW_PICTURE")
		);
		if ($arProduct = $rsProduct->GetNext()) {
			if ($arProduct["PREVIEW_PICTURE"])
				echo 'http://'.$_SERVER["SERVER_NAME"].CFile::GetPath($arProduct["PREVIEW_PICTURE"]);
			else
				echo API_1C::getResponseXml('2');
		}
		else
			echo API_1C::getResponseXml('2');
	}
}
//возвращает url дополнительных картинок товара по внешнему коду
if (isset($_GET['action']) && $_GET['action'] == 'getdopimages' && strlen($_GET['id']) > 0) {
	CModule::IncludeModule('iblock');
	$xml_id = trim(strip_tags($_GET['id']));
	header('Content-Type: application/xml');
	header('Content-Disposition: attachment; filename="response.xml"');
	if ($productId = API_1C::isAddedProduct($xml_id)) {
		$dom = new DomDocument("1.0", "utf-8");
		$dom->formatOutput = true;
		$dom->preserveWhiteSpace = true;
		$response = $dom->createElement('response');
		$dom->appendChild($response);
		$id = $dom->createElement('id', $xml_id);
		$response->appendChild($id);
		$images = $dom->createElement('images');
		$response->appendChild($images);

		$res = CIBlockElement::GetProperty(
			IBLOCK_PRODUCT_ID,
			$productId,
			array('SORT' => 'ASC'),
			array('CODE' => 'IMAGES')
		);
		while ($arRes = $res->Fetch()) {
			$image = $dom->createElement('image', 'http://'.$_SERVER['SERVER_NAME'].CFile::GetPath($arRes['VALUE']));
			$images->appendChild($image);
		}

		echo $dom->saveXML();
		die();
	} else {
		echo API_1C::getResponseXml('3');
	}
}
