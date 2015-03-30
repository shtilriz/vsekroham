<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);?>

<?if (!empty($arResult)):?>
<h3>Бренды</h3>
<ul class="menu__list menu__list_name_brands">
	<?
	foreach($arResult as $arItem) {
		if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1) 
			continue;
		echo '<li class="menu__item"><a class="menu__link'.($arItem["SELECTED"]?' active':'').'" href="'.$arItem["LINK"].'">'.$arItem["TEXT"].'</a></li>';
	}
	?>
	<li class="menu__item">
		<span class="show-trigger showall">Показать все</span>
	</li>
</ul>
<?endif;?>