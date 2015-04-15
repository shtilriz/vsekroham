<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Оплата заказа");

$ORDER_ID = abs((int)$_GET["order"]);
if ($ORDER_ID && CModule::IncludeModule("sale")) {
	$arResult = array();
	$arFilter = Array(
		"ID" => $ORDER_ID
	);
	$rsOrder = CSaleOrder::GetList(array("DATE_INSERT" => "ASC"), $arFilter);
	if ($arOrder = $rsOrder->Fetch()) {
		$rsOrderProps = CSaleOrderPropsValue::GetOrderProps($arOrder["ID"]);
		while ($arProp = $rsOrderProps->Fetch()) {
			if (in_array($arProp["CODE"], array("MANAGER")))
				continue;
			$arOrder["PROPS"][$arProp["CODE"]] = $arProp;
		}
		$arResult = $arOrder;
		unset($arOrder);
	}

	$allSum = 0;
	$allWeight = 0;
	$dbBasketItems = CSaleBasket::GetList(
		array("ID" => "ASC"),
		array(
			"LID" => SITE_ID,
			"ORDER_ID" => $ORDER_ID,
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
	while ($arItem = $dbBasketItems->Fetch()) {
		$allSum += ($arItem["PRICE"] * $arItem["QUANTITY"]);
		$allWeight += ($arItem["WEIGHT"] * $arItem["QUANTITY"]);
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
				BX_RESIZE_IMAGE_PROPORTIONAL,
				true
			);
			$arItem["IMG"] = $y["src"];
			$arItem["DETAIL_PAGE_URL"] = $arProduct["DETAIL_PAGE_URL"];
			$arItem["COLOR"] = $arProduct["PROPERTY_COLOR_VALUE"];
			$arItem["SIZE"] = $arProduct["PROPERTY_SIZE_VALUE"];

		}
		$arResult["BASKET"][] = $arItem;
	}
	$arOrder = array(
		'SITE_ID' => SITE_ID,
		'USER_ID' => $arResult["USER_ID"],
		'ORDER_PRICE' => $allSum,
		'ORDER_WEIGHT' => $allWeight,
		'BASKET_ITEMS' => $arResult["BASKET"]
	);
	$arOptions = array();
	$arErrors = array();
	CSaleDiscount::DoProcessOrder($arOrder, $arOptions, $arErrors);

	foreach ($arOrder["BASKET_ITEMS"] as &$arOneItem) {
		if (array_key_exists('VAT_VALUE', $arOneItem))
			$arOneItem["PRICE_VAT_VALUE"] = $arOneItem["VAT_VALUE"];
		$allVATSum += roundEx($arOneItem["PRICE_VAT_VALUE"] * $arOneItem["QUANTITY"], SALE_VALUE_PRECISION);
		$arOneItem["PRICE_FORMATED"] = CCurrencyLang::CurrencyFormat($arOneItem["PRICE"], $arOneItem["CURRENCY"], true);

		$arOneItem["FULL_PRICE"] = $arOneItem["PRICE"] + $arOneItem["DISCOUNT_PRICE"];
		$arOneItem["FULL_PRICE_FORMATED"] = CCurrencyLang::CurrencyFormat($arOneItem["FULL_PRICE"], $arOneItem["CURRENCY"], true);

		$arOneItem["SUM"] = CCurrencyLang::CurrencyFormat($arOneItem["PRICE"] * $arOneItem["QUANTITY"], $arOneItem["CURRENCY"], true);

		if (0 < doubleval($arOneItem["DISCOUNT_PRICE"] + $arOneItem["PRICE"]))
		{
			$arOneItem["DISCOUNT_PRICE_PERCENT"] = $arOneItem["DISCOUNT_PRICE"]*100 / ($arOneItem["DISCOUNT_PRICE"] + $arOneItem["PRICE"]);
		}
		else
		{
			$arOneItem["DISCOUNT_PRICE_PERCENT"] = 0;
		}
		$arOneItem["DISCOUNT_PRICE_PERCENT_FORMATED"] = roundEx($arOneItem["DISCOUNT_PRICE_PERCENT"], SALE_VALUE_PRECISION)."%";
	}
	unset($arOneItem);

	$arResult["ITEMS"]["AnDelCanBuy"] = $arOrder["BASKET_ITEMS"];

	if ($arResult["PAYED"] == "N")
		$arResult["INVOICE"] = payKeeperGetInvoice($arResult);
	?>

	<?if ($arResult["PAYED"] == "N"):?>
		<div class="p-toggle toggle">
			<div class="toggle-header">
				<div class="toggle-title">
					Ваш заказ № 1256896 подтвержден менеджером. Вы можете произвести оплату.
				</div>
				<a href="#" class="toggle-link">Показать детали заказа</a>
			</div>
			<div class="toggle-content">
				 <?if (!empty($arResult["ITEMS"]["AnDelCanBuy"])):?> <?foreach ($arResult["ITEMS"]["AnDelCanBuy"] as $key => $arItem):?><?endforeach;?>
				<table class="table-o-block">
				<thead>
				<tr>
					<th colspan="4">
						Вы заказали
					</th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<td width="116">
						<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="table-product__img" target="_blank"><img src="<?=$arItem["IMG"]?>" alt="<?=$arItem["NAME"]?>">
						</a>
					</td>
					<td>
						<div class="table-product__content">
							<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="table-product__title" target="_blank"><?=$arItem["NAME"]?></a>
							<?if ($arItem["COLOR"]):?>
								<span class="table-product__color">Цвет: <?=$arItem["COLOR"]?></span>
							<?endif;?>
							<?if ($arItem["SIZE"]):?>
								<span class="table-product__color">Цвет: <?=$arItem["SIZE"]?></span>
							<?endif;?>
						</div>
					</td>
					<td>
						<?=(int)$arItem["QUANTITY"]?> шт.
					</td>
					<td>
						<?if (floatval($arItem["DISCOUNT_PRICE_PERCENT"]) > 0):?>
							<del><?=number_format($arItem["FULL_PRICE"]*$arItem["QUANTITY"], 0, '', ' ')?> р.</del>
						<?elseif ($arItem["PRICE_MARGIN"]["PRICE"] > 0):?>
							<del><?=number_format($arItem["PRICE_MARGIN"]["PRICE"], 0, '', ' ')?> р.</del>
						<?endif;?>
						<span class="table-product__price"><?=SaleFormatCurrency($arItem["PRICE"], "RUB")?></span>
					</td>
				</tr>
				</tbody>
				</table>
				 <?endif;?>
			</div>
		</div>
		<div class="content-inner">
			<table cellspacing="0" cellpadding="0" class="table-sum no-indent">
			<tbody>
			<tr>
				<td width="185">
					<span class="table-sum__title">Сумма к оплате: </span>
				</td>
				<td>
					<span class="table-sum__price"><?=SaleFormatCurrency($arResult["PRICE"], "RUB")?></span>
				</td>
			</tr>
			</tbody>
			</table>
			<div class="o-block">
				<div class="o-block__header">
					Выберите способ оплаты
				</div>
				<div class="payment-method payment-method--two-columns" id="payment-block">
					<div class="payment-method__item">
						<span class="payment-method__icon"><img src="<?=SITE_TEMPLATE_PATH?>/images/expense.png" height="36" width="26" alt=""></span>
						<input id="radio1" name="payment" type="radio" value="/basket/receipt.php?order=<?=$ORDER_ID?>" class="radiobox-styled payment-method__checkbox">
						<label for="radio1" class="payment-method__label">Квитанция в Банке</label>
						<div class="payment-method__info">Оплата заказа по квитанции в любом из банков Вашего города</div>
						<div class="payment-method__commission">Комиссия банка от 1,2%</div>
					</div>
					 <?if (isset($arResult["INVOICE"])):?>
					<div class="payment-method__item">
						<span class="payment-method__icon"><img src="<?=SITE_TEMPLATE_PATH?>/images/visa-mastercard.png" height="47" width="46" alt=""></span>
						<input id="radio2" name="payment" value="http://vsekroham.server.paykeeper.ru/bill/<?=$arResult["INVOICE"]?>" type="radio" class="radiobox-styled payment-method__checkbox">
						<label for="radio2" class="payment-method__label">Карта Visa/MasterCard</label>
						<div class="payment-method__info">Оплата пластиковыми картами Visa/MasterCard.<br>Прием платежей обеспечивает ОАО "Русский Стандарт"</div>
						<div class="payment-method__commission">Комиссия 0%</div>
					</div>
					 <?endif;?>
					<div class="payment-method__footer">
						<div class="row">
							<div class="col-xs-4">
								<span class="payment-method__footer-text">Платежная платформа PayKeeper</span>
							</div>
							<div class="col-xs-4">
								<a href="" class="btn btn-blue">Оплатить</a>
							</div>
							<div class="col-xs-4">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?else:?>
		<p>Ваш заказ уже оплачен!</p>
	<?endif;?>
	<?
}
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>