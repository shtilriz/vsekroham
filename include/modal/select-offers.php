<?php
//выводит окно выбора торгового предложения по параметрам
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if (isset($_GET["id"]) && 0 < $_GET["id"] && CModule::IncludeModule("iblock") && CModule::IncludeModule("catalog")) {
	$PRODUCT_ID = intval($_GET["id"]);
	$arResult = array();
	$rsProducts = CIBlockElement::GetList(
		array(),
		array(
			"IBLOCK_ID" => IBLOCK_PRODUCT_ID,
			"ACTIVE" => "Y",
			"ID" => $PRODUCT_ID
		),
		false,
		false,
		array("IBLOCK_ID", "ID", "NAME", "PREVIEW_PICTURE", "DETAIL_PAGE_URL", "CATALOG_GROUP_1")
	);
	while ($obProduct = $rsProducts->GetNextElement()) {
		$arResult = $obProduct->GetFields();
		$arResult["PROPERTIES"] = $obProduct->GetProperties();

		$arCatalogPrices = CIBlockPriceTools::GetCatalogPrices(false, array('BASE'));
		$arResult["PRICES"] = CIBlockPriceTools::GetItemPrices(false, $arCatalogPrices, $arResult, false, array());
	}
	$rsOffers = CIBlockElement::GetList(
		array("ID" => "ASC"),
		array(
			"IBLOCK_ID" => IBLOCK_SKU_ID,
			"ACTIVE" => "Y",
			"PROPERTY_CML2_LINK" => $PRODUCT_ID
		),
		false,
		false,
		array("IBLOCK_ID", "ID", "NAME", "PREVIEW_PICTURE", "CATALOG_GROUP_1")
	);
	while ($obOffer = $rsOffers->GetNextElement()) {
		$arOffer = $obOffer->GetFields();
		$arOffer["PROPERTIES"] = $obOffer->GetProperties();

		$arCatalogPrices = CIBlockPriceTools::GetCatalogPrices(false, array('BASE'));
		$arOffer["PRICES"] = CIBlockPriceTools::GetItemPrices(false, $arCatalogPrices, $arOffer, false, array());

		$arResult["OFFERS"][] = $arOffer;
	}

	if (!empty($arResult["OFFERS"])) {
		$thisSKUProps = array();
		$arColors = array();
		$arSizes = array();
		foreach ($arResult["OFFERS"] as $keyOffer => $arOffer) {
			/*if ($arOffer["CATALOG_QUANTITY"] <= 0)
				continue;*/
			if (
				!in_array($arOffer["PROPERTIES"]["COLOR"]["VALUE"], $arColors) &&
				!empty($arOffer["PROPERTIES"]["COLOR"]["VALUE"])
			) {
				$thisSKUProps["COLOR"]["VALUES"][$arOffer["ID"]] = array(
					"VALUE" => $arOffer["PROPERTIES"]["COLOR"]["VALUE"],
					"PREVIEW_PICTURE" => $arOffer["PREVIEW_PICTURE"]
				);
				$arColors[] = $arOffer["PROPERTIES"]["COLOR"]["VALUE"];
			}
			if (
				!in_array($arOffer["PROPERTIES"]["SIZE"]["VALUE"], $arSizes) &&
				!empty($arOffer["PROPERTIES"]["SIZE"]["VALUE"])
			) {
				$thisSKUProps["SIZE"]["VALUES"][$arOffer["ID"]] = array(
					"VALUE" => $arOffer["PROPERTIES"]["SIZE"]["VALUE"],
					"PREVIEW_PICTURE" => $arOffer["PREVIEW_PICTURE"]
				);
				$arSizes[] = $arOffer["PROPERTIES"]["SIZE"]["VALUE"];
			}
		}
		$arResult["THIS_SKU_PROPS"] = $thisSKUProps;
	}

	//для выгрузки в объект js
	$arSkuProps = array();
	if (!empty($arColors)) {
		$arSkuProps[] = "COLOR";
	}
	if (!empty($arSizes)) {
		$arSkuProps[] = "SIZE";
	}
	$arTree = array();
	$arSizesInColor = array();
	$arOffersPrice = array();
	foreach ($arResult["OFFERS"] as $key => $arOffer) {
		/*if ($arOffer["CATALOG_QUANTITY"] <= 0)
			continue;*/
		foreach ($arOffer["PROPERTIES"] as $prop => $arProp) {
			if (in_array($prop, $arSkuProps)) {
				$arTree[$arOffer["ID"]][$prop] = $arProp["VALUE"];
			}
		}
		$arOffersPriceDiscount[$arOffer["ID"]] = $arOffer["PRICES"]["BASE"]["PRINT_DISCOUNT_VALUE"];
		$arOffersPrice[$arOffer["ID"]] = $arOffer["PRICES"]["BASE"]["PRINT_VALUE"];
		$arOffersPriceMargin[$arOffer["ID"]] = $arOffer["PRICES"]["MARGIN"]["PRINT_VALUE"];
		$arOffersDiscountDiff[$arOffer["ID"]] = $arOffer["PRICES"]["BASE"]["DISCOUNT_DIFF"];

		if (!empty($arOffer["PROPERTIES"]["COLOR"]["VALUE"]) && !empty($arOffer["PROPERTIES"]["SIZE"]["VALUE"])) {
			$arSizesInColor[$arOffer["PROPERTIES"]["COLOR"]["VALUE"]][$arOffer["ID"]] = $arOffer["PROPERTIES"]["SIZE"]["VALUE"];
		}
	}

	$arResult["JS"]["PRODUCT_ID"] = $arResult["ID"];
	$arResult["JS"]["B_OFFERS"] = (!empty($arResult["OFFERS"])?true:false);
	$arResult["JS"]["SKU_PROPS"] = $arSkuProps;
	$arResult["JS"]["TREE"] = $arTree;
	$arResult["JS"]["SIZE_IN_COLOR"] = $arSizesInColor;
	$arResult["JS"]["PRICE_DISCOUNT"] = $arOffersPriceDiscount;
	$arResult["JS"]["PRICE"] = $arOffersPrice;
	$arResult["JS"]["PRICE_MARGIN"] = $arOffersPriceMargin;
	$arResult["JS"]["DISCOUNT_DIFF"] = $arOffersDiscountDiff;
}
?>

