<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
?>

<div class="b-content__header b-header">
	<div class="b-header__title"><span><?=GetMessage('TITLE')?></span></div>
</div>

<form name="makerFilter">
	<?if (!empty($arResult['SECTIONS'])):?>
	<div class="b-box b-box_alphabet">
		<a href="javascript:void(0)" class="b-header b-header_mod-1 b-header_toggle js-toogle">
			<span class="b-header__icon sprite-equalizer-gray"></span>
			<div class="b-header__title"><span><?=GetMessage('PRODUCT_CATEGORIES')?></span></div>
		</a>
		<div class="js-dropdown">
			<div style="height: 334px;" class="b-box__content scrollbar js-scrollbar">
				<div class="b-checkers">
					<?foreach ($arResult['SECTIONS'] as $id => $name):?>
					<div class="b-checker b-checker_simple b-checker--lead js-b-checker">
						<div class="b-checker__check">
							<input type="checkbox" name="categories[]" value="<?echo $id;?>" id="check-sect-<?echo $id;?>" class="checkbox-styled checkbox-styled--lead" />
							<label for="check-sect-<?echo $id;?>" class="b-checker__label"></label>
						</div>
						<div class="b-checker__content">
							<div class="b-checker__title"><?echo $name;?></div>
						</div>
					</div>
					<?endforeach;?>
				</div>
			</div>
			<div class="b-box__footer">
				<button type="submit" class="b-box__button">Применить</button>
			</div>
		</div>
	</div>
	<?endif;?>

	<?if (!empty($arResult['ABC'])):?>
	<div class="b-box b-box_alphabet">
		<a href="javascript:void(0)" class=" b-header b-header_mod-1 b-header_toggle js-toogle">
			<span class="b-header__icon sprite-a-z"></span>
			<div class="b-header__title"><span><?=GetMessage('ALFAVIT')?></span></div>
		</a>
		<div class="js-dropdown">
			<div style="height: 334px;" class="b-box__content scrollbar js-scrollbar">
				<div class="b-checkers">
					<?foreach ($arResult['ABC'] as $key => $value):?>
					<div class="b-checker b-checker_simple b-checker--lead js-b-checker">
						<div class="b-checker__check">
							<input type="checkbox" name="maker[]" value="<?echo $value;?>" id="check-chr-<?echo $value;?>" class="checkbox-styled checkbox-styled--lead" />
							<label for="check-chr-<?echo $value;?>" class="b-checker__label"></label>
						</div>
						<div class="b-checker__content">
							<div class="b-checker__title"><?echo $value;?></div>
						</div>
					</div>
					<?endforeach;?>
				</div>
			</div>
			<div class="b-box__footer">
				<button type="submit" class="b-box__button">Применить</button>
			</div>
		</div>
	</div>
	<?endif;?>
</form>

<br/>
<?if (!empty($arResult['MAKERS'])):?>
<ul class="b-list-group" id="resultList">
	<?if ($_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest') $APPLICATION->RestartBuffer();?>
	<?foreach ($arResult['MAKERS'] as $chr => $arChars) {
		foreach ($arChars as $key => $arItem) {
			echo sprintf('<li><a href="%s">%s</a></li>', $arItem['DETAIL_PAGE_URL'], $arItem['NAME']);
		}
	}?>
	<?if ($_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest') die();?>
</ul>
<?endif?>
<?/*
<br/>
<div class="spin-loader"></div><a href="#" class="show-more-button js-show-more-button">Показать еще</a>
*/?>