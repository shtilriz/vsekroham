<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);?>

<script src="//api-maps.yandex.ru/2.0/?load=package.full&lang=ru-RU" type="text/javascript"></script>

<?foreach($arResult["ITEMS"] as $arItem):
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));?>
	<div class="contact-box well" id="<?=$this->GetEditAreaId($arItem['ID']);?>" itemscope itemtype="http://schema.org/Organization">
		<h2 itemprop="name"><?=$arItem["NAME"]?></h2>
		<br>
		<div class="clearfix">
			<div class="item-map">
				<div class="item-map-inner" id="map<?=$arItem["ID"]?>" style="width: 263px; height: 263px;">
					<?//здесь будет карта?>
				</div>
			</div>
			<?if (!empty($arItem["PREVIEW_PICTURE"])):?>
			<div class="item-big-img">
				<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["NAME"]?>" width="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arItem["PREVIEW_PICTURE"]["HEIGHT"]?>">
			</div>
			<?endif;?>
		</div>
		<div class="bottom-area clearfi" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
			<meta itemprop="addressLocality" content="<?=$arItem["PROPERTIES"]["CITY"]["VALUE"]?>">
			<meta itemprop="streetAddress" content="<?=$arItem["PROPERTIES"]["STREET"]["VALUE"]?>">
			<div class="item-text">
				<?if ($arItem["DISPLAY_PROPERTIES"]["ADDRESS"]["DISPLAY_VALUE"]):?>
					<div class="item-address"><?=$arItem["DISPLAY_PROPERTIES"]["ADDRESS"]["DISPLAY_VALUE"];?></div>
				<?endif;?>
				<?if ($arItem["DISPLAY_PROPERTIES"]["RATE"]["DISPLAY_VALUE"]):?>
					<div class="item-work-time"><?=$arItem["DISPLAY_PROPERTIES"]["RATE"]["DISPLAY_VALUE"];?></div>
				<?endif;?>
			</div>
			<?if ($arItem["DISPLAY_PROPERTIES"]["PHONE"]["DISPLAY_VALUE"]):?>
			<div class="item-phone"><span class="icon-phone"></span><?=$arItem["DISPLAY_PROPERTIES"]["PHONE"]["DISPLAY_VALUE"]?></div>
			<?endif;?>
		</div>
		<meta itemprop="telephone" content="<?=$arItem["PROPERTIES"]["PHONE"]["VALUE"]?>">
	</div>
<?endforeach;?>

<?foreach($arResult["ITEMS"] as $arItem):?>
	<?if (0 < intval($arItem["PROPERTIES"]["LATITUDE"]["VALUE"]) && 0 < intval($arItem["PROPERTIES"]["LONGITUDE"]["VALUE"])):?>
	<script>
		var map<?=$arItem["ID"]?> = ymaps.ready(init);
			var myMap<?=$arItem["ID"]?>, myPlacemark<?=$arItem["ID"]?>;
			function init() {
		var lat = <?=$arItem["PROPERTIES"]["LATITUDE"]["VALUE"]?>;
		var lon = <?=$arItem["PROPERTIES"]["LONGITUDE"]["VALUE"]?>;
		var zoom = <?=$arItem["PROPERTIES"]["ZOOM"]["VALUE"]?$arItem["PROPERTIES"]["ZOOM"]["VALUE"]:'14'?>;
		myMap<?=$arItem["ID"]?> = new ymaps.Map ("map<?=$arItem["ID"]?>", {
			center: [lat,lon],
			zoom: zoom
		});

		myPlacemark<?=$arItem["ID"]?> = new ymaps.Placemark([lat,lon], {
			hintContent: '<?=$arItem["NAME"]?>',
			balloonContent: '<?=$arItem["NAME"]?>'
		});
		myMap<?=$arItem["ID"]?>.geoObjects.add(myPlacemark<?=$arItem["ID"]?>);

		myMap<?=$arItem["ID"]?>.controls.add(
			new ymaps.control.ZoomControl()
		);
		myMap<?=$arItem["ID"]?>.controls.add('mapTools');
		myMap<?=$arItem["ID"]?>.controls.add('typeSelector');
	}
	</script>
	<?endif;?>
<?endforeach;?>


<?/*
<div class="news-list">
<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?><br />
<?endif;?>
<?foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
	<p class="news-item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
		<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arItem["PREVIEW_PICTURE"])):?>
			<?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?>
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><img
						class="preview_picture"
						border="0"
						src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>"
						width="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"]?>"
						height="<?=$arItem["PREVIEW_PICTURE"]["HEIGHT"]?>"
						alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>"
						title="<?=$arItem["PREVIEW_PICTURE"]["TITLE"]?>"
						style="float:left"
						/></a>
			<?else:?>
				<img
					class="preview_picture"
					border="0"
					src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>"
					width="<?=$arItem["PREVIEW_PICTURE"]["WIDTH"]?>"
					height="<?=$arItem["PREVIEW_PICTURE"]["HEIGHT"]?>"
					alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>"
					title="<?=$arItem["PREVIEW_PICTURE"]["TITLE"]?>"
					style="float:left"
					/>
			<?endif;?>
		<?endif?>
		<?if($arParams["DISPLAY_DATE"]!="N" && $arItem["DISPLAY_ACTIVE_FROM"]):?>
			<span class="news-date-time"><?echo $arItem["DISPLAY_ACTIVE_FROM"]?></span>
		<?endif?>
		<?if($arParams["DISPLAY_NAME"]!="N" && $arItem["NAME"]):?>
			<?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?>
				<a href="<?echo $arItem["DETAIL_PAGE_URL"]?>"><b><?echo $arItem["NAME"]?></b></a><br />
			<?else:?>
				<b><?echo $arItem["NAME"]?></b><br />
			<?endif;?>
		<?endif;?>
		<?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arItem["PREVIEW_TEXT"]):?>
			<?echo $arItem["PREVIEW_TEXT"];?>
		<?endif;?>
		<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arItem["PREVIEW_PICTURE"])):?>
			<div style="clear:both"></div>
		<?endif?>
		<?foreach($arItem["FIELDS"] as $code=>$value):?>
			<small>
			<?=GetMessage("IBLOCK_FIELD_".$code)?>:&nbsp;<?=$value;?>
			</small><br />
		<?endforeach;?>
		<?foreach($arItem["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
			<small>
			<?=$arProperty["NAME"]?>:&nbsp;
			<?if(is_array($arProperty["DISPLAY_VALUE"])):?>
				<?=implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);?>
			<?else:?>
				<?=$arProperty["DISPLAY_VALUE"];?>
			<?endif?>
			</small><br />
		<?endforeach;?>
	</p>
<?endforeach;?>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<br /><?=$arResult["NAV_STRING"]?>
<?endif;?>
</div>
*/?>