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
	<?foreach ($arResult["REVIEWS"] as $key => $arItem):?>
		<div class="review__item" data-review_id="<?=$arItem["ID"]?>">
			<div class="review__top">
				<div class="review__top-right">
					<span class="date"><?=$arItem["UF_DATE"]?></span>
					<?if ($arItem["UF_PROFILE_LINK"]):?>
					<a class="social-logo" rel="nofollow" target="_blank" href="<?=$arItem["UF_PROFILE_LINK"]?>" target="_blank" rel="nofollow">
						<?
						$icon = getIconSocLink($arItem["UF_PROFILE_LINK"]);
						if ($icon) {
							echo "<img src='$icon' alt=''>";
						}
						?>
					</a>
					<?endif;?>
				</div>
				<span class="avatar">
					<?if ($arItem["UF_AVATAR"]):
						$y=CFile::ResizeImageGet(
							$arItem["UF_AVATAR"],
							array("width" => 50, "height" => 50),
							BX_RESIZE_IMAGE_EXACT,
							true
						);?>
						<a href="<?=$arItem["UF_PROFILE_LINK"]?>" target="_blank" rel="nofollow"><img src="<?=$y["src"]?>" alt="" /></a>
					<?else:?>
						<img src="<?=SITE_TEMPLATE_PATH.'/images/avatar_default.png'?>" alt="" />
					<?endif;?>
				</span>
				<?if ($arItem["UF_PROFILE_LINK"]):?>
					<span class="name">
						<a href="<?=$arItem["UF_PROFILE_LINK"]?>" target="_blank" rel="nofollow">
							<? echo $arItem["UF_NAME"]; ?>
						</a>
					</span>
				<?else:?>
					<span class="name"><?=$arItem["UF_NAME"]?></span>
				<?endif;?>
			</div>
			<div class="review__content">
				<div class="stars">
					<?$rating = intval($arItem["UF_RATE"]);
					for ($i = 0; $i < 5 ; $i++) {
						$star = 'star-gray';
						if ($i < $rating)
							$star = 'star-blue';
						echo '<span class="'.$star.'"></span>';
					}?>
				</div>
				<?if ($arItem["UF_WORTH"]):?>
					<p><span class="text-green bold">Достоинства:</span> <?=$arItem["UF_WORTH"];?></p>
				<?endif;?>
				<?if ($arItem["UF_LACK"]):?>
					<p><span class="text-red bold">Недостатки:</span> <?=$arItem["UF_LACK"];?></p>
				<?endif;?>
				<?if ($arItem["UF_COMMENT"]):?>
				<p><span class="bold">Комментарий:</span> <?=$arItem["UF_COMMENT"];?></p>
				<?endif;?>
			</div>
			<div class="review__bottom">
				<div class="author-sity"><?=$arItem["UF_CITY"]?'г. '.$arItem["UF_CITY"]:''?></div>
				<div class="review-utility">
					Отзыв полезен? <a href="#" class="review-plus">Да</a> <span class="review-plus-count"><?=intval($arItem["UF_LIKE"])?></span> / <a href="#" class="review-minus">Нет</a> <span class="review-minus-count"><?=intval($arItem["UF_DIZLIKE"])?></span>
				</div>
			</div>
		</div>

	<?endforeach;?>
	<?if ($arResult["NAV_RESULT"]->NavPageCount > 1)
		echo $arResult["NAV_STRING"]?>
<?endif;?>