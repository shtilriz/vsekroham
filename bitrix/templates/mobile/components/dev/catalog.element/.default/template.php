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

<?//$bShowRecommend = false; //флаг показаны или нет рекомендации?>

<div id="product">
	<div class="b-content__header b-header">
		<a href="#" id="back_page" class="b-header__back b-back"></a>
		<div class="b-header__title"><span><?=$arResult["NAME"]?></span></div>
	</div>
	<div class="slider-big">
		<?$arFirstOffer = reset($arResult["OFFERS"]);
		$activeOfferCount = 0;?>
		<?if (!empty($arResult["OFFERS"])):?>
			<?foreach ($arResult["OFFERS"] as $key => $arOffer):?>
				<div data-index="<?=$activeOfferCount?>"><img src="<?=CFile::GetPath($arOffer["PREVIEW_PICTURE"])?>" alt="<?=$arOffer["NAME"].($arOffer["PROPERTIES"]["COLOR"]["VALUE"]?', цвет: '.$arOffer["PROPERTIES"]["COLOR"]["VALUE"]:'')?>" /></div>
				<?$activeOfferCount++?>
			<?endforeach;?>
		<?else:?>
			<div data-index="<?=$activeOfferCount?>"><img src="<?=CFile::GetPath($arResult["PREVIEW_PICTURE"]["ID"])?>" alt="<?=$arResult["NAME"]?>" /></div>
			<?$activeOfferCount++?>
		<?endif;?>
		<?foreach ($arResult["PROPERTIES"]["IMAGES"]["VALUE"] as $key => $value):?>
			<div data-index="<?=$activeOfferCount?>"><img src="<?=CFile::GetPath($value)?>" alt="<?=$arResult["PROPERTIES"]["IMAGES"]["DESCRIPTION"][$key]?>" /></div>
			<?$activeOfferCount++;?>
		<?endforeach;?>
	</div>

	<?$arRate = getRatingProduct($arResult["ID"]);?>
	<div class="product__stars b-stars-big b-stars-big-<?=$arRate["RATE"]?>"></div>
	<?if ($arRate["COUNT"]>0):?>
		<a href="#" class="product__comments" id="moreLinkReview" data-mode="<?=($arRate["COUNT"]>0?'show':'add')?>"><?=($arRate["COUNT"]>0?$arRate["COUNT"].' '.rating_txt($arRate["COUNT"]):'Оцените первым')?></a>
	<?else:?>
		<a href="/my-review/" class="product__comments">Оцените первым</a>
	<?endif;?>

	<form action="#">
		<?if ($arResult["PROPERTIES"]["AVAILABLE"]["VALUE"] == "Y"):?>
			<div class="b-box b-box_colors" id="skuSelect">
				<div class="b-box__content">
					<?if (!empty($arResult["THIS_SKU_PROPS"]["COLOR"]["VALUES"])):?>
					<select name="COLOR" class="selectpicker">
						<?
						$bFirst = true;
						foreach ($arResult["THIS_SKU_PROPS"]["COLOR"]["VALUES"] as $keyColor => $arColor) {
							echo '<option value="'.$arColor["VALUE"].'"'.($bFirst?' selected':'').'>'.$arColor["VALUE"].'</option>';
							$bFirst = false;
						}
						?>
					</select>
					<?endif;?>

					<?if (!empty($arResult["THIS_SKU_PROPS"]["SIZE"]["VALUES"])):?>
					<select name="SIZE" class="selectpicker">
						<?
						$bFirst = true;
						foreach ($arResult["THIS_SKU_PROPS"]["SIZE"]["VALUES"] as $keyColor => $arSize) {
							echo '<option value="'.$arSize["VALUE"].'"'.($bFirst?' selected':'').'>'.$arSize["VALUE"].'</option>';
							$bFirst = false;
						}
						?>
					</select>
					<?endif;?>

					<?
					$bShowOffers = false;
					foreach ($arResult["OFFERS"] as $key => $arOffer){
						//if (!empty($arOffer["PROPERTIES"]["COLOR"]["VALUE"]) && $arOffer["CATALOG_QUANTITY"] > 0) {
						if (!empty($arOffer["PROPERTIES"]["COLOR"]["VALUE"])) {
							$bShowOffers = true;
							break;
						}
					}
					if (
						is_array($arResult["OFFERS"]) && !empty($arResult["OFFERS"]) && $bShowOffers || is_array($arResult["PROPERTIES"]["IMAGES"]["VALUE"]) && !empty($arResult["PROPERTIES"]["IMAGES"]["VALUE"])
					):?>
					<div class="b-colors">
						<div class="js-dropdown">
							<div class="b-colors__content" id="sku-block">
								<?foreach ($arResult["OFFERS"] as $key => $arOffer):
									if (empty($arOffer["PROPERTIES"]["COLOR"]["VALUE"]) || !$arOffer["PREVIEW_PICTURE"])
										continue;
									if (!in_array($arOffer["PROPERTIES"]["COLOR"]["VALUE"], $arTemp)):
										$arTemp[] = $arOffer["PROPERTIES"]["COLOR"]["VALUE"];?>
										<span class="b-color sku-item" data-toggle="modal" data-target="#modal-pr">
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
													array("width" => 150, "height" => 150),
													BX_RESIZE_IMAGE_PROPORTIONAL,
													true
												);
											}?>
											<div class="b-color__img"><img src="<?=$y["src"]?>" alt="<?=$arOffer["NAME"]?>" /></div>
											<div class="b-color__title">
												<?if ($arOffer["DISPLAY_PROPERTIES"]["COLOR"]["VALUE"]):?>
													<?=$arOffer["DISPLAY_PROPERTIES"]["COLOR"]["VALUE"]?>
												<?elseif ($arOffer["DISPLAY_PROPERTIES"]["SIZE"]["VALUE"]):?>
													<?=$arOffer["DISPLAY_PROPERTIES"]["SIZE"]["VALUE"]?>
												<?endif;?>
											</div>
										</span>
									<?endif;?>
								<?endforeach;?>
								<?foreach ($arResult["PROPERTIES"]["IMAGES"]["VALUE"] as $key => $value):?>
									<span class="b-color sku-item" data-toggle="modal" data-target="#modal-pr">
										<?$y=CFile::ResizeImageGet(
											$value,
											array("width" => 150, "height" => 150),
											BX_RESIZE_IMAGE_PROPORTIONAL,
											true
										);?>
										<div class="b-color__img"><img src="<?=$y["src"]?>" alt="<?=$arResult["PROPERTIES"]["IMAGES"]["DESCRIPTION"][$key]?>" /></div>
										<?if ($arResult["PROPERTIES"]["IMAGES"]["DESCRIPTION"][$key]):?>
											<div class="b-color__title"><?=$arResult["PROPERTIES"]["IMAGES"]["DESCRIPTION"][$key]?></div>
										<?endif;?>
									</span>
								<?endforeach;?>
							</div>
						</div>
						<a href="javascript:void(0)" class="b-colors__header js-toogle">
							<span class="b-colors__icon"></span>
		                	<div class="b-colors__title">Показать все расцветки</div>
		                </a>
					</div>
					<?endif;?>
				</div>
			</div>
		<?endif?>

		<div class="product__order" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
			<meta itemprop="priceCurrency" content="<?=$arResult["PRICES"]["BASE"]["CURRENCY"]?>">
			<?
			$arPrices = array();
			if (!empty($arResult["OFFERS"])) {
				$arFirstOffer = reset($arResult["OFFERS"]);
				$arPrices = $arFirstOffer["PRICES"];
			}
			else
				$arPrices = $arResult["PRICES"];

			if ($arPrices["BASE"]["DISCOUNT_VALUE"] > 0) {
				$oldPrice = "";
				if ($arPrices["BASE"]["DISCOUNT_DIFF"] > 0) {
					$oldPrice = "<div class='product__price-old'>{$arPrices["BASE"]["PRINT_VALUE"]}</div>";
				}
				elseif ($arPrices["MARGIN"]["VALUE"] > 0) {
					$oldPrice = "<div class='product__price-old'>{$arPrices["MARGIN"]["PRINT_VALUE"]}</div>";
				}
				echo "<div class='product__price-wrapper'>
					$oldPrice
					<div class='product__price' id='prPrice' itemprop='price'>{$arPrices["BASE"]["PRINT_DISCOUNT_VALUE"]}</div>
				</div>";
			}
			?>

			<?if ($arResult["PROPERTIES"]["AVAILABLE"]["VALUE"] == "Y"):?>
				<?if (!empty($arResult["OFFERS"])):?>
					<?$arFirstOffer = reset($arResult["OFFERS"]);?>
					<button type="submit" class="product__button add2basket" data-id="<?=$arFirstOffer["ID"];?>" onmousedown="try {rrApi.addToBasket(<?=$arResult["ID"];?>)} catch(e) {}">В корзину</button>
				<?else:?>
					<button type="submit" class="product__button add2basket" data-id="<?=$arResult["ID"];?>" onmousedown="try {rrApi.addToBasket(<?=$arResult["ID"];?>)} catch(e) {}">В корзину</button>
				<?endif;?>
				<link itemprop="availability" href="http://schema.org/InStock">
			<?else:?>
				<span class="not-available btn btn2">Нет в наличии</span>
			<?endif;?>
		</div>
	</form>

	<?if ($arResult["PROPERTIES"]["AVAILABLE"]["VALUE"] == "Y" && $arResult["CATALOG_WEIGHT"] && $arResult["CATALOG_WIDTH"] && $arResult["CATALOG_LENGTH"] && $arResult["CATALOG_HEIGHT"]):?>
		<div id="deliveryCalc2Product" data-weight="<?=$arResult["CATALOG_WEIGHT"]?>" data-width="<?=$arResult["CATALOG_WIDTH"]?>" data-length="<?=$arResult["CATALOG_LENGTH"]?>" data-height="<?=$arResult["CATALOG_HEIGHT"]?>" data-price="<?=$arResult["PRICES"]["BASE"]["DISCOUNT_VALUE"]?>" data-page="product"></div>
	<?endif;?>

	<?/*<div class="product__delivery b-delivery">
		<div class="b-delivery__label">Доставка до города:</div>
		<div class="b-delivery__input">
			<input type="text" value="Ростов-На-Дону" />
		</div>
		<br/>
		<div class="b-delivery__img"><img src="images/temp/48_.png" alt="" />
		</div>
		<div class="b-delivery__info"><span class="b-delivery__info-title">Энергия - </span><span class="b-delivery__price">292 р. </span><span class="b-delivery__days">(2-3 дня)</span>
		</div><a href="#" class="b-delivery__more">Показать все варианты</a>
	</div>*/?>

	<a href="tel:88007753548" class="btn-big btn-primary"> <span class="icon sprite-phone"></span>Позвонить</a>
	<div class="b-tabs product__b-tabs">
		<!-- Nav tabs-->
		<ul role="tablist" class="nav nav-tabs" id="product-tabs">
			<li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Описание</a>
			</li>
			<li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Отзывы<?=($arRate["COUNT"]?' ('.$arRate["COUNT"].')':'')?></a>
			</li>
		</ul>
		<!-- Tab panes-->
		<div class="b-tabs__tab-content tab-content">
			<div id="home" role="tabpanel" class="b-tabs__tab-pane tab-pane active">
				<table class="tab-pane__table">
				<?foreach ($arResult["DISPLAY_PROPERTIES"] as $arProp):
					if (in_array($arProp["CODE"], array("OPTIONS")))
						continue;?>
					<tr>
						<td><?=$arProp["NAME"]?></td>
						<td>
							<?
							if ($arProp["DISPLAY_VALUE"] == "Y") {
								echo 'есть';
							}
							elseif (is_array($arProp["DISPLAY_VALUE"])) {
								echo implode(" / ", $arProp["DISPLAY_VALUE"]);
							}
							else {
								echo htmlspecialcharsBack($arProp["DISPLAY_VALUE"]);
							}
							?>
						</td>
					</tr>
				<?endforeach;?>
				<?if (!empty($arResult["PROPERTIES"]["OPTIONS"]["VALUES"])):?>
					<tr>
						<th colspan="2"><?=$arResult["PROPERTIES"]["OPTIONS"]["NAME"]?></td>
					</tr>
					<?foreach ($arResult["PROPERTIES"]["OPTIONS"]["VALUES"] as $value):?>
						<tr>
							<td><?=$value?></td>
							<td>есть</td>
						</tr>
					<?endforeach;?>
				<?endif;?>
				</table>
				<?if ($arResult["DETAIL_TEXT"]):?>
				<div class="tab-pane__title">Описание</div>
				<div class="tab-pane__content">
					<div class="product-description">
						<?=$arResult["DETAIL_TEXT"]?>
					</div>
				</div>
				<?endif;?>
			</div>
			<div id="profile" role="tabpanel" class="b-tabs__tab-pane tab-pane">
				<?include('reviews.php');?>
			</div>
		</div>
	</div>
	<?include('recommend.php');?>
