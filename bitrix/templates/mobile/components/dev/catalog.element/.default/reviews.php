<?php
function getIconSocLink($link) {
	$icon = '';
	if (!(strpos($link,'vk.com')===false))
		$icon = 'vk.png';
	elseif (!(strpos($link, 'facebook.com')===false))
		$icon = 'facebook.png';
	elseif (!(strpos($link, 'ok.ru')===false))
		$icon = 'ok.png';
	elseif (!(strpos($link, 'mail.ru')===false))
		$icon = 'mail.png';
	elseif (!(strpos($link, 'twitter.com')===false))
		$icon = 'tw.png';
	elseif (!(strpos($link, 'google.com')===false))
		$icon = 'gmail.png';
	elseif (!(strpos($link, 'yandex.ru')===false))
		$icon = 'yandex.png';
	if ($icon)
		$icon = '/bitrix/templates/main/images/icons-soclinks/'.$icon;
	return $icon;
}
?>
<?if (!empty($arResult["REVIEWS"])):?>
<div id="pr-reviews" class="reviews" data-id=<?=$arResult["ID"]?>>
	<?foreach ($arResult["REVIEWS"] as $key => $arItem):?>
	<div class="review__item" data-review_id="<?=$arItem["ID"]?>">
		<div class="review__top">
			<div class="review__top-date"><?=$arItem["UF_DATE"]?></div>
			<div class="review__avatar">
				<?if ($arItem["UF_AVATAR"]):
					$y=CFile::ResizeImageGet(
						$arItem["UF_AVATAR"],
						array("width" => 50, "height" => 50),
						BX_RESIZE_IMAGE_EXACT,
						true
					);?>
					<a href="<?=$arItem["UF_PROFILE_LINK"]?>" target="_blank" rel="nofollow"><img src="<?=$y["src"]?>" alt="" /></a>
				<?else:?>
					<img src="<?=SITE_TEMPLATE_PATH.'/images/temp/user.png'?>" alt="" />
				<?endif;?>
			</div>
			<div class="review__name">
				<?if ($arItem["UF_PROFILE_LINK"]):?>
					<a href="<?=$arItem["UF_PROFILE_LINK"]?>" target="_blank" rel="nofollow">
						<? echo $arItem["UF_NAME"]; ?>
					</a>
				<?else:?>
					<?=$arItem["UF_NAME"]?>
				<?endif;?>
			</div>
		</div>
		<div class="review__content">
			<div class="review__stars b-stars b-stars-<?=$arItem["UF_RATE"]?>"></div>
			<div class="review-text-block">
				<?if ($arItem["UF_WORTH"]):?>
					<div class="review-text-block__title">Достоинства:</div>
					<p class="review-text-block__content"><?=$arItem["UF_WORTH"];?></p>
				<?endif;?>
				<?if ($arItem["UF_LACK"]):?>
					<div class="review-text-block__title">Недостатки:</div>
					<p class="review-text-block__content"><?=$arItem["UF_LACK"];?></p>
				<?endif;?>
				<?if ($arItem["UF_COMMENT"]):?>
					<div class="review-text-block__title review-text-block__title_mod_1">Комментарий:</div>
					<p class="review-text-block__content"><?=$arItem["UF_COMMENT"];?></p>
				<?endif;?>
			</div>
			<div class="review__utility review-utility">
				Отзыв полезен? <a href="#" class="review-utility__plus">Да</a><span class="review-utility__count" data-role="cnt_plus"><?=intval($arItem["UF_LIKE"])?></span> / <a href="#" class="review-utility__minus">Нет</a><span class="review-utility__count" data-role="cnt_minus"><?=intval($arItem["UF_DIZLIKE"])?></span>
			</div>
		</div>
	</div>
	<?endforeach;?>
	<?if ($arResult["NAV_RESULT"]->NavPageCount > 1)
		echo $arResult["NAV_STRING"]?>
</div>
<?/*<p class="product__comments-take comments-take">Отзывы взяты из Яндекс Маркет и mail.ru <img src="<?=SITE_TEMPLATE_PATH?>/images/temp/layer-210.png" alt="" class="comments-take__img" /><img src="<?=SITE_TEMPLATE_PATH?>/images/temp/layer-211.png" alt="" class="comments-take__img" />
</p>*/?>
<?endif;?>
<a href="/my-review/<?=$arResult["ID"]?>/" class="btn-big btn-primary">Отправить отзыв</a>