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
	<div class="b-basket-list">
		<?foreach ($arResult["GRID"]["ROWS"] as $k => $arItem):?>
			<?$arProductIDs[] = $arItem["PRODUCT_ID"];?>
			<div class="b-basket-list__item js-b-basket-list-item">
				<?$y=CFile::ResizeImageGet(
					$arItem["PREVIEW_PICTURE"],
					array("width" => 175, "height" => 175),
					BX_RESIZE_IMAGE_PROPORTIONAL,
					true
				);?>
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="b-basket-list__figure"><img src="<?=$y["src"]?>" alt="<?=$arItem["NAME"]?>" class="b-basket-list__img"/></a>
				<div class="b-basket-list__content">
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="b-basket-list__title"><?=$arItem["NAME"]?></a>
					<div class="b-basket-list__content-bottom">
						<?if (!empty($arItem["PROPS"])):?>
							<ul class="b-basket-list-opts">
								<?foreach ($arItem["PROPS"] as $keyProp => $arProp):?>
								<li class="b-basket-list-opts__item">
									<span class="b-basket-list-opts__type"><?=$arProp["NAME"]?>: </span><span class="b-basket-list-opts__name"><?=$arProp["VALUE"]?></span>
								</li>
								<?endforeach;?>
							</ul>
						<?endif;?>
					</div>
				</div>
				<div class="b-basket-list__aside">
					<a href="#" class="b-basket-list__item-close js-b-basket-list-item-close" data-product-id="<?=$arItem["PRODUCT_ID"]?>"></a>
					<input type="checkbox" name="DELETE_<?=$arItem["ID"]?>" value="Y" style="display: none" class="productDel">
					<?if ($arItem["CAN_BUY"] == "Y"):?>
						<div class="b-basket-list__discount">
						<?if ($arItem["DISCOUNT_PRICE_PERCENT"] > 0 && $arItem["CAN_BUY"] == "Y"):?>
							Скидка <?=$arItem["DISCOUNT_PRICE_PERCENT_FORMATED"]?>
						<?endif;?>
						</div>
						<?if ($arItem["CAN_BUY"] == "Y"):?>
							<div class="b-basket-list__quantity">
								<select name="QUANTITY_INPUT_<?=$arItem["ID"]?>" class="selectpicker col- quanSelect">
									<?for ($i=1; $i < 6; $i++) {
										echo '<option value="'.$i.'"'.($i==(int)$arItem["QUANTITY"]?' selected':'').'>'.$i.'</option>';
									}?>
								</select>
								<input type="hidden" id="QUANTITY_<?=$arItem['ID']?>" name="QUANTITY_<?=$arItem['ID']?>" value="<?=$arItem["QUANTITY"]?>" />
							</div>
						<?endif;?>
						<div class="b-basket-list__price">
							<?if (floatval($arItem["DISCOUNT_PRICE_PERCENT"]) > 0):?>
								<div class="b-basket-list__price-old"><?=SaleFormatCurrency($arItem["FULL_PRICE"]*$arItem["QUANTITY"], "RUB")?></div>
							<?elseif ($arItem["PRICE_MARGIN"]["PRICE"] > 0):?>
								<div class="b-basket-list__price-old"><?=SaleFormatCurrency($arItem["PRICE_MARGIN"]["PRICE"], "RUB")?></div>
							<?endif;?>
							<div class="b-basket-list__price-new"><?=SaleFormatCurrency($arItem["PRICE"]*$arItem["QUANTITY"], "RUB")?></div>
						</div>
					<?else:?>
						<div class="b-basket-list__price-new">нет в продаже</div>
					<?endif;?>
				</div>
			</div>
		<?endforeach;?>
	</div>

	<div class="b-basket-footer-summ"><span>Итого</span>
		<div class="b-basket-footer-summ__price"><?=str_replace(" ", "&nbsp;", $arResult["allSum_FORMATED"])?></div>
	</div>
	<a href="/basket/order.php?step=1" class="b-basket__submit">Оформить заказ</a>
<?else:?>
	<?=GetMessage("SALE_NO_ITEMS");?>
<?endif;?>

<?
$GLOBALS["GOOGLE_TAG_PARAMS"] = array(
	"ECOMM_PRODID" => (!empty($arProductIDs) ? (count($arProductIDs) > 1 ? "[".implode(", ", $arProductIDs)."]" : $arProductIDs[0]) : 0),
	"ECOMM_TOTALVALUE" => $arResult["allSum"]
);
?>