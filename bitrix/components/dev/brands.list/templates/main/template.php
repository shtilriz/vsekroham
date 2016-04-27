<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

function isCyrillic($char)
{
	return preg_match("/^[А-Яа-я]+$/", $char);
}
?>

<form name="makerFilter">
	<div class="b-search brands__search">
		<?/*<button type="submit" class="b-search__button">Найти</button>*/?>
		<input type="text" name="q_brands" value="<?echo trim(strip_tags($_GET['q_brands']));?>" class="b-search__control" placeholder="Быстрый поиcк">
	</div>
	<h1 class="item-title__">Производители</h1>
	<div class="b-az">
		<div class="b-az__item b-az__item--lang">
			<a class="b-az__link active js-b-az-link" data-lang="en" href="#">EN</a>
		</div>
		<div class="b-az__item b-az__item--lang">
			<a class="b-az__link js-b-az-link" data-lang="ru" href="#">RU</a>
		</div>

		<?if (!empty($arResult['ABC'])):?>
		<div class="b-az__item b-az__item--links" data-toggle="buttons">
			<?foreach ($arResult['ABC'] as $key => $value):?>
				<label class="btn b-az__link  <?echo isCyrillic($value) ? 'b-az__link--ru js-b-az-ru' : 'b-az__link--en js-b-az-en'?> js-b-az-lang">
                    <input type="checkbox" name="maker[]" value="<?echo $value;?>" id="check-chr-<?echo $value;?>" autocomplete="off"><?echo $value;?>
                </label>
			<?endforeach;?>
		</div>
		<?endif;?>
	</div>
</form>

<ul class="brands-simple-list clearfix brands__page-list" id="resultList">
	<?if ($_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest') $APPLICATION->RestartBuffer();?>
	<?foreach ($arResult['MAKERS'] as $chr => $arChars):?>
	<li>
		<ul>
			<?foreach ($arChars as $key => $arItem):?>
			<li><a href="<?echo $arItem['DETAIL_PAGE_URL'];?>"><?echo $arItem['NAME'];?></a></li>
			<?endforeach;?>
		</ul>
	</li>
	<?endforeach;?>
	<?if (empty($arResult['MAKERS'])):?>
	<div class="search-results__inner">
		<div class="nothing">К сожалению у нас нет брендов, соответствующих вашему запросу. Попробуйте изменить поисковый запрос</div>
	</div>
	<?endif;?>
	<?if ($_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest') die();?>
</ul>

