<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult["RECOMMEND"]["ITEMS"])):?>
	<div class="b-content__header b-header">
		<div class="b-header__title"><span><?=($arResult["RECOMMEND"]["TYPE"] == 'UpSellItemToItems'?'Похожие товары':'Рекомендуем к просмотру')?></span></div>
	</div>
	<div class="product__goods b-goods" id="recommends">
		<?$frame = $this->createFrame("recommends", false)->begin('');
		$frame->setAnimation(true);?>
		<?foreach ($arResult["RECOMMEND"]["ITEMS"] as $key => $arItem):?>
		<div class="b-g b-g_list">
			<?$y=CFile::ResizeImageGet(
				$arItem["PREVIEW_PICTURE"],
				array("width" => 220, "height" => 180),
				BX_RESIZE_IMAGE_PROPORTIONAL,
				true
			);?>
			<a href="<?=$arItem["DETAIL_PAGE_URL"];?>" class="b-g__img" onmousedown="try { rrApi.recomMouseDown(<?=$arItem["ID"]?>, {methodName: '<?=$arResult["RECOMMEND"]["TYPE"]?>'}) } catch(e) {}"><img src="<?=$y["src"]?>" alt="<?=$arItem["NAME"]?>" />
			</a>
			<div class="b-g__content"><a href="<?=$arItem["DETAIL_PAGE_URL"];?>" class="b-g__title" onmousedown="try { rrApi.recomMouseDown(<?=$arItem["ID"]?>, {methodName: '<?=$arResult["RECOMMEND"]["TYPE"]?>'}) } catch(e) {}"><?=$arItem["NAME"]?></a>
				<div class="b-g__price"><?=($arItem["PROPERTIES"]["AVAILABLE"]["VALUE"] == "Y"?$arItem["PRICES"]["BASE"]["PRINT_DISCOUNT_VALUE"]:'Нет в наличии')?></div>
				<?$arRate = getRatingProduct($arItem["ID"]);?>
				<div class="b-g__stars b-stars b-stars-<?=$arRate["RATE"]?>"></div>
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>#reviews" class="b-g__count-comments"><?=($arRate["COUNT"]>0?$arRate["COUNT"].' '.rating_txt($arRate["COUNT"]):'Оцените первым')?></a>
			</div>
			<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="b-g__next" onmousedown="try { rrApi.recomMouseDown(<?=$arItem["ID"]?>, {methodName: '<?=$arResult["RECOMMEND"]["TYPE"]?>'}) } catch(e) {}"></a>
		</div>
		<?endforeach;?>
		<?$frame->end();?>
	</div>
	<?if ($arResult["RECOMMEND"]["TYPE"]):?>
		<script type="text/javascript">
		rrApiOnReady.push(function() {
			try {
				rrApi.recomTrack('<?=$arResult["RECOMMEND"]["TYPE"]?>', <?=$arResult["ID"]?>, [<?=implode(",", $arResult["RECOMMEND"]["IDS"])?>]);
			} catch(e) {}
		});
		</script>
	<?endif;?>
<?endif;?>
