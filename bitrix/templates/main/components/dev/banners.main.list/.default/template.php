<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);?>

<?if (!empty($arResult["rows"])):?>
<div class="slider-block">
	<div class="slider">
		<?foreach ($arResult["rows"] as $key => $arItem) {
			$y=CFile::ResizeImageGet(
				$arItem["UF_IMAGE"],
				array("width" => 669, "height" => 300),
				BX_RESIZE_IMAGE_EXACT,
				true
			);
			echo '<img src="'.$y["src"].'" alt="'.$arItem["UF_NAME"].'" width="'.$y["width"].'" height="'.$y["height"].'">';
		}?>
	</div>
	<a class="slide-prev" href="#">Предыдущий слайд</a>
	<a class="slide-next" href="#">Следующий слайд</a>
</div>
<?endif;?>