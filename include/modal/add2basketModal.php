<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?if (isset($_GET["id"]) && $_GET["id"] > 0 && CModule::IncludeModule("iblock") && CModule::IncludeModule("catalog") && CModule::IncludeModule("sale")):
	//достать данные добавляемого товара
	$arResult = array();
	$IBLOCK_SECTION_ID = 0;
	$rsItem = CIBlockElement::GetList(
		array(),
		array(
			"IBLOCK_ID" => array(IBLOCK_PRODUCT_ID, IBLOCK_SKU_ID),
			"ID" => $_GET["id"],
			"ACTIVE" => "Y"
		),
		false,
		false,
		array("IBLOCK_ID", "ID", "NAME", "DETAIL_PAGE_URL", "PREVIEW_PICTURE", "CATALOG_GROUP_1", "IBLOCK_SECTION_ID")
	);
	if ($obItem = $rsItem->GetNextElement()) {
		$arItem = $obItem->GetFields();
		$arItem["PROPERTIES"] = $obItem->GetProperties();

		$arCatalogPrices = CIBlockPriceTools::GetCatalogPrices(false, array('BASE'));
		$arPrices = CIBlockPriceTools::GetItemPrices(false, $arCatalogPrices, $arItem, false, array());

		$IBLOCK_SECTION_ID = $arItem["IBLOCK_SECTION_ID"];

		$y=CFile::ResizeImageGet(
			$arItem["PREVIEW_PICTURE"],
			array("width" => 100, "height" => 200),
			BX_RESIZE_IMAGE_PROPORTIONAL,
			true
		);

		$arResult = array(
			"IBLOCK_ID" => $arItem["IBLOCK_ID"],
			"ID" => $arItem["ID"],
			"NAME" => $arItem["NAME"],
			"IMG" => $y["src"],
			"DETAIL_PAGE_URL" => $arItem["DETAIL_PAGE_URL"],
			"PRICE" => $arPrices["BASE"]["PRINT_DISCOUNT_VALUE"],
			"COLOR" => $arItem["PROPERTIES"]["COLOR"]["VALUE"],
			"SIZE" => $arItem["PROPERTIES"]["SIZE"]["VALUE"]
		);
	}
	?>

	<?
	//проверить, разрешено ли выводить рекомендации для товара данного раздела
	$bShowRecommend = true;
	if ($IBLOCK_SECTION_ID > 0) {
		$arSect = array();
		$nav = CIBlockSection::GetNavChain(false, $IBLOCK_SECTION_ID);
		while ($arSectionPath = $nav->GetNext()) {
			$arSect[] = $arSectionPath["ID"];
		}
		if (!empty($arSect)) {
			$db_list = CIBlockSection::GetList(
				array(),
				array(
					"IBLOCK_ID" => 5,
					"ID" => $arSect,
					"ACTIVE" => "Y"
				),
				false,
				array("IBLOCK_ID", "UF_SHOWRECOMMEND")
			);
			while ($ar_result = $db_list->GetNext()) {
				if ($ar_result["UF_SHOWRECOMMEND"]) {
					$bShowRecommend = false;
					break;
				}
			}
		}
	}

	if ($bShowRecommend) {
		//достать рекомендации
		$PRODUCT_ID = intval($_GET["id"]);
		$mxResult = CCatalogSku::GetProductInfo($PRODUCT_ID);
		if (is_array($mxResult)) {
			$PRODUCT_ID = $mxResult["ID"];
		}
		$query = 'http://api.retailrocket.ru/api/1.0/Recomendation/CrossSellItemToItems/53a000601e994424286fc7d9/'.$PRODUCT_ID;
		$xml_string = file_get_contents($query);
		$arData = json_decode($xml_string,true);
		$arResult["RECOMMEND"]["IDS"] = $arData;
		$arResult["RECOMMEND"]["TYPE"] = "CrossSellItemToItems";

		if (!empty($arData) && is_array($arData)) {
			foreach ($arData as $key => $id) {
				$rsRecommends = CIBlockElement::GetList(
					array(),
					array(
						"IBLOCK_ID" => $arParams["IBLOCK_ID"],
						"ACTIVE" => "Y",
						"ID" => $id
					),
					false,
					false,
					array("IBLOCK_ID", "ID", "NAME", "PREVIEW_PICTURE", "DETAIL_PICTURE", "DETAIL_PAGE_URL", "CATALOG_GROUP_1")
				);
				while ($obRecommend = $rsRecommends->GetNextElement()) {
					$arRecommend = $obRecommend->GetFields();
					$arRecommend["PROPERTIES"] = $obRecommend->GetProperties();
					$arCatalogPrices = CIBlockPriceTools::GetCatalogPrices(false, array('BASE'));
					$arRecommend["PRICES"] = CIBlockPriceTools::GetItemPrices(false, $arCatalogPrices, $arRecommend, false, array());
					$arResult["RECOMMEND"]["ITEMS"][] = $arRecommend;
				}
			}
		}
	}?>

	<div class="cart-added popup" id="add2basketModal">
		<div class="popup__top">
			<a href="#" class="popup__close">X</a>
			<strong class="popup__title">Товар добавлен в корзину</strong>
		</div>
		<div class="popup__content">
			<div class="added-items">
				<table>
					<tr>
						<td><a href="<?=$arResult["DETAIL_PAGE_URL"]?>" class="item-img"><img src="<?=$arResult["IMG"]?>" alt="<?=$arResult["NAME"]?>"></a></td>
						<td>
							<a href="<?=$arResult["DETAIL_PAGE_URL"]?>" class="item-name"><h2><?=$arResult["NAME"]?></h2></a>
							<?if ($arResult["COLOR"]):?>
								<span class="item-color">Цвет: <?=$arResult["COLOR"];?></span>
							<?endif;?>
							<?if ($arResult["SIZE"]):?>
								<span class="item-size">Размер: <?=$arResult["SIZE"]?></span>
							<?endif;?>
						</td>
						<td>
							<span class="item-price"><?=$arResult["PRICE"]?></span>
						</td>
					</tr>
				</table>
			</div>
			<div class="btm-links">
				<a href="#" class="cart-added__cancel popup__button__close">Продолжить покупки</a>
				<a href="/basket/" class="cart-added__submit">Оформить заказ</a>
			</div>
		</div>

		<?if (!empty($arResult["RECOMMEND"]["ITEMS"])):?>
		<div class="popup__footer">
			<div class="slider slider_size_mini">
				<div class="slider__inner">
					<h3>Рекомендуем купить</h3>
					<div id="slider">
					<?foreach ($arResult["RECOMMEND"]["ITEMS"] as $key => $arItem):?>
						<div class="slider__item">
							<a class="stuff-list__link" href="<?=$arItem["DETAIL_PAGE_URL"]?>" onmousedown="try { rrApi.recomMouseDown(<?=$arItem["ID"]?>, {methodName: '<?=$arResult["RECOMMEND"]["TYPE"]?>'}) } catch(e) {}">
								<?$y=CFile::ResizeImageGet(
									$arItem["PREVIEW_PICTURE"],
									array("width" => 168, "height" => 160),
									BX_RESIZE_IMAGE_PROPORTIONAL,
									true
								);?>
								<img src="<?=$y["src"]?>" alt="<?=$arItem["NAME"]?>"/>
								<span><?=$arItem["NAME"]?></span>
							</a>
							<?if ($arItem["PROPERTIES"]["AVAILABLE"]["VALUE"] == "Y"):?>
								<span class="stuff-list__price"><?=$arItem["PRICES"]["BASE"]["PRINT_DISCOUNT_VALUE"]?></span>
							<?else:?>
								<span class="not-available">Нет в наличии</span>
							<?endif;?>
							<div class="stuff-list__bottom">
								<a class="add-to-basket form-button" href="<?=$arItem["DETAIL_PAGE_URL"];?>" onmousedown="try { rrApi.recomMouseDown(<?=$arItem["ID"]?>, {methodName: '<?=$arResult["RECOMMEND"]["TYPE"]?>'}) } catch(e) {}">Подробнее</a>
								<div class="badge-wrapper">
									<?if ($arItem["PRICES"]["BASE"]["DISCOUNT_DIFF_PERCENT"] > 0):?>
										<div class="badge badge_type_discount">Cкидка <?=$arItem["PRICES"]["BASE"]["DISCOUNT_DIFF_PERCENT"]?>%</div>
									<?endif;?>
									<?if (!empty($arItem["PROPERTIES"]["GIFT"]["VALUE"])):?>
										<div class="badge badge_type_gift">+ Подарок</div>
									<?endif;?>
								</div>
							</div>
						</div>
					<?endforeach;?>
					</div>
					<a class="slide-prev" href="#">Предыдущий слайд</a>
					<a class="slide-next" href="#">Следующий слайд</a>
				</div>
			</div>
		</div>
		<script type="text/javascript">
		if ($('.cart-added .slider_size_mini').length > 0) {
			$('.cart-added .slider_size_mini #slider img').on('load', function() {
				$('.cart-added .slider_size_mini #slider').carouFredSel({
					scroll: {
						items: 1,
						duration: 600,
						timeoutDuration: 12000
					},
					items: {
						visible: 3
					},
					prev: ".slider_size_mini .slide-prev",
					next: ".slider_size_mini .slide-next"
				});
			});
		}
		</script>
		<?endif;?>

	</div>
<?endif;?>