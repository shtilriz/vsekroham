<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/include/arCity.php");
Cmodule::IncludeModule("sale");

mb_internal_encoding("UTF-8");
function mb_ucfirst($text) {
    return mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1);
}

if (isset($_REQUEST["city"]) && strlen($_REQUEST["city"]) > 0) {
	$yourCity = mb_ucfirst(clearStr($_REQUEST["city"]));
}
else {
	$yourCity = ($_SESSION["YOUR_CITY"]?$_SESSION["YOUR_CITY"]:getYourCity());
}
$yourCity = trim($yourCity);

//не показывать для Москвы и Московской области
if (in_array($yourCity, $arCityMoskowRegion))
	exit();

$arrCacheParams = [
	$yourCity,
	$_REQUEST['weight'],
	$_REQUEST['length'],
	$_REQUEST['width'],
	$_REQUEST['height']
];
$cache_id = md5(serialize($arrCacheParams));
$cache_dir = "/edost/" . $cache_id;
$obCache = new CPHPCache;

if($obCache->InitCache(3600*24, $cache_id, $cache_dir)) {
	$arReturn = $obCache->GetVars();
}
elseif ($obCache->StartDataCache()) {
	if ($yourCity && (int)$_REQUEST["weight"] > 0) {
		if ($curl = curl_init()) {
			$arPost = array(
				"id" => 4590,
				"p" => "CC5jGjKi9guSHltNUiNmZG7XQ3vRf2js",
				"to_city" => $yourCity,
				"weight" => (round($_REQUEST["weight"]/1000, 2)),
				"strah" => 0
			);
			if ((int)$_REQUEST["length"] > 0 && (int)$_REQUEST["width"] > 0 && (int)$_REQUEST["height"] > 0) {
				$arPost["ln"] = (int)$_REQUEST["length"];
				$arPost["wd"] = (int)$_REQUEST["width"];
				$arPost["hg"] = (int)$_REQUEST["height"];
			}
			curl_setopt($curl, CURLOPT_URL, 'http://www.edost.ru/edost_calc_kln.php');
			curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $arPost);
			$out = curl_exec($curl);
			curl_close($curl);

			$arReturn = array();
			$xml = new CDataXML();
			$xml->LoadString($out);

			$arData = $xml->GetArray();
			foreach ($arData["rsp"]["#"]["tarif"] as $key => $arItem) {
				$id = (int)$arItem["#"]["id"][0]["#"];
				$company = trim($arItem["#"]["company"][0]["#"]);
				$price = (int)$arItem["#"]["price"][0]["#"];
				$day = trim($arItem["#"]["day"][0]["#"]);
				$name = trim($arItem["#"]["name"][0]["#"]);
				$arReturn[$id] = array(
					"id" => $id,
					"company" => $company,
					"price" => $price,
					"day" => $day,
					"name" => $name
				);
			}
		}
	}
	$obCache->EndDataCache($arReturn);
}
foreach ($arReturn as $key => $arItem) {
	//если Курьер (тариф 1)
	if ($arItem["id"] == 31) {
		if ((int)$_REQUEST["price"] >= 2000) {
			$arReturn[$key]["name"] = "За каждый км. от МКАД";
			$arReturn[$key]["price"] = 25;
		}
		else {
			$arReturn[$key]["name"] = "350 р. + 25р. за каждый км от МКАД";
			$arReturn[$key]["price"] = 350;
		}
	}
	if ($arItem["id"] == 32) {
		if ((int)$_REQUEST["price"] < 2000) {
			$arReturn[$key]["name"] = "если сумма заказа ниже 2000 рублей";
			$arReturn[$key]["price"] = 350;
		}
	}

}
//ищем самый привлекательный вариант по цене
$deliveryID = 0;
if (isset($_SESSION["DELIVERY_CURRENT"]["id"]) && $_REQUEST["page"] == "order") {
	$deliveryID = $_SESSION["DELIVERY_CURRENT"]["id"];
}
if ($_REQUEST["deliveryID"]) {
	$deliveryID = (int)$_REQUEST["deliveryID"];
}
if (count($arReturn) > 0) {
	if ($deliveryID && array_key_exists($deliveryID, $arReturn)) {
		$arCurrent = $arReturn[$deliveryID];
	}
	else {
		$price = 999999;
		$arCurrent = array();
		foreach ($arReturn as $key => $arItem) {
			if ((int)$arItem["price"] < $price) {
				$arCurrent = $arItem;
				$price = $arItem["price"];
			}
			if ($price == 0)
				break;
		}
	}
}
if (!empty($arCurrent)) {
	$arCurrent["weight"] = (int)$_REQUEST["weight"];
	$arCurrent["width"] = (int)$_REQUEST["width"];
	$arCurrent["length"] = (int)$_REQUEST["length"];
	$arCurrent["height"] = (int)$_REQUEST["height"];
	$arCurrent["city"] = $yourCity;
	$_SESSION["DELIVERY_CURRENT"] = $arCurrent;
}
else {
	unset($_SESSION["DELIVERY_CURRENT"]);
}

