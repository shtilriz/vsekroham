<?if (!empty($arCurrent)):?>
<div class="delivery-row__img">
	<?if (file_exists($_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH.'/images/delivery_img/'.$arCurrent["id"].'.gif')):?>
	<img src="<?=SITE_TEMPLATE_PATH?>/images/delivery_img/<?=$arCurrent["id"]?>.gif">
	<?endif;?>
</div>
<?endif;?>

<div class="delivery-row__content" id="edostDeliveryPrice" data-price="<?=$arCurrent["price"]?$arCurrent["price"]:''?>">
	<input type="hidden" name="YOUR_CITY_PRODUCT" value="<?=($_SESSION["YOUR_CITY"]?$_SESSION["YOUR_CITY"]:'');?>" data-city="<?=($_SESSION["YOUR_CITY"]?$_SESSION["YOUR_CITY"]:'');?>"/>
	<?if (!in_array($yourCity, $arCity) || empty($arCurrent)):?>
		<span class="text-green">РАСЧИТЫВАЕТСЯ ИНДИВИДУАЛЬНО</span>
	<?else:?>
		<span class="delivery-courier"><?=$arCurrent["company"]?> <?=($arCurrent["day"]?'<span class="muted">('.$arCurrent["day"].')</span>':'')?></span>
		<span class="price"><?=SaleFormatCurrency($arCurrent["price"], "RUB");?></span>
	<?endif;?>
	<div class="bl-calc__show-more">
		<?if (count($arReturn) > 1):?>
		<a class="more-link" data-target="popup-calc" href="#">Изменить...</a>
		<?endif;?>
	</div>
</div>