<?$arFirstOffer = reset($arResult["OFFERS"]);?>
<div class="popup popup-choose-param" id="popupSelectOffers" style="display: none;">
	<form action="#">
		<div class="popup__top">
			<a href="#" class="popup__close">X</a>
			<strong class="popup__title">Нужно выбрать параметры товара, чтобы добавить в корзину</strong>
		</div>
		<div class="popup__content">
			<div class="popup-choose-param__img" id="productImg">
				<?$y=CFile::ResizeImageGet(
					$arResult["PREVIEW_PICTURE"],
					array("width" => 150, "height" => 150),
					BX_RESIZE_IMAGE_PROPORTIONAL,
					true
				);?>
				<img src="<?=$y["src"]?>" alt="<?=$arResult["NAME"]?>">
			</div>
			<div class="popup-choose-param__content">
				<a href="<?=$arResult["DETAIL_PAGE_URL"]?>" target="_blank" class="popup-choose-param__title"><?=$arResult["NAME"]?></a>
				<div class="popup-choose-param__price" id="prPrice"><?=$arResult["PRICES"]["BASE"]["PRINT_DISCOUNT_VALUE"]?></div>
				<table>
					<tbody>
						<?if (!empty($arResult["THIS_SKU_PROPS"]["COLOR"]["VALUES"])):?>
						<tr>
							<td width="65">Цвет:</td>
							<td>
								<select class="select-chosen" name="COLOR" data-placeholder="Выберите цвет">
									<option value=""></option>
									<?foreach ($arResult["THIS_SKU_PROPS"]["COLOR"]["VALUES"] as $keyColor => $arColor) {
										$y=CFile::ResizeImageGet(
											$arColor["PREVIEW_PICTURE"],
											array("width" => 150, "height" => 150),
											BX_RESIZE_IMAGE_PROPORTIONAL,
											true
										);
										echo '<option value="'.$arColor["VALUE"].'" data-image="'.$y["src"].'">'.$arColor["VALUE"].'</option>';
									}?>
								</select>
							</td>
						</tr>
						<?endif;?>
						<?if (!empty($arResult["THIS_SKU_PROPS"]["SIZE"]["VALUES"])):?>
						<tr>
							<td width="65">Размер:</td>
							<td>
								<select class="select-chosen" name="SIZE" data-placeholder="Выберите размер">
									<option value=""></option>
									<?foreach ($arResult["THIS_SKU_PROPS"]["SIZE"]["VALUES"] as $keySize => $arSize) {
										echo '<option value="'.$arSize["VALUE"].'">'.$arSize["VALUE"].'</option>';
									}?>
								</select>
							</td>
						</tr>
						<?endif;?>
					</tbody>
				</table>
				<div id="message"></div>
			</div>
		</div>
		<div class="popup__footer">
			<div class="form-field">
				<div class="form-field__button">
					<a class="form-button form-button_bg_gray" href="<?=$arResult["DETAIL_PAGE_URL"]?>">Перейти к товару</a>
					<?if ($arResult["PROPERTIES"]["AVAILABLE"]["VALUE"] == "Y"):?>
						<?if (!empty($arResult["OFFERS"])):?>
							<a class="add-to-basket form-button" href="#" data-id="<?=$arFirstOffer["ID"];?>" onmousedown="try {rrApi.addToBasket(<?=$arResult["ID"];?>)} catch(e) {}">Добавить в корзину</a>
						<?else:?>
							<a class="add-to-basket form-button" href="#" data-id="<?=$arResult["ID"];?>" onmousedown="try {rrApi.addToBasket(<?=$arResult["ID"];?>)} catch(e) {}">Добавить в корзину</a>
						<?endif;?>
					<?else:?>
						<span class="not-available">Нет в наличии</span>
					<?endif;?>
					<?/*<input type="submit" class="form-button button_type_submit" value="Добавить в корзину">*/?>
				</div>
			</div>
		</div>
	</form>
</div>

<script type="text/javascript">
	var SKU = new Object;
	SKU = <?=json_encode($arResult["JS"]);?>;
	setTimeout(function () {
		$('.select-chosen').chosen({
			disable_search: true,
			no_results_text: $(this).data('placeholder')
		});
	},300);
</script>
