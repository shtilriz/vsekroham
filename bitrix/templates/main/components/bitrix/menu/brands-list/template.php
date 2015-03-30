<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);?>

<?if (!empty($arResult)):?>
<div class="brands brands_type_content">
	<ul class="brands__list">
	<?foreach($arResult as $arItem):?>
		<li class="brands__item"><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></li>
	<?endforeach;?>
	</ul>
</div>
<?endif;?>