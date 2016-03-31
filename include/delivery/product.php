<div class="bl-calc">
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
</div>