if ($_REQUEST["page"] == "product") {
	include('product.php');
}
elseif ($_REQUEST["page"] == "basket") {
	include('basket.php');
}
elseif ($_REQUEST["page"] == "order") {
	include('order.php');
}
?>

<?/*<div class="bl-calc">
	<div class="bl-calc__sity-change js-sity-change">
		<a href="#" class="more-link">Изменить</a>
		<span class="icon icon-marker"></span>
	</div>
	<div class="bl-calc__label"><?=($yourCity=='Москва')?'Доставка по городу':'Доставка до города'?>:</div>
	<div class="bl-calc__input">
		<input class="sity-change-input" type="text" name="YOUR_CITY_PRODUCT" value="<?=($_SESSION["YOUR_CITY"]?$_SESSION["YOUR_CITY"]:'');?>" data-city="<?=($_SESSION["YOUR_CITY"]?$_SESSION["YOUR_CITY"]:'');?>" disabled>
		<div class="address-dropdown">
			<div class="address-dropdown__list-wrapper" id="cityBlock2Product">

			</div>
		</div>
	</div>
	<?if (!empty($arCurrent)):?>
		<?if (3500 <= (int)$arCurrent["price"] || !in_array($yourCity, $arCity)):?>
			<div class="bl-calc__show-more">Доставка рассчитывается индивидуально</div>
		<?else:?>
			<div class="bl-calc__delivery">

				<div class="bl-calc__delivery-img">
					<?if (file_exists($_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH.'/images/delivery_img/'.$arCurrent["id"].'.gif')):?>
					<img src="<?=SITE_TEMPLATE_PATH?>/images/delivery_img/<?=$arCurrent["id"]?>.gif">
					<?endif;?>
				</div>
				<?=$arCurrent["company"]?> - <span class="bl-calc__delivery-price"><?=$arCurrent["price"]?> р.</span> <?=($arCurrent["day"]?'('.$arCurrent["day"].')':'')?>
			</div>
		<?endif;?>
			<div class="bl-calc__show-more">
				<?if (count($arReturn) > 1):?>
				<a href="#" data-target="popup-calc" class="more-link">Показать все варианты</a>
				<?endif;?>
			</div>
	<?else:?>
		<div class="bl-calc__show-more">Рассчитывается индивидуально</div>
	<?endif;?>
</div>*/?>

<?//if (!empty($arReturn)):?>
<div class="popup popup-calc" id="popup-calc">
	<div class="popup__top">
		<a href="#" class="popup__close">X</a>
		<strong class="popup__title">Доставка товара в регион России</strong>
	</div>
	<div class="popup__content">
		<div class="b-calc">
			<div class="b-calc__top">
				<div class="b-calc__sity">
					Ваш город: <input class="sity-change-input" type="text" value="<?=($_SESSION["YOUR_CITY"]?$_SESSION["YOUR_CITY"]:'');?>" disabled>
					<div class="address-dropdown">
						<div class="address-dropdown__list-wrapper" id="cityBlock2ProductModal">

						</div>
					</div>
				</div>
				<div class="b-calc__sity-change js-sity-change"><a href="#" class="more-link">Изменить</a> <span class="icon icon-marker"></span></div>
				<?if (!in_array($yourCity, $arCityMoskowRegion)):?>
					<p>Список транспортных компаний не полный. После оформления заказа мы подберем для Вас оптимальный вариант доставки.</p>
				<?endif;?>
			</div>
			<div class="b-calc__list" id="cityModalList">
				<?if($_REQUEST["mode"] == "modal") $APPLICATION->RestartBuffer();?>
				<?if (!empty($arReturn)):?>
					<div class="scroll-pane js-scroll-pane">
					<?foreach ($arReturn as $key => $arItem):?>
						<a href="#" class="b-calc__list-item" data-id="<?=$arItem["id"]?>">
							<?if (file_exists($_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH.'/images/delivery_img/'.$arItem["id"].'.gif')):?>
								<span class="b-calc__icon"><img src="<?=SITE_TEMPLATE_PATH?>/images/delivery_img/<?=$arItem["id"]?>.gif" width="42"></span>
							<?endif;?>
							<div class="b-calc__list-content">
								<div class="b-calc__list-price"><?=($arItem["price"] <= 0?'<span>бесплатно!</span>':$arItem["price"].' р.')?></div>
								<div class="b-calc__list-title"><?=$arItem["company"].($arItem["name"]?' ('.$arItem["name"].')':'').($arItem["day"]?', <span>'.$arItem["day"].'</span>':'')?></div>
							</div>
						</a>
					<?endforeach;?>
					</div>
				<?else:?>
					<p>Уточните стоимость в магазине</p>
				<?endif;?>
				<?if($_REQUEST["mode"] == "modal") die();?>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
$(function() {
	var pane = $('#cityModalList .js-scroll-pane').jScrollPane({
		mouseWheelSpeed: 200
	});
});
</script>