</div>


<div id="modal-pr-added-to-cart" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" class="modal fade modal-pr-added-to-cart">
	<div role="document" class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" data-dismiss="modal" aria-label="Close" class="modal__close"><span aria-hidden="true">×</span>
				</button>
				<h3 id="myModalLabel" class="modal-title">Товар добавлен в корзину</h3>
			</div>
			<div class="modal-body">

			</div>
			<div class="modal-footer"></div>
		</div>
	</div>
</div>

<?include('carousel-modal.php');?>


<?/*
<h1 class="item-title__"><?=$arResult["NAME"]?></h1>

<div class="item" itemscope itemtype="http://schema.org/Product">
	<meta itemprop="name" content="<?=$arResult["NAME"]?>">
	<meta itemprop="description" content="<?=_substr(strip_tags($arResult["DETAIL_TEXT"]), 255)?>">
	<div class="item__image">
		<?$arFirstOffer = reset($arResult["OFFERS"]);?>
		<?if (!empty($arResult["OFFERS"]) && $arFirstOffer["PREVIEW_PICTURE"]):?>
			<a href="#" data-target="cart-img-popup" data-index="0" id="productImg">
				<?$y=CFile::ResizeImageGet(
					$arFirstOffer["PREVIEW_PICTURE"],
					array("width" => 250, "height" => 250),
					BX_RESIZE_IMAGE_PROPORTIONAL,
					true
				);?>
				<img src="<?=$y["src"]?>" alt="<?=$arResult["OFFERS"][0]["NAME"]?>" itemprop="image">
			</a>
		<?else:?>
			<a href="#" data-target="cart-img-popup" data-index="0" id="productImg">
				<?$y=CFile::ResizeImageGet(
					$arResult["PREVIEW_PICTURE"]["ID"],
					array("width" => 250, "height" => 250),
					BX_RESIZE_IMAGE_PROPORTIONAL,
					true
				);?>
				<img src="<?=$y["src"]?>" alt="<?=$arResult["NAME"]?>" itemprop="image">
			</a>
		<?endif;?>
		<div class="badge-wrapper">
			<?if ($arResult["PRICES"]["BASE"]["DISCOUNT_DIFF_PERCENT"] > 0):?>
				<span class="badge badge_type_discount">Cкидка <?=$arResult["PRICES"]["BASE"]["DISCOUNT_DIFF_PERCENT"]?>%</span>
			<?endif;?>
			<?if (!empty($arResult["PROPERTIES"]["GIFT"]["VALUE"])):?>
				<span class="badge badge_type_gift">+ Подарок</span>
			<?endif;?>
			<?if ($arResult["PROPERTIES"]["NEW"]["VALUE"] == "Y"):?>
				<span class="badge badge_type_gift">Новинка</span>
			<?endif;?>
		</div>
	</div>
	<div class="item__info" id="item__info">
		<form action="#">
			<?$arRate = getRatingProduct($arResult["ID"]);?>
			<div class="item__info-top">
				<div class="stars">
					<?for ($i=0; $i < 5; $i++)
						echo '<span class="'.($i<$arRate["RATE"]?'star-blue':'star-gray').'"></span>';
					?>
				</div>
				<a href="#" class="more-link" id="moreLinkReview" data-mode="<?=($arRate["COUNT"]>0?'show':'add')?>"><?=($arRate["COUNT"]>0?$arRate["COUNT"].' '.rating_txt($arRate["COUNT"]):'Оцените первым')?></a>
			</div>
			<div class="item__info-mdl">
				<a class="callback" href="#" data-target="callback">Заказать звонок менеджера</a>
				<div class="article"><span>Артикул:</span> <articul><?=$arResult["ID"]?></articul></div>
				<?if (isset($arResult["COUNTRY"]["UF_NAME"])):?>
					<div class="manufacturer"><span>Производитель:</span> <?=$arResult["COUNTRY"]["UF_NAME"]?></div>
				<?endif;?>

				<?if (isset($arResult["GIFT"]) && !empty($arResult["GIFT"])):?>
					<div class="gift"><span>Подарок:</span> <a href="<?=$arResult["GIFT"]["DETAIL_PAGE_URL"]?>" target="_blank"><?=$arResult["GIFT"]["NAME"]?></a></div>
				<?endif;?>

				<?if ($arResult["PROPERTIES"]["AVAILABLE"]["VALUE"] == "Y" && $arResult["CATALOG_WEIGHT"] && $arResult["CATALOG_WIDTH"] && $arResult["CATALOG_LENGTH"] && $arResult["CATALOG_HEIGHT"]):?>
					<div id="deliveryCalc2Product" data-weight="<?=$arResult["CATALOG_WEIGHT"]?>" data-width="<?=$arResult["CATALOG_WIDTH"]?>" data-length="<?=$arResult["CATALOG_LENGTH"]?>" data-height="<?=$arResult["CATALOG_HEIGHT"]?>" data-price="<?=$arResult["PRICES"]["BASE"]["DISCOUNT_VALUE"]?>" data-page="product"></div>
				<?endif;?>

				<?if ($arResult["PROPERTIES"]["AVAILABLE"]["VALUE"] == "Y"):?>
					<?if (!empty($arResult["THIS_SKU_PROPS"]["COLOR"]["VALUES"])):?>
					<div class="form-field">
						<div class="form-field__select">
							<select class="form-select select_type_color" data-placeholder="Выберите цвет" name="COLOR">
								<?
								$bFirst = true;
								foreach ($arResult["THIS_SKU_PROPS"]["COLOR"]["VALUES"] as $keyColor => $arColor) {
									$y=CFile::ResizeImageGet(
										$arColor["PREVIEW_PICTURE"],
										array("width" => 250, "height" => 250),
										BX_RESIZE_IMAGE_PROPORTIONAL,
										true
									);
									echo '<option value="'.$arColor["VALUE"].'" data-image="'.$y["src"].'"'.($bFirst?' selected':'').'>'.$arColor["VALUE"].'</option>';
									$bFirst = false;
								}?>
							</select>
						</div>
					</div>
					<?endif;?>

					<?if (!empty($arResult["THIS_SKU_PROPS"]["SIZE"]["VALUES"])):?>
						<div class="form-field">
							<div class="product-size-ch">
								<?$arColorOne = reset($arResult["THIS_SKU_PROPS"]["COLOR"]["VALUES"]);
								$bFirst = true;
								foreach ($arResult["THIS_SKU_PROPS"]["SIZE"]["VALUES"] as $keySize => $arSize) {
									$disabled = '';
									if (!empty($arResult["THIS_SKU_PROPS"]["COLOR"]["VALUES"]))
										$disabled = ' disabled';
									if (in_array($arSize["VALUE"], $arResult["JS"]["SIZE_IN_COLOR"][$arColorOne["VALUE"]]))
										$disabled = '';
									echo "<input type='radio' id='size-ch$keySize' name='SIZE' value='{$arSize["VALUE"]}' class='radiobox-styled1'$disabled".($bFirst?' checked':'')."/><label for='size-ch$keySize'>{$arSize["VALUE"]}</label>";
									$bFirst = false;
								}?>
							</div>
						</div>
					<?endif;?>
				<?endif;?>
			</div>
			<div class="item__info-btm item-box" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
				<meta itemprop="priceCurrency" content="<?=$arResult["PRICES"]["BASE"]["CURRENCY"]?>">
				<?if ($arResult["PROPERTIES"]["AVAILABLE"]["VALUE"] == "Y"):?>
					<?if (!empty($arResult["OFFERS"])):?>
						<?$arFirstOffer = reset($arResult["OFFERS"]);?>
						<a class="add-to-basket form-button" href="#" data-id="<?=$arFirstOffer["ID"];?>" onmousedown="try {rrApi.addToBasket(<?=$arResult["ID"];?>)} catch(e) {}">В корзину</a>
					<?else:?>
						<a class="add-to-basket form-button" href="#" data-id="<?=$arResult["ID"];?>" onmousedown="try {rrApi.addToBasket(<?=$arResult["ID"];?>)} catch(e) {}">В корзину</a>
					<?endif;?>
					<link itemprop="availability" href="http://schema.org/InStock">
				<?else:?>
					<span class="not-available btn btn2">Нет в наличии</span>
				<?endif;?>
				<?
				$arPrices = array();
				if (!empty($arResult["OFFERS"])) {
					$arFirstOffer = reset($arResult["OFFERS"]);
					$arPrices = $arFirstOffer["PRICES"];
				}
				else
					$arPrices = $arResult["PRICES"];

				if ($arPrices["BASE"]["DISCOUNT_VALUE"] > 0) {
					$oldPrice = "";
					if ($arPrices["BASE"]["DISCOUNT_DIFF"] > 0) {
						$oldPrice = "<span class='price__old'>{$arPrices["BASE"]["PRINT_VALUE"]}</span>";
					}
					elseif ($arPrices["MARGIN"]["VALUE"] > 0) {
						$oldPrice = "<span class='price__old'>{$arPrices["MARGIN"]["PRINT_VALUE"]}</span>";
					}
					echo "<div class='price'>
						<span id='prPrice' itemprop='price'>{$arPrices["BASE"]["PRINT_DISCOUNT_VALUE"]}</span>
						$oldPrice
					</div>";
				}
				?>
			</div>
		</form>
	</div>
</div>

<?
$bShowOffers = false;
foreach ($arResult["OFFERS"] as $key => $arOffer){
	//if (!empty($arOffer["PROPERTIES"]["COLOR"]["VALUE"]) && $arOffer["CATALOG_QUANTITY"] > 0) {
	if (!empty($arOffer["PROPERTIES"]["COLOR"]["VALUE"])) {
		$bShowOffers = true;
		break;
	}
}
if (
	is_array($arResult["OFFERS"]) && !empty($arResult["OFFERS"]) && $bShowOffers || is_array($arResult["PROPERTIES"]["IMAGES"]["VALUE"]) && !empty($arResult["PROPERTIES"]["IMAGES"]["VALUE"])
):?>
<div class="item-colors" id="product-colors">
	<h3>Выберите расцветку</h3>
	<div class="item-colors__slider">
		<div class="slider">
			<?
			if (empty($arResult["OFFERS"]) || !$bShowOffers)
				$activeOfferCount = 1;
			else
				$activeOfferCount = 0;
			$arTemp = array();
			foreach ($arResult["OFFERS"] as $key => $arOffer):
				if (empty($arOffer["PROPERTIES"]["COLOR"]["VALUE"]))
					continue;?>
				<?if (!in_array($arOffer["PROPERTIES"]["COLOR"]["VALUE"], $arTemp)):
					$arTemp[] = $arOffer["PROPERTIES"]["COLOR"]["VALUE"];
					?>
					<a data-target="cart-img-popup" data-index="<?=$activeOfferCount?>" class="slider__item" href="#">
						<div class="slider__item_img">
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
									array("width" => 100, "height" => 100),
									BX_RESIZE_IMAGE_PROPORTIONAL,
									true
								);
							}?>
							<img
								src="<?=$y["src"]?>"
								alt="<?=($arOffer["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"]?$arOffer["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"]:$arOffer["NAME"])?>">
						</div>
						<?if ($arOffer["DISPLAY_PROPERTIES"]["COLOR"]["VALUE"]):?>
							<span><?=$arOffer["DISPLAY_PROPERTIES"]["COLOR"]["VALUE"]?></span>
						<?elseif ($arOffer["DISPLAY_PROPERTIES"]["SIZE"]["VALUE"]):?>
							<span><?=$arOffer["DISPLAY_PROPERTIES"]["SIZE"]["VALUE"]?></span>
						<?endif;?>
					</a>
					<?$activeOfferCount++;?>
				<?endif;?>
			<?endforeach;?>
			<?foreach ($arResult["PROPERTIES"]["IMAGES"]["VALUE"] as $key => $value):?>
				<a data-target="cart-img-popup" data-index="<?=$activeOfferCount?>" class="slider__item" href="#">
					<div class="slider__item_img">
						<?$y=CFile::ResizeImageGet(
							$value,
							array("width" => 100, "height" => 100),
							BX_RESIZE_IMAGE_PROPORTIONAL,
							true
						);?>
						<img src="<?=$y["src"]?>" alt="<?=$arResult["PROPERTIES"]["IMAGES"]["DESCRIPTION"][$key]?>">
					</div>
					<?if ($arResult["PROPERTIES"]["IMAGES"]["DESCRIPTION"][$key]):?>
						<span><?=$arResult["PROPERTIES"]["IMAGES"]["DESCRIPTION"][$key]?></span>
					<?endif;?>
				</a>
				<?$activeOfferCount++;?>
			<?endforeach;?>
		</div>
		<a class="slide-prev" href="#">Предыдущий</a>
		<a class="slide-next" href="#">Следующий</a>
	</div>

	<?if ($activeOfferCount > 5):?>
	<div class="item-colors__list" style="display: none;">
		<div class="colors-list">
			<?
			if (empty($arResult["OFFERS"]) || !$bShowOffers)
				$activeOfferCount = 1;
			else
				$activeOfferCount = 0;
			$arTemp = array();
			foreach ($arResult["OFFERS"] as $key => $arOffer):
				if (empty($arOffer["PROPERTIES"]["COLOR"]["VALUE"]))
					continue;
				<?if (!in_array($arOffer["PROPERTIES"]["COLOR"]["VALUE"], $arTemp)):
					$arTemp[] = $arOffer["PROPERTIES"]["COLOR"]["VALUE"];
					?>
					<a data-target="cart-img-popup" data-index="<?=$activeOfferCount?>" class="colors-list__item" href="#">
						<div class="slider__item_img">
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
									array("width" => 100, "height" => 100),
									BX_RESIZE_IMAGE_PROPORTIONAL,
									true
								);
							}?>
							<img
								src="<?=$y["src"]?>"
								alt="<?=($arOffer["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"]?$arOffer["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"]:$arOffer["NAME"])?>">
						</div>
						<?if ($arOffer["DISPLAY_PROPERTIES"]["COLOR"]["VALUE"]):?>
							<span><?=$arOffer["DISPLAY_PROPERTIES"]["COLOR"]["VALUE"]?></span>
						<?elseif ($arOffer["DISPLAY_PROPERTIES"]["SIZE"]["VALUE"]):?>
							<span><?=$arOffer["DISPLAY_PROPERTIES"]["SIZE"]["VALUE"]?></span>
						<?endif;?>
					</a>
					<?$activeOfferCount++;?>
				<?endif?>
			<?endforeach;?>
			<?foreach ($arResult["PROPERTIES"]["IMAGES"]["VALUE"] as $key => $value):?>
				<a data-target="cart-img-popup" data-index="<?=$activeOfferCount?>" class="colors-list__item" href="#">
					<div class="slider__item_img">
						<?$y=CFile::ResizeImageGet(
							$value,
							array("width" => 100, "height" => 100),
							BX_RESIZE_IMAGE_PROPORTIONAL,
							true
						);?>
						<img src="<?=$y["src"]?>" alt="<?=$arResult["PROPERTIES"]["IMAGES"]["DESCRIPTION"][$key]?>">
					</div>
					<?if ($arResult["PROPERTIES"]["IMAGES"]["DESCRIPTION"][$key]):?>
						<span><?=$arResult["PROPERTIES"]["IMAGES"]["DESCRIPTION"][$key]?></span>
					<?endif;?>
				</a>
				<?$activeOfferCount++;?>
			<?endforeach;?>
		</div>
	</div>
	<a class="get-more" href="#" data-showtext="Показать все расцветки" data-hidetext="Свернуть">Показать все расцветки</a>
	<?endif;?>
</div>
<?elseif (!empty($arResult["RECOMMEND"]["ITEMS"]) && !$bShowRecommend):?>
	<?$bShowRecommend = true;?>
	<?include('recommend.php');?>
<?endif;?>

<div class="item-tabs">
	<nav class="item-tabs__nav">
		<ul>
			<li><a class="item-tabs__link" href="#description">Описание</a></li>
			<li><a class="item-tabs__link" href="#reviews">Отзывы<?=($arRate["COUNT"]?' ('.$arRate["COUNT"].')':'')?></a></li>
		</ul>
	</nav>
	<div class="item-tabs__content">
		<div class="tab" id="description">
			<table>
				<?foreach ($arResult["DISPLAY_PROPERTIES"] as $arProp):
					if (in_array($arProp["CODE"], array("OPTIONS")))
						continue;?>
					<tr>
						<td><?=$arProp["NAME"]?></td>
						<td>
							<?
							if ($arProp["DISPLAY_VALUE"] == "Y") {
								echo 'есть';
							}
							elseif (is_array($arProp["DISPLAY_VALUE"])) {
								echo implode(" / ", $arProp["DISPLAY_VALUE"]);
							}
							else {
								echo htmlspecialcharsBack($arProp["DISPLAY_VALUE"]);
							}
							?>
						</td>
					</tr>
				<?endforeach;?>
				<?if (!empty($arResult["PROPERTIES"]["OPTIONS"]["VALUES"])):?>
					<tr>
						<th colspan="2"><?=$arResult["PROPERTIES"]["OPTIONS"]["NAME"]?></td>
					</tr>
					<?foreach ($arResult["PROPERTIES"]["OPTIONS"]["VALUES"] as $value):?>
						<tr>
							<td><?=$value?></td>
							<td>есть</td>
						</tr>
					<?endforeach;?>
				<?endif;?>
				<?if ($arResult["DETAIL_TEXT"]):?>
					<tr>
						<th colspan="2">Описание</th>
					</tr>
					<tr>
						<td colspan="2"><?=$arResult["DETAIL_TEXT"]?></td>
					</tr>
				<?endif;?>
			</table>
		</div>
		<div class="tab" id="reviews">

			<div class="reviews" id="pr-reviews" data-id="<?=$arResult["ID"]?>">
				<?include('reviews.php');?>
			</div>

			<?include('form-review.php');?>

		</div>
	</div>
</div>

<?if (!$bShowRecommend):?>
	<?include('recommend.php');?>
<?endif;?>

<?include('carousel-modal.php');*/?>

