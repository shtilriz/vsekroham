<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?//выводит всплывающее окно с расцветками?>

<?
$bShowOffers = false;
foreach ($arResult["OFFERS"] as $key => $arOffer){
	//if (!empty($arOffer["PROPERTIES"]["COLOR"]["VALUE"]) && $arOffer["CATALOG_QUANTITY"] > 0) {
	if (!empty($arOffer["PROPERTIES"]["COLOR"]["VALUE"])) {
		$bShowOffers = true;
		break;
	}
}
?>
<div class="cart-img-popup popup" id="cart-img-popup">
	<?$frame = $this->createFrame("cart-img-popup", false)->begin('');?>
	<div class="popup__left">
		<div class="slider slider_popup">
			<div class="slider__inner">
				<h3 class="cart-img-popup__title"><?=$arResult["NAME"]?></h3>
				<div id="slider">

					<?$i = 0;?>
					<?if (empty($arResult["OFFERS"]) || !$bShowOffers):?>
						<div class="slider__item" data-index="<?=$i?>">
							<?$y=CFile::ResizeImageGet(
								$arResult["PREVIEW_PICTURE"]["ID"],
								array("width" => 800, "height" => 600),
								BX_RESIZE_IMAGE_PROPORTIONAL,
								true
							);?>
							<img data-title="<?=$arResult["NAME"]?>" data-size="<?=$arResult["PROPERTIES"]["SIZE"]["VALUE"]?>" data-color="<?=$arResult["PROPERTIES"]["COLOR"]["VALUE"]?>" src="<?=$y["src"]?>" width="<?=$y["width"]?>" height="<?=$y["height"]?>" data-id="<?=$arResult["ID"]?>" alt="" data-price="<?=$arResult["OFFERS"][0]["PRICES"]["BASE"]["PRINT_DISCOUNT_VALUE"]?>" data-size="<?=$arResult["OFFERS"][0]["PROPERTIES"]["SIZE"]["VALUE"]?>" />
						</div>
						<?$i++;?>
					<?else:?>
						<?
						$arTemp = array();
						foreach ($arResult["OFFERS"] as $key => $arOffer):
							if (empty($arOffer["PROPERTIES"]["COLOR"]["VALUE"]))
								continue;
							/*if ($arOffer["CATALOG_QUANTITY"] <= 0 || empty($arOffer["PROPERTIES"]["COLOR"]["VALUE"]))
								continue;*/?>
							<?if (!in_array($arOffer["PROPERTIES"]["COLOR"]["VALUE"], $arTemp)):
								$arTemp[] = $arOffer["PROPERTIES"]["COLOR"]["VALUE"];
								?>
								<div class="slider__item" data-index="<?=$i?>">
									<?$offerIMG = 0;
									$y = array();
									if ($arOffer["PREVIEW_PICTURE"]) {
										$offerIMG = $arOffer["PREVIEW_PICTURE"];
									}
									elseif ($arResult["PREVIEW_PICTURE"]["ID"]) {
										$offerIMG = $arResult["PREVIEW_PICTURE"]["ID"];
									}
									if ($offerIMG) {
										$y=CFile::ResizeImageGet(
											$offerIMG,
											array("width" => 800, "height" => 600),
											BX_RESIZE_IMAGE_PROPORTIONAL,
											true
										);
									}?>
									<img data-title="<?=$arOffer["NAME"]?>" data-size="<?=$arOffer["PROPERTIES"]["SIZE"]["VALUE"]?>" data-color="<?=$arOffer["PROPERTIES"]["COLOR"]["VALUE"]?>" src="<?=$y["src"]?>" width="<?=$y["width"]?>" height="<?=$y["height"]?>" data-id="<?=$arOffer["ID"]?>" data-price="<?=$arOffer["PRICES"]["BASE"]["PRINT_DISCOUNT_VALUE"]?>" alt="<?=($arOffer["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"]?$arOffer["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"]:$arOffer["NAME"])?>" />
								</div>
								<?$i++;?>
							<?endif;?>
						<?endforeach;?>
					<?endif;?>

					<?foreach ($arResult["PROPERTIES"]["IMAGES"]["VALUE"] as $key => $value):?>
						<div class="slider__item" data-index="<?=$i?>">
							<?$y=CFile::ResizeImageGet(
								$value,
								array("width" => 800, "height" => 600),
								BX_RESIZE_IMAGE_PROPORTIONAL,
								true
							);?>
							<img data-title="<?=($arResult["PROPERTIES"]["IMAGES"]["DESCRIPTION"][$key]?$arResult["PROPERTIES"]["IMAGES"]["DESCRIPTION"][$key]:$arResult["NAME"])?>" src="<?=$y["src"]?>" width="<?=$y["width"]?>" height="<?=$y["height"]?>" alt="" />
						</div>
						<?$i++;?>
					<?endforeach;?>
				</div>
				<a class="slide-prev" href="#">Предыдущий слайд</a>
				<a class="slide-next" href="#">Следующий слайд</a>
			</div>
		</div>
	</div>
	<div class="popup__right">
		<div class="close-wrap">
			<a href="#" class="popup__close">Закрыть</a>
		</div>
		<div class="popup__right__opt">
			<span class="popup__right__opt__inner">
				<?//здесь будут подставляться свойства (цвета, размеры и т.д.)?>
			</span>
			<div class="price"></div>
			<?if ($arResult["PROPERTIES"]["AVAILABLE"]["VALUE"] == "Y"):?>
				<a data-id="" href="#" class="add-to-basket form-button" id="add2bsk_popup">В корзину</a>
			<?endif;?>
		</div>
	</div>
	<?$frame->end();?>
</div>