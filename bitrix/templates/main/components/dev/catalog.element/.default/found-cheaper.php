<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}
?>

<div class="found-cheaper popup" id="found-cheaper">
	<div class="popup__top">
		<a href="#" class="popup__close">X</a>
		<strong class="popup__title">Нашли дешевле? Сделаем скидку.</strong>
	</div>
	<div class="popup__content">
		<div class="added-items">
			<table>
				<tr>
					<td>
						<a href="<?echo $arResult['DETAIL_PAGE_URL']?>" class="item-img">
							<?$arFirstOffer = reset($arResult['OFFERS']);?>
							<?if (!empty($arResult['OFFERS']) && $arFirstOffer['PREVIEW_PICTURE']):?>
									<?$y=CFile::ResizeImageGet(
										$arFirstOffer['PREVIEW_PICTURE'],
										array('width' => 250, 'height' => 250),
										BX_RESIZE_IMAGE_PROPORTIONAL,
										true
									);?>
									<img src="<?echo $y['src']?>" alt="<?echo $arFirstOffer['NAME']?>">
								</a>
							<?else:?>
								<a href="<?echo $arResult['DETAIL_PAGE_URL']?>"  class="item-img">
									<?$y=CFile::ResizeImageGet(
										$arResult['PREVIEW_PICTURE']['ID'],
										array('width' => 250, 'height' => 250),
										BX_RESIZE_IMAGE_PROPORTIONAL,
										true
									);?>
									<img src="<?=$y['src']?>" alt="<?echo $arResult['NAME']?>">
							<?endif;?>
						</a>
					</td>
					<td>
						<a href="<?echo $arResult['DETAIL_PAGE_URL']?>" class="item-name">
							<h2><?echo $arResult['NAME']?></h2>
						</a>
						<span class="item-color js-item-color"></span>
						<span class="item-size js-item-size"></span>
					</td>
					<td>
						<span class="item-price js-item-price"></span>
					</td>
				</tr>
			</table>
		</div>
		<div class="text-center">
			<h2>Нашли этот товар дешевле?</h2>
			<p class="lead">
				Заполните заявку на лучшую цену.
				<br> Пришлите нам ссылку на этот товар в другом магазине.
			</p>
		</div>
		<form name="found-cheaper" class="js-load-block">
			<input type="hidden" name="PRODUCT" value="<?echo $arResult['ID'];?>">
			<input type="hidden" name="PRICE" value="">
			<table class="like-inline noboder">
				<tbody>
					<tr>
						<td class="first">
							<label class="control-label block-label">ФИО</label>
						</td>
						<td class="second">
							<input type="text" name="FIO" value="" class="form-control">
						</td>
						<td></td>
					</tr>
					<tr>
						<td class="first">
							<label class="control-label block-label">Телефон</label>
						</td>
						<td>
							<input type="text" name="PHONE" value="" class="form-control">
						</td>
						<td></td>
					</tr>
					<tr>
						<td class="first">
							<label class="control-label block-label">E-mail</label>
						</td>
						<td class="second">
							<input type="text" name="EMAIL" value="" class="form-control">
						</td>
						<td></td>
					</tr>
					<tr>
						<td class="first">
							<label class="control-label block-label">Ссылка на товар в другом
								<br> магазине</label>
						</td>
						<td class="second">
							<input type="text" name="LINK" value="" class="form-control">
						</td>
						<td></td>
					</tr>
					<tr>
						<td class="first">
							<label class="control-label block-label">Цена товара в другом
							<br> магазине</label>
						</td>
						<td class="second">
							<input type="text" name="PRICE_LINK" value="" class="form-control">
						</td>
						<td></td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<td class="first"></td>
						<td class="second">
							<button type="submit" class="button-blue">
								<span class="lead">Отправить заявку</span>
							</button>
						</td>
						<td></td>
					</tr>
				</tfoot>
			</table>
		</form>
	</div>
</div>