<?
$arJS = array(
	"ID" => $arResult["ID"],
	"NAME" => $arResult["NAME"],
	"PRICES" => $arResult["PRICES"]
);
if (!empty($arResult["THIS_SKU_PROPS"]["COLOR"]) && !empty($arResult["THIS_SKU_PROPS"]["SIZE"])) {
	$arJS["COUNT_SKU_PROPS"] = 2;
}
elseif (!empty($arResult["THIS_SKU_PROPS"]["COLOR"]) || !empty($arResult["THIS_SKU_PROPS"]["SIZE"])) {
	$arJS["COUNT_SKU_PROPS"] = 1;
}
else {
	$arJS["COUNT_SKU_PROPS"] = 0;
}
foreach ($arResult["OFFERS"] as $keyOffer => $arOffer) {
	$arProps = array();
	foreach ($arOffer["PROPERTIES"] as $keyProp => $arProp) {
		if (in_array($keyProp, array("COLOR", "SIZE"))) {
			$arProps[$keyProp] = $arProp["VALUE"];
		}
	}
	$arTemp = array(
		"ID" => $arOffer["ID"],
		"NAME" => $arOffer["NAME"],
		"PRICES" => $arOffer["PRICES"],
		"PROPS" => $arProps
	);
	$arJS["OFFERS"][] = $arTemp;
}
?>

