<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
require($_SERVER["DOCUMENT_ROOT"]."/include/arCity.php");
$yourCity = ($_SESSION["YOUR_CITY"]?$_SESSION["YOUR_CITY"]:getYourCity());

echo ShowError($arResult["ERROR_MESSAGE"]);

$bDelayColumn  = false;
$bDeleteColumn = false;
$bWeightColumn = false;
$bPropsColumn  = false;
$bPriceType    = false;
$arProductIDs = array();

if ($normalCount > 0):?>
	<div class="cart__top">
		<a class="cart__top-clear js-clear-cart" href="">Очистить корзину</a>
		<a class="back-to" href="/catalog/kolyaski/">Вернуться в каталог</a>
	</div>
	<table class="cart__list">
		<thead>
			<tr>
				<th class="item-img">&nbsp;</th>
				<th class="item-name">Название</th>
				<th class="item-discount">Скидка</th>
				<th class="item-count">Количество</th>
				<th class="item-price">Цена</th>
				<?/*<th class="item-hold">&nbsp;</th>*/?>
				<th class="item-remove">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
		<?foreach ($arResult["GRID"]["ROWS"] as $k => $arItem):?>
			<?
			$isGift = false; //является ли товар подарком
			foreach ($arItem["PROPS"] as $keyProp => $arProp) {
				if ($arProp['CODE'] == 'GIFT') {
					$isGift = true;
				}
			}
			$arProductIDs[] = $arItem["PRODUCT_ID"];?>
			<?if ($arItem["DELAY"] == "N"):?>
			<tr>
				<td>
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="item-img">
						<?$y=CFile::ResizeImageGet(
							$arItem["PREVIEW_PICTURE"],
							array("width" => 100, "height" => 200),
							BX_RESIZE_IMAGE_PROPORTIONAL,
							true
						);?>
						<img src="<?=$y["src"]?>" alt="<?=$arItem["NAME"]?>" width="<?=$y["width"]?>" height="<?=$y["height"]?>">
					</a>
				</td>
				<td>
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="item-name"><h2><?=$arItem["NAME"]?></h2></a>
					<?foreach ($arItem["PROPS"] as $keyProp => $arProp) {
						echo '<span class="item-color">'.$arProp["NAME"].': '.$arProp["VALUE"].'</span>';
					}?>
				</td>
				<td>
					<?if ($arItem["DISCOUNT_PRICE_PERCENT"] > 0 && $arItem["CAN_BUY"] == "Y"):?>
						<span class="item-discount"><?=$arItem["DISCOUNT_PRICE_PERCENT_FORMATED"]?></span>
					<?endif;?>
				</td>
				<td>
					<?if ($arItem["CAN_BUY"] == "Y"):?>
					<div class="item-count">
						<div class="form-field__inputtext plus-minus">
							<input type="text" name="QUANTITY_INPUT_<?=$arItem["ID"]?>" value="<?=$arItem["QUANTITY"]?>" data-price="<?=intval($arItem["PRICE"])?>" data-discount="<?=intval($arItem["DISCOUNT_PRICE"])?>" readonly="readonly" class="input-quan">
							<input type="hidden" id="QUANTITY_<?=$arItem['ID']?>" name="QUANTITY_<?=$arItem['ID']?>" value="<?=$arItem["QUANTITY"]?>" />

							<?if (!$isGift):?>
							<a class="increase" href="#"><span>+</span></a>
							<a class="decrease" href="#"><span>−</span></a>
							<?endif;?>
						</div>
					</div>
					<?endif;?>
				</td>
				<td>
					<?if ($arItem["CAN_BUY"] == "Y"):?>
					<div class="current_price" id="current_price_<?=$arItem["ID"]?>">
					<?if (floatval($arItem["DISCOUNT_PRICE_PERCENT"]) > 0):?>
						<span class="item-price item-price_type_old"><?=number_format($arItem["FULL_PRICE"]*$arItem["QUANTITY"], 0, '', ' ')?> р.</span>
					<?elseif ($arItem["PRICE_MARGIN"]["PRICE"] > 0):?>
						<span class="item-price item-price_type_old"><?=number_format($arItem["PRICE_MARGIN"]["PRICE"]*$arItem["QUANTITY"], 0, '', ' ')?> р.</span>
					<?endif;?>
					<span class="item-price"><?=number_format($arItem["PRICE"]*$arItem["QUANTITY"], 0, '', ' ')?> р.</span>
					</div>
					<?else:?>
						<span class="item-price" style="font-size: 16px;">нет в продаже</span>
					<?endif;?>
				</td>
				<td>
					<a class="item-remove" href="#" data-product-id="<?=$arItem["PRODUCT_ID"]?>"<?=($isGift?' style="display: none"':'')?>>Удалить</a>
					<input type="checkbox" name="DELETE_<?=$arItem["ID"]?>" value="Y" style="display: none" class="productDel">
				</td>
			</tr>
			<?endif;?>
		<?endforeach;?>
		</tbody>
		<tfoot>
			<tr data-role="delivery-row"><td colspan="7"><p>Доставка</p></td></tr>
			<tr class="delivery-row" data-role="delivery-row">
				<td colspan="4">
					<div id="deliveryCalc2Product" data-weight="<?=$arResult["TOTAL_WEIGHT"]?>" data-width="<?=$arResult["TOTAL_WIDTH"]?>" data-length="<?=$arResult["TOTAL_LENGTH"]?>" data-height="<?=$arResult["TOTAL_HEIGHT"]?>" data-price="<?=$arResult["allSum"]?>" data-page="basket"></div>
				</td>
				<td colspan="3" class="delivery-price" id="eDeliveryPrice"></td>
			</tr>
			<tr class="delivery-footer">
				<td colspan="4">
					<?if ($yourCity == "Москва" && $arResult["allSum"] < 2000):?>
						Доставка заказов до 2000 руб платная - 350 руб
					<?else:?>
						<div class="js-dropdown b-info-1" id="infoAboutDelivery">
							<div class="js-dropdown-toggle">
								<span class="b-info-1__icon"><span class="b-info-1__icon-info"></span></span>
								<div class="b-info-1__title">Информация о доставке</div>
							</div>
							<div class="b-info-1__drop-content b-dropdown-content b-dropdown-content--white js-dropdown-content">
								<p>В стоимость доставки не включена обрешетка (жесткая упаковка), необходимая для мебели и хрупких товаров. <br/><br/>После оформления заказа мы подберем самые выгодные условия доставки. Список транспортных компаний не полный.</p>
							</div>
						</div>
					<?endif;?>
				</td>
				<td colspan="3">
					<div class="itogo">
						<div class="clearfix">
							<span class="item-price"><?=str_replace(" ", "&nbsp;", $arResult["allSum_FORMATED"])?></span>
							<span>Стоимость заказа: </span>
						</div>
						<div class="clearfix" id="eDeliveryTotalPrice" style="display: none;">
							<span class="item-price"></span>
							<span><small>Стоимость доставки:</small> </span>
						</div>
					</div>
				</td>
			</tr>
		</tfoot>
	</table>

	<div class="row">
		<div class="col-xs-8">
			<div class="form-field js-promo b-promo has-error">
				<a href="#" class="more-link more-link-promo js-more-link-promo lead">Ввести промокод</a>
				<div class="form-field__inputtext b-promo__content js-promo-content"<?echo ($arResult['COUPON'] ? ' style="display: block;"' : '');?>>
					<input type="text" name="COUPON" value="<?echo ($arResult['COUPON'] ? $arResult['COUPON'] : '');?>" id="coupon" class="inputtext b-promo__control js-promo-control" />
					<button class="button-pink button-sm b-promo__submit js-basket-coupon-send">ok</button>
					<?/*<span class="help-text">Промокод не действителен</span>*/?>
				</div>
			</div>
		</div>
		<div class="col-xs-4">
			<div class="form-field">
				<div class="form-field__button">
					<a href="/basket/order.php" class="cart__submit">Оформить заказ</a>
				</div>
			</div>
		</div>
	</div>
<?else:?>
	<div id="basket_items_list">
		<table>
			<tbody>
				<tr>
					<td colspan="<?=$numCells?>" style="text-align:center">
						<div class=""><?=GetMessage("SALE_NO_ITEMS");?></div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
<?endif;?>

<?
$GLOBALS["GOOGLE_TAG_PARAMS"] = array(
	"ECOMM_PRODID" => (!empty($arProductIDs) ? (count($arProductIDs) > 1 ? "[".implode(", ", $arProductIDs)."]" : $arProductIDs[0]) : 0),
	"ECOMM_TOTALVALUE" => $arResult["allSum"]
);
?>

<?if ($_GET["dev"] == "Y"):?>
	<pre>
		<?var_dump($arResult);?>
	</pre>
<?endif?>