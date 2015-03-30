<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$this->setFrameMode(true);?>

<?if (count($arResult) > 0):?>
<div class="brands brands_type_content">
	<ul class="brands__list">
		<?foreach ($arResult as $arBrand) {
			echo '<li class="brands__item"><a href="'.$arBrand["LINK"].$brand.'">'.$arBrand["NAME"].'</a></li>';
		}?>
	</ul>
</div>
<?endif;?>