<?if (!empty($arJS)):?>
	<script type="text/javascript">
		window.PRODUCT = new Object();
		PRODUCT = <?=json_encode($arJS);?>;
	</script>
<?endif;?>

<script type="text/javascript">
	var SKU = new Object;
	SKU = <?=json_encode($arResult["JS"]);?>;
</script>

<script type="text/javascript">
    rrApiOnReady.push(function() {
		try{ rrApi.view(<?=$arResult["ID"]?>); } catch(e) {}
	})
</script>

<?if ($arResult["NAME"] && $arResult["DETAIL_PAGE_URL"] && $arResult["PREVIEW_PICTURE"] && ($arResult["PREVIEW_TEXT"] || $arResult["DETAIL_TEXT"])):?>
	<?$this->SetViewTarget("OpenGraphHTMLtag");?> prefix="og: http://ogp.me/ns#"<?$this->EndViewTarget();?>
	<?$this->SetViewTarget("OpenGraph");?>
	<meta property="og:title" content="<?=strip_tags($arResult["NAME"])?>" />
	<?if ($arResult["PREVIEW_TEXT"] || $arResult["DETAIL_TEXT"]):
		$TEXT = ($arResult["PREVIEW_TEXT"]?$arResult["PREVIEW_TEXT"]:$arResult["DETAIL_TEXT"]);
		$obParser = new CTextParser;
		$TEXT = $obParser->html_cut($TEXT, 300);
		?>
		<meta property="og:description" content="<?=trim(strip_tags($TEXT))?>" />
	<?endif?>
	<meta property="og:image" content="http://<?=$_SERVER["SERVER_NAME"].$arResult["PREVIEW_PICTURE"]["SRC"]?>" />
	<meta property="og:type" content="website"/>
	<meta property="og:url" content="http://<?=$_SERVER["SERVER_NAME"].$arResult["DETAIL_PAGE_URL"]?>" />
	<?$this->EndViewTarget();?>
