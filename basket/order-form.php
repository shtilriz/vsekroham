<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
}
$arResult = array();
$obCache = new CPHPCache();
$cacheLifetime = 3600; $cacheID = "order.page"; $cachePath = "/".$cacheID; $IBLOCK_ID = 1;
if ($obCache->InitCache($cacheLifetime, $cacheID, $cachePath)) {
	$vars = $obCache->GetVars();
	$arResult = $vars["arResult"];
}
elseif ($obCache->StartDataCache()) {
	//данные покупателя
	$db_props = CSaleOrderProps::GetList(
		array("SORT" => "ASC"),
		array("PERSON_TYPE_ID" => 1, "PROPS_GROUP_ID" => 1),
		false,
		false,
		array()
	);
	while ($arProps = $db_props->Fetch()) {
		$arResult["USER_PROPS"][] = $arProps;
	}
	//способы доставки
	$db_dtype = CSaleDelivery::GetList(
		array("SORT" => "ASC"),
		array("LID"=>SITE_ID, "ACTIVE"=>"Y"),
		false,
		false,
		array()
	);
	while ($arDeliv = $db_dtype->Fetch()) {
		$dbRes = CSaleDelivery::GetDelivery2PaySystem(array("DELIVERY_ID" => $arDeliv["ID"]));
		while ($arRes = $dbRes->Fetch()) {
			//$arD2P[$ardeliv["ID"]][] = $arRes["PAYSYSTEM_ID"];
			$arResult["D2P"][$arDeliv["ID"]][] = $arRes["PAYSYSTEM_ID"];
		}
		$arResult["DELIVERY"][] = $arDeliv;
	}
	//адреса для самовывоза
	if (CModule::IncludeModule("iblock")) {
		$rsShops = CIBlockElement::GetList(
			array("SORT" => "ASC"),
			array(
				"IBLOCK_ID" => 7,
				"ACTIVE" => "Y",
				"!PROPERTY_DELIVERY" => false
			),
			false,
			false,
			array("IBLOCK_ID", "ID", "NAME")
		);
		while ($obShops = $rsShops->GetNextElement()) {
			$arShops = $obShops->GetFields();
			$arShops["PROPERTIES"] = $obShops->GetProperties();
			$arResult["SHOPS"][] = $arShops;
		}
	}
	//способы оплаты
	$db_ptype = CSalePaySystem::GetList(
		array("SORT" => "ASC"),
		array(
			"SID" => SITE_ID,
			"ACTIVE" => "Y",
			"PSA_PERSON_TYPE_ID" => 1
		),
		false,
		false,
		array()
	);
	while ($arPay = $db_ptype->Fetch()) {
		$arResult["PAYMENT"][] = $arPay;
	}
	$obCache->EndDataCache(array("arResult" => $arResult));
}
?>

<h1>Оформление заказа</h1>

<div class="before-ordering-box">
	<a href="/basket/">Вернуться в корзину</a>
