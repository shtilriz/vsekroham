<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Оформление заказа");
CModule::IncludeModule("iblock");
CModule::IncludeModule("sale");
global $USER;

$arUserProps = array();
foreach ($_POST["USER_PROP"] as $key => $value) {
	$arUserProps[trim(strip_tags($key))] = trim(strip_tags($value));
}
$DELIVERY = (int)$_POST["delivery"];
$PAY_SYSTEM_ID = (int)$_POST["PAY_SYSTEM_ID"];
$USER_DESCRIPTION = trim(strip_tags($_POST["moreinfo"]));

if (strlen($arUserProps["FIO"]) && strlen($arUserProps["EMAIL"]) && strlen($arUserProps["PHONE"]) && strlen($arUserProps["CITY"]) && strlen($arUserProps["ADDRESS"]) && $DELIVERY && $PAY_SYSTEM_ID) {
	$bUserNew = false;
	$ID_USER = 0;
	if (!$USER->IsAuthorized()) {
		$filter = array("EMAIL" => $arUserProps["EMAIL"], "GROUPS_ID" => array(6));
		$rsUsers = CUser::GetList(($by="id"), ($order="asc"), $filter); // выбираем пользователей
		$is_filtered = $rsUsers->is_filtered; // отфильтрована ли выборка ?
		if (intval($rsUsers->SelectedRowsCount()) > 0) {	//Если в базе есть юзер с таким емейлом, то берем его ID
			if ($arUser = $rsUsers->Fetch()) {
				$ID_USER = $arUser["ID"];
				/*if (!$USER->IsAdmin()) {
					$user = new CUser;
					$arFieldsUser["NAME"] = $_POST["USER_PROP"]["FIO"];
					$arFieldsUser["PERSONAL_MOBILE"] = $_POST["USER_PROP"]["PHONE"];
					$arFieldsUser["PERSONAL_CITY"] = $_POST["USER_PROP"]["CITY"];
					$arFieldsUser["PERSONAL_STREET"] = $_POST["USER_PROP"]["ADDRESS"];

					$user->Update($ID_USER, $arFieldsUser);
				}*/
			}
		}
		else 	//иначе создаем нового пользователя
		{
			$bUserNew = true;
			$new_user = new CUser;
			$pass = substr(md5(uniqid(rand(),true)),0,10);
			$arFieldsUser = array(
				"EMAIL"             => $arUserProps["EMAIL"],
				"LOGIN"             => $arUserProps["EMAIL"],
				"ACTIVE"            => "Y",
				"GROUP_ID"          => array(6),
				"PASSWORD"          => $pass,
				"CONFIRM_PASSWORD"  => $pass
			);
			$arFieldsUser["NAME"] = $arUserProps["FIO"];
			$arFieldsUser["PERSONAL_MOBILE"] = $arUserProps["PHONE"];
			$arFieldsUser["PERSONAL_CITY"] = $arUserProps["CITY"];
			$arFieldsUser["PERSONAL_STREET"] = $arUserProps["ADDRESS"];

			$ID_USER = $new_user->Add($arFieldsUser);
		}
	}
	else {
		$ID_USER = $USER->GetID();
		/*$user = new CUser;
		$arFieldsUser["NAME"] = $_POST["USER_PROP"]["FIO"];
		$arFieldsUser["PERSONAL_MOBILE"] = $_POST["USER_PROP"]["PHONE"];
		$arFieldsUser["PERSONAL_STREET"] = $_POST["USER_PROP"]["ADDRESS"];

		$user->Update($ID_USER, $arFieldsUser);*/
	}

	//$USER->Authorize($ID_USER);

	$allSum = 0.0;
	$allWeight = 0.0;
	$discount = 0;
	$sostav = ''; //состав заказа для передачи в почтовое уведомление
	$basketList = '';
	$yaGoods = array();
	$arBasketItems = array();

	$rsBaskets = CSaleBasket::GetList(
		array("ID" => "ASC"),
		array(
			"FUSER_ID" => CSaleBasket::GetBasketUserID(),
			"LID" => SITE_ID,
			"ORDER_ID" => "NULL",
			"CAN_BUY" => "Y"
		),
		false,
		false,
		array(
			"ID", "NAME", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "DELAY", "CAN_BUY",
			"PRICE", "WEIGHT", "DETAIL_PAGE_URL", "NOTES", "CURRENCY", "VAT_RATE", "CATALOG_XML_ID",
			"PRODUCT_XML_ID", "SUBSCRIBE", "DISCOUNT_PRICE", "PRODUCT_PROVIDER_CLASS", "TYPE", "SET_PARENT_ID"
		)
	);
	while ($arItem = $rsBaskets->GetNext()) {
		$allSum += intval($arItem["PRICE"])*intval($arItem["QUANTITY"]);
		$allWeight += ($arItem["WEIGHT"] * $arItem["QUANTITY"]);
		$arBasketItems[] = $arItem;
	}

	if (!empty($arBasketItems)) {
		$arOrder = array(
			'SITE_ID' => SITE_ID,
			'USER_ID' => $USER_ID,
			'ORDER_PRICE' => $allSum,
			'ORDER_WEIGHT' => $allWeight,
			'BASKET_ITEMS' => $arBasketItems
		);
		$arOptions = array();
		$arErrors = array();
		CSaleDiscount::DoProcessOrder($arOrder, $arOptions, $arErrors);
		$arBasketItems = $arOrder['BASKET_ITEMS'];
	}

	$allSum = 0.0;
	foreach ($arBasketItems as $key => $arItem) {
		$allSum += intval($arItem["PRICE"])*intval($arItem["QUANTITY"]);
		$discount += intval($arItem["DISCOUNT_PRICE"])*intval($arItem["QUANTITY"]);

		if ($arItem["PRODUCT_ID"]) {
			$rsProduct = CIBlockElement::GetList(
				array("SORT" => "ASC"),
				array("ID" => $arItem["PRODUCT_ID"]),
				false,
				false,
				array("IBLOCK_ID", "ID", "NAME", "PREVIEW_PICTURE", "DETAIL_PAGE_URL", "PROPERTY_COLOR", "PROPERTY_SIZE")
			);
			if ($arProduct = $rsProduct->GetNext()) {
				$y=CFile::ResizeImageGet(
					$arProduct["PREVIEW_PICTURE"],
					array("width" => 95, "height" => 95),
					BX_RESIZE_IMAGE_EXACT,
					true
				);
				$arItem["IMG"] = $y["src"];
				$arItem["DETAIL_PAGE_URL"] = $arProduct["DETAIL_PAGE_URL"];
				$arItem["COLOR"] = $arProduct["PROPERTY_COLOR_VALUE"];
				$arItem["SIZE"] = $arProduct["PROPERTY_SIZE_VALUE"];

				$yaGoods[] = array(
					"ID" => $arProduct["ID"],
					"NAME" => $arProduct["NAME"],
					"PRICE" => $arItem["PRICE"],
					"QUANTITY" => $arItem["QUANTITY"]
				);
			}
		}
		$basketList .= '<tr>
			<td width="116"><a class="table-product__img" href="'.$arItem["DETAIL_PAGE_URL"].'" target="_blank"><img width="95" height="95" alt="'.$arItem["NAME"].'" src="'.$arItem["IMG"].'"></a></td>
			<td>
				<div class="table-product__content">
					<a class="table-product__title" href="'.$arItem["DETAIL_PAGE_URL"].'" target="_blank">'.$arItem["NAME"].'</a>
					'.($arItem["COLOR"]?'<span class="table-product__color">Цвет: '.$arItem["COLOR"].'</span>':'').'
					'.($arItem["SIZE"]?'<span class="table-product__color">Размер: '.$arItem["SIZE"].'</span>':'').'
				</div>
			</td>
			<td>'.intval($arItem["QUANTITY"]).'шт.</td>
			<td>
				<span class="table-product__price">'.SaleFormatCurrency($arItem["PRICE"],"RUB").'</span>
			</td>
		</tr>';

		$sostav .= '<tr>
			<td width="420" style="height: 41px;padding: 0 15px;vertical-align: middle;color: #007acf;border-top: 1px solid #e0dddd;border-bottom: 1px solid #e0dddd;"><a href="http://'.$_SERVER["SERVER_NAME"].$arItem["DETAIL_PAGE_URL"].'" style="color: #007acf;text-decoration: none;">'.$arItem["NAME"].'</a><br>
				<div style="color: #a3a3a3;font-size: 14px;line-height: 18px;margin-bottom: 5px;">
					'.($arItem["COLOR"]?'<span style="display: block;">Цвет: '.$arItem["COLOR"].'</span>':'').'
					'.($arItem["SIZE"]?'<span style="display: block;">Размер: '.$arItem["SIZE"].'</span>':'').'
				</div>
			</td>
			<td class="quantity" style="height: 41px;padding: 0 15px;vertical-align: middle;color: #7f8090;border-top: 1px solid #e0dddd;border-bottom: 1px solid #e0dddd;">'.intval($arItem["QUANTITY"]).'шт.</td>
			<td style="height: 41px;padding: 0 15px;vertical-align: middle;color: #007acf;border-top: 1px solid #e0dddd;border-bottom: 1px solid #e0dddd;">'.SaleFormatCurrency(intval($arItem["PRICE"])*intval($arItem["QUANTITY"]),"RUB").'</td>
		</tr>';
	}

	if ($allSum > 0) {
		if ($basketList) {
			$basketList = '<table class="table-o-block"><thead><tr><th colspan="4">Данные заказа</th></tr></thead><tbody>'.$basketList.'</tbody></table>';
		}

		$priceDeliv = 0;
		$nameDeliv = '';
		if ($DELIVERY) {
			$db_dtype = CSaleDelivery::GetList(
				array("SORT" => "ASC"),
				array(
					"LID" => SITE_ID,
					"ACTIVE" => "Y",
					"ID" => $DELIVERY
				),
				false,
				false,
				array()
			);
			if ($ardeliv = $db_dtype->Fetch()) {
				$priceDeliv = $ardeliv["PRICE"];
				$nameDeliv = $ardeliv["NAME"];
			}
		}
		$allSum = $allSum + $priceDeliv;

		if ($basketList) {
			$basketList .= '<table cellspacing="0" cellpadding="0" border="0" class="table-sum">
				<tbody>
					<tr>
						<td width="185"><span class="table-sum__title">Итоговая сумма'.($priceDeliv>0?' с учётом доставки':'').':</span></td>
						<td><span class="table-sum__price">'.number_format($allSum, 0, "", " ").' руб.</span></td>
					</tr>
				</tbody>
			</table>';
		}
		if ($sostav) {
			$sostav = '<table class="subtable2" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;width: 100%;border: 1px solid #e0dddd;margin-top: 10px;margin-bottom: 23px;">
		        <tbody>
					'.$sostav.'
				</tbody>
			</table>
			<span class="lead" style="font-size: 18px;color: #151734;">Стоимость заказа'.($priceDeliv>0?' с учётом доставки':'').': </span><span class="price" style="padding-left: 10px;font-size: 30px;color: #007acf;">'.SaleFormatCurrency($allSum,"RUB").'</span>';
		}

		$namePayment = '';
		if ($PAY_SYSTEM_ID) {
			$db_ptype = CSalePaySystem::GetList(
				array("SORT" => "ASC"),
				array(
					"SID" => SITE_ID,
					"ACTIVE" => "Y",
					"PSA_PERSON_TYPE_ID" => 1,
					"ID" => $PAY_SYSTEM_ID
				),
				false,
				false,
				array()
			);
			if ($ptype = $db_ptype->Fetch()) {
				$namePayment = $ptype["NAME"];
			}
		}

		//данные пользователя
		$user_data = '';
		foreach ($arUserProps as $prop => $value) {
			$arSort = array("SORT"=>"ASC");
			$arFilter = array("PERSON_TYPE_ID"=>1,"CODE"=>$prop);
			$db_props = CSaleOrderProps::GetList($arSort,$arFilter,false,false,array());
			if ($arProps = $db_props->Fetch())
			{
				$user_data .= '<tr>
					<td class="first" width="120" style="padding: 6px 15px;text-align: right;">'.$arProps["NAME"].'</td>
					<td style="padding: 6px 15px;"><span class="strong" style="color: #151734;">'.$value.'</span></td>
				</tr>';
			}
		}
		if ($USER_DESCRIPTION) {
			$user_data .= '<tr>
				<td class="first" width="120" style="padding: 6px 15px;text-align: right;">Дополнительная информация</td>
				<td style="padding: 6px 15px;"><span class="strong" style="color: #151734;">'.$USER_DESCRIPTION.'</span></td>
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

		$arFields = array(
			"LID" => SITE_ID,
			"PERSON_TYPE_ID" => 1,
			"PAYED" => "N",
			"CANCELED" => "N",
			"STATUS_ID" => "N",
			"PRICE" => $allSum,
			"CURRENCY" => "RUB",
			"USER_ID" => $ID_USER,
			"PAY_SYSTEM_ID" => $PAY_SYSTEM_ID,
			"PRICE_DELIVERY" => $priceDeliv,
			"DELIVERY_ID" => $DELIVERY,
			"DISCOUNT_VALUE" => $discount,
			"TAX_VALUE" => 0.0,
			"USER_DESCRIPTION" => $USER_DESCRIPTION
		);

		if ($ORDER_ID = CSaleOrder::Add($arFields)) {

			if ($USER_ID == 3) {
				if ($_SERVER['HTTP_X_FORWARDED_FOR']) {
					$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
				}
				else {
					$ip = $_SERVER["REMOTE_ADDR"];
				}
				$mess2log = "Сделан заказ №$ORDER_ID с IP $ip пользователем ID=$USER_ID";
				AddMessage2Log($mess2log);
			}

			CSaleBasket::OrderBasket($ORDER_ID, $_SESSION["SALE_USER_ID"], SITE_ID);
			//Если введены свойства заказа, добавляем их к заказу
			$arSort = array("SORT"=>"ASC");
			$arFilter = array("PERSON_TYPE_ID"=>1,"PROPS_GROUP_ID"=>array(1,2));
			$db_props = CSaleOrderProps::GetList($arSort,$arFilter,false,false,array());
			$propsList = '';$propsEventList = '';
			while ($props = $db_props->Fetch())
			{
				if (isset($_POST[$props["CODE"]]) && $_POST[$props["CODE"]] != '') {
					if ($_POST["delivery"] != 3 && $props["CODE"] == "ADDRESS_SHOPS")
						continue;
					$arFields = array(
						"ORDER_ID" => $ORDER_ID,
						"ORDER_PROPS_ID" => $props["ID"],
						"NAME" => $props["NAME"],
						"CODE" => $props["CODE"],
						"VALUE" => $_POST[$props["CODE"]]
					);
					CSaleOrderPropsValue::Add($arFields);
				}
				if (isset($arUserProps[$props["CODE"]]) && $arUserProps[$props["CODE"]] != '') {
					$arFields = array(
						"ORDER_ID" => $ORDER_ID,
						"ORDER_PROPS_ID" => $props["ID"],
						"NAME" => $props["NAME"],
						"CODE" => $props["CODE"],
						"VALUE" => $arUserProps[$props["CODE"]]
					);
					CSaleOrderPropsValue::Add($arFields);
				}
				if (isset($_POST["date-delivery_mkad"]) && strlen($_POST["date-delivery_mkad"]) > 0 && in_array($_POST["delivery"], array(1,2))) {
					$arFields = array(
						"ORDER_ID" => $ORDER_ID,
						"ORDER_PROPS_ID" => 6,
						"NAME" => "Дата доставки",
						"CODE" => "DELIVERY_DATE",
						"VALUE" => $_POST["date-delivery_mkad"]
					);
					CSaleOrderPropsValue::Add($arFields);
				}
				if (isset($_POST["date-delivery_zamkad"]) && strlen($_POST["date-delivery_zamkad"]) > 0 && in_array($_POST["delivery"], array(3,4))) {
					$arFields = array(
						"ORDER_ID" => $ORDER_ID,
						"ORDER_PROPS_ID" => 6,
						"NAME" => "Дата доставки",
						"CODE" => "DELIVERY_DATE",
						"VALUE" => $_POST["date-delivery_zamkad"]
					);
					CSaleOrderPropsValue::Add($arFields);
				}
				if (isset($_POST["ADDRESS_SHOP"]) && strlen($_POST["ADDRESS_SHOP"]) > 0 && $_POST["delivery"] == 5) {
					$arFields = array(
						"ORDER_ID" => $ORDER_ID,
						"ORDER_PROPS_ID" => 7,
						"NAME" => "Адрес магазина для самовывоза",
						"CODE" => "ADDRESS_SHOP",
						"VALUE" => $_POST["ADDRESS_SHOP"]
					);
					CSaleOrderPropsValue::Add($arFields);
				}
			}
			//формируем массив для почтового уведомления
			$arEventFields = array(
				"EMAIL" => $arUserProps["EMAIL"],
				"LOGIN" => $arUserProps["EMAIL"],
				"ORDER_USER" => $arUserProps["FIO"],
				"ORDER_DATE" => date('d.m.Y'),
				"ORDER_ID" => $ORDER_ID,
				"ORDER_LIST" => $sostav,
				"DELIVERY" => $nameDeliv,
				"PAYMENT" => $namePayment,
				"DATA" => $user_data,
				"PRICE" => SaleFormatCurrency($allSum+$priceDeliv,"RUB"),
				"SALE_EMAIL" => COption::GetOptionString("sale", "order_email", "order@".$SERVER_NAME)
			);
			if (isset($_POST["ADDRESS_SHOP"]) && strlen($_POST["ADDRESS_SHOP"]) > 0 && $DELIVERY == 5) {
				$arEventFields["DELIVERY"] = $arEventFields["DELIVERY"].' ('.$_POST["ADDRESS_SHOP"].')';
			}
			ob_start();
			CEvent::Send("SALE_NEW_ORDER", "s1", $arEventFields);
			ob_end_clean();
			?>

			<h1>Спасибо за покупку!</h1>
			<div class="b-info">
				<span class="icon icon-info"></span>Ваш заказ <b>№<?=$ORDER_ID?></b> от <?=date('d.m.Y')?> успешно оформлен! Ожидайте, вскоре наш менеджер свяжется с вами.
			</div>

			<?=$basketList;?>

			<div class="o-block">
				<div class="o-block__header">Данные покупателя</div>
				<div class="o-block__inner">
					<table cellspacing="0" cellpadding="0" border="0" class="table-3">
						<tbody>
							<?if ($arUserProps["FIO"]):?>
							<tr>
								<td width="165">ФИО</td>
								<td><?=$arUserProps["FIO"]?></td>
							</tr>
							<?endif;?>
							<?if ($arUserProps["PHONE"]):?>
							<tr>
								<td width="165">Мобильный телефон</td>
								<td><?=$arUserProps["PHONE"]?></td>
							</tr>
							<?endif;?>
							<?if ($arUserProps["EMAIL"]):?>
							<tr>
								<td width="165">E-mail</td>
								<td><?=$arUserProps["EMAIL"]?></td>
							</tr>
							<?endif;?>
							<?if ($arUserProps["CITY"]):?>
							<tr>
								<td width="165">Город</td>
								<td><?=$arUserProps["CITY"]?></td>
							</tr>
							<?endif;?>
							<?if ($arUserProps["ADDRESS"]):?>
							<tr>
								<td width="165">Адрес</td>
								<td><?=$arUserProps["ADDRESS"]?></td>
							</tr>
							<?endif;?>
							<?if ($nameDeliv):?>
							<tr>
								<td width="165">Способ доставки</td>
								<td><?=$nameDeliv?></td>
							</tr>
							<?endif;?>
							<?if ($namePayment):?>
							<tr>
								<td width="165">Способ оплаты</td>
								<td><?=$namePayment?></td>
							</tr>
							<?endif;?>
							<?if ($USER_DESCRIPTION):?>
							<tr>
								<td width="165">Доплнительная инф.</td>
								<td><?=$USER_DESCRIPTION?></td>
							</tr>
							<?endif;?>
						</tbody>
					</table>
				</div>
			</div>

			<a href="/">Вернуться на главную страницу</a>

			<?
			if (!empty($yaGoods)) {
				$yaScriptG = '';
				$orderSumm = number_format($allSum, 2, '.', '');
				foreach ($yaGoods as $key => $good) {
					$quan = intval($good["QUANTITY"]);
					$yaScriptG .= "{
						id: {$good["ID"]},
						name: \"{$good["NAME"]}\",
						price: {$good["PRICE"]},
						quantity: {$quan}
					}";
					if (($key+1)<count($yaGoods)) {
						$yaScriptG .= ',';
					}
				}
				$GLOBALS["YAPARAMS"] = "var yaParams = {
					order_id: {$ORDER_ID},
					order_price: {$orderSumm},
					currency: \"RUR\",
					exchange_rate: 1,
					goods: [{$yaScriptG}]
				};";
			}
			?>
			<script type="text/javascript">
			rrApiOnReady.push(function() {
				try {
					rrApi.order({
						transaction: <?=$ORDER_ID?>,
						items: [
							<?foreach ($yaGoods as $key => $good):
								$PRODUCT_ID = $good["ID"];
								$mxResult = CCatalogSku::GetProductInfo($PRODUCT_ID);
								if (is_array($mxResult)) {
									$PRODUCT_ID = $mxResult['ID'];
								}?>
								{ id: <?=$PRODUCT_ID?>, qnt: <?=$good["QUANTITY"]?>,  price: <?=$good["PRICE"]?>},
							<?endforeach;?>
						]
					});
				} catch(e) {}
			})
			</script>
			<?
		}
		else {
			?>
			<div class="block-info">
				<span class="icon icon-info info-icon"></span>
				<div class="icon info-text">Возникла ошибка при оформлении заказа. Повторите попытку позже.</div>
			</div>
			<?
		}
	}
	else {
		?>
		<div class="block-info">
			<span class="icon icon-info info-icon"></span>
			<div class="icon info-text">У вас нет товаров в корзине.</div>
		</div>
		<?
	}
}
else {
	?>
	<div class="block-info">
		<span class="icon icon-info info-icon"></span>
		<div class="icon info-text">Вы не заполнили данные заказа. <a href="/basket/order.php">Перейти на форму оформления заказа</a></div>
	</div>
	<?
}
?>

<?if ($_REQUEST["subscribe"] == "Y" && isset($arUserProps["EMAIL"]) && filter_var($arUserProps["EMAIL"], FILTER_VALIDATE_EMAIL)):?>
	<script type="text/javascript">
		rrApiOnReady.push(function () { rrApi.setEmail('<?=$arUserProps["EMAIL"]?>'); });
	</script>
<?endif;?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
