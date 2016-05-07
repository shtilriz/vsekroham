<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$this->setFrameMode(true);?>

<?if (count($arResult) > 0):?>
<div class="brands brands--tags">
	<div class="brands__inner brands__dropdown js-dropdown">
		<span class="brands__title">Рекомендуемые:</span>
		<?$cntBlocks = count($arResult['BLOCKS']);?>
		<?foreach ($arResult['BLOCKS'] as $key => $arBlock):?>
		<ul class="brands__list<?echo ($key>1 ? ' brands__list--hidden js-dropdown-content' : '');?>">
			<?$cntBrandsOfBlock = count($arBlock);?>
			<?foreach ($arBlock as $cell => $arBrand):?>
				<?if ($cntBlocks > 2 && $key == 1 && ($cell+1) == $cntBrandsOfBlock):?>
					<li class="brands__item">
						<a class="brands__link-toggle" href="<?echo $arBrand['LINK'];?>"><?echo $arBrand['NAME'];?></a>
						<span class="brands__more-link brands__more-link--toggle js-brands-toggle">Еще...</span>
					</li>
				<?elseif ($cntBlocks > 2 && ($key+1) == $cntBlocks && ($cell+1) == $cntBrandsOfBlock):?>
					<li class="brands__item"><a class="brands__link" href="<?echo $arBrand['LINK'];?>"><?echo $arBrand['NAME'];?></a></li>
					<li class="brands__item"><span class="brands__more-link js-brands-toggle">Скрыть...</span></li>
				<?else:?>
					<li class="brands__item"><a class="brands__link" href="<?echo $arBrand['LINK'];?>"><?echo $arBrand['NAME'];?></a></li>
				<?endif;?>
			<?endforeach?>
		</ul>
		<?endforeach;?>
	</div>
</div>
<?endif;?>