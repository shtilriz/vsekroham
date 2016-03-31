<?php
require $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php";
set_time_limit(0);

$file = $_SERVER["DOCUMENT_ROOT"]."/upload/log1c.txt";
$logStr = 'order1c|';
if ($_SERVER['HTTP_X_FORWARDED_FOR']) {
	$logStr .= $_SERVER['HTTP_X_FORWARDED_FOR'];
}
else {
	$logStr .= $_SERVER["REMOTE_ADDR"];
}
$logStr .= "|".implode("|", $_GET)."|".date('d.m.Y H:i')."\n";
file_put_contents($file, $logStr, FILE_APPEND);

class ORDER_1C {
	const IBLOCK_PRODUCT_ID = 1;		//инфоблок товаров
	const IBLOCK_SKU_ID = 2;			//инфоблок ТП
	const UPLOAD_DIR = "/upload/1c/";	//Директория для загрузки файлов синхронизации
	const USER_ID = 9139;				//ID пользователя, от имени которого будет происходить синхронизация

	private static $arActions = array(
		'getid',						//команда для получения данных заказа по его id
		'getall',						//команда для получения данных всех заказов
		'getnew',						//команда для получения данных заказов со статусом "Принят"
		'setstatus',					//команда устанавливает новый статус заказа
		'getstatuslist',				//команда возвращает список всех доступных статусов заказа
		'getdeliverylist',				//команда возвращает список всех доступных служб доставок
		'getpaylist',					//команда возвращает список всех доступных платежных систем
		'addorder',						//команда добавляет новый заказ в Bitrix из 1C
		'updorder',						//команда обновляет данные заказа
	);
	private static $arResponseMsg = array(
		"0" => "OK",
		"1" => "неправильная команда",
		"2" => "пустая выборка данных",
		"3" => "заказ не найден",
		"4" => "не указан id заказа",
		"5" => "отсутствует файл выгрузки",
		"6" => "ошибка работы с файлом",
		"7" => "ошибка установки нового статуса заказа",
		"8" => "не указан id заказа и/или статус заказа"
	);
	function __construct () {
		CModule::IncludeModule("iblock");
		CModule::IncludeModule("catalog");
		CModule::IncludeModule("sale");
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

	//принимает XML файл и загружает его в директорию. возвращает путь до файла или false в случае ошибки
	public function uploadFile () {
		if (!empty($_FILES) && isset($_FILES)) {
			$arFile = reset($_FILES);
			$uploaddir = $_SERVER["DOCUMENT_ROOT"].self::UPLOAD_DIR;
			$uploadfile = $uploaddir.basename($arFile["name"]);
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

	//возвращает массив данных о заказе
	public function getOrderArray ($ID) {
		if (!$ID)
			return false;

		if ($arOrder = CSaleOrder::GetByID($ID)) {
			if ($arDeliv = self::getDelivery2Order($arOrder["DELIVERY_ID"])) {
				$arOrder["DELIVERY"] = $arDeliv;
			}
			if ($arDeliv = self::getPay2Order($arOrder["PAY_SYSTEM_ID"])) {
				$arOrder["PAY"] = $arDeliv;
			}
			if ($arBasketItems = self::getBasket2Order($ID)) {
				$arOrder["BASKET"] = $arBasketItems;
			}
			$arOrder["PROPS"] = self::getProps2Order($ID);
			return $arOrder;
		}
		else
			return false;
	}

	//возвращает массив данных о всех заказах
	public function getAllOrdersArray ($arFilter) {
		$db_sales = CSaleOrder::GetList(array("DATE_INSERT" => "ASC"), $arFilter);

		$arOrders = array();
		while ($arOrder = $db_sales->Fetch()) {
			if ($arDeliv = self::getDelivery2Order($arOrder["DELIVERY_ID"])) {
				$arOrder["DELIVERY"] = $arDeliv;
			}
			if ($arDeliv = self::getPay2Order($arOrder["PAY_SYSTEM_ID"])) {
				$arOrder["PAY"] = $arDeliv;
			}
			if ($arBasketItems = self::getBasket2Order($arOrder["ID"])) {
				$arOrder["BASKET"] = $arBasketItems;
			}
			$arOrder["PROPS"] = self::getProps2Order($arOrder["ID"]);
			$arOrders[] = $arOrder;
		}
		return $arOrders;
	}

	//возвращает корзину по $ID заказа
	public function getBasket2Order ($ID) {
		if (!$ID)
			return false;

		$dbBasketItems = CSaleBasket::GetList(
			array("NAME" => "ASC","ID" => "ASC"),
			array(
				"LID" => SITE_ID,
				"ORDER_ID" => $ID
			),
			false,
			false,
			array()
		);
		while ($arItem = $dbBasketItems->Fetch()) {
			//достать идентификатор 1С
			$arItem["XML_ID"] = self::getXMLID2ID($arItem["PRODUCT_ID"]);
			$rsProduct = CIBlockElement::GetList(
				array("SORT" => "ASC"),
				array("ID" => $arItem["PRODUCT_ID"]),
				false,
				false,
				array("DETAIL_PAGE_URL", "PROPERTY_COLOR", "PROPERTY_SIZE")
			);
			if ($arProduct = $rsProduct->GetNext()) {
				$arItem["DETAIL_PAGE_URL"] = $arProduct["DETAIL_PAGE_URL"];
				$arItem["COLOR"] = $arProduct["PROPERTY_COLOR_VALUE"];
				$arItem["SIZE"] = $arProduct["PROPERTY_SIZE_VALUE"];
			}
			$mxResult = CCatalogSku::GetProductInfo($arItem["PRODUCT_ID"]);
			if (is_array($mxResult))
				$arItem["SITE_ID"] = $mxResult["ID"].'-'.$arItem["PRODUCT_ID"];
			else
				$arItem["SITE_ID"] = $arItem["PRODUCT_ID"];
			$arBasketItems[] = $arItem;
		}
		return $arBasketItems;
	}

	//возвращает описание службы доставки по его $DELIVERY_ID
	public function getDelivery2Order ($DELIVERY_ID) {
		if (!$DELIVERY_ID)
			return false;

		if ($arDeliv = CSaleDelivery::GetByID($DELIVERY_ID))
			return $arDeliv;
		else
			return false;
	}

	//возвращает описание платежной системы по её $PAY_SYSTEM_ID
	public function getPay2Order ($PAY_SYSTEM_ID) {
		if (!$PAY_SYSTEM_ID)
			return false;

		if ($arPaySys = CSalePaySystem::GetByID($PAY_SYSTEM_ID))
			return $arPaySys;
		else
			return false;
	}

	//возвращает свойства заказа по его $ORDER_ID
	public function getProps2Order ($ORDER_ID) {
		if (!$ORDER_ID)
			return false;

		$arProps = array();
		$db_props = CSaleOrderPropsValue::GetOrderProps($ORDER_ID);
		while ($arProp = $db_props->Fetch()) {
			$arProps[$arProp["CODE"]] = $arProp;
		}
		return $arProps;
	}

	//возвращает XML_ID товара по его ID
	public function getXMLID2ID ($ID) {
		if (!$ID)
			return false;
		$res = CIBlockElement::GetList(array(),array("ID" => $ID),false,false,array("XML_ID"));
		if ($arRes = $res->GetNext())
			return $arRes["XML_ID"];
		else
			return false;
	}

	//возвращает данные заказов из массива $arOrders в формате xml
	public function getOrderXml ($arOrders) {
		if (empty($arOrders))
			return false;

		$dom = new DomDocument("1.0", "utf-8");
		$dom->formatOutput = true;
		$dom->preserveWhiteSpace = false;
		$export = $dom->createElement('ЭкспортЗаказов');
		$dom->appendChild($export);
		$date = $dom->createElement('ДатаИВремя', date('Y-m-d H:i:s', time()));
		$export->appendChild($date);
		$orders = $dom->createElement('Заказы');
		$export->appendChild($orders);

		foreach ($arOrders as $key => $arOrder) {
			$order = $dom->createElement('Заказ');
			$order_id = $dom->createElement('Ид', $arOrder["ID"]);
			$order->appendChild($order_id);
			$order_date = $dom->createElement('ДатаИВремя', $arOrder["DATE_INSERT"]);
			$order->appendChild($order_date);
			$status = $dom->createElement('Статус', $arOrder["STATUS_ID"]);
			$order->appendChild($status);
			$canceled = $dom->createElement('ЗаказОтменен', $arOrder["CANCELED"]);
			$order->appendChild($canceled);
			$allowDelivery = $dom->createElement('ДоставкаРазрешена', $arOrder["ALLOW_DELIVERY"]);
			$order->appendChild($allowDelivery);
			$payed = $dom->createElement('ЗаказОплачен', $arOrder["PAYED"]);
			$order->appendChild($payed);
			//доставка
			$delivery = $dom->createElement('Доставка');
			$delivery_id = $dom->createElement('Ид',$arOrder["DELIVERY"]["ID"]);
			$delivery->appendChild($delivery_id);
			$delivery_name = $dom->createElement('Наименование', $arOrder["DELIVERY"]["NAME"]);
			$delivery->appendChild($delivery_name);
			$deliveryService = $dom->createElement('СлужбаДоставки', $arOrder["PROPS"]["DELIVERY"]["VALUE"]);
			$delivery->appendChild($deliveryService);
			$tariff = $dom->createElement('ТарифСлужбыДоставки', $arOrder["PROPS"]["TARIFF"]["VALUE"]);
			$delivery->appendChild($tariff);
			$cityEdost = $dom->createElement('ГородРасчетаДоставки', $arOrder["PROPS"]["CITY_EDOST"]["VALUE"]);
			$delivery->appendChild($cityEdost);
			$day = $dom->createElement('КоличествоДнейДоставки', $arOrder["PROPS"]["DAY"]["VALUE"]);
			$delivery->appendChild($day);
			$city = $dom->createElement('Город', $arOrder["PROPS"]["CITY"]["VALUE"]);
			$delivery->appendChild($city);
			$address = $dom->createElement('Адрес', $arOrder["PROPS"]["ADDRESS"]["VALUE"]);
			$delivery->appendChild($address);
			if ($arOrder["PROPS"]["DELIVERY_DATE"]["VALUE"]) {
				$delivery_date = $dom->createElement('ДатаДоставки', $arOrder["PROPS"]["DELIVERY_DATE"]["VALUE"]);
				$delivery->appendChild($delivery_date);
			}
			$trackingNumber = $dom->createElement('ИдентификаторОтправления', ($arOrder["TRACKING_NUMBER"]?$arOrder["TRACKING_NUMBER"]:''));
			$delivery->appendChild($trackingNumber);
			$delivery_price = $dom->createElement('Стоимость', $arOrder["PRICE_DELIVERY"]);
			$delivery->appendChild($delivery_price);
			$order->appendChild($delivery);
			//оплата
			$pay = $dom->createElement('СпособОплаты');
			$pay_id = $dom->createElement('Ид', $arOrder["PAY"]["ID"]);
			$pay->appendChild($pay_id);
			$pay_name = $dom->createElement('Наименование', $arOrder["PAY"]["NAME"]);
			$pay->appendChild($pay_name);
			$firstName = $dom->createElement('Имя', $arOrder["PROPS"]["FIO"]["VALUE"]);
			$pay->appendChild($firstName);
			$secondName = $dom->createElement('Фамилия', $arOrder["PROPS"]["FAMILY"]["VALUE"]);
			$pay->appendChild($secondName);
			if ($arOrder["PROPS"]["PHONE"]["VALUE"]) {
				$pay_phone = $dom->createElement('Телефон', $arOrder["PROPS"]["PHONE"]["VALUE"]);
				$pay->appendChild($pay_phone);
			}
			if ($arOrder["PROPS"]["EMAIL"]["VALUE"]) {
				$pay_email = $dom->createElement('АдресЭлПочты', $arOrder["PROPS"]["EMAIL"]["VALUE"]);
				$pay->appendChild($pay_email);
			}
			if ($arOrder["USER_DESCRIPTION"]) {
				$pay_user_descr = $dom->createElement('КомментарииКЗаказу', $arOrder["USER_DESCRIPTION"]);
				$pay->appendChild($pay_user_descr);
			}
			$order->appendChild($pay);
			//корзина
			$basket = $dom->createElement('Товары');
			foreach ($arOrder["BASKET"] as $cell => $arItem) {
				$item = $dom->createElement('Товар');
				$item_id = $dom->createElement('Ид', $arItem["XML_ID"]);
				$item->appendChild($item_id);
				$item_product_id = $dom->createElement('ТоварИд', $arItem["SITE_ID"]);
				$item->appendChild($item_product_id);
				$item_name = $dom->createElement('НаименованиеТовара', $arItem["NAME"]);
				$item->appendChild($item_name);
				$item_price = $dom->createElement('Цена', $arItem["PRICE"]);
				$item->appendChild($item_price);
				$item_discount = $dom->createElement('Скидка', $arItem["DISCOUNT_PRICE"]);
				$item->appendChild($item_discount);
				$item_percent_discount = $dom->createElement('Процент_скидки', (int)$arItem["DISCOUNT_VALUE"]);
				$item->appendChild($item_percent_discount);
				$item_quan = $dom->createElement('Количество', (int)$arItem["QUANTITY"]);
				$item->appendChild($item_quan);
				$item_link = $dom->createElement('Ссылка', $arItem["DETAIL_PAGE_URL"]);
				$item->appendChild($item_link);
				if ($arItem["COLOR"]) {
					$item_color = $dom->createElement('Цвет', $arItem["COLOR"]);
					$item->appendChild($item_color);
				}
				if ($arItem["SIZE"]) {
					$item_size = $dom->createElement('Размер', $arItem["SIZE"]);
					$item->appendChild($item_size);
				}

				$basket->appendChild($item);
			}
			$order->appendChild($basket);

			$itogo = $dom->createElement('Итого', $arOrder["PRICE"]);
			$order->appendChild($itogo);
			if ($arOrder["DISCOUNT_VALUE"]) {
				$discount = $dom->createElement('Скидка', $arOrder["DISCOUNT_VALUE"]);
				$order->appendChild($discount);
			}

			$orders->appendChild($order);
		}
		return $dom->saveXML();
	}

	//возвращает массив всех статусов заказов
	public function getStatusList () {
		$arStatus = array();
		$rsStatus = CSaleStatus::GetList(
			array("SORT" => "ASC"),
			array(),
			false,
			false,
			array("ID", "NAME")
		);
		while ($arStat = $rsStatus->GetNext()) {
			$arStatus[$arStat["ID"]] = $arStat["NAME"];
		}
		if (!empty($arStatus))
			return $arStatus;
		else
			return false;
	}

	//возвращает список всех статусов заказов в формате xml
	public function getStatusListXml ($arStatusList) {
		if (empty($arStatusList))
			return false;

		$dom = new DomDocument("1.0", "utf-8");
		$dom->formatOutput = true;
		$dom->preserveWhiteSpace = false;
		$statusList = $dom->createElement('СтатусыЗаказов');
		$dom->appendChild($statusList);
		foreach ($arStatusList as $id => $name) {
			$status = $dom->createElement('Статус', $name);
			$status_id = $dom->createAttribute('Ид');
			$status_id->value = $id;
			$status->appendChild($status_id);
			$statusList->appendChild($status);
		}
		return $dom->saveXML();
	}

	//возвращает массив всех способов доставки
	public function getDeliveryList () {
		$arDelivery = array();
		$rsDelivery = CSaleDelivery::GetList(
			array("SORT" => "ASC"),
			array("ACTIVE" => "Y"),
			false,
			false,
			array("ID", "NAME", "ORDER_PRICE_FROM", "ORDER_PRICE_TO", "PRICE")
		);
		while ($arDeliv = $rsDelivery->GetNext()) {
			$arDelivery[$arDeliv["ID"]] = $arDeliv;
		}
		if (!empty($arDelivery))
			return $arDelivery;
		else
			return false;
	}

	//возвращает список всех служб доставки в формате xml
	public function getDeliveryListXml ($arDeliveryList) {
		if (empty($arDeliveryList))
			return false;

		$dom = new DomDocument("1.0", "utf-8");
		$dom->formatOutput = true;
		$dom->preserveWhiteSpace = false;
		$deliveryList = $dom->createElement('СлужбыДоставки');
		$dom->appendChild($deliveryList);
		foreach ($arDeliveryList as $id => $arDeliv) {
			$delivery = $dom->createElement('СлужбаДоставки');
			$delivery_id = $dom->createAttribute('Ид');
			$delivery_id->value = $id;
			$delivery->appendChild($delivery_id);
			$name = $dom->createElement('Наименование', $arDeliv["NAME"]);
			$delivery->appendChild($name);
			$minPrice = $dom->createElement('МинимальнаяСтоимостьЗаказа', $arDeliv["ORDER_PRICE_FROM"]);
			$delivery->appendChild($minPrice);
			$maxPrice = $dom->createElement('МаксимальнаяСтоимостьЗаказа', $arDeliv["ORDER_PRICE_TO"]);
			$delivery->appendChild($maxPrice);
			$price = $dom->createElement('СтоимостьДоставки', $arDeliv["PRICE"]);
			$delivery->appendChild($price);
			$deliveryList->appendChild($delivery);
		}
		return $dom->saveXML();
	}

	//возвращает массив всех платежных систем
	public function getPaymentList () {
		$arPayment = array();
		$rsPayment = CSalePaySystem::GetList(
			array("SORT" => "ASC"),
			array(),
			false,
			false,
			array("ID", "NAME")
		);
		while ($arPay = $rsPayment->GetNext()) {
			$arPayment[$arPay["ID"]] = $arPay["NAME"];
		}
		if (!empty($arPayment))
			return $arPayment;
		else
			return false;
	}

	//возвращает список всех платежных систем в формате xml
	public function getPaymentListXml ($arPaymentList) {
		if (empty($arPaymentList))
			return false;

		$dom = new DomDocument("1.0", "utf-8");
		$dom->formatOutput = true;
		$dom->preserveWhiteSpace = false;
		$paymentList = $dom->createElement('ПлатежныеСистемы');
		$dom->appendChild($paymentList);
		foreach ($arPaymentList as $id => $name) {
			$payment = $dom->createElement('ПлатежнаяСистема', $name);
			$payment_id = $dom->createAttribute('Ид');
			$payment_id->value = $id;
			$payment->appendChild($payment_id);
			$paymentList->appendChild($payment);
		}
		return $dom->saveXML();
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

	//парсит xml-файл с заказами и возвращает мсссив данных
	public function parseXml2ArrayOrder ($xmlFile) {
		if ($xml = simplexml_load_file($xmlFile)) {
			$arReturn = array();
			foreach ($xml->Заказы->Заказ as $order) {
				$arOrder = array();
				$arOrder["ID"] = (int)$order->Ид;
				$arOrder["DELIVERY_ID"] = (int)$order->Доставка->Ид;
				$arOrder["PRICE_DELIVERY"] = sprintf("%.2f", (string)$order->Доставка->Стоимость);
				$arOrder["PAY_SYSTEM_ID"] = (int)$order->СпособОплаты->Ид;
				$arOrder["STATUS_ID"] = (string)$order->Статус;
				$arOrder["CANCELED"] = (string)$order->ЗаказОтменен;
				$arOrder["ALLOW_DELIVERY"] = (string)$order->ДоставкаРазрешена;
				$arOrder["PAYED"] = (string)$order->ЗаказОплачен;
				$arOrder["TRACKING_NUMBER"] = (string)$order->Доставка->ИдентификаторОтправления;
				$arOrder["PROPS"] = array(
					"FIO" => (string)$order->СпособОплаты->Имя,
					"FAMILY" => (string)$order->СпособОплаты->Фамилия,
					"EMAIL" => (string)$order->СпособОплаты->АдресЭлПочты,
					"PHONE"  => (string)$order->СпособОплаты->Телефон,
					"CITY"  => (string)$order->Доставка->Город,
					"ADDRESS"  => (string)$order->Доставка->Адрес,
					"DELIVERY_DATE" => (string)$order->Доставка->ДатаДоставки,
					"DELIVERY" => (string)$order->Доставка->СлужбаДоставки,
					/*"TARIFF" => (string)$order->Доставка->ТарифСлужбыДоставки,
					"CITY_EDOST" => (string)$order->Доставка->ГородРасчетаДоставки,
					"DAY" => (string)$order->Доставка->КоличествоДнейДоставки*/
				);
				$arBasket = array();
				foreach ($order->Товары->Товар as $product) {
					$arBasket[] = array(
						"XML_ID" => (string)$product->Ид,
						"PRODUCT_ID" => self::getIDFromStr((string)$product->ТоварИд),
						"NAME" => (string)$product->НаименованиеТовара,
						"PRICE" => sprintf("%.2f", (string)$product->Цена),
						"QUANTITY" => (int)$product->Количество,
						"DETAIL_PAGE_URL" => (string)$product->Ссылка,
						"COLOR" => (string)$product->Цвет,
						"SIZE" => (string)$product->Размер
					);
				}
				$arOrder["BASKET"] = $arBasket;
				$arOrder["PRICE"] = sprintf("%.2f", (string)$order->Итого);
				$arOrder["DISCOUNT"] = sprintf("%.2f", (string)$order->Скидка);
				$arOrder["USER_DESCRIPTION"] = (string)$order->КомментарииКЗаказу;

				$arReturn[] = $arOrder;
			}
			return $arReturn;
		}
		else
			return false;
	}

	//создает заказ из данных массива $arOrder
	public function addOrder ($arOrder) {
		if (empty($arOrder))
			return false;

		$FUSER_ID = CSaleUser::GetList(array("USER_ID" => self::USER_ID));
		if(!$FUSER_ID["ID"])
			$FUSER_ID["ID"] = CSaleUser::_Add(array("USER_ID" => self::USER_ID));
		if(!$FUSER_ID["ID"])
			return false;

		CSaleBasket::DeleteAll($FUSER_ID["ID"], false); //очистить текущую корзину

		//добавить товары в корзину
		foreach ($arOrder["BASKET"] as $key => $arItem) {
			self::_add2basket($arItem, $FUSER_ID["ID"]);
		}

		//создать заказ
		$arFields = array(
			"LID" => SITE_ID,
			"PERSON_TYPE_ID" => 1,
			"PAYED" => ($arOrder["PAYED"] ? $arOrder["PAYED"] : "N"),
			"CANCELED" => ($arOrder["CANCELED"] ? $arOrder["CANCELED"] : "N"),
			"STATUS_ID" => ($arOrder["STATUS_ID"] ? $arOrder["STATUS_ID"] : "N"),
			"PRICE" => $arOrder["PRICE"],
			"CURRENCY" => "RUB",
			"USER_ID" => self::USER_ID,
			"DISCOUNT_VALUE" => ($arOrder["DISCOUNT"] ? $arOrder["DISCOUNT"] : 0),
			"TAX_VALUE" => 0.0,
			"USER_DESCRIPTION" => $arOrder["USER_DESCRIPTION"],
			"TRACKING_NUMBER" => ($arOrder["TRACKING_NUMBER"] ? $arOrder["TRACKING_NUMBER"] : '')
		);
		if ($arOrder["PAY_SYSTEM_ID"])
			$arFields["PAY_SYSTEM_ID"] = $arOrder["PAY_SYSTEM_ID"];
		if ($arOrder["DELIVERY_ID"])
			$arFields["DELIVERY_ID"] = $arOrder["DELIVERY_ID"];
		$arFields["PRICE_DELIVERY"] = $arOrder["PRICE_DELIVERY"] ? $arOrder["PRICE_DELIVERY"] : 0;

		if ($ORDER_ID = CSaleOrder::Add($arFields)) {
			CSaleBasket::OrderBasket($ORDER_ID, $FUSER_ID["ID"], SITE_ID);
			//Если введены свойства заказа, добавляем их к заказу
			$arSort = array("SORT"=>"ASC");
			$arFilter = array("PERSON_TYPE_ID"=>1,"PROPS_GROUP_ID"=>array(1,2,3));
			$db_props = CSaleOrderProps::GetList($arSort,$arFilter,false,false,array());
			while ($props = $db_props->Fetch()) {
				if (isset($arOrder["PROPS"][$props["CODE"]]) && $arOrder["PROPS"][$props["CODE"]] != '') {
					if ($arOrder["DELIVERY_ID"] != 3 && $props["CODE"] == "ADDRESS_SHOPS")
						continue;
					$arFields = array(
						"ORDER_ID" => $ORDER_ID,
						"ORDER_PROPS_ID" => $props["ID"],
						"NAME" => $props["NAME"],
						"CODE" => $props["CODE"],
						"VALUE" => $arOrder["PROPS"][$props["CODE"]]
					);
					CSaleOrderPropsValue::Add($arFields);
				}
			}
			//отправить письмо пользователю о созданном заказе
			$arOrder["ID"] = $ORDER_ID;
			self::_sendEmailOrder($arOrder, "SALE_NEW_ORDER");

			return $ORDER_ID;
		}
		else
			return false;
	}

	//добавляет товар в корзину пользователя с внутренним кодом $FUSER_ID
	private function _add2basket ($arItem, $FUSER_ID) {
		if (empty($arItem))
			return false;

		$arFieldsBsk = array(
			"PRODUCT_ID" => $arItem["PRODUCT_ID"],
			//"PRODUCT_PRICE_ID" => 0,
			"PRICE" => $arItem["PRICE"],
			"CURRENCY" => "RUB",
			"QUANTITY" => $arItem["QUANTITY"] ? $arItem["QUANTITY"] : 1,
			"LID" => LANG,
			"NAME" => $arItem["NAME"]
		);
		if ($FUSER_ID) {
			$arFieldsBsk["FUSER_ID"] = $FUSER_ID;
		}
		if ($arItem["ORDER_ID"]) {
			$arFieldsBsk["ORDER_ID"] = $arItem["ORDER_ID"];
		}
		$arProps = array();
		if ($arItem["COLOR"]) {
			$arProps[] = array(
				"NAME" => "Цвет",
				"CODE" => "COLOR",
				"VALUE" => $arItem["COLOR"]
			);
		}
		if ($arItem["SIZE"]) {
			$arProps[] = array(
				"NAME" => "Размер",
				"CODE" => "SIZE",
				"VALUE" => $arItem["SIZE"]
			);
		}
		$arFieldsBsk["PROPS"] = $arProps;
		CSaleBasket::Add($arFieldsBsk);
	}

	//отправляет емейл уведомление пользователю о созданном заказе
	private function _sendEmailOrder ($arOrder, $event) {
		if ($arOrder["PROPS"]["EMAIL"]) {
			$nameDeliv = '';
			$arDelivery = self::getDeliveryList();
			if (array_key_exists($arOrder["DELIVERY_ID"], $arDelivery))
				$nameDeliv = $arDelivery[$arOrder["DELIVERY_ID"]]["NAME"].($arOrder["PROPS"]["DELIVERY"]?' ('.$arOrder["PROPS"]["DELIVERY"].')':'');
			$namePayment = '';
			$arPayments = self::getPaymentList();
			if (array_key_exists($arOrder["PAY_SYSTEM_ID"], $arPayments))
				$namePayment = $arPayments[$arOrder["PAY_SYSTEM_ID"]];

			$arEventFields = array(
				"EMAIL" => $arOrder["PROPS"]["EMAIL"],
				"LOGIN" => $arOrder["PROPS"]["EMAIL"],
				"ORDER_USER" => $arOrder["PROPS"]["FIO"].($arOrder["PROPS"]["FAMILY"]?' '.$arOrder["PROPS"]["FAMILY"]:''),
				"ORDER_DATE" => date('d.m.Y'),
				"ORDER_ID" => $arOrder["ID"],
				"ORDER_LIST" => self::_getBasketHTML($arOrder),
				"DELIVERY" => $nameDeliv,
				"PAYMENT" => $namePayment,
				"DATA" => self::_getUserDataHTML($arOrder),
				"PRICE" => SaleFormatCurrency((int)$arOrder["PRICE"]+(int)$arOrder["PRICE_DELIVERY"],"RUB"),
				"SALE_EMAIL" => COption::GetOptionString("sale", "order_email", "order@".$SERVER_NAME)
			);
			/*if (isset($_POST["ADDRESS_SHOP"]) && strlen($_POST["ADDRESS_SHOP"]) > 0 && $DELIVERY == 5) {
				$arEventFields["DELIVERY"] = $arEventFields["DELIVERY"].' ('.$_POST["ADDRESS_SHOP"].')';
			}*/
			ob_start();
			CEvent::Send($event, "s1", $arEventFields);
			ob_end_clean();
		}
	}

	//возвращает html-код таблицы с товарами заказа
	private function _getBasketHTML($arOrder) {
		$sostav = '';
		$allSum = 0;
		foreach ($arOrder["BASKET"] as $key => $arItem) {
			$allSum += intval($arItem["PRICE"])*intval($arItem["QUANTITY"]);
			$DETAIL_PAGE_URL = '';
			$rsProduct = CIBlockElement::GetList(array(),array("ACTIVE"=>"Y","ID"=>$arItem["PRODUCT_ID"]),false,false,array("IBLOCK_ID", "DETAIL_PAGE_URL"));
			if ($arProduct = $rsProduct->GetNext())
				$DETAIL_PAGE_URL = $arProduct["DETAIL_PAGE_URL"];

			$sostav .= '<tr>
				<td width="420" style="height: 41px;padding: 0 15px;vertical-align: middle;color: #007acf;border-top: 1px solid #e0dddd;border-bottom: 1px solid #e0dddd;"><a href="http://'.$_SERVER["SERVER_NAME"].$DETAIL_PAGE_URL.'" style="color: #007acf;text-decoration: none;">'.$arItem["NAME"].'</a><br>
					<div style="color: #a3a3a3;font-size: 14px;line-height: 18px;margin-bottom: 5px;">
						'.($arItem["COLOR"]?'<span style="display: block;">Цвет: '.$arItem["COLOR"].'</span>':'').'
						'.($arItem["SIZE"]?'<span style="display: block;">Размер: '.$arItem["SIZE"].'</span>':'').'
					</div>
				</td>
				<td class="quantity" style="height: 41px;padding: 0 15px;vertical-align: middle;color: #7f8090;border-top: 1px solid #e0dddd;border-bottom: 1px solid #e0dddd;">'.intval($arItem["QUANTITY"]).'шт.</td>
				<td style="height: 41px;padding: 0 15px;vertical-align: middle;color: #007acf;border-top: 1px solid #e0dddd;border-bottom: 1px solid #e0dddd;">'.SaleFormatCurrency(intval($arItem["PRICE"])*intval($arItem["QUANTITY"]),"RUB").'</td>
			</tr>';
		}

		if ($sostav) {
			$sostav = '<table class="subtable2" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;width: 100%;border: 1px solid #e0dddd;margin-top: 10px;margin-bottom: 23px;">
		        <tbody>
					'.$sostav.'
				</tbody>
			</table>
			'.((int)$arOrder["PRICE_DELIVERY"]>0?'<span class="lead" style="font-size: 18px;color: #151734;">Стоимость доставки:</span> <span class="price" style="padding-left: 10px;font-size: 30px;color: #007acf;">'.SaleFormatCurrency($arOrder["PRICE_DELIVERY"],"RUB").'</span><br>':'').'
			<span class="lead" style="font-size: 18px;color: #151734;">Стоимость заказа:</span> <span class="price" style="padding-left: 10px;font-size: 30px;color: #007acf;">'.SaleFormatCurrency($allSum,"RUB").'</span>';
		}
		return $sostav;
	}

	//возвращает html-код таблицы с данными о покупателе
	private function _getUserDataHTML($arOrder) {
		$user_data = '';
		foreach ($arOrder["PROPS"] as $prop => $value) {
			if (!$value)
				continue;
			$arSort = array("SORT"=>"ASC");
			$arFilter = array("PERSON_TYPE_ID"=>1,"CODE"=>$prop);
			$db_props = CSaleOrderProps::GetList($arSort,$arFilter,false,false,array());
			if ($arProps = $db_props->Fetch()) {

				$user_data .= '<tr>
					<td class="first" width="120" style="padding: 6px 15px;text-align: right;">'.$arProps["NAME"].'</td>
					<td style="padding: 6px 15px;"><span class="strong" style="color: #151734;">'.$value.'</span></td>
				</tr>';
			}
		}
		if ($arOrder["USER_DESCRIPTION"]) {
			$user_data .= '<tr>
				<td class="first" width="120" style="padding: 6px 15px;text-align: right;">Дополнительная информация</td>
				<td style="padding: 6px 15px;"><span class="strong" style="color: #151734;">'.$arOrder["USER_DESCRIPTION"].'</span></td>
			</tr>';
		}
		if ($user_data) {
			$user_data = '<div class="lead" style="font-size: 18px;color: #151734;">Ваши данные</div>
			<table class="subtable3" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;margin-top: 23px;margin-bottom: 20px;">
				<tbody>
					'.$user_data.'
				</tbody>
			</table>';
		}
		return $user_data;
	}

	//изменяет заказ из данных массива $arOrder
	public function updOrder ($arOrder) {
		if (empty($arOrder))
			return false;

		$arOrderOld = self::getOrderArray($arOrder["ID"]);

		$FUSER_ID = CSaleUser::GetList(array("USER_ID" => $arOrderOld["USER_ID"]));
		if(!$FUSER_ID["ID"])
			$FUSER_ID["ID"] = CSaleUser::_Add(array("USER_ID" => $arOrderOld["USER_ID"]));
		if(!$FUSER_ID["ID"])
			return false;

		//CSaleBasket::DeleteAll($FUSER_ID["ID"], false); //удалить текущую корзину
		/*$dbBasketItems = CSaleBasket::GetList(
			array(),
			array("USER_ID" => $arOrderOld["USER_ID"], "DELAY" => "N", "CAN_BUY" => "Y"),
			false,
			false,
			array("ID")
		);
		while ($arItems = $dbBasketItems->Fetch()) {
			CSaleBasket::Delete($arItems["ID"]);
		}*/

		$arFields = array(
			"LID" => SITE_ID,
			"PERSON_TYPE_ID" => 1,
			"PAYED" => ($arOrder["PAYED"] ? $arOrder["PAYED"] : "N"),
			//"CANCELED" => ($arOrder["CANCELED"] ? $arOrder["CANCELED"] : "N"),
			//"STATUS_ID" => ($arOrder["STATUS_ID"] ? $arOrder["STATUS_ID"] : "N"),
			"PRICE" => $arOrder["PRICE"],
			"CURRENCY" => "RUB",
			"USER_ID" => $arOrderOld["USER_ID"],
			"DISCOUNT_VALUE" => ($arOrder["DISCOUNT"] ? $arOrder["DISCOUNT"] : 0),
			"TAX_VALUE" => 0.0,
			"USER_DESCRIPTION" => $arOrder["USER_DESCRIPTION"],
			"TRACKING_NUMBER" => ($arOrder["TRACKING_NUMBER"] ? $arOrder["TRACKING_NUMBER"] : '')
		);

		if ($arOrder["PAY_SYSTEM_ID"])
			$arFields["PAY_SYSTEM_ID"] = $arOrder["PAY_SYSTEM_ID"];
		if ($arOrder["DELIVERY_ID"])
			$arFields["DELIVERY_ID"] = $arOrder["DELIVERY_ID"];
		$arFields["PRICE_DELIVERY"] = $arOrder["PRICE_DELIVERY"] ? $arOrder["PRICE_DELIVERY"] : 0;

		if ($ORDER_ID = CSaleOrder::Update($arOrder["ID"], $arFields)) {
			//CSaleBasket::OrderBasket($ORDER_ID, $FUSER_ID["ID"], SITE_ID);
			//Если введены свойства заказа, добавляем их к заказу
			$arSort = array("SORT"=>"ASC");
			$arFilter = array("PERSON_TYPE_ID"=>1,"PROPS_GROUP_ID"=>array(1,2));
			$db_props = CSaleOrderProps::GetList($arSort,$arFilter,false,false,array());
			while ($props = $db_props->Fetch()) {
				if (isset($arOrder["PROPS"][$props["CODE"]]) && $arOrder["PROPS"][$props["CODE"]] != '') {
					if ($arOrder["DELIVERY_ID"] != 3 && $props["CODE"] == "ADDRESS_SHOPS")
						continue;
					$arFields = array(
						"ORDER_ID" => $ORDER_ID,
						"ORDER_PROPS_ID" => $props["ID"],
						"NAME" => $props["NAME"],
						"CODE" => $props["CODE"],
						"VALUE" => $arOrder["PROPS"][$props["CODE"]]
					);
					CSaleOrderPropsValue::Add($arFields);
				}
			}
			if ($arOrder["CANCELED"] && $arOrderOld["CANCELED"] != $arOrder["STATUS_ID"]) {
				CSaleOrder::CancelOrder($ORDER_ID, $arOrder["CANCELED"], "");
			}
			if ($arOrder["STATUS_ID"] && $arOrderOld["STATUS_ID"] != $arOrder["STATUS_ID"]) {
				ob_start();
				CSaleOrder::StatusOrder($ORDER_ID, $arOrder["STATUS_ID"]);
				ob_end_clean();
				//отправить письмо пользователю о измененном заказе
				if ($arOrder["STATUS_ID"] == "I")
					self::_sendEmailOrder($arOrder, "SALE_UPD_ORDER");
			}

			//добавить товары в корзину
			/*foreach ($arOrder["BASKET"] as $key => $arItem) {
				self::_add2basket($arItem, $FUSER_ID["ID"]);
			}

			CSaleBasket::OrderBasket($ORDER_ID, $FUSER_ID["ID"], SITE_ID);*/
			self::updBasket($arOrder);

			return $ORDER_ID;
		}
		else
			return false;
	}

	//обновляет корзину с заказом
	public function updBasket (&$arOrder) {
		//достать товары текущей корзины
		$arBasketOrder = self::getBasket2Order($arOrder["ID"]);

		//в цикле по товарам текущей корзины
		foreach ($arBasketOrder as $key => $arItem) {
			$bExist = false;
			foreach ($arOrder["BASKET"] as $cell => $arItem1C) {
				//если количество товара текущей корзины не совпадет с количеством товара в корзине из 1С
				if ($arItem["PRODUCT_ID"] == $arItem1C["PRODUCT_ID"]) {
					$bExist = true;
					if ($arItem["QUANTITY"] != $arItem1C["QUANTITY"]) {
						//обновить кол-во из 1С
						CSaleBasket::Update($arItem["ID"], array("QUANTITY" => $arItem1C["QUANTITY"]));
					}
				}
			}
			//если товар текущей корзины отсутствует в корзине из 1С
			if (!$bExist) {
				//удалить этот товар из текущей корзины
				CSaleBasket::Delete($arItem["ID"]);
			}
		}

		//если в текущей корзине отсутствует какой либо товар из 1С
		foreach ($arOrder["BASKET"] as $cell => $arItem1C) {
			$bExist = false;
			foreach ($arBasketOrder as $key => $arItem) {
				if ($arItem["PRODUCT_ID"] == $arItem1C["PRODUCT_ID"]) {
					$bExist = true;
				}
			}
			//добавить в текущую корзину товар из 1С
			if (!$bExist) {
				$arItemBsk = $arItem1C;
				$arItemBsk["ORDER_ID"] = $arOrder["ID"];
				self::_add2basket($arItemBsk, false);
			}
		}
	}
}

//проверяет, существует ли допустимая команда API
if (isset($_GET["action"])) {
	if (!ORDER_1C::isActionExists($_GET["action"])) {
		header('Content-Type: application/xml');
		header('Content-Disposition: attachment; filename="response.xml"');
		echo ORDER_1C::getResponseXml('1');
	}
}

//возвращает данные заказа по его ID
if (isset($_GET["action"]) && $_GET["action"] == "getid" && (int)$_GET["id"] > 0) {
	$id = (int)$_GET["id"];
	$order1c = new ORDER_1C;
	if ($arOrder = $order1c->getOrderArray($id)) {
		$arOrders[] = $arOrder;
		header('Content-Type: application/xml');
		header('Content-Disposition: attachment; filename="export.xml"');
		echo $order1c->getOrderXml($arOrders);
	}
}

//возвращает данные всех заказов
if (isset($_GET["action"]) && $_GET["action"] == "getall") {
	$order1c = new ORDER_1C;
	if ($arOrders = $order1c->getAllOrdersArray(array())) {
		header('Content-Type: application/xml');
		header('Content-Disposition: attachment; filename="export.xml"');
		echo $order1c->getOrderXml($arOrders);
	}
}

//возвращает данные заказов со статусом "принят"
if (isset($_GET["action"]) && $_GET["action"] == "getnew") {
	$order1c = new ORDER_1C;
	$arFilter = array("STATUS_ID" => "N", "CANCELED" => "N");
	if ($arOrders = $order1c->getAllOrdersArray($arFilter)) {
		header('Content-Type: application/xml');
		header('Content-Disposition: attachment; filename="export.xml"');
		echo $order1c->getOrderXml($arOrders);
	}
}

//устанавливает статус заказа
if (isset($_GET["action"]) && $_GET["action"] == "setstatus" && isset($_GET["id"]) && isset($_GET["status"])) {
	$order_id = abs((int)$_GET["id"]);
	$status = trim(strip_tags($_GET["status"]));
	header('Content-Type: application/xml');
	header('Content-Disposition: attachment; filename="response.xml"');
	if ($order_id && $status && CModule::IncludeModule("sale")) {
		if (CSaleOrder::StatusOrder($order_id, $status))
			echo ORDER_1C::getResponseXml('0');
		else
  			echo ORDER_1C::getResponseXml('7');
	}
	else
		echo ORDER_1C::getResponseXml('8');
}

//возвращает список всех статусов заказа
if (isset($_GET["action"]) && $_GET["action"] == "getstatuslist") {
	header('Content-Type: application/xml');
	$order1c = new ORDER_1C;
	if ($arStatusList = $order1c->getStatusList()) {
		if ($statusXml = $order1c->getStatusListXml($arStatusList)) {
			header('Content-Disposition: attachment; filename="export.xml"');
			echo $statusXml;
		}
		else {
			header('Content-Disposition: attachment; filename="response.xml"');
			echo ORDER_1C::getResponseXml('2');
		}
	}
}

//возвращает список всех служб доставки
if (isset($_GET["action"]) && $_GET["action"] == "getdeliverylist") {
	header('Content-Type: application/xml');
	$order1c = new ORDER_1C;
	if ($arDeliveryList = $order1c->getDeliveryList()) {
		if ($deliveryXml = $order1c->getDeliveryListXml($arDeliveryList)) {
			header('Content-Disposition: attachment; filename="export.xml"');
			echo $deliveryXml;
		}
		else {
			header('Content-Disposition: attachment; filename="response.xml"');
			echo ORDER_1C::getResponseXml('2');
		}
	}
}

//возвращает список всех платежных систем
if (isset($_GET["action"]) && $_GET["action"] == "getpaylist") {
	header('Content-Type: application/xml');
	$order1c = new ORDER_1C;
	if ($arPaymentList = $order1c->getPaymentList()) {
		if ($paymentXml = $order1c->getPaymentListXml($arPaymentList)) {
			header('Content-Disposition: attachment; filename="export.xml"');
			echo $paymentXml;
		}
		else {
			header('Content-Disposition: attachment; filename="response.xml"');
			echo ORDER_1C::getResponseXml('2');
		}
	}
}

//добавляет новый заказ в Bitrix из 1C
if (isset($_GET["action"]) && $_GET["action"] == "addorder") {
	$order1c = new ORDER_1C;
	if ($uploadfile = $order1c->uploadFile()) {
		if ($arOrders = $order1c->parseXml2ArrayOrder($uploadfile)) {
			if ($order_id = $order1c->addOrder($arOrders[0])) {
				if ($arOrder = $order1c->getOrderArray($order_id)) {
					$arReturnOrders[] = $arOrder;
					header('Content-Type: application/xml');
					header('Content-Disposition: attachment; filename="export.xml"');
					echo $order1c->getOrderXml($arReturnOrders);
				}
			}
		}
	}
	else
		echo ORDER_1C::getResponseXml('6');
}

//обновляет данные заказа
if (isset($_GET["action"]) && $_GET["action"] == "updorder") {
	$order1c = new ORDER_1C;
	if ($uploadfile = $order1c->uploadFile()) {
		if ($arOrders = $order1c->parseXml2ArrayOrder($uploadfile)) {
			if ($order_id = $order1c->updOrder($arOrders[0])) {
				if ($arOrder = $order1c->getOrderArray($order_id)) {
					$arReturnOrders[] = $arOrder;
					header('Content-Type: application/xml');
					header('Content-Disposition: attachment; filename="export.xml"');
					echo $order1c->getOrderXml($arReturnOrders);
				}
			}
		}
	}
	else
		echo ORDER_1C::getResponseXml('6');
}
?>