</div>
<div class="ordering-box">
	<form action="order-completed.php" method="post" name="orderForm">
		<?if (!empty($arResult["USER_PROPS"])):?>
		<div class="box-content top-controls" id="user-props">
			<table class="like-inline">
				<tbody>
					<?foreach ($arResult["USER_PROPS"] as $key => $arProp):?>
						<tr>
							<td class="first<?=($arProp["SIZE2"] > 1?' v-top':'')?>"><label class="control-label text-right block-label"><?=$arProp["NAME"]?></label></td>
							<td class="second">
								<?if ($arProp["TYPE"] == "TEXT"):?>
									<?if ($arProp["SIZE2"] > 1):?>
										<textarea class="form-control" name="USER_PROP[<?=$arProp["CODE"]?>]" rows="<?=$arProp["SIZE2"]?>"></textarea>
									<?else:?>
										<input class="form-control" type="text" name="USER_PROP[<?=$arProp["CODE"]?>]" value="<?=($arProp["CODE"] == "CITY"?getYourCity():'')?>" />
									<?endif;?>
								<?endif;?>
								<?if ($arProps["TYPE"] == "CHECKBOX"):?>
									<input type="checkbox" name="USER_PROP[<?=$arProp["CODE"]?>]">
								<?endif;?>
							</td>
							<td class="error-cont"></td>
						</tr>
						<?if ($arProp["CODE"] == "EMAIL"):?>
							<tr>
								<td class="first"><label class="control-label text-right block-label">&nbsp;</label></td>
								<td class="second">
									<label><input type="checkbox" name="subscribe" value="Y" checked> Получать скидки и предложения на E-mail</label>
								</td>
								<td class="error-cont"></td>
							</tr>
						<?endif;?>
					<?endforeach;?>
				</tbody>
			</table>
		</div>
		<?endif;?>

		<div class="box-heading">Способ доставки</div>
		<div class="box-content">
			<table class="like-inline">
				<tbody>
					<?if (getSummBasket() > 2000):?>
						<tr>
							<td class="first"></td>
							<td class="second">
								<label class="control-label">
									<input type="radio" name="delivery" value="2"<?=(!isset($_SESSION["DELIVERY_CURRENT"])?' checked':'')?>/>Доставка курьером по Москве
								</label>
							</td>
							<td>
								<input type="text" name="date-delivery_mkad" value="<?=date('d.m.Y',time()+86400)?>" class="form-control date-delivery">
								<i>Выберите дату</i>
							</td>
							<td class="text-right"><span class="text-green">БЕСПЛАТНО</span></td>
						</tr>
						<tr>
							<td class="first"></td>
							<td class="second">
								<label class="control-label">
									<input type="radio" name="delivery" value="4" />Доставка курьером за пределы МКАД
								</label>
							</td>
							<td>
								<input type="text" name="date-delivery_zamkad" value="<?=date('d.m.Y',time()+86400)?>" class="form-control date-delivery">
								<i>Выберите дату</i>
							</td>
							<td class="text-right"><span class="text-green">25 руб за км</span></td>
						</tr>
					<?else:?>
						<tr>
							<td class="first"></td>
							<td class="second">
								<label class="control-label">
									<input type="radio" name="delivery" value="1"<?=(empty($_SESSION["DELIVERY_CURRENT"])?' checked':'')?>/>Доставка курьером по Москве
								</label>
							</td>
							<td>
								<input type="text" name="date-delivery_mkad" value="<?=date('d.m.Y',time()+86400)?>" class="form-control date-delivery">
								<i>Выберите дату</i>
							</td>
							<td class="text-right"><span class="text-green">350 руб</span></td>
						</tr>
						<tr>
							<td class="first"></td>
							<td class="second">
								<label class="control-label">
									<input type="radio" name="delivery" value="3" />Доставка курьером за пределы МКАД
								</label>
							</td>
							<td>
								<input type="text" name="date-delivery_zamkad" value="<?=date('d.m.Y',time()+86400)?>" class="form-control date-delivery">
								<i>Выберите дату</i>
							</td>
							<td class="text-right"><span class="text-green">350 руб + 25 руб за км</span></td>
						</tr>
					<?endif;?>
					<tr>
						<td class="first"></td>
						<td class="second">
							<label class="control-label">
								<input type="radio" name="delivery" value="6"<?=(!empty($_SESSION["DELIVERY_CURRENT"])?' checked':'')?>>Доставка транспортной компанией
							</label>
						</td>
						<td colspan="2" class="text-right">
							<?if (isset($_SESSION["DELIVERY_CURRENT"])):?>
							<div id="deliveryCalc2Product" class="order-delivery-row" data-weight="<?=$_SESSION["DELIVERY_CURRENT"]["weight"]?>" data-width="<?=$_SESSION["DELIVERY_CURRENT"]["width"]?>" data-length="<?=$_SESSION["DELIVERY_CURRENT"]["length"]?>" data-height="<?=$_SESSION["DELIVERY_CURRENT"]["height"]?>" data-price="<?=getSummBasket()?>" data-page="order"></div>
							<?else:?>
							<span class="text-green">РАСЧИТЫВАЕТСЯ ИНДИВИДУАЛЬНО</span>
							<?endif;?>
						</td>
					</tr>

					<tr>
						<td class="first"></td>
						<td class="second">
							<label class="control-label">
								<input type="radio" name="delivery" value="5">Самовывоз со склада или магазина
							</label>
						</td>
						<td colspan="2" class="text-right"><span class="text-green">БЕСПЛАТНО</span></td>
					</tr>
					<?if (!empty($arResult["SHOPS"])):?>
					<tr class="toggle-content">
						<td colspan="4">
							<div class="box1">
								<div class="box1__title">
									Выберите пункт выдачи, в котором вам будет удобно забрать заказ
								</div>

								<div class="clearfix">
									<div class="address-list address-list--bullets">
										<div class="address-list__title">Адреса списком</div>
										<ul class="address-list__content pager">
											<?foreach ($arResult["SHOPS"] as $key => $arItem):?>
												<li class="address-list__item" data-id="<?=$arItem["ID"]?>" data-name="<?=$arItem["NAME"]?>" data-street="<?=$arItem["PROPERTIES"]["ADDRESS"]["VALUE"]?>" data-lat="<?=$arItem["PROPERTIES"]["LATITUDE"]["VALUE"]?>" data-lon="<?=$arItem["PROPERTIES"]["LONGITUDE"]["VALUE"]?>" data-zoom="<?=$arItem["PROPERTIES"]["ZOOM"]["VALUE"]?$arItem["PROPERTIES"]["ZOOM"]["VALUE"]:'16'?>" class="carousel__slide"><a href="#<?=$key?>"><?=$arItem["NAME"]?></a></li>
											<?endforeach;?>
										</ul>
										<?/*<div class="address-list__footer">
											Вы выбрали пункт выдачи
										</div>*/?>
									</div>
									<div class="address-map">
										<input type="hidden" name="ADDRESS_SHOP" value="">
										<div class="address-map__title">Адреса на карте</div>
										<div class="address-map__content" id="shop-map"></div>
										<?/*<div class="address-map__footer clearfix">
											<div class="w100" id="select-address">
												<?//=$arResult["SHOPS"][0]["PROPERTIES"]["ADDRESS"]["VALUE"]?>
											</div>
										</div>*/?>
									</div>
								</div>
								<?if (0 < intval($arResult["SHOPS"][0]["PROPERTIES"]["LATITUDE"]["VALUE"]) || 0 < intval($arResult["SHOPS"][0]["PROPERTIES"]["LONGITUDE"]["VALUE"])):
									$arItem = $arResult["SHOPS"][0];?>
									<script>
										$(function() {
											var map = ymaps.ready(init);
											var myMap;
											function init() {
												myMap = new ymaps.Map ('shop-map', {
													center: [55.72479891,37.64696100],
													zoom: 10
												});

												<?foreach ($arResult["SHOPS"] as $key => $arEl):?>
													myPlacemark<?=$arEl["ID"]?> = new ymaps.Placemark([<?=$arEl["PROPERTIES"]["LATITUDE"]["VALUE"]?>,<?=$arEl["PROPERTIES"]["LONGITUDE"]["VALUE"]?>], {
														hintContent: '<?=$arEl["NAME"]?>',
														balloonContent: '<?=$arEl["NAME"]?>'
													});
													myMap.geoObjects.add(myPlacemark<?=$arEl["ID"]?>);
												<?endforeach?>

												myMap.controls.add(
													new ymaps.control.ZoomControl()
												);
												myMap.controls.add('mapTools');
												myMap.controls.add('typeSelector');
											}
											$('.address-list__content li').on('click', function(e) {
												e.preventDefault();
												var el = $(this),
													lat = el.data('lat'),
													lon = el.data('lon'),
													zoom = el.data('zoom'),
													address = el.data('street');
												if (!el.hasClass('selected')) {
													el.addClass('selected').siblings().removeClass('selected');
													myMap.setCenter([lat,lon], zoom, {duration: 800});
													//$('#select-address').text(address);
													$('input[name=ADDRESS_SHOP]').val(address);
												}
											});
										});
									</script>
								<?endif;?>
							</div>
						</td>
					</tr>
					<?endif;?>

				</tbody>
			</table>
		</div>

		<?if (!empty($arResult["PAYMENT"])):?>
		<div class="box-heading">Способы оплаты</div>
		<div class="box-content" id="payments-block">
			<div id="has-err">
				<div class="text-danger" style="display:none;">Выберите пожалуйста способ оплаты</div>
				<table class="like-inline">
					<tbody>
						<?foreach ($arResult["PAYMENT"] as $key => $arPay):?>
						<tr>
							<td class="first"></td>
							<td class="second">
								<label class="control-label">
									<input name="PAY_SYSTEM_ID" type="radio" value="<?=$arPay["ID"]?>"><?=$arPay["NAME"]?>
								</label>
							</td>
							<td></td>
						</tr>
						<?endforeach;?>
					</tbody>
				</table>
			</div>
		</div>
		<?endif;?>

		<div class="box-heading">Дополнительная информация</div>
		<div class="box-content">
			<table class="like-inline">
				<tbody>
					<tr>
						<td class="first"></td>
						<td class="" colspan="3">
							<textarea class="form-control" name="moreinfo" rows="6"></textarea>
							<?/*<span class="help-block">В какое время лучше доставить и т. п.</span>*/?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<?/*<div class="box-heading">Итоговая сумма</div>*/?>
		<div class="box-content">
			<table class="like-inline like-inline-lead">
				<tbody>
					<tr>
						<td class="first"></td>
						<td colspan="3">
							<?/*
							<div class="itogo">
								<div class="clearfix box-price">
									<span>Стоимость заказа: </span>
									<span class="item-price"><?=SaleFormatCurrency(getSummBasket(), "RUB");?></span>
								</div>
								<div class="clearfix box-price" id="eDeliveryTotalPrice" style="display: none;">
									<span><small>Стоимость доставки:</small> </span>
									<span class="item-price"></span>
								</div>
							</div>*/?>
							<div><button type="submit" class="ordering-box__submit">Подтвердить оформление</button></div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</form>
</div>

<script type="text/javascript">
	var D2P = new Object;
	D2P = <?=json_encode($arResult["D2P"])?>;
</script>