<?endif;?>

<?/*$this->SetViewTarget("baner-anex");?>
	<?if (in_array($arResult["IBLOCK_SECTION_ID"], array(141, 157, 165, 170, 174, 180, 184, 190))):?>
	<div class="banner">
		<a href="/brands/anex/"><img src="<?=SITE_TEMPLATE_PATH?>/images/Anex_Banner_Classic.png"></a>
	</div>
	<?endif;?>
<?$this->EndViewTarget();*/?>

<script type="text/javascript">
(function(d,w){
var n=d.getElementsByTagName("script")[0],
s=d.createElement("script"),
f=function(){n.parentNode.insertBefore(s,n);};
s.type="text/javascript";
s.async=true;
s.src="http://track.recreativ.ru/trck.php?shop=1584&ttl=30&offer=<?=$arResult['ID']?>&rnd="+Math.floor(Math.random()*999);
if(window.opera=="[object Opera]"){d.addEventListener("DOMContentLoaded", f, false);}
else{f();}
})(document,window);
</script>

<script type="text/javascript">
dataLayer.push({
	"ecommerce": {
		"detail": {
			"products": [
				<?if ($offersExist[$arResult["ID"]]):?>
					<?foreach ($arResult["OFFERS"] as $key => $arOffer):?>
					{
						"id": "<?=$arOffer["ID"]?>",
						"name" : "<?=$arOffer["NAME"]?>",
						"price": <?=$arOffer["PRICES"]["BASE"]["DISCOUNT_VALUE"]?>,
						<?if ($arResult["DISPLAY_PROPERTIES"]["MAKER"]["VALUE"]):?>"brand": "<?=$arResult["DISPLAY_PROPERTIES"]["MAKER"]["LINK_ELEMENT_VALUE"][$arResult["DISPLAY_PROPERTIES"]["MAKER"]["VALUE"]]["NAME"]?>",<?endif;?>
						<?if (!empty($arSections)):?>"category": "<?=implode("/",$arSections);?>",<?endif;?>
						<?if ($arOffer["PROPERTIES"]["COLOR"]["VALUE"]):?>"variant" : "<?=$arOffer["PROPERTIES"]["COLOR"]["VALUE"]?>"<?endif;?>
					},
					<?endforeach;?>
				<?else:?>
				{
					"id": "<?=$arResult["ID"]?>",
					"name" : "<?=$arResult["NAME"]?>",
					"price": <?=$arResult["PRICES"]["BASE"]["DISCOUNT_VALUE"]?>,
					<?if ($arResult["PROPERTIES"]["MAKER"]["VALUE"]):?>"brand": "<?=$arResult["DISPLAY_PROPERTIES"]["MAKER"]["LINK_ELEMENT_VALUE"][$arResult["DISPLAY_PROPERTIES"]["MAKER"]["VALUE"]]["NAME"]?>",<?endif;?>
					<?if (!empty($arSections)):?>"category": "<?=implode("/",$arSections);?>",<?endif;?>
					<?if ($arResult["PROPERTIES"]["COLOR"]["VALUE"]):?>"variant" : "<?=$arResult["PROPERTIES"]["COLOR"]["VALUE"]?>"<?endif;?>
				}
				<?endif;?>
			]
		}
	}
});
</script>