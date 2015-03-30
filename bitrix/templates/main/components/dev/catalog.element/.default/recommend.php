<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult["RECOMMEND"]["ITEMS"])):?>
	<div class="slider slider_type_similar" id="recommends">
		<?$frame = $this->createFrame("recommends", false)->begin('');
		$frame->setAnimation(true);?>
		<div class="slider__inner">
			<?if ($arResult["RECOMMEND"]["TYPE"] == 'UpSellItemToItems'):?>
				<h4>Похожие товары</h4>
			<?else:?>
				<h4>Рекомендуем к просмотру</h4>
			<?endif;?>
			<div id="slider">
			<?foreach ($arResult["RECOMMEND"]["ITEMS"] as $key => $arItem):?>
				<div class="slider__item">
					<a class="stuff-list__link" href="<?=$arItem["DETAIL_PAGE_URL"];?>" onmousedown="try { rrApi.recomMouseDown(<?=$arItem["ID"]?>, {methodName: '<?=$arResult["RECOMMEND"]["TYPE"]?>'}) } catch(e) {}">
						<?$y=CFile::ResizeImageGet(
							$arItem["PREVIEW_PICTURE"],
							array("width" => 220, "height" => 180),
							BX_RESIZE_IMAGE_PROPORTIONAL,
							true
						);?>
						<img src="<?=$y["src"]?>" alt="<?=$arItem["NAME"]?>"/>
						<span><?=$arItem["NAME"]?></span>
					</a>
					<?$arRate = getRatingProduct($arItem["ID"]);?>
					<table class="stars-wrapper">
						<tr>
							<td>
								<div class="stars">
								<?for ($i=0; $i < 5; $i++)
									echo '<span class="'.($i<$arRate["RATE"]?'star-blue':'star-gray').'"></span>';
								?>
								</div>
							</td>
							<td>
								<a class="more-link" href="<?=$arItem["DETAIL_PAGE_URL"]?>#reviews"><?=($arRate["COUNT"]>0?$arRate["COUNT"].' '.rating_txt($arRate["COUNT"]):'Оцените первым')?></a>
							</td>
						</tr>
					</table>
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
							<?if (in_array($arItem["IBLOCK_SECTION_ID"], array(141,157,165,170,174,180,184,190))&&$arItem["PROPERTIES"]["MAKER"]["VALUE"]==15419):?>
								<div class="badge badge_type_bonus">Бонус 2000 р.</div>
							<?endif;?>
						</div>
					</div>
				</div>
			<?endforeach;?>
			</div>
			<a class="slide-prev" href="#">Предыдущий слайд</a>
			<a class="slide-next" href="#">Следующий слайд</a>
		</div>
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
