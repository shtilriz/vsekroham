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
		<?/*<span class="badge badge_type_new">Новинка</span>*/?>
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

				<?/*if ($arResult["PROPERTIES"]["WARRANTY"]["VALUE"]):?>
					<div class="warranty"><span>Гарантия:</span> <?=$arResult["PROPERTIES"]["WARRANTY"]["VALUE"]?></div>
				<?endif;*/?>
				<?if (isset($arResult["GIFT"]) && !empty($arResult["GIFT"])):?>
					<div class="gift"><span>Подарок:</span> <a href="<?=$arResult["GIFT"]["DETAIL_PAGE_URL"]?>" target="_blank"><?=$arResult["GIFT"]["NAME"]?></a></div>
				<?endif;?>

				<?if ($arResult["PROPERTIES"]["AVAILABLE"]["VALUE"] == "Y" && $arResult["CATALOG_WEIGHT"] && $arResult["CATALOG_WIDTH"] && $arResult["CATALOG_LENGTH"] && $arResult["CATALOG_HEIGHT"]):?>
					<div id="deliveryCalc2Product" data-weight="<?=$arResult["CATALOG_WEIGHT"]?>" data-width="<?=$arResult["CATALOG_WIDTH"]?>" data-length="<?=$arResult["CATALOG_LENGTH"]?>" data-height="<?=$arResult["CATALOG_HEIGHT"]?>" data-price="<?=$arResult["PRICES"]["BASE"]["DISCOUNT_VALUE"]?>"></div>
				<?endif;?>

				<?if ($arResult["PROPERTIES"]["AVAILABLE"]["VALUE"] == "Y"):?>
					<?if (!empty($arResult["THIS_SKU_PROPS"]["COLOR"]["VALUES"])):?>
					<div class="form-field">
						<div class="form-field__select">
							<select class="form-select select_type_color" data-placeholder="Выберите цвет" name="COLOR">
								<?/*<option value=""></option>*/?>
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
						<?/*<div class="form-field__select">
							<select class="form-select select_type_size" data-placeholder="Выберите размер" name="SIZE">
								<option value=""></option>
								<?foreach ($arResult["THIS_SKU_PROPS"]["SIZE"]["VALUES"] as $keySize => $arSize) {
									echo '<option value="'.$arSize["VALUE"].'">'.$arSize["VALUE"].'</option>';
								}?>
							</select>
						</div>*/?>
					<?endif;?>

					<?if (empty($arResult["OFFERS"]) && $arResult["PROPERTIES"]["SIZE"]["VALUE"]):?>
						<div class="form-field">
							<div class="product-size-ch">
								<input type='radio' id='size-ch' name='SIZE' value='' class='radiobox-styled1' checked/><label for='size-ch'><?=$arResult["PROPERTIES"]["SIZE"]["VALUE"]?></label>
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
				<?/*if ($arResult["B_OFFERS"]):?>
					<?if (!empty($arResult["OFFERS"])):?>
						<?$arFirstOffer = reset($arResult["OFFERS"]);?>
						<a class="add-to-basket form-button" href="#" data-id="<?=$arFirstOffer["ID"];?>">В корзину</a>
					<?else:?>
						<a class="add-to-basket form-button" href="#" data-id="<?=$arResult["ID"];?>">В корзину</a>
					<?endif;?>
				<?else:?>
					<span class="not-available">Нет в наличии</span>
				<?endif;*/?>
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
	is_array($arResult["OFFERS"]) && !empty($arResult["OFFERS"]) && $bShowOffers ||
	is_array($arResult["PROPERTIES"]["IMAGES"]["VALUE"]) &&	!empty($arResult["PROPERTIES"]["IMAGES"]["VALUE"])
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
				/*if ($arOffer["CATALOG_QUANTITY"] <= 0 || empty($arOffer["PROPERTIES"]["COLOR"]["VALUE"]))
					continue;*/
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
				/*if ($arOffer["CATALOG_QUANTITY"] <= 0 || empty($arOffer["PROPERTIES"]["COLOR"]["VALUE"]))
					continue;*/?>
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

<?include('recommend.php');?>

<?include('carousel-modal.php');?>

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
	<meta property="og:image" content="https://<?=$_SERVER["SERVER_NAME"].$arResult["PREVIEW_PICTURE"]["SRC"]?>" />
	<meta property="og:url" content="https://<?=$_SERVER["SERVER_NAME"].$arResult["DETAIL_PAGE_URL"]?>" />
	<?$this->EndViewTarget();?>
<?endif;?>