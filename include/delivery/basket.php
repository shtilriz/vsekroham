<?if (!empty($arCurrent)):?>
<div class="delivery-row__img">
	<?if (file_exists($_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH.'/images/delivery_img/'.$arCurrent["id"].'.gif')):?>
	<img src="<?=SITE_TEMPLATE_PATH?>/images/delivery_img/<?=$arCurrent["id"]?>.gif">
	<?endif;?>
</div>
<?endif;?>
<div class="delivery-row__content" id="edostDeliveryPrice" data-price="<?=$arCurrent["price"]?$arCurrent["price"]:''?>">
	<div class="delivery-row__input">
		<span class="muted delivery-sity"><?=($yourCity=='Москва')?'Доставка по городу':'Доставка до города'?>:</span>
		<input type="text" name="YOUR_CITY_PRODUCT" value="<?=($_SESSION["YOUR_CITY"]?$_SESSION["YOUR_CITY"]:'');?>" class="selected-sity sity-change-input" data-city="<?=($_SESSION["YOUR_CITY"]?$_SESSION["YOUR_CITY"]:'');?>" disabled/>
		<div class="address-dropdown">
			<div class="address-dropdown__list-wrapper" id="cityBlock2Product">

			</div>
		</div>
	</div>
	<span class="muted">Если это не Ваш город, нажмите: </span>
	<span class="">
		<a class="more-link js-sity-change" href="#">Выбрать другой город</a> <span class="icon icon-marker"></span>
	</span>
	<?if (!in_array($yourCity, $arCity)):?>
		<div class="nothing-found">
			<span class="icon icon-warning"></span>
			<span class="muted">Мы не нашли Ваш город. Стоимость доставки будет рассчитана индивидуально, оформляйте заказ!</span>
		</div>
	<?elseif (empty($arCurrent)):?>
		<div class="nothing-found">
			<span class="icon icon-warning"></span>
			<span class="muted">Стоимость доставки будет рассчитана индивидуально, оформляйте заказ!</span>
		</div>
	<?else:?>
		<span class="delivery-courier block-level"><?=$arCurrent["company"]?> <?=($arCurrent["day"]?'<span class="muted">('.$arCurrent["day"].')</span>':'')?></span>
	<?endif;?>
	<div class="bl-calc__show-more">
		<?if (count($arReturn) > 1):?>
		<a class="more-link" data-target="popup-calc" href="#">Выбрать другой доступный способ доставки</a>
		<?endif;?>
	</div>
</div>

<div class="muted">
	<div class="delivery-info">* После оформления заказа мы подберем самые выгодные условия доставки. Список транспортных компаний не полный. В стоимость доставки не включена обрешетка (жесткая упаковка), необходимая для мебели и хрупких товаров.</div>
</div>