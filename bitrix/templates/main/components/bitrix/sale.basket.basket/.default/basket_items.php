<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
echo ShowError($arResult["ERROR_MESSAGE"]);

$bDelayColumn  = false;
$bDeleteColumn = false;
$bWeightColumn = false;
$bPropsColumn  = false;
$bPriceType    = false;

if ($normalCount > 0):?>
	<div class="cart__top">
		<a class="clear-cart" href="">Очистить корзину</a>
		<a class="back-to" href="/catalog/">Вернуться в каталог</a>
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
							<?/*<input id="item-count" name="item-count" type="text" value="1">*/?>

							<input type="text" name="QUANTITY_INPUT_<?=$arItem["ID"]?>" value="<?=$arItem["QUANTITY"]?>" data-price="<?=intval($arItem["PRICE"])?>" data-discount="<?=intval($arItem["DISCOUNT_PRICE"])?>" readonly="readonly" class="input-quan">
							<input type="hidden" id="QUANTITY_<?=$arItem['ID']?>" name="QUANTITY_<?=$arItem['ID']?>" value="<?=$arItem["QUANTITY"]?>" />

							<a class="increase" href="#"><span>+</span></a>
							<a class="decrease" href="#"><span>−</span></a>
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
						<span class="item-price item-price_type_old"><?=number_format($arItem["PRICE_MARGIN"]["PRICE"], 0, '', ' ')?> р.</span>
					<?endif;?>
					<span class="item-price"><?=number_format($arItem["PRICE"]*$arItem["QUANTITY"], 0, '', ' ')?> р.</span>
					</div>
					<?else:?>
						<span class="item-price" style="font-size: 16px;">нет в продаже</span>
					<?endif;?>
				</td>
				<?/*<td><a class="item-hold" href="#">Отложить</a></td>*/?>
				<td><a class="item-remove" href="#">Удалить</a><input type="checkbox" name="DELETE_<?=$arItem["ID"]?>" value="Y" style="display: none" class="productDel"></td>
			</tr>
			<?endif;?>
		<?endforeach;?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="4">
					<?/*
					if ($arParams["HIDE_COUPON"] != "Y"):
						$couponClass = "";
						if (array_key_exists('VALID_COUPON', $arResult))
						{
							$couponClass = ($arResult["VALID_COUPON"] === true) ? "good" : "bad";
						}
						elseif (array_key_exists('COUPON', $arResult) && !empty($arResult["COUPON"]))
						{
							$couponClass = "good";
						}?>
						<div class="form-field">
							<div class="form-field__inputtext coupon">
								<label for="coupon">Скидочный купон</label>
								<input id="coupon" class="inputtext <?=$couponClass?>" type="text" name="COUPON" value="<?=$arResult["COUPON"]?>"/>
							</div>
						</div>
					<?else:?>
						&nbsp;
					<?endif;*/?>
					<?$yourCity = ($_SESSION["YOUR_CITY"]?$_SESSION["YOUR_CITY"]:getYourCity());?>
					<?if ($yourCity == "Москва" && $arResult["allSum"] < 2000):?>
						Доставка заказов до 2000 руб платная - 350 руб
					<?/*elseif ($countNastella):
						if ($countNastella > 2)
							$countNastella = 2;
						$totalBonus = $countNastella * 2000;
						echo "Ваш неиспользованный бонус составляет: ".round($totalBonus-$arResult["DISCOUNT_PRICE_ALL"])." руб.<br/>Использовано: {$arResult["DISCOUNT_PRICE_ALL"]} руб.";*/
					endif;?>
				</td>
				<td colspan="3">
					<div class="itogo">
						<span>Итого:</span> <?=str_replace(" ", "&nbsp;", $arResult["allSum_FORMATED"])?>
					</div>
				</td>
			</tr>
		</tfoot>
	</table>
	<div class="form-field">
		<div class="form-field__button">
			<a href="/basket/order.php" class="form-button lg">Оформить заказ</a>
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